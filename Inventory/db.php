<?php
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "home_inventory";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>