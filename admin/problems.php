<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_content_manager();
$pageTitle = t('problems') . ' | ' . t('admin');
include dirname(__DIR__) . '/includes/layout/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="fw-bold mb-0"><?= h(t('problems')) ?></h1>
  <a class="btn btn-primary" href="<?= h(app_url('admin/problem-edit.php')) ?>"><?= h(t('create')) ?></a>
</div>
<?php if (!db_available()): render_db_notice(); else: ?>
<?php $rows = db()->query("SELECT p.*, en.title title_en, ru.title title_ru, ch.slug chapter_slug FROM problems p JOIN chapters ch ON ch.id=p.chapter_id LEFT JOIN problem_texts en ON en.problem_id=p.id AND en.lang='en' LEFT JOIN problem_texts ru ON ru.problem_id=p.id AND ru.lang='ru' ORDER BY p.sort_order, p.id")->fetchAll(); ?>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th><?= h(t('code')) ?></th><th><?= h(t('chapter')) ?></th><th>EN</th><th>RU</th><th><?= h(t('difficulty')) ?></th><th><?= h(t('status')) ?></th><th></th></tr></thead><tbody>
<?php foreach ($rows as $row): ?>
  <tr>
    <td><span class="badge text-bg-light border"><?= h($row['problem_code']) ?></span></td>
    <td><?= h($row['chapter_slug']) ?></td>
    <td><?= h($row['title_en'] ?? '') ?></td>
    <td><?= h($row['title_ru'] ?? '') ?></td>
    <td><?= h($row['difficulty']) ?></td>
    <td><?= h((int)$row['is_published'] === 1 ? t('published') : t('draft')) ?></td>
    <td><a class="btn btn-sm btn-outline-primary" href="<?= h(app_url('admin/problem-edit.php', ['id' => $row['id']])) ?>"><?= h(t('edit')) ?></a></td>
  </tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/includes/layout/footer.php'; ?>
