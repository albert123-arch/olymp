<?php
require_once __DIR__ . '/_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        execute_query('DELETE FROM chapters WHERE id = ?', [(int) $_POST['id']]);
    } else {
        execute_query('INSERT INTO chapters (course_id, slug, sort_order, is_published, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())', [(int) $_POST['course_id'], (string) $_POST['slug'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
    }
    header('Location: ' . url('admin/chapters.php'));
    exit;
}
$courses = fetch_all('SELECT id, slug FROM courses ORDER BY sort_order');
$chapters = fetch_all('SELECT ch.*, c.slug course_slug, COALESCE(ct.title, ch.slug) title FROM chapters ch JOIN courses c ON c.id = ch.course_id LEFT JOIN chapter_texts ct ON ct.chapter_id = ch.id AND ct.lang = ? ORDER BY c.sort_order, ch.sort_order', [current_lang()]);
admin_header(t('chapters'));
?>
<h1 class="h3"><?= e(t('chapters')) ?></h1>
<form class="admin-panel p-3 row g-2 mb-3" method="post"><?= csrf_field() ?>
<div class="col-md-3"><select class="form-select" name="course_id"><?php foreach ($courses as $c): ?><option value="<?= e((string) $c['id']) ?>"><?= e($c['slug']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><input class="form-control" name="slug" placeholder="<?= e(t('slug')) ?>" required></div><div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="0"></div>
<div class="col-md-3"><button class="btn btn-accent w-100"><?= e(t('save')) ?></button></div>
</form>
<div class="admin-panel p-3"><table class="table table-sm"><tbody><?php foreach ($chapters as $ch): ?><tr><td><?= e($ch['course_slug']) ?></td><td><?= e($ch['title']) ?></td><td><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/chapter-edit.php', ['id' => $ch['id']])) ?>"><?= e(t('edit')) ?></a></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$ch['id']) ?>"><button class="btn btn-sm btn-outline-danger"><?= e(t('delete')) ?></button></form></td></tr><?php endforeach; ?></tbody></table></div>
<?php admin_footer(); ?>
