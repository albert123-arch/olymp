<?php
require_once __DIR__ . '/_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        execute_query('DELETE FROM courses WHERE id = ?', [(int) $_POST['id']]);
    } else {
        execute_query('INSERT INTO courses (slug, sort_order, is_published, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', [(string) $_POST['slug'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
    }
    header('Location: ' . url('admin/courses.php'));
    exit;
}
$courses = fetch_all('SELECT c.*, COALESCE(ct.title, c.slug) title FROM courses c LEFT JOIN course_texts ct ON ct.course_id = c.id AND ct.lang = ? ORDER BY c.sort_order', [current_lang()]);
admin_header(t('courses'));
?>
<h1 class="h3"><?= e(t('courses')) ?></h1>
<form class="admin-panel p-3 row g-2 mb-3" method="post">
    <?= csrf_field() ?>
    <div class="col-md-5"><input class="form-control" name="slug" placeholder="<?= e(t('slug')) ?>" required></div>
    <div class="col-md-2"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-2 form-check pt-2"><input class="form-check-input" name="is_published" type="checkbox" checked> <label class="form-check-label"><?= e(t('publish')) ?></label></div>
    <div class="col-md-3"><button class="btn btn-accent w-100"><?= e(t('save')) ?></button></div>
</form>
<div class="admin-panel p-3"><table class="table table-sm align-middle mb-0"><tbody>
<?php foreach ($courses as $course): ?><tr><td><?= e($course['title']) ?></td><td><?= e($course['slug']) ?></td><td><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/course-edit.php', ['id' => $course['id']])) ?>"><?= e(t('edit')) ?></a></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$course['id']) ?>"><button class="btn btn-sm btn-outline-danger"><?= e(t('delete')) ?></button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php admin_footer(); ?>
