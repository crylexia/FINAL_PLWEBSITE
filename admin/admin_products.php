<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../auth/login.php");
    exit();
}

$msg = "";

/* =========================
   ADD PRODUCT
========================= */
if(isset($_POST["add_product"])){

    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $desc = mysqli_real_escape_string($conn, $_POST["description"]);
    $price = $_POST["price"];
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $stock = (int)$_POST["stock"]; // ✅ NEW

    $imageName = $_FILES["image"]["name"];
    $tmp = $_FILES["image"]["tmp_name"];
    $folder = "../assets/css/uploads/" . $imageName;

    if(move_uploaded_file($tmp, $folder)){
        $sql = "INSERT INTO products (name, description, price, image, category, stock)
                VALUES ('$name','$desc','$price','$imageName','$category','$stock')";
        mysqli_query($conn,$sql);
        $msg = "Product added successfully!";
    } else {
        $msg = "Image upload failed!";
    }
}
/* =========================
   DELETE PRODUCT
========================= */
if(isset($_GET["delete"])){
    $id = $_GET["delete"];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header("Location: admin_products.php");
    exit();
}

/* =========================
   RESTOCK PRODUCT
========================= */
if(isset($_POST["restock"])){

    $product_id = (int)$_POST["product_id"];
    $add_stock = (int)$_POST["add_stock"];

    if($add_stock > 0){
        mysqli_query($conn, "
            UPDATE products 
            SET stock = stock + $add_stock 
            WHERE id = $product_id
        ");
    }

    header("Location: admin_products.php");
    exit();
}

/* =========================
   FETCH DISTINCT CATEGORIES
========================= */
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
    $safe_cats = array_map(function($c) use ($conn) {
    return "'" . mysqli_real_escape_string($conn, $c) . "'";
        }, $selected_categories);
    $cat_filter = "WHERE category IN (" . implode(',', $safe_cats) . ")";
}

