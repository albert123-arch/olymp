<?php
declare(strict_types=1);
$problem = $problem ?? [];
$tags = array_filter(explode(',', (string)($problem['tags_csv'] ?? '')));
$cardId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)($problem['problem_code'] ?? $problem['id'] ?? uniqid('p')));
$levelKey = difficulty_level_key((string)($problem['difficulty'] ?? 'core'));
$typeKey = problem_type_key($problem);
$searchText = strtolower(($problem['title'] ?? '') . ' ' . ($problem['problem_code'] ?? '') . ' ' . implode(' ', $tags));
$primaryTag = $tags ? reset($tags) : '';
$problemNumber = (string)($problem['book_number'] ?? '');
if ($problemNumber === '') {
    $problemNumber = (string)($problem['problem_code'] ?? '');
}
$isLoggedIn = !empty($_SESSION['user_id']);
$isBookmarked = (int)($problem['is_bookmarked'] ?? 0) === 1;
$isSolved = (string)($problem['progress_status'] ?? '') === 'solved';
?>
<article class="card problem-card compact-problem-card shadow-sm mb-3"
         data-problem-code="<?= h($problem['problem_code'] ?? '') ?>"
         data-problem-id="<?= h((string)($problem['id'] ?? '')) ?>"
         data-bookmarked="<?= $isBookmarked ? '1' : '0' ?>"
         data-solved="<?= $isSolved ? '1' : '0' ?>"
         data-difficulty="<?= h($levelKey) ?>"
         data-type="<?= h($typeKey) ?>"
         data-tags="<?= h(implode(' ', $tags)) ?>"
         data-search="<?= h($searchText) ?>">
  <div class="card-body">
    <div class="problem-title-row">
      <div class="problem-title-main">
        <h2 class="problem-heading"><?= h($problem['title'] ?? '') ?></h2>
        <?php if ($primaryTag !== ''): ?>
          <span class="badge rounded-pill problem-tag"><?= h(tag_label((string)$primaryTag)) ?></span>
        <?php endif; ?>
      </div>
      <div class="problem-actions-secondary">
        <button class="problem-icon-btn problem-solved-toggle js-solved" type="button" aria-label="<?= h(t('mark_solved_action')) ?>" title="<?= h(t('mark_solved_action')) ?>" data-default="<?= h(t('mark_solved_action')) ?>" data-active="<?= h(t('solved_action')) ?>" <?= $isLoggedIn ? 'data-api-url="' . h(url('api/problem-progress.php')) . '"' : '' ?>>
          <span aria-hidden="true"></span>
        </button>
        <?php if ($problemNumber !== ''): ?>
          <span class="problem-number"><?= h($problemNumber) ?></span>
        <?php endif; ?>
        <button class="problem-icon-btn problem-bookmark-toggle js-bookmark" type="button" aria-label="<?= h(t('bookmark_action')) ?>" title="<?= h(t('bookmark_action')) ?>" data-default="<?= h(t('bookmark_action')) ?>" data-active="<?= h(t('bookmarked_action')) ?>" <?= $isLoggedIn ? 'data-api-url="' . h(url('api/bookmark.php')) . '"' : '' ?>>
          <svg aria-hidden="true" viewBox="0 0 24 24" focusable="false">
            <path d="M6 3.75h12a1 1 0 0 1 1 1v15.5l-7-4.1-7 4.1V4.75a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="statement math-content"><?= $problem['statement_html'] ?? '' ?></div>
    <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'statement'); include __DIR__ . '/media-renderer.php'; ?>
    <div class="actions">
      <?php if (!empty($problem['hint_html'])): ?>
        <button class="btn btn-sm btn-outline-primary js-reveal-toggle" type="button" data-reveal-target="hint-<?= h($cardId) ?>"><?= h(t('hint')) ?></button>
      <?php endif; ?>
      <?php if (!empty($problem['solution_html'])): ?>
        <button class="btn btn-sm btn-outline-primary js-reveal-toggle" type="button" data-reveal-target="solution-<?= h($cardId) ?>"><?= h(t('solution')) ?></button>
      <?php endif; ?>
      <a class="btn btn-sm btn-outline-dark" href="<?= h(problem_url((string)($problem['problem_code'] ?? ''))) ?>"><?= h(t('open_problem')) ?></a>
    </div>
    <?php if (!empty($problem['hint_html'])): ?>
      <details class="reveal-box reveal-hint mt-3 js-reveal" id="hint-<?= h($cardId) ?>">
        <summary><?= h(t('hint')) ?></summary>
        <div class="reveal-content math-content">
          <?= $problem['hint_html'] ?>
          <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'hint'); include __DIR__ . '/media-renderer.php'; ?>
        </div>
      </details>
    <?php endif; ?>
    <?php if (!empty($problem['solution_html'])): ?>
      <details class="reveal-box reveal-solution mt-3 js-reveal" id="solution-<?= h($cardId) ?>">
        <summary><?= h(t('solution')) ?></summary>
        <div class="reveal-content math-content">
          <?= $problem['solution_html'] ?>
          <?php $mediaItems = fetch_problem_media((int)($problem['id'] ?? 0), 'solution'); include __DIR__ . '/media-renderer.php'; ?>
        </div>
      </details>
    <?php endif; ?>
  </div>
</article>
