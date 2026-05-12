PRAGMA journal_mode = WAL;
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT NOT NULL UNIQUE,
    email TEXT DEFAULT '',
    password TEXT NOT NULL,
    balance REAL DEFAULT 0,
    is_admin INTEGER DEFAULT 0,
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
    status TEXT DEFAULT 'pending',
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS photos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    filename TEXT NOT NULL,
    category TEXT DEFAULT 'general',
    is_free INTEGER DEFAULT 0,
    price REAL DEFAULT 5,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS photo_downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    photo_id INTEGER NOT NULL,
    amount_paid REAL DEFAULT 0,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL DEFAULT ''
);

INSERT OR IGNORE INTO settings (key, value) VALUES
    ('site_name', 'Sagor Photography'),
    ('price_per_photo', '10'),
    ('free_photos_count', '2'),
    ('phone', '01XXXXXXXXX'),
    ('whatsapp', '01XXXXXXXXX'),
    ('email', 'booking@sagor.accountsbazar.com'),
    ('location', 'বাংলাদেশ');
