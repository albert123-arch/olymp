SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS languages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO languages (code, title, is_default, is_active, sort_order) VALUES
('ru', 'Русский', 1, 1, 1),
('en', 'English', 0, 1, 2)
ON DUPLICATE KEY UPDATE
title = VALUES(title),
is_default = VALUES(is_default),
is_active = VALUES(is_active),
sort_order = VALUES(sort_order);

ALTER TABLE course_texts
    MODIFY lang VARCHAR(10) NOT NULL,
    ADD COLUMN IF NOT EXISTS description_html MEDIUMTEXT NULL;

ALTER TABLE chapter_texts
    MODIFY lang VARCHAR(10) NOT NULL,
    ADD COLUMN IF NOT EXISTS description_html MEDIUMTEXT NULL;

ALTER TABLE course_texts
    DROP COLUMN IF EXISTS summary_html,
    DROP COLUMN IF EXISTS overview_html;

ALTER TABLE chapter_texts
    DROP COLUMN IF EXISTS summary_html;

ALTER TABLE problem_texts
    MODIFY lang VARCHAR(10) NOT NULL;

ALTER TABLE problems
    ADD COLUMN IF NOT EXISTS difficulty_new TINYINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS problem_type ENUM('computation','proof','counterexample','construction','challenge','mixed') NOT NULL DEFAULT 'mixed' AFTER difficulty;

UPDATE problems
SET difficulty_new = CASE difficulty
    WHEN 'intro' THEN 1
    WHEN 'core' THEN 2
    WHEN 'challenge' THEN 3
    ELSE 1
END
WHERE difficulty_new IS NULL;

ALTER TABLE problems
    DROP COLUMN difficulty;

ALTER TABLE problems
    CHANGE difficulty_new difficulty TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE problem_media
    ADD COLUMN IF NOT EXISTS lang VARCHAR(10) NULL AFTER role,
    ADD COLUMN IF NOT EXISTS original_name VARCHAR(255) NOT NULL DEFAULT '' AFTER file_path,
    ADD COLUMN IF NOT EXISTS file_size INT UNSIGNED NOT NULL DEFAULT 0 AFTER mime_type,
    ADD COLUMN IF NOT EXISTS is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order;

ALTER TABLE problem_media_texts
    MODIFY lang VARCHAR(10) NOT NULL;

CREATE TABLE IF NOT EXISTS tag_texts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag_id INT UNSIGNED NOT NULL,
    lang VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    UNIQUE KEY uq_tag_lang (tag_id, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO tag_texts (tag_id, lang, title)
SELECT id, 'en', REPLACE(slug, '-', ' ')
FROM tags;

INSERT IGNORE INTO tag_texts (tag_id, lang, title)
SELECT id, 'ru', REPLACE(slug, '-', ' ')
FROM tags;

SET FOREIGN_KEY_CHECKS = 1;
