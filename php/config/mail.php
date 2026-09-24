<?php
// Email delivery via SMTP. Credentials are read from environment variables or
// php/config/mail.local.php (which must stay out of Git).
function mail_config(): array {
    $localFile = __DIR__ . '/mail.local.php';
    $local = file_exists($localFile) ? require $localFile : [];
    return [
        'host' => $local['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: '',
        'port' => (int)($local['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 465),
        'user' => $local['SMTP_USER'] ?? getenv('SMTP_USER') ?: '',
        'password' => $local['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?: '',
        'from' => $local['MAIL_FROM'] ?? getenv('MAIL_FROM') ?: '',
        'from_name' => $local['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME') ?: 'Isabel Rojas Beauty Salón & Spa',
        'app_url' => rtrim($local['APP_URL'] ?? getenv('APP_URL') ?: '', '/'),
    ];
}

function app_base_url(): string {
    $cfg = mail_config();
    if ($cfg['app_url'] !== '') return $cfg['app_url'];

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = preg_replace('#/php/auth(?:/[^/]*)?$#', '', $script);

    return $scheme . '://' . $host . rtrim($base, '/');
}

function smtp_read($socket): string {
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }
    return $response;
}

function smtp_expect($socket, array $codes): bool {
    $response = smtp_read($socket);
    $code = (int)substr($response, 0, 3);
    return in_array($code, $codes, true);
}

function smtp_command($socket, string $command, array $codes): bool {
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $codes);
}

function sendVerificationEmail(string $email, string $firstName, string $token): bool {
    $cfg = mail_config();

    if ($cfg['host'] === '' || $cfg['user'] === '' || $cfg['password'] === '' || $cfg['from'] === '') {
        error_log('SMTP no configurado: faltan SMTP_HOST, SMTP_USER, SMTP_PASSWORD o MAIL_FROM.');
        return false;
    }

    $verifyUrl = app_base_url() . '/php/auth/verify_email.php?token=' . rawurlencode($token);
    $siteName = $cfg['from_name'];
    $safeName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8');

    $subject = 'Verifica tu correo electrónico';
    $body = '<!doctype html><html lang="es"><body style="font-family:Arial,sans-serif;background:#f5f3ef;padding:24px;color:#222">'
        . '<div style="max-width:560px;margin:auto;background:#fff;padding:32px;border-radius:12px">'
        . '<h2>' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . '</h2>'
        . '<p>Hola ' . $safeName . ',</p>'
        . '<p>Gracias por crear tu cuenta. Confirma que este correo electrónico te pertenece para activar tu cuenta.</p>'
        . '<p style="text-align:center;margin:28px 0"><a href="' . $safeUrl . '" style="display:inline-block;padding:12px 22px;background:#c9a84c;color:#111;text-decoration:none;border-radius:8px;font-weight:bold">Verificar mi correo</a></p>'
        . '<p>Este enlace caduca en 30 minutos.</p>'
        . '<p>Si no creaste esta cuenta, puedes ignorar este correo.</p>'
        . '</div></body></html>';

    $port = $cfg['port'] ?: 465;
    $transport = $port === 465 ? 'ssl://' . $cfg['host'] : $cfg['host'];
    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client($transport . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        error_log("SMTP conexión fallida: {$errno} {$errstr}");
        return false;
    }

    stream_set_timeout($socket, 15);

    try {
        if (!smtp_expect($socket, [220])) throw new RuntimeException('SMTP greeting failed');
        if (!smtp_command($socket, 'EHLO localhost', [250])) throw new RuntimeException('SMTP EHLO failed');

        if ($port !== 465) {
            if (!smtp_command($socket, 'STARTTLS', [220])) throw new RuntimeException('SMTP STARTTLS failed');
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('SMTP TLS negotiation failed');
            }
            if (!smtp_command($socket, 'EHLO localhost', [250])) throw new RuntimeException('SMTP EHLO after TLS failed');
        }

        if (!smtp_command($socket, 'AUTH LOGIN', [334])) throw new RuntimeException('SMTP AUTH not accepted');
        if (!smtp_command($socket, base64_encode($cfg['user']), [334])) throw new RuntimeException('SMTP username rejected');
        if (!smtp_command($socket, base64_encode($cfg['password']), [235])) throw new RuntimeException('SMTP password rejected');
        if (!smtp_command($socket, 'MAIL FROM:<' . $cfg['from'] . '>', [250])) throw new RuntimeException('SMTP sender rejected');
        if (!smtp_command($socket, 'RCPT TO:<' . $email . '>', [250, 251])) throw new RuntimeException('SMTP recipient rejected');
        if (!smtp_command($socket, 'DATA', [354])) throw new RuntimeException('SMTP DATA rejected');

        $headers = 'Date: ' . date(DATE_RFC2822) . "\r\n";
        $headers .= 'From: ' . $siteName . ' <' . $cfg['from'] . ">\r\n";
        $headers .= 'To: <' . $email . ">\r\n";
        $headers .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";
        $message = $headers . "\r\n" . $body . "\r\n.\r\n";

        fwrite($socket, $message);
        if (!smtp_expect($socket, [250])) throw new RuntimeException('SMTP message rejected');
        smtp_command($socket, 'QUIT', [221, 250]);
        fclose($socket);
        return true;
    } catch (Throwable $e) {
        error_log('SMTP verification email failed: ' . $e->getMessage());
        @fwrite($socket, "RSET\r\n");
        @fclose($socket);
        return false;
    }
}
?>