<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET["id"];

mysqli_query($conn, "DELETE FROM products WHERE id='$id'");

header("Location: ../user/products.php");