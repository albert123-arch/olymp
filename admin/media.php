<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
$message = '';
if (db_available() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $problemId = (int)$_POST['problem_id'];
    $stmt = db()->prepare('SELECT problem_code FROM problems WHERE id=?');
    $stmt->execute([$problemId]);
    $code = (string)$stmt->fetchColumn();
    if ($code !== '' && isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $dir = dirname(__DIR__) . '/uploads/problems/' . $code;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $name = preg_replace('/[^a-zA-Z0-9._-]/', '-', basename($_FILES['image']['name']));
        $target = $dir . '/' . $name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $path = 'uploads/problems/' . $code . '/' . $name;
        db()->prepare('INSERT INTO problem_media (problem_id, role, file_path, mime_type, sort_order) VALUES (?,?,?,?,?)')
            ->execute([$problemId, $_POST['role'], $path, $_FILES['image']['type'] ?? 'image/*', (int)$_POST['sort_order']]);
        $mediaId = (int)db()->lastInsertId();
        foreach (SUPPORTED_LANGS as $lang) {
            db()->prepare('INSERT INTO problem_media_texts (media_id, lang, alt_text, caption_html) VALUES (?,?,?,?)')
                ->execute([$mediaId, $lang, $_POST["alt_$lang"] ?? '', $_POST["caption_$lang"] ?? '']);
        }
        $message = t('upload');
    }
}
$pageTitle = t('media') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('media')) ?></h1>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php if ($message): ?><div class="alert alert-success"><?= h($message) ?></div><?php endif; ?>
<?php $problems = db()->query('SELECT id, problem_code FROM problems ORDER BY sort_order')->fetchAll(); ?>
<form method="post" enctype="multipart/form-data" class="card shadow-sm mb-4"><div class="card-body row g-3">
  <div class="col-md-4"><label class="form-label"><?= h(t('problem')) ?></label><select name="problem_id" class="form-select"><?php foreach ($problems as $p): ?><option value="<?= h((string)$p['id']) ?>"><?= h($p['problem_code']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><label class="form-label"><?= h(t('role')) ?></label><select name="role" class="form-select"><?php foreach (['statement','hint','solution','teacher_note','extra'] as $role): ?><option value="<?= h($role) ?>"><?= h(t('role_' . $role)) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><label class="form-label"><?= h(t('upload')) ?></label><input name="image" type="file" accept="image/*" class="form-control" required></div>
  <div class="col-md-2"><label class="form-label"><?= h(t('sort_order')) ?></label><input name="sort_order" type="number" value="0" class="form-control"></div>
  <?php foreach (SUPPORTED_LANGS as $lang): ?>
    <div class="col-md-6"><label class="form-label"><?= h(t('alt_text')) ?> <?= h($lang) ?></label><input name="alt_<?= h($lang) ?>" class="form-control"></div>
    <div class="col-md-6"><label class="form-label"><?= h(t('caption')) ?> <?= h($lang) ?></label><textarea name="caption_<?= h($lang) ?>" class="form-control"></textarea></div>
  <?php endforeach; ?>
  <div class="col-12"><button class="btn btn-primary"><?= h(t('upload')) ?></button></div>
</div></form>
<?php $media = db()->query('SELECT pm.*, p.problem_code FROM problem_media pm JOIN problems p ON p.id=pm.problem_id ORDER BY pm.created_at DESC')->fetchAll(); ?>
<div class="row g-3"><?php foreach ($media as $m): ?><div class="col-md-4"><div class="card"><img src="/<?= h($m['file_path']) ?>" class="card-img-top" alt=""><div class="card-body small"><strong><?= h($m['problem_code']) ?></strong> <?= h($m['role']) ?><br><?= h($m['file_path']) ?></div></div></div><?php endforeach; ?></div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
