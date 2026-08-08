<?php
require_once __DIR__ . '/conection.php';
require_once __DIR__ . '/../models/users.php';

class users_crud {
  private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  //read all
  public function getAll(): array {
    $result = mysqli_query($this->conn, "SELECT * FROM users");
    $users  = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $users[] = $row;
    }
    return $users;
  }

  //read by id
  public function getById(int $id): array|null {
    $result = mysqli_query($this->conn, "SELECT * FROM users WHERE id = $id");
    return mysqli_fetch_assoc($result) ?: null;
  }

  //read by email
  public function getByEmail(string $email): array|null {
    $email  = mysqli_real_escape_string($this->conn, $email);
    $result = mysqli_query($this->conn, "SELECT * FROM users WHERE email = '$email'");
    return mysqli_fetch_assoc($result) ?: null;
  }

  //create user
  public function create(array $data): bool {
    $first_name = mysqli_real_escape_string($this->conn, $data['first_name']);
    $last_name  = mysqli_real_escape_string($this->conn, $data['last_name']);
    $email      = mysqli_real_escape_string($this->conn, $data['email']);
    $password   = password_hash($data['password'], PASSWORD_BCRYPT);
    $role       = mysqli_real_escape_string($this->conn, $data['role']);

    $sql = "INSERT INTO users (first_name, last_name, email, password, role)
            VALUES ('$first_name','$last_name','$email','$password','$role')";

    return mysqli_query($this->conn, $sql);
  }

  //update user
  public function update(int $id, array $data): bool {
    $first_name = mysqli_real_escape_string($this->conn, $data['first_name']);
    $last_name  = mysqli_real_escape_string($this->conn, $data['last_name']);
    $email      = mysqli_real_escape_string($this->conn, $data['email']);

    $sql = "UPDATE users SET
              first_name = '$first_name',
              last_name  = '$last_name',
              email      = '$email'
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }

  //delete user (hard delete, since there is no 'active' column)
  public function delete(int $id): bool {
    return mysqli_query($this->conn, "DELETE FROM users WHERE id = $id");
  }

}
?>