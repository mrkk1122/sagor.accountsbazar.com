<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

header('Content-Type: application/json; charset=utf-8');

$db = get_db();
$sinceBooking = (int)($_GET['since_booking'] ?? 0);
$sinceHelp = (int)($_GET['since_help'] ?? 0);

$latestBooking = (int)$db->query("SELECT COALESCE(MAX(id),0) FROM bookings")->fetchColumn();
$latestHelp = (int)$db->query("SELECT COALESCE(MAX(id),0) FROM help_requests")->fetchColumn();

$newBookingCount = 0;
$newHelpCount = 0;

if ($sinceBooking > 0) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE id > ?");
    $stmt->execute([$sinceBooking]);
    $newBookingCount = (int)$stmt->fetchColumn();
}

if ($sinceHelp > 0) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM help_requests WHERE id > ?");
    $stmt->execute([$sinceHelp]);
    $newHelpCount = (int)$stmt->fetchColumn();
}

echo json_encode([
    'ok' => true,
    'latest_booking_id' => $latestBooking,
    'latest_help_id' => $latestHelp,
    'new_booking_count' => $newBookingCount,
    'new_help_count' => $newHelpCount,
], JSON_UNESCAPED_UNICODE);
