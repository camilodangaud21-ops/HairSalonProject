<?php
require_once __DIR__ . '/session.php';
start_app_session();
header('Content-Type: application/json');

require_once __DIR__ . '/../models/users.php';
require_once __DIR__ . '/../config/users_crud.php';
require_once __DIR__ . '/../config/conection.php';
require_once __DIR__ . '/../config/mail.php';

$data = json_decode(file_get_contents('php://input'), true);

$first_name = trim($data['first_name'] ?? '');
$last_name = trim($data['last_name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');
$password_confirm = (string)($data['password_confirm'] ?? '');

if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

if ($password !== $password_confirm) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
    exit;
}

$crud = new users_crud();

if ($crud->getByEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado']);
    exit;
}

$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);
$expiresAt = date('Y-m-d H:i:s', time() + 1800);

$ok = $crud->create([
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'password' => $password,
    'role' => 'client',
    'email_verified' => false,
    'verification_token_hash' => $tokenHash,
    'verification_expires_at' => $expiresAt,
]);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
    exit;
}

$emailSent = sendVerificationEmail($email, $first_name, $token);
if ($emailSent) {
    $createdUser = $crud->getByEmail($email);
    if ($createdUser) $crud->markVerificationEmailSent((int)$createdUser['id']);
}

echo json_encode([
    'success' => true,
    'message' => $emailSent
        ? 'Cuenta creada. Revisa tu correo y haz clic en el botón de verificación para activarla.'
        : 'Cuenta creada, pero no pudimos enviar el correo de verificación. Revisa la configuración SMTP y vuelve a intentarlo.',
    'requires_verification' => true,
    'email_sent' => $emailSent
]);
?>
