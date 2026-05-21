<?php
require_once __DIR__ . '/_bootstrap.php';
$users = fetch_all('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
admin_header(t('users'));
?>
<h1 class="h3"><?= e(t('users')) ?></h1><div class="admin-panel p-3"><table class="table table-sm"><tbody><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php admin_footer(); ?>

