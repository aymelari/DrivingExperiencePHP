<?php
// Database connection file
$conn = new mysqli("mysql-sema.alwaysdata.net","sema", "AysuSema123.", "sema_drivingexperience");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>