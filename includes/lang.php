<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

const DEFAULT_LANG = 'ru';

$GLOBALS['TRANSLATIONS'] = [
    'ru' => [
        'home' => 'Главная',
        'courses' => 'Курсы',
        'practice' => 'Практика',
        'worksheets' => 'Листы заданий',
        'teacher_dashboard' => 'Панель учителя',
        'admin' => 'Админ',
        'login' => 'Войти',
        'logout' => 'Выйти',
        'register' => 'Регистрация',
        'profile' => 'Профиль',
        'number_theory' => 'Теория чисел',
        'algebra' => 'Алгебра',
        'geometry' => 'Геометрия',
        'combinatorics' => 'Комбинаторика',
        'inequalities' => 'Неравенства',
        'functional_equations' => 'Функциональные уравнения',
        'mixed_problems' => 'Смешанные задачи',
        'overview' => 'Обзор',
        'chapters' => 'Главы',
        'theory' => 'Теория',
        'examples' => 'Примеры',
        'worked_examples' => 'Разобранные примеры',
        'practice_problems' => 'Задачи',
        'worksheet' => 'Лист заданий',
        'teacher_notes' => 'Заметки учителя',
        'hint' => 'Подсказка',
        'solution' => 'Решение',
        'open_problem' => 'Открыть задачу',
        'bookmark' => 'В закладки',
        'bookmarked' => 'В закладках',
        'mark_solved' => 'Отметить решённой',
        'solved' => 'Решено',
        'studied' => 'Изучено',
        'difficulty' => 'Сложность',
        'level' => 'Уровень',
        'tags' => 'Теги',
        'search' => 'Поиск',
        'filter' => 'Фильтр',
        'coming_soon' => 'Скоро',
        'start_learning' => 'Начать обучение',
        'start_practice' => 'Начать практику',
        'continue_learning' => 'Продолжить',
        'back_to_course' => 'Назад к курсу',
        'back_to_chapter' => 'Назад к главе',
        'upload_image' => 'Загрузить рисунок',
        'statement' => 'Условие',
        'teacher_note' => 'Заметка учителя',
        'publish' => 'Опубликовать',
        'draft' => 'Черновик',
        'save' => 'Сохранить',
        'delete' => 'Удалить',
        'edit' => 'Редактировать',
        'missing_translation' => 'Нет перевода',
        'missing_translation_warning' => 'Нет перевода для выбранного языка',
        'no_items' => 'Пока нет материалов',
        'teacher_only' => 'Материал для учителя',
        'dashboard' => 'Панель',
        'published' => 'Опубликовано',
        'drafts' => 'Черновики',
        'users' => 'Пользователи',
        'media' => 'Медиа',
        'import_json' => 'Импорт JSON',
        'translation_check' => 'Проверка переводов',
        'email' => 'Email',
        'password' => 'Пароль',
        'name' => 'Имя',
        'role' => 'Роль',
        'create_account' => 'Создать аккаунт',
        'setup_required' => 'Нужно создать includes/config.php и импортировать базу данных.',
        'title' => 'Заголовок',
        'description' => 'Описание',
        'entity' => 'Сущность',
        'code' => 'Код',
        'language' => 'Язык',
        'slug' => 'Адрес',
        'sort_order' => 'Порядок',
        'total_courses' => 'Курсы',
        'total_chapters' => 'Главы',
        'total_problems' => 'Задачи',
        'published_problems' => 'Опубликованные задачи',
        'draft_problems' => 'Черновики задач',
        'missing_translations' => 'Нет переводов',
        'out_of_3' => 'из 3',
        'problem_type' => 'Тип задачи',
        'computation' => 'Вычисление',
        'proof' => 'Доказательство',
        'counterexample' => 'Контрпример',
        'construction' => 'Построение',
        'challenge' => 'Сложная задача',
        'mixed' => 'Смешанная',
        'image_role' => 'Роль изображения',
        'extra' => 'Дополнительно',
        'menu' => 'Меню',
        'invalid_login' => 'Неверный email или пароль',
        'account_created' => 'Аккаунт создан. Теперь можно войти.',
    ],
    'en' => [
        'home' => 'Home',
        'courses' => 'Courses',
        'practice' => 'Practice',
        'worksheets' => 'Worksheets',
        'teacher_dashboard' => 'Teacher Dashboard',
        'admin' => 'Admin',
        'login' => 'Login',
        'logout' => 'Logout',
        'register' => 'Register',
        'profile' => 'Profile',
        'number_theory' => 'Number Theory',
        'algebra' => 'Algebra',
        'geometry' => 'Geometry',
        'combinatorics' => 'Combinatorics',
        'inequalities' => 'Inequalities',
        'functional_equations' => 'Functional Equations',
        'mixed_problems' => 'Mixed Problems',
        'overview' => 'Overview',
        'chapters' => 'Chapters',
        'theory' => 'Theory',
        'examples' => 'Examples',
        'worked_examples' => 'Worked Examples',
        'practice_problems' => 'Practice Problems',
        'worksheet' => 'Worksheet',
        'teacher_notes' => 'Teacher Notes',
        'hint' => 'Hint',
        'solution' => 'Solution',
        'open_problem' => 'Open Problem',
        'bookmark' => 'Bookmark',
        'bookmarked' => 'Bookmarked',
        'mark_solved' => 'Mark as solved',
        'solved' => 'Solved',
        'studied' => 'Studied',
        'difficulty' => 'Difficulty',
        'level' => 'Level',
        'tags' => 'Tags',
        'search' => 'Search',
        'filter' => 'Filter',
        'coming_soon' => 'Coming Soon',
        'start_learning' => 'Start Learning',
        'start_practice' => 'Start Practice',
        'continue_learning' => 'Continue Learning',
        'back_to_course' => 'Back to Course',
        'back_to_chapter' => 'Back to Chapter',
        'upload_image' => 'Upload Image',
        'statement' => 'Statement',
        'teacher_note' => 'Teacher Note',
        'publish' => 'Publish',
        'draft' => 'Draft',
        'save' => 'Save',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'missing_translation' => 'Missing Translation',
        'missing_translation_warning' => 'Missing translation for selected language',
        'no_items' => 'No materials yet',
        'teacher_only' => 'Teacher material',
        'dashboard' => 'Dashboard',
        'published' => 'Published',
        'drafts' => 'Drafts',
        'users' => 'Users',
        'media' => 'Media',
        'import_json' => 'Import JSON',
        'translation_check' => 'Translation Check',
        'email' => 'Email',
        'password' => 'Password',
        'name' => 'Name',
        'role' => 'Role',
        'create_account' => 'Create account',
        'setup_required' => 'Create includes/config.php and import the database first.',
        'title' => 'Title',
        'description' => 'Description',
        'entity' => 'Entity',
        'code' => 'Code',
        'language' => 'Language',
        'slug' => 'Slug',
        'sort_order' => 'Sort order',
        'total_courses' => 'Courses',
        'total_chapters' => 'Chapters',
        'total_problems' => 'Problems',
        'published_problems' => 'Published problems',
        'draft_problems' => 'Draft problems',
        'missing_translations' => 'Missing translations',
        'out_of_3' => 'out of 3',
        'problem_type' => 'Problem type',
        'computation' => 'Computation',
        'proof' => 'Proof',
        'counterexample' => 'Counterexample',
        'construction' => 'Construction',
        'challenge' => 'Challenge',
        'mixed' => 'Mixed',
        'image_role' => 'Image role',
        'extra' => 'Extra',
        'menu' => 'Menu',
        'invalid_login' => 'Invalid email or password',
        'account_created' => 'Account created. You can log in now.',
    ],
];

