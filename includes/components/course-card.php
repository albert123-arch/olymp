<?php
declare(strict_types=1);
$course = $course ?? [];
$isActive = ($course['status'] ?? '') === 'active';
?>
<article class="card course-card h-100 shadow-sm">
  <div class="card-body d-flex flex-column">
    <div class="d-flex justify-content-between gap-2 mb-3">
      <h2 class="h5 mb-0"><?= h($course['title'] ?? '') ?></h2>
      <span class="badge <?= $isActive ? 'text-bg-success' : 'text-bg-secondary' ?>">
        <?= h($isActive ? t('active_now') : t('coming_soon')) ?>
      </span>
    </div>
    <div class="text-secondary flex-grow-1"><?= html_or_soon($course['summary_html'] ?? '') ?></div>
    <a class="btn <?= $isActive ? 'btn-primary' : 'btn-outline-secondary' ?> mt-3" href="<?= h(course_url((string)($course['slug'] ?? ''))) ?>">
      <?= h(t('open_course')) ?>
    </a>
  </div>
</article>
