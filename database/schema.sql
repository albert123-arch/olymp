SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS chapter_progress;
DROP TABLE IF EXISTS user_problem_progress;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS problem_tags;
DROP TABLE IF EXISTS tag_texts;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS problem_media_texts;
DROP TABLE IF EXISTS problem_media;
DROP TABLE IF EXISTS problem_texts;
DROP TABLE IF EXISTS problems;
DROP TABLE IF EXISTS chapter_texts;
DROP TABLE IF EXISTS chapters;
DROP TABLE IF EXISTS course_texts;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS languages;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','admin') NOT NULL DEFAULT 'student',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE languages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(150) NOT NULL UNIQUE,
    sort_order INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE course_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description_html MEDIUMTEXT NULL,
    UNIQUE KEY uq_course_lang (course_id, lang),
    CONSTRAINT fk_course_texts_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chapters (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    slug VARCHAR(150) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY uq_course_chapter_slug (course_id, slug),
    CONSTRAINT fk_chapters_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chapter_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description_html MEDIUMTEXT NULL,
    theory_html MEDIUMTEXT NULL,
    examples_html MEDIUMTEXT NULL,
    worksheet_html MEDIUMTEXT NULL,
    teacher_notes_html MEDIUMTEXT NULL,
    UNIQUE KEY uq_chapter_lang (chapter_id, lang),
    CONSTRAINT fk_chapter_texts_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problems (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT UNSIGNED NOT NULL,
    problem_code VARCHAR(50) NOT NULL UNIQUE,
    book_number INT NULL,
    difficulty TINYINT UNSIGNED NOT NULL DEFAULT 1,
    problem_type ENUM('computation','proof','counterexample','construction','challenge','mixed') NOT NULL DEFAULT 'mixed',
    sort_order INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_problems_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problem_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    problem_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    statement_html MEDIUMTEXT NOT NULL,
    hint_html MEDIUMTEXT NULL,
    solution_html MEDIUMTEXT NULL,
    teacher_note_html MEDIUMTEXT NULL,
    UNIQUE KEY uq_problem_lang (problem_id, lang),
    CONSTRAINT fk_problem_texts_problem FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problem_media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    problem_id INT UNSIGNED NOT NULL,
    role ENUM('statement','hint','solution','teacher_note','extra') NOT NULL DEFAULT 'statement',
    lang VARCHAR(10) NULL,
    file_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_problem_media_problem FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problem_media_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    caption_html MEDIUMTEXT NULL,
    alt_text VARCHAR(255) NULL,
    UNIQUE KEY uq_media_lang (media_id, lang),
    CONSTRAINT fk_media_texts_media FOREIGN KEY (media_id) REFERENCES problem_media(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tag_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    UNIQUE KEY uq_tag_lang (tag_id, lang),
    CONSTRAINT fk_tag_texts_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problem_tags (
    problem_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (problem_id, tag_id),
    CONSTRAINT fk_problem_tags_problem FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    CONSTRAINT fk_problem_tags_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    problem_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_bookmark (user_id, problem_id),
    CONSTRAINT fk_bookmarks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookmarks_problem FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_problem_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    problem_id INT UNSIGNED NOT NULL,
    status ENUM('not_started','viewed','solved','needs_review') NOT NULL DEFAULT 'not_started',
    last_opened_at DATETIME NULL,
    solved_at DATETIME NULL,
    UNIQUE KEY uq_user_problem (user_id, problem_id),
    CONSTRAINT fk_problem_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_problem_progress_problem FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chapter_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    chapter_id INT UNSIGNED NOT NULL,
    status ENUM('not_started','studying','completed') NOT NULL DEFAULT 'not_started',
    last_opened_at DATETIME NULL,
    completed_at DATETIME NULL,
    UNIQUE KEY uq_user_chapter (user_id, chapter_id),
    CONSTRAINT fk_chapter_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_chapter_progress_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

