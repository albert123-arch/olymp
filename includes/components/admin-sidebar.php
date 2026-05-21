<?php require_once __DIR__ . '/../functions.php'; ?>
<aside class="admin-sidebar">
    <a href="<?= e(url('admin/index.php')) ?>"><?= icon('admin') ?> <?= e(t('dashboard')) ?></a>
    <a href="<?= e(url('admin/courses.php')) ?>"><?= icon('courses') ?> <?= e(t('courses')) ?></a>
    <a href="<?= e(url('admin/chapters.php')) ?>"><?= icon('courses') ?> <?= e(t('chapters')) ?></a>
    <a href="<?= e(url('admin/problems.php')) ?>"><?= icon('practice') ?> <?= e(t('practice_problems')) ?></a>
    <a href="<?= e(url('admin/media.php')) ?>"><?= icon('media') ?> <?= e(t('media')) ?></a>
    <a href="<?= e(url('admin/import-json.php')) ?>"><?= icon('upload') ?> <?= e(t('import_json')) ?></a>
    <a href="<?= e(url('admin/users.php')) ?>"><?= icon('user') ?> <?= e(t('users')) ?></a>
    <a href="<?= e(url('admin/translation-check.php')) ?>"><?= icon('language') ?> <?= e(t('translation_check')) ?></a>
</aside>
