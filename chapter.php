<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$courseSlug = (string)($_GET['course'] ?? 'number-theory');
$chapterSlug = (string)($_GET['chapter'] ?? 'divisibility-prime-factorisation');
$chapter = db_available() ? fetch_chapter($courseSlug, $chapterSlug) : null;
$pageTitle = ($chapter['title'] ?? t('chapters')) . ' | ' . t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<?php if (!$chapter): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <div class="mb-4">
    <a href="<?= h(app_url('course.php', ['course' => $courseSlug])) ?>" class="link-secondary"><?= h(t('back')) ?></a>
    <h1 class="fw-bold mt-2"><?= h($chapter['title']) ?></h1>
    <div class="lead text-secondary"><?= $chapter['summary_html'] ?></div>
  </div>
  <ul class="nav nav-tabs" role="tablist">
    <?php foreach (['theory', 'examples', 'practice', 'worksheet', 'teacher_notes'] as $i => $key): ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#<?= h($key) ?>" type="button"><?= h(t($key)) ?></button>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tab-content border border-top-0 p-3 p-lg-4 bg-white">
    <section class="tab-pane fade show active math-content" id="theory"><?= $chapter['theory_html'] ?></section>
    <section class="tab-pane fade math-content" id="examples"><?= $chapter['examples_html'] ?></section>
    <section class="tab-pane fade" id="practice">
      <?php $problems = fetch_problems((int)$chapter['id']); ?>
      <?php if (!$problems): ?><p class="text-secondary"><?= h(t('no_records')) ?></p><?php endif; ?>
      <div class="toolbar practice-toolbar">
        <label><span><?= h(t('search')) ?></span><input id="searchInput" type="search" class="form-control" autocomplete="off"></label>
        <label><span><?= h(t('difficulty')) ?></span><select id="difficultyFilter" class="form-select"><option value=""><?= h(t('all')) ?></option><option value="level1"><?= h(t('level1')) ?></option><option value="level2"><?= h(t('level2')) ?></option><option value="level3"><?= h(t('level3')) ?></option></select></label>
        <label><span><?= h(t('type')) ?></span><select id="typeFilter" class="form-select"><option value=""><?= h(t('all')) ?></option><option value="proof"><?= h(t('proof')) ?></option><option value="computation"><?= h(t('computation')) ?></option><option value="counterexample"><?= h(t('counterexample')) ?></option><option value="warmup"><?= h(t('warmup')) ?></option></select></label>
      </div>
      <?php foreach ($problems as $problem): ?>
        <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
      <?php endforeach; ?>
    </section>
    <section class="tab-pane fade math-content" id="worksheet"><?= $chapter['worksheet_html'] ?></section>
    <section class="tab-pane fade math-content" id="teacher_notes">
      <?php if (user_can_manage_content()): ?>
        <?= $chapter['teacher_notes_html'] ?>
      <?php else: ?>
        <div class="alert alert-warning"><?= h(t('access_denied')) ?></div>
      <?php endif; ?>
    </section>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
