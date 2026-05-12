<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME) ?> | <?= htmlspecialchars(SITE_TAGLINE) ?></title>
    <meta name="description" content="প্রফেশনাল ফটোগ্রাফি সার্ভিস - বিয়ে, জন্মদিন, আউটডোর শুট ও সকল ইভেন্টের অ্যাডভান্স বুকিং নিন।">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<header id="top">
    <div class="navbar-wrap">
        <div class="container nav-inner">
            <a href="#top" class="logo"><?= PHOTOGRAPHER_NAME ?> <span>Photography</span></a>
            <button class="hamburger" id="hamburger" aria-label="Menu">&#9776;</button>
            <nav id="main-nav">
                <a href="#about">পরিচিতি</a>
                <a href="#services">সার্ভিস</a>
                <a href="#pricing">মূল্য</a>
                <a href="#schedule">সময়সূচি</a>
                <a href="#booking">বুকিং</a>
                <a href="#contact">যোগাযোগ</a>
            </nav>
        </div>
    </div>

    <!-- ===== HERO ===== -->
    <div class="hero-section">
        <div class="container hero-inner">
            <div class="hero-text">
                <span class="badge">✦ Professional Photographer</span>
                <h1>আপনার বিশেষ মুহূর্ত<br>ক্যামেরায় ধরে রাখুন</h1>
                <p>বিয়ে, জন্মদিন, আউটডোর শুট, পোর্ট্রেট সহ সব ধরনের ইভেন্টের প্রফেশনাল ফটোগ্রাফি সার্ভিস। প্রতিটি ছবির মূল্য মাত্র <strong>৳<?= PRICE_PER_PHOTO ?></strong> টাকা।</p>
                <div class="hero-btns">
                    <a href="#booking" class="btn btn-gold">এখনই বুকিং করুন</a>
                    <a href="#schedule" class="btn btn-outline">সময়সূচি দেখুন</a>
                </div>
            </div>
        </div>
    </div>
</header>
