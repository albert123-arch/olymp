<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_content_manager();
$pageTitle = t('admin_dashboard') . ' | ' . t('site_title');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('admin_dashboard')) ?></h1>
<?php if (!db_available()): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <?php
  $counts = [
      t('courses') => db()->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
      t('chapters') => db()->query('SELECT COUNT(*) FROM chapters')->fetchColumn(),
      t('problems') => db()->query('SELECT COUNT(*) FROM problems')->fetchColumn(),
      t('media') => db()->query('SELECT COUNT(*) FROM problem_media')->fetchColumn(),
  ];
  $missing = db()->query("SELECT COUNT(*) FROM problems p WHERE NOT EXISTS (SELECT 1 FROM problem_texts pt WHERE pt.problem_id=p.id AND pt.lang='en') OR NOT EXISTS (SELECT 1 FROM problem_texts pt WHERE pt.problem_id=p.id AND pt.lang='ru')")->fetchColumn();
  ?>
  <div class="row g-3 mb-4">
    <?php foreach ($counts as $label => $count): ?>
      <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body"><div class="text-secondary"><?= h($label) ?></div><div class="display-6 fw-bold"><?= h((string)$count) ?></div></div></div></div>
    <?php endforeach; ?>
  </div>
  <div class="alert alert-info"><?= h(t('missing_translations')) ?>: <?= h((string)$missing) ?></div>
  <div class="list-group">
    <a class="list-group-item list-group-item-action" href="<?= h(app_url('admin/courses.php')) ?>"><?= h(t('courses')) ?></a>
    <a class="list-group-item list-group-item-action" href="<?= h(app_url('admin/chapters.php')) ?>"><?= h(t('chapters')) ?></a>
    <a class="list-group-item list-group-item-action" href="<?= h(app_url('admin/problems.php')) ?>"><?= h(t('problems')) ?></a>
    <a class="list-group-item list-group-item-action" href="<?= h(app_url('admin/media.php')) ?>"><?= h(t('media')) ?></a>
    <a class="list-group-item list-group-item-action" href="<?= h(app_url('admin/import-json.php')) ?>"><?= h(t('import_json')) ?></a>
  </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>

