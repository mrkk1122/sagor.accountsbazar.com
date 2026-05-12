<?php
require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

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
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(191) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(191) NOT NULL DEFAULT '',
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
        title VARCHAR(191) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL DEFAULT 'general',
        is_free TINYINT(1) NOT NULL DEFAULT 0,
        price DECIMAL(12,2) NOT NULL DEFAULT 5,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
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
    $ins = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    foreach ($defaults as $k => $v) $ins->execute([$k, $v]);

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
    $stmt = get_db()->prepare("SELECT `value` FROM settings WHERE `key`=?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    $cache[$key] = ($val !== false) ? $val : $default;
    return $cache[$key];
}
