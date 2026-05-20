<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$pageTitle = t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<section class="py-4 py-lg-5">
  <div class="row align-items-end g-4">
    <div class="col-lg-8">
      <p class="text-uppercase text-secondary small fw-semibold mb-2"><?= h(t('courses')) ?></p>
      <h1 class="display-5 fw-bold"><?= h(t('all_courses')) ?></h1>
      <p class="lead text-secondary mb-0"><?= h(t('homepage_intro')) ?></p>
    </div>
  </div>
</section>

<?php if (!db_available()): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <div class="row g-3">
    <?php foreach (fetch_all_courses() as $course): ?>
      <div class="col-md-6 col-xl-4">
        <?php include __DIR__ . '/includes/components/course-card.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

