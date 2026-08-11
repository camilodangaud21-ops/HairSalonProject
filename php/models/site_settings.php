<?php
// Model: site_settings
class site_settings {
  public int    $id;
  public string $setting_key;
  public string $setting_value;

  public function __construct(int $id, string $setting_key  , string $setting_value) {
    $this->id = $id;
    $this->setting_key = $setting_key;
    $this->setting_value = $setting_value;
  }
}