<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
start_session();
require_login('/login.php');

$user    = current_user();
$db      = get_db();
$photoId = (int)($_GET['photo_id'] ?? 0);

if (!$photoId) {
    header('Location: profile.php');
    exit;
}

$pStmt = $db->prepare("SELECT * FROM photos WHERE id=?");
$pStmt->execute([$photoId]);
$photo = $pStmt->fetch();

if (!$photo) {
    header('Location: profile.php?err=notfound');
    exit;
}

// Check already downloaded
$dlChk = $db->prepare("SELECT id FROM photo_downloads WHERE user_id=? AND photo_id=?");
$dlChk->execute([$user['id'], $photoId]);
$alreadyDl = $dlChk->fetch();

if (!$alreadyDl) {
    $isFreePhoto = (bool)$photo['is_free'];

    if ($isFreePhoto) {
        $db->prepare("INSERT INTO photo_downloads (user_id, photo_id, amount_paid) VALUES (?,?,0)")
           ->execute([$user['id'], $photoId]);
    } else {
        $price = (float)$photo['price'];
        // Atomic balance deduction
        $db->beginTransaction();
        try {
            $uStmt = $db->prepare("SELECT balance FROM users WHERE id=?");
            $uStmt->execute([$user['id']]);
            $balance = (float)$uStmt->fetchColumn();

            if ($balance < $price) {
                $db->rollBack();
                header('Location: profile.php?err=nobalance');
                exit;
            }

            $db->prepare("UPDATE users SET balance = balance - ? WHERE id=?")
               ->execute([$price, $user['id']]);
            $db->prepare("INSERT INTO photo_downloads (user_id, photo_id, amount_paid) VALUES (?,?,?)")
               ->execute([$user['id'], $photoId, $price]);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            header('Location: profile.php?err=error');
            exit;
        }
    }
}

// Serve the file
$filePath = __DIR__ . '/uploads/photos/' . $photo['filename'];
if (!file_exists($filePath)) {
    header('Location: profile.php?err=filemissing');
    exit;
}

$ext      = strtolower(pathinfo($photo['filename'], PATHINFO_EXTENSION));
$mimeMap  = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'];
$mime     = $mimeMap[$ext] ?? 'application/octet-stream';
// Strip all non-alphanumeric chars (no dots or slashes) to prevent path traversal in Content-Disposition
$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $photo['title']) . '.' . $ext;

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $safeName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-store');
readfile($filePath);
exit;
