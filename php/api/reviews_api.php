<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/reviews_controller.php';

$controller = new reviews_controller();
$action     = $_GET['action'] ?? 'featured';

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

function requireClient() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Solo los clientes pueden dejar reseñas']);
    exit;
  }
}

switch ($action) {

  case 'featured':
    echo json_encode($controller->getFeatured());
    break;

  case 'all':
    echo json_encode($controller->getAllActive());
    break;

  case 'summary':
    echo json_encode($controller->getSummary());
    break;

  case 'allAdmin':
    requireAdmin();
    echo json_encode($controller->getAllAdmin());
    break;

  case 'create':
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    // Admins can set a custom author name (adding a review on behalf of a client).
    // Regular clients always get their own session name, so they can't spoof another author.
    if ($_SESSION['user']['role'] !== 'admin' || empty(trim($data['author_name'] ?? ''))) {
      $data['author_name'] = $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'];
    }
    echo json_encode($controller->create($data));
  break;

  case 'createClient':
    requireClient();
    $data = json_decode(file_get_contents('php://input'), true);
    $data['author_name'] = $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'];
    echo json_encode($controller->create($data));
    break;

  case 'update':
    requireAdmin();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($_GET['id'] ?? 0);
    echo json_encode($controller->update($id, $data));
    break;

  case 'toggleFeatured':
    requireAdmin();
    $data     = json_decode(file_get_contents('php://input'), true);
    $id       = (int) ($_GET['id'] ?? 0);
    $featured = (bool) ($data['featured'] ?? false);
    echo json_encode($controller->toggleFeatured($id, $featured));
    break;

  case 'toggleActive':
    requireAdmin();
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($_GET['id'] ?? 0);
    $active = (bool) ($data['active'] ?? false);
    echo json_encode($controller->toggleActive($id, $active));
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