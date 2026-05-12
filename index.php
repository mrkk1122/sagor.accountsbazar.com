<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/booking-handler.php';

$booking = handle_booking();
$__user  = current_user(); // already set via header.php include chain
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- ===== ABOUT ===== -->
<section id="about">
    <div class="container">
        <div class="section-title">
            <span class="label">About Me</span>
            <h2>আমার সম্পর্কে জানুন</h2>
            <p>আমি একজন প্রফেশনাল ফটোগ্রাফার যিনি বিয়ে, জন্মদিন, পারিবারিক অনুষ্ঠান ও আউটডোর শুটের জন্য কাস্টমাইজড সার্ভিস দিই।</p>
        </div>

        <div class="about-grid">
            <div class="about-img">
                <img src="https://images.unsplash.com/photo-1452378174528-3090a4bba7b2?auto=format&fit=crop&w=800&q=80"
                     alt="Photographer at work" loading="lazy">
                <div class="exp-badge">✦ Professional Photographer</div>
            </div>

            <div class="about-content">
                <h2>প্রতিটি মুহূর্ত যেন একটি শিল্প</h2>
                <p>আমি আপনার বিশেষ মুহূর্তগুলোকে সুন্দর ও পেশাদারভাবে ক্যামেরাবন্দি করি। প্রতিটি ছবিতে আমি নিশ্চিত করি সেরা আলো, কম্পোজিশন এবং অনুভূতি।</p>
                <p>সাশ্রয়ী মূল্যে সর্বোচ্চ মানের ফটোগ্রাফি সার্ভিস — যেখানে প্রতিটি ছবির মূল্য মাত্র <strong class="text-gold">৳<?= PRICE_PER_PHOTO ?></strong> টাকা।</p>

                <div class="stats-row">
                    <div class="stat"><span class="num">৫০০+</span><span class="lbl">সন্তুষ্ট ক্লাইন্ট</span></div>
                    <div class="stat"><span class="num">১০০০+</span><span class="lbl">ইভেন্ট কভার</span></div>
                    <div class="stat"><span class="num">৳<?= PRICE_PER_PHOTO ?></span><span class="lbl">প্রতি ছবি</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SERVICES ===== -->
<section id="services">
    <div class="container">
        <div class="section-title">
            <span class="label">Services</span>
            <h2>আমার সার্ভিসমূহ</h2>
            <p>বিভিন্ন ধরনের ফটোগ্রাফি সার্ভিস যা আপনার প্রয়োজন ও বাজেট অনুযায়ী কাস্টমাইজ করা যাবে।</p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">💍</div>
                <h3>বিয়ের ফটোগ্রাফি</h3>
                <p>বিয়ে, গায়ে হলুদ ও রিসেপশনের সম্পূর্ণ প্রফেশনাল কভারেজ — প্রতিটি মুহূর্ত চিরস্মরণীয় করে রাখুন।</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎂</div>
                <h3>জন্মদিনের ফটোগ্রাফি</h3>
                <p>শিশু থেকে প্রাপ্তবয়স্ক — সবার জন্য আনন্দের জন্মদিন উদযাপনের স্মরণীয় ছবি।</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🌿</div>
                <h3>আউটডোর ফটোশুট</h3>
                <p>পার্ক, নদীর ধার, ছাদ বা যেকোনো আউটডোর লোকেশনে ক্রিয়েটিভ ফটোশুট।</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🖼️</div>
                <h3>পোর্ট্রেট ফটোগ্রাফি</h3>
                <p>ব্যক্তিগত, কাপল বা পারিবারিক পোর্ট্রেট — সোশ্যাল মিডিয়া বা স্মৃতির জন্য নিখুঁত শট।</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎪</div>
                <h3>ইভেন্ট কভারেজ</h3>
                <p>কর্পোরেট, সাংস্কৃতিক বা যেকোনো বড় অনুষ্ঠানের সম্পূর্ণ ফটো কভারেজ।</p>
            </div>
            <div class="service-card">
                <div class="service-icon">👨‍👩‍👧</div>
                <h3>ফ্যামিলি ফটোশুট</h3>
                <p>পরিবারের সুন্দর মুহূর্তগুলো একসাথে ক্যামেরায় ধরে রাখুন — ঘরে বা বাইরে।</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== PRICING ===== -->
