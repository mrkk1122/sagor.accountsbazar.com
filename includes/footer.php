<footer id="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <span class="logo"><?= PHOTOGRAPHER_NAME ?> <span>Photography</span></span>
            <p>আপনার বিশেষ মুহূর্তগুলো আমাদের ক্যামেরায় চিরস্মরণীয় হয়ে থাকুক।</p>
        </div>
        <!-- Quick Links removed as per request -->
        <div class="footer-contact">
            <h4>যোগাযোগ</h4>
            <p>📞 <?= PHONE ?></p>
            <p>📧 <?= EMAIL ?></p>
            <p>📍 <?= LOCATION ?></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</footer>

<nav class="mobile-fixed-bar" aria-label="Mobile quick navigation">
    <a href="/#top"><span class="mfb-icon" aria-hidden="true">🏠</span><span class="mfb-label">হোম</span></a>
    <a href="/ai_photo_genaretor.php"><span class="mfb-icon" aria-hidden="true">🤖</span><span class="mfb-label">AI Photo</span></a>
    <a href="/booking.php"><span class="mfb-icon" aria-hidden="true">📅</span><span class="mfb-label">বুকিং</span></a>
    <a href="/contact.php"><span class="mfb-icon" aria-hidden="true">☎</span><span class="mfb-label">যোগাযোগ</span></a>
    <?php if ($__user): ?>
        <?php
            $__name = (string)($__user['name'] ?? '');
            $__initial = function_exists('mb_substr') ? mb_substr($__name, 0, 1) : substr($__name, 0, 1);
            if ($__initial === '') $__initial = '👤';
        ?>
        <a href="/profile.php"><span class="mfb-avatar" aria-hidden="true"><?= htmlspecialchars($__initial) ?></span><span class="mfb-label">প্রোফাইল</span></a>
    <?php else: ?>
        <a href="/login.php"><span class="mfb-icon" aria-hidden="true">🔑</span><span class="mfb-label">লগইন</span></a>
    <?php endif; ?>
</nav>

<script src="js/main.js?v=20260512-12"></script>
</body>
</html>
