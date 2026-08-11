<?php
// Auth: logout script
session_start();
$_SESSION = [];
session_destroy();
header('Location: /index.php');
exit;
?>