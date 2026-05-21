<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = t('home');
include __DIR__ . '/includes/layout/header.php';
$courses = has_real_config() ? get_courses(true) : [];
?>
<section class="hero">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <p class="text-accent fw-semibold mb-2"><?= e(t('courses')) ?></p>
            <h1 class="display-5 fw-bold mb-3">Olympiad Math</h1>
            <p class="lead text-muted mb-4"><?= e(current_lang() === 'ru' ? 'Платформа для олимпиадной математики: теория, задачи, подсказки и решения в спокойном учебном формате.' : 'An olympiad mathematics platform with theory, problems, hints, and solutions in a focused learning format.') ?></p>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-accent" href="<?= e(course_url('number-theory')) ?>"><?= e(t('start_learning')) ?></a>
                <a class="btn btn-outline-secondary" href="<?= e(practice_url('number-theory')) ?>"><?= e(t('start_practice')) ?></a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="admin-panel p-4">
                <div class="text-muted small mb-2"><?= e(t('continue_learning')) ?></div>
                <div class="h4 mb-2"><?= e(t('number_theory')) ?></div>
                <div class="math-content text-muted">\(a \mid b\), \(\gcd(a,b)\), сравнения, спуск и классические идеи.</div>
            </div>
        </div>
    </div>
</section>

<section id="courses" class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h3 mb-0"><?= e(t('courses')) ?></h2>
    </div>
    <div class="row g-3">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 col-xl-4"><?php include __DIR__ . '/includes/components/course-card.php'; ?></div>
        <?php endforeach; ?>
        <?php if (!$courses): ?>
            <div class="col-12"><div class="alert alert-info"><?= e(t('no_items')) ?></div></div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

