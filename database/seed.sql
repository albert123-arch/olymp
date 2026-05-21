SET NAMES utf8mb4;

INSERT INTO languages (id, code, title, is_default, is_active, sort_order) VALUES
(1,'ru','Русский',1,1,1),
(2,'en','English',0,1,2);

INSERT INTO courses (id, slug, sort_order, is_published, created_at, updated_at) VALUES
(1,'number-theory',1,1,NOW(),NOW()),
(2,'algebra',2,1,NOW(),NOW()),
(3,'geometry',3,1,NOW(),NOW()),
(4,'combinatorics',4,1,NOW(),NOW()),
(5,'inequalities',5,1,NOW(),NOW()),
(6,'functional-equations',6,1,NOW(),NOW()),
(7,'mixed-problems',7,1,NOW(),NOW());

INSERT INTO course_texts (course_id, lang, title, description_html) VALUES
(1,'ru','Теория чисел','<p>Делимость, НОД, сравнения, диофантовы уравнения и классические олимпиадные методы.</p>'),
(1,'en','Number Theory','<p>Divisibility, gcd, congruences, Diophantine equations, and classical olympiad methods.</p>'),
(2,'ru','Алгебра','<p>Скоро: многочлены, преобразования, последовательности и уравнения.</p>'),
(2,'en','Algebra','<p>Coming soon: polynomials, transformations, sequences, and equations.</p>'),
(3,'ru','Геометрия','<p>Скоро: углы, окружности, подобие и конфигурации.</p>'),
(3,'en','Geometry','<p>Coming soon: angles, circles, similarity, and configurations.</p>'),
(4,'ru','Комбинаторика','<p>Скоро: подсчёт, инварианты, графы и принцип Дирихле.</p>'),
(4,'en','Combinatorics','<p>Coming soon: counting, invariants, graphs, and pigeonhole principle.</p>'),
(5,'ru','Неравенства','<p>Скоро: классические оценки и методы доказательства.</p>'),
(5,'en','Inequalities','<p>Coming soon: classical estimates and proof methods.</p>'),
(6,'ru','Функциональные уравнения','<p>Скоро: подстановки, инъективность, сюръективность и структуры функций.</p>'),
(6,'en','Functional Equations','<p>Coming soon: substitutions, injectivity, surjectivity, and function structure.</p>'),
(7,'ru','Смешанные задачи','<p>Скоро: подборки для тренировок и контрольных листов.</p>'),
(7,'en','Mixed Problems','<p>Coming soon: mixed sets for practice and worksheets.</p>');

INSERT INTO chapters (id, course_id, slug, sort_order, is_published, created_at, updated_at) VALUES
(1,1,'divisibility-prime-factorisation',1,1,NOW(),NOW()),
(2,1,'gcd-lcm-euclidean-algorithm',2,1,NOW(),NOW()),
(3,1,'remainders-congruences',3,1,NOW(),NOW()),
(4,1,'diophantine-equations',4,1,NOW(),NOW()),
(5,1,'infinite-descent',5,1,NOW(),NOW()),
(6,1,'fermat-euler-theorems',6,1,NOW(),NOW());

INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES
(1,'ru','Делимость и разложение на простые множители','<p>Первая глава о языке делимости и простых множителях.</p>','<p>Запись \(a \mid b\) означает, что существует целое \(k\), для которого \(b=ak\). Простые множители помогают видеть структуру числа: если \(n=p_1^{\alpha_1}\cdots p_s^{\alpha_s}\), то делители строятся выбором степеней от \(0\) до \(\alpha_i\).</p>','<p><strong>Пример.</strong> \(120=2^3\cdot3\cdot5\), поэтому \(8\mid120\), а \(9\nmid120\).</p>','<p>Решите задачи 1-40. Сначала отмечайте делимость через разложение, затем пробуйте доказательства.</p>','<p>Следите, чтобы ученики проговаривали определение делимости, а не только считали на калькуляторе.</p>'),
(1,'en','Divisibility and Prime Factorisation','<p>The first chapter introduces divisibility and prime factors.</p>','<p>The notation \(a \mid b\) means that \(b=ak\) for some integer \(k\). Prime factorisation reveals number structure: if \(n=p_1^{\alpha_1}\cdots p_s^{\alpha_s}\), divisors are formed by choosing exponents from \(0\) to \(\alpha_i\).</p>','<p><strong>Example.</strong> \(120=2^3\cdot3\cdot5\), so \(8\mid120\), while \(9\nmid120\).</p>','<p>Solve problems 1-40. Start with factorisations, then try proofs.</p>','<p>Ask students to state the definition of divisibility, not only compute.</p>'),
(2,'ru','НОД, НОК и алгоритм Евклида','<p>Скоро.</p>','','','',''),
(2,'en','GCD, LCM, and the Euclidean Algorithm','<p>Coming soon.</p>','','','',''),
(3,'ru','Остатки и сравнения','<p>Скоро.</p>','','','',''),
(3,'en','Remainders and Congruences','<p>Coming soon.</p>','','','',''),
(4,'ru','Диофантовы уравнения','<p>Скоро.</p>','','','',''),
(4,'en','Diophantine Equations','<p>Coming soon.</p>','','','',''),
(5,'ru','Бесконечный спуск','<p>Скоро.</p>','','','',''),
(5,'en','Infinite Descent','<p>Coming soon.</p>','','','',''),
(6,'ru','Теоремы Ферма и Эйлера','<p>Скоро.</p>','','','',''),
(6,'en','Fermat and Euler Theorems','<p>Coming soon.</p>','','','','');

INSERT INTO tags (id, slug, created_at) VALUES
(1,'divisibility',NOW()),(2,'factorisation',NOW()),(3,'parity',NOW()),(4,'prime-numbers',NOW()),(5,'remainders',NOW());

