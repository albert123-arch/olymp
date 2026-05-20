<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
$message = '';
if (db_available() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode((string)($_POST['json'] ?? ''), true);
    if (is_array($data)) {
        $items = array_is_list($data) ? $data : ($data['problems'] ?? []);
        foreach ($items as $item) {
            db()->prepare('INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, sort_order, is_published) VALUES (?,?,?,?,?,?)')
                ->execute([(int)$item['chapter_id'], $item['problem_code'], (int)($item['book_number'] ?? 0), $item['difficulty'] ?? 'core', (int)($item['sort_order'] ?? 0), (int)($item['is_published'] ?? 0)]);
            $problemId = (int)db()->lastInsertId();
            foreach (SUPPORTED_LANGS as $lang) {
                $tx = $item['texts'][$lang] ?? [];
                db()->prepare('INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$problemId, $lang, $tx['title'] ?? '', $tx['statement_html'] ?? '', $tx['hint_html'] ?? '', $tx['solution_html'] ?? '', $tx['teacher_note_html'] ?? '']);
            }
        }
        $message = t('import_json');
    }
}
$pageTitle = t('import_json') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('import_json')) ?></h1>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php if ($message): ?><div class="alert alert-success"><?= h($message) ?></div><?php endif; ?>
<form method="post" class="card shadow-sm"><div class="card-body">
  <textarea name="json" class="form-control font-monospace" rows="18" placeholder='[{"chapter_id":1,"problem_code":"NT-01-041","difficulty":"core","texts":{"en":{"title":"...","statement_html":"<p>...</p>"},"ru":{"title":"...","statement_html":"<p>...</p>"}}}]'></textarea>
  <button class="btn btn-primary mt-3"><?= h(t('import_json')) ?></button>
</div></form>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
