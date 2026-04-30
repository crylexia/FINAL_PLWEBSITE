<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cart.php");
    exit();
}

include "../config/db.php";

$user_id = (int) $_SESSION["user_id"];

/* FETCH CART ITEMS */
$sql = "SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";

$result = mysqli_query($conn, $sql);

$cart_items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
}

/* CHECK STOCK FIRST */
foreach ($cart_items as $item) {
    $pid = (int)$item["product_id"];
    $qty = (int)$item["quantity"];

    $stock_res = mysqli_query($conn, "SELECT stock FROM products WHERE id = $pid");
    $stock_row = mysqli_fetch_assoc($stock_res);
    $stock = (int)$stock_row["stock"];

    if ($qty > $stock) {
        die("Not enough stock for product ID: $pid");
    }
}

/* COMPUTE TOTAL */
foreach ($cart_items as $item) {
    $total += $item["quantity"] * $item["price"];
}

/* CREATE ORDER */
mysqli_query($conn, "
    INSERT INTO orders (user_id, total, status)
    VALUES ($user_id, $total, 'Pending')
");

$order_id = mysqli_insert_id($conn);

/* INSERT ITEMS + DEDUCT STOCK */
foreach ($cart_items as $item) {

    $product_id = (int)$item["product_id"];
    $qty = (int)$item["quantity"];
    $price = (float)$item["price"];

    mysqli_query($conn, "
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES ($order_id, $product_id, $qty, $price)
    ");

    /* REDUCE STOCK */
    mysqli_query($conn, "
        UPDATE products 
        SET stock = stock - $qty 
        WHERE id = $product_id
    ");
}

/* CLEAR CART */
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

header("Location: orders.php");
exit();
?>