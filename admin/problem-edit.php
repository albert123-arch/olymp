<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_content_manager();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (db_available() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id > 0) {
        db()->prepare('UPDATE problems SET chapter_id=?, problem_code=?, book_number=?, difficulty=?, sort_order=?, is_published=? WHERE id=?')
            ->execute([(int)$_POST['chapter_id'], $_POST['problem_code'], (int)$_POST['book_number'], $_POST['difficulty'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
    } else {
        db()->prepare('INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, sort_order, is_published) VALUES (?,?,?,?,?,?)')
            ->execute([(int)$_POST['chapter_id'], $_POST['problem_code'], (int)$_POST['book_number'], $_POST['difficulty'], (int)$_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
        $id = (int)db()->lastInsertId();
    }
    foreach (SUPPORTED_LANGS as $lang) {
        db()->prepare('REPLACE INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES (?,?,?,?,?,?,?)')
            ->execute([$id, $lang, $_POST["title_$lang"], $_POST["statement_$lang"], $_POST["hint_$lang"], $_POST["solution_$lang"], $_POST["teacher_$lang"]]);
    }
    $tags = array_filter(array_map('trim', explode(',', (string)($_POST['tags'] ?? ''))));
    db()->prepare('DELETE FROM problem_tags WHERE problem_id=?')->execute([$id]);
    foreach ($tags as $tag) {
        db()->prepare('INSERT IGNORE INTO tags (slug) VALUES (?)')->execute([$tag]);
        $tagId = (int)db()->query('SELECT id FROM tags WHERE slug=' . db()->quote($tag))->fetchColumn();
        db()->prepare('INSERT IGNORE INTO problem_tags (problem_id, tag_id) VALUES (?,?)')->execute([$id, $tagId]);
    }
    header('Location: ' . url('admin/problems.php'));
    exit;
}
$problem = null;
$texts = [];
if (db_available() && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM problems WHERE id=?');
    $stmt->execute([$id]);
    $problem = $stmt->fetch();
    $stmt = db()->prepare('SELECT * FROM problem_texts WHERE problem_id=?');
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $row) {
        $texts[$row['lang']] = $row;
    }
}
$pageTitle = t('problem') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('problem')) ?></h1>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php
$chapters = db()->query("SELECT ch.id, CONCAT(c.slug, ' / ', en.title) title FROM chapters ch JOIN courses c ON c.id=ch.course_id JOIN chapter_texts en ON en.chapter_id=ch.id AND en.lang='en' ORDER BY c.sort_order, ch.sort_order")->fetchAll();
$tagCsv = '';
if ($id > 0) {
    $stmt = db()->prepare('SELECT GROUP_CONCAT(t.slug ORDER BY t.slug SEPARATOR ", ") FROM problem_tags pt JOIN tags t ON t.id=pt.tag_id WHERE pt.problem_id=?');
    $stmt->execute([$id]);
    $tagCsv = (string)$stmt->fetchColumn();
}
?>
<form method="post" class="card shadow-sm"><div class="card-body row g-3">
  <input type="hidden" name="id" value="<?= h((string)$id) ?>">
  <div class="col-md-4"><label class="form-label"><?= h(t('chapter')) ?></label><select name="chapter_id" class="form-select"><?php foreach ($chapters as $ch): ?><option value="<?= h((string)$ch['id']) ?>" <?= (int)($problem['chapter_id'] ?? 0) === (int)$ch['id'] ? 'selected' : '' ?>><?= h($ch['title']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('code')) ?></label><input name="problem_code" class="form-control" value="<?= h($problem['problem_code'] ?? '') ?>" required></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('book_number')) ?></label><input name="book_number" type="number" class="form-control" value="<?= h((string)($problem['book_number'] ?? 0)) ?>"></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('difficulty')) ?></label><select name="difficulty" class="form-select"><?php foreach (['intro','core','challenge'] as $d): ?><option value="<?= h($d) ?>" <?= ($problem['difficulty'] ?? 'core') === $d ? 'selected' : '' ?>><?= h(difficulty_label($d)) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('sort_order')) ?></label><input name="sort_order" type="number" class="form-control" value="<?= h((string)($problem['sort_order'] ?? 0)) ?>"></div>
  <div class="col-md-8"><label class="form-label"><?= h(t('tags')) ?></label><input name="tags" class="form-control" value="<?= h($tagCsv) ?>"></div>
  <div class="col-md-4 d-flex align-items-end"><label class="form-check"><input class="form-check-input" name="is_published" type="checkbox" <?= (int)($problem['is_published'] ?? 1) === 1 ? 'checked' : '' ?>> <?= h(t('published')) ?></label></div>
  <?php foreach (SUPPORTED_LANGS as $lang): $tx = $texts[$lang] ?? []; ?>
    <div class="col-12"><h2 class="h5 text-uppercase mt-2"><?= h($lang) ?></h2></div>
    <div class="col-12"><label class="form-label"><?= h(t('title')) ?></label><input name="title_<?= h($lang) ?>" class="form-control" value="<?= h($tx['title'] ?? '') ?>" required></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('statement_html')) ?></label><textarea name="statement_<?= h($lang) ?>" rows="7" class="form-control" required><?= h($tx['statement_html'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('hint_html')) ?></label><textarea name="hint_<?= h($lang) ?>" rows="7" class="form-control"><?= h($tx['hint_html'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('solution_html')) ?></label><textarea name="solution_<?= h($lang) ?>" rows="7" class="form-control"><?= h($tx['solution_html'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('teacher_note_html')) ?></label><textarea name="teacher_<?= h($lang) ?>" rows="7" class="form-control"><?= h($tx['teacher_note_html'] ?? '') ?></textarea></div>
  <?php endforeach; ?>
  <div class="col-12"><button class="btn btn-primary"><?= h(t('save')) ?></button></div>
</div></form>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
