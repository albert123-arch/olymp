SET NAMES utf8mb4;

UPDATE chapter_texts
SET
  description_html = REPLACE(REPLACE(description_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  theory_html = REPLACE(REPLACE(theory_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  examples_html = REPLACE(REPLACE(examples_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  worksheet_html = REPLACE(REPLACE(worksheet_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  teacher_notes_html = REPLACE(REPLACE(teacher_notes_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}')
WHERE lang = 'ru';

UPDATE problem_texts
SET
  statement_html = REPLACE(REPLACE(statement_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  hint_html = REPLACE(REPLACE(hint_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  solution_html = REPLACE(REPLACE(solution_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}'),
  teacher_note_html = REPLACE(REPLACE(teacher_note_html, '\\operatorname{lcm}', '\\operatorname{НОК}'), '\\gcd', '\\operatorname{НОД}')
WHERE lang = 'ru';
