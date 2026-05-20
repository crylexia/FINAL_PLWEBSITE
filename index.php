<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LakbayLokal | LGU Local Products Marketplace</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* HERO */
        .hero {
            background: linear-gradient(to right, #0f172a, #1e3a8a);
            padding: 110px 60px;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 48px;
            letter-spacing: -1px;
            margin-bottom: 15px;
        }

        .hero p {
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.7;
            color: #e5e7eb;
            font-size: 17px;
        }

        .badge {
            display: inline-block;
            background: #fbbf24;
            color: #0f172a;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .hero-btn {
            display: inline-block;
            margin-top: 28px;
            background: linear-gradient(135deg, #fde68a, #fbbf24);
            color: #0f172a;
            padding: 16px 36px;
            font-size: 17px;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            border: 3px solid white;
            box-shadow: 0 18px 40px rgba(0,0,0,0.35);
            transition: 0.3s ease;
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            background: #ffd54a;
        }

        .trust-bar {
            background: #ffffff;
            padding: 25px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 10px;
        }

        .trust-item {
            font-weight: 600;
            color: #0f172a;
            cursor: default;
            user-select: none;
        }
        
        /* SECTION */
        .section {
            padding: 100px 60px;
        }

        .section h2 {
            font-size: 36px;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .split {
            display: flex;
            gap: 60px;
            align-items: center;
        }

        .split img {
            width: 30%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            object-fit: cover;
        }

        .split p {
            font-size: 17px;
            line-height: 1.8;
            color: #334155;
        }

        /* FEATURES */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 50px;
        }

        .feature-card {
            background: white;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 35px rgba(0,0,0,0.12);
        }

        .feature-card h3 {
            color: #0f172a;
            margin-top: 0;
            font-size: 22px;
        }

        .feature-card p {
            color: #475569;
            line-height: 1.7;
            font-size: 16px;
        }

        /* FEATURED CATEGORIES */
        .categories-section {
            background: #f8fafc;
            text-align: center;
        }

        .section-subtitle {
            max-width: 700px;
            margin: 12px auto 0;
            color: #475569;
            font-size: 17px;
            line-height: 1.7;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
            margin-top: 50px;
        }

        .category-card {
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            cursor: pointer;
            text-align: left;
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 22px 45px rgba(0,0,0,0.16);
        }

        .category-image-wrap {
            overflow: hidden;
            height: 240px;
        }

        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .category-card:hover img {
            transform: scale(1.08);
        }

        .category-content {
            padding: 24px;
        }

        .category-content h3 {
            margin: 0 0 12px;
            font-size: 22px;
            color: #0f172a;
        }

        .category-content p {
            margin: 0;
            color: #475569;
            line-height: 1.7;
            font-size: 15.5px;
        }

        /* CTA */
        .cta {
            background: linear-gradient(135deg, #0b3c5d, #1d4ed8);
            padding: 100px 60px;
            text-align: center;
            color: white;
        }

        .cta h2 {
            font-size: 36px;
            margin: 0 0 15px;
            color: #fbbf24;
        }

        .cta p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            color: #e5e7eb;
            text-align: center;
        }

        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #fde68a, #fbbf24);
            color: #0f172a;
            padding: 18px 42px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            margin-top: 30px;
            border: 3px solid white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            transition: 0.3s ease;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            background: #ffd54a;
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

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .trust-bar {
                justify-content: center;
                text-align: center;
            }

            .split {
                flex-direction: column;
            }

            .split img {
                width: 100%;
            }

            .feature-grid,
            .categories-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero,
            .section,
            .cta {
                padding-left: 25px;
                padding-right: 25px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .section h2,
            .cta h2 {
                font-size: 30px;
            }

            .cta p {
                font-size: 17px;
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

            .category-image-wrap {
                height: 220px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">LakbayLokal Marketplace</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="user/catalog.php">Products</a>
        <a href="user/tourism.php">Tourism Areas</a>
        <a href="auth/register.php" class="btn">Register</a>
        <a href="auth/login.php" class="btn">Login</a>
    </nav>
</header>

<section class="hero">
    <span class="badge">Official Lingayen Souvenir Marketplace</span>
    <h1>Discover Authentic Souvenirs from Lingayen, Pangasinan</h1>
    <p>Explore locally made delicacies, crafts, and cultural keepsakes from souvenir shops in Lingayen. Support local businesses while bringing home a piece of Pangasinan’s heritage.</p>
    <a href="user/catalog.php" class="hero-btn">Shop Lingayen Souvenirs</a>
</section>

<div class="trust-bar">
    <div class="trust-item">Lingayen-Based Sellers</div>
    <div class="trust-item">Authentic Local Souvenirs</div>
    <div class="trust-item">Community-Supported Shops</div>
    <div class="trust-item">Proudly Pangasinense Products</div>
</div>

<section class="section">
    <div class="split">
        <div class="feature-card">
            <h2>Bringing Lingayen’s Souvenir Shops Online</h2>
            <p>LakbayLokal connects customers to souvenir shops in Lingayen, Pangasinan, making it easier to discover products that reflect the town’s culture, local pride, and tourism identity. From delicacies to handcrafted pasalubong, every item represents the heart of Lingayen.</p>
        </div>
        <img src="assets/css/images/pictures/lingayen_seal.png" alt="Lingayen, Pangasinan">
    </div>
</section>

<section class="section">
    <h2>Why Shop from Lingayen Souvenir Stores?</h2>

    <div class="feature-grid">
        <div class="feature-card">
            <h3>Authentic Local Products</h3>
            <p>Find souvenirs, pasalubong items, delicacies, and handcrafted goods made and sold by trusted local shops in Lingayen.</p>
        </div>
        <div class="feature-card">
            <h3>Support Small Businesses</h3>
            <p>Every purchase helps local souvenir shop owners, artisans, and community-based entrepreneurs grow their livelihood.</p>
        </div>
        <div class="feature-card">
            <h3>Celebrate Lingayen’s Identity</h3>
            <p>Each product reflects the culture, traditions, and tourism spirit of Lingayen, making every souvenir more meaningful.</p>
        </div>
    </div>
</section>

<section class="section categories-section">
    <h2>Featured Souvenir Categories</h2>
    <p class="section-subtitle">Explore the most loved local products from Lingayen’s souvenir shops.</p>

    <div class="categories-grid">
        <div class="category-card">
            <div class="category-image-wrap">
                <img src="assets/css/images/pictures/souvenir_clothes.jpg" alt="Souvenir Clothing">
            </div>
            <div class="category-content">
                <h3>Souvenir Clothing</h3>
                <p>Discover locally inspired shirts, wearable souvenirs, and apparel that showcase Lingayen’s culture and pride.</p>
            </div>
        </div>

        <div class="category-card">
            <div class="category-image-wrap">
                <img src="assets/css/images/pictures/souvenir_handicrafts.jpg" alt="Handcrafted Souvenirs">
            </div>
            <div class="category-content">
                <h3>Handcrafted Souvenirs</h3>
                <p>Browse handmade crafts, decorative keepsakes, and artisan products created by local makers and small businesses.</p>
            </div>
        </div>

        <div class="category-card">
            <div class="category-image-wrap">
                <img src="assets/css/images/pictures/souvenir_foods.jpg" alt="Local Delicacies">
            </div>
            <div class="category-content">
                <h3>Local Delicacies</h3>
                <p>Bring home delicious pasalubong favorites and authentic Lingayen treats perfect for sharing with family and friends.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <h2>Take Home a Piece of Lingayen</h2>
    <p>Browse unique souvenirs and local products from Lingayen’s trusted shops and support the town’s growing local marketplace.</p>
    <a href="user/catalog.php" class="cta-btn">Browse Souvenir Products</a>
</section>

<footer class="site-footer">
    <div class="footer-content">
        <h3>LakbayLokal Marketplace</h3>
        <p class="footer-tagline">Your online destination for authentic souvenir products from Lingayen, Pangasinan.</p>

        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p><strong>Email:</strong> support@lakbaylokal.ph</p>
            <p><strong>Phone:</strong> +63 912 345 6789</p>
            <p><strong>Office:</strong> Lingayen Souvenir and Local Trade Support Desk</p>
            <p><strong>Hours:</strong> Mon - Fri | 8:00 AM - 5:00 PM</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2026 LakbayLokal Marketplace — Promoting Lingayen Souvenir Shops and Local Products</p>
    </div>
</footer>

</body>
</html>