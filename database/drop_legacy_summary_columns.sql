SET NAMES utf8mb4;

ALTER TABLE course_texts
    DROP COLUMN IF EXISTS summary_html,
    DROP COLUMN IF EXISTS overview_html;

ALTER TABLE chapter_texts
    DROP COLUMN IF EXISTS summary_html;
