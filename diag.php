<?php
/**
 * DIAGNOSTIC PAGE — DELETE AFTER USE
 * Open: https://yourdomain.com/diag.php
 */
if (!isset($_GET['k']) || $_GET['k'] !== 'sagor2026') {
    http_response_code(403); exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP ===\n";
echo "Version   : " . PHP_VERSION . "\n";
echo "SAPI      : " . php_sapi_name() . "\n";
echo "OS        : " . PHP_OS . "\n\n";

echo "=== Extensions ===\n";
foreach (['pdo', 'pdo_mysql', 'pdo_sqlite', 'mbstring', 'json', 'session'] as $ext) {
    echo str_pad($ext, 15) . ": " . (extension_loaded($ext) ? "OK" : "MISSING") . "\n";
}
echo "\n";

echo "=== Config ===\n";
require_once __DIR__ . '/includes/config.php';
echo "DB_DRIVER  : " . (defined('DB_DRIVER')  ? DB_DRIVER  : 'N/A') . "\n";
echo "DB_HOST    : " . (defined('DB_HOST')    ? DB_HOST    : 'N/A') . "\n";
echo "DB_NAME    : " . (defined('DB_NAME')    ? DB_NAME    : 'N/A') . "\n";
echo "DB_USER    : " . (defined('DB_USER')    ? DB_USER    : 'N/A') . "\n";
echo "DB_PATH    : " . (defined('DB_PATH')    ? DB_PATH    : 'N/A') . "\n";
echo "DB dir writable: " . (is_writable(dirname(DB_PATH)) ? "YES" : "NO") . "\n\n";

echo "=== MySQL Connection ===\n";
if (defined('DB_HOST') && defined('DB_NAME')) {
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        $p = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "Status : CONNECTED\n";
        echo "Server : " . $p->query('SELECT VERSION()')->fetchColumn() . "\n";
    } catch (Throwable $e) {
        echo "Status : FAILED\n";
        echo "Error  : " . $e->getMessage() . "\n";
    }
}
echo "\n";

echo "=== SQLite Fallback ===\n";
if (extension_loaded('pdo_sqlite')) {
    $path = defined('DB_PATH') ? DB_PATH : sys_get_temp_dir() . '/test.sqlite';
    try {
        $p2 = new PDO('sqlite:' . $path);
        echo "Status : OK  (path: $path)\n";
    } catch (Throwable $e) {
        echo "Status : FAILED — " . $e->getMessage() . "\n";
    }
} else {
    echo "pdo_sqlite NOT available\n";
}
echo "\n";

echo "=== .htaccess ===\n";
$ht = __DIR__ . '/.htaccess';
echo "Exists : " . (file_exists($ht) ? "YES" : "NO") . "\n";

echo "\n=== DONE — delete diag.php after use ===\n";
