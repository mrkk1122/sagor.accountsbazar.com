<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
start_session();
require_login('/login.php');

$user = current_user();
$db   = get_db();
$msg  = '';
$err  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_balance_request'])) {
    $amount = (float)($_POST['amount'] ?? 0);
    $note = trim($_POST['note'] ?? '');

    if ($amount <= 0) {
        $err = 'সঠিক পরিমাণ লিখুন।';
    } elseif ($amount > 1000000) {
        $err = 'পরিমাণ খুব বেশি।';
    } else {
        $db->prepare("INSERT INTO balance_requests (user_id, amount, note, status) VALUES (?,?,?,'pending')")
           ->execute([$user['id'], $amount, $note]);
        $msg = 'ব্যালেন্স রিকোয়েস্ট পাঠানো হয়েছে। অ্যাডমিন কনফার্ম করলে ব্যালেন্স যোগ হবে।';
    }
}

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

$totalBookings   = count($bookings);
$totalDownloads = count($downloaded);
$photoCount     = count($photos);

$rStmt = $db->prepare("SELECT * FROM balance_requests WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$rStmt->execute([$user['id']]);
$balanceRequests = $rStmt->fetchAll();

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
    <link rel="stylesheet" href="css/style.css?v=20260512-12">
    <style>
        .profile-page{padding:40px 0 80px;}
        .profile-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:24px;}
        .profile-kpi{background:linear-gradient(140deg,rgba(212,175,55,.12),rgba(212,175,55,.04));border:1px solid rgba(212,175,55,.22);border-radius:14px;padding:16px;}
        .profile-kpi .k-num{display:block;font-size:1.5rem;font-weight:700;color:var(--gold);line-height:1.1;}
        .profile-kpi .k-lbl{font-size:.8rem;color:var(--muted);margin-top:5px;display:block;}
        .profile-topbar{background:var(--dark2);border-bottom:1px solid rgba(255,255,255,.06);padding:14px 0;}
        .profile-topbar-inner{display:flex;align-items:center;justify-content:space-between;gap:12px;}
        .profile-topbar-right{display:flex;gap:12px;align-items:center;}
        .profile-user{color:var(--muted);font-size:.88rem;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .profile-header{background:linear-gradient(120deg,rgba(22,27,34,.98),rgba(28,33,40,.96));border:1px solid rgba(212,175,55,.28);border-radius:18px;padding:30px 32px;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 14px 30px rgba(0,0,0,.24);}
        .avatar{width:70px;height:70px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:var(--dark);flex-shrink:0;}
        .profile-info h2{margin:0 0 4px;color:var(--white);font-size:1.4rem;}
        .profile-info p{margin:0;color:var(--muted);font-size:.88rem;}
        .profile-meta{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
        .meta-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);font-size:.8rem;color:var(--light);}
        .meta-pill.joined{background:rgba(212,175,55,.1);border-color:rgba(212,175,55,.3);color:var(--gold);}
        .balance-badge{margin-left:auto;background:rgba(212,175,55,.12);border:1px solid var(--gold);border-radius:12px;padding:12px 22px;text-align:center;}
        .balance-badge .amt{font-size:1.8rem;font-weight:700;color:var(--gold);display:block;}
        .balance-badge small{color:var(--muted);font-size:.8rem;}
        .balance-action{margin-top:10px;display:flex;justify-content:center;}
        .balance-action .btn{width:100%;max-width:210px;}
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
        .balance-modal{position:fixed;inset:0;background:rgba(0,0,0,.78);backdrop-filter:blur(2px);display:none;z-index:1200;padding:14px;overflow:auto;}
        .balance-modal.active{display:block;}
        .balance-modal-panel{width:min(960px,100%);min-height:calc(100vh - 28px);margin:0 auto;background:linear-gradient(145deg,#11161d,#161d26);border:1px solid rgba(212,175,55,.28);border-radius:16px;box-shadow:0 20px 55px rgba(0,0,0,.45);display:flex;flex-direction:column;}
        .balance-modal-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.08);}
        .balance-modal-head h3{margin:0;font-size:1.08rem;color:var(--gold);}
        .balance-modal-close{background:transparent;border:1px solid rgba(255,255,255,.2);color:var(--light);border-radius:10px;padding:6px 10px;cursor:pointer;}
        .balance-modal-body{padding:18px;display:flex;flex-direction:column;gap:14px;}
        .balance-help{font-size:.86rem;color:var(--muted);background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:10px 12px;}
        .nav-back{margin-bottom:20px;}
        .nav-back a{color:var(--gold);font-size:.9rem;}
        .sec-card{box-shadow:0 10px 22px rgba(0,0,0,.18);}
        @media (max-width:768px){
            .profile-page{padding:24px 0 88px;}
            .profile-kpis{grid-template-columns:1fr 1fr;}
            .profile-topbar-inner{flex-wrap:wrap;}
            .profile-topbar-right{margin-left:auto;}
            .profile-user{display:none;}
            .profile-header{padding:20px 16px;gap:14px;}
            .profile-info h2{font-size:1.15rem;}
            .balance-badge{width:100%;margin-left:0;}
            .balance-action .btn{max-width:none;}
            .sec-card{padding:16px;}
            table.data-tbl{font-size:.82rem;min-width:640px;}
            .photo-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
            .balance-modal{padding:8px;}
            .balance-modal-panel{min-height:calc(100vh - 16px);border-radius:12px;}
            .balance-modal-head{padding:12px 12px;}
            .balance-modal-body{padding:12px;}
        }
        @media (max-width:480px){
            .profile-kpis{grid-template-columns:1fr;}
            .photo-grid{grid-template-columns:1fr;}
            .avatar{width:58px;height:58px;font-size:1.6rem;}
            .balance-badge .amt{font-size:1.5rem;}
            .meta-pill{font-size:.74rem;padding:5px 8px;}
        }
    </style>
