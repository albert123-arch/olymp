-- Repair Russian DB content and ensure starter problems exist.
-- Run this in phpMyAdmin for the Olympiad Maths database.
-- This updates database rows; it does not add PHP hardcoded content.

SET NAMES utf8mb4;

REPLACE INTO course_texts (course_id, lang, title, summary_html, overview_html, teacher_guide_html)
SELECT id, 'ru',
CASE slug WHEN 'number-theory' THEN 'Теория чисел' WHEN 'algebra' THEN 'Алгебра' WHEN 'geometry' THEN 'Геометрия' WHEN 'combinatorics' THEN 'Комбинаторика' WHEN 'inequalities' THEN 'Неравенства' WHEN 'functional-equations' THEN 'Функциональные уравнения' ELSE 'Смешанные задачи' END,
CASE slug WHEN 'number-theory' THEN 'Делимость, простые числа, сравнения, диофантовы уравнения и классические олимпиадные методы.' WHEN 'algebra' THEN 'Тождества, многочлены, последовательности, уравнения и олимпиадная алгебра.' WHEN 'geometry' THEN 'Треугольники, окружности, площади, преобразования и методы доказательства.' WHEN 'combinatorics' THEN 'Подсчет, принцип Дирихле, инварианты, раскраски, графы и игры.' WHEN 'inequalities' THEN 'AM-GM, Коши, перестановки и олимпиадные неравенства.' WHEN 'functional-equations' THEN 'Подстановки, инъективность, сюръективность и структура функций.' ELSE 'Смешанные подборки задач и пробные олимпиады.' END,
CASE slug WHEN 'number-theory' THEN '<p>Начните с делимости и разложения на простые множители, затем переходите к сравнениям и классическим теоремам.</p>' ELSE '<p>Этот курс запланирован для следующего этапа.</p>' END,
CASE slug WHEN 'number-theory' THEN '<p>Сначала работайте с определениями, затем просите учеников проговаривать шаблоны доказательств перед применением формул.</p>' ELSE '<p>Методические заметки появятся вместе с первой опубликованной главой.</p>' END
FROM courses;

REPLACE INTO chapter_texts (chapter_id, lang, title, summary_html, theory_html, examples_html, worksheet_html, teacher_notes_html)
SELECT id, 'ru',
CASE slug WHEN 'divisibility-prime-factorisation' THEN 'Делимость и разложение на простые множители' WHEN 'gcd-lcm-euclidean-algorithm' THEN 'НОД, НОК и алгоритм Евклида' WHEN 'modular-arithmetic' THEN 'Модульная арифметика' WHEN 'congruences-remainders' THEN 'Сравнения и остатки' WHEN 'diophantine-equations' THEN 'Диофантовы уравнения' WHEN 'infinite-descent' THEN 'Бесконечный спуск' ELSE 'Ферма и Эйлер' END,
CASE slug WHEN 'divisibility-prime-factorisation' THEN 'Основные определения, разложение на простые множители, подсчет делителей и культура доказательства.' ELSE 'Скоро.' END,
CASE slug WHEN 'divisibility-prime-factorisation' THEN '<h2>Делимость</h2><p>Для целых \(a\) и \(b\), где \(a\ne0\), запись \(a\mid b\) означает, что \(b=ak\) для некоторого целого \(k\).</p><h2>Разложение на простые множители</h2><p>Каждое целое \(n>1\) единственным образом представляется как произведение степеней простых чисел.</p><h2>Подсчет делителей</h2><p>Если \(n=p_1^{a_1}\cdots p_m^{a_m}\), то \(\tau(n)=(a_1+1)\cdots(a_m+1)\).</p>' ELSE '<p>Материал будет добавлен позже.</p>' END,
CASE slug WHEN 'divisibility-prime-factorisation' THEN '<ol><li>Если \(6\mid n\), докажите \(3\mid n\).</li><li>Найдите разложение \(840\) на простые множители.</li><li>Посчитайте положительные делители \(840\).</li></ol>' ELSE '<p>Примеры будут добавлены позже.</p>' END,
CASE slug WHEN 'divisibility-prime-factorisation' THEN '<p>В этом MVP вкладка практики служит рабочим листом. Печатный экспорт можно добавить позже.</p>' ELSE '<p>Рабочий лист будет добавлен позже.</p>' END,
CASE slug WHEN 'divisibility-prime-factorisation' THEN '<p>Постоянно возвращайтесь к определению \(b=ak\). Сравнивайте прямое доказательство, разложение на множители и доказательство от противного.</p>' ELSE '<p>Методические заметки будут добавлены позже.</p>' END
FROM chapters;

