<?php
session_start();
header("Content-Type: application/json");

// DEBUG TEMPORAL — bórralo después de confirmar
error_log("SESSION: " . print_r($_SESSION, true));
error_log("FILES: " . print_r($_FILES, true));
error_log("POST: " . print_r($_POST, true));

function requireAdmin() {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado — sesión: ' . json_encode($_SESSION)]);
    exit;
  }
}
requireAdmin();

$allowedTypes   = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']; // agregamos image/jpg
$allowedFolders = ['services', 'team', 'site'];
$maxSize        = 5 * 1024 * 1024;

$folder = $_POST['folder'] ?? '';

if (!in_array($folder, $allowedFolders, true)) {
  echo json_encode(['success' => false, 'message' => 'Carpeta no válida: ' . $folder]);
  exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  $errorCode = $_FILES['image']['error'] ?? 'no file';
  echo json_encode(['success' => false, 'message' => 'Error de archivo: ' . $errorCode]);
  exit;
}

$file = $_FILES['image'];

if (!in_array($file['type'], $allowedTypes, true)) {
  echo json_encode(['success' => false, 'message' => 'Tipo no permitido: ' . $file['type']]);
  exit;
}

if ($file['size'] > $maxSize) {
  echo json_encode(['success' => false, 'message' => 'Imagen muy grande: ' . $file['size']]);
  exit;
}

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$safeExt  = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? $ext : 'jpg';
$filename = uniqid('img_', true) . '.' . $safeExt;

$targetDir = __DIR__ . '/../../assets/images/' . $folder . '/';
if (!is_dir($targetDir)) {
  mkdir($targetDir, 0755, true);
}

$targetPath = $targetDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
  echo json_encode(['success' => false, 'message' => 'Error al mover archivo. TargetDir: ' . $targetDir]);
  exit;
}

$publicPath = 'assets/images/' . $folder . '/' . $filename;
echo json_encode(['success' => true, 'path' => $publicPath]);