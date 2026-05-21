<?php
require_once __DIR__ . '/includes/functions.php';
$slug = $_GET['course'] ?? 'number-theory';
$course = has_real_config() ? get_course_by_slug((string) $slug) : null;
if (!$course) {
    http_response_code(has_real_config() ? 404 : 200);
    $course = ['id' => 0, 'slug' => (string) $slug, 'title' => t('courses'), 'description_html' => ''];
}
$chapters = has_real_config() ? get_chapters_for_course((int) $course['id']) : [];
$pageTitle = $course['title'] ?? t('courses');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="mb-4">
    <a class="small text-decoration-none" href="<?= e(url('index.php')) ?>">&larr; <?= e(t('home')) ?></a>
    <h1 class="mt-2"><?= e($course['title'] ?? t('missing_translation')) ?><?= missing_translation_badge($course) ?></h1>
    <div class="text-muted math-content"><?= $course['description_html'] ?? '' ?></div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#chapters" type="button"><?= e(t('chapters')) ?></button></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(practice_url($course['slug'])) ?>"><?= e(t('practice')) ?></a></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#worksheets" type="button"><?= e(t('worksheets')) ?></button></li>
    <?php if (is_teacher()): ?><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#teacher" type="button"><?= e(t('teacher_notes')) ?></button></li><?php endif; ?>
</ul>

<div class="tab-content">
    <section class="tab-pane fade show active" id="chapters">
        <div class="vstack gap-3">
            <?php foreach ($chapters as $chapter): include __DIR__ . '/includes/components/chapter-card.php'; endforeach; ?>
            <?php if (!$chapters): ?><div class="alert alert-info"><?= e(t('no_items')) ?></div><?php endif; ?>
        </div>
    </section>
    <section class="tab-pane fade" id="worksheets">
        <div class="admin-panel p-3"><?= e(t('coming_soon')) ?></div>
    </section>
    <?php if (is_teacher()): ?><section class="tab-pane fade" id="teacher"><div class="admin-panel p-3"><?= e(t('teacher_only')) ?></div></section><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
