<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

start_session();
$user = current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Please login first']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '{}', true);
if (!is_array($payload)) {
    $payload = [];
}

$mode = strtolower(trim((string)($payload['mode'] ?? 'photo')));
if ($mode !== 'photo' && $mode !== 'video') {
    $mode = 'photo';
}

$prompt = trim((string)($payload['prompt'] ?? ''));
$style = trim((string)($payload['style'] ?? 'Realistic'));
$length = trim((string)($payload['length'] ?? '5s'));

if ($prompt === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Prompt is required']);
    exit;
}

if (OPENROUTER_API_KEY === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'OpenRouter API key is missing']);
    exit;
}

$systemMsg = $mode === 'photo'
    ? 'You are an expert image prompt assistant. Return concise, useful generation guidance in Bangla.'
    : 'You are an expert short-video prompt assistant. Return concise, useful generation guidance in Bangla.';

$userMsg = $mode === 'photo'
    ? "Prompt: {$prompt}\nStyle: {$style}\nGive: 1) refined prompt 2) short tips."
    : "Prompt: {$prompt}\nLength: {$length}\nGive: 1) refined video prompt 2) short shot plan in Bangla.";

$request = [
    'model' => 'openai/gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => $systemMsg],
        ['role' => 'user', 'content' => $userMsg],
    ],
    'temperature' => 0.7,
    'max_tokens' => 280,
];

$ch = curl_init(OPENROUTER_API_URL);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . OPENROUTER_API_KEY,
        'Content-Type: application/json',
        'HTTP-Referer: https://sagor.accountsbazar.com',
        'X-Title: Sagor AI Generator',
    ],
    CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

if ($response === false || $curlErr !== '') {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'API connection failed: ' . $curlErr]);
    exit;
}

$data = json_decode($response, true);
if (!is_array($data)) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Invalid API response']);
    exit;
}

if ($status >= 400) {
    $apiErr = (string)($data['error']['message'] ?? ('API failed with status ' . $status));
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => $apiErr]);
    exit;
}

$text = trim((string)($data['choices'][0]['message']['content'] ?? ''));
if ($text === '') {
    $text = $mode === 'photo'
        ? 'Photo prompt তৈরি হয়েছে, আবার চেষ্টা করুন।'
        : 'Video prompt তৈরি হয়েছে, আবার চেষ্টা করুন।';
}

echo json_encode([
    'ok' => true,
    'mode' => $mode,
    'message' => $text,
], JSON_UNESCAPED_UNICODE);
