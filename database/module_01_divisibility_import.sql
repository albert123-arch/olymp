SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SET @course_id := (SELECT id FROM courses WHERE slug = 'number-theory' LIMIT 1);
INSERT INTO chapters (course_id, slug, sort_order, is_published, created_at, updated_at)
SELECT @course_id, 'divisibility-prime-factorisation', 1, 1, NOW(), NOW()
WHERE @course_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM chapters WHERE course_id = @course_id AND slug = 'divisibility-prime-factorisation');
SET @chapter_id := (SELECT id FROM chapters WHERE course_id = @course_id AND slug = 'divisibility-prime-factorisation' LIMIT 1);
UPDATE chapters SET sort_order = 1, is_published = 1, updated_at = NOW() WHERE id = @chapter_id;

INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES
(@chapter_id, 'en', 'Divisibility and Prime Factorisation', '<p>Definitions of divisibility, prime factorisation, divisor counting, and first GCD/LCM ideas.</p>', '<h3>1. What Divisibility Means</h3>
<p>For integers \\(a\\) and \\(b\\), with \\(a \\ne 0\\), we say that \\(a\\) divides \\(b\\) if there is an integer \\(k\\) such that</p>
<p>\\[
b = ak.
\\]</p>
<p>We write \\(a \\mid b\\). If no such integer exists, we write \\(a \\nmid b\\).</p>
<p>Divisibility is not about approximate division. It is about exact integer structure. For example, \\(7 \\mid 42\\) because \\(42 = 7 \\cdot 6\\), but \\(7 \\nmid 43\\).</p>
<h3>2. Basic Laws</h3>
<p>If \\(a \\mid b\\) and \\(a \\mid c\\), then:</p>
<p>\\[
a \\mid (b+c), \\quad a \\mid (b-c), \\quad a \\mid mb
\\]</p>
<p>for every integer \\(m\\). A useful combined form is:</p>
<p>\\[
a \\mid b,\\ a \\mid c \\Rightarrow a \\mid xb + yc
\\]</p>
<p>for all integers \\(x\\) and \\(y\\).</p>
<p>This idea is one of the main engines of olympiad number theory: build a new expression from old divisible expressions.</p>
<h3>3. Remainders</h3>
<p>Every integer \\(n\\) can be written in the form</p>
<p>\\[
n = dq + r,\\quad 0 \\le r < d.
\\]</p>
<p>Here \\(r\\) is the remainder when \\(n\\) is divided by \\(d\\). Divisibility by \\(d\\) means exactly that \\(r=0\\).</p>
<h3>4. Prime Numbers</h3>
<p>A prime number is a positive integer greater than \\(1\\) with exactly two positive divisors: \\(1\\) and itself. The first primes are</p>
<p>\\[
2,3,5,7,11,13,17,19,23,\\dots
\\]</p>
<p>The number \\(1\\) is not prime. This convention makes prime factorisation unique.</p>
<h3>5. Prime Factorisation</h3>
<p>Every integer \\(n>1\\) can be written uniquely as a product of prime powers:</p>
<p>\\[
n = p_1^{a_1}p_2^{a_2}\\cdots p_m^{a_m},
\\]</p>
<p>where the \\(p_i\\) are distinct primes and the \\(a_i\\) are positive integers.</p>
<p>For example,</p>
<p>\\[
360 = 2^3 \\cdot 3^2 \\cdot 5.
\\]</p>
<h3>6. Counting Divisors</h3>
<p>If</p>
<p>\\[
n = p_1^{a_1}p_2^{a_2}\\cdots p_m^{a_m},
\\]</p>
<p>then the number of positive divisors of \\(n\\) is</p>
<p>\\[
\\tau(n) = (a_1+1)(a_2+1)\\cdots(a_m+1).
\\]</p>
<p>Each divisor chooses an exponent from \\(0\\) to \\(a_i\\) for each prime \\(p_i\\).</p>
<h3>7. Greatest Common Divisor and Least Common Multiple</h3>
<p>For two positive integers \\(a\\) and \\(b\\), the greatest common divisor is written \\(\\gcd(a,b)\\), and the least common multiple is written \\(\\operatorname{lcm}(a,b)\\).</p>
<p>If</p>
<p>\\[
a = \\prod p^{\\alpha_p},\\quad b = \\prod p^{\\beta_p},
\\]</p>
<p>then</p>
<p>\\[
\\gcd(a,b)=\\prod p^{\\min(\\alpha_p,\\beta_p)},\\quad
\\operatorname{lcm}(a,b)=\\prod p^{\\max(\\alpha_p,\\beta_p)}.
\\]</p>
<p>Also,</p>
<p>\\[
ab = \\gcd(a,b)\\operatorname{lcm}(a,b).
\\]</p>', '<p>Since \\(n=6k=3(2k)\\), \\(3 \\mid n\\).</p>
<ol>
<li>Prove that if \\(6 \\mid n\\), then \\(3 \\mid n\\).</li>
</ol>
<ol>
<li>Find the prime factorisation of \\(840\\).</li>
</ol>
<p>\\[
   840=84\\cdot10=(2^2\\cdot3\\cdot7)(2\\cdot5)=2^3\\cdot3\\cdot5\\cdot7.
\\]</p>
<p>From \\(840=2^3\\cdot3\\cdot5\\cdot7\\), the number is \\((3+1)(1+1)^3=32\\).</p>
<ol>
<li>Count the positive divisors of \\(840\\).</li>
</ol>
<ol>
<li>Find \\(\\gcd(84,126)\\).</li>
</ol>
<p>\\[
   84=2^2\\cdot3\\cdot7,\\quad 126=2\\cdot3^2\\cdot7,
\\]</p>
<p>so \\(\\gcd(84,126)=2\\cdot3\\cdot7=42\\).</p>
<ol>
<li>Find \\(\\operatorname{lcm}(84,126)\\).</li>
</ol>
<p>\\[
   \\operatorname{lcm}(84,126)=2^2\\cdot3^2\\cdot7=252.
\\]</p>
<p>If \\(b=ak\\), then \\(bc=a(kc)\\).</p>
<ol>
<li>Prove that if \\(a \\mid b\\), then \\(a \\mid bc\\).</li>
</ol>
<p>Write \\(b=ax\\), \\(c=ay\\). Then \\(5b-2c=a(5x-2y)\\).</p>
<ol>
<li>Prove that if \\(a \\mid b\\) and \\(a \\mid c\\), then \\(a \\mid 5b-2c\\).</li>
</ol>
<p>We need \\((a+1)(b+1)=12\\). The smallest choice is \\(2^2\\cdot3^3=108\\), compared with \\(2^5\\cdot3=96\\). Actually \\(96\\) is smaller, so the answer is \\(96\\).</p>
<ol>
<li>Find the smallest positive integer with prime factorisation using only \\(2\\) and \\(3\\) and exactly \\(12\\) divisors.</li>
</ol>
<ol>
<li>Show that \\(n^2-n\\) is even for every integer \\(n\\).</li>
</ol>
<p>\\[
   n^2-n=n(n-1),
\\]</p>
<p>the product of two consecutive integers, one of which is even.</p>
<ol>
<li>Show that \\(3 \\mid n^3-n\\) for every integer \\(n\\).</li>
</ol>
<p>\\[
    n^3-n=n(n-1)(n+1),
\\]</p>
<p>three consecutive integers include a multiple of \\(3\\).</p>
<p>If \\(p>3\\), then one of \\(p,p+2,p+4\\) is divisible by \\(3\\). Since all are greater than \\(3\\), impossible. Thus \\(p=3\\).</p>
<ol>
<li>Find all primes \\(p\\) such that \\(p+2\\) and \\(p+4\\) are also prime.</li>
</ol>
<p>The divisors are \\(1,2,3,4,6,8,12,24\\).</p>
<ol>
<li>Find all \\(n\\) such that \\(n \\mid 24\\) and \\(n\\) is positive.</li>
</ol>
<p>Write \\(b=ak\\) and \\(a=bm\\). Then \\(a=akm\\). If \\(a\\ne0\\), \\(km=1\\), so \\(k=m=1\\) or \\(k=m=-1\\).</p>
<ol>
<li>If \\(a\\mid b\\) and \\(b\\mid a\\), prove that \\(a=\\pm b\\).</li>
</ol>
<ol>
<li>Find the largest power of \\(2\\) dividing \\(100!\\).</li>
</ol>
<p>\\[
    \\left\\lfloor\\frac{100}{2}\\right\\rfloor+\\left\\lfloor\\frac{100}{4}\\right\\rfloor+\\left\\lfloor\\frac{100}{8}\\right\\rfloor+\\left\\lfloor\\frac{100}{16}\\right\\rfloor+\\left\\lfloor\\frac{100}{32}\\right\\rfloor+\\left\\lfloor\\frac{100}{64}\\right\\rfloor=97.
\\]</p>
<p>This is Euclid''s lemma. In this module it may be used as a theorem after prime factorisation is introduced.</p>
<ol>
<li>Prove that if \\(p\\) is prime and \\(p\\mid ab\\), then \\(p\\mid a\\) or \\(p\\mid b\\).</li>
</ol>', '<p>A focused set for printing and independent practice.</p>
<h3>Warm-up</h3>
<ol>
<li><strong>NT-01-001. Exact Division</strong> Decide whether \\(8\\mid 120\\), \\(8\\mid 122\\), and \\(9\\mid 117\\). Explain each answer.</li>
<li><strong>NT-01-005. Prime Factorisation of 420</strong> Find the prime factorisation of \\(420\\).</li>
<li><strong>NT-01-007. Counting Divisors</strong> How many positive divisors does \\(360\\) have?</li>
<li><strong>NT-01-009. GCD from Factorisations</strong> Find \\(\\gcd(144,180)\\).</li>
</ol>
<h3>Core Practice</h3>
<ol>
<li><strong>NT-01-012. Consecutive Product</strong> Prove that \\(2\\mid n(n+1)\\) for every integer \\(n\\).</li>
<li><strong>NT-01-014. Six Divides a Product</strong> Prove that \\(6\\mid n(n+1)(n+2)\\) for every integer \\(n\\).</li>
<li><strong>NT-01-017. Smallest Number with Eight Divisors</strong> Find the smallest positive integer with exactly \\(8\\) positive divisors.</li>
<li><strong>NT-01-020. Dividing a Shifted Expression</strong> If \\(n\\) leaves remainder \\(2\\) when divided by \\(5\\), what remainder does \\(3n+1\\) leave when divided by \\(5\\)?</li>
<li><strong>NT-01-030. Find the Missing Exponent</strong> For \\(n=2^3\\cdot3^a\\), find all positive integers \\(a\\) such that \\(n\\) has \\(20\\) positive divisors.</li>
</ol>
<h3>Challenge</h3>
<ol>
<li><strong>NT-01-024. Prime Triple</strong> Find all primes \\(p\\) such that \\(p\\), \\(p+2\\), and \\(p+4\\) are all prime.</li>
<li><strong>NT-01-029. Coprime Product</strong> If \\(\\gcd(a,b)=1\\), \\(a\\mid n\\), and \\(b\\mid n\\), prove that \\(ab\\mid n\\).</li>
<li><strong>NT-01-035. Even Divisors</strong> How many positive even divisors does \\(720\\) have?</li>
<li><strong>NT-01-040. Olympiad Warm-Up</strong> Prove that \\(24\\mid n(n^2-1)(n+2)\\) for every integer \\(n\\).</li>
</ol>', '<h3>Teaching Goals</h3>
<ul>
<li>Make divisibility a language of structure, not arithmetic speed.</li>
<li>Train students to rewrite a divisibility claim as \\(b=ak\\).</li>
<li>Introduce prime factorisation as a coordinate system for positive integers.</li>
<li>Connect divisor counting with independent choices of exponents.</li>
<li>Build early olympiad habits: test small cases, factor expressions, use remainders, and look for invariant divisibility.</li>
</ul>
<h3>Suggested Lesson Sequence</h3>
<ol>
<li>Exact division and notation.</li>
<li>Divisibility laws and linear combinations.</li>
<li>Remainders and modular viewpoint without heavy notation.</li>
<li>Prime numbers and uniqueness of factorisation.</li>
<li>Divisor counting.</li>
<li>GCD and LCM through prime exponents.</li>
<li>Mixed olympiad examples.</li>
<li>Timed practice and reflection.</li>
</ol>
<h3>Common Misconceptions</h3>
<ul>
<li>Students may treat \\(a\\mid b\\) as a fraction. Emphasize that it is a statement, not a number.</li>
<li>Students often call \\(1\\) prime. Explain that excluding \\(1\\) preserves uniqueness of factorisation.</li>
<li>Students may count divisors by listing and miss pairs. Prime exponents give a safer method.</li>
<li>Students may confuse \\(\\gcd\\) and \\(\\operatorname{lcm}\\). Use minimum and maximum exponent language.</li>
</ul>
<h3>Differentiation</h3>
<ul>
<li>Support: give factor trees, divisor tables, and fill-in proof templates.</li>
<li>Core: mix computation with short proofs.</li>
<li>Extension: include factorial exponents, Euclid''s lemma, and parameter problems.</li>
</ul>
<h3>Assessment Ideas</h3>
<ul>
<li>Exit ticket: prove \\(d\\mid a\\) and \\(d\\mid b\\Rightarrow d\\mid 3a-2b\\).</li>
<li>Quick quiz: factorise three numbers and count divisors.</li>
<li>Challenge: find the smallest integer with exactly \\(18\\) divisors.</li>
</ul>')
ON DUPLICATE KEY UPDATE title = VALUES(title), description_html = VALUES(description_html), theory_html = VALUES(theory_html), examples_html = VALUES(examples_html), worksheet_html = VALUES(worksheet_html), teacher_notes_html = VALUES(teacher_notes_html);

INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES
(@chapter_id, 'ru', 'Делимость и разложение на простые множители', '<p>Определения делимости, простые множители, подсчет делителей и первые идеи НОД и НОК.</p>', '<h3>1. Что означает делимость</h3>
<p>Для целых чисел \\(a\\) и \\(b\\), где \\(a \\ne 0\\), говорят, что \\(a\\) делит \\(b\\), если существует целое число \\(k\\), такое что</p>
<p>\\[
b=ak.
\\]</p>
<p>Обозначение: \\(a\\mid b\\). Если такого целого \\(k\\) нет, пишут \\(a\\nmid b\\).</p>
<p>Делимость означает точную целочисленную структуру, а не приближенное деление.</p>
<h3>2. Основные свойства</h3>
<p>Если \\(a\\mid b\\) и \\(a\\mid c\\), то</p>
<p>\\[
a\\mid(b+c),\\quad a\\mid(b-c),\\quad a\\mid mb
\\]</p>
<p>для любого целого \\(m\\). Более общий вид:</p>
<p>\\[
a\\mid b,\\ a\\mid c \\Rightarrow a\\mid xb+yc
\\]</p>
<p>для любых целых \\(x\\) и \\(y\\).</p>
<h3>3. Простые числа и разложение на множители</h3>
<p>Простое число - это натуральное число больше \\(1\\), которое имеет ровно два положительных делителя: \\(1\\) и само себя.</p>
<p>Каждое натуральное число \\(n>1\\) единственным образом раскладывается в произведение степеней простых чисел:</p>
<p>\\[
n=p_1^{a_1}p_2^{a_2}\\cdots p_m^{a_m}.
\\]</p>
<p>Например,</p>
<p>\\[
360=2^3\\cdot3^2\\cdot5.
\\]</p>
<h3>4. Количество делителей</h3>
<p>Если</p>
<p>\\[
n=p_1^{a_1}p_2^{a_2}\\cdots p_m^{a_m},
\\]</p>
<p>то количество положительных делителей равно</p>
<p>\\[
\\tau(n)=(a_1+1)(a_2+1)\\cdots(a_m+1).
\\]</p>
<h3>5. НОД и НОК</h3>
<p>Через \\(\\operatorname{НОД}(a,b)\\) обозначается наибольший общий делитель, а через \\(\\operatorname{НОК}(a,b)\\) - наименьшее общее кратное.</p>
<p>\\[
\\operatorname{НОД}(a,b)=\\prod p^{\\min(\\alpha_p,\\beta_p)},\\quad
\\operatorname{НОК}(a,b)=\\prod p^{\\max(\\alpha_p,\\beta_p)}.
\\]</p>
<p>Также верно:</p>
<p>\\[
ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b).
\\]</p>', '<h3>NT-01-001. Точная делимость</h3>
<p>Определите, верно ли \\(8\\mid 120\\), \\(8\\mid 122\\) и \\(9\\mid 117\\). Объясните каждый ответ.</p>
<p>\\(120=8\\cdot15\\), значит \\(8\\mid120\\). Так как \\(122=8\\cdot15+2\\), то \\(8\\nmid122\\). Также \\(117=9\\cdot13\\), значит \\(9\\mid117\\).</p>
<h3>NT-01-005. Разложение числа 420</h3>
<p>Найдите разложение числа \\(420\\) на простые множители.</p>
<p>\\(420=42\\cdot10=(2\\cdot3\\cdot7)(2\\cdot5)=2^2\\cdot3\\cdot5\\cdot7\\).</p>
<h3>NT-01-010. НОК по разложениям</h3>
<p>Найдите \\(\\operatorname{НОК}(144,180)\\).</p>
<p>Так как \\(144=2^4\\cdot3^2\\) и \\(180=2^2\\cdot3^2\\cdot5\\), получаем \\(\\operatorname{НОК}(144,180)=2^4\\cdot3^2\\cdot5=720\\).</p>
<h3>NT-01-012. Произведение соседних чисел</h3>
<p>Докажите, что \\(2\\mid n(n+1)\\) для любого целого \\(n\\).</p>
<p>Числа \\(n\\) и \\(n+1\\) соседние, значит одно из них четное. Поэтому их произведение делится на \\(2\\).</p>
<h3>NT-01-014. Делимость произведения на шесть</h3>
<p>Докажите, что \\(6\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</p>
<p>Среди трех последовательных чисел одно делится на \\(3\\), и хотя бы одно четное. Так как \\(2\\) и \\(3\\) взаимно просты, произведение делится на \\(6\\).</p>
<h3>NT-01-020. Остаток линейного выражения</h3>
<p>Если \\(n\\) дает остаток \\(2\\) при делении на \\(5\\), какой остаток дает \\(3n+1\\) при делении на \\(5\\)?</p>
<p>Пусть \\(n=5k+2\\). Тогда \\(3n+1=15k+7=5(3k+1)+2\\), значит остаток равен \\(2\\).</p>
<h3>NT-01-024. Тройка простых чисел</h3>
<p>Найдите все простые \\(p\\), для которых \\(p\\), \\(p+2\\) и \\(p+4\\) также простые.</p>
<p>Если \\(p>3\\), то числа \\(p,p+2,p+4\\) покрывают все остатки по модулю \\(3\\), значит одно из них делится на \\(3\\). Оно больше \\(3\\), поэтому не может быть простым. Проверяем \\(p=3\\): получаем \\(3,5,7\\), все простые. Следовательно, \\(p=3\\).</p>
<h3>NT-01-035. Четные делители</h3>
<p>Сколько положительных четных делителей имеет число \\(720\\)?</p>
<p>\\(720=2^4\\cdot3^2\\cdot5\\). У четного делителя показатель у \\(2\\) может быть \\(1,2,3\\) или \\(4\\): \\(4\\) варианта. У показателя \\(3\\) есть \\(3\\) варианта, у показателя \\(5\\) — \\(2\\) варианта. Всего \\(4\\cdot3\\cdot2=24\\).</p>', '<p>Подборка задач для печати и самостоятельной работы.</p>
<h3>Разминка</h3>
<ol>
<li><strong>NT-01-001. Точная делимость</strong> Определите, верно ли \\(8\\mid 120\\), \\(8\\mid 122\\) и \\(9\\mid 117\\). Объясните каждый ответ.</li>
<li><strong>NT-01-005. Разложение числа 420</strong> Найдите разложение числа \\(420\\) на простые множители.</li>
<li><strong>NT-01-007. Количество делителей</strong> Сколько положительных делителей имеет число \\(360\\)?</li>
<li><strong>NT-01-009. НОД по разложениям</strong> Найдите \\(\\operatorname{НОД}(144,180)\\).</li>
</ol>
<h3>Основная практика</h3>
<ol>
<li><strong>NT-01-012. Произведение соседних чисел</strong> Докажите, что \\(2\\mid n(n+1)\\) для любого целого \\(n\\).</li>
<li><strong>NT-01-014. Делимость произведения на шесть</strong> Докажите, что \\(6\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</li>
<li><strong>NT-01-017. Наименьшее число с восемью делителями</strong> Найдите наименьшее положительное целое число, имеющее ровно \\(8\\) положительных делителей.</li>
<li><strong>NT-01-020. Остаток линейного выражения</strong> Если \\(n\\) дает остаток \\(2\\) при делении на \\(5\\), какой остаток дает \\(3n+1\\) при делении на \\(5\\)?</li>
<li><strong>NT-01-030. Найти неизвестный показатель</strong> Для \\(n=2^3\\cdot3^a\\) найдите все положительные целые \\(a\\), при которых \\(n\\) имеет \\(20\\) положительных делителей.</li>
</ol>
<h3>Сложные задачи</h3>
<ol>
<li><strong>NT-01-024. Тройка простых чисел</strong> Найдите все простые \\(p\\), для которых \\(p\\), \\(p+2\\) и \\(p+4\\) также простые.</li>
<li><strong>NT-01-029. Произведение взаимно простых делителей</strong> Если \\(\\operatorname{НОД}(a,b)=1\\), \\(a\\mid n\\) и \\(b\\mid n\\), докажите, что \\(ab\\mid n\\).</li>
<li><strong>NT-01-035. Четные делители</strong> Сколько положительных четных делителей имеет число \\(720\\)?</li>
<li><strong>NT-01-040. Олимпиадная разминка</strong> Докажите, что \\(24\\mid n(n^2-1)(n+2)\\) для любого целого \\(n\\).</li>
</ol>', '<h3>Цели урока</h3>
<ul>
<li>Сделать делимость языком структуры, а не только вычислением.</li>
<li>Научить переписывать утверждение \\(a\\mid b\\) как \\(b=ak\\).</li>
<li>Ввести простые числа и разложение на простые множители.</li>
<li>Связать подсчет делителей с выбором показателей степеней.</li>
<li>Подготовить к олимпиадным приемам: разности, линейные комбинации, остатки и факторизация.</li>
</ul>
<h3>Ход урока</h3>
<ol>
<li>Точная делимость и запись \\(a\\mid b\\).</li>
<li>Основные свойства делимости и линейные комбинации.</li>
<li>Остатки без перегрузки формальной модульной записью.</li>
<li>Простые числа и единственность разложения.</li>
<li>Подсчет делителей через показатели степеней.</li>
<li>НОД и НОК через простые степени.</li>
<li>Смешанные олимпиадные примеры.</li>
<li>Короткая самостоятельная работа и обсуждение решений.</li>
</ol>')
ON DUPLICATE KEY UPDATE title = VALUES(title), description_html = VALUES(description_html), theory_html = VALUES(theory_html), examples_html = VALUES(examples_html), worksheet_html = VALUES(worksheet_html), teacher_notes_html = VALUES(teacher_notes_html);

INSERT INTO tags (slug, created_at) SELECT 'absolute-value', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'absolute-value');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'absolute-value' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Absolute Value') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Модуль числа') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'classification', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'classification');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'classification' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Classification') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Классификация') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'consecutive-integers', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'consecutive-integers');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'consecutive-integers' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Consecutive Integers') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Последовательные целые числа') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'coprime', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'coprime');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'coprime' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Coprime Numbers') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Взаимно простые числа') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'counterexample', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'counterexample');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'counterexample' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Counterexample') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Контрпример') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'counting', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'counting');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'counting' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Counting') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Подсчет') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'definition', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'definition');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'definition' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Definition') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Определение') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'difference', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'difference');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'difference' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Difference') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Разность') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'digits', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'digits');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'digits' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Digits') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Цифры') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'divisibility', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'divisibility');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'divisibility' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Divisibility') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Делимость') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'divisibility-test', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'divisibility-test');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'divisibility-test' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Divisibility Test') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Признак делимости') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'divisors', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'divisors');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'divisors' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Divisors') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Делители') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'equation', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'equation');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'equation' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Equation') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Уравнение') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'euclid-lemma', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'euclid-lemma');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'euclid-lemma' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Euclid Lemma') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Лемма Евклида') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'exponent', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'exponent');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'exponent' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Exponent') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Показатель степени') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'exponents', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'exponents');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'exponents' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Exponents') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Показатели степеней') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'expression', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'expression');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'expression' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Expression') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Выражение') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'factorial', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'factorial');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'factorial' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Factorial') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Факториал') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'factorisation', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'factorisation');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'factorisation' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Factorisation') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Разложение на множители') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'gcd', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'gcd');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'gcd' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'GCD') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'НОД') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'identity', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'identity');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'identity' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Identity') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Тождество') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'impossibility', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'impossibility');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'impossibility' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Impossibility') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Невозможность') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'integer', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'integer');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'integer' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Integer') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Целое число') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'lcm', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'lcm');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'lcm' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'LCM') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'НОК') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'linear-combination', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'linear-combination');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'linear-combination' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Linear Combination') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Линейная комбинация') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'listing', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'listing');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'listing' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Listing') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Перебор') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'optimization', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'optimization');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'optimization' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Optimization') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Оптимизация') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'prime', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'prime');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'prime' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Prime Numbers') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Простые числа') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'proof', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'proof');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'proof' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Proof') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Доказательство') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'remainders', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'remainders');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'remainders' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Remainders') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Остатки') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'squares', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'squares');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'squares' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Squares') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Квадраты') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'tau-function', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'tau-function');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'tau-function' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Divisor Function') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Функция числа делителей') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'trailing-zeros', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'trailing-zeros');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'trailing-zeros' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Trailing Zeros') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Нули в конце') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'transitivity', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'transitivity');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'transitivity' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Transitivity') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Транзитивность') ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-001', 1, 1, 'computation', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-001' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Exact Division', '<p>Decide whether \\(8\\mid 120\\), \\(8\\mid 122\\), and \\(9\\mid 117\\). Explain each answer.</p>', '<p>Use the definition: \\(a\\mid b\\) means \\(b=ak\\) for some integer \\(k\\).</p>', '<p>\\(120=8\\cdot15\\), so \\(8\\mid120\\). Since \\(122=8\\cdot15+2\\), \\(8\\nmid122\\). Also \\(117=9\\cdot13\\), so \\(9\\mid117\\).</p>', '<p>Use this to separate exact division from decimal division.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Точная делимость', '<p>Определите, верно ли \\(8\\mid 120\\), \\(8\\mid 122\\) и \\(9\\mid 117\\). Объясните каждый ответ.</p>', '<p>Используйте определение: \\(a\\mid b\\) означает, что \\(b=ak\\) для некоторого целого \\(k\\).</p>', '<p>\\(120=8\\cdot15\\), значит \\(8\\mid120\\). Так как \\(122=8\\cdot15+2\\), то \\(8\\nmid122\\). Также \\(117=9\\cdot13\\), значит \\(9\\mid117\\).</p>', '<p>Используйте эту задачу, чтобы отделить точную делимость от обычного деления с остатком или десятичной дробью.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'definition';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'integer';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-002', 2, 1, 'proof', 2, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-002' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Divisibility from a Product', '<p>Prove that if \\(12\\mid n\\), then \\(3\\mid n\\).</p>', '<p>Write \\(n=12k\\).</p>', '<p>If \\(12\\mid n\\), then \\(n=12k=3(4k)\\) for some integer \\(k\\). Therefore \\(3\\mid n\\).</p>', '<p>Ask students which divisors of \\(12\\) must also divide \\(n\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Делимость из произведения', '<p>Докажите, что если \\(12\\mid n\\), то \\(3\\mid n\\).</p>', '<p>Запишите \\(n=12k\\).</p>', '<p>Если \\(12\\mid n\\), то \\(n=12k=3(4k)\\) для некоторого целого \\(k\\). Следовательно, \\(3\\mid n\\).</p>', '<p>Спросите учеников, какие делители числа \\(12\\) обязательно делят \\(n\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'definition';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-003', 3, 1, 'proof', 3, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-003' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Linear Combination', '<p>If \\(7\\mid a\\) and \\(7\\mid b\\), prove that \\(7\\mid 4a+5b\\).</p>', '<p>Set \\(a=7x\\) and \\(b=7y\\).</p>', '<p>Let \\(a=7x\\) and \\(b=7y\\). Then \\(4a+5b=28x+35y=7(4x+5y)\\), so \\(7\\mid4a+5b\\).</p>', '<p>This is a foundational proof pattern for later modular arithmetic.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Линейная комбинация', '<p>Если \\(7\\mid a\\) и \\(7\\mid b\\), докажите, что \\(7\\mid 4a+5b\\).</p>', '<p>Положите \\(a=7x\\) и \\(b=7y\\).</p>', '<p>Пусть \\(a=7x\\) и \\(b=7y\\). Тогда \\(4a+5b=28x+35y=7(4x+5y)\\), значит \\(7\\mid4a+5b\\).</p>', '<p>Это базовый шаблон доказательства, который позже естественно переходит в сравнения по модулю.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'linear-combination';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-004', 4, 2, 'proof', 4, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-004' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Check a Divisibility Claim', '<p>If \\(11\\mid 3x+2\\) and \\(11\\mid x-3\\), must \\(11\\mid 11x-7\\)? Give a proof or counterexample.</p>', '<p>Test a value of \\(x\\) satisfying both given conditions.</p>', '<p>Since \\(11\\mid3x+2\\) and \\(11\\mid x-3\\), any integer linear combination is divisible by \\(11\\). Now \\((3x+2)+8(x-3)=11x-22=11(x-2)\\), so it is divisible by \\(11\\). Also \\((11x-7)-(11x-22)=15\\), so the original conclusion is not always true. For example, \\(x=3\\) satisfies \\(x-3=0\\), but \\(3x+2=11\\), while \\(11x-7=26\\) is not divisible by \\(11\\). Thus the statement is false.</p>', '<p>This is intentionally a trap: students must learn to check whether a requested conclusion actually follows.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Проверка утверждения о делимости', '<p>Если \\(11\\mid 3x+2\\) и \\(11\\mid x-3\\), обязательно ли \\(11\\mid 11x-7\\)? Дайте доказательство или контрпример.</p>', '<p>Проверьте значение \\(x\\), которое удовлетворяет обоим данным условиям.</p>', '<p>Так как \\(11\\mid3x+2\\) и \\(11\\mid x-3\\), любая целая линейная комбинация этих выражений делится на \\(11\\). Например, \\((3x+2)+8(x-3)=11x-22=11(x-2)\\). Но \\((11x-7)-(11x-22)=15\\), поэтому исходное заключение не следует. При \\(x=3\\) имеем \\(x-3=0\\), \\(3x+2=11\\), но \\(11x-7=26\\), что не делится на \\(11\\). Значит утверждение ложно.</p>', '<p>Это намеренная ловушка: ученики должны привыкнуть проверять, действительно ли вывод следует из условий.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'difference';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-005', 5, 1, 'computation', 5, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-005' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Prime Factorisation of 420', '<p>Find the prime factorisation of \\(420\\).</p>', '<p>Break \\(420\\) as \\(42\\cdot10\\).</p>', '<p>\\(420=42\\cdot10=(2\\cdot3\\cdot7)(2\\cdot5)=2^2\\cdot3\\cdot5\\cdot7\\).</p>', '<p>Encourage multiple valid factor trees and one canonical final form.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Разложение числа 420', '<p>Найдите разложение числа \\(420\\) на простые множители.</p>', '<p>Разбейте \\(420\\) как \\(42\\cdot10\\).</p>', '<p>\\(420=42\\cdot10=(2\\cdot3\\cdot7)(2\\cdot5)=2^2\\cdot3\\cdot5\\cdot7\\).</p>', '<p>Разрешайте разные деревья множителей, но требуйте один канонический ответ в конце.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-006', 6, 2, 'computation', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-006' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Prime Factorisation of 756', '<p>Find the prime factorisation of \\(756\\).</p>', '<p>Use \\(756=75\\cdot10+6\\), or divide by \\(2\\), then by \\(3\\).</p>', '<p>\\(756=2\\cdot378=2^2\\cdot189=2^2\\cdot3^3\\cdot7\\).</p>', '<p>Good for checking repeated prime factors.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Разложение числа 756', '<p>Найдите разложение числа \\(756\\) на простые множители.</p>', '<p>Сначала разделите на \\(2\\), затем проверяйте делимость на \\(3\\).</p>', '<p>\\(756=2\\cdot378=2^2\\cdot189=2^2\\cdot3^3\\cdot7\\).</p>', '<p>Хорошая задача для проверки повторяющихся простых множителей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-007', 7, 1, 'computation', 7, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-007' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Counting Divisors', '<p>How many positive divisors does \\(360\\) have?</p>', '<p>First write \\(360\\) as a product of prime powers.</p>', '<p>\\(360=2^3\\cdot3^2\\cdot5\\). Therefore \\(\\tau(360)=(3+1)(2+1)(1+1)=24\\).</p>', '<p>Ask students to explain why each exponent gets one extra choice.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Количество делителей', '<p>Сколько положительных делителей имеет число \\(360\\)?</p>', '<p>Сначала разложите \\(360\\) на степени простых чисел.</p>', '<p>\\(360=2^3\\cdot3^2\\cdot5\\). Поэтому \\(\\tau(360)=(3+1)(2+1)(1+1)=24\\).</p>', '<p>Попросите учеников объяснить, почему к каждому показателю степени прибавляется один вариант.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'tau-function';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-008', 8, 2, 'computation', 8, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-008' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'A Square with Odd Divisors', '<p>Explain why every perfect square has an odd number of positive divisors.</p>', '<p>In a square, all prime exponents are even.</p>', '<p>If \\(n=m^2\\), then every exponent in the prime factorisation of \\(n\\) is even. Thus each factor \\(a_i+1\\) in \\(\\tau(n)=\\prod(a_i+1)\\) is odd, and a product of odd numbers is odd.</p>', '<p>This connects divisor counting with a classic pairing argument.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Квадрат и нечетное число делителей', '<p>Объясните, почему каждый полный квадрат имеет нечетное число положительных делителей.</p>', '<p>В разложении полного квадрата все показатели степеней четные.</p>', '<p>Если \\(n=m^2\\), то все показатели степеней в разложении \\(n\\) на простые множители четные. Значит каждый множитель \\(a_i+1\\) в формуле \\(\\tau(n)=\\prod(a_i+1)\\) нечетен, а произведение нечетных чисел нечетно.</p>', '<p>Эта задача связывает формулу числа делителей с классическим разбиением делителей на пары.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'squares';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-009', 9, 1, 'computation', 9, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-009' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'GCD from Factorisations', '<p>Find \\(\\gcd(144,180)\\).</p>', '<p>Use the minimum exponent of each prime.</p>', '<p>\\(144=2^4\\cdot3^2\\) and \\(180=2^2\\cdot3^2\\cdot5\\). Hence \\(\\gcd(144,180)=2^2\\cdot3^2=36\\).</p>', '<p>Make students verbalize minimum exponents.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОД по разложениям', '<p>Найдите \\(\\operatorname{НОД}(144,180)\\).</p>', '<p>Берите минимальный показатель степени каждого простого числа.</p>', '<p>\\(144=2^4\\cdot3^2\\), а \\(180=2^2\\cdot3^2\\cdot5\\). Поэтому \\(\\operatorname{НОД}(144,180)=2^2\\cdot3^2=36\\).</p>', '<p>Добивайтесь, чтобы ученики проговаривали правило минимальных показателей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-010', 10, 1, 'computation', 10, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-010' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'LCM from Factorisations', '<p>Find \\(\\operatorname{lcm}(144,180)\\).</p>', '<p>Use the maximum exponent of each prime.</p>', '<p>Using \\(144=2^4\\cdot3^2\\) and \\(180=2^2\\cdot3^2\\cdot5\\), we get \\(\\operatorname{lcm}(144,180)=2^4\\cdot3^2\\cdot5=720\\).</p>', '<p>Pair with the previous problem to contrast GCD and LCM.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОК по разложениям', '<p>Найдите \\(\\operatorname{НОК}(144,180)\\).</p>', '<p>Берите максимальный показатель степени каждого простого числа.</p>', '<p>Так как \\(144=2^4\\cdot3^2\\) и \\(180=2^2\\cdot3^2\\cdot5\\), получаем \\(\\operatorname{НОК}(144,180)=2^4\\cdot3^2\\cdot5=720\\).</p>', '<p>Дайте эту задачу рядом с предыдущей, чтобы противопоставить НОД и НОК.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-011', 11, 2, 'computation', 11, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-011' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Product Formula', '<p>Verify that \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\) for \\(a=48\\), \\(b=180\\).</p>', '<p>Compute both \\(\\gcd\\) and \\(\\operatorname{lcm}\\).</p>', '<p>\\(48=2^4\\cdot3\\), \\(180=2^2\\cdot3^2\\cdot5\\). Thus \\(\\gcd=2^2\\cdot3=12\\) and \\(\\operatorname{lcm}=2^4\\cdot3^2\\cdot5=720\\). Then \\(12\\cdot720=8640=48\\cdot180\\).</p>', '<p>This prepares students for proving the identity using prime exponents.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Формула произведения', '<p>Проверьте равенство \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\) для \\(a=48\\), \\(b=180\\).</p>', '<p>Найдите и \\(\\operatorname{НОД}\\), и \\(\\operatorname{НОК}\\).</p>', '<p>\\(48=2^4\\cdot3\\), \\(180=2^2\\cdot3^2\\cdot5\\). Тогда \\(\\operatorname{НОД}=2^2\\cdot3=12\\), а \\(\\operatorname{НОК}=2^4\\cdot3^2\\cdot5=720\\). Получаем \\(12\\cdot720=8640=48\\cdot180\\).</p>', '<p>Эта задача готовит учеников к доказательству формулы через показатели простых степеней.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'identity';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-012', 12, 1, 'proof', 12, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-012' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Consecutive Product', '<p>Prove that \\(2\\mid n(n+1)\\) for every integer \\(n\\).</p>', '<p>Among two consecutive integers, one is even.</p>', '<p>The integers \\(n\\) and \\(n+1\\) are consecutive, so one of them is even. Therefore their product is divisible by \\(2\\).</p>', '<p>Invite both parity-case and consecutive-number solutions.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Произведение соседних чисел', '<p>Докажите, что \\(2\\mid n(n+1)\\) для любого целого \\(n\\).</p>', '<p>Из двух соседних целых чисел одно четное.</p>', '<p>Числа \\(n\\) и \\(n+1\\) соседние, значит одно из них четное. Поэтому их произведение делится на \\(2\\).</p>', '<p>Разрешайте оба подхода: через четность по случаям и через свойство соседних чисел.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'consecutive-integers';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-013', 13, 1, 'proof', 13, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-013' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Three Consecutive Integers', '<p>Prove that \\(3\\mid n(n+1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Among any three consecutive integers, one is a multiple of \\(3\\).</p>', '<p>The integers \\(n,n+1,n+2\\) cover all possible remainders modulo \\(3\\). One is divisible by \\(3\\), so the product is divisible by \\(3\\).</p>', '<p>This is an early doorway into remainders.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Три последовательных числа', '<p>Докажите, что \\(3\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Среди любых трех последовательных целых чисел есть кратное \\(3\\).</p>', '<p>Числа \\(n,n+1,n+2\\) дают все возможные остатки при делении на \\(3\\). Одно из них делится на \\(3\\), значит произведение делится на \\(3\\).</p>', '<p>Это ранний вход в язык остатков.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'consecutive-integers';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-014', 14, 2, 'proof', 14, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-014' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Six Divides a Product', '<p>Prove that \\(6\\mid n(n+1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Show divisibility by \\(2\\) and by \\(3\\).</p>', '<p>Among three consecutive integers, one is divisible by \\(3\\), and at least one is even. Since \\(2\\) and \\(3\\) are coprime, the product is divisible by \\(6\\).</p>', '<p>Emphasize why divisibility by \\(2\\) and \\(3\\) together gives divisibility by \\(6\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Делимость произведения на шесть', '<p>Докажите, что \\(6\\mid n(n+1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Докажите делимость на \\(2\\) и на \\(3\\).</p>', '<p>Среди трех последовательных чисел одно делится на \\(3\\), и хотя бы одно четное. Так как \\(2\\) и \\(3\\) взаимно просты, произведение делится на \\(6\\).</p>', '<p>Подчеркните, почему делимость на \\(2\\) и на \\(3\\) вместе дает делимость на \\(6\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'consecutive-integers';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-015', 15, 2, 'proof', 15, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-015' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Difference of Squares', '<p>Prove that if \\(a-b\\) is divisible by \\(5\\), then \\(a^2-b^2\\) is divisible by \\(5\\).</p>', '<p>Factor \\(a^2-b^2\\).</p>', '<p>Since \\(a^2-b^2=(a-b)(a+b)\\), and \\(5\\mid a-b\\), the product \\((a-b)(a+b)\\) is divisible by \\(5\\).</p>', '<p>A useful example of turning algebraic factorisation into number theory.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Разность квадратов', '<p>Докажите: если \\(a-b\\) делится на \\(5\\), то \\(a^2-b^2\\) делится на \\(5\\).</p>', '<p>Разложите \\(a^2-b^2\\) на множители.</p>', '<p>Так как \\(a^2-b^2=(a-b)(a+b)\\) и \\(5\\mid a-b\\), произведение \\((a-b)(a+b)\\) делится на \\(5\\).</p>', '<p>Полезный пример того, как алгебраическое разложение превращается в доказательство по теории чисел.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-016', 16, 1, 'mixed', 16, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-016' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Prime or Composite', '<p>Classify each number as prime or composite: \\(37, 49, 57, 83\\).</p>', '<p>To test \\(n\\), check prime divisors up to \\(\\sqrt n\\).</p>', '<p>\\(37\\) is prime. \\(49=7^2\\) is composite. \\(57=3\\cdot19\\) is composite. \\(83\\) is prime because it is not divisible by \\(2,3,5,7\\), and \\(\\sqrt{83}<10\\).</p>', '<p>Teach the square-root stopping rule explicitly.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Простое или составное', '<p>Определите, какие числа простые, а какие составные: \\(37, 49, 57, 83\\).</p>', '<p>Для проверки числа \\(n\\) достаточно проверять простые делители не больше \\(\\sqrt n\\).</p>', '<p>\\(37\\) простое. \\(49=7^2\\) составное. \\(57=3\\cdot19\\) составное. \\(83\\) простое, так как оно не делится на \\(2,3,5,7\\), а \\(\\sqrt{83}<10\\).</p>', '<p>Явно объясните правило остановки на квадратном корне.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'classification';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-017', 17, 2, 'mixed', 17, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-017' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Smallest Number with Eight Divisors', '<p>Find the smallest positive integer with exactly \\(8\\) positive divisors.</p>', '<p>Factor \\(8\\) as \\(8\\), \\(4\\cdot2\\), or \\(2\\cdot2\\cdot2\\).</p>', '<p>The possible exponent patterns are \\(7\\), \\(3,1\\), and \\(1,1,1\\). The smallest candidates are \\(2^7=128\\), \\(2^3\\cdot3=24\\), and \\(2\\cdot3\\cdot5=30\\). The smallest is \\(24\\).</p>', '<p>Students often choose many small primes too quickly; compare exponent patterns.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Наименьшее число с восемью делителями', '<p>Найдите наименьшее положительное целое число, имеющее ровно \\(8\\) положительных делителей.</p>', '<p>Разложите \\(8\\) как \\(8\\), \\(4\\cdot2\\) или \\(2\\cdot2\\cdot2\\).</p>', '<p>Возможные наборы показателей: \\(7\\), \\(3,1\\), \\(1,1,1\\). Минимальные кандидаты: \\(2^7=128\\), \\(2^3\\cdot3=24\\), \\(2\\cdot3\\cdot5=30\\). Наименьшее число равно \\(24\\).</p>', '<p>Ученики часто слишком быстро выбирают много маленьких простых чисел; сравните разные наборы показателей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'optimization';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-018', 18, 2, 'mixed', 18, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-018' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Exactly Nine Divisors', '<p>Find the smallest positive integer with exactly \\(9\\) positive divisors.</p>', '<p>The exponent patterns come from \\(9\\) and \\(3\\cdot3\\).</p>', '<p>The patterns are \\(8\\) and \\(2,2\\). The smallest candidates are \\(2^8=256\\) and \\(2^2\\cdot3^2=36\\). Therefore the answer is \\(36\\).</p>', '<p>This reinforces assigning larger exponents to smaller primes.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Ровно девять делителей', '<p>Найдите наименьшее положительное целое число, имеющее ровно \\(9\\) положительных делителей.</p>', '<p>Наборы показателей получаются из \\(9\\) и \\(3\\cdot3\\).</p>', '<p>Возможные наборы: \\(8\\) и \\(2,2\\). Минимальные кандидаты: \\(2^8=256\\) и \\(2^2\\cdot3^2=36\\). Следовательно, ответ \\(36\\).</p>', '<p>Эта задача закрепляет правило: большие показатели выгоднее отдавать меньшим простым числам.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'optimization';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-019', 19, 2, 'computation', 19, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-019' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'A Divisor Equation', '<p>Find all positive integers \\(n\\) such that \\(n\\mid 30\\).</p>', '<p>List the positive divisors of \\(30\\).</p>', '<p>Since \\(30=2\\cdot3\\cdot5\\), its positive divisors are \\(1,2,3,5,6,10,15,30\\).</p>', '<p>A simple but important bridge from divisibility notation to solution sets.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Уравнение с делимостью', '<p>Найдите все положительные целые \\(n\\), такие что \\(n\\mid 30\\).</p>', '<p>Перечислите положительные делители числа \\(30\\).</p>', '<p>Так как \\(30=2\\cdot3\\cdot5\\), его положительные делители: \\(1,2,3,5,6,10,15,30\\).</p>', '<p>Простая, но важная связь между записью делимости и множеством решений.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'equation';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-020', 20, 2, 'computation', 20, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-020' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Dividing a Shifted Expression', '<p>If \\(n\\) leaves remainder \\(2\\) when divided by \\(5\\), what remainder does \\(3n+1\\) leave when divided by \\(5\\)?</p>', '<p>Write \\(n=5k+2\\).</p>', '<p>Let \\(n=5k+2\\). Then \\(3n+1=15k+7=5(3k+1)+2\\), so the remainder is \\(2\\).</p>', '<p>Use this before formal modular notation.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Остаток линейного выражения', '<p>Если \\(n\\) дает остаток \\(2\\) при делении на \\(5\\), какой остаток дает \\(3n+1\\) при делении на \\(5\\)?</p>', '<p>Запишите \\(n=5k+2\\).</p>', '<p>Пусть \\(n=5k+2\\). Тогда \\(3n+1=15k+7=5(3k+1)+2\\), значит остаток равен \\(2\\).</p>', '<p>Используйте эту задачу до введения формальной записи сравнений.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'remainders';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'expression';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-021', 21, 2, 'computation', 21, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-021' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Remainder of a Square', '<p>Show that the square of an integer leaves remainder \\(0\\) or \\(1\\) when divided by \\(4\\).</p>', '<p>Consider even and odd integers.</p>', '<p>If \\(n=2k\\), then \\(n^2=4k^2\\), remainder \\(0\\). If \\(n=2k+1\\), then \\(n^2=4k^2+4k+1\\), remainder \\(1\\).</p>', '<p>This result becomes very useful in impossibility proofs.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Остаток квадрата', '<p>Докажите, что квадрат целого числа дает остаток \\(0\\) или \\(1\\) при делении на \\(4\\).</p>', '<p>Рассмотрите четные и нечетные числа.</p>', '<p>Если \\(n=2k\\), то \\(n^2=4k^2\\), остаток \\(0\\). Если \\(n=2k+1\\), то \\(n^2=4k^2+4k+1\\), остаток \\(1\\).</p>', '<p>Этот факт часто используется в доказательствах невозможности.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'remainders';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'squares';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-022', 22, 2, 'computation', 22, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-022' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'No Square Remainder Two', '<p>Prove that no integer square can leave remainder \\(2\\) when divided by \\(4\\).</p>', '<p>Use the result that square remainders modulo \\(4\\) are only \\(0\\) and \\(1\\).</p>', '<p>From the even/odd cases, every integer square is either \\(4k\\) or \\(4k+1\\). Therefore remainder \\(2\\) is impossible.</p>', '<p>Ask students to connect this to equations such as \\(x^2=4m+2\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Квадрат не дает остаток два', '<p>Докажите, что квадрат целого числа не может давать остаток \\(2\\) при делении на \\(4\\).</p>', '<p>Используйте факт, что остатки квадратов по модулю \\(4\\) бывают только \\(0\\) и \\(1\\).</p>', '<p>Из рассмотрения четного и нечетного случая следует, что любой квадрат имеет вид \\(4k\\) или \\(4k+1\\). Поэтому остаток \\(2\\) невозможен.</p>', '<p>Попросите учеников связать это с уравнениями вида \\(x^2=4m+2\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'remainders';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'squares';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'impossibility';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-023', 23, 2, 'computation', 23, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-023' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'A Prime Divisor', '<p>Let \\(p\\) be prime. If \\(p\\mid 35\\), find all possible values of \\(p\\).</p>', '<p>Factor \\(35\\).</p>', '<p>Since \\(35=5\\cdot7\\), the prime divisors of \\(35\\) are \\(5\\) and \\(7\\). Thus \\(p\\in\\{5,7\\}\\).</p>', '<p>Simple example of using prime factorisation to restrict possibilities.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Простой делитель', '<p>Пусть \\(p\\) простое число. Если \\(p\\mid 35\\), найдите все возможные значения \\(p\\).</p>', '<p>Разложите \\(35\\) на множители.</p>', '<p>Так как \\(35=5\\cdot7\\), простые делители числа \\(35\\) равны \\(5\\) и \\(7\\). Поэтому \\(p\\in\\{5,7\\}\\).</p>', '<p>Простой пример того, как разложение на простые множители ограничивает варианты.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisibility';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-024', 24, 3, 'challenge', 24, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-024' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Prime Triple', '<p>Find all primes \\(p\\) such that \\(p\\), \\(p+2\\), and \\(p+4\\) are all prime.</p>', '<p>Look at remainders modulo \\(3\\).</p>', '<p>If \\(p>3\\), then \\(p,p+2,p+4\\) are three numbers covering all remainders modulo \\(3\\), so one is divisible by \\(3\\). Since it is greater than \\(3\\), it cannot be prime. Checking \\(p=3\\), we get \\(3,5,7\\), all prime. Therefore \\(p=3\\).</p>', '<p>A classic olympiad-style use of remainders with primes.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Тройка простых чисел', '<p>Найдите все простые \\(p\\), для которых \\(p\\), \\(p+2\\) и \\(p+4\\) также простые.</p>', '<p>Посмотрите на остатки при делении на \\(3\\).</p>', '<p>Если \\(p>3\\), то числа \\(p,p+2,p+4\\) покрывают все остатки по модулю \\(3\\), значит одно из них делится на \\(3\\). Оно больше \\(3\\), поэтому не может быть простым. Проверяем \\(p=3\\): получаем \\(3,5,7\\), все простые. Следовательно, \\(p=3\\).</p>', '<p>Классический олимпиадный пример использования остатков для простых чисел.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'remainders';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-025', 25, 3, 'challenge', 25, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-025' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Factorial Exponent', '<p>Find the exponent of \\(5\\) in the prime factorisation of \\(50!\\).</p>', '<p>Count multiples of \\(5\\), then extra factors from multiples of \\(25\\).</p>', '<p>The exponent is \\(\\left\\lfloor50/5\\right\\rfloor+\\left\\lfloor50/25\\right\\rfloor=10+2=12\\).</p>', '<p>Introduce Legendre''s formula informally through counting.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Показатель простого в факториале', '<p>Найдите показатель степени \\(5\\) в разложении \\(50!\\) на простые множители.</p>', '<p>Посчитайте кратные \\(5\\), затем дополнительные множители из кратных \\(25\\).</p>', '<p>Показатель равен \\(\\left\\lfloor50/5\\right\\rfloor+\\left\\lfloor50/25\\right\\rfloor=10+2=12\\).</p>', '<p>Неформально введите формулу Лежандра через подсчет кратных.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorial';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'exponent';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-026', 26, 3, 'challenge', 26, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-026' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Trailing Zeros', '<p>How many zeros does \\(60!\\) end with?</p>', '<p>Each trailing zero needs one factor \\(10=2\\cdot5\\). Which prime is rarer?</p>', '<p>The number of trailing zeros is the exponent of \\(5\\) in \\(60!\\), since factors of \\(2\\) are more plentiful. It is \\(\\lfloor60/5\\rfloor+\\lfloor60/25\\rfloor=12+2=14\\).</p>', '<p>Make students explain why counting fives is enough.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Нули в конце факториала', '<p>Сколькими нулями оканчивается число \\(60!\\)?</p>', '<p>Каждый ноль требует множитель \\(10=2\\cdot5\\). Какой простой множитель встречается реже?</p>', '<p>Число нулей в конце равно показателю степени \\(5\\) в \\(60!\\), потому что двоек больше. Получаем \\(\\lfloor60/5\\rfloor+\\lfloor60/25\\rfloor=12+2=14\\).</p>', '<p>Попросите учеников объяснить, почему достаточно считать пятерки.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorial';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'trailing-zeros';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-027', 27, 2, 'counterexample', 27, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-027' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'A Divisibility Counterexample', '<p>Is this statement true: if \\(a\\mid bc\\), then \\(a\\mid b\\) or \\(a\\mid c\\)? Give proof or counterexample.</p>', '<p>Try a composite value of \\(a\\).</p>', '<p>The statement is false. Take \\(a=6\\), \\(b=2\\), \\(c=3\\). Then \\(6\\mid bc\\) because \\(bc=6\\), but \\(6\\nmid2\\) and \\(6\\nmid3\\).</p>', '<p>This prepares students for the special role of primes in Euclid''s lemma.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Контрпример к делимости', '<p>Верно ли утверждение: если \\(a\\mid bc\\), то \\(a\\mid b\\) или \\(a\\mid c\\)? Дайте доказательство или контрпример.</p>', '<p>Попробуйте составное значение \\(a\\).</p>', '<p>Утверждение ложно. Возьмем \\(a=6\\), \\(b=2\\), \\(c=3\\). Тогда \\(6\\mid bc\\), потому что \\(bc=6\\), но \\(6\\nmid2\\) и \\(6\\nmid3\\).</p>', '<p>Эта задача готовит учеников к особой роли простых чисел в лемме Евклида.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'counterexample';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-028', 28, 2, 'proof', 28, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-028' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Euclid''s Lemma Example', '<p>Let \\(p\\) be prime and \\(p\\mid ab\\). Explain why the conclusion \\(p\\mid a\\) or \\(p\\mid b\\) is reasonable using prime factorisation.</p>', '<p>A prime factor appearing in \\(ab\\) must come from one of the factors.</p>', '<p>In the prime factorisation of \\(ab\\), all prime factors come from the factorisations of \\(a\\) and \\(b\\). If the prime \\(p\\) appears in \\(ab\\), it must appear in \\(a\\) or in \\(b\\). Thus \\(p\\mid a\\) or \\(p\\mid b\\).</p>', '<p>Keep this intuitive here; a formal proof can come later.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Пример к лемме Евклида', '<p>Пусть \\(p\\) простое и \\(p\\mid ab\\). Объясните через разложение на простые множители, почему естественен вывод: \\(p\\mid a\\) или \\(p\\mid b\\).</p>', '<p>Простой множитель, который появился в \\(ab\\), должен прийти из одного из множителей.</p>', '<p>В разложении \\(ab\\) на простые множители все простые множители приходят из разложений \\(a\\) и \\(b\\). Если простой \\(p\\) встречается в \\(ab\\), он должен встречаться в \\(a\\) или в \\(b\\). Значит \\(p\\mid a\\) или \\(p\\mid b\\).</p>', '<p>Здесь достаточно интуитивного объяснения; формальное доказательство можно дать позже.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'euclid-lemma';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-029', 29, 3, 'challenge', 29, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-029' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Coprime Product', '<p>If \\(\\gcd(a,b)=1\\), \\(a\\mid n\\), and \\(b\\mid n\\), prove that \\(ab\\mid n\\).</p>', '<p>Use prime factorisation: coprime numbers share no prime factors.</p>', '<p>Since \\(a\\) and \\(b\\) share no prime factors, the prime powers required by \\(a\\) and by \\(b\\) are independent. If \\(n\\) is divisible by both, then \\(n\\) contains all prime powers from \\(a\\) and all from \\(b\\), so \\(ab\\mid n\\).</p>', '<p>This is a key theorem for combining divisibility conditions.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Произведение взаимно простых делителей', '<p>Если \\(\\operatorname{НОД}(a,b)=1\\), \\(a\\mid n\\) и \\(b\\mid n\\), докажите, что \\(ab\\mid n\\).</p>', '<p>Используйте разложение на простые множители: взаимно простые числа не имеют общих простых делителей.</p>', '<p>Так как \\(a\\) и \\(b\\) не имеют общих простых множителей, простые степени, нужные для \\(a\\), и простые степени, нужные для \\(b\\), независимы. Если \\(n\\) делится на оба числа, то \\(n\\) содержит все простые степени из \\(a\\) и все из \\(b\\), значит \\(ab\\mid n\\).</p>', '<p>Это ключевая теорема для объединения условий делимости.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'coprime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisibility';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-030', 30, 2, 'computation', 30, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-030' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Find the Missing Exponent', '<p>For \\(n=2^3\\cdot3^a\\), find all positive integers \\(a\\) such that \\(n\\) has \\(20\\) positive divisors.</p>', '<p>Use \\((3+1)(a+1)=20\\).</p>', '<p>\\(\\tau(n)=(3+1)(a+1)=4(a+1)\\). Set \\(4(a+1)=20\\), so \\(a+1=5\\) and \\(a=4\\).</p>', '<p>A clean algebraic use of the divisor formula.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Найти неизвестный показатель', '<p>Для \\(n=2^3\\cdot3^a\\) найдите все положительные целые \\(a\\), при которых \\(n\\) имеет \\(20\\) положительных делителей.</p>', '<p>Используйте \\((3+1)(a+1)=20\\).</p>', '<p>\\(\\tau(n)=(3+1)(a+1)=4(a+1)\\). Решаем \\(4(a+1)=20\\), откуда \\(a+1=5\\) и \\(a=4\\).</p>', '<p>Чистое алгебраическое применение формулы числа делителей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'exponents';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-031', 31, 2, 'computation', 31, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-031' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Shared Divisors', '<p>How many positive common divisors do \\(84\\) and \\(126\\) have?</p>', '<p>Common divisors are exactly the divisors of the \\(\\gcd\\).</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), so \\(\\gcd(84,126)=2\\cdot3\\cdot7=42\\). Since \\(42=2\\cdot3\\cdot7\\), it has \\((1+1)^3=8\\) positive divisors.</p>', '<p>This links GCD to a counting question.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Общие делители', '<p>Сколько положительных общих делителей имеют числа \\(84\\) и \\(126\\)?</p>', '<p>Общие делители двух чисел — это ровно делители их \\(\\operatorname{НОД}\\).</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), значит \\(\\operatorname{НОД}(84,126)=2\\cdot3\\cdot7=42\\). Так как \\(42=2\\cdot3\\cdot7\\), у него \\((1+1)^3=8\\) положительных делителей.</p>', '<p>Эта задача связывает НОД с задачей на подсчет.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-032', 32, 1, 'computation', 32, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-032' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'All Divisors from Exponents', '<p>List all positive divisors of \\(2^2\\cdot3\\).</p>', '<p>Choose the exponent of \\(2\\) from \\(0,1,2\\) and of \\(3\\) from \\(0,1\\).</p>', '<p>The number is \\(12\\). Its positive divisors are \\(1,2,3,4,6,12\\).</p>', '<p>Have students build a small exponent table.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Все делители через показатели', '<p>Перечислите все положительные делители числа \\(2^2\\cdot3\\).</p>', '<p>Выберите показатель у \\(2\\) из \\(0,1,2\\), а показатель у \\(3\\) из \\(0,1\\).</p>', '<p>Число равно \\(12\\). Его положительные делители: \\(1,2,3,4,6,12\\).</p>', '<p>Пусть ученики построят маленькую таблицу показателей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'listing';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-033', 33, 1, 'computation', 33, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-033' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Divisibility by Nine', '<p>Use the digit-sum test to decide whether \\(738\\) is divisible by \\(9\\).</p>', '<p>Add the digits.</p>', '<p>The digit sum is \\(7+3+8=18\\), and \\(9\\mid18\\). Therefore \\(9\\mid738\\).</p>', '<p>Later this can be proved with remainders modulo \\(9\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Признак делимости на девять', '<p>Используйте сумму цифр, чтобы определить, делится ли \\(738\\) на \\(9\\).</p>', '<p>Сложите цифры числа.</p>', '<p>Сумма цифр равна \\(7+3+8=18\\), а \\(9\\mid18\\). Следовательно, \\(9\\mid738\\).</p>', '<p>Позже этот признак можно доказать через остатки по модулю \\(9\\).</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisibility-test';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'digits';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-034', 34, 2, 'computation', 34, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-034' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Make It Divisible', '<p>Find all digits \\(x\\) such that the number \\(45x2\\) is divisible by \\(3\\).</p>', '<p>A number is divisible by \\(3\\) when its digit sum is divisible by \\(3\\).</p>', '<p>The digit sum is \\(4+5+x+2=11+x\\). We need \\(11+x\\equiv0\\pmod3\\). Since \\(11\\equiv2\\pmod3\\), \\(x\\equiv1\\pmod3\\). The possible digits are \\(1,4,7\\).</p>', '<p>Good entry point to using congruence language informally.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Сделать число делящимся', '<p>Найдите все цифры \\(x\\), при которых число \\(45x2\\) делится на \\(3\\).</p>', '<p>Число делится на \\(3\\), если сумма его цифр делится на \\(3\\).</p>', '<p>Сумма цифр равна \\(4+5+x+2=11+x\\). Нужно \\(11+x\\equiv0\\pmod3\\). Так как \\(11\\equiv2\\pmod3\\), получаем \\(x\\equiv1\\pmod3\\). Возможные цифры: \\(1,4,7\\).</p>', '<p>Хороший вход в неформальное использование сравнений.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'digits';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisibility-test';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-035', 35, 3, 'challenge', 35, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-035' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Even Divisors', '<p>How many positive even divisors does \\(720\\) have?</p>', '<p>First factor \\(720\\). For an even divisor, the exponent of \\(2\\) must be at least \\(1\\).</p>', '<p>\\(720=2^4\\cdot3^2\\cdot5\\). An even divisor has exponent of \\(2\\) equal to \\(1,2,3,\\) or \\(4\\): \\(4\\) choices. The exponent of \\(3\\) has \\(3\\) choices and of \\(5\\) has \\(2\\) choices. Total: \\(4\\cdot3\\cdot2=24\\).</p>', '<p>This deepens divisor counting beyond the standard formula.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Четные делители', '<p>Сколько положительных четных делителей имеет число \\(720\\)?</p>', '<p>Сначала разложите \\(720\\). Для четного делителя показатель степени \\(2\\) должен быть хотя бы \\(1\\).</p>', '<p>\\(720=2^4\\cdot3^2\\cdot5\\). У четного делителя показатель у \\(2\\) может быть \\(1,2,3\\) или \\(4\\): \\(4\\) варианта. У показателя \\(3\\) есть \\(3\\) варианта, у показателя \\(5\\) — \\(2\\) варианта. Всего \\(4\\cdot3\\cdot2=24\\).</p>', '<p>Эта задача углубляет подсчет делителей за пределы стандартной формулы.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'counting';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-036', 36, 2, 'computation', 36, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-036' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Odd Divisors', '<p>How many positive odd divisors does \\(720\\) have?</p>', '<p>Odd divisors use no factor \\(2\\).</p>', '<p>Since \\(720=2^4\\cdot3^2\\cdot5\\), an odd divisor must use \\(2^0\\). Then the exponent of \\(3\\) has \\(3\\) choices and the exponent of \\(5\\) has \\(2\\) choices. There are \\(3\\cdot2=6\\) positive odd divisors.</p>', '<p>Pair this with even-divisor counting.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Нечетные делители', '<p>Сколько положительных нечетных делителей имеет число \\(720\\)?</p>', '<p>Нечетные делители не используют множитель \\(2\\).</p>', '<p>Так как \\(720=2^4\\cdot3^2\\cdot5\\), нечетный делитель должен содержать \\(2^0\\). Тогда у показателя \\(3\\) есть \\(3\\) варианта, а у показателя \\(5\\) — \\(2\\) варианта. Всего \\(3\\cdot2=6\\) положительных нечетных делителей.</p>', '<p>Дайте эту задачу вместе с подсчетом четных делителей.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'divisors';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'counting';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-037', 37, 2, 'proof', 37, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-037' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'A Divisibility Chain', '<p>Prove that if \\(a\\mid b\\) and \\(b\\mid c\\), then \\(a\\mid c\\).</p>', '<p>Write \\(b=ak\\) and \\(c=bm\\).</p>', '<p>If \\(a\\mid b\\), then \\(b=ak\\). If \\(b\\mid c\\), then \\(c=bm\\). Therefore \\(c=akm=a(km)\\), so \\(a\\mid c\\).</p>', '<p>Students should see divisibility as a relation with structure.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Цепочка делимости', '<p>Докажите, что если \\(a\\mid b\\) и \\(b\\mid c\\), то \\(a\\mid c\\).</p>', '<p>Запишите \\(b=ak\\) и \\(c=bm\\).</p>', '<p>Если \\(a\\mid b\\), то \\(b=ak\\). Если \\(b\\mid c\\), то \\(c=bm\\). Следовательно, \\(c=akm=a(km)\\), значит \\(a\\mid c\\).</p>', '<p>Ученики должны видеть делимость как отношение со своей структурой.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'transitivity';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-038', 38, 3, 'challenge', 38, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-038' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Mutual Divisibility', '<p>Let \\(a\\) and \\(b\\) be positive integers. Prove that if \\(a\\mid b\\) and \\(b\\mid a\\), then \\(a=b\\).</p>', '<p>Use \\(b=ak\\) and compare sizes.</p>', '<p>Since \\(a\\mid b\\), write \\(b=ak\\) for a positive integer \\(k\\). Since \\(b\\mid a\\), write \\(a=bm\\) for a positive integer \\(m\\). Then \\(a=akm\\). Because \\(a>0\\), we get \\(km=1\\), so \\(k=m=1\\), and \\(a=b\\).</p>', '<p>For integer versions, discuss signs separately.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Взаимная делимость', '<p>Пусть \\(a\\) и \\(b\\) — положительные целые числа. Докажите: если \\(a\\mid b\\) и \\(b\\mid a\\), то \\(a=b\\).</p>', '<p>Используйте \\(b=ak\\) и сравните размеры чисел.</p>', '<p>Так как \\(a\\mid b\\), запишем \\(b=ak\\), где \\(k\\) — положительное целое число. Так как \\(b\\mid a\\), запишем \\(a=bm\\), где \\(m\\) — положительное целое число. Тогда \\(a=akm\\). Поскольку \\(a>0\\), получаем \\(km=1\\), значит \\(k=m=1\\), и \\(a=b\\).</p>', '<p>Для версии с произвольными целыми числами отдельно обсудите знаки.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'absolute-value';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-039', 39, 2, 'computation', 39, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-039' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Find the GCD and LCM', '<p>Find \\(\\gcd(210,330)\\) and \\(\\operatorname{lcm}(210,330)\\).</p>', '<p>Factor both numbers first.</p>', '<p>\\(210=2\\cdot3\\cdot5\\cdot7\\), and \\(330=2\\cdot3\\cdot5\\cdot11\\). Therefore \\(\\gcd=2\\cdot3\\cdot5=30\\), and \\(\\operatorname{lcm}=2\\cdot3\\cdot5\\cdot7\\cdot11=2310\\).</p>', '<p>This example makes shared and unshared prime factors visible.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Найти НОД и НОК', '<p>Найдите \\(\\operatorname{НОД}(210,330)\\) и \\(\\operatorname{НОК}(210,330)\\).</p>', '<p>Сначала разложите оба числа на простые множители.</p>', '<p>\\(210=2\\cdot3\\cdot5\\cdot7\\), а \\(330=2\\cdot3\\cdot5\\cdot11\\). Поэтому \\(\\operatorname{НОД}=2\\cdot3\\cdot5=30\\), а \\(\\operatorname{НОК}=2\\cdot3\\cdot5\\cdot7\\cdot11=2310\\).</p>', '<p>Этот пример хорошо показывает общие и необщие простые множители.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-01-040', 40, 3, 'challenge', 40, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), book_number = VALUES(book_number), difficulty = VALUES(difficulty), problem_type = VALUES(problem_type), sort_order = VALUES(sort_order), is_published = VALUES(is_published), updated_at = NOW();
SET @problem_id := (SELECT id FROM problems WHERE problem_code = 'NT-01-040' LIMIT 1);
DELETE FROM problem_tags WHERE problem_id = @problem_id;
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Olympiad Warm-Up', '<p>Prove that \\(24\\mid n(n^2-1)(n+2)\\) for every integer \\(n\\).</p>', '<p>Factor the expression into four consecutive integers.</p>', '<p>We have \\(n(n^2-1)(n+2)=n(n-1)(n+1)(n+2)\\), the product of four consecutive integers. Among four consecutive integers there is a multiple of \\(4\\), another even number, and at least one multiple of \\(3\\). Thus the product is divisible by \\(8\\cdot3=24\\).</p>', '<p>Check the divisibility by \\(8\\) carefully: four consecutive integers contain a multiple of \\(4\\) and another even number.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Олимпиадная разминка', '<p>Докажите, что \\(24\\mid n(n^2-1)(n+2)\\) для любого целого \\(n\\).</p>', '<p>Разложите выражение в произведение четырех последовательных целых чисел.</p>', '<p>Имеем \\(n(n^2-1)(n+2)=n(n-1)(n+1)(n+2)\\), то есть произведение четырех последовательных целых чисел. Среди них есть число, кратное \\(4\\), еще одно четное число и хотя бы одно число, кратное \\(3\\). Поэтому произведение делится на \\(8\\cdot3=24\\).</p>', '<p>Аккуратно проверьте делимость на \\(8\\): среди четырех последовательных чисел есть кратное \\(4\\) и еще одно четное число.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title), statement_html = VALUES(statement_html), hint_html = VALUES(hint_html), solution_html = VALUES(solution_html), teacher_note_html = VALUES(teacher_note_html);
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'factorisation';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'consecutive-integers';

SET FOREIGN_KEY_CHECKS = 1;
