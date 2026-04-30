<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized");
}

include "../config/db.php";

$order_id = (int)$_POST["order_id"];

/* CHECK ORDER */
$res = mysqli_query($conn, "SELECT status FROM orders WHERE id = $order_id");
$order = mysqli_fetch_assoc($res);

if (!$order || $order["status"] !== "Pending") {
    die("Order already processed or invalid.");
}

/* UPDATE ORDER STATUS */
mysqli_query($conn, "
    UPDATE orders 
    SET status = 'Approved' 
    WHERE id = $order_id
");

/* AUDIT LOG */
$admin = (int)$_SESSION["user_id"];

mysqli_query($conn, "
    INSERT INTO order_audit (order_id, admin_id, action)
    VALUES ($order_id, $admin, 'APPROVED')
");

header("Location: ../user/orders.php");
exit();
?>