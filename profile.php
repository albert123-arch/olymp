<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();
$pageTitle = t('profile');
include __DIR__ . '/includes/layout/header.php';
?>
<div class="admin-panel p-4">
    <h1 class="h4"><?= e(t('profile')) ?></h1>
    <p class="mb-1"><?= e(t('name')) ?>: <?= e($user['name'] ?? '') ?></p>
    <p class="mb-1"><?= e(t('email')) ?>: <?= e($user['email'] ?? '') ?></p>
    <p class="mb-0"><?= e(t('role')) ?>: <?= e($user['role'] ?? '') ?></p>
</div>
<?php include __DIR__ . '/includes/layout/footer.php'; ?>

