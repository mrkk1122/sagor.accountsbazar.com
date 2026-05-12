-- MySQL/MariaDB schema for phpMyAdmin import
-- Charset/collation
SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(191) NOT NULL DEFAULT '',
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_users_phone (phone),
    CHECK (balance >= 0),
    CHECK (is_admin IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(191) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    is_free TINYINT(1) NOT NULL DEFAULT 0,
    price DECIMAL(12,2) NOT NULL DEFAULT 5,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CHECK (is_free IN (0, 1)),
    CHECK (price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photo_downloads (
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
    CONSTRAINT fk_photo_downloads_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    CHECK (amount_paid >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS help_requests (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_password_resets_user_used (user_id, used),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES
    ('site_name', 'Sagor Photography'),
    ('price_per_photo', '10'),
    ('free_photos_count', '2'),
    ('phone', '01XXXXXXXXX'),
    ('whatsapp', '01XXXXXXXXX'),
    ('email', 'booking@sagor.accountsbazar.com'),
    ('location', 'বাংলাদেশ')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Default Admin Login
-- Phone: 01700000000
-- Password: Admin@12345
INSERT INTO users (name, phone, email, password, is_admin)
VALUES ('Admin', '01700000000', '', '$2y$10$U9/ZbPDIkcb3Ai2.gRzpZuE2MqvSbKwTyVQeRBQs7d.aaDLxeuRBW', 1)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    password = VALUES(password),
    is_admin = 1;

SET FOREIGN_KEY_CHECKS = 1;
