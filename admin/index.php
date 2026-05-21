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
  $totalCourses = (int)db()->query('SELECT COUNT(*) FROM courses')->fetchColumn();
  $activeCourses = (int)db()->query('SELECT COUNT(*) FROM courses WHERE is_published = 1 AND status = "active"')->fetchColumn();
  $hiddenCourses = (int)db()->query('SELECT COUNT(*) FROM courses WHERE is_published = 0 OR status != "active"')->fetchColumn();
  $totalChapters = (int)db()->query('SELECT COUNT(*) FROM chapters')->fetchColumn();
  $publishedChapters = (int)db()->query('SELECT COUNT(*) FROM chapters WHERE is_published = 1')->fetchColumn();
  $totalProblems = (int)db()->query('SELECT COUNT(*) FROM problems')->fetchColumn();
  $publishedProblems = (int)db()->query('SELECT COUNT(*) FROM problems WHERE is_published = 1')->fetchColumn();
  $mediaCount = (int)db()->query('SELECT COUNT(*) FROM problem_media')->fetchColumn();
  $missingTexts = (int)db()->query("SELECT COUNT(*) FROM problems p WHERE NOT EXISTS (SELECT 1 FROM problem_texts pt WHERE pt.problem_id=p.id AND pt.lang='en') OR NOT EXISTS (SELECT 1 FROM problem_texts pt WHERE pt.problem_id=p.id AND pt.lang='ru')")->fetchColumn();
  $recentCourses = db()->query('SELECT id, slug, updated_at FROM courses ORDER BY updated_at DESC LIMIT 3')->fetchAll();
  $recentChapters = db()->query('SELECT ch.id, c.slug AS course_slug, ch.slug, ch.updated_at FROM chapters ch JOIN courses c ON c.id = ch.course_id ORDER BY ch.updated_at DESC LIMIT 3')->fetchAll();
  $recentProblems = db()->query('SELECT p.id, p.problem_code, ch.slug AS chapter_slug, c.slug AS course_slug, p.updated_at FROM problems p JOIN chapters ch ON ch.id = p.chapter_id JOIN courses c ON c.id = ch.course_id ORDER BY p.updated_at DESC LIMIT 3')->fetchAll();
  ?>
  <div class="row g-3 mb-4">
    <?php $cards = [
      [t('courses'), $totalCourses, 'text-bg-primary'],
      [t('chapters'), $totalChapters, 'text-bg-success'],
      [t('problems'), $totalProblems, 'text-bg-warning'],
      [t('media'), $mediaCount, 'text-bg-info'],
    ]; ?>
    <?php foreach ($cards as [$label, $count, $badgeClass]): ?>
      <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-secondary"><?= h($label) ?></div><div class="display-6 fw-bold"><?= h((string)$count) ?></div></div><span class="badge <?= h($badgeClass) ?> rounded-pill px-3 py-2"><?= h($label) ?></span></div></div></div>
    <?php endforeach; ?>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100"><div class="card-body">
        <h2 class="h6"><?= h(t('courses')) ?> &mdash; <?= h(t('published')) ?> / <?= h(t('draft')) ?></h2>
        <p class="mb-1"><strong><?= h($activeCourses) ?></strong> <?= h(t('active')) ?></p>
        <p><strong><?= h($hiddenCourses) ?></strong> <?= h(t('coming_soon')) ?></p>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm h-100"><div class="card-body">
        <h2 class="h6"><?= h(t('missing_translations')) ?></h2>
        <p class="mb-0"><?= h((string)$missingTexts) ?></p>
      </div></div>
    </div>
  </div>
  <div class="mb-4">
    <div class="btn-group" role="group">
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/courses.php')) ?>"><?= h(t('courses')) ?></a>
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/chapters.php')) ?>"><?= h(t('chapters')) ?></a>
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/problems.php')) ?>"><?= h(t('problems')) ?></a>
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/media.php')) ?>"><?= h(t('media')) ?></a>
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/import-json.php')) ?>"><?= h(t('import_json')) ?></a>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <h2 class="h6 mb-3"><?= h(t('courses')) ?> <?= h(t('recent')) ?></h2>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentCourses as $row): ?>
            <li><?= h($row['slug']) ?> <small class="text-muted"><?= h($row['updated_at']) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <h2 class="h6 mb-3"><?= h(t('chapters')) ?> <?= h(t('recent')) ?></h2>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentChapters as $row): ?>
            <li><?= h($row['course_slug']) ?>/<?= h($row['slug']) ?> <small class="text-muted"><?= h($row['updated_at']) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <h2 class="h6 mb-3"><?= h(t('problems')) ?> <?= h(t('recent')) ?></h2>
        <ul class="list-unstyled mb-0">
          <?php foreach ($recentProblems as $row): ?>
            <li><?= h($row['course_slug']) ?>/<?= h($row['chapter_slug']) ?>/<?= h($row['problem_code']) ?> <small class="text-muted"><?= h($row['updated_at']) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    </div>
  </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>

