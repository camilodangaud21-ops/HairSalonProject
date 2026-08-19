<?php
class reviews {
  public int $id;
  public string $author_name;
  public int $rating;
  public ?string $comment;
  public bool $featured;
  public bool $active;
  public string $created_at;

  public function __construct(int $id, string $author_name, int $rating, ?string $comment, bool $featured, bool $active, string $created_at) {
    $this->id = $id;
    $this->author_name = $author_name;
    $this->rating = $rating;
    $this->comment = $comment;
    $this->featured = $featured;
    $this->active = $active;
    $this->created_at = $created_at;
  }
}