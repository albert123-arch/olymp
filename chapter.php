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
    <a href="<?= h(course_url($courseSlug)) ?>" class="link-secondary"><?= h(t('back')) ?></a>
    <h1 class="fw-bold mt-2"><?= h($chapter['title']) ?></h1>
    <div class="lead text-secondary"><?= $chapter['summary_html'] ?></div>
  </div>
  <ul class="nav nav-tabs hash-tab-nav" role="tablist">
    <?php foreach (['theory', 'examples', 'practice', 'worksheet', 'teacher_notes'] as $i => $key): ?>
      <li class="nav-item" role="presentation">
        <a class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#<?= h($key) ?>" href="#<?= h($key) ?>" role="tab" aria-controls="<?= h($key) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= h(t($key)) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tab-content hash-tab-content border border-top-0 p-3 p-lg-4 bg-white">
    <section class="tab-pane fade show active math-content" id="theory"><?= $chapter['theory_html'] ?></section>
    <section class="tab-pane fade math-content" id="examples"><?= $chapter['examples_html'] ?></section>
    <section class="tab-pane fade" id="practice">
      <?php $problems = fetch_problems((int)$chapter['id']); ?>
      <?php if (!$problems): ?><p class="text-secondary"><?= h(t('no_records')) ?></p><?php endif; ?>
      <p><a class="btn btn-outline-primary" href="<?= h(practice_url($courseSlug, $chapterSlug)) ?>"><?= h(t('practice')) ?></a></p>
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
  <script>
    (function () {
      function openHashTab() {
        var hash = window.location.hash;
        if (!hash) return;
        var pane = document.querySelector(hash + '.tab-pane');
        var link = document.querySelector('.hash-tab-nav [href="' + hash + '"], .hash-tab-nav [data-bs-target="' + hash + '"]');
        if (!pane || !link) return;
        document.querySelectorAll('.hash-tab-content > .tab-pane').forEach(function (el) {
          el.classList.remove('active', 'show');
        });
        document.querySelectorAll('.hash-tab-nav .nav-link').forEach(function (el) {
          el.classList.remove('active');
          el.setAttribute('aria-selected', 'false');
        });
        pane.classList.add('active', 'show');
        link.classList.add('active');
        link.setAttribute('aria-selected', 'true');
      }
      window.addEventListener('hashchange', openHashTab);
      openHashTab();
    })();
  </script>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
