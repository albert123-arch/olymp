<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$user = current_user();
if (!$user) {
    json_response(['ok' => false, 'error' => 'auth_required'], 401);
}

$input = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($input)) {
    json_response(['ok' => false, 'error' => 'bad_json'], 400);
}

$problemId = (int)($input['problem_id'] ?? 0);
$solved = (bool)($input['solved'] ?? false);
if ($problemId <= 0) {
    json_response(['ok' => false, 'error' => 'bad_problem'], 400);
}

$exists = db()->prepare('SELECT id FROM problems WHERE id = ? AND is_published = 1 LIMIT 1');
$exists->execute([$problemId]);
if (!$exists->fetchColumn()) {
    json_response(['ok' => false, 'error' => 'not_found'], 404);
}

if ($solved) {
    $stmt = db()->prepare(
        'INSERT INTO user_problem_progress (user_id, problem_id, status, attempts, started_at, solved_at)
         VALUES (?, ?, "solved", 1, NOW(), NOW())
         ON DUPLICATE KEY UPDATE status = "solved", attempts = attempts + 1, started_at = COALESCE(started_at, NOW()), solved_at = NOW()'
    );
    $stmt->execute([(int)$user['id'], $problemId]);
    json_response(['ok' => true, 'status' => 'solved']);
}

$stmt = db()->prepare('DELETE FROM user_problem_progress WHERE user_id = ? AND problem_id = ?');
$stmt->execute([(int)$user['id'], $problemId]);

json_response(['ok' => true, 'status' => 'unseen']);
