<?php
// Centralized session bootstrap and authorization guards.
function start_app_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_trans_sid', '0');

    session_name('hair_salon_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function current_user(): ?array {
    start_app_session();
    return isset($_SESSION['user']) && is_array($_SESSION['user'])
        ? $_SESSION['user']
        : null;
}

function require_authenticated_user(?string $role = null): array {
    $user = current_user();

    if (!$user) {
        require_once __DIR__ . '/../config/app.php';
        header('Location: ' . app_url('index.php'));
        exit;
    }

    if ($role !== null && ($user['role'] ?? '') !== $role) {
        require_once __DIR__ . '/../config/app.php';
        header('Location: ' . app_url('index.php'));
        exit;
    }

    return $user;
}

function destroy_app_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_app_session();
    }

    $_SESSION = [];

    $params = session_get_cookie_params();
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'] ?? '',
            'secure' => (bool)$params['secure'],
            'httponly' => (bool)$params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}
?>
