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

$isMultipart = isset($_SERVER['CONTENT_TYPE']) && stripos((string)$_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false;

$payload = [];
if ($isMultipart) {
    $payload = $_POST;
} else {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ?: '{}', true);
    if (!is_array($payload)) {
        $payload = [];
    }
}

$mode = strtolower(trim((string)($payload['mode'] ?? 'photo_edit')));
if ($mode !== 'photo_edit') {
    $mode = 'photo_edit';
}

$prompt = trim((string)($payload['prompt'] ?? ''));

if ($prompt === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Prompt is required']);
    exit;
}

if (!isset($_FILES['source_image']) || !is_array($_FILES['source_image'])) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Source image upload করা আবশ্যক']);
    exit;
}

$src = $_FILES['source_image'];
if ((int)($src['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Image upload failed']);
    exit;
}

$tmpPath = (string)($src['tmp_name'] ?? '');
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid uploaded file']);
    exit;
}

$maxBytes = 8 * 1024 * 1024;
if ((int)($src['size'] ?? 0) <= 0 || (int)$src['size'] > $maxBytes) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Image size সর্বোচ্চ 8MB হতে হবে']);
    exit;
}

$mime = '';
if (function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string)finfo_file($finfo, $tmpPath) : '';
    if ($finfo) {
        finfo_close($finfo);
    }
}

if ($mime === '' && function_exists('exif_imagetype')) {
    $imgType = @exif_imagetype($tmpPath);
    $typeMap = [
        IMAGETYPE_JPEG => 'image/jpeg',
        IMAGETYPE_PNG => 'image/png',
        IMAGETYPE_WEBP => 'image/webp',
    ];
    if ($imgType && isset($typeMap[$imgType])) {
        $mime = $typeMap[$imgType];
    }
}

$extMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

$allowedExtensions = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'webp' => 'image/webp',
];

if (!isset($extMap[$mime])) {
    // Some shared host environments return empty or generic MIME types.
    // In that case, use file extension fallback for common image formats.
    $originalName = strtolower((string)($src['name'] ?? ''));
    $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);
    if ($fileExt !== '' && isset($allowedExtensions[$fileExt])) {
        $mime = $allowedExtensions[$fileExt];
    } else {
        http_response_code(422);
        echo json_encode([
            'ok' => false,
            'error' => 'Only JPG, PNG, WEBP allowed',
        ]);
        exit;
    }
}

$uploadDir = dirname(__DIR__) . '/uploads/ai-edits';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Upload directory তৈরি করা যায়নি']);
    exit;
}

$filename = 'src_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extMap[$mime];
$targetPath = $uploadDir . '/' . $filename;
if (!move_uploaded_file($tmpPath, $targetPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Uploaded file save করা যায়নি']);
    exit;
}

$seed = random_int(100000, 999999);
$finalPrompt = trim($prompt . ', preserve same person identity, preserve face structure, realistic edit, high quality, do not change subject');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
$sourceUrl = $scheme . '://' . $host . '/uploads/ai-edits/' . rawurlencode($filename);

$imageUrl = 'https://image.pollinations.ai/prompt/' . rawurlencode($finalPrompt)
    . '?width=1024&height=1024&seed=' . $seed
    . '&image=' . rawurlencode($sourceUrl)
    . '&nologo=true';

echo json_encode([
    'ok' => true,
    'mode' => 'photo_edit',
    'provider' => 'pollinations',
    'message' => 'Photo edit complete. নিচে result দেখুন।',
    'source_image_url' => $sourceUrl,
    'image_url' => $imageUrl,
    'seed' => $seed,
    'final_prompt' => $finalPrompt,
], JSON_UNESCAPED_UNICODE);
exit;
