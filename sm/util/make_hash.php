<?php
require_once __DIR__ . '/../config/config.php';
$pwd = $_GET['pwd'] ?? 'admin123';
echo '<pre>'.password_hash($pwd, PASSWORD_DEFAULT).'</pre>';
