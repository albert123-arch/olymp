<?php
require_once __DIR__ . '/_bootstrap.php';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$hasProblemType = column_exists('problems', 'problem_type');
$difficultyIsEnum = strpos((string) column_type('problems', 'difficulty'), 'enum') === 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $difficulty = (int) ($_POST['difficulty'] ?? 1);
        $difficultyValue = $difficultyIsEnum ? ([1 => 'intro', 2 => 'core', 3 => 'challenge'][$difficulty] ?? 'core') : $difficulty;
        if ($id) {
            if ($hasProblemType) {
                execute_query('UPDATE problems SET chapter_id=?, problem_code=?, book_number=?, difficulty=?, problem_type=?, sort_order=?, is_published=?, updated_at=NOW() WHERE id=?', [(int) $_POST['chapter_id'], (string) $_POST['problem_code'], (int) $_POST['book_number'], $difficultyValue, (string) $_POST['problem_type'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
            } else {
                execute_query('UPDATE problems SET chapter_id=?, problem_code=?, book_number=?, difficulty=?, sort_order=?, is_published=?, updated_at=NOW() WHERE id=?', [(int) $_POST['chapter_id'], (string) $_POST['problem_code'], (int) $_POST['book_number'], $difficultyValue, (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0, $id]);
            }
        } else {
            if ($hasProblemType) {
                execute_query('INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [(int) $_POST['chapter_id'], (string) $_POST['problem_code'], (int) $_POST['book_number'], $difficultyValue, (string) $_POST['problem_type'], (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
            } else {
                execute_query('INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, sort_order, is_published, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())', [(int) $_POST['chapter_id'], (string) $_POST['problem_code'], (int) $_POST['book_number'], $difficultyValue, (int) $_POST['sort_order'], isset($_POST['is_published']) ? 1 : 0]);
            }
            $id = (int) db()->lastInsertId();
        }
        foreach (admin_languages() as $lang) {
            $c = $lang['code'];
            execute_query('INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title=VALUES(title), statement_html=VALUES(statement_html), hint_html=VALUES(hint_html), solution_html=VALUES(solution_html), teacher_note_html=VALUES(teacher_note_html)', [$id, $c, $_POST["title_$c"] ?? '', $_POST["statement_$c"] ?? '', $_POST["hint_$c"] ?? '', $_POST["solution_$c"] ?? '', $_POST["teacher_$c"] ?? '']);
        }
        upload_problem_image($id, (string) $_POST['problem_code']);
        header('Location: ' . url('admin/problem-edit.php', ['id' => $id]));
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
$problem = $id ? fetch_one('SELECT * FROM problems WHERE id=?', [$id]) : ['chapter_id'=>'', 'problem_code'=>'', 'book_number'=>0, 'difficulty'=>1, 'problem_type'=>'computation', 'sort_order'=>0, 'is_published'=>1];
$currentDifficulty = is_string($problem['difficulty'] ?? null) ? (['intro' => 1, 'core' => 2, 'challenge' => 3][$problem['difficulty']] ?? 1) : (int) ($problem['difficulty'] ?? 1);
$chapters = fetch_all('SELECT ch.id, ch.slug, c.slug course_slug FROM chapters ch JOIN courses c ON c.id=ch.course_id ORDER BY c.sort_order, ch.sort_order');
$byLang = $id ? array_column(fetch_all('SELECT * FROM problem_texts WHERE problem_id=?', [$id]), null, 'lang') : [];
$types = ['computation','proof','counterexample','construction','challenge','mixed'];
admin_header(t('edit'));
?>
<form class="admin-panel p-3" method="post" enctype="multipart/form-data">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= e((string) $id) ?>">
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="row g-2 mb-3">
<div class="col-md-4"><select class="form-select" name="chapter_id"><?php foreach ($chapters as $ch): ?><option value="<?= e((string) $ch['id']) ?>" <?= (int)$problem['chapter_id']===(int)$ch['id']?'selected':'' ?>><?= e($ch['course_slug'].'/'.$ch['slug']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input class="form-control" name="problem_code" value="<?= e($problem['problem_code']) ?>" placeholder="NT-01-001"></div>
<div class="col-md-2"><input class="form-control" type="number" name="book_number" value="<?= e((string)$problem['book_number']) ?>"></div>
<div class="col-md-2"><select class="form-select" name="difficulty"><?php for($i=1;$i<=3;$i++): ?><option value="<?= $i ?>" <?= $currentDifficulty===$i?'selected':'' ?>><?= $i ?></option><?php endfor; ?></select></div>
<?php if ($hasProblemType): ?><div class="col-md-2"><select class="form-select" name="problem_type" aria-label="<?= e(t('problem_type')) ?>"><?php foreach($types as $type): ?><option value="<?= e($type) ?>" <?= ($problem['problem_type'] ?? '')===$type?'selected':'' ?>><?= e(t($type)) ?></option><?php endforeach; ?></select></div><?php endif; ?>
<div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="<?= e((string)$problem['sort_order']) ?>"></div>
<div class="col-md-2 form-check pt-2"><input class="form-check-input" name="is_published" type="checkbox" <?= $problem['is_published']?'checked':'' ?>> <label class="form-check-label"><?= e(t('publish')) ?></label></div>
</div>
<?php foreach (admin_languages() as $lang): $c=$lang['code']; $txt=$byLang[$c]??[]; ?><h2 class="h5"><?= e(strtoupper($c)) ?></h2>
<input class="form-control mb-2" name="title_<?= e($c) ?>" value="<?= e($txt['title'] ?? '') ?>" placeholder="<?= e(t('title')) ?>">
<label class="form-label w-100"><?= e(t('statement')) ?><?= admin_textarea("statement_$c", $txt['statement_html'] ?? '', 5) ?></label>
<label class="form-label w-100"><?= e(t('hint')) ?><?= admin_textarea("hint_$c", $txt['hint_html'] ?? '', 3) ?></label>
<label class="form-label w-100"><?= e(t('solution')) ?><?= admin_textarea("solution_$c", $txt['solution_html'] ?? '', 5) ?></label>
<label class="form-label w-100"><?= e(t('teacher_note')) ?><?= admin_textarea("teacher_$c", $txt['teacher_note_html'] ?? '', 3) ?></label>
<?php endforeach; ?>
<h2 class="h5"><?= e(t('upload_image')) ?></h2>
<div class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></div><div class="col-md-3"><select class="form-select" name="image_role" aria-label="<?= e(t('image_role')) ?>"><?php foreach(['statement','hint','solution','teacher_note','extra'] as $role): ?><option value="<?= e($role) ?>"><?= e(t($role)) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><input class="form-control" name="image_lang" placeholder="<?= e(t('language')) ?>"></div></div>
<button class="btn btn-accent"><?= e(t('save')) ?></button>
</form>
<?php admin_footer(); ?>
