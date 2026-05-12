<footer id="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <span class="logo"><?= PHOTOGRAPHER_NAME ?> <span>Photography</span></span>
            <p>আপনার বিশেষ মুহূর্তগুলো আমাদের ক্যামেরায় চিরস্মরণীয় হয়ে থাকুক।</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="#about">পরিচিতি</a>
            <a href="#services">সার্ভিস</a>
            <a href="#pricing">মূল্য</a>
            <a href="#schedule">সময়সূচি</a>
            <a href="#booking">বুকিং</a>
        </div>
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
    <a href="/#services"><span class="mfb-icon" aria-hidden="true">🛠</span><span class="mfb-label">সার্ভিস</span></a>
    <a href="/#booking"><span class="mfb-icon" aria-hidden="true">📅</span><span class="mfb-label">বুকিং</span></a>
    <a href="/#contact"><span class="mfb-icon" aria-hidden="true">☎</span><span class="mfb-label">যোগাযোগ</span></a>
    <?php if ($__user): ?>
        <a href="/profile.php"><span class="mfb-icon" aria-hidden="true">👤</span><span class="mfb-label">প্রোফাইল</span></a>
    <?php else: ?>
        <a href="/login.php"><span class="mfb-icon" aria-hidden="true">🔑</span><span class="mfb-label">লগইন</span></a>
    <?php endif; ?>
</nav>

<script src="js/main.js"></script>
</body>
</html>
