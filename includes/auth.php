<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id']) || !has_real_config()) {
        return null;
    }
    try {
        return fetch_one('SELECT id, name, email, role FROM users WHERE id = ?', [(int) $_SESSION['user_id']]);
    } catch (Throwable $e) {
        return null;
    }
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_teacher(): bool
{
    $user = current_user();
    return $user && in_array($user['role'], ['teacher', 'admin'], true);
}

function is_admin(): bool
{
    $user = current_user();
    return $user && $user['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . url('login.php'));
        exit;
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function login_user(string $email, string $password): bool
{
    $user = fetch_one('SELECT * FROM users WHERE email = ?', [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    $_SESSION['user_id'] = (int) $user['id'];
    return true;
}

