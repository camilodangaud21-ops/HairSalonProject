<?php
class users {
    public int    $id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(int $id, string $first_name, string $last_name, string $email, string $password, string $role) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
?>