<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$courseSlug = $argv[1] ?? 'number-theory';
$lang = $argv[2] ?? 'ru';
$_GET['lang'] = $lang;

$course = get_course_by_slug($courseSlug);
if (!$course) {
    exit("Course not found\n");
}

echo "# " . $course['title'] . "\n\n";
foreach (get_chapters_for_course((int) $course['id']) as $chapter) {
    echo "## " . $chapter['title'] . "\n\n";
    foreach (get_problems(['chapter_id' => (int) $chapter['id']]) as $problem) {
        echo $problem['book_number'] . ". " . strip_tags($problem['statement_html']) . "\n\n";
    }
}

