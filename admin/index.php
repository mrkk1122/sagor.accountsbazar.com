<?php
$__title = 'ড্যাশবোর্ড';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$totalUsers    = $db->query("SELECT COUNT(*) FROM users WHERE is_admin=0")->fetchColumn();
$totalBookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending       = $db->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$totalPhotos   = $db->query("SELECT COUNT(*) FROM photos")->fetchColumn();

$recent = $db->query(
    "SELECT b.*, u.name as uname FROM bookings b LEFT JOIN users u ON b.user_id=u.id ORDER BY b.created_at DESC LIMIT 10"
)->fetchAll();

$sl = ['pending'=>'b-pending','confirmed'=>'b-confirmed','completed'=>'b-completed','cancelled'=>'b-cancelled'];
$sn = ['pending'=>'অপেক্ষমান','confirmed'=>'নিশ্চিত','completed'=>'সম্পন্ন','cancelled'=>'বাতিল'];
?>

<div class="stats-grid">
    <div class="stat-card"><div class="st-icon">👥</div><div class="st-num"><?= $totalUsers ?></div><div class="st-label">মোট ইউজার</div></div>
    <div class="stat-card"><div class="st-icon">📋</div><div class="st-num"><?= $totalBookings ?></div><div class="st-label">মোট বুকিং</div></div>
    <div class="stat-card"><div class="st-icon">⏳</div><div class="st-num"><?= $pending ?></div><div class="st-label">অপেক্ষমান বুকিং</div></div>
    <div class="stat-card"><div class="st-icon">🖼️</div><div class="st-num"><?= $totalPhotos ?></div><div class="st-label">মোট ছবি</div></div>
</div>

<div class="table-card">
    <div class="table-card-hdr">
        <h3>সাম্প্রতিক বুকিং</h3>
        <a href="bookings.php" class="btn btn-outline btn-sm">সব দেখুন</a>
    </div>
    <div class="table-scroll">
    <table class="at">
        <thead><tr><th>#</th><th>ইউজার</th><th>ফোন</th><th>সার্ভিস</th><th>তারিখ</th><th>স্ট্যাটাস</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['uname'] ?? $b['name']) ?></td>
            <td><?= htmlspecialchars($b['phone']) ?></td>
            <td><?= htmlspecialchars($b['service']) ?></td>
            <td><?= htmlspecialchars($b['booking_date']) ?></td>
            <td><span class="badge <?= $sl[$b['status']] ?? '' ?>"><?= $sn[$b['status']] ?? $b['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:20px;">কোনো বুকিং নেই</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
