<?php
require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL UNIQUE,
        email TEXT DEFAULT '',
        password TEXT NOT NULL,
        balance REAL DEFAULT 0,
        is_admin INTEGER DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        service TEXT NOT NULL,
        booking_date TEXT NOT NULL,
        booking_time TEXT NOT NULL,
        details TEXT DEFAULT '',
        status TEXT DEFAULT 'pending',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        filename TEXT NOT NULL,
        category TEXT DEFAULT 'general',
        is_free INTEGER DEFAULT 0,
        price REAL DEFAULT 5,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photo_downloads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        photo_id INTEGER NOT NULL,
        amount_paid REAL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL DEFAULT ''
    )");

    // Seed default settings
    $defaults = [
        'site_name'         => 'Sagor Photography',
        'price_per_photo'   => '10',
        'free_photos_count' => '2',
        'phone'             => '01XXXXXXXXX',
        'whatsapp'          => '01XXXXXXXXX',
        'email'             => 'booking@sagor.accountsbazar.com',
        'location'          => 'বাংলাদেশ',
    ];
    $ins = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
    foreach ($defaults as $k => $v) $ins->execute([$k, $v]);

    // Seed admin with a random password on first run
    $hasAdmin = $pdo->query("SELECT id FROM users WHERE is_admin=1")->fetch();
    if (!$hasAdmin) {
        $randPass  = bin2hex(random_bytes(10));
        $credFile  = $dir . '/admin_credentials.txt';
        $pdo->prepare("INSERT INTO users (name, phone, password, is_admin) VALUES (?,?,?,1)")
            ->execute(['Admin', '01700000000', password_hash($randPass, PASSWORD_DEFAULT)]);
        file_put_contents($credFile,
            "Default Admin Login (delete this file after first login)\n" .
            "Phone: 01700000000\n" .
            "Password: $randPass\n", LOCK_EX);
        @chmod($credFile, 0600);
    }

    return $pdo;
}

/** Read a single setting value from DB, or $default if missing */
function get_setting(string $key, string $default = ''): string {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    $stmt = get_db()->prepare("SELECT value FROM settings WHERE key=?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    $cache[$key] = ($val !== false) ? $val : $default;
    return $cache[$key];
}
