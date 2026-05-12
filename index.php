<?php
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sagor Photography | Professional Booking</title>
    <style>
        :root {
            --primary: #111827;
            --secondary: #d4af37;
            --light: #f9fafb;
            --muted: #6b7280;
            --card: #ffffff;
            --accent: #1f2937;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f3f4f6;
            color: #111827;
            line-height: 1.6;
        }

        .container {
            width: min(1100px, 92%);
            margin: auto;
        }

        header {
            background: linear-gradient(rgba(17,24,39,.75), rgba(17,24,39,.78)), url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1400&q=80') center/cover;
            color: white;
            padding: 90px 0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 70px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .logo span {
            color: var(--secondary);
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 18px;
            font-size: 15px;
        }

        .hero {
            max-width: 700px;
        }

        .hero h1 {
            font-size: 48px;
            line-height: 1.15;
            margin-bottom: 18px;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 26px;
            color: #e5e7eb;
        }

        .btn {
            display: inline-block;
            background: var(--secondary);
            color: #111827;
            padding: 14px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            margin-left: 12px;
        }

        section {
            padding: 70px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--muted);
            max-width: 700px;
            margin: auto;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .card {
            background: var(--card);
            padding: 24px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
        }

        .card h3 {
            margin-bottom: 12px;
            color: var(--accent);
        }

        .price-box {
            text-align: center;
            background: #111827;
            color: white;
            padding: 34px 24px;
            border-radius: 16px;
        }

        .price-box .price {
            font-size: 42px;
            color: var(--secondary);
            font-weight: 700;
            margin: 14px 0;
        }

        .schedule {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .schedule .day {
            background: white;
            padding: 18px;
            border-left: 4px solid var(--secondary);
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,.05);
        }

        .booking-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            align-items: start;
        }

        form {
            background: white;
            padding: 24px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.05);
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
        }

        .field textarea {
            min-height: 120px;
            resize: vertical;
        }

        .info-box {
            background: #111827;
            color: white;
            padding: 26px;
            border-radius: 14px;
        }

        .info-box h3 {
            margin-bottom: 14px;
            color: var(--secondary);
        }

        .info-box p,
        .info-box li {
            color: #e5e7eb;
        }

        .info-box ul {
            padding-left: 20px;
            margin: 12px 0;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 24px 0;
        }

        @media (max-width: 850px) {
            .booking-wrap {
                grid-template-columns: 1fr;
            }

            nav {
                flex-direction: column;
                gap: 14px;
            }

            .nav-links {
                text-align: center;
            }

            .nav-links a {
                margin: 0 8px;
            }

            .hero h1 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">Sagor <span>Photography</span></div>
                <div class="nav-links">
                    <a href="#about">About</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#schedule">Schedule</a>
                    <a href="#booking">Booking</a>
                    <a href="#contact">Contact</a>
                </div>
            </nav>

            <div class="hero">
                <h1>প্রফেশনাল ফটোগ্রাফি সার্ভিস ও অ্যাডভান্স বুকিং</h1>
                <p>আমি আপনার বিশেষ মুহূর্তগুলোকে সুন্দরভাবে ক্যামেরাবন্দি করি। এখন আপনি সহজেই অনলাইন থেকে আমাকে hire করতে পারবেন, বুকিং দিতে পারবেন এবং কাজের সময়সূচি দেখতে পারবেন।</p>
                <a href="#booking" class="btn">এখনই বুকিং করুন</a>
                <a href="#pricing" class="btn btn-outline">দাম দেখুন</a>
            </div>
        </div>
    </header>

    <section id="about">
        <div class="container">
            <div class="section-title">
                <h2>আমার সম্পর্কে</h2>
                <p>আমি একজন প্রফেশনাল ফটোগ্রাফার। বিয়ে, জন্মদিন, পারিবারিক অনুষ্ঠান, আউটডোর ফটোশুট এবং ব্যক্তিগত প্রজেক্টের জন্য কাস্টমাইজড ফটোগ্রাফি সার্ভিস দিয়ে থাকি।</p>
            </div>
            <div class="cards">
                <div class="card">
                    <h3>ইভেন্ট ফটোগ্রাফি</h3>
                    <p>বিয়ে, গায়ে হলুদ, জন্মদিন, কর্পোরেট ইভেন্টসহ বিভিন্ন অনুষ্ঠানের প্রফেশনাল কভারেজ।</p>
                </div>
                <div class="card">
                    <h3>পোর্ট্রেট শুট</h3>
                    <p>ব্যক্তিগত, কাপল, ফ্যামিলি এবং সোশ্যাল মিডিয়া পোর্ট্রেট শুট সেবা।</p>
                </div>
                <div class="card">
                    <h3>অ্যাডভান্স বুকিং</h3>
                    <p>আগেই তারিখ, সময় এবং শুটের ধরন নির্ধারণ করে সহজে বুকিং সম্পন্ন করুন।</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing">
        <div class="container">
            <div class="section-title">
                <h2>প্রাইসিং</h2>
                <p>স্বচ্ছ ও সহজ মূল্য তালিকা যাতে কাস্টমাররা আগেই খরচ সম্পর্কে জানতে পারেন।</p>
            </div>
            <div class="price-box">
                <h3>প্রতি ছবির মূল্য</h3>
                <div class="price">৳ ১০</div>
                <p>প্রতি ছবির জন্য নির্ধারিত মূল্য ১০ টাকা। বড় কাজের ক্ষেত্রে আলাদা প্যাকেজ ও কাস্টম অফার দেওয়া যাবে।</p>
            </div>
        </div>
    </section>

    <section id="schedule">
        <div class="container">
            <div class="section-title">
                <h2>কাজের সময়সূচি</h2>
                <p>নিচে সপ্তাহের দিন এবং কাজের সময় দেওয়া হলো। প্রয়োজনে বিশেষ দিনে আলাদা বুকিং নেওয়া হবে।</p>
            </div>
            <div class="cards" style="margin-bottom: 24px;">
                <div class="card">
                    <h3>দৈনিক সময়</h3>
                    <p><strong>সকাল ৯:০০ টা</strong> থেকে <strong>রাত ৮:০০ টা</strong> পর্যন্ত বুকিং নেওয়া হয়।</p>
                </div>
                <div class="card">
                    <h3>অ্যাডভান্স বুকিং</h3>
                    <p>যে কোনো শুটের জন্য অন্তত <strong>১-৩ দিন আগে</strong> বুকিং করলে ভালো সার্ভিস নিশ্চিত করা যাবে।</p>
                </div>
                <div class="card">
                    <h3>বিশেষ নোট</h3>
                    <p>শুক্রবার ও ছুটির দিনে বিশেষ প্যাকেজ বা প্রিমিয়াম বুকিং প্রযোজ্য হতে পারে।</p>
                </div>
            </div>
            <div class="schedule">
                <div class="day"><strong>শনিবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>রবিবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>সোমবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>মঙ্গলবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>বুধবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>বৃহস্পতিবার</strong><br>সকাল ৯টা - রাত ৮টা</div>
                <div class="day"><strong>শুক্রবার</strong><br>বিশেষ বুকিং / অগ্রিম যোগাযোগ</div>
            </div>
        </div>
    </section>

    <section id="booking">
        <div class="container">
            <div class="section-title">
                <h2>অ্যাডভান্স বুকিং ফর্ম</h2>
                <p>আপনার প্রয়োজনীয় তথ্য পূরণ করে বুকিং পাঠান। আমি দ্রুত আপনার সাথে যোগাযোগ করব।</p>
            </div>

            <div class="booking-wrap">
                <div>
                    <?php if ($success): ?>
                        <div class="success">ধন্যবাদ! আপনার বুকিং অনুরোধ সফলভাবে গ্রহণ করা হয়েছে। খুব শীঘ্রই আপনার সাথে যোগাযোগ করা হবে।</div>
                    <?php endif; ?>

                    <form method="post" action="#booking">
                        <div class="field">
                            <label for="name">আপনার নাম</label>
                            <input type="text" id="name" name="name" placeholder="পূর্ণ নাম লিখুন" required>
                        </div>
                        <div class="field">
                            <label for="phone">মোবাইল নাম্বার</label>
                            <input type="text" id="phone" name="phone" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label for="service">শুটের ধরন</label>
                            <select id="service" name="service" required>
                                <option value="">সিলেক্ট করুন</option>
                                <option>Wedding Photography</option>
                                <option>Birthday Photography</option>
                                <option>Outdoor Photoshoot</option>
                                <option>Portrait Photography</option>
                                <option>Event Coverage</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="date">বুকিংয়ের তারিখ</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        <div class="field">
                            <label for="time">পছন্দের সময়</label>
                            <input type="time" id="time" name="time" required>
                        </div>
                        <div class="field">
                            <label for="details">অতিরিক্ত তথ্য</label>
                            <textarea id="details" name="details" placeholder="লোকেশন, আনুমানিক ছবির সংখ্যা, বিশেষ নির্দেশনা লিখুন"></textarea>
                        </div>
                        <button type="submit" class="btn">বুকিং পাঠান</button>
                    </form>
                </div>

                <div class="info-box" id="contact">
                    <h3>যোগাযোগ ও বুকিং তথ্য</h3>
                    <p>আপনি চাইলে সরাসরি ফোন, WhatsApp বা Facebook-এর মাধ্যমেও যোগাযোগ করতে পারেন।</p>
                    <ul>
                        <li><strong>ফটোগ্রাফার:</strong> Sagor Photography</li>
                        <li><strong>প্রতি ছবির মূল্য:</strong> ১০ টাকা</li>
                        <li><strong>সার্ভিস সময়:</strong> সকাল ৯টা - রাত ৮টা</li>
                        <li><strong>সাপ্তাহিক ছুটি:</strong> শুক্রবার (বিশেষ বুকিং ব্যতীত)</li>
                        <li><strong>লোকেশন:</strong> বাংলাদেশ</li>
                        <li><strong>মোবাইল:</strong> 01XXXXXXXXX</li>
                        <li><strong>WhatsApp:</strong> 01XXXXXXXXX</li>
                        <li><strong>Email:</strong> booking@example.com</li>
                    </ul>
                    <p>বুকিং কনফার্ম করার জন্য অগ্রিম পেমেন্ট বা ফোনে নিশ্চিতকরণ লাগতে পারে।</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Sagor Photography. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
