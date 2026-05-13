<?php
$__title = 'ইউজার ম্যানেজমেন্ট';
require_once __DIR__ . '/includes/header.php';

$db  = get_db();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_balance'])) {
        $uid    = (int)$_POST['user_id'];
        $amount = (float)$_POST['amount'];
        if ($uid && $amount > 0) {
            $db->prepare("UPDATE users SET balance = balance + ? WHERE id=? AND is_admin=0")->execute([$amount, $uid]);
            $msg = 'ব্যালেন্স যোগ করা হয়েছে।';
        } else {
            $err = 'অবৈধ পরিমাণ।';
        }
    } elseif (isset($_POST['delete_user'])) {
        $uid = (int)$_POST['user_id'];
        if ($uid) {
            $db->prepare("DELETE FROM photo_downloads WHERE user_id=?")->execute([$uid]);
            $db->prepare("DELETE FROM bookings WHERE user_id=?")->execute([$uid]);
            $db->prepare("DELETE FROM users WHERE id=? AND is_admin=0")->execute([$uid]);
            $msg = 'ইউজার মুছে ফেলা হয়েছে।';
        }
    }
}

$users = $db->query(
    "SELECT *, (SELECT COUNT(*) FROM bookings WHERE user_id=users.id) as bcount FROM users ORDER BY created_at DESC"
)->fetchAll();
?>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="table-card">
    <div class="table-card-hdr"><h3>সকল ইউজার (<?= count($users) ?>)</h3></div>
    <div class="table-scroll">
    <table class="at">
        <thead><tr><th>#</th><th>নাম</th><th>ফোন</th><th>ইমেইল</th><th>ব্যালেন্স</th><th>বুকিং</th><th>ধরন</th><th>যোগদান</th><th>অ্যাকশন</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['phone']) ?></td>
            <td><?= htmlspecialchars($u['email'] ?: '-') ?></td>
            <td style="color:var(--gold);font-weight:600;">৳<?= number_format($u['balance'], 0) ?></td>
            <td><?= $u['bcount'] ?></td>
            <td><?= $u['is_admin'] ? '<span class="badge b-admin">অ্যাডমিন</span>' : '<span class="badge b-confirmed">ইউজার</span>' ?></td>
            <td style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></td>
            <td class="cell-actions">
                <?php if (!$u['is_admin']): ?>
                <!-- Add Balance -->
                <form method="post" class="inline-form inline-form-compact">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="number" name="amount" min="1" step="1" placeholder="৳" class="inline-input">
                    <button type="submit" name="add_balance" class="btn btn-gold btn-sm">যোগ করুন</button>
                </form>
                <!-- Delete -->
                <form method="post" onsubmit="return confirm('ইউজার মুছে ফেলবেন?')">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">মুছুন</button>
                </form>
                <?php else: ?>
                    <span style="color:var(--muted);font-size:.8rem;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?><tr><td colspan="9" style="text-align:center;color:var(--muted);padding:20px;">কোনো ইউজার নেই</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
