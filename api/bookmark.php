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
$bookmarked = (bool)($input['bookmarked'] ?? false);
if ($problemId <= 0) {
    json_response(['ok' => false, 'error' => 'bad_problem'], 400);
}

$exists = db()->prepare('SELECT id FROM problems WHERE id = ? AND is_published = 1 LIMIT 1');
$exists->execute([$problemId]);
if (!$exists->fetchColumn()) {
    json_response(['ok' => false, 'error' => 'not_found'], 404);
}

if ($bookmarked) {
    $stmt = db()->prepare('INSERT IGNORE INTO bookmarks (user_id, problem_id) VALUES (?, ?)');
    $stmt->execute([(int)$user['id'], $problemId]);
} else {
    $stmt = db()->prepare('DELETE FROM bookmarks WHERE user_id = ? AND problem_id = ?');
    $stmt->execute([(int)$user['id'], $problemId]);
}

json_response(['ok' => true, 'bookmarked' => $bookmarked]);
