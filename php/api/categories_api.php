<?php
// Start or resume the session for authorization checks.
session_start();

// Allow requests from any origin and return JSON responses.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Load the categories controller to handle business logic.
require_once __DIR__ . '/../controllers/categories_controller.php';

$controller = new categories_controller();
$action     = $_GET['action'] ?? 'all';

// Ensure the current user is an administrator before allowing protected actions.
function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

switch ($action) {

  // Return all active categories for public use.
  case 'all':
    echo json_encode($controller->getAllActive());
    break;

  // Return all categories, including inactive ones, only for admin users.
  case 'allAdmin':
    requireAdmin();
    echo json_encode($controller->getAllAdmin());
    break;

  // Create a new category from JSON request data.
  case 'create':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controller->create($data));
    break;

  // Update an existing category by ID with JSON request data.
  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->update($id, $data));
    break;

  // Toggle the active state of a category by ID.
  case 'toggleActive':
    requireAdmin();
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($_GET['id'] ?? 0);
    $active = (bool) ($data['active'] ?? false);
    echo json_encode($controller->toggleActive($id, $active));
    break;

  // Delete a category by ID.
  case 'delete':
    requireAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->delete($id));
    break;

  // Handle unsupported action requests.
  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>