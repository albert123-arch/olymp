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
  <div class="toolbar practice-toolbar">
    <label>
      <span><?= h(t('search')) ?></span>
      <input id="searchInput" type="search" class="form-control" autocomplete="off">
    </label>
    <label>
      <span><?= h(t('difficulty')) ?></span>
      <select id="difficultyFilter" class="form-select">
        <option value=""><?= h(t('all')) ?></option>
        <option value="level1"><?= h(t('level1')) ?></option>
        <option value="level2"><?= h(t('level2')) ?></option>
        <option value="level3"><?= h(t('level3')) ?></option>
      </select>
    </label>
    <label>
      <span><?= h(t('type')) ?></span>
      <select id="typeFilter" class="form-select">
        <option value=""><?= h(t('all')) ?></option>
        <option value="proof"><?= h(t('proof')) ?></option>
        <option value="computation"><?= h(t('computation')) ?></option>
        <option value="counterexample"><?= h(t('counterexample')) ?></option>
        <option value="warmup"><?= h(t('warmup')) ?></option>
      </select>
    </label>
  </div>
  <?php foreach (fetch_problems() as $problem): ?>
    <?php include __DIR__ . '/includes/components/problem-card.php'; ?>
  <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
