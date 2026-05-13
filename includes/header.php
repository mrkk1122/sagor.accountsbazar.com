<?php
require_once __DIR__ . '/auth.php';
start_session();
$__user = current_user();
$__showHero = isset($__showHero) ? (bool)$__showHero : (basename($_SERVER['SCRIPT_NAME']) === 'index.php');
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
    <link rel="stylesheet" href="css/style.css?v=20260512-13-12">
</head>
<body>

<!-- ===== WELCOME INSTALL MODAL ===== -->
<div id="welcome-install-modal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(13,17,23,.96);backdrop-filter:blur(2px);align-items:center;justify-content:center;">
    <div style="background:var(--dark2,#141a2f);border-radius:18px;box-shadow:0 8px 32px #0008;padding:38px 28px 28px 28px;max-width:340px;width:90vw;text-align:center;">
        <div style="font-size:2.2rem;margin-bottom:12px;">👋</div>
        <h2 style="color:var(--gold);margin-bottom:10px;">স্বাগতম!</h2>
        <p style="color:var(--light,#f0f6fc);margin-bottom:18px;">ওয়েবসাইট ব্যবহার করতে আগে অ্যাপ ইনস্টল করুন। ইনস্টল সম্পন্ন হলে হোমপেজ ওপেন হবে।</p>
        <button id="welcome-install-btn" class="btn btn-gold" style="width:100%;font-size:1.1rem;">📱 ইনস্টল করুন</button>
    </div>
</div>

<!-- ===== NAVBAR ===== -->
<header id="top">
    <div class="navbar-wrap">
        <div class="container nav-inner">
            <a href="/" class="logo"><?= PHOTOGRAPHER_NAME ?> <span>Photography</span></a>
            <nav id="main-nav">
                <a href="/#about">পরিচিতি</a>
                <a href="/#services">সার্ভিস</a>
                <a href="/booking.php">বুকিং</a>
                <a href="/contact.php">যোগাযোগ</a>
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

    <?php if ($__showHero): ?>
    <!-- ===== HERO ===== -->
    <div class="hero-section">
        <div class="container hero-inner">
            <div class="hero-text">
                <span class="badge">✦ Professional Photographer</span>
                <h1>আপনার বিশেষ মুহূর্ত<br>ক্যামেরায় ধরে রাখুন</h1>
                <p>বিয়ে, জন্মদিন, আউটডোর শুট, পোর্ট্রেট সহ সব ধরনের ইভেন্টের প্রফেশনাল ফটোগ্রাফি সার্ভিস। প্রতিটি ছবির মূল্য মাত্র <strong>৳<?= PRICE_PER_PHOTO ?></strong> টাকা।</p>
                <div class="hero-btns">
                    <a href="/#services" class="btn btn-gold">সার্ভিস দেখুন</a>
                    <a href="/contact.php" class="btn btn-outline">যোগাযোগ করুন</a>
                    <button id="pwa-install-btn" class="btn btn-outline" style="display:none;align-items:center;gap:6px;">📱 অ্যাপ ইনস্টল করুন</button>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-photo-frame">
                    <img src="https://scontent.fdac177-1.fna.fbcdn.net/v/t39.30808-6/600275462_1510949467403993_4294733459223387361_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=cc71e4&_nc_eui2=AeHx-N6Du_gK4b1xb6ZRldxTaGqclblDRDNoapyVuUNEMzK1XlEyyoRwmzlEOwGRYNygvl3PD-zIJvX91DAifWxL&_nc_ohc=bZ-VCLnd2n4Q7kNvwGKFcaf&_nc_oc=AdpdtY3e_kbZXmlmoTG4rFe_j20AHhbKu717HjHDeHaXoYxiP314V4EaSqRw5sdQuLs&_nc_zt=23&_nc_ht=scontent.fdac177-1.fna&_nc_gid=ZdKTOdPg1DH4aFpJdhrEqg&_nc_ss=7b2a8&oh=00_Af76V0KDcEbYDqvH_6SjzuIUiiKx80YTyKPuB3DI5gi8kQ&oe=6A099680" alt="Camera man with professional camera" style="display:block; margin-left:auto; margin-right:auto; max-width:420px; width:100%; border-radius:18px; box-shadow:0 4px 24px #0002;" loading="eager">
                </div>
                <span class="hero-float-badge">Live Shoot</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</header>
<script>
(function(){
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    }
    var _prompt = null;
    var installModal = document.getElementById('welcome-install-modal');
    var installBtn = document.getElementById('welcome-install-btn');
    var pwaBtn = document.getElementById('pwa-install-btn');
    var installed = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

    function openHomeAfterInstall() {
        if (window.location.pathname !== '/') {
            window.location.href = '/';
            return;
        }
        window.location.reload();
    }

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        _prompt = e;
        if (pwaBtn) pwaBtn.style.display = 'inline-flex';
        if (!installed && installModal) installModal.style.display = 'flex';
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (!installed && installModal) installModal.style.display = 'flex';
        if (installBtn) installBtn.onclick = function() {
            if (_prompt) {
                _prompt.prompt();
                _prompt.userChoice.then(function(r) {
                    if (r.outcome === 'accepted') {
                        if (installModal) installModal.style.display = 'none';
                        openHomeAfterInstall();
                    }
                });
            } else {
                window.location.href = '/';
            }
        };
        if (pwaBtn) pwaBtn.addEventListener('click', function() {
            if (!_prompt) {
                window.location.href = '/';
                return;
            }
            _prompt.prompt();
            _prompt.userChoice.then(function(r) {
                if (r.outcome === 'accepted') pwaBtn.style.display = 'none';
                _prompt = null;
            });
        });
    });

    window.addEventListener('appinstalled', function() {
        if (installModal) installModal.style.display = 'none';
        openHomeAfterInstall();
    });
})();
</script>
