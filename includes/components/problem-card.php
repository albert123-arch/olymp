<?php
/** @var array $problem */
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/media-renderer.php';
$tags = get_problem_tags((int) $problem['id']);
$mainTag = $tags[0]['title'] ?? '';
$cardId = 'problem-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $problem['problem_code']);
?>
<article class="problem-card" data-problem-code="<?= e($problem['problem_code']) ?>" data-problem-id="<?= e((string) $problem['id']) ?>">
    <div class="problem-meta">
        <a class="problem-code" href="<?= e(problem_url($problem['problem_code'])) ?>">#<?= e((string) ($problem['book_number'] ?: $problem['problem_code'])) ?></a>
        <a class="problem-title" href="<?= e(problem_url($problem['problem_code'])) ?>"><?= e($problem['title'] ?? t('missing_translation')) ?></a>
        <?php if ($mainTag): ?><span class="tag-chip"><?= e($mainTag) ?></span><?php endif; ?>
        <?= render_stars($problem['difficulty']) ?>
        <div class="problem-actions">
            <?php if (!empty($problem['hint_html'])): ?>
                <button class="icon-btn js-toggle-panel" type="button" data-target="#<?= e($cardId) ?>-hint" aria-label="<?= e(t('hint')) ?>">?</button>
            <?php endif; ?>
            <button class="icon-btn js-solved" type="button" aria-label="<?= e(t('mark_solved')) ?>">○</button>
            <button class="icon-btn js-bookmark" type="button" aria-label="<?= e(t('bookmark')) ?>">☆</button>
        </div>
    </div>
    <?php if (!empty($problem['translation_missing'])): ?>
        <div class="alert alert-warning py-2 small mb-2"><?= e(t('missing_translation_warning')) ?></div>
    <?php endif; ?>
    <div class="problem-statement"><?= $problem['statement_html'] ?? '' ?></div>
    <?php render_problem_media((int) $problem['id'], 'statement'); ?>
    <?php if (!empty($problem['hint_html'])): ?>
        <div id="<?= e($cardId) ?>-hint" class="soft-panel hint-panel d-none">
            <div class="fw-semibold mb-1"><?= e(t('hint')) ?></div>
            <?= $problem['hint_html'] ?>
            <?php render_problem_media((int) $problem['id'], 'hint'); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($problem['solution_html'])): ?>
        <button class="link-button js-toggle-panel mt-2" type="button" data-target="#<?= e($cardId) ?>-solution"><?= e(t('solution')) ?></button>
        <div id="<?= e($cardId) ?>-solution" class="soft-panel solution-panel d-none">
            <?= $problem['solution_html'] ?>
            <?php render_problem_media((int) $problem['id'], 'solution'); ?>
        </div>
    <?php endif; ?>
</article>
