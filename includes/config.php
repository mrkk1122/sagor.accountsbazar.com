<?php
// Site Configuration
define('SITE_NAME', 'Sagor Photography');
define('SITE_TAGLINE', 'প্রফেশনাল ফটোগ্রাফি সার্ভিস');
define('PHOTOGRAPHER_NAME', 'Sagor');
define('PRICE_PER_PHOTO', '১০');
define('PHONE', '01790088564');
define('WHATSAPP', '01790088564');
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

// Mail (SMTP over SSL/TLS)
define('MAIL_HOST', 'mail.accountsbazar.com');
define('MAIL_USERNAME', 'sagor@accountsbazar.com');
define('MAIL_PASSWORD', '1410689273KK@#');
define('MAIL_PORT', 465);
define('MAIL_SECURITY', 'ssl'); // ssl for 465
define('MAIL_FROM_EMAIL', 'sagor@accountsbazar.com');
define('MAIL_FROM_NAME', SITE_NAME);

$secretLocalConfig = __DIR__ . '/secret.local.php';
if (is_file($secretLocalConfig)) {
    require_once $secretLocalConfig;
}

if (!function_exists('resolve_openrouter_api_key')) {
    function resolve_openrouter_api_key()
    {
        $candidate = '';

        if (defined('OPENROUTER_API_KEY_OVERRIDE')) {
            $candidate = (string)OPENROUTER_API_KEY_OVERRIDE;
        }

        if ($candidate === '' && getenv('OPENROUTER_API_KEY') !== false) {
            $candidate = (string)getenv('OPENROUTER_API_KEY');
        } elseif ($candidate === '' && isset($_SERVER['OPENROUTER_API_KEY'])) {
            $candidate = (string)$_SERVER['OPENROUTER_API_KEY'];
        } elseif ($candidate === '' && isset($_ENV['OPENROUTER_API_KEY'])) {
            $candidate = (string)$_ENV['OPENROUTER_API_KEY'];
        } elseif ($candidate === '') {
            $fileCandidates = [
                __DIR__ . '/.openrouter.key',
                dirname(__DIR__) . '/.openrouter.key',
                __DIR__ . '/.openrouter.key.txt',
                dirname(__DIR__) . '/.openrouter.key.txt',
            ];

            foreach ($fileCandidates as $keyFile) {
                if (!is_file($keyFile)) {
                    continue;
                }

                $content = @file_get_contents($keyFile);
                if ($content === false) {
                    continue;
                }

                $candidate = (string)$content;
                break;
            }
        }

        $candidate = trim($candidate);
        if (
            $candidate === '' ||
            strtoupper($candidate) === 'YOUR_OPENROUTER_API_KEY' ||
            stripos($candidate, 'your_openrouter_api_key') !== false
        ) {
            return '';
        }

        return $candidate;
    }
}

// AI (OpenRouter)
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
define('OPENROUTER_API_KEY', resolve_openrouter_api_key());

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
