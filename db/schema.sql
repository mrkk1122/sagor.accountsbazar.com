-- SQLite schema file.
-- If you are using MySQL/MariaDB (phpMyAdmin), use db/schema.mysql.sql instead.
PRAGMA journal_mode = WAL;
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT NOT NULL UNIQUE,
    email TEXT DEFAULT '',
    password TEXT NOT NULL,
    balance REAL DEFAULT 0 CHECK (balance >= 0),
    is_admin INTEGER DEFAULT 0 CHECK (is_admin IN (0, 1)),
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS bookings (
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
);

CREATE TABLE IF NOT EXISTS photos (
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
);

CREATE TABLE IF NOT EXISTS photo_downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    photo_id INTEGER NOT NULL,
    amount_paid REAL DEFAULT 0 CHECK (amount_paid >= 0),
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    UNIQUE (user_id, photo_id)
);

CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS help_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT DEFAULT '',
    message TEXT NOT NULL,
    status TEXT DEFAULT 'new' CHECK (status IN ('new', 'seen', 'resolved')),
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    otp_hash TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    used INTEGER DEFAULT 0 CHECK (used IN (0, 1)),
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS balance_requests (
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
);

CREATE INDEX IF NOT EXISTS idx_bookings_user_id ON bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_photo_downloads_user_id ON photo_downloads(user_id);
CREATE INDEX IF NOT EXISTS idx_photo_downloads_photo_id ON photo_downloads(photo_id);
CREATE INDEX IF NOT EXISTS idx_help_requests_status ON help_requests(status);
CREATE INDEX IF NOT EXISTS idx_password_resets_user_used ON password_resets(user_id, used);
CREATE INDEX IF NOT EXISTS idx_balance_requests_user_status ON balance_requests(user_id, status);

INSERT OR IGNORE INTO settings (key, value) VALUES
    ('site_name', 'Sagor Photography'),
    ('price_per_photo', '10'),
    ('free_photos_count', '2'),
    ('phone', '01XXXXXXXXX'),
    ('whatsapp', '01XXXXXXXXX'),
    ('email', 'booking@sagor.accountsbazar.com'),
    ('location', 'বাংলাদেশ');

-- Default Admin Login
-- Phone: 01700000000
-- Password: Admin@12345
INSERT INTO users (name, phone, email, password, is_admin)
VALUES ('Admin', '01700000000', '', '$2y$10$U9/ZbPDIkcb3Ai2.gRzpZuE2MqvSbKwTyVQeRBQs7d.aaDLxeuRBW', 1)
ON CONFLICT(phone) DO UPDATE SET
    name = excluded.name,
    email = excluded.email,
    password = excluded.password,
    is_admin = 1;
