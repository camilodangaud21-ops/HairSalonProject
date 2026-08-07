<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../config/conection.php';
require_once '../config/settings_crud.php';

$crud   = new settings_crud();
$action = $_GET['action'] ?? 'all';

function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

switch ($action) {

  case 'all':
    requireAdmin();
    echo json_encode($crud->getAll());
    break;

  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => (bool) $crud->updateMany($data)]);
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>