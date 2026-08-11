<?php
// Auth: login API
session_start();
require_once '../config/conection.php';
require_once '../models/users.php';
require_once '../config/users_crud.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$email    = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($email === '' || $password === '') {
  echo json_encode(['success' => false, 'message' => 'Correo y contraseña son obligatorios']);
  exit;
}

$crud = new users_crud();
$user = $crud->getByEmail($email);

if (!$user) {
  echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
  exit;
}

if (!password_verify($password, $user['password'])) {
  echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
  exit;
}

$_SESSION['user'] = [
  'id'         => $user['id'],
  'first_name' => $user['first_name'],
  'last_name'  => $user['last_name'],
  'email'      => $user['email'],
  'role'       => $user['role'],
];

echo json_encode([
  'success'  => true,
  'role'     => $user['role'],
  'redirect' => $user['role'] === 'admin'
    ? '/peluqueria/admin/pages/dashboard.php'
    : '/peluqueria/index.php',
]);
?>