DROP TEMPORARY TABLE IF EXISTS seed_problem_rows;
CREATE TEMPORARY TABLE seed_problem_rows (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  title_en VARCHAR(255) NOT NULL,
  title_ru VARCHAR(255) NOT NULL,
  topic VARCHAR(120) NOT NULL,
  subtopic VARCHAR(160) NOT NULL,
  difficulty ENUM('intro','core','challenge') NOT NULL DEFAULT 'core',
  tags JSON NULL,
  statement_html_en MEDIUMTEXT NOT NULL,
  statement_html_ru MEDIUMTEXT NOT NULL,
  hint_html_en MEDIUMTEXT NOT NULL,
  hint_html_ru MEDIUMTEXT NOT NULL,
  solution_html_en MEDIUMTEXT NOT NULL,
  solution_html_ru MEDIUMTEXT NOT NULL,
  teacher_note_html_en MEDIUMTEXT NOT NULL,
  teacher_note_html_ru MEDIUMTEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO seed_problem_rows
(id, title_en, title_ru, topic, subtopic, difficulty, tags, statement_html_en, statement_html_ru, hint_html_en, hint_html_ru, solution_html_en, solution_html_ru, teacher_note_html_en, teacher_note_html_ru, sort_order)
VALUES
('NT-01-001', 'Exact Division', 'Точная делимость', 'Number Theory', 'Divisibility', 'intro', '["definition", "integer"]', '<p>Decide whether \\(8\\mid 120\\), \\(8\\mid 122\\), and \\(9\\mid 117\\). Explain each answer.</p>', '<p>Определите, верно ли \\(8\\mid 120\\), \\(8\\mid 122\\) и \\(9\\mid 117\\). Объясните каждый ответ.</p>', '<p>Use the definition: \\(a\\mid b\\) means \\(b=ak\\) for some integer \\(k\\).</p>', '<p>Используйте определение: \\(a\\mid b\\) означает, что \\(b=ak\\) для некоторого целого \\(k\\).</p>', '<p>\\(120=8\\cdot15\\), so \\(8\\mid120\\). Since \\(122=8\\cdot15+2\\), \\(8\\nmid122\\). Also \\(117=9\\cdot13\\), so \\(9\\mid117\\).</p>', '<p>\\(120=8\\cdot15\\), значит \\(8\\mid120\\). Так как \\(122=8\\cdot15+2\\), то \\(8\\nmid122\\). Также \\(117=9\\cdot13\\), значит \\(9\\mid117\\).</p>', '<p>Use this to separate exact division from decimal division.</p>', '<p>Используйте эту задачу, чтобы отделить точную делимость от обычного деления с остатком или десятичной дробью.</p>', 1),
('NT-01-002', 'Divisibility from a Product', 'Делимость из произведения', 'Number Theory', 'Divisibility', 'intro', '["proof", "definition"]', '<p>Prove that if \\(12\\mid n\\), then \\(3\\mid n\\).</p>', '<p>Докажите, что если \\(12\\mid n\\), то \\(3\\mid n\\).</p>', '<p>Write \\(n=12k\\).</p>', '<p>Запишите \\(n=12k\\).</p>', '<p>If \\(12\\mid n\\), then \\(n=12k=3(4k)\\) for some integer \\(k\\). Therefore \\(3\\mid n\\).</p>', '<p>Если \\(12\\mid n\\), то \\(n=12k=3(4k)\\) для некоторого целого \\(k\\). Следовательно, \\(3\\mid n\\).</p>', '<p>Ask students which divisors of \\(12\\) must also divide \\(n\\).</p>', '<p>Спросите учеников, какие делители числа \\(12\\) обязательно делят \\(n\\).</p>', 2),
('NT-01-003', 'Linear Combination', 'Линейная комбинация', 'Number Theory', 'Divisibility Laws', 'intro', '["proof", "linear-combination"]', '<p>If \\(7\\mid a\\) and \\(7\\mid b\\), prove that \\(7\\mid 4a+5b\\).</p>', '<p>Если \\(7\\mid a\\) и \\(7\\mid b\\), докажите, что \\(7\\mid 4a+5b\\).</p>', '<p>Set \\(a=7x\\) and \\(b=7y\\).</p>', '<p>Положите \\(a=7x\\) и \\(b=7y\\).</p>', '<p>Let \\(a=7x\\) and \\(b=7y\\). Then \\(4a+5b=28x+35y=7(4x+5y)\\), so \\(7\\mid4a+5b\\).</p>', '<p>Пусть \\(a=7x\\) и \\(b=7y\\). Тогда \\(4a+5b=28x+35y=7(4x+5y)\\), значит \\(7\\mid4a+5b\\).</p>', '<p>This is a foundational proof pattern for later modular arithmetic.</p>', '<p>Это базовый шаблон доказательства, который позже естественно переходит в сравнения по модулю.</p>', 3),
('NT-01-004', 'Check a Divisibility Claim', 'Проверка утверждения о делимости', 'Number Theory', 'Divisibility Laws', 'core', '["proof", "difference"]', '<p>If \\(11\\mid 3x+2\\) and \\(11\\mid x-3\\), must \\(11\\mid 11x-7\\)? Give a proof or counterexample.</p>', '<p>Если \\(11\\mid 3x+2\\) и \\(11\\mid x-3\\), обязательно ли \\(11\\mid 11x-7\\)? Дайте доказательство или контрпример.</p>', '<p>Test a value of \\(x\\) satisfying both given conditions.</p>', '<p>Проверьте значение \\(x\\), которое удовлетворяет обоим данным условиям.</p>', '<p>Since \\(11\\mid3x+2\\) and \\(11\\mid x-3\\), any integer linear combination is divisible by \\(11\\). Now \\((3x+2)+8(x-3)=11x-22=11(x-2)\\), so it is divisible by \\(11\\). Also \\((11x-7)-(11x-22)=15\\), so the original conclusion is not always true. For example, \\(x=3\\) satisfies \\(x-3=0\\), but \\(3x+2=11\\), while \\(11x-7=26\\) is not divisible by \\(11\\). Thus the statement is false.</p>', '<p>Так как \\(11\\mid3x+2\\) и \\(11\\mid x-3\\), любая целая линейная комбинация этих выражений делится на \\(11\\). Например, \\((3x+2)+8(x-3)=11x-22=11(x-2)\\). Но \\((11x-7)-(11x-22)=15\\), поэтому исходное заключение не следует. При \\(x=3\\) имеем \\(x-3=0\\), \\(3x+2=11\\), но \\(11x-7=26\\), что не делится на \\(11\\). Значит утверждение ложно.</p>', '<p>This is intentionally a trap: students must learn to check whether a requested conclusion actually follows.</p>', '<p>Это намеренная ловушка: ученики должны привыкнуть проверять, действительно ли вывод следует из условий.</p>', 4),
('NT-01-005', 'Prime Factorisation of 420', 'Разложение числа 420', 'Number Theory', 'Prime Factorisation', 'intro', '["factorisation"]', '<p>Find the prime factorisation of \\(420\\).</p>', '<p>Найдите разложение числа \\(420\\) на простые множители.</p>', '<p>Break \\(420\\) as \\(42\\cdot10\\).</p>', '<p>Разбейте \\(420\\) как \\(42\\cdot10\\).</p>', '<p>\\(420=42\\cdot10=(2\\cdot3\\cdot7)(2\\cdot5)=2^2\\cdot3\\cdot5\\cdot7\\).</p>', '<p>\\(420=42\\cdot10=(2\\cdot3\\cdot7)(2\\cdot5)=2^2\\cdot3\\cdot5\\cdot7\\).</p>', '<p>Encourage multiple valid factor trees and one canonical final form.</p>', '<p>Разрешайте разные деревья множителей, но требуйте один канонический ответ в конце.</p>', 5),
('NT-01-006', 'Prime Factorisation of 756', 'Разложение числа 756', 'Number Theory', 'Prime Factorisation', 'core', '["factorisation"]', '<p>Find the prime factorisation of \\(756\\).</p>', '<p>Найдите разложение числа \\(756\\) на простые множители.</p>', '<p>Use \\(756=75\\cdot10+6\\), or divide by \\(2\\), then by \\(3\\).</p>', '<p>Сначала разделите на \\(2\\), затем проверяйте делимость на \\(3\\).</p>', '<p>\\(756=2\\cdot378=2^2\\cdot189=2^2\\cdot3^3\\cdot7\\).</p>', '<p>\\(756=2\\cdot378=2^2\\cdot189=2^2\\cdot3^3\\cdot7\\).</p>', '<p>Good for checking repeated prime factors.</p>', '<p>Хорошая задача для проверки повторяющихся простых множителей.</p>', 6),
('NT-01-007', 'Counting Divisors', 'Количество делителей', 'Number Theory', 'Number of Divisors', 'intro', '["divisors", "tau-function"]', '<p>How many positive divisors does \\(360\\) have?</p>', '<p>Сколько положительных делителей имеет число \\(360\\)?</p>', '<p>First write \\(360\\) as a product of prime powers.</p>', '<p>Сначала разложите \\(360\\) на степени простых чисел.</p>', '<p>\\(360=2^3\\cdot3^2\\cdot5\\). Therefore \\(\\tau(360)=(3+1)(2+1)(1+1)=24\\).</p>', '<p>\\(360=2^3\\cdot3^2\\cdot5\\). Поэтому \\(\\tau(360)=(3+1)(2+1)(1+1)=24\\).</p>', '<p>Ask students to explain why each exponent gets one extra choice.</p>', '<p>Попросите учеников объяснить, почему к каждому показателю степени прибавляется один вариант.</p>', 7),
('NT-01-008', 'A Square with Odd Divisors', 'Квадрат и нечетное число делителей', 'Number Theory', 'Number of Divisors', 'core', '["divisors", "squares"]', '<p>Explain why every perfect square has an odd number of positive divisors.</p>', '<p>Объясните, почему каждый полный квадрат имеет нечетное число положительных делителей.</p>', '<p>In a square, all prime exponents are even.</p>', '<p>В разложении полного квадрата все показатели степеней четные.</p>', '<p>If \\(n=m^2\\), then every exponent in the prime factorisation of \\(n\\) is even. Thus each factor \\(a_i+1\\) in \\(\\tau(n)=\\prod(a_i+1)\\) is odd, and a product of odd numbers is odd.</p>', '<p>Если \\(n=m^2\\), то все показатели степеней в разложении \\(n\\) на простые множители четные. Значит каждый множитель \\(a_i+1\\) в формуле \\(\\tau(n)=\\prod(a_i+1)\\) нечетен, а произведение нечетных чисел нечетно.</p>', '<p>This connects divisor counting with a classic pairing argument.</p>', '<p>Эта задача связывает формулу числа делителей с классическим разбиением делителей на пары.</p>', 8),
('NT-01-009', 'GCD from Factorisations', 'НОД по разложениям', 'Number Theory', 'GCD and LCM', 'intro', '["gcd", "factorisation"]', '<p>Find \\(\\gcd(144,180)\\).</p>', '<p>Найдите \\(\\gcd(144,180)\\).</p>', '<p>Use the minimum exponent of each prime.</p>', '<p>Берите минимальный показатель степени каждого простого числа.</p>', '<p>\\(144=2^4\\cdot3^2\\) and \\(180=2^2\\cdot3^2\\cdot5\\). Hence \\(\\gcd(144,180)=2^2\\cdot3^2=36\\).</p>', '<p>\\(144=2^4\\cdot3^2\\), а \\(180=2^2\\cdot3^2\\cdot5\\). Поэтому \\(\\gcd(144,180)=2^2\\cdot3^2=36\\).</p>', '<p>Make students verbalize minimum exponents.</p>', '<p>Добивайтесь, чтобы ученики проговаривали правило минимальных показателей.</p>', 9),
('NT-01-010', 'LCM from Factorisations', 'НОК по разложениям', 'Number Theory', 'GCD and LCM', 'intro', '["lcm", "factorisation"]', '<p>Find \\(\\operatorname{lcm}(144,180)\\).</p>', '<p>Найдите \\(\\operatorname{lcm}(144,180)\\).</p>', '<p>Use the maximum exponent of each prime.</p>', '<p>Берите максимальный показатель степени каждого простого числа.</p>', '<p>Using \\(144=2^4\\cdot3^2\\) and \\(180=2^2\\cdot3^2\\cdot5\\), we get \\(\\operatorname{lcm}(144,180)=2^4\\cdot3^2\\cdot5=720\\).</p>', '<p>Так как \\(144=2^4\\cdot3^2\\) и \\(180=2^2\\cdot3^2\\cdot5\\), получаем \\(\\operatorname{lcm}(144,180)=2^4\\cdot3^2\\cdot5=720\\).</p>', '<p>Pair with the previous problem to contrast GCD and LCM.</p>', '<p>Дайте эту задачу рядом с предыдущей, чтобы противопоставить НОД и НОК.</p>', 10),
('NT-01-011', 'Product Formula', 'Формула произведения', 'Number Theory', 'GCD and LCM', 'core', '["gcd", "lcm", "identity"]', '<p>Verify that \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\) for \\(a=48\\), \\(b=180\\).</p>', '<p>Проверьте равенство \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\) для \\(a=48\\), \\(b=180\\).</p>', '<p>Compute both \\(\\gcd\\) and \\(\\operatorname{lcm}\\).</p>', '<p>Найдите и \\(\\gcd\\), и \\(\\operatorname{lcm}\\).</p>', '<p>\\(48=2^4\\cdot3\\), \\(180=2^2\\cdot3^2\\cdot5\\). Thus \\(\\gcd=2^2\\cdot3=12\\) and \\(\\operatorname{lcm}=2^4\\cdot3^2\\cdot5=720\\). Then \\(12\\cdot720=8640=48\\cdot180\\).</p>', '<p>\\(48=2^4\\cdot3\\), \\(180=2^2\\cdot3^2\\cdot5\\). Тогда \\(\\gcd=2^2\\cdot3=12\\), а \\(\\operatorname{lcm}=2^4\\cdot3^2\\cdot5=720\\). Получаем \\(12\\cdot720=8640=48\\cdot180\\).</p>', '<p>This prepares students for proving the identity using prime exponents.</p>', '<p>Эта задача готовит учеников к доказательству формулы через показатели простых степеней.</p>', 11),
('NT-01-012', 'Consecutive Product', 'Произведение соседних чисел', 'Number Theory', 'Divisibility Proofs', 'intro', '["consecutive-integers", "proof"]', '<p>Prove that \\(2\\mid n(n+1)\\) for every integer \\(n\\).</p>', '<p>Докажите, что \\(2\\mid n(n+1)\\) для любого целого \\(n\\).</p>', '<p>Among two consecutive integers, one is even.</p>', '<p>Из двух соседних целых чисел одно четное.</p>', '<p>The integers \\(n\\) and \\(n+1\\) are consecutive, so one of them is even. Therefore their product is divisible by \\(2\\).</p>', '<p>Числа \\(n\\) и \\(n+1\\) соседние, значит одно из них четное. Поэтому их произведение делится на \\(2\\).</p>', '<p>Invite both parity-case and consecutive-number solutions.</p>', '<p>Разрешайте оба подхода: через четность по случаям и через свойство соседних чисел.</p>', 12),
('NT-01-013', 'Three Consecutive Integers', 'Три последовательных числа', 'Number Theory', 'Divisibility Proofs', 'intro', '["consecutive-integers", "proof"]', '<p>Prove that \\(3\\mid n(n+1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Докажите, что \\(3\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Among any three consecutive integers, one is a multiple of \\(3\\).</p>', '<p>Среди любых трех последовательных целых чисел есть кратное \\(3\\).</p>', '<p>The integers \\(n,n+1,n+2\\) cover all possible remainders modulo \\(3\\). One is divisible by \\(3\\), so the product is divisible by \\(3\\).</p>', '<p>Числа \\(n,n+1,n+2\\) дают все возможные остатки при делении на \\(3\\). Одно из них делится на \\(3\\), значит произведение делится на \\(3\\).</p>', '<p>This is an early doorway into remainders.</p>', '<p>Это ранний вход в язык остатков.</p>', 13),
('NT-01-014', 'Six Divides a Product', 'Делимость произведения на шесть', 'Number Theory', 'Divisibility Proofs', 'core', '["consecutive-integers", "proof"]', '<p>Prove that \\(6\\mid n(n+1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Докажите, что \\(6\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Show divisibility by \\(2\\) and by \\(3\\).</p>', '<p>Докажите делимость на \\(2\\) и на \\(3\\).</p>', '<p>Among three consecutive integers, one is divisible by \\(3\\), and at least one is even. Since \\(2\\) and \\(3\\) are coprime, the product is divisible by \\(6\\).</p>', '<p>Среди трех последовательных чисел одно делится на \\(3\\), и хотя бы одно четное. Так как \\(2\\) и \\(3\\) взаимно просты, произведение делится на \\(6\\).</p>', '<p>Emphasize why divisibility by \\(2\\) and \\(3\\) together gives divisibility by \\(6\\).</p>', '<p>Подчеркните, почему делимость на \\(2\\) и на \\(3\\) вместе дает делимость на \\(6\\).</p>', 14),
('NT-01-015', 'Difference of Squares', 'Разность квадратов', 'Number Theory', 'Factorisation Methods', 'core', '["factorisation", "proof"]', '<p>Prove that if \\(a-b\\) is divisible by \\(5\\), then \\(a^2-b^2\\) is divisible by \\(5\\).</p>', '<p>Докажите: если \\(a-b\\) делится на \\(5\\), то \\(a^2-b^2\\) делится на \\(5\\).</p>', '<p>Factor \\(a^2-b^2\\).</p>', '<p>Разложите \\(a^2-b^2\\) на множители.</p>', '<p>Since \\(a^2-b^2=(a-b)(a+b)\\), and \\(5\\mid a-b\\), the product \\((a-b)(a+b)\\) is divisible by \\(5\\).</p>', '<p>Так как \\(a^2-b^2=(a-b)(a+b)\\) и \\(5\\mid a-b\\), произведение \\((a-b)(a+b)\\) делится на \\(5\\).</p>', '<p>A useful example of turning algebraic factorisation into number theory.</p>', '<p>Полезный пример того, как алгебраическое разложение превращается в доказательство по теории чисел.</p>', 15),
('NT-01-016', 'Prime or Composite', 'Простое или составное', 'Number Theory', 'Primes', 'intro', '["prime", "classification"]', '<p>Classify each number as prime or composite: \\(37, 49, 57, 83\\).</p>', '<p>Определите, какие числа простые, а какие составные: \\(37, 49, 57, 83\\).</p>', '<p>To test \\(n\\), check prime divisors up to \\(\\sqrt n\\).</p>', '<p>Для проверки числа \\(n\\) достаточно проверять простые делители не больше \\(\\sqrt n\\).</p>', '<p>\\(37\\) is prime. \\(49=7^2\\) is composite. \\(57=3\\cdot19\\) is composite. \\(83\\) is prime because it is not divisible by \\(2,3,5,7\\), and \\(\\sqrt{83}<10\\).</p>', '<p>\\(37\\) простое. \\(49=7^2\\) составное. \\(57=3\\cdot19\\) составное. \\(83\\) простое, так как оно не делится на \\(2,3,5,7\\), а \\(\\sqrt{83}<10\\).</p>', '<p>Teach the square-root stopping rule explicitly.</p>', '<p>Явно объясните правило остановки на квадратном корне.</p>', 16),
('NT-01-017', 'Smallest Number with Eight Divisors', 'Наименьшее число с восемью делителями', 'Number Theory', 'Number of Divisors', 'core', '["optimization", "divisors"]', '<p>Find the smallest positive integer with exactly \\(8\\) positive divisors.</p>', '<p>Найдите наименьшее положительное целое число, имеющее ровно \\(8\\) положительных делителей.</p>', '<p>Factor \\(8\\) as \\(8\\), \\(4\\cdot2\\), or \\(2\\cdot2\\cdot2\\).</p>', '<p>Разложите \\(8\\) как \\(8\\), \\(4\\cdot2\\) или \\(2\\cdot2\\cdot2\\).</p>', '<p>The possible exponent patterns are \\(7\\), \\(3,1\\), and \\(1,1,1\\). The smallest candidates are \\(2^7=128\\), \\(2^3\\cdot3=24\\), and \\(2\\cdot3\\cdot5=30\\). The smallest is \\(24\\).</p>', '<p>Возможные наборы показателей: \\(7\\), \\(3,1\\), \\(1,1,1\\). Минимальные кандидаты: \\(2^7=128\\), \\(2^3\\cdot3=24\\), \\(2\\cdot3\\cdot5=30\\). Наименьшее число равно \\(24\\).</p>', '<p>Students often choose many small primes too quickly; compare exponent patterns.</p>', '<p>Ученики часто слишком быстро выбирают много маленьких простых чисел; сравните разные наборы показателей.</p>', 17),
('NT-01-018', 'Exactly Nine Divisors', 'Ровно девять делителей', 'Number Theory', 'Number of Divisors', 'core', '["optimization", "divisors"]', '<p>Find the smallest positive integer with exactly \\(9\\) positive divisors.</p>', '<p>Найдите наименьшее положительное целое число, имеющее ровно \\(9\\) положительных делителей.</p>', '<p>The exponent patterns come from \\(9\\) and \\(3\\cdot3\\).</p>', '<p>Наборы показателей получаются из \\(9\\) и \\(3\\cdot3\\).</p>', '<p>The patterns are \\(8\\) and \\(2,2\\). The smallest candidates are \\(2^8=256\\) and \\(2^2\\cdot3^2=36\\). Therefore the answer is \\(36\\).</p>', '<p>Возможные наборы: \\(8\\) и \\(2,2\\). Минимальные кандидаты: \\(2^8=256\\) и \\(2^2\\cdot3^2=36\\). Следовательно, ответ \\(36\\).</p>', '<p>This reinforces assigning larger exponents to smaller primes.</p>', '<p>Эта задача закрепляет правило: большие показатели выгоднее отдавать меньшим простым числам.</p>', 18),
('NT-01-019', 'A Divisor Equation', 'Уравнение с делимостью', 'Number Theory', 'Divisibility', 'core', '["equation", "divisors"]', '<p>Find all positive integers \\(n\\) such that \\(n\\mid 30\\).</p>', '<p>Найдите все положительные целые \\(n\\), такие что \\(n\\mid 30\\).</p>', '<p>List the positive divisors of \\(30\\).</p>', '<p>Перечислите положительные делители числа \\(30\\).</p>', '<p>Since \\(30=2\\cdot3\\cdot5\\), its positive divisors are \\(1,2,3,5,6,10,15,30\\).</p>', '<p>Так как \\(30=2\\cdot3\\cdot5\\), его положительные делители: \\(1,2,3,5,6,10,15,30\\).</p>', '<p>A simple but important bridge from divisibility notation to solution sets.</p>', '<p>Простая, но важная связь между записью делимости и множеством решений.</p>', 19),
('NT-01-020', 'Dividing a Shifted Expression', 'Остаток линейного выражения', 'Number Theory', 'Remainders', 'core', '["remainders", "expression"]', '<p>If \\(n\\) leaves remainder \\(2\\) when divided by \\(5\\), what remainder does \\(3n+1\\) leave when divided by \\(5\\)?</p>', '<p>Если \\(n\\) дает остаток \\(2\\) при делении на \\(5\\), какой остаток дает \\(3n+1\\) при делении на \\(5\\)?</p>', '<p>Write \\(n=5k+2\\).</p>', '<p>Запишите \\(n=5k+2\\).</p>', '<p>Let \\(n=5k+2\\). Then \\(3n+1=15k+7=5(3k+1)+2\\), so the remainder is \\(2\\).</p>', '<p>Пусть \\(n=5k+2\\). Тогда \\(3n+1=15k+7=5(3k+1)+2\\), значит остаток равен \\(2\\).</p>', '<p>Use this before formal modular notation.</p>', '<p>Используйте эту задачу до введения формальной записи сравнений.</p>', 20),
('NT-01-021', 'Remainder of a Square', 'Остаток квадрата', 'Number Theory', 'Remainders', 'core', '["remainders", "squares"]', '<p>Show that the square of an integer leaves remainder \\(0\\) or \\(1\\) when divided by \\(4\\).</p>', '<p>Докажите, что квадрат целого числа дает остаток \\(0\\) или \\(1\\) при делении на \\(4\\).</p>', '<p>Consider even and odd integers.</p>', '<p>Рассмотрите четные и нечетные числа.</p>', '<p>If \\(n=2k\\), then \\(n^2=4k^2\\), remainder \\(0\\). If \\(n=2k+1\\), then \\(n^2=4k^2+4k+1\\), remainder \\(1\\).</p>', '<p>Если \\(n=2k\\), то \\(n^2=4k^2\\), остаток \\(0\\). Если \\(n=2k+1\\), то \\(n^2=4k^2+4k+1\\), остаток \\(1\\).</p>', '<p>This result becomes very useful in impossibility proofs.</p>', '<p>Этот факт часто используется в доказательствах невозможности.</p>', 21),
('NT-01-022', 'No Square Remainder Two', 'Квадрат не дает остаток два', 'Number Theory', 'Remainders', 'core', '["remainders", "squares", "impossibility"]', '<p>Prove that no integer square can leave remainder \\(2\\) when divided by \\(4\\).</p>', '<p>Докажите, что квадрат целого числа не может давать остаток \\(2\\) при делении на \\(4\\).</p>', '<p>Use the result that square remainders modulo \\(4\\) are only \\(0\\) and \\(1\\).</p>', '<p>Используйте факт, что остатки квадратов по модулю \\(4\\) бывают только \\(0\\) и \\(1\\).</p>', '<p>From the even/odd cases, every integer square is either \\(4k\\) or \\(4k+1\\). Therefore remainder \\(2\\) is impossible.</p>', '<p>Из рассмотрения четного и нечетного случая следует, что любой квадрат имеет вид \\(4k\\) или \\(4k+1\\). Поэтому остаток \\(2\\) невозможен.</p>', '<p>Ask students to connect this to equations such as \\(x^2=4m+2\\).</p>', '<p>Попросите учеников связать это с уравнениями вида \\(x^2=4m+2\\).</p>', 22),
('NT-01-023', 'A Prime Divisor', 'Простой делитель', 'Number Theory', 'Primes', 'core', '["prime", "divisibility"]', '<p>Let \\(p\\) be prime. If \\(p\\mid 35\\), find all possible values of \\(p\\).</p>', '<p>Пусть \\(p\\) простое число. Если \\(p\\mid 35\\), найдите все возможные значения \\(p\\).</p>', '<p>Factor \\(35\\).</p>', '<p>Разложите \\(35\\) на множители.</p>', '<p>Since \\(35=5\\cdot7\\), the prime divisors of \\(35\\) are \\(5\\) and \\(7\\). Thus \\(p\\in\\{5,7\\}\\).</p>', '<p>Так как \\(35=5\\cdot7\\), простые делители числа \\(35\\) равны \\(5\\) и \\(7\\). Поэтому \\(p\\in\\{5,7\\}\\).</p>', '<p>Simple example of using prime factorisation to restrict possibilities.</p>', '<p>Простой пример того, как разложение на простые множители ограничивает варианты.</p>', 23),
('NT-01-024', 'Prime Triple', 'Тройка простых чисел', 'Number Theory', 'Primes', 'challenge', '["prime", "remainders", "proof"]', '<p>Find all primes \\(p\\) such that \\(p\\), \\(p+2\\), and \\(p+4\\) are all prime.</p>', '<p>Найдите все простые \\(p\\), для которых \\(p\\), \\(p+2\\) и \\(p+4\\) также простые.</p>', '<p>Look at remainders modulo \\(3\\).</p>', '<p>Посмотрите на остатки при делении на \\(3\\).</p>', '<p>If \\(p>3\\), then \\(p,p+2,p+4\\) are three numbers covering all remainders modulo \\(3\\), so one is divisible by \\(3\\). Since it is greater than \\(3\\), it cannot be prime. Checking \\(p=3\\), we get \\(3,5,7\\), all prime. Therefore \\(p=3\\).</p>', '<p>Если \\(p>3\\), то числа \\(p,p+2,p+4\\) покрывают все остатки по модулю \\(3\\), значит одно из них делится на \\(3\\). Оно больше \\(3\\), поэтому не может быть простым. Проверяем \\(p=3\\): получаем \\(3,5,7\\), все простые. Следовательно, \\(p=3\\).</p>', '<p>A classic olympiad-style use of remainders with primes.</p>', '<p>Классический олимпиадный пример использования остатков для простых чисел.</p>', 24),
('NT-01-025', 'Factorial Exponent', 'Показатель простого в факториале', 'Number Theory', 'Prime Factorisation', 'challenge', '["factorial", "exponent"]', '<p>Find the exponent of \\(5\\) in the prime factorisation of \\(50!\\).</p>', '<p>Найдите показатель степени \\(5\\) в разложении \\(50!\\) на простые множители.</p>', '<p>Count multiples of \\(5\\), then extra factors from multiples of \\(25\\).</p>', '<p>Посчитайте кратные \\(5\\), затем дополнительные множители из кратных \\(25\\).</p>', '<p>The exponent is \\(\\left\\lfloor50/5\\right\\rfloor+\\left\\lfloor50/25\\right\\rfloor=10+2=12\\).</p>', '<p>Показатель равен \\(\\left\\lfloor50/5\\right\\rfloor+\\left\\lfloor50/25\\right\\rfloor=10+2=12\\).</p>', '<p>Introduce Legendre''s formula informally through counting.</p>', '<p>Неформально введите формулу Лежандра через подсчет кратных.</p>', 25),
('NT-01-026', 'Trailing Zeros', 'Нули в конце факториала', 'Number Theory', 'Prime Factorisation', 'challenge', '["factorial", "trailing-zeros"]', '<p>How many zeros does \\(60!\\) end with?</p>', '<p>Сколькими нулями оканчивается число \\(60!\\)?</p>', '<p>Each trailing zero needs one factor \\(10=2\\cdot5\\). Which prime is rarer?</p>', '<p>Каждый ноль требует множитель \\(10=2\\cdot5\\). Какой простой множитель встречается реже?</p>', '<p>The number of trailing zeros is the exponent of \\(5\\) in \\(60!\\), since factors of \\(2\\) are more plentiful. It is \\(\\lfloor60/5\\rfloor+\\lfloor60/25\\rfloor=12+2=14\\).</p>', '<p>Число нулей в конце равно показателю степени \\(5\\) в \\(60!\\), потому что двоек больше. Получаем \\(\\lfloor60/5\\rfloor+\\lfloor60/25\\rfloor=12+2=14\\).</p>', '<p>Make students explain why counting fives is enough.</p>', '<p>Попросите учеников объяснить, почему достаточно считать пятерки.</p>', 26),
('NT-01-027', 'A Divisibility Counterexample', 'Контрпример к делимости', 'Number Theory', 'Divisibility', 'core', '["counterexample"]', '<p>Is this statement true: if \\(a\\mid bc\\), then \\(a\\mid b\\) or \\(a\\mid c\\)? Give proof or counterexample.</p>', '<p>Верно ли утверждение: если \\(a\\mid bc\\), то \\(a\\mid b\\) или \\(a\\mid c\\)? Дайте доказательство или контрпример.</p>', '<p>Try a composite value of \\(a\\).</p>', '<p>Попробуйте составное значение \\(a\\).</p>', '<p>The statement is false. Take \\(a=6\\), \\(b=2\\), \\(c=3\\). Then \\(6\\mid bc\\) because \\(bc=6\\), but \\(6\\nmid2\\) and \\(6\\nmid3\\).</p>', '<p>Утверждение ложно. Возьмем \\(a=6\\), \\(b=2\\), \\(c=3\\). Тогда \\(6\\mid bc\\), потому что \\(bc=6\\), но \\(6\\nmid2\\) и \\(6\\nmid3\\).</p>', '<p>This prepares students for the special role of primes in Euclid''s lemma.</p>', '<p>Эта задача готовит учеников к особой роли простых чисел в лемме Евклида.</p>', 27),
('NT-01-028', 'Euclid''s Lemma Example', 'Пример к лемме Евклида', 'Number Theory', 'Primes', 'core', '["prime", "euclid-lemma"]', '<p>Let \\(p\\) be prime and \\(p\\mid ab\\). Explain why the conclusion \\(p\\mid a\\) or \\(p\\mid b\\) is reasonable using prime factorisation.</p>', '<p>Пусть \\(p\\) простое и \\(p\\mid ab\\). Объясните через разложение на простые множители, почему естественен вывод: \\(p\\mid a\\) или \\(p\\mid b\\).</p>', '<p>A prime factor appearing in \\(ab\\) must come from one of the factors.</p>', '<p>Простой множитель, который появился в \\(ab\\), должен прийти из одного из множителей.</p>', '<p>In the prime factorisation of \\(ab\\), all prime factors come from the factorisations of \\(a\\) and \\(b\\). If the prime \\(p\\) appears in \\(ab\\), it must appear in \\(a\\) or in \\(b\\). Thus \\(p\\mid a\\) or \\(p\\mid b\\).</p>', '<p>В разложении \\(ab\\) на простые множители все простые множители приходят из разложений \\(a\\) и \\(b\\). Если простой \\(p\\) встречается в \\(ab\\), он должен встречаться в \\(a\\) или в \\(b\\). Значит \\(p\\mid a\\) или \\(p\\mid b\\).</p>', '<p>Keep this intuitive here; a formal proof can come later.</p>', '<p>Здесь достаточно интуитивного объяснения; формальное доказательство можно дать позже.</p>', 28),
('NT-01-029', 'Coprime Product', 'Произведение взаимно простых делителей', 'Number Theory', 'GCD and LCM', 'challenge', '["coprime", "divisibility"]', '<p>If \\(\\gcd(a,b)=1\\), \\(a\\mid n\\), and \\(b\\mid n\\), prove that \\(ab\\mid n\\).</p>', '<p>Если \\(\\gcd(a,b)=1\\), \\(a\\mid n\\) и \\(b\\mid n\\), докажите, что \\(ab\\mid n\\).</p>', '<p>Use prime factorisation: coprime numbers share no prime factors.</p>', '<p>Используйте разложение на простые множители: взаимно простые числа не имеют общих простых делителей.</p>', '<p>Since \\(a\\) and \\(b\\) share no prime factors, the prime powers required by \\(a\\) and by \\(b\\) are independent. If \\(n\\) is divisible by both, then \\(n\\) contains all prime powers from \\(a\\) and all from \\(b\\), so \\(ab\\mid n\\).</p>', '<p>Так как \\(a\\) и \\(b\\) не имеют общих простых множителей, простые степени, нужные для \\(a\\), и простые степени, нужные для \\(b\\), независимы. Если \\(n\\) делится на оба числа, то \\(n\\) содержит все простые степени из \\(a\\) и все из \\(b\\), значит \\(ab\\mid n\\).</p>', '<p>This is a key theorem for combining divisibility conditions.</p>', '<p>Это ключевая теорема для объединения условий делимости.</p>', 29),
('NT-01-030', 'Find the Missing Exponent', 'Найти неизвестный показатель', 'Number Theory', 'Number of Divisors', 'core', '["divisors", "exponents"]', '<p>For \\(n=2^3\\cdot3^a\\), find all positive integers \\(a\\) such that \\(n\\) has \\(20\\) positive divisors.</p>', '<p>Для \\(n=2^3\\cdot3^a\\) найдите все положительные целые \\(a\\), при которых \\(n\\) имеет \\(20\\) положительных делителей.</p>', '<p>Use \\((3+1)(a+1)=20\\).</p>', '<p>Используйте \\((3+1)(a+1)=20\\).</p>', '<p>\\(\\tau(n)=(3+1)(a+1)=4(a+1)\\). Set \\(4(a+1)=20\\), so \\(a+1=5\\) and \\(a=4\\).</p>', '<p>\\(\\tau(n)=(3+1)(a+1)=4(a+1)\\). Решаем \\(4(a+1)=20\\), откуда \\(a+1=5\\) и \\(a=4\\).</p>', '<p>A clean algebraic use of the divisor formula.</p>', '<p>Чистое алгебраическое применение формулы числа делителей.</p>', 30),
('NT-01-031', 'Shared Divisors', 'Общие делители', 'Number Theory', 'GCD and LCM', 'core', '["gcd", "divisors"]', '<p>How many positive common divisors do \\(84\\) and \\(126\\) have?</p>', '<p>Сколько положительных общих делителей имеют числа \\(84\\) и \\(126\\)?</p>', '<p>Common divisors are exactly the divisors of the \\(\\gcd\\).</p>', '<p>Общие делители двух чисел — это ровно делители их \\(\\gcd\\).</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), so \\(\\gcd(84,126)=2\\cdot3\\cdot7=42\\). Since \\(42=2\\cdot3\\cdot7\\), it has \\((1+1)^3=8\\) positive divisors.</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), значит \\(\\gcd(84,126)=2\\cdot3\\cdot7=42\\). Так как \\(42=2\\cdot3\\cdot7\\), у него \\((1+1)^3=8\\) положительных делителей.</p>', '<p>This links GCD to a counting question.</p>', '<p>Эта задача связывает НОД с задачей на подсчет.</p>', 31),
('NT-01-032', 'All Divisors from Exponents', 'Все делители через показатели', 'Number Theory', 'Prime Factorisation', 'intro', '["divisors", "listing"]', '<p>List all positive divisors of \\(2^2\\cdot3\\).</p>', '<p>Перечислите все положительные делители числа \\(2^2\\cdot3\\).</p>', '<p>Choose the exponent of \\(2\\) from \\(0,1,2\\) and of \\(3\\) from \\(0,1\\).</p>', '<p>Выберите показатель у \\(2\\) из \\(0,1,2\\), а показатель у \\(3\\) из \\(0,1\\).</p>', '<p>The number is \\(12\\). Its positive divisors are \\(1,2,3,4,6,12\\).</p>', '<p>Число равно \\(12\\). Его положительные делители: \\(1,2,3,4,6,12\\).</p>', '<p>Have students build a small exponent table.</p>', '<p>Пусть ученики построят маленькую таблицу показателей.</p>', 32),
('NT-01-033', 'Divisibility by Nine', 'Признак делимости на девять', 'Number Theory', 'Divisibility Tests', 'intro', '["divisibility-test", "digits"]', '<p>Use the digit-sum test to decide whether \\(738\\) is divisible by \\(9\\).</p>', '<p>Используйте сумму цифр, чтобы определить, делится ли \\(738\\) на \\(9\\).</p>', '<p>Add the digits.</p>', '<p>Сложите цифры числа.</p>', '<p>The digit sum is \\(7+3+8=18\\), and \\(9\\mid18\\). Therefore \\(9\\mid738\\).</p>', '<p>Сумма цифр равна \\(7+3+8=18\\), а \\(9\\mid18\\). Следовательно, \\(9\\mid738\\).</p>', '<p>Later this can be proved with remainders modulo \\(9\\).</p>', '<p>Позже этот признак можно доказать через остатки по модулю \\(9\\).</p>', 33),
('NT-01-034', 'Make It Divisible', 'Сделать число делящимся', 'Number Theory', 'Divisibility Tests', 'core', '["digits", "divisibility-test"]', '<p>Find all digits \\(x\\) such that the number \\(45x2\\) is divisible by \\(3\\).</p>', '<p>Найдите все цифры \\(x\\), при которых число \\(45x2\\) делится на \\(3\\).</p>', '<p>A number is divisible by \\(3\\) when its digit sum is divisible by \\(3\\).</p>', '<p>Число делится на \\(3\\), если сумма его цифр делится на \\(3\\).</p>', '<p>The digit sum is \\(4+5+x+2=11+x\\). We need \\(11+x\\equiv0\\pmod3\\). Since \\(11\\equiv2\\pmod3\\), \\(x\\equiv1\\pmod3\\). The possible digits are \\(1,4,7\\).</p>', '<p>Сумма цифр равна \\(4+5+x+2=11+x\\). Нужно \\(11+x\\equiv0\\pmod3\\). Так как \\(11\\equiv2\\pmod3\\), получаем \\(x\\equiv1\\pmod3\\). Возможные цифры: \\(1,4,7\\).</p>', '<p>Good entry point to using congruence language informally.</p>', '<p>Хороший вход в неформальное использование сравнений.</p>', 34),
('NT-01-035', 'Even Divisors', 'Четные делители', 'Number Theory', 'Number of Divisors', 'challenge', '["divisors", "counting"]', '<p>How many positive even divisors does \\(720\\) have?</p>', '<p>Сколько положительных четных делителей имеет число \\(720\\)?</p>', '<p>First factor \\(720\\). For an even divisor, the exponent of \\(2\\) must be at least \\(1\\).</p>', '<p>Сначала разложите \\(720\\). Для четного делителя показатель степени \\(2\\) должен быть хотя бы \\(1\\).</p>', '<p>\\(720=2^4\\cdot3^2\\cdot5\\). An even divisor has exponent of \\(2\\) equal to \\(1,2,3,\\) or \\(4\\): \\(4\\) choices. The exponent of \\(3\\) has \\(3\\) choices and of \\(5\\) has \\(2\\) choices. Total: \\(4\\cdot3\\cdot2=24\\).</p>', '<p>\\(720=2^4\\cdot3^2\\cdot5\\). У четного делителя показатель у \\(2\\) может быть \\(1,2,3\\) или \\(4\\): \\(4\\) варианта. У показателя \\(3\\) есть \\(3\\) варианта, у показателя \\(5\\) — \\(2\\) варианта. Всего \\(4\\cdot3\\cdot2=24\\).</p>', '<p>This deepens divisor counting beyond the standard formula.</p>', '<p>Эта задача углубляет подсчет делителей за пределы стандартной формулы.</p>', 35),
('NT-01-036', 'Odd Divisors', 'Нечетные делители', 'Number Theory', 'Number of Divisors', 'core', '["divisors", "counting"]', '<p>How many positive odd divisors does \\(720\\) have?</p>', '<p>Сколько положительных нечетных делителей имеет число \\(720\\)?</p>', '<p>Odd divisors use no factor \\(2\\).</p>', '<p>Нечетные делители не используют множитель \\(2\\).</p>', '<p>Since \\(720=2^4\\cdot3^2\\cdot5\\), an odd divisor must use \\(2^0\\). Then the exponent of \\(3\\) has \\(3\\) choices and the exponent of \\(5\\) has \\(2\\) choices. There are \\(3\\cdot2=6\\) positive odd divisors.</p>', '<p>Так как \\(720=2^4\\cdot3^2\\cdot5\\), нечетный делитель должен содержать \\(2^0\\). Тогда у показателя \\(3\\) есть \\(3\\) варианта, а у показателя \\(5\\) — \\(2\\) варианта. Всего \\(3\\cdot2=6\\) положительных нечетных делителей.</p>', '<p>Pair this with even-divisor counting.</p>', '<p>Дайте эту задачу вместе с подсчетом четных делителей.</p>', 36),
('NT-01-037', 'A Divisibility Chain', 'Цепочка делимости', 'Number Theory', 'Divisibility', 'core', '["proof", "transitivity"]', '<p>Prove that if \\(a\\mid b\\) and \\(b\\mid c\\), then \\(a\\mid c\\).</p>', '<p>Докажите, что если \\(a\\mid b\\) и \\(b\\mid c\\), то \\(a\\mid c\\).</p>', '<p>Write \\(b=ak\\) and \\(c=bm\\).</p>', '<p>Запишите \\(b=ak\\) и \\(c=bm\\).</p>', '<p>If \\(a\\mid b\\), then \\(b=ak\\). If \\(b\\mid c\\), then \\(c=bm\\). Therefore \\(c=akm=a(km)\\), so \\(a\\mid c\\).</p>', '<p>Если \\(a\\mid b\\), то \\(b=ak\\). Если \\(b\\mid c\\), то \\(c=bm\\). Следовательно, \\(c=akm=a(km)\\), значит \\(a\\mid c\\).</p>', '<p>Students should see divisibility as a relation with structure.</p>', '<p>Ученики должны видеть делимость как отношение со своей структурой.</p>', 37),
('NT-01-038', 'Mutual Divisibility', 'Взаимная делимость', 'Number Theory', 'Divisibility', 'challenge', '["proof", "absolute-value"]', '<p>Let \\(a\\) and \\(b\\) be positive integers. Prove that if \\(a\\mid b\\) and \\(b\\mid a\\), then \\(a=b\\).</p>', '<p>Пусть \\(a\\) и \\(b\\) — положительные целые числа. Докажите: если \\(a\\mid b\\) и \\(b\\mid a\\), то \\(a=b\\).</p>', '<p>Use \\(b=ak\\) and compare sizes.</p>', '<p>Используйте \\(b=ak\\) и сравните размеры чисел.</p>', '<p>Since \\(a\\mid b\\), write \\(b=ak\\) for a positive integer \\(k\\). Since \\(b\\mid a\\), write \\(a=bm\\) for a positive integer \\(m\\). Then \\(a=akm\\). Because \\(a>0\\), we get \\(km=1\\), so \\(k=m=1\\), and \\(a=b\\).</p>', '<p>Так как \\(a\\mid b\\), запишем \\(b=ak\\), где \\(k\\) — положительное целое число. Так как \\(b\\mid a\\), запишем \\(a=bm\\), где \\(m\\) — положительное целое число. Тогда \\(a=akm\\). Поскольку \\(a>0\\), получаем \\(km=1\\), значит \\(k=m=1\\), и \\(a=b\\).</p>', '<p>For integer versions, discuss signs separately.</p>', '<p>Для версии с произвольными целыми числами отдельно обсудите знаки.</p>', 38),
('NT-01-039', 'Find the GCD and LCM', 'Найти НОД и НОК', 'Number Theory', 'GCD and LCM', 'core', '["gcd", "lcm"]', '<p>Find \\(\\gcd(210,330)\\) and \\(\\operatorname{lcm}(210,330)\\).</p>', '<p>Найдите \\(\\gcd(210,330)\\) и \\(\\operatorname{lcm}(210,330)\\).</p>', '<p>Factor both numbers first.</p>', '<p>Сначала разложите оба числа на простые множители.</p>', '<p>\\(210=2\\cdot3\\cdot5\\cdot7\\), and \\(330=2\\cdot3\\cdot5\\cdot11\\). Therefore \\(\\gcd=2\\cdot3\\cdot5=30\\), and \\(\\operatorname{lcm}=2\\cdot3\\cdot5\\cdot7\\cdot11=2310\\).</p>', '<p>\\(210=2\\cdot3\\cdot5\\cdot7\\), а \\(330=2\\cdot3\\cdot5\\cdot11\\). Поэтому \\(\\gcd=2\\cdot3\\cdot5=30\\), а \\(\\operatorname{lcm}=2\\cdot3\\cdot5\\cdot7\\cdot11=2310\\).</p>', '<p>This example makes shared and unshared prime factors visible.</p>', '<p>Этот пример хорошо показывает общие и необщие простые множители.</p>', 39),
('NT-01-040', 'Olympiad Warm-Up', 'Олимпиадная разминка', 'Number Theory', 'Divisibility Proofs', 'challenge', '["proof", "factorisation", "consecutive-integers"]', '<p>Prove that \\(24\\mid n(n^2-1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Докажите, что \\(24\\mid n(n^2-1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Factor the expression into four consecutive integers.</p>', '<p>Разложите выражение в произведение четырех последовательных целых чисел.</p>', '<p>We have \\(n(n^2-1)(n+2)=n(n-1)(n+1)(n+2)\\), the product of four consecutive integers. Among four consecutive integers there is a multiple of \\(4\\), another even number, and at least one multiple of \\(3\\). Thus the product is divisible by \\(8\\cdot3=24\\).</p>', '<p>Имеем \\(n(n^2-1)(n+2)=n(n-1)(n+1)(n+2)\\), то есть произведение четырех последовательных целых чисел. Среди них есть число, кратное \\(4\\), еще одно четное число и хотя бы одно число, кратное \\(3\\). Поэтому произведение делится на \\(8\\cdot3=24\\).</p>', '<p>Check the divisibility by \\(8\\) carefully: four consecutive integers contain a multiple of \\(4\\) and another even number.</p>', '<p>Аккуратно проверьте делимость на \\(8\\): среди четырех последовательных чисел есть кратное \\(4\\) и еще одно четное число.</p>', 40);

INSERT IGNORE INTO problems (chapter_id, problem_code, book_number, difficulty, sort_order, is_published)
SELECT ch.id, s.id, s.sort_order, s.difficulty, s.sort_order, 1
FROM seed_problem_rows s
JOIN chapters ch ON ch.slug = 'divisibility-prime-factorisation';

REPLACE INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html)
SELECT p.id, 'en', s.title_en, s.statement_html_en, s.hint_html_en, s.solution_html_en, s.teacher_note_html_en
FROM seed_problem_rows s
JOIN problems p ON p.problem_code = s.id;

