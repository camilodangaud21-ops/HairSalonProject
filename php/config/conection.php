<?php
$localhost = "localhost";
$username  = "root";
$password  = "";
$database  = "bd_hair_salon";

$conn = mysqli_connect($localhost, $username, $password, $database);

if (!$conn) {
  die("Error de conexión: " . mysqli_connect_error());
}
?>