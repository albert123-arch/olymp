<?php
/** @var array $course */
require_once __DIR__ . '/../functions.php';
$isReady = ($course['slug'] ?? '') === 'number-theory';
?>
<article class="course-card h-100">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <h2 class="h5 mb-0"><?= e($course['title'] ?? t('missing_translation')) ?><?= missing_translation_badge($course) ?></h2>
        <?php if (!$isReady): ?><span class="badge text-bg-light border"><?= e(t('coming_soon')) ?></span><?php endif; ?>
    </div>
    <div class="text-muted small mb-3"><?= math_html($course['description_html'] ?? '') ?></div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-sm btn-accent" href="<?= e(course_url($course['slug'])) ?>"><?= e($isReady ? t('start_learning') : t('overview')) ?></a>
        <?php if ($isReady): ?>
            <a class="btn btn-sm btn-outline-secondary" href="<?= e(practice_url($course['slug'])) ?>"><?= e(t('start_practice')) ?></a>
        <?php endif; ?>
    </div>
</article>
