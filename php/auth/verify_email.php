<?php
session_start();
require_once __DIR__ . '/../config/conection.php';
require_once __DIR__ . '/../config/users_crud.php';

$token = trim($_GET['token'] ?? '');

function verificationPage(string $title, string $message, bool $success = false): void {
    $color = $success ? '#6dbf6d' : '#e55';
    echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
        . '</title></head><body style="margin:0;min-height:100vh;display:grid;place-items:center;background:#0f0e0c;color:#f0eae0;font-family:Arial,sans-serif;padding:24px;box-sizing:border-box">'
        . '<main style="width:min(100%,520px);background:#1a1814;border:1px solid #2e2923;border-radius:16px;padding:32px;text-align:center">'
        . '<div style="font-size:42px;margin-bottom:12px">' . ($success ? '✓' : '!') . '</div>'
        . '<h1 style="color:' . $color . ';font-weight:600">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
        . '<p style="color:#8a8278;line-height:1.6">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<a href="/peluqueria/index.php" style="display:inline-block;margin-top:16px;padding:11px 18px;background:#c9a84c;color:#0f0e0c;text-decoration:none;border-radius:8px;font-weight:bold">Ir al sitio</a>'
        . '</main></body></html>';
    exit;
}

if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    verificationPage('Enlace no válido', 'El enlace de verificación no es válido.');
}

$crud = new users_crud();
$user = $crud->verifyEmailByTokenHash(hash('sha256', $token));

if (!$user) {
    verificationPage('Enlace expirado', 'El enlace de verificación ya no es válido o ha expirado. Solicita un nuevo correo de verificación.');
}

if (!$crud->markEmailVerified((int)$user['id'])) {
    verificationPage('No se pudo verificar', 'Ocurrió un error al activar tu correo. Inténtalo nuevamente.');
}

$_SESSION['user'] = [
    'id' => $user['id'],
    'first_name' => $user['first_name'],
    'last_name' => $user['last_name'],
    'email' => $user['email'],
    'role' => $user['role'],
];

verificationPage('Correo verificado', 'Tu correo fue verificado correctamente. Ya puedes usar tu cuenta.', true);
?>
