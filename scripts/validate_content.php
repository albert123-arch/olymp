<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$rows = [];
$languages = table_exists('languages')
    ? fetch_all('SELECT code FROM languages WHERE is_active = 1 ORDER BY sort_order, code')
    : [['code' => 'ru'], ['code' => 'en']];
foreach ($languages as $language) {
    $rows = array_merge($rows, fetch_all(
        "SELECT 'problem' entity, p.problem_code code, ? lang
         FROM problems p
         LEFT JOIN problem_texts pt ON pt.problem_id=p.id AND pt.lang=?
         WHERE pt.problem_id IS NULL OR pt.title='' OR pt.statement_html=''",
        [$language['code'], $language['code']]
    ));
}

foreach ($rows as $row) {
    echo "{$row['entity']} {$row['code']} missing {$row['lang']}\n";
}
echo count($rows) . " missing content rows\n";
