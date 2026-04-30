<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourism Areas</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        :root {
            --primary: #183153;
            --primary-dark: #10253d;
            --accent: #f4b400;
            --accent-dark: #d89c00;
            --text: #1e293b;
            --muted: #64748b;
            --bg-soft: #f8fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
        }

        body {
            background: #f5f7fb;
        }

        /* =========================
           TOURISM SECTION REDESIGN
        ========================== */
        .overview {
            max-width: 1400px;
            margin: 50px auto 70px;
            padding: 0 18px; /* reduced so no thick white frame */
        }

        .tourism-section-card {
            background: #fcfdff;;
            border-radius: 28px;
            padding: 42px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            border: none; /* remove the visible edge */
        }

        .section-eyebrow {
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--accent-dark);
            background: rgba(244, 180, 0, 0.12);
            padding: 8px 14px;
            border-radius: 999px;
            margin-bottom: 16px;
        }

        .overview h2 {
            font-size: 40px;
            line-height: 1.15;
            color: var(--primary);
            margin: 0 0 10px;
            font-weight: 800;
        }

        .subtitle {
            font-size: 18px;
            color: var(--muted);
            margin: 0 0 34px;
            max-width: 760px;
            line-height: 1.7;
        }

        .tourism-layout {
            display: flex;
            align-items: flex-start;
            gap: 38px;
        }

        .tourism-text {
            flex: 1.1;
            min-width: 0;
        }

        .tourism-image {
            width: 46%;
            flex-shrink: 0;
        }

        .tourism-intro {
            font-size: 16px;
            line-height: 1.9;
            color: var(--text);
            margin-bottom: 28px;
        }

        .tourism-intro strong {
            color: var(--primary);
        }

        /* Attraction cards */
        .attractions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .attraction-card {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            border-radius: 16px;
            padding: 18px 18px 16px;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .attraction-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 24px rgba(15, 23, 42, 0.08);
            border-color: #dbe5f0;
        }

        .attraction-card.active {
            background: #fffdf4;
            border-color: rgba(244, 180, 0, 0.45);
            box-shadow: 0 14px 28px rgba(244, 180, 0, 0.14);
            transform: translateY(-3px);
        }

        .attraction-card h4 {
            margin: 0 0 8px;
            font-size: 16px;
            line-height: 1.4;
            color: var(--primary);
            font-weight: 700;
        }

        .attraction-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: var(--muted);
        }

        .tourism-closing {
            font-size: 15px;
            line-height: 1.85;
            color: var(--text);
            margin-bottom: 30px;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 26px;
            border-radius: 999px;
            background: var(--accent);
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            font-size: 15px;
            box-shadow: 0 10px 22px rgba(244, 180, 0, 0.22);
            transition: all 0.25s ease;
        }

        .cta-btn:hover {
            background: #ffbf1f;
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(244, 180, 0, 0.28);
        }

        /* =========================
           SLIDER
        ========================== */
        #instaSlider {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.18);
            line-height: 0;
            user-select: none;
            background: #dfe7f1;
        }

        #tourismMainImage {
            width: 100%;
            height: 520px;
            object-fit: cover;
            display: block;
            transition: opacity 0.25s ease;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(15, 23, 42, 0.70) 0%,
                rgba(15, 23, 42, 0.28) 28%,
                rgba(15, 23, 42, 0.02) 55%,
                rgba(15, 23, 42, 0) 100%
            );
            pointer-events: none;
            z-index: 5;
        }

        .slider-caption-inside {
            position: absolute;
            left: 22px;
            bottom: 22px;
            z-index: 20;
            color: #fff;
        }

        .slider-caption-inside small {
            display: block;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.85;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .slider-caption-inside span {
            display: block;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
        }

        .arrow-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.45);
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            z-index: 25;
            backdrop-filter: blur(4px);
            transition: background 0.2s ease, transform 0.2s ease;
            line-height: 1;
        }

        .arrow-btn:hover {
            background: rgba(15, 23, 42, 0.75);
            transform: translateY(-50%) scale(1.08);
        }

        .left-arrow  { left: 16px; }
        .right-arrow { right: 16px; }

        .slider-dots {
            position: absolute;
            bottom: 26px;
            right: 22px;
            display: flex;
            gap: 8px;
            z-index: 25;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.45);
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .dot.active {
            background: var(--accent);
            transform: scale(1.25);
        }

        .slider-meta {
            margin-top: 16px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 18px;
        }

        .slider-meta p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
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
           RESPONSIVE
        ========================== */
        @media (max-width: 1100px) {
            .tourism-layout {
                flex-direction: column;
            }

            .tourism-image,
            .tourism-text {
                width: 100%;
            }

            #tourismMainImage {
                height: 460px;
            }
        }

        @media (max-width: 768px) {
            .overview {
                padding: 0 12px;
                margin: 30px auto 50px;
            }

            .tourism-section-card {
                padding: 24px;
                border-radius: 22px;
            }

            .overview h2 {
                font-size: 30px;
            }

            .subtitle {
                font-size: 16px;
                margin-bottom: 24px;
            }

            .attractions-grid {
                grid-template-columns: 1fr;
            }

            #tourismMainImage {
                height: 320px;
            }

            .slider-caption-inside span {
                font-size: 18px;
            }

            .slider-dots {
                right: 18px;
                bottom: 20px;
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
        <a href="index.php">Home</a>
        <a href="catalog.php">Products</a>
        <a href="tourism.php">Tourism Areas</a>
        <a href="../auth/login.php" class="btn">Login</a>
    </nav>
</header>

<section class="overview">
    <div class="tourism-section-card">
        <span class="section-eyebrow">Featured Destination</span>

        <h2>Discover Lingayen, Pangasinan</h2>
        <p class="subtitle">
            A coastal destination where heritage landmarks, cultural spaces, and scenic waterfront attractions come together in one historic provincial capital.
        </p>

        <div class="tourism-layout">
            <div class="tourism-text">
                <p class="tourism-intro">
                    <strong>Lingayen</strong> stands as one of Pangasinan’s most notable destinations, known for its historic institutions,
                    public landmarks, and beachfront spaces along the Lingayen Gulf. It offers visitors a well-rounded experience
                    that blends local heritage, civic architecture, and coastal leisure.
                </p>

                <div class="attractions-grid">
                    <div class="attraction-card active" data-index="0">
                        <h4>Capitol Beachfront and Baywalk</h4>
                        <p>A scenic seaside promenade ideal for sunset walks, open-air recreation, and family visits.</p>
                    </div>

                    <div class="attraction-card" data-index="1">
                        <h4>Pangasinan Provincial Capitol</h4>
                        <p>A prominent neoclassical landmark widely recognized for its architectural character and civic importance.</p>
                    </div>

                    <div class="attraction-card" data-index="2">
                        <h4>Urduja House</h4>
                        <p>The official residence of the Governor of Pangasinan, named after the legendary Princess Urduja.</p>
                    </div>

                    <div class="attraction-card" data-index="3">
                        <h4>Casa Real and Provincial Museum</h4>
                        <p>A heritage site that preserves and presents the local history, identity, and cultural legacy of Pangasinan.</p>
                    </div>

                    <div class="attraction-card" data-index="4">
                        <h4>Sison Auditorium</h4>
                        <p>A longstanding venue for public programs, cultural performances, and important community events.</p>
                    </div>
                </div>

                <p class="tourism-closing">
                    With its combination of public landmarks, cultural institutions, and coastal attractions, Lingayen offers a meaningful and memorable travel experience in the heart of Pangasinan.
                </p>

                <a href="catalog.php" class="cta-btn">Explore Local Products</a>
            </div>

            <div class="tourism-image">
                <div id="instaSlider">
                    <img id="tourismMainImage" src="../assets/css/images/pictures/lingbeach.jpg" alt="Lingayen Beachfront">

                    <div class="image-overlay"></div>

                    <div class="slider-caption-inside">
                        <small id="imageTag">Coastal Attraction</small>
                        <span id="imageCaption">Lingayen Beachfront</span>
                    </div>

                    <span class="arrow-btn left-arrow" id="prevBtn">&#10094;</span>
                    <span class="arrow-btn right-arrow" id="nextBtn">&#10095;</span>

                    <div class="slider-dots" id="sliderDots"></div>
                </div>

                <div class="slider-meta">
                    <p id="imageDescription">
                        Experience the scenic coastal stretch of Lingayen, known for open public spaces and waterfront views.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const tourismImages = [
        {
            src: "../assets/css/images/pictures/lingbeach.jpg",
            caption: "Lingayen Beachfront",
            tag: "Coastal Attraction",
            description: "Experience the scenic coastal stretch of Lingayen, known for open public spaces and waterfront views."
        },
        {
            src: "../assets/css/images/pictures/lingcapitol.jpg",
            caption: "Pangasinan Provincial Capitol",
            tag: "Heritage Landmark",
            description: "A distinguished neoclassical structure that serves as one of Pangasinan’s most recognizable landmarks."
        },
        {
            src: "../assets/css/images/pictures/urdujahouse.jpg",
            caption: "Urduja House",
            tag: "Historic Residence",
            description: "An official provincial residence associated with local heritage and the legend of Princess Urduja."
        },
        {
            src: "../assets/css/images/pictures/provmuseum.jpg",
            caption: "Casa Real and Pangasinan Provincial Museum",
            tag: "Cultural Site",
            description: "A heritage destination that showcases the history, culture, and legacy of Pangasinan."
        },
        {
            src: "../assets/css/images/pictures/sisonauditorium.jpg",
            caption: "Sison Auditorium",
            tag: "Public Venue",
            description: "A long-established public venue for performances, civic events, and community gatherings."
        },
    ];

    let current = 0;
    const img = document.getElementById("tourismMainImage");
    const caption = document.getElementById("imageCaption");
    const tag = document.getElementById("imageTag");
    const description = document.getElementById("imageDescription");
    const dotsWrap = document.getElementById("sliderDots");
    const slider = document.getElementById("instaSlider");
    const cards = document.querySelectorAll(".attraction-card");

    // Build dots
    tourismImages.forEach((_, i) => {
        const d = document.createElement("span");
        d.className = "dot" + (i === 0 ? " active" : "");
        d.addEventListener("click", (e) => {
            e.stopPropagation();
            goTo(i);
            resetAutoSlide();
        });
        dotsWrap.appendChild(d);
    });

    function updateDots() {
        dotsWrap.querySelectorAll(".dot").forEach((d, i) => {
            d.classList.toggle("active", i === current);
        });
    }

    function updateActiveCard() {
        cards.forEach((card, i) => {
            card.classList.toggle("active", i === current);
        });
    }

    function goTo(index) {
        current = (index + tourismImages.length) % tourismImages.length;

        img.style.opacity = 0;

        setTimeout(() => {
            img.src = tourismImages[current].src;
            img.alt = tourismImages[current].caption;
            caption.textContent = tourismImages[current].caption;
            tag.textContent = tourismImages[current].tag;
            description.textContent = tourismImages[current].description;
            img.style.opacity = 1;

            updateDots();
            updateActiveCard();
        }, 180);
    }

    // Clickable attraction cards
    cards.forEach((card) => {
        card.addEventListener("click", () => {
            const index = parseInt(card.dataset.index);
            goTo(index);
            resetAutoSlide();
        });
    });

    document.getElementById("prevBtn").addEventListener("click", (e) => {
        e.stopPropagation();
        goTo(current - 1);
        resetAutoSlide();
    });

    document.getElementById("nextBtn").addEventListener("click", (e) => {
        e.stopPropagation();
        goTo(current + 1);
        resetAutoSlide();
    });

    // Touch swipe
    let touchStartX = 0;

    slider.addEventListener("touchstart", e => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });

    slider.addEventListener("touchend", e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            goTo(current + (diff > 0 ? 1 : -1));
            resetAutoSlide();
        }
    });

    // Auto-slide
    let autoSlide = setInterval(() => goTo(current + 1), 5000);

    function resetAutoSlide() {
        clearInterval(autoSlide);
        autoSlide = setInterval(() => goTo(current + 1), 5000);
    }
</script>

<footer class="site-footer">
    <div class="footer-content">
        <h3>LakbayLokal Marketplace</h3>
        <p class="footer-tagline">Your online destination for authentic souvenir products from Lingayen, Pangasinan.</p>
    </div>

    <div class="footer-bottom">
        <p>© 2026 LakbayLokal Marketplace — Promoting Lingayen Souvenir Shops and Local Products</p>
    </div>
</footer>

</body>
</html>