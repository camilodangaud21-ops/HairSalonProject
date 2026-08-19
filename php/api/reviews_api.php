<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/reviews_controller.php';

$controller = new reviews_controller();
$action     = $_GET['action'] ?? 'all';

function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}

function requireLogin() {
  if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para dejar una reseña']);
    exit;
  }
}

switch ($action) {

  // Public: all active reviews + average
  case 'all':
    echo json_encode($controller->getAll());
    break;

  case 'average':
    echo json_encode($controller->getAverageRating());
    break;

  // Client: create review (must be logged in)
  case 'create':
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $data['author_name'] = $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'];
    echo json_encode($controller->create($data));
    break;

  // Admin only
  case 'allAdmin':
    requireAdmin();
    echo json_encode($controller->getAllAdmin());
    break;

  case 'feature':
    requireAdmin();
    $id    = (int) ($_GET['id'] ?? 0);
    $data  = json_decode(file_get_contents('php://input'), true);
    $value = (bool) ($data['featured'] ?? false);
    echo json_encode($controller->feature($id, $value));
    break;

  case 'delete':
    requireAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->delete($id));
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>