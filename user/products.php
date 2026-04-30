<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$uid = (int) $_SESSION["user_id"];
$products = [];

/* Fetch cart count */
$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = $uid");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total_items'] ?? 0;

/* Fetch all distinct categories for the pills */
$cat_result = mysqli_query($conn, "SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row['category'];
}

/* Active filter — supports multiple categories */
$selected_categories = isset($_GET['category']) ? (array)$_GET['category'] : [];

/* Build category filter clause */
$cat_filter = '';
if (!empty($selected_categories)) {
    $safe_cats = array_map(fn($c) => "'" . mysqli_real_escape_string($conn, $c) . "'", $selected_categories);
    $cat_filter = "AND p.category IN (" . implode(',', $safe_cats) . ")";
}

/* Fetch products + favorite status */
$sql = "SELECT p.*, 
               CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS is_favorite
        FROM products p
        LEFT JOIN favorites f 
            ON p.id = f.product_id AND f.user_id = $uid
        WHERE 1=1 $cat_filter
        ORDER BY is_favorite DESC, p.created_at DESC";

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
<title>Products | LakbayLokal</title>
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

/* ── Filter Bar ── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 40px 20px;
    flex-wrap: wrap;
}

.filter-bar > label {
    font-weight: 700;
    color: #102a43;
    font-size: 14px;
    white-space: nowrap;
}

.category-checkboxes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* Hide the real checkbox */
.checkbox-pill input[type="checkbox"] {
    display: none;
}

/* The visible pill */
.checkbox-pill span {
    display: inline-block;
    padding: 7px 16px;
    border-radius: 999px;
    border: 2px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.checkbox-pill span:hover {
    border-color: #1e3a8a;
    color: #1e3a8a;
}

/* Checked state */
.checkbox-pill input[type="checkbox"]:checked + span {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #fff;
}

.filter-btn {
    padding: 8px 18px !important;
    background: #1e3a8a !important;
    color: #fff !important;
    border: none !important;
    border-radius: 10px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    width: auto !important;
    margin: 0 !important;
    transition: background 0.2s !important;
}

.filter-btn:hover {
    background: #102a43 !important;
}

.clear-btn {
    padding: 8px 14px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s;
    white-space: nowrap;
}

.clear-btn:hover {
    background: #e2e8f0;
}

/* ── Product Grid ── */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
    padding: 25px 40px 40px;
}

.product-card {
    position: relative;
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-left: 6px solid #f59e0b;
}

.product-card h3 {
    color: #102a43;
}

.buy-btn {
    margin-top: 15px;
    background: #1e3a8a;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    font-weight: 600;
}

.buy-btn:hover {
    background: #102a43;
}

/* Category badge */
.product-category {
    display: inline-block;
    background: #eff6ff;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 999px;
    margin-bottom: 8px;
}

/* Favorite heart */
.image-wrap {
    position: relative;
}

.heart-form {
    position: absolute;
    top: 8px;
    right: 8px;
    margin: 0;
}

.heart-btn {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    cursor: pointer;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.heart-btn.favorited     { color: #dc2626; }
.heart-btn.not-favorited { color: #9ca3af; }

/* Quantity Selector */
.qty-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
}

.qty-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 10px;
    background: #e2e8f0;
    color: #102a43;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover { background: #cbd5e1; }

.qty-input {
    width: 60px;
    height: 36px;
    text-align: center;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #102a43;
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
        <a href="cart.php">Cart (<?= (int)$cart_count ?>)</a>
        <a href="orders.php">Orders</a>
        <a href="../reviews/reviews.php">Reviews</a>
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

<!-- ── Category Filter Bar ── -->
<form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="filter-bar">
    <label>Filter by Category:</label>

    <div class="category-checkboxes">
        <?php foreach ($categories as $cat): ?>
            <label class="checkbox-pill">
                <input type="checkbox" name="category[]" value="<?= htmlspecialchars($cat) ?>"
                    <?= in_array($cat, $selected_categories) ? 'checked' : '' ?>>
                <span><?= htmlspecialchars($cat) ?></span>
            </label>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="filter-btn">Apply</button>

    <?php if (!empty($selected_categories)): ?>
        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="clear-btn">✕ Clear</a>
    <?php endif; ?>
</form>

<!-- ── Product Grid ── -->
<div class="product-grid">
<?php if (count($products) > 0): ?>
    <?php foreach ($products as $p): ?>
        <?php $img = (!empty($p["image"])) ? $p["image"] : "placeholder.png"; ?>
        <?php $stock = (int)$p["stock"]; ?>

        <div class="product-card">

            <div class="image-wrap">
                <img src="../assets/css/uploads/<?= htmlspecialchars($img) ?>"
                    style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:12px;">

                <!-- Favorite Heart Button -->
                <form method="post" action="add_to_favorite.php" class="heart-form">
                    <input type="hidden" name="product_id" value="<?= (int)$p["id"] ?>">
                    <button
                        type="submit"
                        class="heart-btn <?= $p["is_favorite"] ? 'favorited' : 'not-favorited' ?>"
                        title="<?= $p["is_favorite"] ? 'Remove from favorites' : 'Add to favorites' ?>">
                        <?= $p["is_favorite"] ? '♥' : '♡' ?>
                    </button>
                </form>
            </div>

            <!-- Category Badge -->
            <p class="product-category"><?= htmlspecialchars($p["category"]) ?></p>

            <h3><?= htmlspecialchars($p["name"]) ?></h3>
            <p><?= htmlspecialchars($p["description"]) ?></p>

            <strong>₱<?= number_format($p["price"], 2) ?></strong>

            <!-- STOCK DISPLAY -->
            <p style="margin-top:6px; font-size:13px;
                color: <?= ($stock <= 5) ? 'red' : '#475569' ?>;">
                Stock: <?= $stock ?>
            </p>

            <form method="post" action="add_to_cart.php"
                <?= ($stock <= 0) ? 'onsubmit="return false;"' : '' ?>>

                <input type="hidden" name="product_id" value="<?= (int)$p["id"] ?>">

                <!-- Quantity Selector -->
                <div class="qty-wrap">
                    <button type="button" class="qty-btn" onclick="changeQty(<?= (int)$p['id'] ?>, -1)">−</button>

                    <input
                        type="number"
                        name="quantity"
                        id="qty-<?= (int)$p['id'] ?>"
                        class="qty-input"
                        value="1"
                        min="1"
                        max="<?= $stock ?>"
                        readonly>

                    <button type="button" class="qty-btn" onclick="changeQty(<?= (int)$p['id'] ?>, 1)">+</button>
                </div>

                <button 
                    class="buy-btn"
                    type="submit"
                    <?= ($stock <= 0) ? 'disabled style="background:#9ca3af; cursor:not-allowed;"' : '' ?>>

                    <?= ($stock <= 0) ? 'Out of Stock' : 'Add to Cart' ?>
                </button>

            </form>

        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="padding:20px; color:#64748b;">No products found for the selected category.</p>
<?php endif; ?>
</div>

<script>
function changeQty(productId, change) {
    const input = document.getElementById('qty-' + productId);
    let current = parseInt(input.value) || 1;
    const maxStock = parseInt(input.max) || 1;

    current += change;

    if (current < 1) current = 1;
    if (current > maxStock) current = maxStock;

    input.value = current;
}
</script>

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