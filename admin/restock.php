<?php
session_start();
include "../config/db.php";

// Only admin allowed
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = (int)$_POST["product_id"];
    $add_stock = (int)$_POST["add_stock"];

    if ($add_stock <= 0) {
        die("Invalid stock value");
    }

    // Add stock
    $sql = "UPDATE products 
            SET stock = stock + $add_stock 
            WHERE id = $product_id";

    if (!mysqli_query($conn, $sql)) {
        die("Error updating stock: " . mysqli_error($conn));
    }

    // Redirect back
    header("Location: admin_products.php");
    exit();
}
?>