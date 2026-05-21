<?php require_once __DIR__ . '/../functions.php'; ?>
<aside class="admin-sidebar">
    <a href="<?= e(url('admin/index.php')) ?>"><?= e(t('dashboard')) ?></a>
    <a href="<?= e(url('admin/courses.php')) ?>"><?= e(t('courses')) ?></a>
    <a href="<?= e(url('admin/chapters.php')) ?>"><?= e(t('chapters')) ?></a>
    <a href="<?= e(url('admin/problems.php')) ?>"><?= e(t('practice_problems')) ?></a>
    <a href="<?= e(url('admin/media.php')) ?>"><?= e(t('media')) ?></a>
    <a href="<?= e(url('admin/import-json.php')) ?>"><?= e(t('import_json')) ?></a>
    <a href="<?= e(url('admin/users.php')) ?>"><?= e(t('users')) ?></a>
    <a href="<?= e(url('admin/translation-check.php')) ?>"><?= e(t('translation_check')) ?></a>
</aside>

