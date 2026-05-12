<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/booking-handler.php';

$bookingResult = ['success' => false, 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingResult = handle_booking();
}

$user = current_user();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section id="booking">
    <div class="container">
        <div class="section-title">
            <span class="label">Booking</span>
            <h2>নতুন বুকিং করুন</h2>
            <p>তারিখ, সময় এবং সার্ভিস সিলেক্ট করে সহজেই আপনার বুকিং অনুরোধ পাঠান।</p>
        </div>

        <div class="booking-grid">
            <div class="booking-form-wrap">
                <?php if ($bookingResult['message']): ?>
                    <div class="alert <?= $bookingResult['success'] ? 'alert-success' : 'alert-error' ?>">
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

<button id="back-top" aria-label="Back to top">↑</button>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
