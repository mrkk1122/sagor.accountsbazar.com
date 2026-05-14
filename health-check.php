<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$key = defined('OPENROUTER_API_KEY') ? (string) OPENROUTER_API_KEY : '';
$keyLoaded = $key !== '';
$keyFormatValid = $keyLoaded && strpos($key, 'sk-or-v1-') === 0;

$keySource = 'none';
$rawEnv = getenv('OPENROUTER_API_KEY');
$rawServer = isset($_SERVER['OPENROUTER_API_KEY']) ? (string)$_SERVER['OPENROUTER_API_KEY'] : '';
$rawDotEnv = isset($_ENV['OPENROUTER_API_KEY']) ? (string)$_ENV['OPENROUTER_API_KEY'] : '';

if ($rawEnv !== false && trim((string)$rawEnv) !== '') {
    $keySource = stripos((string)$rawEnv, 'your_openrouter_api_key') !== false ? 'getenv-placeholder' : 'getenv';
} elseif (isset($_SERVER['OPENROUTER_API_KEY']) && trim((string)$_SERVER['OPENROUTER_API_KEY']) !== '') {
    $keySource = stripos((string)$_SERVER['OPENROUTER_API_KEY'], 'your_openrouter_api_key') !== false ? '$_SERVER-placeholder' : '$_SERVER';
} elseif (isset($_ENV['OPENROUTER_API_KEY']) && trim((string)$_ENV['OPENROUTER_API_KEY']) !== '') {
    $keySource = stripos((string)$_ENV['OPENROUTER_API_KEY'], 'your_openrouter_api_key') !== false ? '$_ENV-placeholder' : '$_ENV';
} elseif (is_file(__DIR__ . '/includes/.openrouter.key')) {
    $keySource = 'includes/.openrouter.key';
} elseif (is_file(__DIR__ . '/.openrouter.key')) {
    $keySource = '.openrouter.key';
} elseif (is_file(__DIR__ . '/includes/.openrouter.key.txt')) {
    $keySource = 'includes/.openrouter.key.txt';
} elseif (is_file(__DIR__ . '/.openrouter.key.txt')) {
    $keySource = '.openrouter.key.txt';
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
        'raw_env_has_value' => ($rawEnv !== false && trim((string)$rawEnv) !== ''),
        'raw_server_has_value' => trim($rawServer) !== '',
        'raw_dotenv_has_value' => trim($rawDotEnv) !== '',
        'curl_extension_loaded' => function_exists('curl_init'),
        'php_version' => PHP_VERSION,
    ],
    'next' => ($keyLoaded && $keyFormatValid)
        ? 'API key loaded. You can now test AI generation from /ai_photo_genaretor.php'
        : 'API key missing/invalid. Set OPENROUTER_API_KEY in env or create includes/.openrouter.key(.txt), then recheck health-check.php',
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
