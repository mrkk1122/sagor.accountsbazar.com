<?php
require_once __DIR__ . '/includes/config.php';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- ===== ABOUT ===== -->
<section id="about">
    <div class="container">
        <div class="about-photo-home" style="text-align:center; margin-bottom: 24px;">
            <img src="https://scontent.fdac177-2.fna.fbcdn.net/v/t39.30808-6/597425241_1504348431397430_5748346082661165302_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=833d8c&_nc_ohc=k92RtCvGRxIQ7kNvwFh05gz&_nc_oc=AdoXodMKYji_IxWy4yjGzIHZNa5uleN6Sk91aUm-NZ5QkuAXqsrxsBLRf5iRExoJFds&_nc_zt=23&_nc_ht=scontent.fdac177-2.fna&_nc_gid=CVeG6fwgRO5TWX511FCjPQ&_nc_ss=7b289&oh=00_Af6CddAhe59L6HLHZpF03zhdQAzwepnBmcd0_o3JNdg2oA&oe=6A099309" alt="Photographer Sagor" style="max-width: 320px; width: 100%; border-radius: 12px; box-shadow: 0 4px 24px #0002;" loading="lazy">
        </div>
        <div class="section-title">
            <span class="label">About Me</span>
            <h2>আমার সম্পর্কে জানুন</h2>
            <p>আমি একজন প্রফেশনাল ফটোগ্রাফার যিনি বিয়ে, জন্মদিন, পারিবারিক অনুষ্ঠান ও আউটডোর শুটের জন্য কাস্টমাইজড সার্ভিস দিই।</p>
        </div>
        <div class="about-grid">
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

<!-- Back to top -->
<button id="back-top" aria-label="Back to top">↑</button>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
