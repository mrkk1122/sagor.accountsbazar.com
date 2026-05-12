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
