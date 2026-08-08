<?php
require_once __DIR__ . '/../config/services_crud.php';

class services_controller {
  private $crud;

  public function __construct() {
    $this->crud = new services_crud();
  }

  // ── VALIDACIÓN ──
  private function validate(array $data): array {
    $errors = [];

    if (empty(trim($data['name'] ?? ''))) {
      $errors[] = 'El nombre es obligatorio.';
    }

    if (empty(trim($data['category'] ?? ''))) {
      $errors[] = 'La categoría es obligatoria.';
    }

    if (!isset($data['price']) || !is_numeric($data['price']) || (float) $data['price'] <= 0) {
      $errors[] = 'El precio debe ser un número mayor a 0.';
    }

    if (empty(trim($data['duration'] ?? ''))) {
      $errors[] = 'La duración es obligatoria.';
    }

    if (isset($data['from_of']) && !in_array((string) $data['from_of'], ['0', '1'], true)) {
      $errors[] = 'El valor de "desde" no es válido.';
    }

    if (isset($data['popular']) && !in_array((string) $data['popular'], ['0', '1'], true)) {
      $errors[] = 'El valor de "popular" no es válido.';
    }

    return $errors;
  }

  // ── ACCIONES ──
  public function getAll(): array {
    return $this->crud->getAll();
  }

  public function getAllAdmin(): array {
    return $this->crud->getAllAdmin();
  }

  public function getById(int $id): array|null {
    if ($id <= 0) return null;
    return $this->crud->getById($id);
  }

  public function getByCategory(string $category): array {
    if (trim($category) === '') return [];
    return $this->crud->getByCategory($category);
  }

  public function create(array $data): array {
    $errors = $this->validate($data);
    if (!empty($errors)) {
      return ['success' => false, 'message' => implode(' ', $errors)];
    }

    $ok = $this->crud->create($data);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al guardar en la base de datos.'];
  }

  public function update(int $id, array $data): array {
    if ($id <= 0) {
      return ['success' => false, 'message' => 'ID inválido.'];
    }

    $errors = $this->validate($data);
    if (!empty($errors)) {
      return ['success' => false, 'message' => implode(' ', $errors)];
    }

    $ok = $this->crud->update($id, $data);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al actualizar en la base de datos.'];
  }

  public function toggleActive(int $id, bool $active): array {
    if ($id <= 0) {
      return ['success' => false, 'message' => 'ID inválido.'];
    }

    $ok = $this->crud->toggleActive($id, $active);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al cambiar el estado.'];
  }

  public function delete(int $id): array {
    if ($id <= 0) {
      return ['success' => false, 'message' => 'ID inválido.'];
    }

    $ok = $this->crud->hardDelete($id);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al eliminar.'];
  }
}
?>