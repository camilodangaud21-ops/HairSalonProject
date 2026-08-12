<?php
// Return JSON responses and allow cross-origin API calls.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Load the team controller to manage team member data.
require_once __DIR__ . '/../controllers/team_controller.php';

$controller = new team_controller();
$action     = $_GET['action'] ?? 'all';

// Require admin authorization for protected actions.
function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

switch ($action) {

  // Return all active team members for public display.
  case 'all':
    echo json_encode($controller->getAllActive());
    break;

  // Return all team members, including inactive ones, for admins.
  case 'allAdmin':
    requireAdmin();
    echo json_encode($controller->getAllAdmin());
    break;

  // Create a new team member record.
  case 'create':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controller->create($data));
    break;

  // Update an existing team member by ID.
  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->update($id, $data));
    break;

  // Toggle whether a team member is active.
  case 'toggleActive':
    requireAdmin();
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($_GET['id'] ?? 0);
    $active = (bool) ($data['active'] ?? false);
    echo json_encode($controller->toggleActive($id, $active));
    break;

  // Delete a team member entry by ID.
  case 'delete':
    requireAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->delete($id));
    break;

  // Respond with an error for unsupported actions.
  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>