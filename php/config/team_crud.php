<?php
require_once __DIR__ . '/conection.php';
require_once __DIR__ . '/../models/team.php';
// Config: team CRUD
class team_crud {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    //read all (for public site, only active clients)
    public function getAllActive(): array {
    $result = mysqli_query($this->conn, "SELECT * FROM team WHERE active = 1 ORDER BY display_order ASC, id ASC");
    $members = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $members[] = $row;
    }
    return $members;
  }
    
    //read all (for admin dashboard, includes active and inactive)
    public function getAllAdmin(): array {
    $result = mysqli_query($this->conn, "SELECT * FROM team ORDER BY display_order ASC, id ASC");
    $members = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $members[] = $row;
    }
    return $members;
  }
    //read by id
  public function getById(int $id): array|null {
    $result = mysqli_query($this->conn, "SELECT * FROM team WHERE id = $id");
    return mysqli_fetch_assoc($result) ?: null;
  }
    //create staff for the team
  public function create(array $data): bool {
    $name          = mysqli_real_escape_string($this->conn, $data['name']);
    $role          = mysqli_real_escape_string($this->conn, $data['role']);
    $rating        = isset($data['rating']) && $data['rating'] !== '' ? (float) $data['rating'] : 'NULL';
    $photo         = mysqli_real_escape_string($this->conn, $data['photo']);
    $active        = (int) $data['active'];
    $display_order = (int) $data['display_order'];

    $sql = "INSERT INTO team (name, role, rating, photo, active, display_order)
            VALUES ('$name','$role',$rating,'$photo',1,$display_order)";

    return mysqli_query($this->conn, $sql);
  }
    //update staff for the team
    public function update(int $id, array $data): bool {
    $name          = mysqli_real_escape_string($this->conn, $data['name']);
    $role          = mysqli_real_escape_string($this->conn, $data['role']);
    $rating        = isset($data['rating']) && $data['rating'] !== '' ? (float) $data['rating'] : 'NULL';
    $photo         = mysqli_real_escape_string($this->conn, $data['photo'] ?? '');
    $display_order = (int) ($data['display_order'] ?? 0);

    $sql = "UPDATE team SET
              name          = '$name',
              role          = '$role',
              rating        = $rating,
              photo         = '$photo',
              display_order = $display_order
            WHERE id = $id";

    return mysqli_query($this->conn, $sql);
  }

    //toggle active status for a staff member
  public function toggleActive(int $id, bool $active): bool {
    $value = $active ? 1 : 0;
    return mysqli_query($this->conn, "UPDATE team SET active = $value WHERE id = $id");
  }

    //delete staff for the team
  public function delete(int $id): bool {
    return mysqli_query($this->conn, "DELETE FROM team WHERE id = $id");
  }
}
?>