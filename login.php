<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
start_session();

// Already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$phone || !$pass) {
        $error = 'ফোন নম্বর ও পাসওয়ার্ড দিন।';
    } elseif (!validate_bd_phone($phone)) {
        $error = 'সঠিক বাংলাদেশি ফোন নম্বর দিন।';
    } else {
        $stmt = get_db()->prepare("SELECT * FROM users WHERE phone=?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            session_regenerate_id(true);
            header('Location: profile.php');
            exit;
        } else {
            $error = 'ফোন নম্বর বা পাসওয়ার্ড ভুল।';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন | <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="css/style.css?v=20260512-8">
    <style>
        .auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;}
        .auth-card{background:var(--dark2);border:1px solid rgba(212,175,55,.2);border-radius:20px;padding:40px;width:100%;max-width:440px;}
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
        <h2>লগইন করুন</h2>
        <p class="sub">আপনার অ্যাকাউন্টে প্রবেশ করুন</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="phone">মোবাইল নম্বর</label>
                <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required autofocus>
            </div>
            <div class="field">
                <label for="password">পাসওয়ার্ড</label>
                <input type="password" id="password" name="password" placeholder="পাসওয়ার্ড লিখুন" required>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;margin-top:8px;">লগইন করুন</button>
        </form>

        <div class="auth-footer">
            অ্যাকাউন্ট নেই? <a href="register.php">নিবন্ধন করুন</a>
        </div>
    </div>
</div>
</body>
</html>
