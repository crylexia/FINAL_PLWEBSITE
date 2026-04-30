<?php
session_start();

/* =========================
   DATABASE CONNECTION
========================= */
$conn = new mysqli("localhost", "root", "", "lgu_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

/* =========================
   HANDLE FORM SUBMISSIONS
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // SUBMIT MULTIPLE REVIEWS AT ONCE
    if (isset($_POST['submit_all_reviews']) && isset($_POST['reviews'])) {

        $reviews = $_POST['reviews'];
        $saved_count = 0;

        $stmt = $conn->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                rating = VALUES(rating),
                comment = VALUES(comment)
        ");

        foreach ($reviews as $product_id => $review_data) {
            $p_id = intval($product_id);
            $rat  = isset($review_data['rating']) ? intval($review_data['rating']) : 3;
            $com  = isset($review_data['comment']) ? trim($review_data['comment']) : '';

            if ($rat >= 1 && $rat <= 5) {
                $stmt->bind_param("iiis", $user_id, $p_id, $rat, $com);
                if ($stmt->execute()) {
                    $saved_count++;
                }
            }
        }

        $stmt->close();

        if ($saved_count > 0) {
            $success_msg = "$saved_count review(s) saved successfully!";
        } else {
            $success_msg = "No reviews were submitted.";
        }
    }

    // DELETE REVIEW
    if (isset($_POST['delete_review'])) {
        $r_id = intval($_POST['review_id']);

        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $r_id, $user_id);

        if ($stmt->execute()) {
            $success_msg = "Review deleted successfully.";
        }

        $stmt->close();
    }

    // EDIT REVIEW
    if (isset($_POST['edit_review'])) {
        $r_id = intval($_POST['review_id']);
        $rat  = intval($_POST['rating']);
        $com  = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        $stmt = $conn->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("isii", $rat, $com, $r_id, $user_id);

        if ($stmt->execute()) {
            $success_msg = "Review updated successfully!";
        }

        $stmt->close();
    }
}

/* =========================
   FETCH CART COUNT
========================= */
$uid = $_SESSION["user_id"];
$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = $uid");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total_items'] ?? 0;

/* =========================
   FETCH PENDING REVIEWS
========================= */
$pending_query = "SELECT DISTINCT p.id, p.name, p.image
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  JOIN products p ON oi.product_id = p.id
                  LEFT JOIN reviews r 
                      ON r.product_id = p.id 
                      AND r.user_id = o.user_id
                  WHERE o.user_id = ?
                  AND o.status = 'Approved'
                  AND r.id IS NULL";

$stmt = $conn->prepare($pending_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_result = $stmt->get_result();
$stmt->close();

/* =========================
   FETCH REVIEW HISTORY
========================= */
$history_query = "SELECT r.*, p.name, p.image
                  FROM reviews r
                  JOIN products p ON r.product_id = p.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at DESC";

$h_stmt = $conn->prepare($history_query);
$h_stmt->bind_param("i", $user_id);
$h_stmt->execute();
$history_result = $h_stmt->get_result();
$h_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ratings & Reviews - LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* =========================
   PAGE
========================= */

.review-container {
    max-width: 1180px;
    margin: 60px auto;
    padding: 0 28px;
}

.page-title {
    font-size: 32px;
    font-weight: 800;
    color: #1e3a8a;
    margin-bottom: 18px;
}

.page-subtitle {
    font-size: 15px;
    color: #64748b;
    margin-bottom: 40px;
    line-height: 1.6;
}

/* =========================
   SECTION TITLE
========================= */
.section-title {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title::before {
    content: "";
    display: inline-block;
    width: 6px;
    height: 32px;
    background: #f4b400;
    border-radius: 999px;
}

/* =========================
   SUCCESS MESSAGE
========================= */
.success-msg {
    background: #dcfce7;
    color: #166534;
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 26px;
    font-weight: 700;
    border-left: 5px solid #22c55e;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.08);
}

/* =========================
   EMPTY STATE
========================= */
.empty-state {
    background: #ffffff;
    border-radius: 18px;
    padding: 22px 24px;
    margin-bottom: 36px;
    color: #64748b;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    border: 1px solid #e2e8f0;
}

/* =========================
   REVIEW CARD
========================= */
.review-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 28px;
    margin-bottom: 28px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    display: flex;
    gap: 24px;
    align-items: flex-start;
    border: 1px solid #edf2f7;
}

.review-image {
    flex: 0 0 130px;
}

.review-image img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 16px;
    display: block;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.review-content {
    flex: 1;
    min-width: 0;
}

.product-name {
    display: block;
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1.2;
}

.review-meta {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 10px;
}

/* =========================
   STAR DISPLAY
========================= */
.star-rating {
    font-size: 24px;
    color: #f4b400;
    margin: 4px 0 12px;
    letter-spacing: 1px;
}

/* =========================
   REVIEW TEXT
========================= */
.review-text {
    color: #475569;
    margin: 0 0 18px;
    line-height: 1.7;
    font-size: 15px;
    background: #f8fafc;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

.review-text em {
    color: #64748b;
}

/* =========================
   FORMS
========================= */
.pending-form {
    margin-bottom: 42px;
}

.review-content form {
    width: 100%;
    margin: 0;
}

.review-history-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 8px;
}

/* =========================
   INPUTS
========================= */
.review-content select,
.review-content textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 16px;
    box-sizing: border-box;
    background: #fff;
    outline: none;
    transition: 0.2s ease;
}

.review-content select:focus,
.review-content textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.10);
}

