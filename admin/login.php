<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();

// Already logged in as admin
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

function read_default_admin_credentials(): ?array {
    $path = __DIR__ . '/../db/admin_credentials.txt';
    if (!is_file($path)) return null;
    $content = @file_get_contents($path);
    if ($content === false) return null;

    $phone = null;
    $pass  = null;

    if (preg_match('/^Phone:\s*(.+)$/mi', $content, $m)) {
        $phone = trim($m[1]);
    }
    if (preg_match('/^Password:\s*(.+)$/mi', $content, $m)) {
        $pass = trim($m[1]);
    }

    if (!$phone || !$pass) return null;
    return ['phone' => $phone, 'password' => $pass];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$phone || !$pass) {
        $error = 'ফোন নম্বর ও পাসওয়ার্ড আবশ্যক।';
    } else {
        $stmt = get_db()->prepare("SELECT * FROM users WHERE phone=? AND is_admin=1");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            session_regenerate_id(true);
            header('Location: index.php');
            exit;
        } else {
            // Self-heal fallback: if provided credentials match admin_credentials.txt,
            // sync admin hash in the current active DB and allow login.
            $defaultCred = read_default_admin_credentials();
            if ($defaultCred && $phone === $defaultCred['phone'] && hash_equals($defaultCred['password'], $pass)) {
                $db = get_db();
                $hash = password_hash($pass, PASSWORD_DEFAULT);

                $find = $db->prepare("SELECT * FROM users WHERE phone=? LIMIT 1");
                $find->execute([$phone]);
                $existing = $find->fetch();

                if ($existing) {
                    $db->prepare("UPDATE users SET is_admin=1, password=? WHERE id=?")
                       ->execute([$hash, $existing['id']]);
                    $_SESSION['admin_id'] = $existing['id'];
                } else {
                    $db->prepare("INSERT INTO users (name, phone, email, password, is_admin) VALUES (?,?,?,?,1)")
                       ->execute(['Admin', $phone, '', $hash]);
                    $_SESSION['admin_id'] = $db->lastInsertId();
                }

                session_regenerate_id(true);
                header('Location: index.php');
                exit;
            }

            $error = 'ফোন নম্বর বা পাসওয়ার্ড ভুল অথবা আপনি অ্যাডমিন নন।';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন | <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4af37">
    <link rel="stylesheet" href="../css/admin.css?v=20260512-3">
    <style>
        body{display:block;background:var(--dark);}
        .login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
        .login-card{background:var(--dark2);border:1px solid rgba(212,175,55,.2);border-radius:20px;padding:40px;width:100%;max-width:420px;}
        .login-card h2{color:#fff;margin-bottom:6px;}
        .login-card .sub{color:var(--muted);font-size:.88rem;margin-bottom:24px;}
        @media (max-width:560px){
            .login-wrap{padding:16px 12px;align-items:flex-start;}
            .login-card{padding:22px 16px;border-radius:14px;}
            .login-card h2{font-size:1.25rem;}
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <h2>⚙️ অ্যাডমিন লগইন</h2>
        <p class="sub"><?= htmlspecialchars(SITE_NAME) ?> কন্ট্রোল প্যানেল</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="field" style="margin-bottom:14px;">
                <label for="phone">অ্যাডমিন ফোন নম্বর</label>
                <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required autofocus>
            </div>
            <div class="field" style="margin-bottom:20px;">
                <label for="password">পাসওয়ার্ড</label>
                <input type="password" id="password" name="password" placeholder="পাসওয়ার্ড" required>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;">প্রবেশ করুন</button>
        </form>
        <div style="margin-top:16px;text-align:center;font-size:.82rem;color:var(--muted);">
            <a href="/">← সাইটে ফিরুন</a>
        </div>
    </div>
</div>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(function(){});
}
</script>
</body>
</html>
