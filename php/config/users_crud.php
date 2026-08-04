<?php
require_once 'conection.php';
require_once '../models/users.php';

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

    $sql = "INSERT INTO users (first_name, last_name, email, password, role, active, created_at, updated_at)
            VALUES ('$first_name','$last_name','$email','$password','$role',1,NOW(),NOW())";

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
              email      = '$email',
              updated_at = NOW()
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }
//delete user
  public function delete(int $id): bool {
    return mysqli_query($this->conn, "UPDATE users SET active = 0 WHERE id = $id");
  }

}
?>