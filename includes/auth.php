<?php
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function current_user()
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

function is_logged_in()
{
    return current_user() !== null;
}

function is_teacher()
{
    $user = current_user();
    return $user && in_array($user['role'], ['teacher', 'admin'], true);
}

function is_admin()
{
    $user = current_user();
    return $user && $user['role'] === 'admin';
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: ' . url('login.php'));
        exit;
    }
}

function require_admin()
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function login_user($email, $password)
{
    $user = fetch_one('SELECT * FROM users WHERE email = ?', [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    $_SESSION['user_id'] = (int) $user['id'];
    return true;
}

