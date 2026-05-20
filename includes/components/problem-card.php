<?php
declare(strict_types=1);
$problem = $problem ?? [];
$tags = array_filter(explode(',', (string)($problem['tags_csv'] ?? '')));
$cardId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)($problem['problem_code'] ?? $problem['id'] ?? uniqid('p')));
?>
<article class="card problem-card shadow-sm mb-3" data-problem-code="<?= h($problem['problem_code'] ?? '') ?>">
  <div class="card-body">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
      <div>
        <?php if (!empty($problem['problem_code'])): ?>
          <span class="badge text-bg-light border me-2"><?= h($problem['problem_code']) ?></span>
        <?php endif; ?>
        <span class="badge difficulty-badge"><?= h(difficulty_label((string)($problem['difficulty'] ?? 'core'))) ?></span>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary js-bookmark" type="button" data-default="<?= h(t('bookmark')) ?>" data-active="<?= h(t('bookmarked')) ?>"><?= h(t('bookmark')) ?></button>
        <button class="btn btn-sm btn-outline-success js-solved" type="button" data-default="<?= h(t('mark_solved')) ?>" data-active="<?= h(t('solved')) ?>"><?= h(t('mark_solved')) ?></button>
      </div>
    </div>
    <h2 class="h5"><?= h($problem['title'] ?? '') ?></h2>
    <div class="math-content"><?= $problem['statement_html'] ?? '' ?></div>
    <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'statement'); include __DIR__ . '/media-renderer.php'; ?>
    <?php if ($tags): ?>
      <div class="d-flex flex-wrap gap-1 my-3">
        <?php foreach ($tags as $tag): ?>
          <span class="badge rounded-pill text-bg-light border"><?= h($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="d-flex flex-wrap gap-2">
      <?php if (!empty($problem['hint_html'])): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#hint-<?= h($cardId) ?>" type="button"><?= h(t('hint')) ?></button>
      <?php endif; ?>
      <?php if (!empty($problem['solution_html'])): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#solution-<?= h($cardId) ?>" type="button"><?= h(t('solution')) ?></button>
      <?php endif; ?>
      <a class="btn btn-sm btn-link" href="<?= h(app_url('problem.php', ['code' => $problem['problem_code'] ?? ''])) ?>"><?= h(t('open_problem')) ?></a>
    </div>
    <?php if (!empty($problem['hint_html'])): ?>
      <div class="collapse reveal-box mt-3" id="hint-<?= h($cardId) ?>">
        <h3 class="h6"><?= h(t('hint')) ?></h3>
        <?= $problem['hint_html'] ?>
        <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'hint'); include __DIR__ . '/media-renderer.php'; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($problem['solution_html'])): ?>
      <div class="collapse reveal-box mt-3" id="solution-<?= h($cardId) ?>">
        <h3 class="h6"><?= h(t('solution')) ?></h3>
        <?= $problem['solution_html'] ?>
        <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'solution'); include __DIR__ . '/media-renderer.php'; ?>
      </div>
    <?php endif; ?>
  </div>
</article>
