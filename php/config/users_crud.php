<?php
require_once __DIR__ . '/conection.php';
require_once __DIR__ . '/../models/users.php';
// Config: users CRUD

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
    $email_verified = !empty($data['email_verified']) ? 1 : 0;
    $verification_token_hash = !empty($data['verification_token_hash'])
      ? mysqli_real_escape_string($this->conn, $data['verification_token_hash'])
      : '';
    $verification_expires_at = !empty($data['verification_expires_at'])
      ? mysqli_real_escape_string($this->conn, $data['verification_expires_at'])
      : '';

    $sql = "INSERT INTO users (
              first_name, last_name, email, password, role,
              email_verified, email_verification_token, email_verification_expires
            ) VALUES (
              '$first_name','$last_name','$email','$password','$role',
              $email_verified,
              " . ($verification_token_hash !== '' ? "'$verification_token_hash'" : "NULL") . ",
              " . ($verification_expires_at !== '' ? "'$verification_expires_at'" : "NULL") . "
            )";

    return mysqli_query($this->conn, $sql);
  }

  public function verifyEmailByTokenHash(string $tokenHash): array|null {
    $tokenHash = mysqli_real_escape_string($this->conn, $tokenHash);
    $result = mysqli_query($this->conn, "SELECT * FROM users
      WHERE email_verification_token = '$tokenHash'
        AND email_verification_expires > NOW()
        AND email_verified = 0
      LIMIT 1");

    return mysqli_fetch_assoc($result) ?: null;
  }

  public function markEmailVerified(int $id): bool {
    return mysqli_query($this->conn, "UPDATE users SET
      email_verified = 1,
      email_verification_token = NULL,
      email_verification_expires = NULL
      WHERE id = $id");
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