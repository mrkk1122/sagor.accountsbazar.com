<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mailer.php';

/**
 * Booking form handler
 * Returns ['success' => bool, 'message' => string]
 */
function handle_booking(): array {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['success' => false, 'message' => ''];
    }

    // Require login
    $user = current_user();
    if (!$user) {
        return ['success' => false, 'message' => 'বুকিং করতে প্রথমে লগইন করুন।'];
    }

    // Read raw input first; htmlspecialchars is applied only when writing to HTML output
    $name    = trim($_POST['name']    ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $service = trim($_POST['service'] ?? '');
    $date    = trim($_POST['date']    ?? '');
    $time    = trim($_POST['time']    ?? '');
    $details = trim($_POST['details'] ?? '');

    if (!$name || !$phone || !$service || !$date || !$time) {
        return ['success' => false, 'message' => 'সকল প্রয়োজনীয় তথ্য পূরণ করুন।'];
    }

    if (!preg_match('/^01[3-9]\d{8}$/', preg_replace('/\s+/', '', $phone))) {
        return ['success' => false, 'message' => 'সঠিক বাংলাদেশি মোবাইল নাম্বার দিন (01XXXXXXXXX)।'];
    }

    $booking_date = DateTime::createFromFormat('Y-m-d', $date);
    if (!$booking_date || $booking_date < new DateTime('today')) {
        return ['success' => false, 'message' => 'অনুগ্রহ করে ভবিষ্যতের একটি তারিখ নির্বাচন করুন।'];
    }

    // Sanitise values before writing to the log
    $sanitise = function(string $v): string { return preg_replace('/[\r\n\t|]+/', ' ', $v); };

    // Save to log file
    $log_dir = __DIR__ . '/../bookings';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $entry = implode(' | ', [
        date('Y-m-d H:i:s'),
        'UserID: '  . $user['id'],
        'Name: '    . $sanitise($name),
        'Phone: '   . $sanitise($phone),
        'Service: ' . $sanitise($service),
        'Date: '    . $sanitise($date),
        'Time: '    . $sanitise($time),
        'Details: ' . $sanitise($details),
    ]) . PHP_EOL;

    file_put_contents($log_dir . '/bookings.log', $entry, FILE_APPEND | LOCK_EX);

    // Save to database
    get_db()->prepare(
        "INSERT INTO bookings (user_id, name, phone, service, booking_date, booking_time, details) VALUES (?,?,?,?,?,?,?)"
    )->execute([$user['id'], $name, $phone, $service, $date, $time, $details]);

    // Send booking confirmation email if user has an email address
    if (!empty($user['email']) && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeService = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
        $safeDate = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
        $safeTime = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');

        $subject = 'Booking Confirmation - ' . SITE_NAME;
        $html = '<h3>আপনার বুকিং কনফার্ম হয়েছে</h3>'
              . '<p>ধন্যবাদ ' . $safeName . ', আপনার বুকিং অনুরোধ আমরা পেয়েছি।</p>'
              . '<p><strong>সার্ভিস:</strong> ' . $safeService . '<br>'
              . '<strong>তারিখ:</strong> ' . $safeDate . '<br>'
              . '<strong>সময়:</strong> ' . $safeTime . '<br>'
              . '<strong>ফোন:</strong> ' . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . '</p>'
              . '<p>খুব দ্রুত আমরা আপনার সাথে যোগাযোগ করব।</p>';
        $text = "আপনার বুকিং কনফার্ম হয়েছে\n"
              . "সার্ভিস: {$service}\nতারিখ: {$date}\nসময়: {$time}\nফোন: {$phone}\n";

        smtp_send_mail($user['email'], $subject, $html, $text);
    }

    // Build WhatsApp URL for admin notification
    $adminWa = preg_replace('/[^0-9]/', '', get_setting('whatsapp', WHATSAPP));
    if ($adminWa !== '' && $adminWa[0] === '0') {
        $adminWa = '88' . $adminWa;
    }
    $adminPhone = get_setting('phone', PHONE);
    $waLines = [
        '🔔 নতুন বুকিং এসেছে!',
        '',
        '👤 নাম: ' . $name,
        '📞 ফোন: ' . $phone,
        '📷 সার্ভিস: ' . $service,
        '📅 তারিখ: ' . $date,
        '⏰ সময়: ' . $time,
        '📝 বিস্তারিত: ' . ($details ?: 'N/A'),
    ];
    $waText = rawurlencode(implode("\n", $waLines));
    $whatsappUrl = ($adminWa !== '') ? ('https://wa.me/' . $adminWa . '?text=' . $waText) : '';

    return [
        'success'      => true,
        'message'      => 'ধন্যবাদ ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! আপনার বুকিং অনুরোধ সফলভাবে গৃহীত হয়েছে। শীঘ্রই ' . htmlspecialchars($adminPhone, ENT_QUOTES, 'UTF-8') . ' নম্বরে যোগাযোগ করা হবে।',
        'booking_name'     => htmlspecialchars($name,    ENT_QUOTES, 'UTF-8'),
        'booking_phone'    => htmlspecialchars($phone,   ENT_QUOTES, 'UTF-8'),
        'booking_service'  => htmlspecialchars($service, ENT_QUOTES, 'UTF-8'),
        'booking_date'     => htmlspecialchars($date,    ENT_QUOTES, 'UTF-8'),
        'booking_time'     => htmlspecialchars($time,    ENT_QUOTES, 'UTF-8'),
        'booking_details'  => htmlspecialchars($details, ENT_QUOTES, 'UTF-8'),
        'admin_phone'      => htmlspecialchars($adminPhone, ENT_QUOTES, 'UTF-8'),
        'whatsapp_url'     => $whatsappUrl,
    ];
}