</head>
<body>
<div class="profile-topbar">
    <div class="container profile-topbar-inner">
        <a href="/" class="logo" style="font-size:1.1rem;"><?= htmlspecialchars(PHOTOGRAPHER_NAME) ?> <span>Photography</span></a>
        <div class="profile-topbar-right">
            <span class="profile-user">👤 <?= htmlspecialchars($user['name']) ?></span>
            <a href="logout.php" class="btn btn-outline" style="padding:8px 16px;font-size:.82rem;">লগআউট</a>
        </div>
    </div>
</div>

<div class="container profile-page">

    <?php if ($msg): ?><div class="alert alert-success" style="margin-bottom:14px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-error" style="margin-bottom:14px;"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="avatar"><?= mb_substr($user['name'], 0, 1) ?></div>
        <div class="profile-info">
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <div class="profile-meta">
                <span class="meta-pill">📞 <?= htmlspecialchars($user['phone']) ?></span>
                <?php if ($user['email']): ?><span class="meta-pill">📧 <?= htmlspecialchars($user['email']) ?></span><?php endif; ?>
                <span class="meta-pill joined">✨ সদস্য হয়েছেন: <?= htmlspecialchars(substr($user['created_at'], 0, 10)) ?></span>
            </div>
        </div>
        <div class="balance-badge">
            <span class="amt">৳<?= number_format($user['balance'], 0) ?></span>
            <small>অ্যাকাউন্ট ব্যালেন্স</small>
            <div class="balance-action">
                <button type="button" class="btn btn-gold" id="open-balance-modal">ব্যালেন্স যোগ</button>
            </div>
        </div>
    </div>

    <div class="profile-kpis">
        <div class="profile-kpi"><span class="k-num"><?= $totalBookings ?></span><span class="k-lbl">মোট বুকিং</span></div>
        <div class="profile-kpi"><span class="k-num"><?= $totalDownloads ?></span><span class="k-lbl">ডাউনলোড করা ছবি</span></div>
        <div class="profile-kpi"><span class="k-num"><?= $photoCount ?></span><span class="k-lbl">মোট উপলব্ধ ছবি</span></div>
    </div>

    <div class="sec-card">
        <h3>💳 ব্যালেন্স রিকোয়েস্ট হিস্টোরি</h3>
        <?php if ($balanceRequests): ?>
            <div style="overflow-x:auto;margin-top:14px;">
                <table class="data-tbl">
                    <thead><tr><th>#</th><th>পরিমাণ</th><th>স্ট্যাটাস</th><th>অ্যাডমিন নোট</th><th>তারিখ</th></tr></thead>
                    <tbody>
                    <?php foreach ($balanceRequests as $i => $r):
                        $clr = $r['status'] === 'confirmed' ? '#22c55e' : ($r['status'] === 'rejected' ? '#ef4444' : '#d4af37');
                        $lbl = $r['status'] === 'confirmed' ? 'কনফার্ম' : ($r['status'] === 'rejected' ? 'বাতিল' : 'অপেক্ষমান');
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>৳<?= number_format((float)$r['amount'], 0) ?></td>
                            <td><span class="badge-status" style="color:<?= $clr ?>;border-color:<?= $clr ?>;"><?= $lbl ?></span></td>
                            <td style="color:var(--muted);"><?= htmlspecialchars($r['admin_note'] ?: '-') ?></td>
                            <td style="color:var(--muted);"><?= htmlspecialchars(substr($r['created_at'], 0, 16)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color:var(--muted);text-align:center;padding:12px 0;">এখনো কোনো ব্যালেন্স রিকোয়েস্ট নেই।</p>
        <?php endif; ?>
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
            <p style="color:var(--muted);text-align:center;padding:20px 0;">এখনো কোনো বুকিং নেই। <a href="/booking.php">বুকিং করুন</a></p>
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

<div class="balance-modal" id="balance-modal" aria-hidden="true">
    <div class="balance-modal-panel">
        <div class="balance-modal-head">
            <h3>💳 ব্যালেন্স যোগ করার রিকোয়েস্ট</h3>
            <button type="button" class="balance-modal-close" id="close-balance-modal">বন্ধ করুন ✕</button>
        </div>
        <div class="balance-modal-body">
            <div class="balance-help">আপনি যে amount add করতে চান সেটি লিখে request পাঠান। অ্যাডমিন confirm করলে আপনার account balance update হবে।</div>
            <form method="post">
                <div class="form-grid" style="display:grid;grid-template-columns:1fr 2fr;gap:12px;">
                    <div class="field">
                        <label>পরিমাণ (৳)</label>
                        <input type="number" name="amount" min="1" step="1" placeholder="যেমন: 500" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>নোট (ঐচ্ছিক)</label>
                        <input type="text" name="note" maxlength="255" placeholder="bkash/নগদ ট্রানজেকশন রেফারেন্স" value="<?= htmlspecialchars($_POST['note'] ?? '') ?>">
                    </div>
                </div>
                <div style="margin-top:14px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
                    <button type="button" class="btn btn-outline" id="cancel-balance-modal">বাতিল</button>
                    <button type="submit" name="submit_balance_request" class="btn btn-gold">রিকোয়েস্ট পাঠান</button>
                </div>
            </form>
        </div>
    </div>
</div>

<nav class="mobile-fixed-bar" aria-label="Mobile quick navigation">
    <a href="/"><span class="mfb-icon" aria-hidden="true">🏠</span><span class="mfb-label">হোম</span></a>
    <a href="/#services"><span class="mfb-icon" aria-hidden="true">🛠</span><span class="mfb-label">সার্ভিস</span></a>
    <a href="/booking.php"><span class="mfb-icon" aria-hidden="true">📅</span><span class="mfb-label">বুকিং</span></a>
    <a href="/contact.php"><span class="mfb-icon" aria-hidden="true">☎</span><span class="mfb-label">যোগাযোগ</span></a>
    <a href="/profile.php"><span class="mfb-avatar" aria-hidden="true"><?= htmlspecialchars(mb_substr($user['name'], 0, 1)) ?></span><span class="mfb-label">প্রোফাইল</span></a>
</nav>

<script src="js/main.js?v=20260512-10"></script>
<script>
(function(){
    var modal = document.getElementById('balance-modal');
    var openBtn = document.getElementById('open-balance-modal');
    var closeBtn = document.getElementById('close-balance-modal');
    var cancelBtn = document.getElementById('cancel-balance-modal');
    if (!modal || !openBtn) return;

    function openModal() {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e){
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_balance_request']) && $err): ?>
    openModal();
    <?php endif; ?>
})();
</script>
</body>
</html>
