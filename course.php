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
    <a href="<?= h(url('index.php')) ?>" class="link-secondary"><?= h(t('back')) ?></a>
    <h1 class="fw-bold mt-2"><?= h($course['title']) ?></h1>
    <div class="lead text-secondary"><?= $course['summary_html'] ?></div>
  </div>
  <ul class="nav nav-tabs hash-tab-nav" role="tablist">
    <?php foreach (['overview', 'chapters', 'practice', 'worksheets', 'teacher_guide'] as $i => $key): ?>
      <li class="nav-item" role="presentation">
        <a class="nav-link <?= $i === 0 ? 'active' : '' ?>" href="#<?= h($key) ?>" role="tab" aria-controls="<?= h($key) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= h(t($key)) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tab-content hash-tab-content border border-top-0 p-3 p-lg-4 bg-white">
    <section class="tab-pane active" id="overview"><?= $course['overview_html'] ?></section>
    <section class="tab-pane" id="chapters">
      <div class="row g-3">
        <?php foreach (fetch_chapters((int)$course['id']) as $chapter): ?>
          <?php $chapter['course_slug'] = $course['slug']; ?>
          <div class="col-lg-6"><?php include __DIR__ . '/includes/components/chapter-card.php'; ?></div>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="tab-pane" id="practice">
      <p><a class="btn btn-outline-primary" href="<?= h(practice_url((string)$course['slug'])) ?>"><?= h(t('practice')) ?></a></p>
      <?php foreach (fetch_problems(null, (int)$course['id']) as $problem): ?>
        <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
      <?php endforeach; ?>
    </section>
    <section class="tab-pane" id="worksheets"><p class="text-secondary"><?= h(t('worksheets')) ?></p></section>
    <section class="tab-pane" id="teacher_guide"><?= $course['teacher_guide_html'] ?></section>
  </div>
  <script>
    (function () {
      function openHashTab() {
        var hash = window.location.hash;
        if (!hash) return;
        var pane = document.querySelector(hash + '.tab-pane');
        var link = document.querySelector('.hash-tab-nav [href="' + hash + '"]');
        if (!pane || !link) return;
        document.querySelectorAll('.hash-tab-content > .tab-pane').forEach(function (el) {
          el.classList.remove('active');
        });
        document.querySelectorAll('.hash-tab-nav .nav-link').forEach(function (el) {
          el.classList.remove('active');
          el.setAttribute('aria-selected', 'false');
        });
        pane.classList.add('active');
        link.classList.add('active');
        link.setAttribute('aria-selected', 'true');
      }
      window.addEventListener('hashchange', openHashTab);
      openHashTab();
    })();
  </script>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
