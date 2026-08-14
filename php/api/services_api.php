<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Load the services controller for request handling.
require_once __DIR__ . '/../controllers/services_controller.php';

$controller = new services_controller();
$action     = $_GET['action'] ?? 'all';

// Require the current user to be an admin for protected actions.
function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

switch ($action) {

  // Return all services available to public clients.
  case 'all':
    echo json_encode($controller->getAll());
    break;

  // Return all services, including inactive ones, for admins.
  case 'allAdmin':
    requireAdmin();
    echo json_encode($controller->getAllAdmin());
    break;

  // Return a single service by its numeric ID.
  case 'byId':
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->getById($id));
    break;

  // Return services filtered by category name.
  case 'byCategory':
    $category = $_GET['category'] ?? '';
    echo json_encode($controller->getByCategory($category));
    break;

  // Create a new service using JSON request payload.
  case 'create':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controller->create($data));
    break;

  // Update an existing service by ID.
  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->update($id, $data));
    break;

  // Toggle the active flag for a service.
  case 'toggleActive':
    requireAdmin();
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($_GET['id'] ?? 0);
    $active = (bool) ($data['active'] ?? false);
    echo json_encode($controller->toggleActive($id, $active));
    break;

  // Delete a service by ID.
  case 'delete':
    requireAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->delete($id));
    break;

  // Respond with an error for invalid actions.
  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>