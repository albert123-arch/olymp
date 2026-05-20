<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$code = (string)($_GET['code'] ?? '');
$problem = db_available() && $code !== '' ? fetch_problem($code) : null;
$pageTitle = ($problem['title'] ?? t('problem')) . ' | ' . t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<?php if (!$problem): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <a href="<?= h(app_url('chapter.php', ['course' => $problem['course_slug'], 'chapter' => $problem['chapter_slug']])) ?>" class="link-secondary"><?= h(t('back')) ?></a>
  <div class="mt-3">
    <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
  </div>
  <?php if (!empty($problem['teacher_note_html'])): ?>
    <section class="card border-warning mt-3">
      <div class="card-body">
        <h2 class="h5"><?= h(t('teacher_notes')) ?></h2>
        <div class="math-content"><?= $problem['teacher_note_html'] ?></div>
        <?php $mediaItems = fetch_problem_media((int)$problem['id'], 'teacher_note'); include __DIR__ . '/includes/components/media-renderer.php'; ?>
      </div>
    </section>
  <?php endif; ?>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

