<?php
require_once __DIR__ . '/_bootstrap.php';
$missing = fetch_all(
    "SELECT 'course' entity, c.slug code, l.code lang
     FROM courses c JOIN languages l ON l.is_active=1
     LEFT JOIN course_texts ct ON ct.course_id=c.id AND ct.lang=l.code
     WHERE ct.id IS NULL OR ct.title=''
     UNION ALL
     SELECT 'chapter', ch.slug, l.code FROM chapters ch JOIN languages l ON l.is_active=1 LEFT JOIN chapter_texts txt ON txt.chapter_id=ch.id AND txt.lang=l.code WHERE txt.id IS NULL OR txt.title=''
     UNION ALL
     SELECT 'problem', p.problem_code, l.code FROM problems p JOIN languages l ON l.is_active=1 LEFT JOIN problem_texts pt ON pt.problem_id=p.id AND pt.lang=l.code WHERE pt.id IS NULL OR pt.title='' OR pt.statement_html=''
     ORDER BY entity, code, lang"
);
admin_header(t('translation_check'));
?>
<h1 class="h3"><?= e(t('translation_check')) ?></h1>
<div class="admin-panel p-3"><table class="table table-sm"><thead><tr><th><?= e(t('entity')) ?></th><th><?= e(t('code')) ?></th><th><?= e(t('language')) ?></th></tr></thead><tbody><?php foreach($missing as $m): ?><tr><td><?= e($m['entity']) ?></td><td><?= e($m['code']) ?></td><td><span class="badge text-bg-warning"><?= e($m['lang']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?php admin_footer(); ?>
