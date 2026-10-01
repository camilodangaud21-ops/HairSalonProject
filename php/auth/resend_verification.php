<?php
require_once __DIR__ . '/session.php';
start_app_session();
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
    // Keep the response generic so this endpoint cannot be used to enumerate accounts.
    echo json_encode([
        'success' => true,
        'message' => 'Si existe una cuenta pendiente de verificación con ese correo, recibirás un nuevo mensaje.'
    ]);
    exit;
}

if (isset($user['email_verified']) && (int)$user['email_verified'] === 1) {
    echo json_encode(['success' => false, 'message' => 'Este correo ya está verificado.']);
    exit;
}

$retryAfter = $crud->getVerificationResendCooldown((int)$user['id'], 60);
if ($retryAfter > 0) {
    echo json_encode([
        'success' => false,
        'retry_after' => $retryAfter,
        'message' => "Espera {$retryAfter} segundos antes de solicitar otro correo."
    ]);
    exit;
}

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
        'message' => 'No pudimos enviar el correo de verificación. Revisa la configuración SMTP o el firewall del servidor.'
    ]);
    exit;
}

$crud->markVerificationEmailSent((int)$user['id']);

echo json_encode([
    'success' => true,
    'retry_after' => 60,
    'message' => 'Te enviamos un nuevo correo de verificación. Revisa también la carpeta de spam.'
]);
?>
