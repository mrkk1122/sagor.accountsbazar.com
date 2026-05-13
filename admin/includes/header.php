<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
start_session();
require_admin();

$adminId   = $_SESSION['admin_id'];
$adminStmt = get_db()->prepare("SELECT name FROM users WHERE id=?");
$adminStmt->execute([$adminId]);
$adminName = $adminStmt->fetchColumn() ?: 'Admin';
$page      = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($__title ?? 'অ্যাডমিন') ?> | <?= htmlspecialchars(SITE_NAME) ?> অ্যাডমিন</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="../css/admin.css?v=20260512-3">
</head>
<body>

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <span>⚙️ অ্যাডমিন প্যানেল</span>
        <small><?= htmlspecialchars(SITE_NAME) ?></small>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php"    class="<?= $page==='index'    ? 'active':'' ?>"><span class="icon">📊</span> ড্যাশবোর্ড</a>
        <a href="bookings.php" class="<?= $page==='bookings' ? 'active':'' ?>"><span class="icon">📋</span> বুকিং</a>
        <a href="balance-requests.php" class="<?= $page==='balance-requests' ? 'active':'' ?>"><span class="icon">💳</span> ব্যালেন্স রিকোয়েস্ট</a>
        <a href="users.php"    class="<?= $page==='users'    ? 'active':'' ?>"><span class="icon">👥</span> ইউজার</a>
        <a href="photos.php"   class="<?= $page==='photos'   ? 'active':'' ?>"><span class="icon">🖼️</span> ছবি</a>
        <a href="settings.php" class="<?= $page==='settings' ? 'active':'' ?>"><span class="icon">⚙️</span> সেটিংস</a>
    </nav>
    <div class="sidebar-footer">
        👤 <?= htmlspecialchars($adminName) ?><br>
        <a href="logout.php" style="color:#ef4444;">লগআউট</a> &nbsp;|&nbsp;
        <a href="/" style="color:var(--muted);" target="_blank">সাইট দেখুন</a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-topbar">
        <h1><?= htmlspecialchars($__title ?? 'অ্যাডমিন') ?></h1>
        <div style="display:flex;align-items:center;gap:10px;">
            <button id="admin-install-btn" class="btn btn-outline btn-sm" style="display:none;" disabled>📱 Install</button>
            <span style="color:var(--muted);font-size:.85rem;"><?= date('d M Y') ?></span>
        </div>
    </div>
    <nav class="admin-mobile-nav" aria-label="Admin quick navigation">
        <a href="index.php" class="<?= $page==='index' ? 'active':'' ?>">📊 ড্যাশবোর্ড</a>
        <a href="bookings.php" class="<?= $page==='bookings' ? 'active':'' ?>">📋 বুকিং</a>
        <a href="balance-requests.php" class="<?= $page==='balance-requests' ? 'active':'' ?>">💳 ব্যালেন্স</a>
        <a href="users.php" class="<?= $page==='users' ? 'active':'' ?>">👥 ইউজার</a>
        <a href="photos.php" class="<?= $page==='photos' ? 'active':'' ?>">🖼️ ছবি</a>
        <a href="settings.php" class="<?= $page==='settings' ? 'active':'' ?>">⚙️ সেটিংস</a>
    </nav>
    <div class="admin-content">
<script>
(function(){
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    }

    var installPrompt = null;
    var installBtn = document.getElementById('admin-install-btn');
    window.addEventListener('beforeinstallprompt', function(e){
        e.preventDefault();
        installPrompt = e;
        if (installBtn) {
            installBtn.style.display = 'inline-block';
            installBtn.disabled = false;
        }
    });
    if (installBtn) {
        installBtn.addEventListener('click', function(){
            if (!installPrompt) return;
            installPrompt.prompt();
            installPrompt.userChoice.then(function(){
                installPrompt = null;
                installBtn.disabled = true;
            });
        });
    }

    function pushNotify(title, body) {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            new Notification(title, { body: body, icon: '/img/icon-192.png' });
        } else if (Notification.permission === 'denied') {
            alert('Notification permission denied!\nনোটিফিকেশন চালু করতে ব্রাউজার সেটিংস থেকে অনুমতি দিন।');
        }
    }

    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().catch(function(){});
    }

    var lastBookingId = Number(localStorage.getItem('admin:lastBookingId') || '0');
    var lastHelpId = Number(localStorage.getItem('admin:lastHelpId') || '0');

    function pollAdminNotifications() {
        fetch('notifications.php?since_booking=' + encodeURIComponent(lastBookingId) + '&since_help=' + encodeURIComponent(lastHelpId), { credentials: 'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (!data || !data.ok) return;

                if (data.new_booking_count > 0) {
                    pushNotify('নতুন বুকিং এসেছে', data.new_booking_count + 'টি নতুন বুকিং পাওয়া গেছে।');
                }
                if (data.new_help_count > 0) {
                    pushNotify('নতুন হেল্প রিকোয়েস্ট', data.new_help_count + 'টি নতুন হেল্প রিকোয়েস্ট পাওয়া গেছে।');
                }

                if (data.latest_booking_id >= 0) {
                    lastBookingId = data.latest_booking_id;
                    localStorage.setItem('admin:lastBookingId', String(lastBookingId));
                }
                if (data.latest_help_id >= 0) {
                    lastHelpId = data.latest_help_id;
                    localStorage.setItem('admin:lastHelpId', String(lastHelpId));
                }
            })
            .catch(function(){});
    }

    pollAdminNotifications();
    setInterval(pollAdminNotifications, 15000);
})();
</script>
