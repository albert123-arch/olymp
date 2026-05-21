<?php
require_once __DIR__ . '/_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $media = fetch_one('SELECT * FROM problem_media WHERE id=?', [(int) $_POST['id']]);
        if ($media) {
            @unlink(__DIR__ . '/../' . $media['file_path']);
            execute_query('DELETE FROM problem_media WHERE id=?', [(int) $_POST['id']]);
        }
    } else {
        execute_query('UPDATE problem_media SET role=?, lang=?, sort_order=?, is_published=? WHERE id=?', [(string) $_POST['role'], ($_POST['lang'] ?? '') ?: null, (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, (int) $_POST['id']]);
    }
}
$items = fetch_all('SELECT pm.*, p.problem_code FROM problem_media pm JOIN problems p ON p.id=pm.problem_id ORDER BY pm.created_at DESC');
admin_header(t('media'));
?>
<h1 class="h3"><?= e(t('media')) ?></h1>
<div class="admin-panel p-3"><table class="table table-sm align-middle"><tbody>
<?php foreach ($items as $m): ?><tr><td><img src="<?= e(url($m['file_path'], [])) ?>" style="max-width:90px;max-height:60px" alt=""></td><td><?= e($m['problem_code']) ?><br><span class="text-muted small"><?= e($m['original_name']) ?></span></td><td><form class="row g-1" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e((string)$m['id']) ?>"><div class="col"><select class="form-select form-select-sm" name="role"><?php foreach(['statement','hint','solution','teacher_note','extra'] as $role): ?><option value="<?= e($role) ?>" <?= $m['role']===$role?'selected':'' ?>><?= e(t($role)) ?></option><?php endforeach; ?></select></div><div class="col"><input class="form-control form-control-sm" name="lang" value="<?= e($m['lang'] ?? '') ?>" placeholder="<?= e(t('language')) ?>"></div><div class="col"><input class="form-control form-control-sm" type="number" name="sort_order" value="<?= e((string)$m['sort_order']) ?>"></div><div class="col-auto"><input class="form-check-input" name="is_published" type="checkbox" <?= $m['is_published']?'checked':'' ?>></div><div class="col-auto"><button class="btn btn-sm btn-accent"><?= e(t('save')) ?></button></div></form></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$m['id']) ?>"><button class="btn btn-sm btn-outline-danger"><?= e(t('delete')) ?></button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php admin_footer(); ?>
