<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {
    $user_id = (int) $_SESSION["user_id"];
    $product_id = (int) $_POST["product_id"];

    // Check if already favorited
    $check = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id = $user_id AND product_id = $product_id LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        // Already favorited -> remove it
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        // Not favorited yet -> add it
        mysqli_query($conn, "INSERT INTO favorites (user_id, product_id) VALUES ($user_id, $product_id)");
    }

    // Redirect back to previous page if available
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header("Location: " . $redirect);
    exit();
}
?>