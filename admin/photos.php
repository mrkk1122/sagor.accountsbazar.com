<?php
$__title = 'ছবি ম্যানেজমেন্ট';
require_once __DIR__ . '/includes/header.php';

$db  = get_db();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete_photo'])) {
        $pid  = (int)$_POST['photo_id'];
        $pRow = $db->prepare("SELECT filename FROM photos WHERE id=?");
        $pRow->execute([$pid]);
        $p = $pRow->fetch();
        if ($p) {
            // Delete DB records first, then file
            $db->prepare("DELETE FROM photo_downloads WHERE photo_id=?")->execute([$pid]);
            $db->prepare("DELETE FROM photos WHERE id=?")->execute([$pid]);
            $fp = __DIR__ . '/../uploads/photos/' . $p['filename'];
            if (file_exists($fp)) @unlink($fp);
            $msg = 'ছবি মুছে ফেলা হয়েছে।';
        }

    } elseif (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $title    = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        $price    = max(0, (float)($_POST['price'] ?? PHOTO_PRICE));
        $is_free  = isset($_POST['is_free']) ? 1 : 0;

        if (!$title) {
            $err = 'শিরোনাম আবশ্যক।';
        } else {
            $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
            $allowed_ext  = ['jpg','jpeg','png','gif','webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $_FILES['photo_file']['tmp_name']);
            finfo_close($finfo);
            $ext   = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));

            if (!in_array($mime, $allowed_mime, true) || !in_array($ext, $allowed_ext, true)) {
                $err = 'শুধু JPG, PNG, GIF, WebP আপলোড করা যাবে।';
            } else {
                $uploadDir = __DIR__ . '/../uploads/photos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $filename = bin2hex(random_bytes(12)) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $filename)) {
                    $db->prepare("INSERT INTO photos (title, filename, category, is_free, price) VALUES (?,?,?,?,?)")
                       ->execute([$title, $filename, $category, $is_free, $price]);
                    $msg = 'ছবি আপলোড হয়েছে।';
                } else {
                    $err = 'আপলোড ব্যর্থ।';
                }
            }
        }
    }
}

$photos = $db->query("SELECT * FROM photos ORDER BY created_at DESC")->fetchAll();
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="form-card">
    <h3>➕ নতুন ছবি আপলোড</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="field"><label>শিরোনাম *</label><input type="text" name="title" required placeholder="ছবির নাম"></div>
            <div class="field"><label>ক্যাটাগরি</label>
                <select name="category">
                    <option value="general">সাধারণ</option>
                    <option value="wedding">বিয়ে</option>
                    <option value="birthday">জন্মদিন</option>
                    <option value="outdoor">আউটডোর</option>
                    <option value="portrait">পোর্ট্রেট</option>
                    <option value="event">ইভেন্ট</option>
                    <option value="family">ফ্যামিলি</option>
                </select>
            </div>
            <div class="field"><label>মূল্য (৳)</label><input type="number" name="price" value="<?= PHOTO_PRICE ?>" min="0" step="1"></div>
            <div class="field"><label>ছবি ফাইল *</label><input type="file" name="photo_file" accept="image/jpeg,image/png,image/gif,image/webp" required></div>
        </div>
        <div style="margin-top:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;color:var(--muted);font-size:.88rem;">
                <input type="checkbox" name="is_free" value="1"> বিনামূল্যে ছবি হিসেবে চিহ্নিত করুন
            </label>
            <button type="submit" class="btn btn-gold" style="margin-left:auto;">আপলোড করুন</button>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-card-hdr"><h3>সকল ছবি (<?= count($photos) ?>)</h3></div>
    <div style="overflow-x:auto;">
    <table class="at">
        <thead><tr><th>থাম্ব</th><th>শিরোনাম</th><th>ক্যাটাগরি</th><th>মূল্য</th><th>বিনামূল্যে</th><th>আপলোড</th><th>অ্যাকশন</th></tr></thead>
        <tbody>
        <?php foreach ($photos as $p): ?>
        <tr>
            <td>
                <?php $fp = '../uploads/photos/' . $p['filename']; ?>
                <?php if (file_exists(__DIR__ . '/' . $fp)): ?>
                    <img src="<?= htmlspecialchars($fp) ?>" class="ph-thumb" alt="">
                <?php else: ?>
                    <span style="color:var(--muted);">📷</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td>৳<?= $p['price'] ?></td>
            <td><?= $p['is_free'] ? '<span class="badge b-confirmed">হ্যাঁ</span>' : '<span style="color:var(--muted)">না</span>' ?></td>
            <td style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars(substr($p['created_at'], 0, 10)) ?></td>
            <td>
                <form method="post" onsubmit="return confirm('ছবি মুছে ফেলবেন?')">
                    <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                    <button type="submit" name="delete_photo" class="btn btn-danger btn-sm">মুছুন</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$photos): ?><tr><td colspan="7" style="text-align:center;color:var(--muted);padding:20px;">কোনো ছবি নেই</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
