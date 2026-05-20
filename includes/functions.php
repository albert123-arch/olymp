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

function db_table_exists(string $table): bool
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
        return false;
    }

    try {
        $stmt = db()->prepare(
            'SELECT COUNT(*)
             FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $stmt->execute([$table]);
        return (int)$stmt->fetchColumn() > 0;
    } catch (Throwable) {
        return false;
    }
}

function db_column_exists(string $table, string $column): bool
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
        return false;
    }

    try {
        $stmt = db()->prepare(
            'SELECT COUNT(*)
             FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int)$stmt->fetchColumn() > 0;
    } catch (Throwable) {
        return false;
    }
}

function db_has_user_problem_tables(): array
{
    return [
        'bookmarks' => db_table_exists('bookmarks')
            && db_column_exists('bookmarks', 'user_id')
            && db_column_exists('bookmarks', 'problem_id'),
        'progress' => db_table_exists('user_problem_progress')
            && db_column_exists('user_problem_progress', 'user_id')
            && db_column_exists('user_problem_progress', 'problem_id')
            && db_column_exists('user_problem_progress', 'status'),
    ];
}

function require_content_manager(): void
{
    if (!user_can_manage_content()) {
        header('Location: ' . url('login.php'));
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

function app_base_url(): string
{
    $configFile = dirname(__DIR__) . '/config.php';
    if (is_file($configFile)) {
        $config = require $configFile;
        if (is_array($config)) {
            $configuredBase = rtrim((string)($config['base_url'] ?? $config['BASE_URL'] ?? ''), '/');
            if ($configuredBase !== '') {
                if (!preg_match('#^(https?:)?//#', $configuredBase) && !str_starts_with($configuredBase, '/') && !str_starts_with($configuredBase, '.')) {
                    $configuredBase = '/' . $configuredBase;
                }
                return $configuredBase;
            }
        }
    }

    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptName === '') {
        return '';
    }

    if (str_contains($scriptName, '/admin/')) {
        return rtrim((string)strstr($scriptName, '/admin/', true), '/');
    }

    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    return $dir === '/' || $dir === '.' ? '' : $dir;
}

function url(string $path = '', array $params = []): string
{
    $params = array_merge(['lang' => current_lang()], $params);
    $query = http_build_query($params);
    $base = app_base_url();
    $cleanPath = ltrim($path, '/');
    $prefix = $base !== '' ? $base : '';
    return $prefix . '/' . $cleanPath . ($query ? '?' . $query : '');
}

function asset_url(string $path): string
{
    $base = app_base_url();
    $cleanPath = ltrim($path, '/');
    $prefix = $base !== '' ? $base : '';
    return $prefix . '/' . $cleanPath;
}

function app_url(string $path = '', array $params = []): string
{
    return url($path, $params);
}

function course_url(string $courseSlug): string
{
    return url('course.php', ['course' => $courseSlug]);
}

function chapter_url(string $courseSlug, string $chapterSlug): string
{
    return url('chapter.php', ['course' => $courseSlug, 'chapter' => $chapterSlug]);
}

function practice_url(string $courseSlug, ?string $chapterSlug = null): string
{
    $params = ['course' => $courseSlug];
    if ($chapterSlug !== null && $chapterSlug !== '') {
        $params['chapter'] = $chapterSlug;
    }
    return url('practice.php', $params);
}

function problem_url(string $problemCode): string
{
    return url('problem.php', ['code' => $problemCode]);
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

function fetch_problems(?int $chapterId = null, ?int $courseId = null): array
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
    } elseif ($courseId !== null) {
        $sql .= ' AND p.chapter_id IN (SELECT id FROM chapters WHERE course_id = :course_id)';
        $params['course_id'] = $courseId;
    }
    $sql .= ' ORDER BY p.sort_order, p.id';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return attach_user_problem_state($stmt->fetchAll());
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
    if (!$row) {
        return null;
    }

    $rows = attach_user_problem_state([$row]);
    return $rows[0] ?? $row;
}

