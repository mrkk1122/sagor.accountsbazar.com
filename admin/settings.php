<?php
$__title = 'সাইট সেটিংস';
require_once __DIR__ . '/includes/header.php';

$db  = get_db();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $fields = ['site_name','price_per_photo','free_photos_count','phone','whatsapp','email','location'];
    $upd = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    foreach ($fields as $f) {
        $val = trim($_POST[$f] ?? '');
        $upd->execute([$f, $val]);
    }
    $msg = 'সেটিংস সংরক্ষণ হয়েছে।';
}

// Load settings
$keys = ['site_name','price_per_photo','free_photos_count','phone','whatsapp','email','location'];
$s = [];
foreach ($keys as $k) $s[$k] = get_setting($k);
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="form-card">
    <h3>⚙️ সাইট সেটিংস পরিবর্তন করুন</h3>
    <form method="post">
        <div class="form-grid">
            <div class="field">
                <label>সাইটের নাম</label>
                <input type="text" name="site_name" value="<?= htmlspecialchars($s['site_name']) ?>" required>
            </div>
            <div class="field">
                <label>প্রতি ছবির মূল্য (৳)</label>
                <input type="number" name="price_per_photo" value="<?= htmlspecialchars($s['price_per_photo']) ?>" min="0" step="1" required>
            </div>
            <div class="field">
                <label>বিনামূল্যে ছবির সংখ্যা</label>
                <input type="number" name="free_photos_count" value="<?= htmlspecialchars($s['free_photos_count']) ?>" min="0" step="1" required>
            </div>
            <div class="field">
                <label>ফোন নম্বর</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($s['phone']) ?>">
            </div>
            <div class="field">
                <label>WhatsApp নম্বর</label>
                <input type="tel" name="whatsapp" value="<?= htmlspecialchars($s['whatsapp']) ?>">
            </div>
            <div class="field">
                <label>ইমেইল</label>
                <input type="email" name="email" value="<?= htmlspecialchars($s['email']) ?>">
            </div>
            <div class="field" style="grid-column:1/-1;">
                <label>অবস্থান (Location)</label>
                <input type="text" name="location" value="<?= htmlspecialchars($s['location']) ?>">
            </div>
        </div>
        <div style="margin-top:20px;">
            <button type="submit" name="save_settings" class="btn btn-gold">সেটিংস সংরক্ষণ করুন</button>
        </div>
    </form>
</div>

<div class="form-card">
    <h3>🔑 অ্যাডমিন পাসওয়ার্ড পরিবর্তন</h3>
    <?php
    $perr = '';
    $pmsg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_pass'])) {
        $old  = $_POST['old_pass'] ?? '';
        $new1 = $_POST['new_pass'] ?? '';
        $new2 = $_POST['new_pass2'] ?? '';
        $aid  = $_SESSION['admin_id'];
        $aRow = $db->prepare("SELECT password FROM users WHERE id=?");
        $aRow->execute([$aid]);
        $ahash = $aRow->fetchColumn();
        if (!password_verify($old, $ahash)) {
            $perr = 'বর্তমান পাসওয়ার্ড ভুল।';
        } elseif (strlen($new1) < 6) {
            $perr = 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষর হতে হবে।';
        } elseif ($new1 !== $new2) {
            $perr = 'নতুন পাসওয়ার্ড দুটি মিলছে না।';
        } else {
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new1, PASSWORD_DEFAULT), $aid]);
            $pmsg = 'পাসওয়ার্ড পরিবর্তন হয়েছে।';
        }
    }
    ?>
    <?php if ($pmsg): ?><div class="alert alert-success"><?= htmlspecialchars($pmsg) ?></div><?php endif; ?>
    <?php if ($perr): ?><div class="alert alert-error"><?= htmlspecialchars($perr) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-grid">
            <div class="field"><label>বর্তমান পাসওয়ার্ড</label><input type="password" name="old_pass" required></div>
            <div class="field"><label>নতুন পাসওয়ার্ড</label><input type="password" name="new_pass" placeholder="কমপক্ষে ৬ অক্ষর" required></div>
            <div class="field"><label>নতুন পাসওয়ার্ড নিশ্চিত</label><input type="password" name="new_pass2" required></div>
        </div>
        <div style="margin-top:16px;">
            <button type="submit" name="change_pass" class="btn btn-gold">পাসওয়ার্ড পরিবর্তন করুন</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