<section id="pricing">
    <div class="container">
        <div class="section-title">
            <span class="label">Pricing</span>
            <h2>মূল্য তালিকা</h2>
            <p>সহজ ও স্বচ্ছ মূল্য — কোনো লুকানো চার্জ নেই। বড় প্রজেক্টের জন্য কাস্টম প্যাকেজও পাওয়া যাবে।</p>
        </div>

        <div class="pricing-wrap">
            <div class="pricing-card">
                <span class="popular">★ সবচেয়ে জনপ্রিয়</span>
                <h3>স্ট্যান্ডার্ড প্ল্যান</h3>
                <p class="text-muted text-sm">প্রতিটি ছবির জন্য নির্ধারিত মূল্য</p>
                <div class="price-display">
                    <span class="currency">৳</span><span class="amount"><?= PRICE_PER_PHOTO ?></span>
                    <div class="unit">প্রতি ছবি</div>
                </div>
                <ul class="pricing-features">
                    <li>প্রফেশনাল DSLR ক্যামেরায় শুট</li>
                    <li>বেসিক এডিটিং ও কালার কারেকশন</li>
                    <li>ডিজিটাল কপি ডেলিভারি</li>
                    <li>সকাল ৯টা – রাত ৮টা সার্ভিস উইন্ডো</li>
                    <li>বড় প্রজেক্টে কাস্টম অফার উপলব্ধ</li>
                </ul>
                <a href="#booking" class="btn btn-gold" style="display:block;width:100%;text-align:center;">এখনই বুকিং করুন</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== SCHEDULE ===== -->
<section id="schedule">
    <div class="container">
        <div class="section-title">
            <span class="label">Schedule</span>
            <h2>সাপ্তাহিক কাজের সময়সূচি</h2>
            <p>প্রতি সপ্তাহে নিচের দিনগুলোতে বুকিং গ্রহণ করা হয়। শুক্রবার বিশেষ বুকিংয়ের জন্য আগেই যোগাযোগ করুন।</p>
        </div>

        <div class="schedule-grid">
            <?php foreach ($SCHEDULE as $day => $hours): ?>
                <div class="day-card <?= $hours ? '' : 'closed' ?>">
                    <div class="day-name"><?= htmlspecialchars($day) ?></div>
                    <?php if ($hours): ?>
                        <div class="day-time"><?= htmlspecialchars($hours[0]) ?><br>থেকে<br><?= htmlspecialchars($hours[1]) ?></div>
                    <?php else: ?>
                        <div class="day-time">বিশেষ বুকিং<br>অগ্রিম যোগাযোগ</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="time-info-row">
            <div class="time-info-card">
                <div class="time-icon">⏰</div>
                <div>
                    <h4>দৈনিক সার্ভিস সময়</h4>
                    <p>সকাল ৯:০০ টা থেকে রাত ৮:০০ টা পর্যন্ত বুকিং নেওয়া হয়।</p>
                </div>
            </div>
            <div class="time-info-card">
                <div class="time-icon">📅</div>
                <div>
                    <h4>অ্যাডভান্স বুকিং</h4>
                    <p>যেকোনো শুটের জন্য কমপক্ষে ১–৩ দিন আগে বুকিং করলে সেরা সার্ভিস নিশ্চিত।</p>
                </div>
            </div>
            <div class="time-info-card">
                <div class="time-icon">📝</div>
                <div>
                    <h4>বিশেষ নোট</h4>
                    <p>শুক্রবার ও সরকারি ছুটির দিনে বিশেষ প্যাকেজ প্রযোজ্য। আগে যোগাযোগ করুন।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== BOOKING ===== -->
