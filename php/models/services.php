<?php
class Servicio {
  public int    $id;
  public string $name;
  public string $category;
  public int    $price;
  public bool   $from_of;
  public string $duration;
  public bool   $popular;
  public string $description;
  public string $image;
  public bool   $active;
  public string $created_at;
  public string $updated_at;

  public function __construct(int $id, string $name, string $category, int $price, bool $from_of, string $duration, bool $popular, string $description, string $image, bool $active, string $created_at, string $updated_at) {
    $this->id = $id;
    $this->name = $name;
    $this->category = $category;
    $this->price = $price;
    $this->from_of = $from_of;
    $this->duration = $duration;
    $this->popular = $popular;
    $this->description = $description;
    $this->image = $image;
    $this->active = $active;
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
  }
}
?>