<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$key = defined('OPENROUTER_API_KEY') ? (string) OPENROUTER_API_KEY : '';
$keyLoaded = $key !== '';
$keyFormatValid = $keyLoaded && strpos($key, 'sk-or-v1-') === 0;

$keySource = 'none';
if (getenv('OPENROUTER_API_KEY') !== false && trim((string)getenv('OPENROUTER_API_KEY')) !== '') {
    $keySource = 'getenv';
} elseif (isset($_SERVER['OPENROUTER_API_KEY']) && trim((string)$_SERVER['OPENROUTER_API_KEY']) !== '') {
    $keySource = '$_SERVER';
} elseif (isset($_ENV['OPENROUTER_API_KEY']) && trim((string)$_ENV['OPENROUTER_API_KEY']) !== '') {
    $keySource = '$_ENV';
}

$maskedKey = '';
if ($keyLoaded) {
    $prefix = substr($key, 0, 10);
    $suffix = substr($key, -6);
    $maskedKey = $prefix . '...' . $suffix;
}

$response = [
    'ok' => true,
    'service' => 'Sagor AI Health Check',
    'time' => date('c'),
    'checks' => [
        'openrouter_api_url' => defined('OPENROUTER_API_URL') ? OPENROUTER_API_URL : '',
        'openrouter_key_loaded' => $keyLoaded,
        'openrouter_key_format_valid' => $keyFormatValid,
        'openrouter_key_masked' => $maskedKey,
        'openrouter_key_source' => $keySource,
        'curl_extension_loaded' => function_exists('curl_init'),
        'php_version' => PHP_VERSION,
    ],
    'next' => ($keyLoaded && $keyFormatValid)
        ? 'API key loaded. You can now test AI generation from /ai_photo_genaretor.php'
        : 'API key missing/invalid. Set OPENROUTER_API_KEY in Apache env, restart Apache, then recheck health-check.php',
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
