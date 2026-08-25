<?php
require_once __DIR__ . '/conection.php';
require_once __DIR__ . '/../models/reviews.php';

class reviews_crud {
  private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  // reseñas activas, para el sitio público y el cálculo del resumen
  public function getAllActive(): array {
    $result  = mysqli_query($this->conn, "SELECT * FROM reviews WHERE active = 1 ORDER BY created_at DESC");
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }
    return $reviews;
  }

  // solo las destacadas, para mostrar en la lista pública
  public function getFeatured(): array {
    $result  = mysqli_query($this->conn, "SELECT * FROM reviews WHERE active = 1 AND featured = 1 ORDER BY created_at DESC");
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }
    return $reviews;
  }

  // todas, para el dashboard admin
  public function getAllAdmin(): array {
    $result  = mysqli_query($this->conn, "SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }
    return $reviews;
  }

  public function create(array $data): bool {
    $author_name = mysqli_real_escape_string($this->conn, $data['author_name']);
    $rating      = (int) $data['rating'];
    $comment     = mysqli_real_escape_string($this->conn, $data['comment'] ?? '');
    $featured    = (int) ($data['featured'] ?? 0);

    $sql = "INSERT INTO reviews (author_name, rating, comment, featured, active, created_at)
            VALUES ('$author_name', $rating, '$comment', $featured, 1, NOW())";

    return mysqli_query($this->conn, $sql);
  }

  public function update(int $id, array $data): bool {
    $author_name = mysqli_real_escape_string($this->conn, $data['author_name']);
    $rating      = (int) $data['rating'];
    $comment     = mysqli_real_escape_string($this->conn, $data['comment'] ?? '');
    $featured    = (int) ($data['featured'] ?? 0);

    $sql = "UPDATE reviews SET
              author_name = '$author_name',
              rating      = $rating,
              comment     = '$comment',
              featured    = $featured
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }

  public function toggleFeatured(int $id, bool $featured): bool {
    $value = $featured ? 1 : 0;
    return mysqli_query($this->conn, "UPDATE reviews SET featured = $value WHERE id = $id");
  }

  public function toggleActive(int $id, bool $active): bool {
    $value = $active ? 1 : 0;
    return mysqli_query($this->conn, "UPDATE reviews SET active = $value WHERE id = $id");
  }

  public function delete(int $id): bool {
    return mysqli_query($this->conn, "DELETE FROM reviews WHERE id = $id");
  }
}
?>