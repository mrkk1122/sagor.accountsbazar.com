<?php
$__title = 'ব্যালেন্স রিকোয়েস্ট';
require_once __DIR__ . '/includes/header.php';

$db  = get_db();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $rid = (int)($_POST['request_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $adminNote = trim($_POST['admin_note'] ?? '');

    if (!$rid || !in_array($action, ['confirm', 'reject'], true)) {
        $err = 'অবৈধ রিকোয়েস্ট।';
    } else {
        $stmt = $db->prepare("SELECT * FROM balance_requests WHERE id=? LIMIT 1");
        $stmt->execute([$rid]);
        $req = $stmt->fetch();

        if (!$req) {
            $err = 'রিকোয়েস্ট পাওয়া যায়নি।';
        } elseif ($req['status'] !== 'pending') {
            $err = 'এই রিকোয়েস্ট আগে থেকেই প্রক্রিয়া করা হয়েছে।';
        } else {
            $db->beginTransaction();
            try {
                if ($action === 'confirm') {
                    $db->prepare("UPDATE users SET balance = balance + ? WHERE id=? AND is_admin=0")
                       ->execute([(float)$req['amount'], (int)$req['user_id']]);

                    $db->prepare("UPDATE balance_requests SET status='confirmed', admin_note=?, confirmed_by=?, confirmed_at=CURRENT_TIMESTAMP WHERE id=?")
                       ->execute([$adminNote, (int)$_SESSION['admin_id'], $rid]);

                    $msg = 'রিকোয়েস্ট কনফার্ম হয়েছে এবং ইউজারের ব্যালেন্সে টাকা যোগ হয়েছে।';
                } else {
                    $db->prepare("UPDATE balance_requests SET status='rejected', admin_note=?, confirmed_by=?, confirmed_at=CURRENT_TIMESTAMP WHERE id=?")
                       ->execute([$adminNote, (int)$_SESSION['admin_id'], $rid]);

                    $msg = 'রিকোয়েস্ট বাতিল করা হয়েছে।';
                }
                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $err = 'রিকোয়েস্ট প্রসেস করা যায়নি।';
            }
        }
    }
}

$requests = $db->query(
    "SELECT r.*, u.name AS user_name, u.phone AS user_phone FROM balance_requests r\n"
    . "LEFT JOIN users u ON u.id = r.user_id\n"
    . "ORDER BY CASE r.status WHEN 'pending' THEN 0 WHEN 'confirmed' THEN 1 ELSE 2 END, r.created_at DESC"
)->fetchAll();

$statusClass = ['pending' => 'b-pending', 'confirmed' => 'b-confirmed', 'rejected' => 'b-cancelled'];
$statusName = ['pending' => 'অপেক্ষমান', 'confirmed' => 'কনফার্ম', 'rejected' => 'বাতিল'];
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="table-card">
    <div class="table-card-hdr"><h3>ব্যালেন্স রিকোয়েস্ট (<?= count($requests) ?>)</h3></div>
    <div style="overflow-x:auto;">
    <table class="at">
        <thead>
            <tr>
                <th>#</th>
                <th>ইউজার</th>
                <th>ফোন</th>
                <th>পরিমাণ</th>
                <th>নোট</th>
                <th>স্ট্যাটাস</th>
                <th>অ্যাকশন</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['user_name'] ?: 'Unknown') ?></td>
                <td><?= htmlspecialchars($r['user_phone'] ?: '-') ?></td>
                <td style="color:var(--gold);font-weight:700;">৳<?= number_format((float)$r['amount'], 0) ?></td>
                <td style="font-size:.82rem;color:var(--muted);"><?= htmlspecialchars($r['note'] ?: '-') ?></td>
                <td><span class="badge <?= $statusClass[$r['status']] ?? '' ?>"><?= $statusName[$r['status']] ?? $r['status'] ?></span></td>
                <td>
                    <?php if ($r['status'] === 'pending'): ?>
                        <form method="post" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <input type="text" name="admin_note" placeholder="admin note" style="min-width:140px;background:var(--dark3);color:var(--light);border:1px solid rgba(255,255,255,.1);border-radius:6px;padding:6px 8px;font-size:.8rem;">
                            <button type="submit" name="action" value="confirm" class="btn btn-gold btn-sm">কনফার্ম</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">বাতিল</button>
                        </form>
                    <?php else: ?>
                        <span style="color:var(--muted);font-size:.8rem;">প্রসেসড<br><?= htmlspecialchars($r['admin_note'] ?: '-') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$requests): ?><tr><td colspan="7" style="text-align:center;color:var(--muted);padding:20px;">কোনো রিকোয়েস্ট নেই</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
