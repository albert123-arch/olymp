<?php
require_once __DIR__ . '/_bootstrap.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $json = json_decode((string) ($_POST['json'] ?? ''), true);
    if (is_array($json)) {
        $message = current_lang() === 'ru' ? 'JSON прочитан. Для пакетного импорта используйте scripts/import_problems.php.' : 'JSON parsed. Use scripts/import_problems.php for batch import.';
    }
}
admin_header(t('import_json'));
?>
<form class="admin-panel p-3" method="post"><?= csrf_field() ?><h1 class="h3"><?= e(t('import_json')) ?></h1><?php if($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?><textarea class="form-control font-monospace mb-3" name="json" rows="14" placeholder='[{"problem_code":"NT-01-001"}]'></textarea><button class="btn btn-accent"><?= e(t('import_json')) ?></button></form>
<?php admin_footer(); ?>