function supported_lang_codes(): array
{
    return array_keys($GLOBALS['TRANSLATIONS']);
}

function current_lang(): string
{
    $lang = $_GET['lang'] ?? $_COOKIE['lang'] ?? DEFAULT_LANG;
    $lang = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $lang);
    return in_array($lang, supported_lang_codes(), true) ? $lang : DEFAULT_LANG;
}

function t(string $key): string
{
    $lang = current_lang();
    $dict = $GLOBALS['TRANSLATIONS'];
    return $dict[$lang][$key] ?? $dict[DEFAULT_LANG][$key] ?? ('[' . $key . ']');
}

function lang_url(string $lang): string
{
    $params = $_GET;
    $params['lang'] = $lang;
    $path = parse_url($_SERVER['REQUEST_URI'] ?? 'index.php', PHP_URL_PATH) ?: 'index.php';
    return $path . '?' . http_build_query($params);
}

function get_available_languages(): array
{
    if (has_real_config()) {
        try {
            $rows = fetch_all('SELECT code, title, is_default, is_active FROM languages WHERE is_active = 1 ORDER BY sort_order, title');
            if ($rows) {
                return $rows;
            }
        } catch (Throwable $e) {
            // Fall through to static languages during initial setup.
        }
    }
    return [
        ['code' => 'ru', 'title' => 'Русский', 'is_default' => 1, 'is_active' => 1],
        ['code' => 'en', 'title' => 'English', 'is_default' => 0, 'is_active' => 1],
    ];
}
