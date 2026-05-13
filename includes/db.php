<?php
require_once __DIR__ . '/config.php';

function init_sqlite_schema(PDO $pdo): void {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL UNIQUE,
        email TEXT DEFAULT '',
        profile_photo TEXT DEFAULT '',
        password TEXT NOT NULL,
        balance REAL DEFAULT 0 CHECK (balance >= 0),
        is_admin INTEGER DEFAULT 0 CHECK (is_admin IN (0, 1)),
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
        status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'completed', 'cancelled')),
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        booking_id INTEGER,
        title TEXT NOT NULL,
        filename TEXT NOT NULL,
        category TEXT DEFAULT 'general',
        is_free INTEGER DEFAULT 0 CHECK (is_free IN (0, 1)),
        price REAL DEFAULT 5 CHECK (price >= 0),
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photo_downloads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        photo_id INTEGER NOT NULL,
        amount_paid REAL DEFAULT 0 CHECK (amount_paid >= 0),
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
        UNIQUE (user_id, photo_id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL DEFAULT ''
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS help_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT DEFAULT '',
        message TEXT NOT NULL,
        status TEXT DEFAULT 'new' CHECK (status IN ('new', 'seen', 'resolved')),
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        otp_hash TEXT NOT NULL,
        expires_at TEXT NOT NULL,
        used INTEGER DEFAULT 0 CHECK (used IN (0, 1)),
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS balance_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        amount REAL NOT NULL CHECK (amount > 0),
        note TEXT DEFAULT '',
        status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'rejected')),
        admin_note TEXT DEFAULT '',
        confirmed_by INTEGER,
        confirmed_at TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_bookings_user_id ON bookings(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_photo_downloads_user_id ON photo_downloads(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_photo_downloads_photo_id ON photo_downloads(photo_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_help_requests_status ON help_requests(status)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_user_used ON password_resets(user_id, used)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_balance_requests_user_status ON balance_requests(user_id, status)");
}

function init_mysql_schema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(191) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(191) NOT NULL DEFAULT '',
        profile_photo VARCHAR(255) NOT NULL DEFAULT '',
        password VARCHAR(255) NOT NULL,
        balance DECIMAL(12,2) NOT NULL DEFAULT 0,
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_users_phone (phone)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(191) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        service VARCHAR(191) NOT NULL,
        booking_date DATE NOT NULL,
        booking_time TIME NOT NULL,
        details TEXT,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_bookings_user_id (user_id),
        KEY idx_bookings_status (status),
        CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photos (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        booking_id BIGINT UNSIGNED NULL,
        title VARCHAR(191) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL DEFAULT 'general',
        is_free TINYINT(1) NOT NULL DEFAULT 0,
        price DECIMAL(12,2) NOT NULL DEFAULT 5,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_photos_user_id (user_id),
        KEY idx_photos_booking_id (booking_id),
        CONSTRAINT fk_photos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        CONSTRAINT fk_photos_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS photo_downloads (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        photo_id BIGINT UNSIGNED NOT NULL,
        amount_paid DECIMAL(12,2) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_photo_downloads_user_photo (user_id, photo_id),
        KEY idx_photo_downloads_user_id (user_id),
        KEY idx_photo_downloads_photo_id (photo_id),
        CONSTRAINT fk_photo_downloads_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_photo_downloads_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` VARCHAR(100) NOT NULL,
        `value` TEXT NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS help_requests (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        name VARCHAR(191) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(191) NOT NULL DEFAULT '',
        message TEXT NOT NULL,
        status ENUM('new', 'seen', 'resolved') NOT NULL DEFAULT 'new',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_help_requests_status (status),
        CONSTRAINT fk_help_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        otp_hash VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        used TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_password_resets_user_used (user_id, used),
        CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS balance_requests (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        note TEXT,
        status ENUM('pending', 'confirmed', 'rejected') NOT NULL DEFAULT 'pending',
        admin_note TEXT,
        confirmed_by BIGINT UNSIGNED NULL,
        confirmed_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_balance_requests_user_status (user_id, status),
        CONSTRAINT fk_balance_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_balance_requests_admin FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL,
        CHECK (amount > 0)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function ensure_photos_columns(PDO $pdo): void {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    try {
        if ($driver === 'mysql') {
            $dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
            if ($dbName === '') {
                return;
            }

            $check = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='photos' AND COLUMN_NAME=?");

            $check->execute([$dbName, 'user_id']);
            if ((int)$check->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE photos ADD COLUMN user_id BIGINT UNSIGNED NULL");
            }

            $check->execute([$dbName, 'booking_id']);
            if ((int)$check->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE photos ADD COLUMN booking_id BIGINT UNSIGNED NULL");
            }
        } else {
            $cols = [];
            $rows = $pdo->query("PRAGMA table_info(photos)")->fetchAll();
            foreach ($rows as $r) {
                $cols[$r['name']] = true;
            }

            if (!isset($cols['user_id'])) {
                $pdo->exec("ALTER TABLE photos ADD COLUMN user_id INTEGER");
            }
            if (!isset($cols['booking_id'])) {
                $pdo->exec("ALTER TABLE photos ADD COLUMN booking_id INTEGER");
            }
        }
    } catch (Throwable $e) {
        error_log('[DB] ensure_photos_columns failed: ' . $e->getMessage());
    }
}

function ensure_users_profile_photo_column(PDO $pdo): void {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    try {
        if ($driver === 'mysql') {
            $dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
            if ($dbName === '') {
                return;
            }

            $check = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='users' AND COLUMN_NAME='profile_photo'");
            $check->execute([$dbName]);
            if ((int)$check->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) NOT NULL DEFAULT ''");
            }
        } else {
            $cols = [];
            $rows = $pdo->query("PRAGMA table_info(users)")->fetchAll();
            foreach ($rows as $r) {
                $cols[$r['name']] = true;
            }

            if (!isset($cols['profile_photo'])) {
                $pdo->exec("ALTER TABLE users ADD COLUMN profile_photo TEXT DEFAULT ''");
            }
        }
    } catch (Throwable $e) {
        error_log('[DB] ensure_users_profile_photo_column failed: ' . $e->getMessage());
    }
}

function upsert_setting(PDO $pdo, string $key, string $value): void {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        $stmt->execute([$key, $value]);
        return;
    }

    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
    $stmt->execute([$key, $value]);
}

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $driver = defined('DB_DRIVER') ? DB_DRIVER : 'sqlite';
    $mysqlError = null;

    if ($driver === 'mysql') {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            init_mysql_schema($pdo);
            ensure_photos_columns($pdo);
            ensure_users_profile_photo_column($pdo);
        } catch (Throwable $e) {
            $mysqlError = $e->getMessage();
            error_log('[DB] MySQL failed, trying SQLite fallback: ' . $mysqlError);
            $pdo = null;
        }
    }

    if ($pdo === null) {
        try {
            $dir = dirname(DB_PATH);
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            init_sqlite_schema($pdo);
            ensure_photos_columns($pdo);
            ensure_users_profile_photo_column($pdo);
        } catch (Throwable $e) {
            // Both MySQL and SQLite failed — show a safe error page
            $reason = $mysqlError ?? $e->getMessage();
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><title>ডেটাবেজ সমস্যা</title>'
               . '<style>body{font-family:sans-serif;text-align:center;padding:60px;}'
               . 'h1{color:#c0392b;}p{color:#555;}</style></head><body>'
               . '<h1>সাইট সাময়িকভাবে অনুপলব্ধ</h1>'
               . '<p>ডেটাবেজে সংযোগ করা সম্ভব হচ্ছে না। অনুগ্রহ করে কিছুক্ষণ পরে চেষ্টা করুন।</p>'
               . '<!-- DB_ERR:' . htmlspecialchars($reason, ENT_QUOTES) . ' -->'
               . '</body></html>';
            exit;
        }
    }

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
    foreach ($defaults as $k => $v) upsert_setting($pdo, $k, $v);

    // Seed admin with a random password on first run
    $hasAdmin = $pdo->query("SELECT id FROM users WHERE is_admin=1")->fetch();
    if (!$hasAdmin) {
        $randPass  = bin2hex(random_bytes(10));
        $credFile  = __DIR__ . '/../db/admin_credentials.txt';
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
    $db = get_db();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key`=?");
    } else {
        $stmt = $db->prepare("SELECT value FROM settings WHERE key=?");
    }
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    $cache[$key] = ($val !== false) ? $val : $default;
    return $cache[$key];
}
