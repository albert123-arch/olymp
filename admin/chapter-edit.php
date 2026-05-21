<?php
require_once __DIR__ . '/_bootstrap.php';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    execute_query('UPDATE chapters SET course_id=?, slug=?, sort_order=?, is_published=?, updated_at=NOW() WHERE id=?', [(int) $_POST['course_id'], (string) $_POST['slug'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
    foreach (admin_languages() as $lang) {
        $c = $lang['code'];
        execute_query('INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title=VALUES(title), description_html=VALUES(description_html), theory_html=VALUES(theory_html), examples_html=VALUES(examples_html), worksheet_html=VALUES(worksheet_html), teacher_notes_html=VALUES(teacher_notes_html)', [$id, $c, $_POST["title_$c"] ?? '', $_POST["description_$c"] ?? '', $_POST["theory_$c"] ?? '', $_POST["examples_$c"] ?? '', $_POST["worksheet_$c"] ?? '', $_POST["teacher_$c"] ?? '']);
    }
    header('Location: ' . url('admin/chapters.php'));
    exit;
}
$chapter = fetch_one('SELECT * FROM chapters WHERE id=?', [$id]);
$courses = fetch_all('SELECT id, slug FROM courses ORDER BY sort_order');
$byLang = array_column(fetch_all('SELECT * FROM chapter_texts WHERE chapter_id=?', [$id]), null, 'lang');
admin_header(t('edit'));
?>
<form class="admin-panel p-3" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e((string) $id) ?>">
<div class="row g-2 mb-3"><div class="col-md-3"><select class="form-select" name="course_id"><?php foreach ($courses as $c): ?><option value="<?= e((string) $c['id']) ?>" <?= $chapter['course_id']==$c['id']?'selected':'' ?>><?= e($c['slug']) ?></option><?php endforeach; ?></select></div><div class="col-md-5"><input class="form-control" name="slug" value="<?= e($chapter['slug']) ?>"></div><div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="<?= e((string) $chapter['sort_order']) ?>"></div><div class="col-md-2 form-check pt-2"><input class="form-check-input" name="is_published" type="checkbox" <?= $chapter['is_published']?'checked':'' ?>></div></div>
<?php foreach (admin_languages() as $lang): $c=$lang['code']; $txt=$byLang[$c]??[]; ?><h2 class="h5"><?= e(strtoupper($c)) ?></h2>
<input class="form-control mb-2" name="title_<?= e($c) ?>" value="<?= e($txt['title'] ?? '') ?>" placeholder="<?= e(t('title')) ?>">
<?= admin_textarea("description_$c", $txt['description_html'] ?? '', 3) ?><?= admin_textarea("theory_$c", $txt['theory_html'] ?? '', 6) ?><?= admin_textarea("examples_$c", $txt['examples_html'] ?? '', 5) ?><?= admin_textarea("worksheet_$c", $txt['worksheet_html'] ?? '', 4) ?><?= admin_textarea("teacher_$c", $txt['teacher_notes_html'] ?? '', 4) ?>
<?php endforeach; ?><button class="btn btn-accent"><?= e(t('save')) ?></button></form>
<?php admin_footer(); ?>
