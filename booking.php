<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/booking-handler.php';

$bookingResult = ['success' => false, 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingResult = handle_booking();
}

$user = current_user();
$__showHero = false;
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<style>
    .booking-confirm-modal{position:fixed;inset:0;z-index:1500;background:rgba(0,0,0,.82);backdrop-filter:blur(3px);display:none;align-items:center;justify-content:center;padding:14px;overflow-y:auto;}
    .booking-confirm-modal.active{display:flex;}
    .bcm-panel{width:min(520px,100%);background:linear-gradient(145deg,#11161d,#161d26);border:1px solid rgba(212,175,55,.35);border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.55);padding:32px 28px;text-align:center;animation:bcmIn .28s ease;}
    @keyframes bcmIn{from{opacity:0;transform:translateY(22px);}to{opacity:1;transform:none;}}
    .bcm-icon{font-size:3.2rem;line-height:1;margin-bottom:14px;}
    .bcm-title{font-size:1.45rem;font-weight:700;color:var(--white);margin-bottom:8px;}
    .bcm-sub{font-size:.95rem;color:#c9d4e6;margin-bottom:20px;line-height:1.6;}
    .bcm-details{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px 16px;text-align:left;margin-bottom:22px;}
    .bcm-row{display:flex;gap:10px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.88rem;}
    .bcm-row:last-child{border-bottom:none;}
    .bcm-row .bk{color:var(--muted);min-width:80px;flex-shrink:0;}
    .bcm-row .bv{color:var(--light);font-weight:600;word-break:break-word;}
    .bcm-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
    .bcm-actions .btn{min-width:140px;}
    @media(max-width:480px){.bcm-panel{padding:22px 16px;}.bcm-title{font-size:1.2rem;}.bcm-actions .btn{width:100%;}}
</style>

<section id="booking">
    <div class="container">
        <div class="section-title">
            <span class="label">Booking</span>
            <h2>নতুন বুকিং করুন</h2>
            <p>তারিখ, সময় এবং সার্ভিস সিলেক্ট করে সহজেই আপনার বুকিং অনুরোধ পাঠান।</p>
        </div>

        <div class="booking-grid">
            <div class="booking-form-wrap">
                <?php if ($bookingResult['message'] && !$bookingResult['success']): ?>
                    <div class="alert alert-error">
                        <?= $bookingResult['message'] ?>
                    </div>
                <?php endif; ?>

                <?php if (!$user): ?>
                    <div class="alert alert-error" style="margin-bottom:18px;">
                        বুকিং করতে প্রথমে লগইন করুন।
                        <a href="/login.php" style="font-weight:700;margin-left:6px;">লগইন পেজে যান</a>
                    </div>
                <?php endif; ?>

                <form id="booking-form" method="post" novalidate>
                    <div class="form-row">
                        <div class="field">
                            <label for="name">নাম *</label>
                            <input type="text" id="name" name="name" required
                                value="<?= htmlspecialchars($_POST['name'] ?? ($user['name'] ?? '')) ?>"
                                placeholder="আপনার পূর্ণ নাম">
                        </div>
                        <div class="field">
                            <label for="phone">মোবাইল নম্বর *</label>
                            <input type="tel" id="phone" name="phone" required
                                value="<?= htmlspecialchars($_POST['phone'] ?? ($user['phone'] ?? '')) ?>"
                                placeholder="01XXXXXXXXX">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="service">সার্ভিস *</label>
                            <select id="service" name="service" required>
                                <?php $selectedService = $_POST['service'] ?? ''; ?>
                                <option value="">সার্ভিস নির্বাচন করুন</option>
                                <option value="বিয়ের ফটোগ্রাফি" <?= $selectedService === 'বিয়ের ফটোগ্রাফি' ? 'selected' : '' ?>>বিয়ের ফটোগ্রাফি</option>
                                <option value="জন্মদিনের ফটোগ্রাফি" <?= $selectedService === 'জন্মদিনের ফটোগ্রাফি' ? 'selected' : '' ?>>জন্মদিনের ফটোগ্রাফি</option>
                                <option value="আউটডোর ফটোশুট" <?= $selectedService === 'আউটডোর ফটোশুট' ? 'selected' : '' ?>>আউটডোর ফটোশুট</option>
                                <option value="পোর্ট্রেট ফটোগ্রাফি" <?= $selectedService === 'পোর্ট্রেট ফটোগ্রাফি' ? 'selected' : '' ?>>পোর্ট্রেট ফটোগ্রাফি</option>
                                <option value="ইভেন্ট কভারেজ" <?= $selectedService === 'ইভেন্ট কভারেজ' ? 'selected' : '' ?>>ইভেন্ট কভারেজ</option>
                                <option value="ফ্যামিলি ফটোশুট" <?= $selectedService === 'ফ্যামিলি ফটোশুট' ? 'selected' : '' ?>>ফ্যামিলি ফটোশুট</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="date">তারিখ *</label>
                            <input type="date" id="date" name="date" required value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="time">সময় *</label>
                            <input type="time" id="time" name="time" required value="<?= htmlspecialchars($_POST['time'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label for="details">বিস্তারিত (ঐচ্ছিক)</label>
                            <textarea id="details" name="details" placeholder="লোকেশন, বিশেষ চাহিদা, ইভেন্ট টাইপ ইত্যাদি লিখুন"><?= htmlspecialchars($_POST['details'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold" style="width:100%;">
                        বুকিং কনফার্ম করুন
                    </button>
                </form>
            </div>

            <aside class="booking-info">
                <div class="info-card">
                    <h3>📌 বুকিং গাইড</h3>
                    <ul class="steps-list">
                        <li>সঠিক তারিখ ও সময় নির্বাচন করুন</li>
                        <li>আপনার পছন্দের সার্ভিস বেছে নিন</li>
                        <li>দরকার হলে অতিরিক্ত তথ্য লিখুন</li>
                        <li>আমরা দ্রুত আপনার সাথে যোগাযোগ করব</li>
                    </ul>
                </div>

                <div class="info-card booking-time-card">
                    <h3>⏰ বুকিং টাইম ইনফো</h3>
                    <div class="booking-time-grid">
                        <div class="time-pill">সকাল ৯:০০</div>
                        <div class="time-pill">দুপুর ১২:০০</div>
                        <div class="time-pill">বিকাল ৩:০০</div>
                        <div class="time-pill">সন্ধ্যা ৬:০০</div>
                    </div>
                    <p class="booking-time-note">
                        প্রতিদিন বুকিং টাইম রেঞ্জ 09:00 AM - 08:00 PM। জরুরি বুকিং হলে WhatsApp-এ আগে যোগাযোগ করুন।
                    </p>
                </div>

                <div class="info-card">
                    <h3>📞 যোগাযোগ</h3>
                    <div class="info-row"><span class="key">ফোন</span><span class="val"><?= PHONE ?></span></div>
                    <div class="info-row"><span class="key">WhatsApp</span><span class="val"><?= WHATSAPP ?></span></div>
                    <div class="info-row"><span class="key">ইমেইল</span><span class="val"><?= EMAIL ?></span></div>
                    <div class="info-row"><span class="key">লোকেশন</span><span class="val"><?= LOCATION ?></span></div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php if ($bookingResult['success']): ?>
<div class="booking-confirm-modal" id="booking-confirm-modal" aria-modal="true" role="dialog" aria-hidden="true">
    <div class="bcm-panel">
        <div class="bcm-icon">✅</div>
        <div class="bcm-title">ধন্যবাদ <?= $bookingResult['booking_name'] ?>!</div>
        <div class="bcm-sub">আপনার বুকিং অনুরোধ সফলভাবে গৃহীত হয়েছে।<br>শীঘ্রই <strong style="color:var(--gold);"><?= $bookingResult['admin_phone'] ?></strong> নম্বর থেকে আপনার <strong style="color:var(--gold);"><?= $bookingResult['booking_phone'] ?></strong> নম্বরে যোগাযোগ করা হবে।</div>
        <div class="bcm-details">
            <div class="bcm-row"><span class="bk">সার্ভিস</span><span class="bv"><?= $bookingResult['booking_service'] ?></span></div>
            <div class="bcm-row"><span class="bk">তারিখ</span><span class="bv"><?= $bookingResult['booking_date'] ?></span></div>
            <div class="bcm-row"><span class="bk">সময়</span><span class="bv"><?= $bookingResult['booking_time'] ?></span></div>
            <?php if ($bookingResult['booking_details']): ?>
            <div class="bcm-row"><span class="bk">বিস্তারিত</span><span class="bv"><?= $bookingResult['booking_details'] ?></span></div>
            <?php endif; ?>
            <div class="bcm-row"><span class="bk">ফোন</span><span class="bv"><?= $bookingResult['booking_phone'] ?></span></div>
        </div>
        <div class="bcm-actions">
            <?php if ($bookingResult['whatsapp_url']): ?>
            <a href="<?= htmlspecialchars($bookingResult['whatsapp_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold">💬 WhatsApp-এ পাঠান</a>
            <?php endif; ?>
            <a href="/profile.php" class="btn btn-outline">প্রোফাইলে যান</a>
        </div>
    </div>
</div>
<?php endif; ?>

<button id="back-top" aria-label="Back to top">↑</button>

<script>
(function(){
    var m = document.getElementById('booking-confirm-modal');
    if (!m) return;
    m.classList.add('active');
    m.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    m.addEventListener('click', function(e){
        if (e.target === m) {
            m.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && m.classList.contains('active')) {
            m.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
