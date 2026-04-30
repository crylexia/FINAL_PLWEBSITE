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
    $quantity = isset($_POST["quantity"]) ? (int) $_POST["quantity"] : 1;

    if ($quantity < 1) $quantity = 1;

    // GET STOCK
    $stock_res = mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id");
    $stock_row = mysqli_fetch_assoc($stock_res);
    $stock = (int)($stock_row["stock"] ?? 0);

    if ($stock <= 0) {
        header("Location: products.php");
        exit();
    }

    // CHECK CART
    $check = mysqli_query($conn, "SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    if ($row = mysqli_fetch_assoc($check)) {

        $new_qty = $row["quantity"] + $quantity;

        if ($new_qty > $stock) {
            $new_qty = $stock;
        }

        mysqli_query($conn, "
            UPDATE cart 
            SET quantity = $new_qty 
            WHERE user_id = $user_id AND product_id = $product_id
        ");

    } else {

        if ($quantity > $stock) {
            $quantity = $stock;
        }

        if ($stock > 0) {
            mysqli_query($conn, "
                INSERT INTO cart (user_id, product_id, quantity)
                VALUES ($user_id, $product_id, $quantity)
            ");
        }
    }

    header("Location: products.php");
    exit();
}
?>