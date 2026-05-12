<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
start_session();
require_login('/login.php');

$user = current_user();
$db   = get_db();

// Bookings
$bStmt = $db->prepare("SELECT * FROM bookings WHERE user_id=? ORDER BY created_at DESC");
$bStmt->execute([$user['id']]);
$bookings = $bStmt->fetchAll();

// Photos (same ORDER BY as download.php so free-slot positions are consistent)
$photos = $db->query("SELECT * FROM photos ORDER BY created_at ASC")->fetchAll();

// Build free-slot IDs from DB order (mirrors download.php logic exactly)
$freeCount   = (int)get_setting('free_photos_count', (string)FREE_PHOTOS_COUNT);
$freePhotoIds = array_column(array_slice($photos, 0, $freeCount), 'id');

// Already downloaded
$dlStmt = $db->prepare("SELECT photo_id FROM photo_downloads WHERE user_id=?");
$dlStmt->execute([$user['id']]);
$downloaded = array_column($dlStmt->fetchAll(), null, 'photo_id'); // keyed by photo_id

$statusLabel = ['pending'=>'অপেক্ষমান','confirmed'=>'নিশ্চিত','completed'=>'সম্পন্ন','cancelled'=>'বাতিল'];
$statusColor = ['pending'=>'#d4af37','confirmed'=>'#22c55e','completed'=>'#3b82f6','cancelled'=>'#ef4444'];
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রোফাইল | <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-page{padding:40px 0 80px;}
        .profile-header{background:var(--dark2);border:1px solid rgba(212,175,55,.18);border-radius:16px;padding:28px 32px;margin-bottom:28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;}
        .avatar{width:70px;height:70px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:var(--dark);flex-shrink:0;}
        .profile-info h2{margin:0 0 4px;color:var(--white);font-size:1.4rem;}
        .profile-info p{margin:0;color:var(--muted);font-size:.88rem;}
        .balance-badge{margin-left:auto;background:rgba(212,175,55,.12);border:1px solid var(--gold);border-radius:12px;padding:12px 22px;text-align:center;}
        .balance-badge .amt{font-size:1.8rem;font-weight:700;color:var(--gold);display:block;}
        .balance-badge small{color:var(--muted);font-size:.8rem;}
        .sec-card{background:var(--dark2);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:24px;margin-bottom:24px;}
        .sec-card h3{color:var(--gold);margin:0 0 16px;font-size:1.05rem;display:flex;align-items:center;gap:8px;}
        table.data-tbl{width:100%;border-collapse:collapse;font-size:.88rem;}
        table.data-tbl th{text-align:left;padding:10px 12px;color:var(--muted);border-bottom:1px solid rgba(255,255,255,.06);font-weight:600;}
        table.data-tbl td{padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.04);color:var(--light);}
        table.data-tbl tr:last-child td{border-bottom:none;}
        .badge-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.8rem;font-weight:600;border:1px solid;}
        .photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;}
        .photo-card{background:var(--dark3);border:1px solid rgba(255,255,255,.07);border-radius:12px;overflow:hidden;}
        .photo-card img{width:100%;height:150px;object-fit:cover;display:block;}
        .photo-card .ph-img-placeholder{width:100%;height:150px;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--muted);}
        .photo-card .ph-body{padding:12px;}
        .photo-card .ph-title{font-size:.88rem;color:var(--light);margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .photo-card .ph-action{display:block;text-align:center;padding:8px 12px;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;border:none;width:100%;}
        .ph-free{background:rgba(59,183,80,.15);color:#3fb950;border:1px solid rgba(59,183,80,.35);}
        .ph-paid{background:rgba(212,175,55,.15);color:var(--gold);border:1px solid rgba(212,175,55,.35);}
        .ph-done{background:rgba(59,130,246,.12);color:#60a5fa;border:1px solid rgba(59,130,246,.3);}
        .ph-nobal{background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.3);cursor:default;}
        .nav-back{margin-bottom:20px;}
        .nav-back a{color:var(--gold);font-size:.9rem;}
    </style>
</head>
<body>
<div style="background:var(--dark2);border-bottom:1px solid rgba(255,255,255,.06);padding:14px 0;">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;">
        <a href="/" class="logo" style="font-size:1.1rem;"><?= htmlspecialchars(PHOTOGRAPHER_NAME) ?> <span>Photography</span></a>
        <div style="display:flex;gap:12px;align-items:center;">
            <span style="color:var(--muted);font-size:.88rem;">👤 <?= htmlspecialchars($user['name']) ?></span>
            <a href="logout.php" class="btn btn-outline" style="padding:8px 16px;font-size:.82rem;">লগআউট</a>
        </div>
    </div>
</div>

<div class="container profile-page">

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="avatar"><?= mb_substr($user['name'], 0, 1) ?></div>
        <div class="profile-info">
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <p>📞 <?= htmlspecialchars($user['phone']) ?>
            <?php if ($user['email']): ?> &nbsp;|&nbsp; 📧 <?= htmlspecialchars($user['email']) ?><?php endif; ?></p>
            <p style="margin-top:4px;">সদস্য হয়েছেন: <?= htmlspecialchars(substr($user['created_at'], 0, 10)) ?></p>
        </div>
        <div class="balance-badge">
            <span class="amt">৳<?= number_format($user['balance'], 0) ?></span>
            <small>অ্যাকাউন্ট ব্যালেন্স</small>
        </div>
    </div>

    <!-- Bookings -->
    <div class="sec-card">
        <h3>📋 আমার বুকিংসমূহ</h3>
        <?php if ($bookings): ?>
        <div style="overflow-x:auto;">
        <table class="data-tbl">
            <thead>
                <tr>
                    <th>#</th><th>সার্ভিস</th><th>তারিখ</th><th>সময়</th><th>স্ট্যাটাস</th><th>বিস্তারিত</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $i => $b): $clr = $statusColor[$b['status']] ?? '#888'; ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($b['service']) ?></td>
                    <td><?= htmlspecialchars($b['booking_date']) ?></td>
                    <td><?= htmlspecialchars($b['booking_time']) ?></td>
                    <td><span class="badge-status" style="color:<?= $clr ?>;border-color:<?= $clr ?>;"><?= $statusLabel[$b['status']] ?? $b['status'] ?></span></td>
                    <td style="color:var(--muted);font-size:.82rem;"><?= htmlspecialchars(substr($b['details'] ?: '-', 0, 60)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p style="color:var(--muted);text-align:center;padding:20px 0;">এখনো কোনো বুকিং নেই। <a href="/#booking">বুকিং করুন</a></p>
        <?php endif; ?>
    </div>

    <!-- Photo Gallery -->
    <div class="sec-card">
        <h3>🖼️ ফটো গ্যালারি <span style="font-size:.8rem;color:var(--muted);font-weight:400;">(প্রথম <?= $freeCount ?>টি বিনামূল্যে, বাকি প্রতিটি ৳<?= PHOTO_PRICE ?>)</span></h3>

        <?php if (!$photos): ?>
            <p style="color:var(--muted);text-align:center;padding:24px 0;">এখনো কোনো ছবি আপলোড হয়নি।</p>
        <?php else: ?>
        <div class="photo-grid">
            <?php foreach ($photos as $p):
                $isFreeSlot = in_array($p['id'], $freePhotoIds, true);
                $alreadyDl  = isset($downloaded[$p['id']]);
                $photoPath  = 'uploads/photos/' . $p['filename'];
                $hasFile    = file_exists(__DIR__ . '/' . $photoPath);
            ?>
            <div class="photo-card">
                <?php if ($hasFile): ?>
                    <img src="<?= htmlspecialchars($photoPath) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="ph-img-placeholder">📷</div>
                <?php endif; ?>
                <div class="ph-body">
                    <div class="ph-title"><?= htmlspecialchars($p['title']) ?></div>
                    <?php if ($alreadyDl): ?>
                        <a href="download.php?photo_id=<?= $p['id'] ?>" class="ph-action ph-done">✓ ডাউনলোড করা আছে</a>
                    <?php elseif ($isFreeSlot || $p['is_free']): ?>
                        <a href="download.php?photo_id=<?= $p['id'] ?>" class="ph-action ph-free">বিনামূল্যে ডাউনলোড</a>
                    <?php elseif ($user['balance'] >= $p['price']): ?>
                        <a href="download.php?photo_id=<?= $p['id'] ?>" class="ph-action ph-paid">৳<?= $p['price'] ?> ডাউনলোড</a>
                    <?php else: ?>
                        <span class="ph-action ph-nobal">ব্যালেন্স অপর্যাপ্ত</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
