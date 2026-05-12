<?php
/**
 * Booking form handler
 * Returns ['success' => bool, 'message' => string]
 */
function handle_booking(): array {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['success' => false, 'message' => ''];
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

    // Sanitise values before writing to the log (strip control characters / newlines
    // to prevent log-injection attacks)
    $sanitise = fn(string $v): string => preg_replace('/[\r\n\t|]+/', ' ', $v);

    $log_dir = __DIR__ . '/../bookings';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $entry = implode(' | ', [
        date('Y-m-d H:i:s'),
        'Name: '    . $sanitise($name),
        'Phone: '   . $sanitise($phone),
        'Service: ' . $sanitise($service),
        'Date: '    . $sanitise($date),
        'Time: '    . $sanitise($time),
        'Details: ' . $sanitise($details),
    ]) . PHP_EOL;

    file_put_contents($log_dir . '/bookings.log', $entry, FILE_APPEND | LOCK_EX);

    return ['success' => true, 'message' => 'ধন্যবাদ ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! আপনার বুকিং অনুরোধ সফলভাবে গৃহীত হয়েছে। শীঘ্রই ' . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . ' নম্বরে যোগাযোগ করা হবে।'];
}