.review-content select {
    height: 54px;
}

.review-content textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
}

.review-content textarea::placeholder {
    color: #94a3b8;
    font-style: italic;
}

/* =========================
   BUTTONS
========================= */
.btn-submit-all,
.btn-edit,
.btn-delete {
    border: none;
    border-radius: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn-submit-all {
    width: 100%;
    background: linear-gradient(90deg, #1d4ed8, #2f5bdb);
    color: #fff;
    padding: 16px;
    font-size: 17px;
    margin-top: 10px;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
}

.btn-submit-all:hover,
.btn-edit:hover,
.btn-delete:hover {
    transform: translateY(-1px);
    opacity: 0.97;
}

.review-actions {
    display: flex;
    gap: 12px;
    margin-top: 2px;
}

.review-actions .btn-edit,
.review-actions .btn-delete {
    flex: 1;
    margin: 0;
    padding: 14px 18px;
    font-size: 15px;
}

.btn-edit {
    background: #2563eb;
    color: white;
}

.btn-delete {
    background: #dc2626;
    color: white;
}

/* =========================
   SPACING HELPERS
========================= */
.section-spacing {
    margin-top: 10px;
    margin-bottom: 8px;
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

/* =========================
   MOBILE
========================= */
@media (max-width: 768px) {
    .review-container {
        margin: 36px auto;
        padding: 0 16px;
    }

    .page-title {
        font-size: 26px;
    }

    .page-subtitle {
        font-size: 14px;
        margin-bottom: 28px;
    }

    .section-title {
        font-size: 20px;
        margin-bottom: 18px;
    }

    .review-card {
        flex-direction: column;
        padding: 18px;
        gap: 16px;
    }

    .review-image {
        flex: none;
        width: 100%;
    }

    .review-image img {
        width: 100%;
        height: 220px;
        border-radius: 14px;
    }

    .product-name {
        font-size: 22px;
    }

    .star-rating {
        font-size: 22px;
    }

    .review-actions {
        flex-direction: column;
    }

    .review-actions .btn-edit,
    .review-actions .btn-delete {
        width: 100%;
    }

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
        <a href="../user/products.php">Products</a>
        <a href="../user/cart.php">Cart (<?= $cart_count ?>)</a>
        <a href="../user/orders.php">Orders</a>
    </nav>
</header>

<div class="review-container">

    <h1 class="page-title">Rate Your Purchases</h1>
    <p class="page-subtitle">
        Share your experience with products you've purchased. Your feedback helps support local sellers
        and guides future buyers in choosing the best Lingayen souvenirs.
    </p>

    <?php if ($success_msg): ?>
        <div class="success-msg"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <!-- =========================
         PENDING REVIEWS
    ========================= -->
    <h3 class="section-title">Pending Reviews</h3>

    <?php if ($pending_result->num_rows > 0): ?>
        <form method="POST" class="pending-form">
            <?php while($row = $pending_result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-image">
                        <img src="../assets/css/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </div>

                    <div class="review-content">
                        <span class="product-name"><?php echo htmlspecialchars($row['name']); ?></span>
                        <div class="review-meta">Awaiting your feedback</div>

                        <select name="reviews[<?php echo $row['id']; ?>][rating]">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3" selected>3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>

                        <div class="section-spacing"></div>

                        <textarea 
                            name="reviews[<?php echo $row['id']; ?>][comment]" 
                            placeholder="Write an optional review comment..."></textarea>
                    </div>
                </div>
            <?php endwhile; ?>

            <button type="submit" name="submit_all_reviews" class="btn-submit-all">
                Submit All Reviews
            </button>
        </form>
    <?php else: ?>
        <div class="empty-state">No pending items to rate.</div>
    <?php endif; ?>

    <!-- =========================
         REVIEW HISTORY
    ========================= -->
    <h3 class="section-title">Your Review History</h3>

    <?php if ($history_result->num_rows > 0): ?>
        <?php while($h_row = $history_result->fetch_assoc()): ?>
            <div class="review-card">
                <div class="review-image">
                    <img src="../assets/css/uploads/<?php echo htmlspecialchars($h_row['image']); ?>" alt="<?php echo htmlspecialchars($h_row['name']); ?>">
                </div>

                <div class="review-content">
                    <span class="product-name"><?php echo htmlspecialchars($h_row['name']); ?></span>

                    <div class="review-meta">
                        Reviewed on <?php echo date("F j, Y", strtotime($h_row['created_at'])); ?>
                    </div>

                    <div class="star-rating">
                        <?php
                            echo str_repeat("★", (int)$h_row['rating']) .
                                 str_repeat("☆", 5 - (int)$h_row['rating']);
                        ?>
                    </div>

                    <div class="review-text">
                        <?php if (!empty($h_row['comment'])): ?>
                            "<?php echo htmlspecialchars($h_row['comment']); ?>"
                        <?php else: ?>
                            <em>No written comment provided.</em>
                        <?php endif; ?>
                    </div>

                    <form method="POST" class="review-history-form">
                        <input type="hidden" name="review_id" value="<?php echo $h_row['id']; ?>">

                        <select name="rating" required>
                            <?php for($i = 5; $i >= 1; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($h_row['rating'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <textarea name="comment" placeholder="Optional comment"><?php echo htmlspecialchars($h_row['comment']); ?></textarea>

                        <div class="review-actions">
                            <button type="submit" name="edit_review" class="btn-edit">Update</button>
                            <button type="submit" name="delete_review" class="btn-delete" onclick="return confirm('Delete this review?');">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">You haven't shared any reviews yet.</div>
    <?php endif; ?>

</div>

<footer class="site-footer">
    <div class="footer-content">
        <h3>LakbayLokal Marketplace</h3>
        <p class="footer-tagline">
            Your online destination for authentic souvenir products from Lingayen, Pangasinan.
        </p>
    </div>

    <div class="footer-bottom">
        <p>© 2026 LakbayLokal Marketplace — Promoting Lingayen Souvenir Shops and Local Products</p>
    </div>
</footer>

</body>
</html>