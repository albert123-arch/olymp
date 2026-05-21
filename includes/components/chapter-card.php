<?php
/** @var array $chapter */
/** @var array $course */
require_once __DIR__ . '/../functions.php';
?>
<article class="chapter-card">
    <div>
        <div class="small text-muted">#<?= e((string) ($chapter['sort_order'] ?? '')) ?></div>
        <h3 class="h6 mb-1"><?= e($chapter['title'] ?? t('missing_translation')) ?><?= missing_translation_badge($chapter) ?></h3>
        <div class="text-muted small"><?= $chapter['description_html'] ?? '' ?></div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-sm btn-outline-secondary" href="<?= e(chapter_url($course['slug'], $chapter['slug'])) ?>"><?= e(t('theory')) ?></a>
        <a class="btn btn-sm btn-accent" href="<?= e(practice_url($course['slug'], $chapter['slug'])) ?>"><?= e(t('practice')) ?></a>
    </div>
</article>

