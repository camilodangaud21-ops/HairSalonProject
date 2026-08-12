<?php
class category {
  public int $id;
  public string $name;
  public string $label;
  public string $css_class;
  public bool $active;
  public int $display_order;

  public function __construct(int $id, string $name, string $label, string $css_class, bool $active, int $display_order) {
    $this->id = $id;
    $this->name = $name;
    $this->label = $label;
    $this->css_class = $css_class;
    $this->active = $active;
    $this->display_order = $display_order;
  }
}
?>