INSERT INTO tag_texts (tag_id, lang, title) VALUES
(1,'ru','Делимость'),(1,'en','Divisibility'),
(2,'ru','Разложение'),(2,'en','Factorisation'),
(3,'ru','Чётность'),(3,'en','Parity'),
(4,'ru','Простые числа'),(4,'en','Prime Numbers'),
(5,'ru','Остатки'),(5,'en','Remainders');

INSERT INTO problems (id, chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(1,1,'NT-01-001',1,1,'computation',1,1,NOW(),NOW()),
(2,1,'NT-01-002',2,1,'computation',2,1,NOW(),NOW()),
(3,1,'NT-01-003',3,1,'computation',3,1,NOW(),NOW()),
(4,1,'NT-01-004',4,1,'proof',4,1,NOW(),NOW()),
(5,1,'NT-01-005',5,1,'computation',5,1,NOW(),NOW()),
(6,1,'NT-01-006',6,1,'proof',6,1,NOW(),NOW()),
(7,1,'NT-01-007',7,1,'computation',7,1,NOW(),NOW()),
(8,1,'NT-01-008',8,1,'mixed',8,1,NOW(),NOW()),
(9,1,'NT-01-009',9,1,'proof',9,1,NOW(),NOW()),
(10,1,'NT-01-010',10,1,'computation',10,1,NOW(),NOW()),
(11,1,'NT-01-011',11,2,'proof',11,1,NOW(),NOW()),
(12,1,'NT-01-012',12,2,'computation',12,1,NOW(),NOW()),
(13,1,'NT-01-013',13,2,'proof',13,1,NOW(),NOW()),
(14,1,'NT-01-014',14,2,'mixed',14,1,NOW(),NOW()),
(15,1,'NT-01-015',15,2,'proof',15,1,NOW(),NOW()),
(16,1,'NT-01-016',16,2,'computation',16,1,NOW(),NOW()),
(17,1,'NT-01-017',17,2,'proof',17,1,NOW(),NOW()),
(18,1,'NT-01-018',18,2,'mixed',18,1,NOW(),NOW()),
(19,1,'NT-01-019',19,2,'proof',19,1,NOW(),NOW()),
(20,1,'NT-01-020',20,2,'computation',20,1,NOW(),NOW()),
(21,1,'NT-01-021',21,2,'proof',21,1,NOW(),NOW()),
(22,1,'NT-01-022',22,2,'mixed',22,1,NOW(),NOW()),
(23,1,'NT-01-023',23,2,'proof',23,1,NOW(),NOW()),
(24,1,'NT-01-024',24,2,'computation',24,1,NOW(),NOW()),
(25,1,'NT-01-025',25,2,'proof',25,1,NOW(),NOW()),
(26,1,'NT-01-026',26,3,'challenge',26,1,NOW(),NOW()),
(27,1,'NT-01-027',27,3,'proof',27,1,NOW(),NOW()),
(28,1,'NT-01-028',28,3,'mixed',28,1,NOW(),NOW()),
(29,1,'NT-01-029',29,3,'proof',29,1,NOW(),NOW()),
(30,1,'NT-01-030',30,3,'challenge',30,1,NOW(),NOW()),
(31,1,'NT-01-031',31,3,'proof',31,1,NOW(),NOW()),
(32,1,'NT-01-032',32,3,'mixed',32,1,NOW(),NOW()),
(33,1,'NT-01-033',33,3,'proof',33,1,NOW(),NOW()),
(34,1,'NT-01-034',34,3,'challenge',34,1,NOW(),NOW()),
(35,1,'NT-01-035',35,3,'proof',35,1,NOW(),NOW()),
(36,1,'NT-01-036',36,3,'mixed',36,1,NOW(),NOW()),
(37,1,'NT-01-037',37,3,'proof',37,1,NOW(),NOW()),
(38,1,'NT-01-038',38,3,'challenge',38,1,NOW(),NOW()),
(39,1,'NT-01-039',39,3,'proof',39,1,NOW(),NOW()),
(40,1,'NT-01-040',40,3,'mixed',40,1,NOW(),NOW());

INSERT INTO problem_tags (problem_id, tag_id)
SELECT id, CASE
    WHEN id IN (1,2,4,6,8,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39) THEN 1
    WHEN id IN (3,5,7,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40) THEN 2
    ELSE 3
END FROM problems WHERE chapter_id = 1;

INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html)
SELECT id, 'ru',
CONCAT('Задача ', book_number),
CONCAT('<p>Решите тренировочную задачу по теме делимости №', book_number, '. Найдите и обоснуйте, делится ли число \(', 96 + book_number, '\) на ', 2 + (book_number % 9), '.</p>'),
'<p>Используйте определение делимости: \(a\mid b\), если \(b=ak\) для некоторого целого \(k\).</p>',
CONCAT('<p>Разделим число на указанный делитель и проверим остаток. Если остаток равен \(0\), делимость верна; иначе нет. В этой задаче остаток легко получить прямым вычислением.</p>'),
'<p>Попросите ученика проговорить не только ответ, но и причину.</p>'
FROM problems WHERE chapter_id = 1;

INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html)
SELECT id, 'en',
CONCAT('Problem ', book_number),
CONCAT('<p>Solve divisibility practice problem #', book_number, '. Determine and justify whether \(', 96 + book_number, '\) is divisible by ', 2 + (book_number % 9), '.</p>'),
'<p>Use the definition: \(a\mid b\) if \(b=ak\) for some integer \(k\).</p>',
'<p>Divide the number by the proposed divisor and check the remainder. Divisibility holds exactly when the remainder is \(0\).</p>',
'<p>Ask the student to explain the reason, not only the answer.</p>'
FROM problems WHERE chapter_id = 1;

