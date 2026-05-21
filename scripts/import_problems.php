<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$file = $argv[1] ?? '';
if (!$file || !is_file($file)) {
    exit("Usage: php scripts/import_problems.php problems.json\n");
}

$items = json_decode((string) file_get_contents($file), true);
if (!is_array($items)) {
    exit("Invalid JSON\n");
}

foreach ($items as $item) {
    execute_query(
        'INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
         ON DUPLICATE KEY UPDATE chapter_id=VALUES(chapter_id), book_number=VALUES(book_number), difficulty=VALUES(difficulty), problem_type=VALUES(problem_type), sort_order=VALUES(sort_order), is_published=VALUES(is_published), updated_at=NOW()',
        [$item['chapter_id'], $item['problem_code'], $item['book_number'] ?? null, $item['difficulty'] ?? 1, $item['problem_type'] ?? 'mixed', $item['sort_order'] ?? 0, !empty($item['is_published']) ? 1 : 0]
    );
    $problem = fetch_one('SELECT id FROM problems WHERE problem_code = ?', [$item['problem_code']]);
    foreach (($item['texts'] ?? []) as $lang => $text) {
        execute_query(
            'INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE title=VALUES(title), statement_html=VALUES(statement_html), hint_html=VALUES(hint_html), solution_html=VALUES(solution_html), teacher_note_html=VALUES(teacher_note_html)',
            [$problem['id'], $lang, $text['title'] ?? '', $text['statement_html'] ?? '', $text['hint_html'] ?? '', $text['solution_html'] ?? '', $text['teacher_note_html'] ?? '']
        );
    }
}

echo "Imported " . count($items) . " problems\n";

