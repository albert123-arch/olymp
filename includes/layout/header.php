<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../csrf.php';
$pageTitle = $pageTitle ?? t('courses');
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · Olympiad Math</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(url('assets/css/app.css', [])) ?>" rel="stylesheet">
    <script>
        window.OLYMP_AUTH = {
            loggedIn: <?= is_logged_in() ? 'true' : 'false' ?>,
            csrf: '<?= e(csrf_token()) ?>',
            progressUrl: '<?= e(url('progress.php')) ?>'
        };
        window.MathJax = {
            tex: { inlineMath: [['\\(', '\\)']], displayMath: [['\\[', '\\]']] },
            options: { skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'] },
            chtml: { linebreaks: { automatic: true, width: 'container' } }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-accent" href="<?= e(url('index.php')) ?>"><?= icon('courses') ?> Olympiad Math</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="<?= e(t('menu')) ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= e(url('index.php')) ?>"><?= icon('home') ?> <?= e(t('home')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('index.php')) ?>#courses"><?= icon('courses') ?> <?= e(t('courses')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(practice_url('number-theory')) ?>"><?= icon('practice') ?> <?= e(t('practice')) ?></a></li>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('admin/index.php')) ?>"><?= icon('admin') ?> <?= e(t('admin')) ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php include __DIR__ . '/../components/language-switcher.php'; ?>
                <?php if (is_logged_in()): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('profile.php')) ?>"><?= icon('user') ?> <?= e(t('profile')) ?></a>
                    <a class="btn btn-sm btn-accent" href="<?= e(url('logout.php')) ?>"><?= icon('logout') ?> <?= e(t('logout')) ?></a>
                <?php else: ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('login.php')) ?>"><?= icon('login') ?> <?= e(t('login')) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="py-4">
<div class="container">
<?php setup_guard(); ?>