/* =========================
   GET PRODUCTS
========================= */
$products = [];
$result = mysqli_query($conn, "SELECT * FROM products $cat_filter ORDER BY created_at DESC");
while($row = mysqli_fetch_assoc($result)){
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Products</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
body{ background:#f1f5f9; }

.admin-header{
    background:#102a43;
    padding: 10px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.admin-header h1{ color:#fbbf24; }

.container{
    display:flex;
    gap:40px;
    padding:40px;
}

.form-box{
    background:white;
    padding:25px;
    border-radius:16px;
    width:350px;
    flex-shrink: 0;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

input, textarea, select{
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:8px;
    border:1px solid #ccc;
    box-sizing: border-box;
}

button{
    width:100%;
    margin-top:15px;
    padding:12px;
    background:#1e3a8a;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

/* ── Filter Bar ── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
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

.checkbox-pill input[type="checkbox"] {
    display: none;
}

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
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s;
    white-space: nowrap;
}

.clear-btn:hover { background: #e2e8f0; }

/* ── Product Grid ── */
.product-list {
    flex: 1;
    min-width: 0;
}

.product-grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(220px,1fr));
    gap:25px;
}

.product-card{
    background:white;
    padding:15px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    border-left:6px solid #f59e0b;
}

.product-card img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:12px;
    margin-bottom:12px;
}

.product-card h3{
    color:#102a43;
    margin: 10px 0 8px;
}

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

.success{ color:green; text-align:center; margin-top: 10px; }

.no-results {
    color: #64748b;
    font-size: 15px;
    padding: 20px 0;
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
    <nav><a href="dashboard.php">Dashboard</a></nav>
</header>

<div class="container">

    <!-- LEFT: FORM -->
    <div class="form-box">
        <h3 style="margin-bottom: 20px; color: #1e3a8a;">Add New Product</h3>

        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Product Name</label>
                <input type="text" name="name" placeholder="e.g. Fresh Strawberry Jam" required>
            </div>

            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Description</label>
                <textarea name="description" placeholder="Describe the item..." required></textarea>
            </div>

            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Price (₱)</label>
                <input type="number" name="price" step="0.01" placeholder="0.00" required>
            </div>

            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Stock</label>
                <input type="number" name="stock" min="0" placeholder="Enter stock" required>
            </div>

            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Category</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="Attire">Attire</option>
                    <option value="Food Delicacy">Food Delicacy</option>
                    <option value="Handicrafts">Handicrafts</option>
                    <option value="Souvenirs">Souvenirs</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Home Decor">Home Decor</option>
                    <option value="Local Art">Local Art</option>
                    <option value="General">General</option>
                </select>
            </div>

            <div style="margin-bottom: 5px;">
                <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Product Image</label>
                <input type="file" name="image" style="border:none; padding-left:0;" required>
            </div>

            <button type="submit" name="add_product">Add Product</button>
        </form>

        <p class="success"><?= $msg ?></p>
    </div>

    <!-- RIGHT: FILTER + PRODUCT GRID -->
    <div class="product-list">

        <!-- ── Category Filter Bar ── -->
        <form method="GET" action="<?= \htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="filter-bar">
            <label>Filter by Category:</label>

            <div class="category-checkboxes">
                <?php foreach ($categories as $cat): ?>
                    <label class="checkbox-pill">
                        <input type="checkbox" name="category[]" value="<?= \htmlspecialchars($cat) ?>"
                            <?= in_array($cat, $selected_categories) ? 'checked' : '' ?>>
                        <span><?= \htmlspecialchars($cat) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="filter-btn">Apply</button>

            <?php if (!empty($selected_categories)): ?>
                <a href="<?= \htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="clear-btn">✕ Clear</a>
            <?php endif; ?>
        </form>

        <!-- ── Product Grid ── -->
        <?php if (count($products) > 0): ?>
            <div class="product-grid">
            <?php foreach($products as $p): ?>
                <div class="product-card">
                    <?php $img = (!empty($p["image"])) ? $p["image"] : "placeholder.png"; ?>

                    <img src="../assets/css/uploads/<?= \htmlspecialchars($img) ?>"
                        alt="<?= \htmlspecialchars($p["name"]) ?>">

                    <p class="product-category"><?= \htmlspecialchars($p["category"]) ?></p>

                    <h3><?= \htmlspecialchars($p["name"]) ?></h3>
                    <p><?= \htmlspecialchars($p["description"]) ?></p>
                    <strong>₱<?= number_format($p["price"],2) ?></strong>

                    <p style="margin-top:6px; font-size:13px; 
                        color: <?= $p["stock"] <= 5 ? 'red' : '#475569' ?>;">
                        Stock: <?= (int)$p["stock"] ?>
                    </p>

                    <!-- RESTOCK FORM -->
                    <form method="post" style="margin-top:10px; display:flex; gap:6px;">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">

                        <input 
                            type="number" 
                            name="add_stock" 
                            min="1" 
                            placeholder="+Qty" 
                            required
                            style="width:70px; padding:6px; border-radius:6px; border:1px solid #ccc;"
                        >

                        <button type="submit" name="restock" style="
                            background:#f59e0b;
                            color:white;
                            border:none;
                            padding:6px 10px;
                            border-radius:6px;
                            cursor:pointer;
                            font-size:12px;
                            font-weight:600;">
                            Restock
                        </button>
                    </form>

                    <div style="margin-top:15px;">
                        <a href="edit_product.php?id=<?= $p["id"] ?>"
                            style="display:inline-block; background:#fbbf24; color:#1e3a8a;
                                   padding:8px 14px; border-radius:6px; text-decoration:none;
                                   font-size:13px; font-weight:600; margin-right:8px;">
                            Edit
                        </a>
                        <a href="admin_products.php?delete=<?= $p["id"] ?>"
                            onclick="return confirm('Delete this product?');"
                            style="display:inline-block; background:#dc2626; color:white;
                                   padding:8px 14px; border-radius:6px; text-decoration:none;
                                   font-size:13px; font-weight:600;">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No products found for the selected category.</p>
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