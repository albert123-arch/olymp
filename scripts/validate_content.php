<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$rows = fetch_all(
    "SELECT 'problem' entity, p.problem_code code, l.code lang
     FROM problems p
     JOIN languages l ON l.is_active=1
     LEFT JOIN problem_texts pt ON pt.problem_id=p.id AND pt.lang=l.code
     WHERE pt.id IS NULL OR pt.title='' OR pt.statement_html=''"
);

foreach ($rows as $row) {
    echo "{$row['entity']} {$row['code']} missing {$row['lang']}\n";
}
echo count($rows) . " missing content rows\n";

