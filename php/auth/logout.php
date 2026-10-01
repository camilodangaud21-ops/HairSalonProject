<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/app.php';

start_app_session();
destroy_app_session();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Location: ' . app_url('index.php'));
exit;
?>
