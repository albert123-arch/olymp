<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_content_manager();
if (db_available() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        db()->prepare('UPDATE courses SET slug=?, status=?, sort_order=?, is_published=? WHERE id=?')->execute([$_POST['slug'], $_POST['status'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
        foreach (SUPPORTED_LANGS as $lang) {
            db()->prepare('REPLACE INTO course_texts (course_id, lang, title, summary_html, overview_html, teacher_guide_html) VALUES (?,?,?,?,?,?)')
                ->execute([$id, $lang, $_POST["title_$lang"], $_POST["summary_$lang"], $_POST["overview_$lang"], $_POST["teacher_$lang"]]);
        }
    } else {
        db()->prepare('INSERT INTO courses (slug, status, sort_order, is_published) VALUES (?,?,?,?)')->execute([$_POST['slug'], $_POST['status'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
        $id = (int)db()->lastInsertId();
        foreach (SUPPORTED_LANGS as $lang) {
            db()->prepare('INSERT INTO course_texts (course_id, lang, title, summary_html, overview_html, teacher_guide_html) VALUES (?,?,?,?,?,?)')
                ->execute([$id, $lang, $_POST["title_$lang"], $_POST["summary_$lang"], $_POST["overview_$lang"], $_POST["teacher_$lang"]]);
        }
    }
    header('Location: ' . app_url('admin/courses.php'));
    exit;
}
$pageTitle = t('courses') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('courses')) ?></h1>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php $rows = db()->query("SELECT c.*, en.title AS title_en, ru.title AS title_ru FROM courses c LEFT JOIN course_texts en ON en.course_id=c.id AND en.lang='en' LEFT JOIN course_texts ru ON ru.course_id=c.id AND ru.lang='ru' ORDER BY c.sort_order")->fetchAll(); ?>
<div class="table-responsive mb-4"><table class="table align-middle"><thead><tr><th><?= h(t('id')) ?></th><th><?= h(t('slug')) ?></th><th>EN</th><th>RU</th><th><?= h(t('status')) ?></th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= h((string)$row['id']) ?></td><td><?= h($row['slug']) ?></td><td><?= h($row['title_en']) ?></td><td><?= h($row['title_ru']) ?></td><td><?= h($row['status']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<form method="post" class="card shadow-sm"><div class="card-body row g-3">
  <div class="col-md-4"><label class="form-label"><?= h(t('id')) ?></label><input name="id" class="form-control" placeholder="0"></div>
  <div class="col-md-4"><label class="form-label"><?= h(t('slug')) ?></label><input name="slug" class="form-control" required></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('status')) ?></label><select name="status" class="form-select"><option value="active"><?= h(t('active')) ?></option><option value="coming_soon"><?= h(t('coming_soon_status')) ?></option></select></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('sort_order')) ?></label><input name="sort_order" type="number" class="form-control" value="0"></div>
  <div class="col-12"><label class="form-check"><input class="form-check-input" name="is_published" type="checkbox" checked> <?= h(t('published')) ?></label></div>
  <?php foreach (SUPPORTED_LANGS as $lang): ?>
    <div class="col-md-6"><label class="form-label"><?= h(t('title')) ?> <?= h($lang) ?></label><input name="title_<?= h($lang) ?>" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('summary')) ?> <?= h($lang) ?></label><textarea name="summary_<?= h($lang) ?>" class="form-control"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('overview')) ?> <?= h($lang) ?></label><textarea name="overview_<?= h($lang) ?>" class="form-control" rows="4"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('teacher_guide')) ?> <?= h($lang) ?></label><textarea name="teacher_<?= h($lang) ?>" class="form-control" rows="4"></textarea></div>
  <?php endforeach; ?>
  <div class="col-12"><button class="btn btn-primary"><?= h(t('save')) ?></button></div>
</div></form>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
