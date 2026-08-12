<?php
require_once __DIR__ . '/../config/categories_crud.php';

class categories_controller {
  private $crud;

  public function __construct() {
    $this->crud = new categories_crud();
  }

  private function validate(array $data, ?int $excludeId = null): array {
    $errors = [];

    $name = trim($data['name'] ?? '');
    if ($name === '') {
      $errors[] = 'El nombre de la categoría es obligatorio.';
    } else {
      $existing = $this->crud->getByName($name);
      if ($existing && (int) $existing['id'] !== $excludeId) {
        $errors[] = 'Ya existe una categoría con ese nombre.';
      }
    }

    if (empty(trim($data['label'] ?? ''))) {
      $errors[] = 'La etiqueta es obligatoria.';
    }

    if (empty(trim($data['css_class'] ?? ''))) {
      $errors[] = 'La clase CSS es obligatoria.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $data['css_class'])) {
      $errors[] = 'La clase CSS solo puede tener letras, números, guiones y guiones bajos.';
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

    $errors = $this->validate($data, $id);
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