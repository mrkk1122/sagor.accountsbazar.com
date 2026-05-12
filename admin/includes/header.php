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
    <link rel="stylesheet" href="../css/admin.css">
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
        <span style="color:var(--muted);font-size:.85rem;"><?= date('d M Y') ?></span>
    </div>
    <div class="admin-content">
