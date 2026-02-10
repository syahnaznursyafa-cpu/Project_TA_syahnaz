<?php
// connect.php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_TA_syahnaz";

$conn = new mysqli($host, $user, $pass, $db);

$conn->set_charset("utf8mb4");
?>
