<?php
require_once __DIR__ . '/../config/reviews_crud.php';
// Controller: reviews operations

class reviews_controller {
  private $crud;

  public function __construct() {
    $this->crud = new reviews_crud();
  }

    // ── VALIDATION ──
  private function validate(array $data): array {
    $errors = [];
    if (empty(trim($data['author_name'] ?? ''))) {
      $errors[] = 'El nombre es obligatorio.';
    }
    if (empty(trim($data['rating'] ?? ''))) {
      $errors[] = 'La calificación es obligatoria.';
    }else {
      $rating = (float) $data['rating'];
      if ($rating < 1 || $rating > 5) {
        $errors[] = 'La calificación debe estar entre 1 y 5.';
      }
    }
    if (empty(trim($data['comment'] ?? ''))) {
      $errors[] = 'El comentario es obligatorio.';
    }
    return $errors;
  }
  // ── ACTIONS ──
  public function getAll(): array {
    return $this->crud->getAll();
  }

  public function getAllAdmin(): array {
    return $this->crud->getAllAdmin();
  }

  public function getAverageRating(): array {
    return $this->crud->getAverageRating();
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

  public function feature(int $id, bool $value):array{
    if ($id <= 0) {
      return ['success' => false, 'message' => 'ID inválido.'];
    }

    $ok = $this->crud->feature($id, $value);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al destacar la reseña.'];
  }

  public function delete(int $id):array{
    if($id <= 0){
      return ['success' => false, 'message' => 'ID inválido.'];
    } 
    $ok = $this->crud->delete($id);
    return $ok
      ? ['success' => true]
      : ['success' => false, 'message' => 'Error al eliminar la reseña.'];

  }

}
?>