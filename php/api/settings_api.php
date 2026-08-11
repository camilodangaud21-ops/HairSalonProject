<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/settings_controller.php';
// API: settings endpoints

$controller = new settings_controller();
$action     = $_GET['action'] ?? 'all';

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
    echo json_encode($controller->getAll());
    break;

  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controller->updateMany($data));
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>