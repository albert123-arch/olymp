<?php
require_once __DIR__ . '/_bootstrap.php';
$q = trim((string) ($_GET['q'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    execute_query('DELETE FROM problems WHERE id = ?', [(int) $_POST['id']]);
    header('Location: ' . url('admin/problems.php'));
    exit;
}
$params = ['lang' => current_lang()];
$where = ['1=1'];
if ($q !== '') {
    $where[] = '(p.problem_code LIKE :q OR pt.title LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
$problems = fetch_all(
    "SELECT p.*, COALESCE(pt.title, p.problem_code) title, ch.slug chapter_slug, c.slug course_slug
     FROM problems p
     JOIN chapters ch ON ch.id = p.chapter_id
     JOIN courses c ON c.id = ch.course_id
     LEFT JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = :lang
     WHERE " . implode(' AND ', $where) . "
     ORDER BY c.sort_order, ch.sort_order, p.sort_order, p.id",
    $params
);
admin_header(t('practice_problems'));
?>
<h1 class="h3"><?= e(t('practice_problems')) ?></h1>
<form class="admin-panel p-3 mb-3 d-flex gap-2"><input type="hidden" name="lang" value="<?= e(current_lang()) ?>"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="<?= e(t('search')) ?>"><button class="btn btn-accent"><?= e(t('filter')) ?></button><a class="btn btn-outline-secondary" href="<?= e(url('admin/problem-edit.php')) ?>"><?= e(t('edit')) ?></a></form>
<div class="admin-panel p-3"><table class="table table-sm align-middle"><tbody>
<?php foreach ($problems as $p): ?><tr><td><?= e($p['problem_code']) ?></td><td><?= e($p['title']) ?></td><td><?= e($p['course_slug'] . '/' . $p['chapter_slug']) ?></td><td><?= $p['is_published'] ? e(t('published')) : e(t('draft')) ?></td><td><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/problem-edit.php', ['id' => $p['id']])) ?>"><?= e(t('edit')) ?></a></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e((string)$p['id']) ?>"><button class="btn btn-sm btn-outline-danger"><?= e(t('delete')) ?></button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php admin_footer(); ?>
