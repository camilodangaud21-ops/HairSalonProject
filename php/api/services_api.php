<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../config/conection.php';
require_once '../config/services_crud.php';

$crud   = new services_crud();
$action = $_GET['action'] ?? 'all';

switch ($action) {

  case 'all':
    echo json_encode($crud->getAll());
    break;

  case 'byId':
    $id = (int) $_GET['id'];
    echo json_encode($crud->getById($id));
    break;

  case 'byCategory':
    $category = $_GET['category'] ?? '';
    echo json_encode($crud->getByCategory($category));
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>