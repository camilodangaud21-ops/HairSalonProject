<?php
require_once __DIR__ . '/../models/site_settings.php';
require_once __DIR__ . '/conection.php';

class settings_crud {
private $conn;

  public function __construct() {
    global $conn;
    $this->conn = $conn;
  }

  // read all settings as raw rows (useful for the dashboard)
  public function getAll(): array {
    $result   = mysqli_query($this->conn, "SELECT * FROM site_settings");
    $settings = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $settings[] = $row;
    }
    return $settings;
  }

  // read all settings as key => value (useful for rendering the public site)
  public function getAllAsMap(): array {
    $rows = $this->getAll();
    $map  = [];
    foreach ($rows as $row) {
      $map[$row['setting_key']] = $row['setting_value'];
    }
    return $map;
  }

  // read a single setting by key
  public function getByKey(string $key): string|null {
    $key    = mysqli_real_escape_string($this->conn, $key);
    $result = mysqli_query($this->conn, "SELECT setting_value FROM site_settings WHERE setting_key = '$key'");
    $row    = mysqli_fetch_assoc($result);
    return $row ? $row['setting_value'] : null;
  }

  // update a single setting (only update the value, not the key)
  public function updateByKey(string $key, string $value): bool {
    $key   = mysqli_real_escape_string($this->conn, $key);
    $value = mysqli_real_escape_string($this->conn, $value);
    return mysqli_query($this->conn, "UPDATE site_settings SET setting_value = '$value' WHERE setting_key = '$key'");
  }

  // update several settings at once (for the dashboard form)
  public function updateMany(array $data): bool {
    $ok = true;
    foreach ($data as $key => $value) {
      if (!$this->updateByKey($key, (string) $value)) {
        $ok = false;
      }
    }
    return $ok;
  }
}
?>
