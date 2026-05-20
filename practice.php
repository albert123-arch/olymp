<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$pageTitle = t('practice') . ' | ' . t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<h1 class="fw-bold mb-4"><?= h(t('practice')) ?></h1>
<?php if (!db_available()): ?>
  <?php render_db_notice(); ?>
<?php else: ?>
  <?php foreach (fetch_problems() as $problem): ?>
    <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
  <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

