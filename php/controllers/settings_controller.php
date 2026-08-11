<?php
require_once __DIR__ . '/../config/settings_crud.php';
// Controller: settings operations

class settings_controller {
  private $crud;

  // allowed keys — prevents sending arbitrary keys in request body
  private const ALLOWED_KEYS = [
    'whatsapp_number',
    'about_us_text',
    'schedule_today',
    'address',
  ];

  public function __construct() {
    $this->crud = new settings_crud();
  }

  // ── VALIDATION ──
  private function validate(array $data): array {
    $errors = [];

    if (empty($data)) {
      $errors[] = 'No se recibieron datos para actualizar.';
      return $errors;
    }

    foreach ($data as $key => $value) {
      if (!in_array($key, self::ALLOWED_KEYS, true)) {
        $errors[] = "La clave '$key' no es válida.";
        continue;
      }

      if (trim((string) $value) === '') {
        $errors[] = "El campo '$key' no puede estar vacío.";
      }
    }

    if (isset($data['whatsapp_number']) && !preg_match('/^\d{10,15}$/', $data['whatsapp_number'])) {
      $errors[] = 'El número de WhatsApp debe tener solo dígitos (10 a 15 números, con código de país).';
    }

    return $errors;
  }

  // ── ACTIONS ──
  public function getAll(): array {
    return $this->crud->getAll();
  }

  public function getAllAsMap(): array {
    return $this->crud->getAllAsMap();
  }

  public function updateMany(array $data): array {
    $errors = $this->validate($data);
    if (!empty($errors)) {
      return ['success' => false, 'message' => implode(' ', $errors)];
    }

    $ok = $this->crud->updateMany($data);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al guardar la configuración.'];
  }
}
?>