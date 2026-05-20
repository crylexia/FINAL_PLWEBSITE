<?php
include "../config/db.php";

$products = [];

$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Product Catalog | LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* ── Products Section Header ── */
.products-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    padding: 40px 40px 12px;
    flex-wrap: wrap;
}

.products-header-text h2 {
    margin: 0;
    font-size: 34px;
    font-weight: 800;
    color: #102a43;
    line-height: 1.2;
}

.products-header-text p {
    margin: 8px 0 0;
    font-size: 15px;
    color: #64748b;
    max-width: 620px;
    line-height: 1.6;
}

.products-header-meta {
    display: flex;
    align-items: center;
}

.products-count {
    display: inline-block;
    padding: 8px 14px;
    background: #eff6ff;
    color: #1e3a8a;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
}
.product-grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(220px,1fr));
    gap:25px;
    padding:60px;
}

.product-card{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    border-left:6px solid #f59e0b;
}

.product-card h3{
    color:#102a43;
}

/* Category badge — matches product.php style */
.product-category{
    display: inline-block;
    background: #eff6ff;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 999px;
    margin-bottom: 8px;
}

.login-btn{
    margin-top:15px;
    display:block;
    background:#1e3a8a;
    color:white;
    padding:10px;
    text-align:center;
    border-radius:8px;
    text-decoration:none;
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

        .footer-contact {
            margin-top: 10px;
        }

        .footer-contact h4 {
            margin: 0 0 18px;
            font-size: 24px;
            font-weight: 700;
            color: #fbbf24;
        }

        .footer-contact p {
            margin: 10px 0;
            font-size: 17px;
            line-height: 1.7;
            color: #e5e7eb;
        }

        .footer-contact strong {
            color: #f8fafc;
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
 
        @media (max-width: 768px) {
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
    <a href="../index.php">Home</a>
    <a href="catalog.php">Products</a>
    <a href="tourism.php">Tourism Areas</a>
    <a href="../auth/login.php" class="btn">Login</a>
</nav>
</header>

<section class="products-header">
    <div class="products-header-text">
        <h2>Local Products</h2>
        <p>Discover authentic Filipino delicacies, handmade goods, and proudly local favorites.</p>
    </div>

    <div class="products-header-meta">
        <span class="products-count"><?= count($products) ?> Product<?= count($products) !== 1 ? 's' : '' ?></span>
    </div>
</section>

<div class="product-grid">

<?php foreach($products as $p): ?>
    <div class="product-card">

        <!-- Product Image -->
        <?php 
            $img = !empty($p["image"]) ? $p["image"] : "placeholder.png";
        ?>

        <img src="../assets/css/uploads/<?= htmlspecialchars($img) ?>" 
             alt="<?= htmlspecialchars($p["name"]) ?>" 
             style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:12px;">

        <!-- Product Category -->
        <p class="product-category"><?= htmlspecialchars($p["category"]) ?></p>

        <!-- Product Name -->
        <h3><?= htmlspecialchars($p["name"]) ?></h3>

        <!-- Product Description -->
        <p><?= htmlspecialchars($p["description"]) ?></p>

        <!-- Price -->
        <strong>₱<?= number_format($p["price"],2) ?></strong>

        <!-- Login Button -->
        <a href="../auth/login.php" class="login-btn">Login to Buy</a>

    </div>
<?php endforeach; ?>

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