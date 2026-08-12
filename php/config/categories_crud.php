<?php
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/conection.php';

class category_crud {
  private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  //read all categories
  
  public function getAllActive(): array {
    $result = mysqli_query($this->conn, "SELECT * FROM categories WHERE active = 1 ORDER BY display_order ASC, id ASC");
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $categories[] = $row;
    }
    return $categories;
  }

  public function getAllAdmin(): array {
    $result = mysqli_query($this->conn, "SELECT * FROM categories ORDER BY display_order ASC, id ASC");
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $categories[] = $row;
    }
    return $categories;
  }

  public function getByName(string $name): array|null {
    $name   = mysqli_real_escape_string($this->conn, $name);
    $result = mysqli_query($this->conn, "SELECT * FROM categories WHERE name = '$name'");
    return mysqli_fetch_assoc($result) ?: null;
  }

  public function create(array $data): bool {
    $name          = mysqli_real_escape_string($this->conn, $data['name']);
    $label         = mysqli_real_escape_string($this->conn, $data['label']);
    $css_class     = mysqli_real_escape_string($this->conn, $data['css_class']);
    $display_order = (int) ($data['display_order'] ?? 0);

    $sql = "INSERT INTO categories (name, label, css_class, active, display_order)
            VALUES ('$name','$label','$css_class',1,$display_order)";

    return mysqli_query($this->conn, $sql);
  }

  public function update(int $id, array $data): bool {
    $name          = mysqli_real_escape_string($this->conn, $data['name']);
    $label         = mysqli_real_escape_string($this->conn, $data['label']);
    $css_class     = mysqli_real_escape_string($this->conn, $data['css_class']);
    $display_order = (int) ($data['display_order'] ?? 0);

    $sql = "UPDATE categories SET
              name          = '$name',
              label         = '$label',
              css_class     = '$css_class',
              display_order = $display_order
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }

  public function toggleActive(int $id, bool $active): bool {
    $value = $active ? 1 : 0;
    return mysqli_query($this->conn, "UPDATE categories SET active = $value WHERE id = $id");
  }

  public function delete(int $id): bool {
    return mysqli_query($this->conn, "DELETE FROM categories WHERE id = $id");
  }

}
?>