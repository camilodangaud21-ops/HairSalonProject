<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/users_crud.php';
require_once __DIR__ . '/../config/mail.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Escribe un correo electrónico válido.']);
    exit;
}

$crud = new users_crud();
$user = $crud->getByEmail($email);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'No encontramos una cuenta con ese correo.']);
    exit;
}

if (isset($user['email_verified']) && (int)$user['email_verified'] === 1) {
    echo json_encode(['success' => false, 'message' => 'Este correo ya está verificado.']);
    exit;
}

// Generate a fresh token so an old link cannot be reused.
$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);
$expiresAt = date('Y-m-d H:i:s', time() + 1800);

if (!$crud->setVerificationToken((int)$user['id'], $tokenHash, $expiresAt)) {
    echo json_encode(['success' => false, 'message' => 'No pudimos preparar el nuevo correo de verificación.']);
    exit;
}

if (!sendVerificationEmail($email, $user['first_name'], $token)) {
    echo json_encode([
        'success' => false,
        'message' => 'No pudimos enviar el correo de verificación. Si estás trabajando en XAMPP, revisa si el antivirus o firewall está bloqueando la conexión SMTP. En un servidor/hosting, revisa también las restricciones de salida SMTP.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Te enviamos un nuevo correo de verificación. Revisa también la carpeta de spam.'
]);
?>