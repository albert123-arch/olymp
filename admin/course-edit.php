<?php
require_once __DIR__ . '/_bootstrap.php';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    execute_query('UPDATE courses SET slug = ?, sort_order = ?, is_published = ?, updated_at = NOW() WHERE id = ?', [(string) $_POST['slug'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
    foreach (admin_languages() as $lang) {
        $code = $lang['code'];
        $description = $_POST["description_$code"] ?? '';
        execute_query('INSERT INTO course_texts (course_id, lang, title, description_html) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), description_html = VALUES(description_html)', [$id, $code, $_POST["title_$code"] ?? '', $description]);
    }
    header('Location: ' . url('admin/courses.php'));
    exit;
}
$course = fetch_one('SELECT * FROM courses WHERE id = ?', [$id]);
$texts = fetch_all('SELECT * FROM course_texts WHERE course_id = ?', [$id]);
$byLang = array_column($texts, null, 'lang');
admin_header(t('edit'));
?>
<form class="admin-panel p-3" method="post">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= e((string) $id) ?>">
<div class="row g-2 mb-3"><div class="col-md-6"><input class="form-control" name="slug" value="<?= e($course['slug']) ?>"></div><div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="<?= e((string) $course['sort_order']) ?>"></div><div class="col-md-4 form-check pt-2"><input class="form-check-input" name="is_published" type="checkbox" <?= $course['is_published'] ? 'checked' : '' ?>> <label class="form-check-label"><?= e(t('publish')) ?></label></div></div>
<?php foreach (admin_languages() as $lang): $code = $lang['code']; $txt = $byLang[$code] ?? []; ?>
<h2 class="h5"><?= e(strtoupper($code)) ?></h2>
<label class="form-label w-100"><?= e(t('title')) ?><input class="form-control" name="title_<?= e($code) ?>" value="<?= e($txt['title'] ?? '') ?>"></label>
<label class="form-label w-100"><?= e(t('description')) ?><?= admin_textarea("description_$code", $txt['description_html'] ?? '', 4) ?></label>
<?php endforeach; ?>
<button class="btn btn-accent"><?= e(t('save')) ?></button>
</form>
<?php admin_footer(); ?>
