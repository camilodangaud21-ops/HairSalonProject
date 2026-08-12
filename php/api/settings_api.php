<?php
// Start the session for authorization checks.
session_start();

// Allow cross-origin requests and force JSON output.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Load the settings controller for configuration operations.
require_once __DIR__ . '/../controllers/settings_controller.php';

$controller = new settings_controller();
$action     = $_GET['action'] ?? 'all';

// Require admin privileges for all settings operations.
function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

switch ($action) {

  // Return all application settings, admin-only.
  case 'all':
    requireAdmin();
    echo json_encode($controller->getAll());
    break;

  // Update multiple settings values from JSON payload.
  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controller->updateMany($data));
    break;

  // Return error for unsupported actions.
  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>