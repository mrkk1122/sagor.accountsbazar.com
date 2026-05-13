<?php
$__title = 'ছবি ম্যানেজমেন্ট';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/mailer.php';

$db  = get_db();
$msg = '';
$err = '';
$currentAdminId = (int)($_SESSION['admin_id'] ?? 0);
$preNotifyUserId = (int)($_GET['notify_user_id'] ?? 0);
$preBookingId = (int)($_GET['booking_id'] ?? 0);
$preTitle = $preBookingId > 0 ? ('Booking #' . $preBookingId . ' Photo') : '';

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

    } elseif (isset($_FILES['photo_file'])) {
        // Handle file upload with detailed error checking
        $fileError = $_FILES['photo_file']['error'];
        
        if ($fileError !== UPLOAD_ERR_OK) {
            // Handle PHP upload errors
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'ফাইল PHP সীমা অতিক্রম।',
                UPLOAD_ERR_FORM_SIZE => 'ফাইল ফর্ম সীমা অতিক্রম।',
                UPLOAD_ERR_PARTIAL => 'ফাইল আংশিক আপলোড।',
                UPLOAD_ERR_NO_FILE => 'কোনো ফাইল নির্বাচিত নেই।',
                UPLOAD_ERR_NO_TMP_DIR => 'টেম্প ডিরেক্টরি সমস্যা।',
                UPLOAD_ERR_CANT_WRITE => 'ডিস্কে লিখতে ব্যর্থ।',
                UPLOAD_ERR_EXTENSION => 'এক্সটেনশন ব্লক করা।'
            ];
            $err = $errors[$fileError] ?? ('আপলোড ত্রুটি #' . $fileError);
        } else {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? 'general');
            $price = max(0, (float)($_POST['price'] ?? PHOTO_PRICE));
            $is_free = isset($_POST['is_free']) ? 1 : 0;
            $notify_user_id = (int)($_POST['notify_user_id'] ?? 0);
            $booking_id = (int)($_POST['booking_id'] ?? 0);

            if (!$title) {
                $err = 'শিরোনাম আবশ্যক।';
            } else {
                $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
                $allowed_ext = ['jpg','jpeg','png','gif','webp'];
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['photo_file']['tmp_name']);
                finfo_close($finfo);
                $ext = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));

                if (!in_array($mime, $allowed_mime, true) || !in_array($ext, $allowed_ext, true)) {
                    $err = 'শুধু JPG, PNG, GIF, WebP গ্রহণযোগ্য।';
                } else {
                    $uploadDir = __DIR__ . '/../uploads/photos/';
                    
                    // Ensure directory exists with proper permissions
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0777, true)) {
                            $err = 'আপলোড ডিরেক্টরি তৈরি ব্যর্থ।';
                        }
                    }
                    
                    if (!$err && is_dir($uploadDir)) {
                        $filename = bin2hex(random_bytes(12)) . '.' . $ext;
                        $fullPath = $uploadDir . $filename;
                        
                        // Move uploaded file
                        if (!move_uploaded_file($_FILES['photo_file']['tmp_name'], $fullPath)) {
                            $err = 'ফাইল সংরক্ষণ ব্যর্থ। অনুমতি যাচাই করুন।';
                        } else if (!file_exists($fullPath)) {
                            $err = 'ফাইল সংরক্ষিত কিন্তু যাচাইকৃত নয়।';
                        } else {
                            // File exists, now try DB insert
                            try {
                                $stmt = $db->prepare("INSERT INTO photos (user_id, booking_id, title, filename, category, is_free, price) VALUES (?,?,?,?,?,?,?)");
                                $success = $stmt->execute([
                                    $notify_user_id > 0 ? $notify_user_id : null,
                                    $booking_id > 0 ? $booking_id : null,
                                    $title,
                                    $filename,
                                    $category,
                                    $is_free,
                                    $price
                                ]);
                                
                                if (!$success) {
                                    $err = 'ডাটাবেস ইনসার্ট ব্যর্থ।';
                                    @unlink($fullPath);
                                } else {
                                    $msg = 'ছবি সফলভাবে আপলোড হয়েছে।';
                                    
                                    // Send notification if user selected
                                    if ($notify_user_id > 0) {
                                        $uStmt = $db->prepare("SELECT name, email FROM users WHERE id=? AND is_admin=0 LIMIT 1");
                                        $uStmt->execute([$notify_user_id]);
                                        $targetUser = $uStmt->fetch();
                                        if ($targetUser && !empty($targetUser['email']) && filter_var($targetUser['email'], FILTER_VALIDATE_EMAIL)) {
                                            $safeUserName = htmlspecialchars($targetUser['name'], ENT_QUOTES, 'UTF-8');
                                            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                                            $subject = 'নতুন ছবি আপলোড হয়েছে - ' . SITE_NAME;
                                            $html = '<h3>হ্যালো ' . $safeUserName . ',</h3>'
                                                  . '<p>আপনার জন্য নতুন একটি ছবি আপলোড করা হয়েছে।</p>'
                                                  . '<p><strong>ছবির শিরোনাম:</strong> ' . $safeTitle . '</p>'
                                                  . '<p>দেখতে ভিজিট করুন: <a href="https://sagor.accountsbazar.com/profile.php">প্রোফাইল</a></p>';
                                            $text = "আপনার জন্য নতুন ছবি আপলোড করা হয়েছে: {$title}. প্রোফাইল চেক করুন।";
                                            smtp_send_mail($targetUser['email'], $subject, $html, $text);
                                            $msg .= ' নির্বাচিত ইউজারের ইমেইলে নোটিফিকেশন পাঠানো হয়েছে।';
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                $err = 'DB ত্রুটি: ' . $e->getMessage();
                                @unlink($fullPath);
                            }
                        }
                    }
                }
            }
        }
    }
}

$photos = $db->query("SELECT * FROM photos ORDER BY created_at DESC")->fetchAll();
$usersForNotify = $db->query("SELECT id, name, email FROM users WHERE is_admin=0 ORDER BY name ASC")->fetchAll();
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="form-card">
    <h3>➕ নতুন ছবি আপলোড</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="field"><label>শিরোনাম *</label><input type="text" name="title" value="<?= htmlspecialchars($preTitle) ?>" required placeholder="ছবির নাম"></div>
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
            <div class="field"><label>ইউজার সিলেক্ট (ইমেইল নোটিফিকেশন)</label>
                <select name="notify_user_id">
                    <option value="0">কাউকে পাঠাবেন না</option>
                    <?php foreach ($usersForNotify as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= $preNotifyUserId === (int)$u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?> <?= !empty($u['email']) ? '(' . htmlspecialchars($u['email']) . ')' : '(ইমেইল নেই)' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="booking_id" value="<?= (int)$preBookingId ?>">
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
