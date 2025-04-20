<?php
$db_host = '35.212.125.126';
$db_port = '3306';
$db_name = 'dbs5nqdmdcgi92';
$db_user = 'uxj34ztsgesvj';
$db_pass = 'tufts12345#';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection fail: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>