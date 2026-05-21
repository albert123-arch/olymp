<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && has_real_config()) {
    verify_csrf();
    execute_query(
        'INSERT INTO users (name, email, password_hash, role, created_at, updated_at) VALUES (?, ?, ?, "student", NOW(), NOW())',
        [(string) $_POST['name'], (string) $_POST['email'], password_hash((string) $_POST['password'], PASSWORD_DEFAULT)]
    );
    $message = t('account_created');
}
$pageTitle = t('register');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6">
<form class="admin-panel p-4" method="post">
    <?= csrf_field() ?>
    <h1 class="h4 mb-3"><?= e(t('register')) ?></h1>
    <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <label class="form-label w-100"><?= e(t('name')) ?><input class="form-control" name="name" required></label>
    <label class="form-label w-100"><?= e(t('email')) ?><input class="form-control" type="email" name="email" required></label>
    <label class="form-label w-100"><?= e(t('password')) ?><input class="form-control" type="password" name="password" required></label>
    <button class="btn btn-accent" type="submit"><?= e(t('create_account')) ?></button>
</form>
</div></div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>
