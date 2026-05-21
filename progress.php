<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_logged_in()) {
    http_response_code(403);
    echo json_encode(['ok' => false]);
    exit;
}

verify_csrf();

$user = current_user();
$problemId = (int) ($_POST['problem_id'] ?? 0);
$kind = (string) ($_POST['kind'] ?? '');
$value = (string) ($_POST['value'] ?? '0');

if (!$user || $problemId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false]);
    exit;
}

if ($kind === 'bookmark') {
    save_bookmark((int) $user['id'], $problemId, $value === '1');
} elseif ($kind === 'solved') {
    save_problem_progress((int) $user['id'], $problemId, $value === '1' ? 'solved' : 'viewed');
} else {
    http_response_code(422);
    echo json_encode(['ok' => false]);
    exit;
}

echo json_encode(['ok' => true]);

