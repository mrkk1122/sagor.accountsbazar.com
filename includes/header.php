<?php
require_once __DIR__ . '/auth.php';
start_session();
$__user = current_user();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME) ?> | <?= htmlspecialchars(SITE_TAGLINE) ?></title>
    <meta name="description" content="প্রফেশনাল ফটোগ্রাফি সার্ভিস - বিয়ে, জন্মদিন, আউটডোর শুট ও সকল ইভেন্টের অ্যাডভান্স বুকিং নিন।">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="css/style.css?v=20260512-7">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<header id="top">
    <div class="navbar-wrap">
        <div class="container nav-inner">
            <a href="/" class="logo"><?= PHOTOGRAPHER_NAME ?> <span>Photography</span></a>
            <button class="hamburger" id="hamburger" aria-label="Menu">&#9776;</button>
            <nav id="main-nav">
                <a href="/#about">পরিচিতি</a>
                <a href="/#services">সার্ভিস</a>
                <a href="/booking.php">বুকিং</a>
                <a href="/#contact">যোগাযোগ</a>
                <?php if ($__user): ?>
                    <a href="/profile.php" class="main-nav-profile"><span class="nav-avatar"><?= htmlspecialchars(mb_substr($__user['name'], 0, 1)) ?></span> প্রোফাইল</a>
                    <a href="/logout.php">লগআউট</a>
                <?php else: ?>
                    <a href="/login.php">লগইন</a>
                    <a href="/register.php">নিবন্ধন</a>
                <?php endif; ?>
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
                    <a href="/#services" class="btn btn-gold">সার্ভিস দেখুন</a>
                    <a href="/#contact" class="btn btn-outline">যোগাযোগ করুন</a>
                    <button id="pwa-install-btn" class="btn btn-outline" style="display:none;align-items:center;gap:6px;">📱 অ্যাপ ইনস্টল করুন</button>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
(function(){
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    }
    var _prompt = null;
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        _prompt = e;
        var btn = document.getElementById('pwa-install-btn');
        if (btn) btn.style.display = 'inline-flex';
    });
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('pwa-install-btn');
        if (btn) btn.addEventListener('click', function() {
            if (!_prompt) return;
            _prompt.prompt();
            _prompt.userChoice.then(function(r) {
                if (r.outcome === 'accepted') btn.style.display = 'none';
                _prompt = null;
            });
        });
    });
})();
</script>
