<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mailer.php';
start_session();

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$db = get_db();
$error = '';
$success = '';
$step = $_SESSION['reset_verified'] ?? false ? 'reset' : (!empty($_SESSION['reset_user_id']) ? 'verify' : 'request');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!validate_bd_phone($phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'সঠিক ফোন ও ইমেইল দিন।';
        } else {
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE phone=? AND email=? LIMIT 1");
            $stmt->execute([$phone, $email]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'এই তথ্য দিয়ে কোনো অ্যাকাউন্ট পাওয়া যায়নি।';
            } else {
                $otp = (string)random_int(100000, 999999);
                $otpHash = password_hash($otp, PASSWORD_DEFAULT);
                $expiresAt = date('Y-m-d H:i:s', time() + 600);

                $db->prepare("UPDATE password_resets SET used=1 WHERE user_id=? AND used=0")->execute([$user['id']]);
                $db->prepare("INSERT INTO password_resets (user_id, otp_hash, expires_at, used) VALUES (?,?,?,0)")
                   ->execute([$user['id'], $otpHash, $expiresAt]);

                $subject = 'Password Reset OTP - ' . SITE_NAME;
                $safeName = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
                $html = '<h3>হ্যালো ' . $safeName . ',</h3>'
                      . '<p>আপনার OTP: <strong style="font-size:22px;letter-spacing:2px;">' . $otp . '</strong></p>'
                      . '<p>এই OTP 10 মিনিটের জন্য বৈধ।</p>';
                $text = "আপনার Password Reset OTP: {$otp} (valid 10 minutes)";

                if (smtp_send_mail($user['email'], $subject, $html, $text)) {
                    $_SESSION['reset_user_id'] = (int)$user['id'];
                    $_SESSION['reset_verified'] = false;
                    $success = 'OTP পাঠানো হয়েছে। ইমেইল চেক করুন।';
                    $step = 'verify';
                } else {
                    $error = 'OTP পাঠানো যায়নি। পরে আবার চেষ্টা করুন।';
                }
            }
        }
    }

    if (isset($_POST['verify_otp'])) {
        $otp = trim($_POST['otp'] ?? '');
        $uid = (int)($_SESSION['reset_user_id'] ?? 0);

        if ($uid <= 0 || !preg_match('/^\d{6}$/', $otp)) {
            $error = 'সঠিক OTP দিন।';
        } else {
            $stmt = $db->prepare("SELECT id, otp_hash, expires_at FROM password_resets WHERE user_id=? AND used=0 ORDER BY id DESC LIMIT 1");
            $stmt->execute([$uid]);
            $row = $stmt->fetch();

            if (!$row) {
                $error = 'OTP পাওয়া যায়নি। আবার OTP নিন।';
                $step = 'request';
            } elseif (strtotime($row['expires_at']) < time()) {
                $db->prepare("UPDATE password_resets SET used=1 WHERE id=?")->execute([$row['id']]);
                $error = 'OTP মেয়াদ শেষ হয়েছে। নতুন OTP নিন।';
            } elseif (!password_verify($otp, $row['otp_hash'])) {
                $error = 'OTP ভুল হয়েছে।';
            } else {
                $db->prepare("UPDATE password_resets SET used=1 WHERE id=?")->execute([$row['id']]);
                $_SESSION['reset_verified'] = true;
                $success = 'OTP যাচাই সফল। নতুন পাসওয়ার্ড সেট করুন।';
                $step = 'reset';
            }
        }
    }

    if (isset($_POST['reset_password'])) {
        $uid = (int)($_SESSION['reset_user_id'] ?? 0);
        $verified = !empty($_SESSION['reset_verified']);
        $p1 = $_POST['password'] ?? '';
        $p2 = $_POST['password2'] ?? '';

        if ($uid <= 0 || !$verified) {
            $error = 'অনুগ্রহ করে প্রথমে OTP যাচাই করুন।';
            $step = 'request';
        } elseif (strlen($p1) < 6) {
            $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
            $step = 'reset';
        } elseif ($p1 !== $p2) {
            $error = 'পাসওয়ার্ড দুটি মিলছে না।';
            $step = 'reset';
        } else {
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($p1, PASSWORD_DEFAULT), $uid]);
            unset($_SESSION['reset_user_id'], $_SESSION['reset_verified']);
            header('Location: login.php?reset=ok');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পাসওয়ার্ড রিসেট | <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="stylesheet" href="css/style.css?v=20260512-12">
    <style>
        .auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;}
        .auth-card{background:var(--dark2);border:1px solid rgba(212,175,55,.2);border-radius:20px;padding:40px;width:100%;max-width:470px;}
        .auth-card h2{color:var(--white);margin-bottom:6px;font-size:1.6rem;}
        .auth-card .sub{color:var(--muted);margin-bottom:24px;font-size:.9rem;}
        .auth-footer{text-align:center;margin-top:20px;color:var(--muted);font-size:.9rem;}
        @media (max-width:560px){
            .auth-wrap{padding:24px 12px;align-items:flex-start;}
            .auth-card{padding:24px 16px;border-radius:14px;}
            .auth-card h2{font-size:1.3rem;}
        }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <h2>পাসওয়ার্ড ভুলে গেছেন?</h2>
        <p class="sub">OTP এর মাধ্যমে আপনার পাসওয়ার্ড রিসেট করুন</p>

        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <?php if ($step === 'request'): ?>
        <form method="post">
            <div class="field">
                <label for="phone">ফোন নম্বর</label>
                <input type="tel" id="phone" name="phone" required placeholder="01XXXXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="email">ইমেইল</label>
                <input type="email" id="email" name="email" required placeholder="you@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" name="send_otp" class="btn btn-gold" style="width:100%;margin-top:8px;">OTP পাঠান</button>
        </form>
        <?php elseif ($step === 'verify'): ?>
        <form method="post">
            <div class="field">
                <label for="otp">৬ সংখ্যার OTP</label>
                <input type="text" id="otp" name="otp" required maxlength="6" pattern="\d{6}" placeholder="123456">
            </div>
            <button type="submit" name="verify_otp" class="btn btn-gold" style="width:100%;margin-top:8px;">OTP যাচাই করুন</button>
        </form>
        <?php else: ?>
        <form method="post">
            <div class="field">
                <label for="password">নতুন পাসওয়ার্ড</label>
                <input type="password" id="password" name="password" required placeholder="কমপক্ষে ৬ অক্ষর">
            </div>
            <div class="field">
                <label for="password2">পাসওয়ার্ড নিশ্চিত</label>
                <input type="password" id="password2" name="password2" required>
            </div>
            <button type="submit" name="reset_password" class="btn btn-gold" style="width:100%;margin-top:8px;">পাসওয়ার্ড আপডেট করুন</button>
        </form>
        <?php endif; ?>

        <div class="auth-footer">
            <a href="login.php">লগইন পেজে ফিরুন</a>
        </div>
    </div>
</div>

<nav class="mobile-fixed-bar" aria-label="Mobile quick navigation">
    <a href="/"><span class="mfb-icon" aria-hidden="true">🏠</span><span class="mfb-label">হোম</span></a>
    <a href="/#services"><span class="mfb-icon" aria-hidden="true">🛠</span><span class="mfb-label">সার্ভিস</span></a>
    <a href="/booking.php"><span class="mfb-icon" aria-hidden="true">📅</span><span class="mfb-label">বুকিং</span></a>
    <a href="/contact.php"><span class="mfb-icon" aria-hidden="true">☎</span><span class="mfb-label">যোগাযোগ</span></a>
    <a href="/login.php"><span class="mfb-icon" aria-hidden="true">🔑</span><span class="mfb-label">লগইন</span></a>
</nav>

<script src="js/main.js?v=20260512-12"></script>
</body>
</html>
