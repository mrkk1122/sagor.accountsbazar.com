<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
start_session();

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (!$name || !$phone || !$pass) {
        $error = 'নাম, ফোন নম্বর ও পাসওয়ার্ড আবশ্যক।';
    } elseif (!validate_bd_phone($phone)) {
        $error = 'সঠিক বাংলাদেশি ফোন নম্বর দিন (01XXXXXXXXX)।';
    } elseif (strlen($pass) < 6) {
        $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
    } elseif ($pass !== $pass2) {
        $error = 'পাসওয়ার্ড দুটি মিলছে না।';
    } else {
        $db = get_db();
        $chk = $db->prepare("SELECT id FROM users WHERE phone=?");
        $chk->execute([$phone]);
        if ($chk->fetch()) {
            $error = 'এই ফোন নম্বরে ইতিমধ্যে অ্যাকাউন্ট আছে।';
        } else {
            $db->prepare("INSERT INTO users (name, phone, email, password) VALUES (?,?,?,?)")
               ->execute([$name, $phone, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            $uid = $db->lastInsertId();
            $_SESSION['user_id'] = $uid;
            session_regenerate_id(true);
            header('Location: profile.php');
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
    <title>নিবন্ধন | <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="css/style.css?v=20260512-12">
    <style>
        .auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;}
        .auth-card{background:var(--dark2);border:1px solid rgba(212,175,55,.2);border-radius:20px;padding:40px;width:100%;max-width:480px;}
        .auth-card h2{color:var(--white);margin-bottom:6px;font-size:1.6rem;}
        .auth-card .sub{color:var(--muted);margin-bottom:28px;font-size:.9rem;}
        .auth-logo{text-align:center;margin-bottom:28px;}
        .auth-logo a{font-size:1.4rem;font-weight:700;color:var(--gold);}
        .auth-footer{text-align:center;margin-top:20px;color:var(--muted);font-size:.9rem;}
        .auth-footer a{color:var(--gold);}
        @media (max-width:560px){
            .auth-wrap{padding:24px 12px;align-items:flex-start;}
            .auth-card{padding:24px 16px;border-radius:14px;}
            .auth-card h2{font-size:1.35rem;}
            .auth-logo{margin-bottom:20px;}
            .auth-logo a{font-size:1.15rem;}
        }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <a href="/"><?= htmlspecialchars(PHOTOGRAPHER_NAME) ?> Photography</a>
        </div>
        <h2>নিবন্ধন করুন</h2>
        <p class="sub">নতুন অ্যাকাউন্ট তৈরি করুন</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="name">পূর্ণ নাম <span style="color:#ef4444">*</span></label>
                <input type="text" id="name" name="name" placeholder="আপনার নাম"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autofocus>
            </div>
            <div class="field">
                <label for="phone">মোবাইল নম্বর <span style="color:#ef4444">*</span></label>
                <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label for="email">ইমেইল (ঐচ্ছিক)</label>
                <input type="email" id="email" name="email" placeholder="example@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="field">
                    <label for="password">পাসওয়ার্ড <span style="color:#ef4444">*</span></label>
                    <input type="password" id="password" name="password" placeholder="কমপক্ষে ৬ অক্ষর" required>
                </div>
                <div class="field">
                    <label for="password2">পাসওয়ার্ড নিশ্চিত করুন <span style="color:#ef4444">*</span></label>
                    <input type="password" id="password2" name="password2" placeholder="পুনরায় লিখুন" required>
                </div>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;margin-top:8px;">অ্যাকাউন্ট তৈরি করুন</button>
        </form>

        <div class="auth-footer">
            ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="login.php">লগইন করুন</a>
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

<script src="js/main.js?v=20260512-10"></script>
</body>
</html>
