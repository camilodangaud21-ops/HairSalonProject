<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../config/conection.php';
require_once '../config/services_crud.php';

$crud   = new services_crud();
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
    echo json_encode($crud->getAll());
    break;

  case 'allAdmin':
    requireAdmin();
    echo json_encode($crud->getAllAdmin());
    break;

  case 'byId':
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($crud->getById($id));
    break;

  case 'byCategory':
    $category = $_GET['category'] ?? '';
    echo json_encode($crud->getByCategory($category));
    break;

  case 'create':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => (bool) $crud->create($data)]);
    break;

  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($_GET['id'] ?? 0);
    echo json_encode(['success' => (bool) $crud->update($id, $data)]);
    break;

  case 'toggleActive':
    requireAdmin();
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($_GET['id'] ?? 0);
    $active = (bool) ($data['active'] ?? false);
    echo json_encode(['success' => (bool) $crud->toggleActive($id, $active)]);
    break;

  case 'delete':
    requireAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode(['success' => (bool) $crud->hardDelete($id)]);
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>