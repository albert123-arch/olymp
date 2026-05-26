SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS grade_levels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  grade_number TINYINT UNSIGNED NOT NULL,
  title_ru VARCHAR(100) NOT NULL,
  title_en VARCHAR(100) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY grade_levels_grade_number_unique (grade_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS problem_grade_levels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  problem_id INT UNSIGNED NOT NULL,
  grade_level_id INT UNSIGNED NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY problem_grade_unique (problem_id, grade_level_id),
  KEY problem_grade_levels_problem_id_index (problem_id),
  KEY problem_grade_levels_grade_level_id_index (grade_level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS problem_ladder_grade_levels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  problem_ladder_id INT UNSIGNED NOT NULL,
  grade_level_id INT UNSIGNED NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY ladder_grade_unique (problem_ladder_id, grade_level_id),
  KEY ladder_grade_ladder_idx (problem_ladder_id),
  KEY ladder_grade_level_idx (grade_level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chapter_grade_levels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_id INT UNSIGNED NOT NULL,
  grade_level_id INT UNSIGNED NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY chapter_grade_unique (chapter_id, grade_level_id),
  KEY chapter_grade_levels_chapter_id_index (chapter_id),
  KEY chapter_grade_levels_grade_level_id_index (grade_level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO grade_levels (grade_number, title_ru, title_en, sort_order, is_active, created_at, updated_at) VALUES
(5, '5 класс', 'Grade 5', 5, 1, NOW(), NOW()),
(6, '6 класс', 'Grade 6', 6, 1, NOW(), NOW()),
(7, '7 класс', 'Grade 7', 7, 1, NOW(), NOW()),
(8, '8 класс', 'Grade 8', 8, 1, NOW(), NOW()),
(9, '9 класс', 'Grade 9', 9, 1, NOW(), NOW()),
(10, '10 класс', 'Grade 10', 10, 1, NOW(), NOW()),
(11, '11 класс', 'Grade 11', 11, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  title_ru = VALUES(title_ru),
  title_en = VALUES(title_en),
  sort_order = VALUES(sort_order),
  is_active = VALUES(is_active),
  updated_at = NOW();
