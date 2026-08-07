<?php
require_once __DIR__ . '/conection.php';
require_once __DIR__ . '/../models/services.php';

class services_crud {
  private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  //read all (for public site, only active services)
  public function getAll(): array {
    $result   = mysqli_query($this->conn, "SELECT * FROM services WHERE active = 1");
    $services = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $services[] = $row;
    }
    return $services;
  }

  //read all (for admin dashboard, includes active and inactive)
  public function getAllAdmin(): array {
    $result   = mysqli_query($this->conn, "SELECT * FROM services");
    $services = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $services[] = $row;
    }
    return $services;
  }

  //read by id
  public function getById(int $id): array|null {
    $result = mysqli_query($this->conn, "SELECT * FROM services WHERE id = $id AND active = 1");
    return mysqli_fetch_assoc($result) ?: null;
  }

  //read by category
  public function getByCategory(string $category): array {
    $category = mysqli_real_escape_string($this->conn, $category);
    $result   = mysqli_query($this->conn, "SELECT * FROM services WHERE category = '$category' AND active = 1");
    $services = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $services[] = $row;
    }
    return $services;
  }

  //create service
  public function create(array $data): bool {
    $name        = mysqli_real_escape_string($this->conn, $data['name']);
    $category    = mysqli_real_escape_string($this->conn, $data['category']);
    $price       = (int) $data['price'];
    $from_of     = (int) $data['from_of'];
    $duration    = mysqli_real_escape_string($this->conn, $data['duration']);
    $popular     = (int) $data['popular'];
    $description = mysqli_real_escape_string($this->conn, $data['description']);
    $image       = mysqli_real_escape_string($this->conn, $data['image']);

    $sql = "INSERT INTO services 
            (name, category, price, from_of, duration, popular, description, image, active, created_at, update_at)
            VALUES 
            ('$name','$category',$price,$from_of,'$duration',$popular,'$description','$image',1,NOW(),NOW())";

    return mysqli_query($this->conn, $sql);
  }

  //update service
  public function update(int $id, array $data): bool {
    $name        = mysqli_real_escape_string($this->conn, $data['name']);
    $category    = mysqli_real_escape_string($this->conn, $data['category']);
    $price       = (int) $data['price'];
    $from_of     = (int) $data['from_of'];
    $duration    = mysqli_real_escape_string($this->conn, $data['duration']);
    $popular     = (int) $data['popular'];
    $description = mysqli_real_escape_string($this->conn, $data['description']);
    $image       = mysqli_real_escape_string($this->conn, $data['image']);

    $sql = "UPDATE services SET
              name        = '$name',
              category    = '$category',
              price       = $price,
              from_of     = $from_of,
              duration    = '$duration',
              popular     = $popular,
              description = '$description',
              image       = '$image',
              update_at   = NOW()
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }

  //activate/deactivate (show or hide to the client without deleting the record)
  public function toggleActive(int $id, bool $active): bool {
    $value = $active ? 1 : 0;
    return mysqli_query($this->conn, "UPDATE services SET active = $value WHERE id = $id");
  }

  //delete service (soft delete, keep record in case you need it elsewhere)
  public function delete(int $id): bool {
    return mysqli_query($this->conn, "UPDATE services SET active = 0 WHERE id = $id");
  }

  //delete service (hard delete, permanently removes the record)
  public function hardDelete(int $id): bool {
    return mysqli_query($this->conn, "DELETE FROM services WHERE id = $id");
  }
}
?>