function attach_user_problem_state(array $problems): array
{
    $userId = (int)($_SESSION['user_id'] ?? 0);
    foreach ($problems as &$problem) {
        $problem['is_bookmarked'] = 0;
        $problem['progress_status'] = null;
    }
    unset($problem);

    if ($userId <= 0 || !$problems) {
        return $problems;
    }

    $ids = array_values(array_unique(array_map(static fn(array $row): int => (int)($row['id'] ?? 0), $problems)));
    $ids = array_values(array_filter($ids, static fn(int $id): bool => $id > 0));
    if (!$ids) {
        return $problems;
    }

    $userTables = db_has_user_problem_tables();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    try {
        if ($userTables['bookmarks']) {
            $stmt = db()->prepare("SELECT problem_id FROM bookmarks WHERE user_id = ? AND problem_id IN ($placeholders)");
            $stmt->execute(array_merge([$userId], $ids));
            $bookmarks = array_flip(array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN)));
            foreach ($problems as &$problem) {
                $problem['is_bookmarked'] = isset($bookmarks[(int)($problem['id'] ?? 0)]) ? 1 : 0;
            }
            unset($problem);
        }

        if ($userTables['progress']) {
            $stmt = db()->prepare("SELECT problem_id, status FROM user_problem_progress WHERE user_id = ? AND problem_id IN ($placeholders)");
            $stmt->execute(array_merge([$userId], $ids));
            $progress = [];
            foreach ($stmt->fetchAll() as $row) {
                $progress[(int)$row['problem_id']] = (string)$row['status'];
            }
            foreach ($problems as &$problem) {
                $problem['progress_status'] = $progress[(int)($problem['id'] ?? 0)] ?? null;
            }
            unset($problem);
        }
    } catch (Throwable $e) {
        error_log('Failed to attach user problem state: ' . $e->getMessage());
    }

    return $problems;
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
    return match ($difficulty) {
        'intro' => t('level1'),
        'challenge' => t('challenge_problem'),
        default => t('level2'),
    };
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

function tag_label(string $tag, ?string $lang = null): string
{
    $lang ??= current_lang();
    $labels = [
        'ru' => [
            'absolute-value' => 'Модуль',
            'classification' => 'Классификация',
            'consecutive-integers' => 'Последовательные числа',
            'coprime' => 'Взаимно простые',
            'counterexample' => 'Контрпример',
            'counting' => 'Подсчет',
            'definition' => 'Определение',
            'difference' => 'Разность',
            'digits' => 'Цифры',
            'divisibility' => 'Делимость',
            'divisibility-test' => 'Признаки делимости',
            'divisors' => 'Делители',
            'divisor-counting' => 'Подсчет делителей',
            'equation' => 'Уравнение',
            'exponent' => 'Показатель',
            'exponents' => 'Показатели',
            'expression' => 'Выражение',
            'factorial' => 'Факториал',
            'factorisation' => 'Разложение на множители',
            'prime-factorisation' => 'Разложение на простые множители',
            'gcd' => 'НОД',
            'identity' => 'Тождество',
            'impossibility' => 'Невозможность',
            'integer' => 'Целые числа',
            'lcm' => 'НОК',
            'linear-combination' => 'Линейная комбинация',
            'listing' => 'Перечисление',
            'modular-arithmetic' => 'Сравнения',
            'optimization' => 'Оптимизация',
            'prime' => 'Простые числа',
            'proof' => 'Доказательство',
            'remainder' => 'Остатки',
            'remainders' => 'Остатки',
            'squares' => 'Квадраты',
            'tau-function' => 'Число делителей',
            'trailing-zeros' => 'Нули в конце',
            'transitivity' => 'Транзитивность',
        ],
    ];

    return $labels[$lang][$tag] ?? $tag;
}

function render_db_notice(): void
{
    echo '<div class="alert alert-warning my-4">' . h(t('missing_db')) . '</div>';
}
