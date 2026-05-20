<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
if (db_available() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        db()->prepare('UPDATE chapters SET course_id=?, slug=?, sort_order=?, is_published=? WHERE id=?')->execute([(int)$_POST['course_id'], $_POST['slug'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
    } else {
        db()->prepare('INSERT INTO chapters (course_id, slug, sort_order, is_published) VALUES (?,?,?,?)')->execute([(int)$_POST['course_id'], $_POST['slug'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
        $id = (int)db()->lastInsertId();
    }
    foreach (SUPPORTED_LANGS as $lang) {
        db()->prepare('REPLACE INTO chapter_texts (chapter_id, lang, title, summary_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([$id, $lang, $_POST["title_$lang"], $_POST["summary_$lang"], $_POST["theory_$lang"], $_POST["examples_$lang"], $_POST["worksheet_$lang"], $_POST["teacher_$lang"]]);
    }
    header('Location: ' . app_url('admin/chapters.php'));
    exit;
}
$pageTitle = t('chapters') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('chapters')) ?></h1>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php $courses = db()->query("SELECT c.id, ct.title FROM courses c JOIN course_texts ct ON ct.course_id=c.id AND ct.lang='en' ORDER BY c.sort_order")->fetchAll(); ?>
<?php $rows = db()->query("SELECT ch.*, c.slug course_slug, en.title title_en, ru.title title_ru FROM chapters ch JOIN courses c ON c.id=ch.course_id LEFT JOIN chapter_texts en ON en.chapter_id=ch.id AND en.lang='en' LEFT JOIN chapter_texts ru ON ru.chapter_id=ch.id AND ru.lang='ru' ORDER BY c.sort_order, ch.sort_order")->fetchAll(); ?>
<div class="table-responsive mb-4"><table class="table"><thead><tr><th><?= h(t('id')) ?></th><th><?= h(t('course')) ?></th><th><?= h(t('slug')) ?></th><th>EN</th><th>RU</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= h((string)$row['id']) ?></td><td><?= h($row['course_slug']) ?></td><td><?= h($row['slug']) ?></td><td><?= h($row['title_en']) ?></td><td><?= h($row['title_ru']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<form method="post" class="card shadow-sm"><div class="card-body row g-3">
  <div class="col-md-2"><label class="form-label"><?= h(t('id')) ?></label><input name="id" class="form-control"></div>
  <div class="col-md-4"><label class="form-label"><?= h(t('course')) ?></label><select name="course_id" class="form-select"><?php foreach ($courses as $c): ?><option value="<?= h((string)$c['id']) ?>"><?= h($c['title']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-4"><label class="form-label"><?= h(t('slug')) ?></label><input name="slug" class="form-control" required></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('sort_order')) ?></label><input name="sort_order" type="number" value="0" class="form-control"></div>
  <div class="col-12"><label class="form-check"><input class="form-check-input" name="is_published" type="checkbox" checked> <?= h(t('published')) ?></label></div>
  <?php foreach (SUPPORTED_LANGS as $lang): ?>
    <div class="col-md-6"><label class="form-label"><?= h(t('title')) ?> <?= h($lang) ?></label><input name="title_<?= h($lang) ?>" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('summary')) ?> <?= h($lang) ?></label><textarea name="summary_<?= h($lang) ?>" class="form-control"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('theory')) ?> <?= h($lang) ?></label><textarea name="theory_<?= h($lang) ?>" rows="5" class="form-control"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('examples')) ?> <?= h($lang) ?></label><textarea name="examples_<?= h($lang) ?>" rows="5" class="form-control"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('worksheet')) ?> <?= h($lang) ?></label><textarea name="worksheet_<?= h($lang) ?>" rows="4" class="form-control"></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('teacher_notes')) ?> <?= h($lang) ?></label><textarea name="teacher_<?= h($lang) ?>" rows="4" class="form-control"></textarea></div>
  <?php endforeach; ?>
  <div class="col-12"><button class="btn btn-primary"><?= h(t('save')) ?></button></div>
</div></form>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
