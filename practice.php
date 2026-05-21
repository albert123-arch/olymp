<?php
require_once __DIR__ . '/includes/functions.php';
$courseSlug = (string) ($_GET['course'] ?? 'number-theory');
$chapterSlug = (string) ($_GET['chapter'] ?? '');
$course = has_real_config() ? get_course_by_slug($courseSlug) : null;
$chapter = ($course && $chapterSlug) ? get_chapter_by_slug((int) $course['id'], $chapterSlug) : null;
$filters = [];
if ($course) { $filters['course_id'] = (int) $course['id']; }
if ($chapter) { $filters['chapter_id'] = (int) $chapter['id']; }
$problems = has_real_config() ? get_problems($filters) : [];
$pageTitle = t('practice');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="mb-4">
    <a class="small text-decoration-none" href="<?= e($chapter ? chapter_url($course['slug'], $chapter['slug']) : course_url($courseSlug)) ?>">&larr; <?= e($chapter ? t('back_to_chapter') : t('back_to_course')) ?></a>
    <h1 class="mt-2"><?= e(t('practice')) ?></h1>
    <div class="text-muted"><?= e($chapter['title'] ?? $course['title'] ?? t('number_theory')) ?></div>
</div>
<div class="row">
    <div class="col-lg-9">
        <?php foreach ($problems as $problem): include __DIR__ . '/includes/components/problem-card.php'; endforeach; ?>
        <?php if (!$problems): ?><div class="alert alert-info"><?= e(t('no_items')) ?></div><?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

