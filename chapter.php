<?php
require_once __DIR__ . '/includes/functions.php';
$courseSlug = (string) ($_GET['course'] ?? 'number-theory');
$chapterSlug = (string) ($_GET['chapter'] ?? '');
$course = has_real_config() ? get_course_by_slug($courseSlug) : null;
$chapter = ($course && $chapterSlug) ? get_chapter_by_slug((int) $course['id'], $chapterSlug) : null;
if (!$course || !$chapter) {
    http_response_code(has_real_config() ? 404 : 200);
    $course = $course ?: ['id' => 0, 'slug' => $courseSlug, 'title' => t('courses')];
    $chapter = ['id' => 0, 'slug' => $chapterSlug, 'title' => t('chapters')];
}
$problems = has_real_config() ? get_problems(['chapter_id' => (int) $chapter['id']]) : [];
$pageTitle = $chapter['title'] ?? t('chapters');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="mb-4">
    <a class="small text-decoration-none" href="<?= e(course_url($course['slug'])) ?>">&larr; <?= e(t('back_to_course')) ?></a>
    <h1 class="mt-2"><?= e($chapter['title'] ?? t('missing_translation')) ?><?= missing_translation_badge($chapter) ?></h1>
    <div class="text-muted math-content"><?= $chapter['description_html'] ?? '' ?></div>
</div>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#theory" type="button"><?= e(t('theory')) ?></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#examples" type="button"><?= e(t('examples')) ?></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#problems" type="button"><?= e(t('practice_problems')) ?></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#worksheet" type="button"><?= e(t('worksheet')) ?></button></li>
    <?php if (is_teacher()): ?><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#teacher" type="button"><?= e(t('teacher_notes')) ?></button></li><?php endif; ?>
</ul>
<div class="tab-content">
    <section class="tab-pane fade show active admin-panel p-3 math-content" id="theory"><?= $chapter['theory_html'] ?? e(t('coming_soon')) ?></section>
    <section class="tab-pane fade admin-panel p-3 math-content" id="examples"><?= $chapter['examples_html'] ?? e(t('coming_soon')) ?></section>
    <section class="tab-pane fade" id="problems">
        <?php foreach ($problems as $problem): include __DIR__ . '/includes/components/problem-card.php'; endforeach; ?>
        <?php if (!$problems): ?><div class="alert alert-info"><?= e(t('no_items')) ?></div><?php endif; ?>
    </section>
    <section class="tab-pane fade admin-panel p-3 math-content" id="worksheet"><?= $chapter['worksheet_html'] ?? e(t('coming_soon')) ?></section>
    <?php if (is_teacher()): ?><section class="tab-pane fade admin-panel p-3 math-content" id="teacher"><?= $chapter['teacher_notes_html'] ?? e(t('teacher_only')) ?></section><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

