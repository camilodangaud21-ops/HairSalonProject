<?php
require_once __DIR__ . '/session.php';
start_app_session();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/conection.php';
require_once __DIR__ . '/../models/users.php';
require_once __DIR__ . '/../config/users_crud.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');

if ($email === '' || $password === '') {
  echo json_encode(['success' => false, 'message' => 'Correo y contraseña son obligatorios']);
  exit;
}

$crud = new users_crud();
$user = $crud->getByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
  echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
  exit;
}

if (isset($user['email_verified']) && (int)$user['email_verified'] !== 1) {
  echo json_encode([
    'success' => false,
    'requires_verification' => true,
    'message' => 'Debes verificar tu correo antes de iniciar sesión.'
  ]);
  exit;
}

session_regenerate_id(true);

$_SESSION['user'] = [
  'id' => $user['id'],
  'first_name' => $user['first_name'],
  'last_name' => $user['last_name'],
  'email' => $user['email'],
  'role' => $user['role'],
];

echo json_encode([
  'success' => true,
  'role' => $user['role'],
  'redirect' => $user['role'] === 'admin'
    ? app_url('admin/pages/dashboard.php')
    : app_url('index.php'),
]);
?>
