<?php
// Verification email delivery. Production should provide a real SMTP/mail transport.
function app_base_url(): string {
    $configured = getenv('APP_URL');
    if ($configured) {
        return rtrim($configured, '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = preg_replace('#/php/auth(?:/[^/]*)?$#', '', $script);

    return $scheme . '://' . $host . rtrim($base, '/');
}

function sendVerificationEmail(string $email, string $firstName, string $token): bool {
    $verifyUrl = app_base_url() . '/php/auth/verify_email.php?token=' . rawurlencode($token);
    $from = getenv('MAIL_FROM') ?: 'no-reply@localhost';
    $siteName = getenv('MAIL_FROM_NAME') ?: 'Isabel Rojas Beauty Salón & Spa';

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

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $siteName . " <" . $from . ">\r\n";

    return mail($email, $subject, $body, $headers);
}
?>
