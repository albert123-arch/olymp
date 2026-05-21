<?php
require_once __DIR__ . '/_bootstrap.php';
$missingTranslations = 0;
foreach (get_available_languages() as $language) {
    $missingTranslations += (int) (fetch_one(
        "SELECT COUNT(*) c
         FROM problems p
         LEFT JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = ?
         WHERE pt.problem_id IS NULL OR pt.title = '' OR pt.statement_html = ''",
        [$language['code']]
    )['c'] ?? 0);
}
$counts = [
    'total_courses' => fetch_one('SELECT COUNT(*) c FROM courses')['c'] ?? 0,
    'total_chapters' => fetch_one('SELECT COUNT(*) c FROM chapters')['c'] ?? 0,
    'total_problems' => fetch_one('SELECT COUNT(*) c FROM problems')['c'] ?? 0,
    'published_problems' => fetch_one('SELECT COUNT(*) c FROM problems WHERE is_published = 1')['c'] ?? 0,
    'draft_problems' => fetch_one('SELECT COUNT(*) c FROM problems WHERE is_published = 0')['c'] ?? 0,
    'missing_translations' => $missingTranslations,
];
admin_header(t('dashboard'));
?>
<h1 class="h3 mb-3"><?= e(t('dashboard')) ?></h1>
<div class="row g-3">
    <?php foreach ($counts as $label => $value): ?>
        <div class="col-sm-6 col-xl-4"><div class="admin-panel p-3"><div class="text-muted small"><?= e(t($label)) ?></div><div class="display-6"><?= e((string) $value) ?></div></div></div>
    <?php endforeach; ?>
</div>
<?php admin_footer(); ?>
