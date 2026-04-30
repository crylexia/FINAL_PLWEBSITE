<?php
$conn = new mysqli("localhost", "root", "", "lgu_marketplace");

if ($conn->connect_error) {
    die("Database Connection Failed");
}
?>
