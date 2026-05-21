<?php
declare(strict_types=1);
$chapter = $chapter ?? [];
$courseSlug = (string)($chapter['course_slug'] ?? ($_GET['course'] ?? ''));
if ($courseSlug === '' && db_available()) {
    $courseSlug = fetch_first_course_slug() ?? '';
}
$chapterSlug = (string)($chapter['slug'] ?? '');
?>
<article class="card chapter-card h-100 shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between gap-2 mb-2">
      <h2 class="h5 mb-0"><?= h($chapter['sort_order'] ?? '') ?>. <?= h($chapter['title'] ?? '') ?></h2>
      <span class="badge text-bg-light border"><?= h(t('chapters')) ?></span>
    </div>
    <div class="text-secondary mb-3"><?= html_or_soon($chapter['summary_html'] ?? '') ?></div>
    <a class="btn btn-outline-primary" href="<?= h(chapter_url($courseSlug, $chapterSlug)) ?>">
      <?= h(t('open_chapter')) ?>
    </a>
  </div>
</article>
