<?php
// Site Configuration
define('SITE_NAME', 'Sagor Photography');
define('SITE_TAGLINE', 'প্রফেশনাল ফটোগ্রাফি সার্ভিস');
define('PHOTOGRAPHER_NAME', 'Sagor');
define('PRICE_PER_PHOTO', '১০');
define('PHONE', '01XXXXXXXXX');
define('WHATSAPP', '01XXXXXXXXX');
define('EMAIL', 'booking@sagor.accountsbazar.com');
define('LOCATION', 'বাংলাদেশ');

// Database & feature constants
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'accounts_sagor');
define('DB_USER', 'accounts_sagor');
define('DB_PASS', '1410689273KK@#');
define('DB_CHARSET', 'utf8mb4');
define('DB_PATH', __DIR__ . '/../db/database.sqlite');
define('FREE_PHOTOS_COUNT', 2);
define('PHOTO_PRICE', 5);

// Weekly schedule: day => [ open, close ] or false if closed
$SCHEDULE = [
    'শনিবার'       => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'রবিবার'       => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'সোমবার'       => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'মঙ্গলবার'     => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'বুধবার'       => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'বৃহস্পতিবার'  => ['সকাল ৯:০০', 'রাত ৮:০০'],
    'শুক্রবার'     => false, // Special booking only
];

$SERVICES = [
    'wedding'   => 'বিয়ের ফটোগ্রাফি',
    'birthday'  => 'জন্মদিনের ফটোগ্রাফি',
    'outdoor'   => 'আউটডোর ফটোশুট',
    'portrait'  => 'পোর্ট্রেট ফটোগ্রাফি',
    'event'     => 'ইভেন্ট কভারেজ',
    'family'    => 'ফ্যামিলি ফটোশুট',
    'commercial'=> 'কমার্শিয়াল ফটোগ্রাফি',
];
