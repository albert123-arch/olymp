<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/components/media-renderer.php';
$code = (string) ($_GET['code'] ?? '');
$problem = has_real_config() && $code ? get_problem_by_code($code) : null;
if (!$problem) {
    http_response_code(has_real_config() ? 404 : 200);
    $problem = ['id' => 0, 'problem_code' => $code, 'title' => t('missing_translation'), 'statement_html' => ''];
}
$tags = $problem['id'] ? get_problem_tags((int) $problem['id']) : [];
$pageTitle = $problem['title'] ?? $problem['problem_code'];
include __DIR__ . '/includes/layout/header.php';
?>
<article class="admin-panel p-3 p-md-4 problem-card" data-problem-code="<?= e($problem['problem_code']) ?>" data-problem-id="<?= e((string) $problem['id']) ?>">
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <a class="small text-decoration-none" href="<?= e(chapter_url($problem['course_slug'] ?? 'number-theory', $problem['chapter_slug'] ?? '')) ?>">&larr; <?= e(t('back_to_chapter')) ?></a>
            <h1 class="h3 mt-2"><?= e($problem['title'] ?? t('missing_translation')) ?><?= missing_translation_badge($problem) ?></h1>
            <div class="text-muted"><?= e($problem['problem_code']) ?> · <?= render_stars((int) ($problem['difficulty'] ?? 1)) ?></div>
        </div>
        <div class="problem-actions align-self-start">
            <button class="icon-btn js-solved" type="button" aria-label="<?= e(t('mark_solved')) ?>">○</button>
            <button class="icon-btn js-bookmark" type="button" aria-label="<?= e(t('bookmark')) ?>">☆</button>
        </div>
    </div>
    <?php if ($tags): ?>
        <div class="mb-3"><?php foreach ($tags as $tag): ?><span class="tag-chip me-1"><?= e($tag['title'] ?? $tag['slug']) ?></span><?php endforeach; ?></div>
    <?php endif; ?>
    <h2 class="h5"><?= e(t('statement')) ?></h2>
    <div class="math-content"><?= $problem['statement_html'] ?? '' ?></div>
    <?php if ($problem['id']) { render_problem_media((int) $problem['id'], 'statement'); } ?>
    <?php if (!empty($problem['hint_html'])): ?>
        <button class="btn btn-sm btn-outline-secondary mt-3 js-toggle-panel" data-target="#hint" type="button">? <?= e(t('hint')) ?></button>
        <div id="hint" class="soft-panel hint-panel d-none math-content"><?= $problem['hint_html'] ?><?php render_problem_media((int) $problem['id'], 'hint'); ?></div>
    <?php endif; ?>
    <?php if (!empty($problem['solution_html'])): ?>
        <button class="btn btn-sm btn-accent mt-3 js-toggle-panel" data-target="#solution" type="button"><?= e(t('solution')) ?></button>
        <div id="solution" class="soft-panel solution-panel d-none math-content"><?= $problem['solution_html'] ?><?php render_problem_media((int) $problem['id'], 'solution'); ?></div>
    <?php endif; ?>
    <?php if (is_teacher() && !empty($problem['teacher_note_html'])): ?>
        <div class="soft-panel mt-3 math-content"><div class="fw-semibold"><?= e(t('teacher_note')) ?></div><?= $problem['teacher_note_html'] ?></div>
    <?php endif; ?>
</article>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