REPLACE INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html)
SELECT p.id, 'ru', s.title_ru, s.statement_html_ru, s.hint_html_ru, s.solution_html_ru, s.teacher_note_html_ru
FROM seed_problem_rows s
JOIN problems p ON p.problem_code = s.id;

INSERT IGNORE INTO tags (slug) VALUES
('absolute-value'),
('classification'),
('consecutive-integers'),
('coprime'),
('counterexample'),
('counting'),
('definition'),
('difference'),
('digits'),
('divisibility'),
('divisibility-test'),
('divisors'),
('equation'),
('euclid-lemma'),
('exponent'),
('exponents'),
('expression'),
('factorial'),
('factorisation'),
('gcd'),
('identity'),
('impossibility'),
('integer'),
('lcm'),
('linear-combination'),
('listing'),
('optimization'),
('prime'),
('proof'),
('remainders'),
('squares'),
('tau-function'),
('trailing-zeros'),
('transitivity');

DROP TEMPORARY TABLE IF EXISTS seed_problem_tag_rows;
CREATE TEMPORARY TABLE seed_problem_tag_rows (
  problem_code VARCHAR(40) NOT NULL,
  tag_slug VARCHAR(120) NOT NULL,
  PRIMARY KEY (problem_code, tag_slug)
) ENGINE=Memory DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO seed_problem_tag_rows (problem_code, tag_slug) VALUES
('NT-01-001', 'definition'),
('NT-01-001', 'integer'),
('NT-01-002', 'proof'),
('NT-01-002', 'definition'),
('NT-01-003', 'proof'),
('NT-01-003', 'linear-combination'),
('NT-01-004', 'proof'),
('NT-01-004', 'difference'),
('NT-01-005', 'factorisation'),
('NT-01-006', 'factorisation'),
('NT-01-007', 'divisors'),
('NT-01-007', 'tau-function'),
('NT-01-008', 'divisors'),
('NT-01-008', 'squares'),
('NT-01-009', 'gcd'),
('NT-01-009', 'factorisation'),
('NT-01-010', 'lcm'),
('NT-01-010', 'factorisation'),
('NT-01-011', 'gcd'),
('NT-01-011', 'lcm'),
('NT-01-011', 'identity'),
('NT-01-012', 'consecutive-integers'),
('NT-01-012', 'proof'),
('NT-01-013', 'consecutive-integers'),
('NT-01-013', 'proof'),
('NT-01-014', 'consecutive-integers'),
('NT-01-014', 'proof'),
('NT-01-015', 'factorisation'),
('NT-01-015', 'proof'),
('NT-01-016', 'prime'),
('NT-01-016', 'classification'),
('NT-01-017', 'optimization'),
('NT-01-017', 'divisors'),
('NT-01-018', 'optimization'),
('NT-01-018', 'divisors'),
('NT-01-019', 'equation'),
('NT-01-019', 'divisors'),
('NT-01-020', 'remainders'),
('NT-01-020', 'expression'),
('NT-01-021', 'remainders'),
('NT-01-021', 'squares'),
('NT-01-022', 'remainders'),
('NT-01-022', 'squares'),
('NT-01-022', 'impossibility'),
('NT-01-023', 'prime'),
('NT-01-023', 'divisibility'),
('NT-01-024', 'prime'),
('NT-01-024', 'remainders'),
('NT-01-024', 'proof'),
('NT-01-025', 'factorial'),
('NT-01-025', 'exponent'),
('NT-01-026', 'factorial'),
('NT-01-026', 'trailing-zeros'),
('NT-01-027', 'counterexample'),
('NT-01-028', 'prime'),
('NT-01-028', 'euclid-lemma'),
('NT-01-029', 'coprime'),
('NT-01-029', 'divisibility'),
('NT-01-030', 'divisors'),
('NT-01-030', 'exponents'),
('NT-01-031', 'gcd'),
('NT-01-031', 'divisors'),
('NT-01-032', 'divisors'),
('NT-01-032', 'listing'),
('NT-01-033', 'divisibility-test'),
('NT-01-033', 'digits'),
('NT-01-034', 'digits'),
('NT-01-034', 'divisibility-test'),
('NT-01-035', 'divisors'),
('NT-01-035', 'counting'),
('NT-01-036', 'divisors'),
('NT-01-036', 'counting'),
('NT-01-037', 'proof'),
('NT-01-037', 'transitivity'),
('NT-01-038', 'proof'),
('NT-01-038', 'absolute-value'),
('NT-01-039', 'gcd'),
('NT-01-039', 'lcm'),
('NT-01-040', 'proof'),
('NT-01-040', 'factorisation'),
('NT-01-040', 'consecutive-integers');

INSERT IGNORE INTO problem_tags (problem_id, tag_id)
SELECT p.id, t.id
FROM seed_problem_tag_rows s
JOIN problems p ON p.problem_code = s.problem_code
JOIN tags t ON t.slug COLLATE utf8mb4_unicode_ci = s.tag_slug COLLATE utf8mb4_unicode_ci;

DROP TEMPORARY TABLE IF EXISTS seed_problem_tag_rows;

DROP TEMPORARY TABLE IF EXISTS seed_problem_rows;
