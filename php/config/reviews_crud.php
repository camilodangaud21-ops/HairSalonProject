<?php
require_once  __DIR__ . '/conection.php';
require_once  __DIR__ . '/../models/reviews.php';

class reviews_crud {
  private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  // read all reviews (public site)
  public function getAll(): array {
    $result  = mysqli_query($this->conn, "SELECT * FROM reviews WHERE active = 1 ORDER BY created_at DESC");
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }
    return $reviews;
  }

  // calculated average rating and totals
  public function getAverageRating(): array {
    $result = mysqli_query($this->conn, "
      SELECT 
        ROUND(AVG(rating), 1) AS average,
        COUNT(*)              AS total,
        SUM(rating = 5)       AS five,
        SUM(rating = 4)       AS four,
        SUM(rating = 3)       AS three,
        SUM(rating = 2)       AS two,
        SUM(rating = 1)       AS one
      FROM reviews 
      WHERE active = 1
    ");
    return mysqli_fetch_assoc($result) ?: [];
  }

  // read all reviews for admin dashboard (includes inactive)
  public function getAllAdmin(): array {
    $result  = mysqli_query($this->conn, "SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }
    return $reviews;
  }

  // create review
  public function create(array $data): bool {
    $author_name = mysqli_real_escape_string($this->conn, $data['author_name']);
    $rating      = (int) $data['rating'];
    $comment     = mysqli_real_escape_string($this->conn, $data['comment']);

    $sql = "INSERT INTO reviews (author_name, rating, comment, featured, active, created_at)
            VALUES ('$author_name', $rating, '$comment', 0, 1, NOW())";

    return mysqli_query($this->conn, $sql);
  }

  // soft delete
  public function delete(int $id): bool {
    return mysqli_query($this->conn, "UPDATE reviews SET active = 0 WHERE id = $id");
  }

  // toggle featured
  public function feature(int $id, bool $value): bool {
    $val = (int) $value;
    return mysqli_query($this->conn, "UPDATE reviews SET featured = $val WHERE id = $id");
  }

}
?>