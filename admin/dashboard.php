<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION["role"] ?? "buyer";
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard | LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#f3f4f6;
}

.dashboard {
    padding: 60px;
}

/* Welcome Section */
.welcome {
    background: linear-gradient(to right, #102a43, #1e3a8a);
    color: white;
    padding: 50px;
    border-radius: 16px;
}

.welcome h1 {
    color: #fbbf24;
    margin:0;
}

/* Cards */
.cards {
    display: flex;
    gap: 30px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.card {
    background: white;
    flex: 1;
    min-width: 250px;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-left: 8px solid #f59e0b;
    text-decoration:none;
    color:inherit;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3 {
    color: #102a43;
    margin-top:0;
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
        <a href="../auth/logout.php">Logout</a>
    </nav>
</header>

<div class="dashboard">

    <div class="welcome">
        <h1>Welcome to LakbayLokal</h1>
        <p>You are successfully logged in to the LGU Local Products Marketplace.</p>
    </div>

    <div class="cards">

    <?php if($role === "admin"): ?>

        <a href="admin_products.php" class="card" style="border-left:8px solid #dc2626;">
            <h3>LGU Product Control</h3>
            <p>Add, edit, and remove local products.</p>
        </a>

        <a href="../user/orders.php" class="card" style="border-left:8px solid #2563eb;">
            <h3>Order Management</h3>
            <p>Approve and manage customer orders.</p>
        </a>

        <a href="../reviews/review_records.php" class="card" style="border-left:8px solid #16a34a;">
            <h3>Review Records</h3>
            <p>View all submitted product reviews.</p>
        </a>

    <?php else: ?>

        <a href="../user/products.php" class="card">
            <h3>Browse Products</h3>
            <p>Explore authentic LGU-approved products.</p>
        </a>
        
        <a href="../user/cart.php" class="card" style="border-left:8px solid #f59e0b;">
            <h3>My Cart</h3>
            <p>View items you added and proceed to checkout.</p>
        </a>

        <a href="../user/orders.php" class="card">
            <h3>My Orders</h3>
            <p>Track your purchases and payments.</p>
        </a>

        <a href="../reviews/reviews.php" class="card">
            <h3>My Reviews</h3>
            <p>View and manage your submitted reviews.</p>
        </a>

    <?php endif; ?>

    </div>

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