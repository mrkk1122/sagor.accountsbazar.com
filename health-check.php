<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$key = defined('OPENROUTER_API_KEY') ? (string) OPENROUTER_API_KEY : '';
$keyLoaded = $key !== '';

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
        'openrouter_key_masked' => $maskedKey,
        'curl_extension_loaded' => function_exists('curl_init'),
        'php_version' => PHP_VERSION,
    ],
    'next' => $keyLoaded
        ? 'API key loaded. You can now test AI generation from /ai_photo_genaretor.php'
        : 'API key not found. Set OPENROUTER_API_KEY in Apache env and restart Apache.',
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
