<?php
$__title = 'বুকিং ম্যানেজমেন্ট';
require_once __DIR__ . '/includes/header.php';

$db  = get_db();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bid    = (int)$_POST['booking_id'];
    $status = $_POST['status'] ?? '';
    $valid  = ['pending','confirmed','completed','cancelled'];
    if ($bid && in_array($status, $valid)) {
        $db->prepare("UPDATE bookings SET status=? WHERE id=?")->execute([$status, $bid]);
        $msg = 'স্ট্যাটাস আপডেট হয়েছে।';
    } else {
        $err = 'অবৈধ ইনপুট।';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_confirm'])) {
    $bid = (int)($_POST['booking_id'] ?? 0);
    if ($bid > 0) {
        $db->prepare("UPDATE bookings SET status='confirmed' WHERE id=?")->execute([$bid]);
        $msg = 'বুকিং কনফার্ম করা হয়েছে।';
    } else {
        $err = 'অবৈধ বুকিং আইডি।';
    }
}

$bookings = $db->query(
    "SELECT b.*, u.name as uname FROM bookings b LEFT JOIN users u ON b.user_id=u.id ORDER BY b.created_at DESC"
)->fetchAll();

$sl = ['pending'=>'b-pending','confirmed'=>'b-confirmed','completed'=>'b-completed','cancelled'=>'b-cancelled'];
$sn = ['pending'=>'অপেক্ষমান','confirmed'=>'নিশ্চিত','completed'=>'সম্পন্ন','cancelled'=>'বাতিল'];
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="table-card">
    <div class="table-card-hdr"><h3>সকল বুকিং (<?= count($bookings) ?>)</h3></div>
    <div class="table-scroll">
    <table class="at">
        <thead><tr><th>#</th><th>ইউজার</th><th>ফোন</th><th>সার্ভিস</th><th>তারিখ</th><th>সময়</th><th>বিস্তারিত</th><th>স্ট্যাটাস</th><th>পরিবর্তন</th><th>ক্লায়েন্ট ছবি</th></tr></thead>
        <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['uname'] ?? $b['name']) ?></td>
            <td><?= htmlspecialchars($b['phone']) ?></td>
            <td><?= htmlspecialchars($b['service']) ?></td>
            <td><?= htmlspecialchars($b['booking_date']) ?></td>
            <td><?= htmlspecialchars($b['booking_time']) ?></td>
            <td style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars(substr($b['details'] ?: '-', 0, 50)) ?></td>
            <td><span class="badge <?= $sl[$b['status']] ?? '' ?>"><?= $sn[$b['status']] ?? $b['status'] ?></span></td>
            <td>
                <form method="post" class="inline-form">
                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                    <select name="status" class="inline-select">
                        <?php foreach ($sn as $sv => $sl2): ?>
                            <option value="<?= $sv ?>" <?= $b['status']===$sv ? 'selected':'' ?>><?= $sl2 ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-gold btn-sm">আপডেট</button>
                    <?php if ($b['status'] !== 'confirmed'): ?>
                        <button type="submit" name="quick_confirm" class="btn btn-outline btn-sm">দ্রুত কনফার্ম</button>
                    <?php endif; ?>
                </form>
            </td>
            <td>
                <?php if (!empty($b['user_id'])): ?>
                    <a class="btn btn-outline btn-sm" href="photos.php?notify_user_id=<?= (int)$b['user_id'] ?>&booking_id=<?= (int)$b['id'] ?>">ছবি যোগ করুন</a>
                <?php else: ?>
                    <span style="color:var(--muted);font-size:.8rem;">ইউজার নেই</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?><tr><td colspan="10" style="text-align:center;color:var(--muted);padding:20px;">কোনো বুকিং নেই</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
