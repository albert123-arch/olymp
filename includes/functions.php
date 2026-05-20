<?php
declare(strict_types=1);

require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id']) || !db_available()) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

function user_can_manage_content(?array $user = null): bool
{
    $user ??= current_user();
    return in_array((string)($user['role'] ?? ''), ['admin', 'teacher'], true);
}

function require_content_manager(): void
{
    if (!user_can_manage_content()) {
        header('Location: ' . app_url('login.php'));
        exit;
    }
}

function login_user(string $email, string $password): bool
{
    if (!db_available()) {
        return false;
    }

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([trim($email)]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, (string)$user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    return true;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
    }
    session_destroy();
}

function register_user(string $name, string $email, string $password): array
{
    $name = trim($name);
    $email = trim($email);
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        return [false, t('register_error')];
    }
    if (!db_available()) {
        return [false, t('missing_db')];
    }

    try {
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'student']);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)db()->lastInsertId();
        return [true, ''];
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return [false, t('email_exists')];
        }
        return [false, t('register_error')];
    }
}

function app_url(string $path = '', array $params = []): string
{
    $params = array_merge(['lang' => current_lang()], $params);
    $query = http_build_query($params);
    return '/' . ltrim($path, '/') . ($query ? '?' . $query : '');
}

function fetch_all_courses(): array
{
    $stmt = db()->prepare(
        'SELECT c.*, ct.title, ct.summary_html
         FROM courses c
         JOIN course_texts ct ON ct.course_id = c.id AND ct.lang = :lang
         WHERE c.is_published = 1
         ORDER BY c.sort_order, c.id'
    );
    $stmt->execute(['lang' => current_lang()]);
    return $stmt->fetchAll();
}

function fetch_course(string $slug): ?array
{
    $stmt = db()->prepare(
        'SELECT c.*, ct.title, ct.summary_html, ct.overview_html, ct.teacher_guide_html
         FROM courses c
         JOIN course_texts ct ON ct.course_id = c.id AND ct.lang = :lang
         WHERE c.slug = :slug AND c.is_published = 1'
    );
    $stmt->execute(['lang' => current_lang(), 'slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function fetch_chapters(int $courseId): array
{
    $stmt = db()->prepare(
        'SELECT ch.*, cht.title, cht.summary_html
         FROM chapters ch
         JOIN chapter_texts cht ON cht.chapter_id = ch.id AND cht.lang = :lang
         WHERE ch.course_id = :course_id AND ch.is_published = 1
         ORDER BY ch.sort_order, ch.id'
    );
    $stmt->execute(['lang' => current_lang(), 'course_id' => $courseId]);
    return $stmt->fetchAll();
}

function fetch_chapter(string $courseSlug, string $chapterSlug): ?array
{
    $stmt = db()->prepare(
        'SELECT ch.*, c.slug AS course_slug, cht.title, cht.summary_html, cht.theory_html,
                cht.examples_html, cht.worksheet_html, cht.teacher_notes_html
         FROM chapters ch
         JOIN courses c ON c.id = ch.course_id
         JOIN chapter_texts cht ON cht.chapter_id = ch.id AND cht.lang = :lang
         WHERE c.slug = :course_slug AND ch.slug = :chapter_slug AND ch.is_published = 1'
    );
    $stmt->execute(['lang' => current_lang(), 'course_slug' => $courseSlug, 'chapter_slug' => $chapterSlug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function fetch_problems(?int $chapterId = null): array
{
    $sql = 'SELECT p.*, pt.title, pt.statement_html, pt.hint_html, pt.solution_html, pt.teacher_note_html,
                   COALESCE(tag_list.tags_csv, "") AS tags_csv
            FROM problems p
            JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = :lang
            LEFT JOIN (
                SELECT ptag.problem_id, GROUP_CONCAT(t.slug ORDER BY t.slug SEPARATOR ",") AS tags_csv
                FROM problem_tags ptag
                JOIN tags t ON t.id = ptag.tag_id
                GROUP BY ptag.problem_id
            ) tag_list ON tag_list.problem_id = p.id
            WHERE p.is_published = 1';
    $params = ['lang' => current_lang()];
    if ($chapterId !== null) {
        $sql .= ' AND p.chapter_id = :chapter_id';
        $params['chapter_id'] = $chapterId;
    }
    $sql .= ' ORDER BY p.sort_order, p.id';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_problem(string $code): ?array
{
    $stmt = db()->prepare(
        'SELECT p.*, pt.title, pt.statement_html, pt.hint_html, pt.solution_html, pt.teacher_note_html,
                ch.slug AS chapter_slug, c.slug AS course_slug,
                COALESCE(tag_list.tags_csv, "") AS tags_csv
         FROM problems p
         JOIN problem_texts pt ON pt.problem_id = p.id AND pt.lang = :lang
         JOIN chapters ch ON ch.id = p.chapter_id
         JOIN courses c ON c.id = ch.course_id
         LEFT JOIN (
             SELECT ptag.problem_id, GROUP_CONCAT(t.slug ORDER BY t.slug SEPARATOR ",") AS tags_csv
             FROM problem_tags ptag
             JOIN tags t ON t.id = ptag.tag_id
             GROUP BY ptag.problem_id
         ) tag_list ON tag_list.problem_id = p.id
         WHERE p.problem_code = :code AND p.is_published = 1
         LIMIT 1'
    );
    $stmt->execute(['lang' => current_lang(), 'code' => $code]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function fetch_problem_media(int $problemId, string $role): array
{
    $stmt = db()->prepare(
        'SELECT pm.*, pmt.alt_text, pmt.caption_html
         FROM problem_media pm
         LEFT JOIN problem_media_texts pmt ON pmt.media_id = pm.id AND pmt.lang = :lang
         WHERE pm.problem_id = :problem_id AND pm.role = :role
         ORDER BY pm.sort_order, pm.id'
    );
    $stmt->execute(['lang' => current_lang(), 'problem_id' => $problemId, 'role' => $role]);
    return $stmt->fetchAll();
}

function difficulty_label(string $difficulty): string
{
    $labels = [
        'en' => ['intro' => 'Intro', 'core' => 'Core', 'challenge' => 'Challenge'],
        'ru' => ['intro' => 'Вводная', 'core' => 'Базовая', 'challenge' => 'Сложная'],
    ];
    return $labels[current_lang()][$difficulty] ?? $difficulty;
}

function difficulty_level_key(string $difficulty): string
{
    return match ($difficulty) {
        'intro' => 'level1',
        'challenge' => 'level3',
        default => 'level2',
    };
}

function problem_type_key(array $problem): string
{
    $tags = array_filter(explode(',', (string)($problem['tags_csv'] ?? '')));
    if (($problem['problem_code'] ?? '') === 'NT-01-040') {
        return 'warmup';
    }
    if (in_array('counterexample', $tags, true)) {
        return 'counterexample';
    }
    if (in_array('proof', $tags, true) || in_array('consecutive-integers', $tags, true)) {
        return 'proof';
    }
    return 'computation';
}

function render_db_notice(): void
{
    echo '<div class="alert alert-warning my-4">' . h(t('missing_db')) . '</div>';
}
