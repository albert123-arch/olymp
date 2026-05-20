<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $message] = register_user(
        (string)($_POST['name'] ?? ''),
        (string)($_POST['email'] ?? ''),
        (string)($_POST['password'] ?? '')
    );
    if ($ok) {
        header('Location: ' . app_url('index.php'));
        exit;
    }
    $error = $message;
}

$pageTitle = t('register_title') . ' | ' . t('site_title');
include __DIR__ . '/includes/layout/header.php';
?>
<section class="auth-shell mx-auto">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 fw-bold mb-3"><?= h(t('register_title')) ?></h1>
      <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
      <form method="post" class="vstack gap-3">
        <div>
          <label class="form-label"><?= h(t('name')) ?></label>
          <input class="form-control" type="text" name="name" autocomplete="name" required>
        </div>
        <div>
          <label class="form-label"><?= h(t('email')) ?></label>
          <input class="form-control" type="email" name="email" autocomplete="email" required>
        </div>
        <div>
          <label class="form-label"><?= h(t('password')) ?></label>
          <input class="form-control" type="password" name="password" autocomplete="new-password" minlength="8" required>
        </div>
        <button class="btn btn-primary" type="submit"><?= h(t('register')) ?></button>
      </form>
      <p class="mb-0 mt-3"><a href="<?= h(app_url('login.php')) ?>"><?= h(t('login')) ?></a></p>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

