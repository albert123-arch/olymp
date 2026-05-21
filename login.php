<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && has_real_config()) {
    verify_csrf();
    if (login_user((string) ($_POST['email'] ?? ''), (string) ($_POST['password'] ?? ''))) {
        header('Location: ' . url('profile.php'));
        exit;
    }
    $error = t('invalid_login');
}
$pageTitle = t('login');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <form class="admin-panel p-4" method="post">
            <?= csrf_field() ?>
            <h1 class="h4 mb-3"><?= e(t('login')) ?></h1>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <label class="form-label"><?= e(t('email')) ?><input class="form-control" type="email" name="email" required></label>
            <label class="form-label w-100"><?= e(t('password')) ?><input class="form-control" type="password" name="password" required></label>
            <button class="btn btn-accent w-100" type="submit"><?= e(t('login')) ?></button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
