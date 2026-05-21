<?php
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_path()
{
    $base = app_config()['app']['base_url'] ?? '';
    return rtrim((string) $base, '/');
}

function url($path, $params = [])
{
    $params = array_merge(['lang' => current_lang()], $params);
    return base_path() . '/' . ltrim($path, '/') . ($params ? '?' . http_build_query($params) : '');
}

function course_url($courseSlug)
{
    return url('course.php', ['course' => $courseSlug]);
}

function chapter_url($courseSlug, $chapterSlug)
{
    return url('chapter.php', ['course' => $courseSlug, 'chapter' => $chapterSlug]);
}

function practice_url($courseSlug, $chapterSlug = null)
{
    $params = ['course' => $courseSlug];
    if ($chapterSlug !== null) {
        $params['chapter'] = $chapterSlug;
    }
    return url('practice.php', $params);
}

function problem_url($problemCode)
{
    return url('problem.php', ['code' => $problemCode]);
}

function setup_guard()
{
    if (!has_real_config()) {
        echo '<div class="alert alert-warning my-4">' . e(t('setup_required')) . '</div>';
    }
}

function localized_select($baseTable, $textTable, $joinKey, $alias = 't')
{
    return "LEFT JOIN {$textTable} {$alias} ON {$alias}.{$joinKey} = {$baseTable}.id AND {$alias}.lang = :lang";
}

function missing_translation_badge($row)
{
    if ($row && !empty($row['translation_missing'])) {
        return '<span class="badge text-bg-warning ms-2">' . e(t('missing_translation')) . '</span>';
    }
    return '';
}

function get_courses($publishedOnly = true)
{
    if (!has_real_config()) {
        return [];
    }
    try {
        $where = $publishedOnly ? 'WHERE c.is_published = 1' : '';
        $descriptionExpr = column_exists('course_texts', 'description_html')
            ? 'ct.description_html'
            : (column_exists('course_texts', 'overview_html')
                ? 'COALESCE(ct.overview_html, ct.summary_html)'
                : 'ct.summary_html');
        $missingExpr = column_exists('course_texts', 'id') ? 'ct.id IS NULL' : 'ct.course_id IS NULL';
        return fetch_all(
            "SELECT c.*, ct.title, {$descriptionExpr} AS description_html,
                    CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
             FROM courses c
             LEFT JOIN course_texts ct ON ct.course_id = c.id AND ct.lang = :lang
             {$where}
             ORDER BY c.sort_order, c.id",
            ['lang' => current_lang()]
        );
    } catch (Throwable $e) {
        $GLOBALS['APP_DB_ERROR'] = $e->getMessage();
        return [];
    }
}

function get_course_by_slug($slug)
{
    try {
        $descriptionExpr = column_exists('course_texts', 'description_html')
            ? 'ct.description_html'
            : (column_exists('course_texts', 'overview_html')
                ? 'COALESCE(ct.overview_html, ct.summary_html)'
                : 'ct.summary_html');
        $missingExpr = column_exists('course_texts', 'id') ? 'ct.id IS NULL' : 'ct.course_id IS NULL';
        return fetch_one(
            "SELECT c.*, ct.title, {$descriptionExpr} AS description_html,
                    CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
             FROM courses c
             LEFT JOIN course_texts ct ON ct.course_id = c.id AND ct.lang = :lang
             WHERE c.slug = :slug",
            ['lang' => current_lang(), 'slug' => $slug]
        );
    } catch (Throwable $e) {
        $GLOBALS['APP_DB_ERROR'] = $e->getMessage();
        return null;
    }
}

function get_chapters_for_course($courseId, $publishedOnly = true)
{
    $where = $publishedOnly ? 'AND ch.is_published = 1' : '';
    $descriptionExpr = column_exists('chapter_texts', 'description_html') ? 'txt.description_html' : 'txt.summary_html';
    $missingExpr = column_exists('chapter_texts', 'id') ? 'txt.id IS NULL' : 'txt.chapter_id IS NULL';
    return fetch_all(
        "SELECT ch.*, txt.title, {$descriptionExpr} AS description_html,
                CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
         FROM chapters ch
         LEFT JOIN chapter_texts txt ON txt.chapter_id = ch.id AND txt.lang = :lang
         WHERE ch.course_id = :course_id {$where}
         ORDER BY ch.sort_order, ch.id",
        ['lang' => current_lang(), 'course_id' => $courseId]
    );
}

function get_chapter_by_slug($courseId, $slug)
{
    $descriptionExpr = column_exists('chapter_texts', 'description_html') ? 'txt.description_html' : 'txt.summary_html';
    $missingExpr = column_exists('chapter_texts', 'id') ? 'txt.id IS NULL' : 'txt.chapter_id IS NULL';
    return fetch_one(
        "SELECT ch.*, txt.title, {$descriptionExpr} AS description_html, txt.theory_html, txt.examples_html, txt.worksheet_html, txt.teacher_notes_html,
                CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
         FROM chapters ch
         LEFT JOIN chapter_texts txt ON txt.chapter_id = ch.id AND txt.lang = :lang
         WHERE ch.course_id = :course_id AND ch.slug = :slug",
        ['lang' => current_lang(), 'course_id' => $courseId, 'slug' => $slug]
    );
}