<section id="booking">
    <div class="container">
        <div class="section-title">
            <span class="label">Advance Booking</span>
            <h2>অ্যাডভান্স বুকিং ফর্ম</h2>
            <p>নিচের ফর্মটি পূরণ করুন। বুকিং কনফার্মেশনের জন্য শীঘ্রই আপনার সাথে যোগাযোগ করা হবে।</p>
        </div>

        <div class="booking-grid">
            <!-- Form -->
            <div class="booking-form-wrap">
                <?php if (!$__user): ?>
                    <div style="text-align:center;padding:48px 20px;">
                        <div style="font-size:3.5rem;margin-bottom:16px;">🔒</div>
                        <h3 style="color:var(--white);margin-bottom:10px;">বুকিং করতে লগইন করুন</h3>
                        <p style="color:var(--muted);margin-bottom:24px;">বুকিং সুবিধা ব্যবহার করতে প্রথমে আপনার অ্যাকাউন্টে লগইন করুন।</p>
                        <a href="/login.php" class="btn btn-gold">লগইন করুন</a>
                        &nbsp;
                        <a href="/register.php" class="btn btn-outline">নতুন অ্যাকাউন্ট</a>
                    </div>
                <?php else: ?>
                <?php if ($booking['message']): ?>
                    <div class="alert <?= $booking['success'] ? 'alert-success' : 'alert-error' ?>">
                        <?= htmlspecialchars($booking['message']) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="#booking" id="booking-form">
                    <div class="form-row">
                        <div class="field">
                            <label for="name">আপনার নাম <span style="color:#ef4444">*</span></label>
                            <input type="text" id="name" name="name"
                                   placeholder="পূর্ণ নাম লিখুন"
                                   value="<?= htmlspecialchars($_POST['name'] ?? $__user['name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="phone">মোবাইল নাম্বার <span style="color:#ef4444">*</span></label>
                            <input type="tel" id="phone" name="phone"
                                   placeholder="01XXXXXXXXX"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? $__user['phone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="service">শুটের ধরন <span style="color:#ef4444">*</span></label>
                        <select id="service" name="service" required>
                            <option value="">সিলেক্ট করুন</option>
                            <?php foreach ($SERVICES as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>"
                                    <?= (($_POST['service'] ?? '') === $val) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="date">বুকিংয়ের তারিখ <span style="color:#ef4444">*</span></label>
                            <input type="date" id="date" name="date"
                                   value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="time">পছন্দের সময় <span style="color:#ef4444">*</span></label>
                            <input type="time" id="time" name="time"
                                   value="<?= htmlspecialchars($_POST['time'] ?? '') ?>" required>
                            <span class="hint">সকাল ৯:০০ – রাত ৮:০০</span>
                        </div>
                    </div>

                    <div class="field">
                        <label for="details">অতিরিক্ত তথ্য</label>
                        <textarea id="details" name="details"
                            placeholder="লোকেশন, আনুমানিক ছবির সংখ্যা, বিশেষ নির্দেশনা লিখুন…"><?= htmlspecialchars($_POST['details'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-gold" style="width:100%">📨 বুকিং পাঠান</button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Sidebar Info -->
            <div class="booking-info">
                <div class="info-card">
                    <h3>📋 বুকিং তথ্য</h3>
                    <div class="info-row"><span class="key">ফটোগ্রাফার</span><span class="val"><?= PHOTOGRAPHER_NAME ?> Photography</span></div>
                    <div class="info-row"><span class="key">প্রতি ছবির মূল্য</span><span class="val">৳<?= PRICE_PER_PHOTO ?> টাকা</span></div>
                    <div class="info-row"><span class="key">সার্ভিস সময়</span><span class="val">সকাল ৯টা – রাত ৮টা</span></div>
                    <div class="info-row"><span class="key">সাপ্তাহিক ছুটি</span><span class="val">শুক্রবার*</span></div>
                    <div class="info-row"><span class="key">লোকেশন</span><span class="val"><?= LOCATION ?></span></div>
                </div>

                <div class="info-card" id="contact">
                    <h3>📞 যোগাযোগ</h3>
                    <div class="info-row"><span class="key">মোবাইল</span><span class="val"><?= PHONE ?></span></div>
                    <div class="info-row"><span class="key">WhatsApp</span><span class="val"><?= WHATSAPP ?></span></div>
                    <div class="info-row"><span class="key">Email</span><span class="val"><?= EMAIL ?></span></div>
                </div>

                <div class="info-card">
                    <h3>🔢 বুকিং প্রক্রিয়া</h3>
                    <ol class="steps-list">
                        <li>ফর্মটি পূরণ করে সাবমিট করুন</li>
                        <li>ফোন বা WhatsApp-এ কনফার্মেশন নিন</li>
                        <li>তারিখ ও সময় চূড়ান্ত করুন</li>
                        <li>নির্ধারিত দিনে ফটোশুট উপভোগ করুন</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CONTACT CARDS ===== -->
<section style="padding:40px 0 90px">
    <div class="container">
        <div class="section-title">
            <span class="label">Contact</span>
            <h2>যোগাযোগের মাধ্যম</h2>
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

<!-- Back to top -->
<button id="back-top" aria-label="Back to top">↑</button>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
