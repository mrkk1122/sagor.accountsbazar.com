<?php
require_once __DIR__ . '/db.php';

function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function current_user(): ?array {
    start_session();
    if (empty($_SESSION['user_id'])) return null;
    $stmt = get_db()->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([(int)$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function require_login(string $redirect = '/login.php'): void {
    if (!current_user()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function is_admin(): bool {
    $u = current_user();
    return $u && (bool)$u['is_admin'];
}

function require_admin(): void {
    start_session();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
    $stmt = get_db()->prepare("SELECT * FROM users WHERE id=? AND is_admin=1");
    $stmt->execute([(int)$_SESSION['admin_id']]);
    if (!$stmt->fetch()) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

function validate_bd_phone(string $phone): bool {
    return (bool)preg_match('/^01[3-9]\d{8}$/', preg_replace('/\s+/', '', $phone));
}
