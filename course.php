<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$slug = (string)($_GET['course'] ?? 'number-theory');
$course = db_available() ? fetch_course($slug) : null;
$pageTitle = ($course['title'] ?? t('courses')) . ' | ' . t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<?php if (!$course): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <div class="mb-4">
    <a href="<?= h(app_url('index.php')) ?>" class="link-secondary"><?= h(t('back')) ?></a>
    <h1 class="fw-bold mt-2"><?= h($course['title']) ?></h1>
    <div class="lead text-secondary"><?= $course['summary_html'] ?></div>
  </div>
  <ul class="nav nav-tabs" role="tablist">
    <?php foreach (['overview', 'chapters', 'practice', 'worksheets', 'teacher_guide'] as $i => $key): ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#<?= h($key) ?>" type="button"><?= h(t($key)) ?></button>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tab-content border border-top-0 p-3 p-lg-4 bg-white">
    <section class="tab-pane fade show active" id="overview"><?= $course['overview_html'] ?></section>
    <section class="tab-pane fade" id="chapters">
      <div class="row g-3">
        <?php foreach (fetch_chapters((int)$course['id']) as $chapter): ?>
          <?php $chapter['course_slug'] = $course['slug']; ?>
          <div class="col-lg-6"><?php include __DIR__ . '/includes/components/chapter-card.php'; ?></div>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="tab-pane fade" id="practice">
      <?php foreach (fetch_problems() as $problem): ?>
        <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
      <?php endforeach; ?>
    </section>
    <section class="tab-pane fade" id="worksheets"><p class="text-secondary"><?= h(t('worksheets')) ?></p></section>
    <section class="tab-pane fade" id="teacher_guide"><?= $course['teacher_guide_html'] ?></section>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

