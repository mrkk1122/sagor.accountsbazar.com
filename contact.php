<?php
require_once __DIR__ . '/includes/config.php';
$__showHero = false;
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section id="contact" style="padding-top:70px;">
    <div class="container">
        <div class="section-title">
            <span class="label">Contact</span>
            <h2>যোগাযোগ করুন</h2>
            <p>কল, WhatsApp অথবা ইমেইলে সহজেই যোগাযোগ করুন। দ্রুত রেসপন্স দেওয়া হয়।</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="c-icon">📞</div>
                <h4>ফোন কল</h4>
                <p><?= PHONE ?></p>
            </div>
            <div class="contact-card">
                <div class="c-icon">💬</div>
                <h4>WhatsApp</h4>
                <p><?= WHATSAPP ?></p>
            </div>
            <div class="contact-card">
                <div class="c-icon">📧</div>
                <h4>ইমেইল</h4>
                <p><?= EMAIL ?></p>
            </div>
            <div class="contact-card">
                <div class="c-icon">📍</div>
                <h4>লোকেশন</h4>
                <p><?= LOCATION ?></p>
            </div>
        </div>
    </div>
</section>

<button id="back-top" aria-label="Back to top">↑</button>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
