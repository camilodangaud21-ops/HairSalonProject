<?php
session_start();
header("Content-Type: application/json");

function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
  }
}
requireAdmin();

$allowedTypes   = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
$allowedFolders = ['logos', 'portfolio', 'services', 'team'];
$maxSize        = 5 * 1024 * 1024; // 5MB

$folder    = $_POST['folder'] ?? '';
$subfolder = $_POST['subfolder'] ?? '';
$oldPath   = $_POST['old_path'] ?? '';

if (!in_array($folder, $allowedFolders, true)) {
  echo json_encode(['success' => false, 'message' => 'Carpeta no válida.']);
  exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success' => false, 'message' => 'No se recibió ninguna imagen válida.']);
  exit;
}

$file = $_FILES['image'];

if (!in_array($file['type'], $allowedTypes, true)) {
  echo json_encode(['success' => false, 'message' => 'Solo se permiten imágenes JPG, PNG o WEBP.']);
  exit;
}

if ($file['size'] > $maxSize) {
  echo json_encode(['success' => false, 'message' => 'La imagen no puede pesar más de 5MB.']);
  exit;
}

$safeSubfolder = '';
if ($folder === 'services' && $subfolder !== '') {
  $slug = strtolower(str_replace(' ', '-', $subfolder));
  $safeSubfolder = preg_replace('/[^a-z0-9\-]/', '', $slug);
}

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$safeExt  = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? $ext : 'jpg';
$filename = uniqid('img_', true) . '.' . $safeExt;

$relativeDir = $safeSubfolder !== '' ? $folder . '/' . $safeSubfolder : $folder;
$targetDir   = __DIR__ . '/../../assets/images/' . $relativeDir . '/';

if (!is_dir($targetDir)) {
  mkdir($targetDir, 0755, true);
}

$targetPath = $targetDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
  echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen en el servidor.']);
  exit;
}

if ($oldPath !== '' && strpos($oldPath, 'assets/images/') === 0) {
  $oldFullPath = __DIR__ . '/../../' . $oldPath;
  if (is_file($oldFullPath)) {
    @unlink($oldFullPath);
  }
}

$publicPath = 'assets/images/' . $relativeDir . '/' . $filename;
echo json_encode(['success' => true, 'path' => $publicPath]);
?>