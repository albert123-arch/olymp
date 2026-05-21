<?php
declare(strict_types=1);
$pageTitle = $pageTitle ?? t('site_title');
$user = current_user();
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= h(asset_url('assets/styles.css')) ?>?v=6" rel="stylesheet">
  <script>
    (function () {
      var serverLang = <?= json_encode(current_lang()) ?>;
      var params = new URLSearchParams(window.location.search);
      var savedLang = localStorage.getItem('olymp_lang');
      if (!params.has('lang') && (savedLang === 'en' || savedLang === 'ru') && savedLang !== serverLang) {
        params.set('lang', savedLang);
        window.location.replace(window.location.pathname + '?' + params.toString() + window.location.hash);
        return;
      }
      localStorage.setItem('olymp_lang', serverLang);
      document.cookie = 'lang=' + serverLang + '; path=/; max-age=31536000; SameSite=Lax';
    })();
    window.MathJax = { tex: { inlineMath: [['\\(', '\\)']], displayMath: [['\\[', '\\]']] } };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= h(url('index.php')) ?>"><?= h(t('site_title')) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="<?= h(t('menu')) ?>">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= h(url('index.php')) ?>"><?= h(t('home')) ?></a></li>
        <li class="nav-item"><a class="nav-link" href="<?= h(practice_url('number-theory')) ?>"><?= h(t('practice')) ?></a></li>
        <?php if (user_can_manage_content($user)): ?>
          <li class="nav-item"><a class="nav-link" href="<?= h(url('admin/index.php')) ?>"><?= h(t('admin')) ?></a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if ($user): ?>
          <span class="text-secondary small"><?= h($user['name']) ?> · <?= h(t('role_' . $user['role'])) ?></span>
          <a class="btn btn-sm btn-outline-secondary" href="<?= h(url('logout.php')) ?>"><?= h(t('logout')) ?></a>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-secondary" href="<?= h(url('login.php')) ?>"><?= h(t('login')) ?></a>
          <a class="btn btn-sm btn-outline-primary" href="<?= h(url('register.php')) ?>"><?= h(t('register')) ?></a>
        <?php endif; ?>
        <?php
          $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
          $baseUrl = app_base_url();
          $currentPath = ltrim($scriptName, '/');
          if ($baseUrl !== '' && str_starts_with($scriptName, $baseUrl . '/')) {
              $currentPath = ltrim(substr($scriptName, strlen($baseUrl)), '/');
          }
          $currentLang = current_lang() === 'ru' ? 'RU' : 'EN';
          $toggleLang = current_lang() === 'ru' ? 'en' : 'ru';
        ?>
        <a class="btn btn-sm btn-outline-secondary lang-toggle" href="<?= h(url($currentPath, array_merge($_GET, ['lang' => $toggleLang]))) ?>" aria-label="<?= h(t('language')) ?>"><?= h($currentLang) ?></a>
      </div>
    </div>
  </div>
</nav>
<main class="container py-4">
