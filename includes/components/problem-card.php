<?php
declare(strict_types=1);
$problem = $problem ?? [];
$tags = array_filter(explode(',', (string)($problem['tags_csv'] ?? '')));
$cardId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)($problem['problem_code'] ?? $problem['id'] ?? uniqid('p')));
$levelKey = difficulty_level_key((string)($problem['difficulty'] ?? 'core'));
$typeKey = problem_type_key($problem);
$searchText = strtolower(($problem['title'] ?? '') . ' ' . ($problem['problem_code'] ?? '') . ' ' . implode(' ', $tags));
?>
<article class="card problem-card compact-problem-card shadow-sm mb-3"
         data-problem-code="<?= h($problem['problem_code'] ?? '') ?>"
         data-difficulty="<?= h($levelKey) ?>"
         data-type="<?= h($typeKey) ?>"
         data-tags="<?= h(implode(' ', $tags)) ?>"
         data-search="<?= h($searchText) ?>">
  <div class="card-body">
    <div class="problem-title-row">
      <div class="problem-title-main">
        <?php if (!empty($problem['problem_code'])): ?>
          <span class="problem-code"><?= h($problem['problem_code']) ?></span>
        <?php endif; ?>
        <span class="problem-level"><?= h(t($levelKey)) ?></span>
        <span class="problem-kind"><?= h(t($typeKey)) ?></span>
      </div>
      <div class="problem-actions-secondary">
        <button class="btn btn-sm btn-outline-secondary js-bookmark" type="button" data-default="<?= h(t('bookmark')) ?>" data-active="<?= h(t('bookmarked')) ?>"><?= h(t('bookmark')) ?></button>
        <button class="btn btn-sm btn-outline-success js-solved" type="button" data-default="<?= h(t('mark_solved')) ?>" data-active="<?= h(t('solved')) ?>"><?= h(t('mark_solved')) ?></button>
      </div>
    </div>
    <h2 class="problem-heading"><?= h($problem['title'] ?? '') ?></h2>
    <div class="statement math-content"><?= $problem['statement_html'] ?? '' ?></div>
    <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'statement'); include __DIR__ . '/media-renderer.php'; ?>
    <?php if ($tags): ?>
      <div class="d-flex flex-wrap gap-1 my-3">
        <?php foreach ($tags as $tag): ?>
          <span class="badge rounded-pill text-bg-light border"><?= h($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="actions">
      <?php if (!empty($problem['hint_html'])): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#hint-<?= h($cardId) ?>" type="button" aria-expanded="false"><?= h(t('hint')) ?></button>
      <?php endif; ?>
      <?php if (!empty($problem['solution_html'])): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#solution-<?= h($cardId) ?>" type="button" aria-expanded="false"><?= h(t('solution')) ?></button>
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