function get_problem_tags($problemId)
{
    if (!table_exists('tag_texts')) {
        return fetch_all(
            "SELECT tg.slug, tg.slug AS title
             FROM problem_tags pt
             JOIN tags tg ON tg.id = pt.tag_id
             WHERE pt.problem_id = :problem_id
             ORDER BY tg.slug",
            ['problem_id' => $problemId]
        );
    }
    return fetch_all(
        "SELECT tg.slug, tt.title
         FROM problem_tags pt
         JOIN tags tg ON tg.id = pt.tag_id
         LEFT JOIN tag_texts tt ON tt.tag_id = tg.id AND tt.lang = :lang
         WHERE pt.problem_id = :problem_id
         ORDER BY tt.title, tg.slug",
        ['lang' => current_lang(), 'problem_id' => $problemId]
    );
}

function get_problems($filters = [])
{
    $where = ['p.is_published = 1'];
    $params = ['lang' => current_lang()];
    if (!empty($filters['chapter_id'])) {
        $where[] = 'p.chapter_id = :chapter_id';
        $params['chapter_id'] = (int) $filters['chapter_id'];
    }
    if (!empty($filters['course_id'])) {
        $where[] = 'ch.course_id = :course_id';
        $params['course_id'] = (int) $filters['course_id'];
    }
    $whereSql = implode(' AND ', $where);
    $missingExpr = column_exists('problem_texts', 'id') ? 'pt.id IS NULL' : 'pt.problem_id IS NULL';
    return fetch_all(
        "SELECT p.*, pt.title, pt.statement_html, pt.hint_html, pt.solution_html,
                ch.slug AS chapter_slug, c.slug AS course_slug,
                CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
         FROM problems p
         JOIN chapters ch ON ch.id = p.chapter_id
         JOIN courses c ON c.id = ch.course_id
         LEFT JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = :lang
         WHERE {$whereSql}
         ORDER BY p.sort_order, p.book_number, p.id",
        $params
    );
}

function get_problem_by_code($code)
{
    $missingExpr = column_exists('problem_texts', 'id') ? 'pt.id IS NULL' : 'pt.problem_id IS NULL';
    return fetch_one(
        "SELECT p.*, pt.title, pt.statement_html, pt.hint_html, pt.solution_html, pt.teacher_note_html,
                ch.slug AS chapter_slug, c.slug AS course_slug, ch.id AS chapter_id,
                CASE WHEN {$missingExpr} THEN 1 ELSE 0 END AS translation_missing
         FROM problems p
         JOIN chapters ch ON ch.id = p.chapter_id
         JOIN courses c ON c.id = ch.course_id
         LEFT JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = :lang
         WHERE p.problem_code = :code",
        ['lang' => current_lang(), 'code' => $code]
    );
}

function render_stars($difficulty)
{
    if (is_string($difficulty)) {
        $difficulty = ['intro' => 1, 'core' => 2, 'challenge' => 3][$difficulty] ?? (int) $difficulty;
    }
    $difficulty = max(1, min(3, (int) $difficulty));
    return '<span class="stars" aria-label="' . e(t('level') . ' ' . $difficulty . ' ' . t('out_of_3')) . '">' .
        str_repeat('&#9733;', $difficulty) . str_repeat('&#9734;', 3 - $difficulty) .
        '</span>';
}

function save_bookmark($userId, $problemId, $enabled)
{
    if ($enabled) {
        execute_query(
            'INSERT IGNORE INTO bookmarks (user_id, problem_id, created_at) VALUES (?, ?, NOW())',
            [$userId, $problemId]
        );
        return;
    }
    execute_query('DELETE FROM bookmarks WHERE user_id = ? AND problem_id = ?', [$userId, $problemId]);
}

function save_problem_progress($userId, $problemId, $status)
{
    $allowed = ['not_started', 'viewed', 'solved', 'needs_review'];
    if (!in_array($status, $allowed, true)) {
        return;
    }
    $statusType = (string) column_type('user_problem_progress', 'status');
    if (strpos($statusType, "'unseen'") !== false) {
        $legacyStatus = ['not_started' => 'unseen', 'viewed' => 'started', 'solved' => 'solved', 'needs_review' => 'review'][$status];
        execute_query(
            "INSERT INTO user_problem_progress (user_id, problem_id, status, attempts, started_at, solved_at)
             VALUES (?, ?, ?, 0, NOW(), IF(? = 'solved', NOW(), NULL))
             ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW(), solved_at = IF(VALUES(status) = 'solved', NOW(), solved_at)",
            [$userId, $problemId, $legacyStatus, $legacyStatus]
        );
        return;
    }
    execute_query(
        "INSERT INTO user_problem_progress (user_id, problem_id, status, last_opened_at, solved_at)
         VALUES (?, ?, ?, NOW(), IF(? = 'solved', NOW(), NULL))
         ON DUPLICATE KEY UPDATE status = VALUES(status), last_opened_at = NOW(), solved_at = IF(VALUES(status) = 'solved', NOW(), solved_at)",
        [$userId, $problemId, $status, $status]
    );
}
