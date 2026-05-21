<?php
require_once __DIR__ . '/_bootstrap.php';
$missing = [];
foreach (get_available_languages() as $language) {
    $code = $language['code'];
    $missing = array_merge($missing, fetch_all(
        "SELECT 'course' entity, c.slug code, ? lang
         FROM courses c
         LEFT JOIN course_texts ct ON ct.course_id=c.id AND ct.lang=?
         WHERE ct.course_id IS NULL OR ct.title=''",
        [$code, $code]
    ));
    $missing = array_merge($missing, fetch_all(
        "SELECT 'chapter' entity, ch.slug code, ? lang
         FROM chapters ch
         LEFT JOIN chapter_texts txt ON txt.chapter_id=ch.id AND txt.lang=?
         WHERE txt.chapter_id IS NULL OR txt.title=''",
        [$code, $code]
    ));
    $missing = array_merge($missing, fetch_all(
        "SELECT 'problem' entity, p.problem_code code, ? lang
         FROM problems p
         LEFT JOIN problem_texts pt ON pt.problem_id=p.id AND pt.lang=?
         WHERE pt.problem_id IS NULL OR pt.title='' OR pt.statement_html=''",
        [$code, $code]
    ));
}
usort($missing, fn($a, $b) => [$a['entity'], $a['code'], $a['lang']] <=> [$b['entity'], $b['code'], $b['lang']]);
admin_header(t('translation_check'));
?>
<h1 class="h3"><?= e(t('translation_check')) ?></h1>
<div class="admin-panel p-3"><table class="table table-sm"><thead><tr><th><?= e(t('entity')) ?></th><th><?= e(t('code')) ?></th><th><?= e(t('language')) ?></th></tr></thead><tbody><?php foreach($missing as $m): ?><tr><td><?= e($m['entity']) ?></td><td><?= e($m['code']) ?></td><td><span class="badge text-bg-warning"><?= e($m['lang']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?php admin_footer(); ?>
