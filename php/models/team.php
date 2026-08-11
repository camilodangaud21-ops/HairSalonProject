<?php
// Model: team
class team{
public int $id;
public string $name;
public string $role;
public float $rating;
public string $photo;
public bool $active;
public int $display_order;
public function __construct(int $id, string $name, string $role, float $rating, string $photo, bool $active, int $display_order) {
    $this->id            = $id;
    $this->name          = $name;
    $this->role          = $role;
    $this->rating        = $rating;
    $this->photo         = $photo;
    $this->active        = $active;
    $this->display_order = $display_order;
  }

}
?>