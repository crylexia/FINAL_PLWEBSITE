<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$is_admin = ($_SESSION["role"] === "admin");

if($is_admin){
    $sql = "SELECT o.*, u.username 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC";
} else {
    $uid = (int)$_SESSION["user_id"];
    $sql = "SELECT o.*, u.username 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.user_id = $uid
            ORDER BY o.created_at DESC";
}

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Orders | LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
    .orders-container { padding: 40px; max-width: 1300px; margin: auto; }
    .orders-title { font-size: 28px; color: #102a43; margin-bottom: 20px; }
    
    .orders-table {
        width: 100%;
        background: white;
        border-radius: 12px;
        border-collapse: collapse;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .orders-table th, .orders-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid #f0f4f8;
        vertical-align: top;
    }
    
    .orders-table th { 
        background: #1e3a8a; 
        color: white; 
        font-weight: 600; 
    }

    .status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        display: inline-block;
    }

    .pending { background: #fef3c7; color: #92400e; }
    .approved { background: #dcfce7; color: #166534; }
    .paid { background: #dcfce7; color: #166534; }

    .approve-btn {
        background: #16a34a;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 13px;
    }

    .approve-btn:hover { background: #15803d; }
    
    .verified-text { 
        color: #16a34a; 
        font-weight: 600; 
        font-size: 14px; 
    }

    .order-items {
        min-width: 260px;
    }

    .item-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }

    .item-name {
        font-weight: 700;
        color: #102a43;
        margin-bottom: 4px;
    }

    .item-meta {
        font-size: 13px;
        color: #475569;
    }

    .empty-items {
        color: #94a3b8;
        font-style: italic;
        font-size: 13px;
    }

    .order-total {
        font-weight: 700;
        color: #1e3a8a;
    }

            /* FOOTER */
        .site-footer {
            background: #183153;
            color: #f8fafc;
            margin-top: 0;
            border-top: 4px solid #f4b400;
            text-align: center;
        }

        .footer-content {
            max-width: 850px;
            margin: 0 auto;
            padding: 40px 20px 28px;
        }

        .footer-content h3 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            color: #f4b400;
        }

        .footer-tagline {
            margin: 14px auto 30px;
            font-size: 17px;
            line-height: 1.7;
            color: #dbe4ef;
            max-width: 680px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding: 16px 20px;
        }

        .footer-bottom p {
            margin: 0;
            font-size: 14px;
            color: #cbd5e1;
        }

@media (max-width: 768px){
.footer-content h3 {
                font-size: 24px;
            }

            .footer-tagline {
                font-size: 15px;
                margin-bottom: 24px;
            }

            .footer-contact h4 {
                font-size: 20px;
            }

            .footer-contact p {
                font-size: 15px;
            }

            .footer-bottom p {
                font-size: 13px;
            }
}

</style>
</head>
<body>

<header>
    <div class="logo">LakbayLokal Marketplace</div>
    <nav>
        <a href="../admin/dashboard.php">Dashboard</a>
        
        <?php if(!$is_admin): ?>
            <a href="products.php">Products</a>
                <?php
                // Fetch total quantity from the database
                $uid = (int)$_SESSION["user_id"];
                $count_res = mysqli_query($conn, "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = $uid");
                $count_row = mysqli_fetch_assoc($count_res);
                $cart_count = $count_row['total_items'] ?? 0;
                ?>
            <a href="cart.php">Cart (<?= $cart_count ?>)</a>
            <a href="../reviews/reviews.php">Reviews</a>
        <?php endif; ?>
    </nav>
</header>

<div class="orders-container">
    <h2 class="orders-title"> Orders Management</h2>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <?php if($is_admin): ?>
                    <th style="text-align: center;">Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>#<?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["username"]) ?></td>

                    <!-- ORDER BREAKDOWN -->
                    <td class="order-items">
                        <?php
                        $order_id = (int)$row["id"];

                        $items_sql = "SELECT oi.*, p.name 
                                    FROM order_items oi
                                    JOIN products p ON oi.product_id = p.id
                                    WHERE oi.order_id = $order_id";

                        $items_result = mysqli_query($conn, $items_sql);

                        if ($items_result && mysqli_num_rows($items_result) > 0):
                            while($item = mysqli_fetch_assoc($items_result)):
                                $subtotal = $item["price"] * $item["quantity"];
                        ?>
                            <div class="item-row">
                                <div class="item-name"><?= htmlspecialchars($item["name"]) ?></div>
                                <div class="item-meta">
                                    Qty: <?= $item["quantity"] ?> × ₱<?= number_format($item["price"], 2) ?>
                                </div>
                                <div class="item-meta">
                                    Subtotal: ₱<?= number_format($subtotal, 2) ?>
                                </div>
                            </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <span class="empty-items">No item breakdown found</span>
                        <?php endif; ?>
                    </td>

                    <td class="order-total">₱<?= number_format($row["total"], 2) ?></td>

                    <td>
                        <span class="status <?= strtolower($row["status"]) ?>">
                            <?= $row["status"] ?>
                        </span>
                    </td>

                    <td><?= date("M d, Y", strtotime($row["created_at"])) ?></td>

                    <?php if($is_admin): ?>
                    <td style="text-align: center;">
                        <?php if($row["status"] == "Pending"): ?>
                            <form method="post" action="../admin/approve_order.php" style="margin:0;">
                                <input type="hidden" name="order_id" value="<?= $row["id"] ?>">
                                <button type="submit" class="approve-btn">Mark as Paid</button>
                            </form>
                        <?php else: ?>
                            <span class="verified-text">✔ Paid</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $is_admin ? 7 : 6 ?>" style="text-align:center; padding: 80px; color:#64748b; font-weight:600;">
                        No orders found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>

<footer class="site-footer">
    <div class="footer-content">
        <h3>LakbayLokal Marketplace</h3>
        <p class="footer-tagline">Your online destination for authentic souvenir products from Lingayen, Pangasinan.</p>
    </div>

    <div class="footer-bottom">
        <p>© 2026 LakbayLokal Marketplace — Promoting Lingayen Souvenir Shops and Local Products</p>
    </div>
</footer>

</html>