<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
$__showHero = false;

start_session();
$__user = current_user();
$helpMsg = '';
$helpErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_help_request'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '' || $message === '') {
        $helpErr = 'নাম, ফোন এবং মেসেজ আবশ্যক।';
    } elseif (!validate_bd_phone($phone)) {
        $helpErr = 'সঠিক বাংলাদেশি ফোন নম্বর দিন।';
    } else {
        get_db()->prepare(
            "INSERT INTO help_requests (user_id, name, phone, email, message) VALUES (?,?,?,?,?)"
        )->execute([
            $__user['id'] ?? null,
            $name,
            $phone,
            $email,
            $message,
        ]);
        $helpMsg = 'আপনার হেল্প রিকোয়েস্ট পাঠানো হয়েছে। খুব দ্রুত যোগাযোগ করা হবে।';
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="contact-hero">
    <div class="container contact-hero-inner">
        <div class="contact-hero-text">
            <span class="label">Direct Contact</span>
            <h1>আমাদের সাথে সরাসরি যোগাযোগ করুন</h1>
            <p>প্রফেশনাল টিম, দ্রুত রেসপন্স এবং নির্ভরযোগ্য সাপোর্টের জন্য এখনই কল বা WhatsApp করুন।</p>
        </div>
        <div class="contact-hero-photo">
            <img src="https://scontent.fdac177-2.fna.fbcdn.net/v/t39.30808-6/672689394_2931369430535398_6126606788332305765_n.jpg?stp=cp6_dst-jpg_tt6&_nc_cat=104&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeGzFGWqRB9eh-NFugdpDqbA0K8L37UBWjPQrwvftQFaM2MVUoroHrposOmBI_Ec8638qHLsVjEgZvrqDaTYIY_b&_nc_ohc=gOUjVpkJKbcQ7kNvwGxgy_d&_nc_oc=AdpauyKEZBoFCji-baZVE91W1EtCjHeJ0mocYujV5eFmMVa-aJxGzLEHwVpWddAD6L8&_nc_zt=23&_nc_ht=scontent.fdac177-2.fna&_nc_gid=EiECtisFV9iY_ou2oiK3-g&_nc_ss=7b2a8&oh=00_Af4fl04lUMdTgyMcgb0eErrVl_3XdeoCp9_E2_4hT_Mgkw&oe=6A098CC6" alt="Cameraman with camera gear" style="display:block; margin-left:auto; margin-right:auto; max-width:320px; width:100%; border-radius:12px; box-shadow:0 4px 24px #0002;" loading="eager">
            <span class="contact-hero-badge">Camera Team Ready</span>
        </div>
    </div>
</section>

<section id="contact" style="padding-top:70px;">
    <div class="container">
        <div class="section-title">
            <span class="label">Contact</span>
            <h2>যোগাযোগ করুন</h2>
            <p>কল, WhatsApp অথবা ইমেইলে সহজেই যোগাযোগ করুন। দ্রুত রেসপন্স দেওয়া হয়।</p>
        </div>

        <div class="contact-grid">
            <a href="tel:<?= preg_replace('/[^0-9]/', '', PHONE) ?>" class="contact-card" style="text-decoration:none;cursor:pointer;">
                <div class="c-icon">📞</div>
                <h4>ফোন কল</h4>
                <p><?= PHONE ?></p>
                <p style="font-size:.8rem;color:var(--muted);margin-top:6px;font-weight:600;">সরাসরি কল করুন</p>
            </a>
            <?php 
                $waNum = preg_replace('/[^0-9]/', '', WHATSAPP);
                if ($waNum !== '' && strlen($waNum) > 0) {
                    if ($waNum[0] === '0') $waNum = '88' . substr($waNum, 1);
                    elseif (strpos($waNum, '88') !== 0) $waNum = '88' . $waNum;
                }
                $waUrl = ($waNum !== '') ? ('https://wa.me/' . $waNum) : '#';
            ?>
            <a href="<?= htmlspecialchars($waUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="contact-card" style="text-decoration:none;cursor:pointer;">
                <div class="c-icon">💬</div>
                <h4>WhatsApp</h4>
                <p><?= WHATSAPP ?></p>
                <p style="font-size:.8rem;color:var(--muted);margin-top:6px;font-weight:600;">সরাসরি চ্যাট করুন</p>
            </a>
            <a href="mailto:<?= htmlspecialchars(EMAIL, ENT_QUOTES, 'UTF-8') ?>" class="contact-card" style="text-decoration:none;cursor:pointer;">
                <div class="c-icon">📧</div>
                <h4>ইমেইল</h4>
                <p><?= EMAIL ?></p>
                <p style="font-size:.8rem;color:var(--muted);margin-top:6px;font-weight:600;">ইমেইল পাঠান</p>
            </a>
            <div class="contact-card">
                <div class="c-icon">📍</div>
                <h4>লোকেশন</h4>
                <p><?= LOCATION ?></p>
            </div>
        </div>

        <div class="booking-form-wrap" style="margin-top:26px;">
            <h3 style="margin:0 0 14px;color:var(--white);">হেল্প / সাপোর্ট রিকোয়েস্ট</h3>

            <?php if ($helpMsg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($helpMsg) ?></div>
            <?php endif; ?>
            <?php if ($helpErr): ?>
                <div class="alert alert-error"><?= htmlspecialchars($helpErr) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-row">
                    <div class="field">
                        <label for="help-name">নাম *</label>
                        <input type="text" id="help-name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? ($__user['name'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label for="help-phone">ফোন *</label>
                        <input type="tel" id="help-phone" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? ($__user['phone'] ?? '')) ?>" placeholder="01XXXXXXXXX">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="help-email">ইমেইল (ঐচ্ছিক)</label>
                        <input type="email" id="help-email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? ($__user['email'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label for="help-message">মেসেজ *</label>
                        <textarea id="help-message" name="message" required placeholder="আপনার সমস্যা/প্রয়োজন লিখুন"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                </div>

                <button type="submit" name="send_help_request" class="btn btn-gold">হেল্প রিকোয়েস্ট পাঠান</button>
            </form>
        </div>
    </div>
</section>

<button id="back-top" aria-label="Back to top">↑</button>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
