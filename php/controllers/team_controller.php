<?php
require_once __DIR__ . '/../config/team_crud.php';
// Controller: team operations

class team_controller {
  private $crud;

  public function __construct() {
    $this->crud = new team_crud();
  }

  private function validate(array $data): array {
    $errors = [];

    if (empty(trim($data['name'] ?? ''))) {
      $errors[] = 'El nombre es obligatorio.';
    }

    if (empty(trim($data['role'] ?? ''))) {
      $errors[] = 'El rol es obligatorio.';
    }

    if (isset($data['rating']) && $data['rating'] !== '') {
      $rating = (float) $data['rating'];
      if ($rating < 0 || $rating > 5) {
        $errors[] = 'El rating debe estar entre 0 y 5.';
      }
    }

    if (isset($data['display_order']) && !is_numeric($data['display_order'])) {
      $errors[] = 'El orden debe ser un número.';
    }

    return $errors;
  }

  public function getAllActive(): array {
    return $this->crud->getAllActive();
  }

  public function getAllAdmin(): array {
    return $this->crud->getAllAdmin();
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

    $ok = $this->crud->delete($id);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al eliminar.'];
  }
}
?>