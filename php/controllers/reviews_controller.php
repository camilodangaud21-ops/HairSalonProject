<?php
require_once __DIR__ . '/../config/reviews_crud.php';

class reviews_controller {
  private $crud;

  public function __construct() {
    $this->crud = new reviews_crud();
  }

  private function validate(array $data): array {
    $errors = [];

    if (empty(trim($data['author_name'] ?? ''))) {
      $errors[] = 'El nombre es obligatorio.';
    }

    if (!isset($data['rating']) || !is_numeric($data['rating']) || (int) $data['rating'] < 1 || (int) $data['rating'] > 5) {
      $errors[] = 'La calificación debe ser un número entre 1 y 5.';
    }

    if (empty(trim($data['comment'] ?? ''))) {
      $errors[] = 'El comentario es obligatorio.';
    }

    return $errors;
  }

  // ── resumen calculado: promedio + conteo por cada estrella (1-5) ──
  public function getSummary(): array {
    $reviews = $this->crud->getAllActive();
    $count   = count($reviews);

    $summary = [
      'average' => 0,
      'count'   => $count,
      'stars'   => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
    ];

    if ($count === 0) {
      return $summary;
    }

    $sum = 0;
    foreach ($reviews as $r) {
      $rating = (int) $r['rating'];
      $sum += $rating;
      if (isset($summary['stars'][$rating])) {
        $summary['stars'][$rating]++;
      }
    }

    $summary['average'] = round($sum / $count, 1);
    return $summary;
  }

  public function getFeatured(): array {
    return $this->crud->getFeatured();
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
      : ['success' => false, 'message' => 'Error al guardar la reseña.'];
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
      : ['success' => false, 'message' => 'Error al actualizar la reseña.'];
  }

  public function toggleFeatured(int $id, bool $featured): array {
    if ($id <= 0) {
      return ['success' => false, 'message' => 'ID inválido.'];
    }

    $ok = $this->crud->toggleFeatured($id, $featured);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al destacar la reseña.'];
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