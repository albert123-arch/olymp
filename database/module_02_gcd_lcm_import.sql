SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SET @course_id := (SELECT id FROM courses WHERE slug = 'number-theory' LIMIT 1);
INSERT INTO chapters (course_id, slug, sort_order, is_published, created_at, updated_at)
SELECT @course_id, 'gcd-lcm-euclidean-algorithm', 2, 1, NOW(), NOW()
WHERE @course_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM chapters WHERE course_id = @course_id AND slug = 'gcd-lcm-euclidean-algorithm');
SET @chapter_id := (SELECT id FROM chapters WHERE course_id = @course_id AND slug = 'gcd-lcm-euclidean-algorithm' LIMIT 1);
UPDATE chapters SET sort_order = 2, is_published = 1, updated_at = NOW() WHERE id = @chapter_id;

INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES
(@chapter_id, 'en', 'GCD, LCM and the Euclidean Algorithm', '<p>GCD, LCM, prime factorisation, the product formula, and the Euclidean algorithm.</p>', '<h3>1. Greatest Common Divisor</h3>
<p>For positive integers \\(a\\) and \\(b\\), the greatest common divisor is the largest positive integer that divides both numbers. It is written</p>
<p>\\[
\\gcd(a,b).
\\]</p>
<p>For example, the positive divisors of \\(18\\) are \\(1,2,3,6,9,18\\), and the positive divisors of \\(30\\) are \\(1,2,3,5,6,10,15,30\\). The common divisors are \\(1,2,3,6\\), so</p>
<p>\\[
\\gcd(18,30)=6.
\\]</p>
<h3>2. Least Common Multiple</h3>
<p>The least common multiple of positive integers \\(a\\) and \\(b\\) is the smallest positive integer divisible by both numbers. It is written</p>
<p>\\[
\\operatorname{lcm}(a,b).
\\]</p>
<p>For example,</p>
<p>\\[
\\operatorname{lcm}(12,18)=36.
\\]</p>
<h3>3. Prime Factorisation Method</h3>
<p>If</p>
<p>\\[
a=\\prod p^{\\alpha_p},\\qquad b=\\prod p^{\\beta_p},
\\]</p>
<p>then</p>
<p>\\[
\\gcd(a,b)=\\prod p^{\\min(\\alpha_p,\\beta_p)},\\qquad
\\operatorname{lcm}(a,b)=\\prod p^{\\max(\\alpha_p,\\beta_p)}.
\\]</p>
<p>The GCD takes the common prime powers. The LCM takes all prime powers needed to cover both numbers.</p>
<h3>4. Product Formula</h3>
<p>For positive integers \\(a\\) and \\(b\\),</p>
<p>\\[
ab=\\gcd(a,b)\\operatorname{lcm}(a,b).
\\]</p>
<p>This identity is often the fastest way to find the LCM after finding the GCD.</p>
<h3>5. Euclidean Algorithm</h3>
<p>The Euclidean algorithm is based on the fact that</p>
<p>\\[
\\gcd(a,b)=\\gcd(b,r),
\\]</p>
<p>where \\(a=bq+r\\) and \\(0\\le r<b\\). Repeatedly replace the larger number by the remainder until the remainder is \\(0\\). The last non-zero remainder is the GCD.</p>
<p>Example:</p>
<p>\\[
252=198\\cdot1+54,
\\]</p>
<p>\\[
198=54\\cdot3+36,
\\]</p>
<p>\\[
54=36\\cdot1+18,
\\]</p>
<p>\\[
36=18\\cdot2+0.
\\]</p>
<p>Therefore \\(\\gcd(252,198)=18\\).</p>
<h3>6. Bezout Form</h3>
<p>Running the Euclidean algorithm backwards can express the GCD as a linear combination:</p>
<p>\\[
\\gcd(a,b)=ax+by
\\]</p>
<p>for some integers \\(x\\) and \\(y\\). This is called Bezout''s identity. It becomes very important in Diophantine equations and modular arithmetic.</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), so \\(\\gcd(84,126)=2\\cdot3\\cdot7=42\\).</p>
<ol>
<li>Find \\(\\gcd(84,126)\\).</li>
</ol>
<p>Use maximum exponents: \\(2^2\\cdot3^2\\cdot7=252\\).</p>
<ol>
<li>Find \\(\\operatorname{lcm}(84,126)\\).</li>
</ol>
<p>\\(252=198+54\\), \\(198=3\\cdot54+36\\), \\(54=36+18\\), \\(36=2\\cdot18\\). The GCD is \\(18\\).</p>
<ol>
<li>Find \\(\\gcd(252,198)\\) by Euclid''s algorithm.</li>
</ol>
<p>\\(\\gcd(48,180)=12\\), so \\(\\operatorname{lcm}(48,180)=48\\cdot180/12=720\\).</p>
<ol>
<li>Find \\(\\operatorname{lcm}(48,180)\\).</li>
</ol>
<p>Any common divisor divides \\((n+1)-n=1\\), so the GCD is \\(1\\).</p>
<ol>
<li>Show that \\(\\gcd(n,n+1)=1\\).</li>
</ol>
<p>Any common divisor divides \\(2\\). Hence the GCD is a positive divisor of \\(2\\).</p>
<ol>
<li>Show that \\(\\gcd(n,n+2)\\) is \\(1\\) or \\(2\\).</li>
</ol>
<p>\\(30=18+12\\), \\(18=12+6\\), so \\(6=18-12=18-(30-18)=2\\cdot18-30\\).</p>
<ol>
<li>Find integers \\(x,y\\) such that \\(30x+18y=6\\).</li>
</ol>
<p>\\(\\gcd(a,b(a+1))=\\gcd(a,b)\\) if \\(\\gcd(a,a+1)=1\\), so the answer is \\(1\\).</p>
<ol>
<li>If \\(\\gcd(a,b)=1\\), find \\(\\gcd(a,ab+b)\\).</li>
</ol>
<p>\\(\\gcd(2026,2024)=\\gcd(2024,2)=2\\).</p>
<ol>
<li>Find \\(\\gcd(2026,2024)\\).</li>
</ol>
<p>Since \\(d\\mid a\\) and \\(d\\mid b\\), it divides every integer linear combination.</p>
<ol>
<li>Prove that if \\(d=\\gcd(a,b)\\), then \\(d\\mid ax+by\\) for all integers \\(x,y\\).</li>
</ol>', '<ol>
<li>List all common positive divisors of \\(24\\) and \\(36\\). Find \\(\\gcd(24,36)\\).</li>
<li>Find \\(\\operatorname{lcm}(8,12)\\) by listing positive multiples.</li>
<li>Find \\(\\gcd(72,120)\\) using prime factorisation.</li>
<li>Find \\(\\operatorname{lcm}(72,120)\\) using prime factorisation.</li>
<li>Verify \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\) for \\(a=72\\), \\(b=120\\).</li>
<li>Use the Euclidean algorithm to find \\(\\gcd(252,198)\\).</li>
<li>Find \\(\\gcd(1001,429)\\).</li>
<li>Prove that \\(\\gcd(n,n+1)=1\\).</li>
<li>Prove that \\(\\gcd(n,n+2)\\) is either \\(1\\) or \\(2\\).</li>
<li>If \\(\\gcd(a,b)=18\\) and \\(ab=9720\\), find \\(\\operatorname{lcm}(a,b)\\).</li>
<li>If \\(\\gcd(a,b)=1\\), prove that \\(\\operatorname{lcm}(a,b)=ab\\).</li>
<li>Find integers \\(x,y\\) such that \\(30x+18y=6\\).</li>
<li>Find \\(\\gcd(2026,2025)\\).</li>
<li>Use the Euclidean algorithm to find \\(\\gcd(987,610)\\).</li>
<li>Prove that \\(\\gcd(2n+1,4n+3)=1\\).</li>
<li>Show that \\(\\gcd(n+3,n+9)\\) must divide \\(6\\).</li>
<li>Two bells ring every \\(12\\) minutes and every \\(18\\) minutes. When will they next ring together?</li>
<li>Ribbons of length \\(84\\) cm and \\(126\\) cm are cut into equal pieces with no waste. Find the greatest possible piece length.</li>
<li>Let \\(d=\\gcd(a,b)\\). Prove that \\(d\\mid 7a-5b\\).</li>
<li>Find all possible values of \\(\\gcd(n^2+n+1,n+1)\\).</li>
</ol>', '<h3>Lesson goals</h3>
<ul>
<li>Students can define \\(\\gcd(a,b)\\) and \\(\\operatorname{lcm}(a,b)\\).</li>
<li>Students can compute GCD and LCM using prime factorisation.</li>
<li>Students can use \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\).</li>
<li>Students can run the Euclidean algorithm.</li>
<li>Students can use the idea that common divisors divide differences.</li>
</ul>
<h3>Common mistakes</h3>
<ul>
<li>Confusing GCD with LCM.</li>
<li>Taking maximum exponents for GCD or minimum exponents for LCM.</li>
<li>Stopping the Euclidean algorithm one line too early.</li>
<li>Forgetting that \\(\\gcd(n,n+1)=1\\) follows from the difference \\(1\\).</li>
<li>Treating \\(\\operatorname{lcm}(a,b)=ab\\) as always true instead of only guaranteed when \\(\\gcd(a,b)=1\\).</li>
</ul>
<h3>Suggested lesson flow</h3>
<ol>
<li>Start with lists of divisors and multiples.</li>
<li>Move to prime factorisations and exponent rules.</li>
<li>Derive the product formula using exponents.</li>
<li>Teach the Euclidean algorithm with two or three numerical examples.</li>
<li>Use expression problems such as \\(\\gcd(2n+1,4n+3)\\).</li>
<li>Finish with one olympiad-style warm-up.</li>
</ol>
<h3>Extension questions</h3>
<ul>
<li>Find all possible values of \\(\\gcd(n+4,n+10)\\).</li>
<li>Prove that \\(\\gcd(3n+2,5n+3)\\) divides \\(1\\).</li>
<li>Find integers \\(x,y\\) such that \\(99x+78y=\\gcd(99,78)\\).</li>
<li>Explain why consecutive Fibonacci numbers make the Euclidean algorithm take many steps.</li>
</ul>')
ON DUPLICATE KEY UPDATE title = VALUES(title), description_html = VALUES(description_html), theory_html = VALUES(theory_html), examples_html = VALUES(examples_html), worksheet_html = VALUES(worksheet_html), teacher_notes_html = VALUES(teacher_notes_html);

INSERT INTO chapter_texts (chapter_id, lang, title, description_html, theory_html, examples_html, worksheet_html, teacher_notes_html) VALUES
(@chapter_id, 'ru', 'НОД, НОК и алгоритм Евклида', '<p>НОД, НОК, разложение на простые множители, формула произведения и алгоритм Евклида.</p>', '<h3>1. Наибольший общий делитель</h3>
<p>Для положительных целых чисел \\(a\\) и \\(b\\) наибольший общий делитель - это наибольшее положительное число, которое делит и \\(a\\), и \\(b\\). Обозначение:</p>
<p>\\[
\\operatorname{НОД}(a,b).
\\]</p>
<p>Например, общие положительные делители чисел \\(18\\) и \\(30\\): \\(1,2,3,6\\). Поэтому</p>
<p>\\[
\\operatorname{НОД}(18,30)=6.
\\]</p>
<h3>2. Наименьшее общее кратное</h3>
<p>Наименьшее общее кратное положительных целых чисел \\(a\\) и \\(b\\) - это наименьшее положительное число, которое делится и на \\(a\\), и на \\(b\\). Обозначение:</p>
<p>\\[
\\operatorname{НОК}(a,b).
\\]</p>
<p>Например,</p>
<p>\\[
\\operatorname{НОК}(12,18)=36.
\\]</p>
<h3>3. Метод разложения на простые множители</h3>
<p>Если</p>
<p>\\[
a=\\prod p^{\\alpha_p},\\qquad b=\\prod p^{\\beta_p},
\\]</p>
<p>то</p>
<p>\\[
\\operatorname{НОД}(a,b)=\\prod p^{\\min(\\alpha_p,\\beta_p)},\\qquad
\\operatorname{НОК}(a,b)=\\prod p^{\\max(\\alpha_p,\\beta_p)}.
\\]</p>
<p>НОД берет общие простые степени. НОК берет все простые степени, которые нужны, чтобы число делилось и на \\(a\\), и на \\(b\\).</p>
<h3>4. Формула произведения</h3>
<p>Для положительных целых чисел \\(a\\) и \\(b\\):</p>
<p>\\[
ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b).
\\]</p>
<p>Эта формула часто позволяет быстро найти НОК, если НОД уже известен.</p>
<h3>5. Алгоритм Евклида</h3>
<p>Алгоритм Евклида основан на факте:</p>
<p>\\[
\\operatorname{НОД}(a,b)=\\operatorname{НОД}(b,r),
\\]</p>
<p>где \\(a=bq+r\\) и \\(0\\le r<b\\). Мы заменяем пару чисел на меньшую пару, пока остаток не станет равен \\(0\\). Последний ненулевой остаток и есть НОД.</p>
<p>Пример:</p>
<p>\\[
252=198\\cdot1+54,
\\]</p>
<p>\\[
198=54\\cdot3+36,
\\]</p>
<p>\\[
54=36\\cdot1+18,
\\]</p>
<p>\\[
36=18\\cdot2+0.
\\]</p>
<p>Значит, \\(\\operatorname{НОД}(252,198)=18\\).</p>
<h3>6. Линейное представление НОД</h3>
<p>Если пройти алгоритм Евклида назад, НОД можно представить как линейную комбинацию:</p>
<p>\\[
\\operatorname{НОД}(a,b)=ax+by
\\]</p>
<p>для некоторых целых \\(x\\) и \\(y\\). Это называется тождеством Безу. Позже оно понадобится в диофантовых уравнениях и сравнениях.</p>', '<p>\\(84=2^2\\cdot3\\cdot7\\), \\(126=2\\cdot3^2\\cdot7\\), значит \\(\\operatorname{НОД}(84,126)=42\\).</p>
<ol>
<li>Найдите \\(\\operatorname{НОД}(84,126)\\).</li>
</ol>
<p>Берем максимальные показатели: \\(2^2\\cdot3^2\\cdot7=252\\).</p>
<ol>
<li>Найдите \\(\\operatorname{НОК}(84,126)\\).</li>
</ol>
<p>\\(252=198+54\\), \\(198=3\\cdot54+36\\), \\(54=36+18\\), \\(36=2\\cdot18\\). НОД равен \\(18\\).</p>
<ol>
<li>Найдите \\(\\operatorname{НОД}(252,198)\\) алгоритмом Евклида.</li>
</ol>
<p>\\(\\operatorname{НОД}(48,180)=12\\), поэтому \\(\\operatorname{НОК}(48,180)=48\\cdot180/12=720\\).</p>
<ol>
<li>Найдите \\(\\operatorname{НОК}(48,180)\\).</li>
</ol>
<p>Любой общий делитель делит разность \\((n+1)-n=1\\), значит НОД равен \\(1\\).</p>
<ol>
<li>Докажите, что \\(\\operatorname{НОД}(n,n+1)=1\\).</li>
</ol>
<p>Любой общий делитель делит разность \\(2\\), значит он является положительным делителем \\(2\\).</p>
<ol>
<li>Докажите, что \\(\\operatorname{НОД}(n,n+2)\\) равен \\(1\\) или \\(2\\).</li>
</ol>
<p>\\(30=18+12\\), \\(18=12+6\\), поэтому \\(6=18-12=18-(30-18)=2\\cdot18-30\\).</p>
<ol>
<li>Найдите целые \\(x,y\\), для которых \\(30x+18y=6\\).</li>
</ol>
<p>Так как \\(ab+b=b(a+1)\\), а \\(a\\) взаимно просто с \\(b\\) и с \\(a+1\\), ответ равен \\(1\\).</p>
<ol>
<li>Если \\(\\operatorname{НОД}(a,b)=1\\), найдите \\(\\operatorname{НОД}(a,ab+b)\\).</li>
</ol>
<p>\\(\\operatorname{НОД}(2026,2024)=\\operatorname{НОД}(2024,2)=2\\).</p>
<ol>
<li>Найдите \\(\\operatorname{НОД}(2026,2024)\\).</li>
</ol>
<p>Так как \\(d\\mid a\\) и \\(d\\mid b\\), число \\(d\\) делит любую целую линейную комбинацию.</p>
<ol>
<li>Докажите: если \\(d=\\operatorname{НОД}(a,b)\\), то \\(d\\mid ax+by\\) для любых целых \\(x,y\\).</li>
</ol>', '<ol>
<li>Перечислите все общие положительные делители чисел \\(24\\) и \\(36\\). Найдите \\(\\operatorname{НОД}(24,36)\\).</li>
<li>Найдите \\(\\operatorname{НОК}(8,12)\\), выписав положительные кратные.</li>
<li>Найдите \\(\\operatorname{НОД}(72,120)\\), используя разложение на простые множители.</li>
<li>Найдите \\(\\operatorname{НОК}(72,120)\\), используя разложение на простые множители.</li>
<li>Проверьте \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\) для \\(a=72\\), \\(b=120\\).</li>
<li>Используйте алгоритм Евклида, чтобы найти \\(\\operatorname{НОД}(252,198)\\).</li>
<li>Найдите \\(\\operatorname{НОД}(1001,429)\\).</li>
<li>Докажите, что \\(\\operatorname{НОД}(n,n+1)=1\\).</li>
<li>Докажите, что \\(\\operatorname{НОД}(n,n+2)\\) равно либо \\(1\\), либо \\(2\\).</li>
<li>Если \\(\\operatorname{НОД}(a,b)=18\\) и \\(ab=9720\\), найдите \\(\\operatorname{НОК}(a,b)\\).</li>
<li>Если \\(\\operatorname{НОД}(a,b)=1\\), докажите, что \\(\\operatorname{НОК}(a,b)=ab\\).</li>
<li>Найдите целые \\(x,y\\), такие что \\(30x+18y=6\\).</li>
<li>Найдите \\(\\operatorname{НОД}(2026,2025)\\).</li>
<li>Используйте алгоритм Евклида, чтобы найти \\(\\operatorname{НОД}(987,610)\\).</li>
<li>Докажите, что \\(\\operatorname{НОД}(2n+1,4n+3)=1\\).</li>
<li>Докажите, что \\(\\operatorname{НОД}(n+3,n+9)\\) обязательно делит \\(6\\).</li>
<li>Два звонка звучат каждые \\(12\\) минут и каждые \\(18\\) минут. Когда они снова прозвучат вместе?</li>
<li>Ленты длиной \\(84\\) см и \\(126\\) см разрезают на равные куски без остатка. Найдите наибольшую возможную длину куска.</li>
<li>Пусть \\(d=\\operatorname{НОД}(a,b)\\). Докажите, что \\(d\\mid 7a-5b\\).</li>
<li>Найдите все возможные значения \\(\\operatorname{НОД}(n^2+n+1,n+1)\\).</li>
</ol>', '<h3>Цели урока</h3>
<ul>
<li>Ученики умеют определять \\(\\operatorname{НОД}(a,b)\\) и \\(\\operatorname{НОК}(a,b)\\).</li>
<li>Ученики умеют находить НОД и НОК через разложение на простые множители.</li>
<li>Ученики используют формулу \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\).</li>
<li>Ученики умеют применять алгоритм Евклида.</li>
<li>Ученики используют идею: общие делители делят разности.</li>
</ul>
<h3>Типичные ошибки</h3>
<ul>
<li>Путают НОД и НОК.</li>
<li>Для НОД берут максимальные показатели, а для НОК - минимальные.</li>
<li>Останавливают алгоритм Евклида на одну строку раньше.</li>
<li>Не замечают, что \\(\\operatorname{НОД}(n,n+1)=1\\) следует из разности \\(1\\).</li>
<li>Считают, что \\(\\operatorname{НОК}(a,b)=ab\\) всегда, хотя это гарантировано при \\(\\operatorname{НОД}(a,b)=1\\).</li>
</ul>
<h3>Рекомендуемый ход урока</h3>
<ol>
<li>Начать со списков делителей и кратных.</li>
<li>Перейти к разложению на простые множители и правилам показателей.</li>
<li>Вывести формулу произведения через показатели степеней.</li>
<li>Объяснить алгоритм Евклида на двух-трех числовых примерах.</li>
<li>Дать задачи с выражениями, например \\(\\operatorname{НОД}(2n+1,4n+3)\\).</li>
<li>Завершить одной олимпиадной разминкой.</li>
</ol>
<h3>Дополнительные вопросы</h3>
<ul>
<li>Найдите все возможные значения \\(\\operatorname{НОД}(n+4,n+10)\\).</li>
<li>Докажите, что \\(\\operatorname{НОД}(3n+2,5n+3)\\) делит \\(1\\).</li>
<li>Найдите целые \\(x,y\\), такие что \\(99x+78y=\\operatorname{НОД}(99,78)\\).</li>
<li>Объясните, почему соседние числа Фибоначчи дают много шагов в алгоритме Евклида.</li>
</ul>')
ON DUPLICATE KEY UPDATE title = VALUES(title), description_html = VALUES(description_html), theory_html = VALUES(theory_html), examples_html = VALUES(examples_html), worksheet_html = VALUES(worksheet_html), teacher_notes_html = VALUES(teacher_notes_html);

DELETE pt FROM problem_texts pt JOIN problems p ON p.id = pt.problem_id WHERE p.problem_code LIKE 'NT-02-%';
DELETE ptag FROM problem_tags ptag JOIN problems p ON p.id = ptag.problem_id WHERE p.problem_code LIKE 'NT-02-%';
DELETE FROM problems WHERE problem_code LIKE 'NT-02-%';

INSERT INTO tags (slug, created_at) SELECT 'bezout', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'bezout');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'bezout' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Bezout Identity') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Тождество Безу') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'coprime', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'coprime');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'coprime' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Coprime Numbers') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Взаимно простые числа') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'definition', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'definition');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'definition' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Definition') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Определение') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'difference', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'difference');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'difference' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Difference') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Разность') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'euclidean-algorithm', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'euclidean-algorithm');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'euclidean-algorithm' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Euclidean Algorithm') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Алгоритм Евклида') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'gcd', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'gcd');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'gcd' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'GCD') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'НОД') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'identity', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'identity');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'identity' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Identity') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Тождество') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'lcm', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'lcm');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'lcm' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'LCM') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'НОК') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'linear-combination', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'linear-combination');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'linear-combination' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Linear Combination') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Линейная комбинация') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'olympiad', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'olympiad');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'olympiad' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Olympiad') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Олимпиада') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'prime-factorisation', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'prime-factorisation');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'prime-factorisation' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Prime Factorisation') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Разложение на простые множители') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'proof', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'proof');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'proof' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Proof') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Доказательство') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tags (slug, created_at) SELECT 'word-problem', NOW() WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'word-problem');
SET @tag_id := (SELECT id FROM tags WHERE slug = 'word-problem' LIMIT 1);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'en', 'Word Problem') ON DUPLICATE KEY UPDATE title = VALUES(title);
INSERT INTO tag_texts (tag_id, lang, title) VALUES (@tag_id, 'ru', 'Текстовая задача') ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-001', 1, 1, 'computation', 1, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Common Divisors', '<p>List all common positive divisors of \\(24\\) and \\(36\\), then find \\(\\gcd(24,36)\\).</p>', '<p>First list the divisors of each number and compare the two lists.</p>', '<p>The positive divisors of \\(24\\) are \\(1,2,3,4,6,8,12,24\\). The positive divisors of \\(36\\) are \\(1,2,3,4,6,9,12,18,36\\). The common divisors are \\(1,2,3,4,6,12\\), so \\(\\gcd(24,36)=12\\).</p>', '<p>Use this as a slow first problem to make the meaning of greatest common divisor concrete.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Общие делители', '<p>Перечислите все общие положительные делители чисел \\(24\\) и \\(36\\), затем найдите \\(\\operatorname{НОД}(24,36)\\).</p>', '<p>Сначала выпишите делители каждого числа и сравните два списка.</p>', '<p>Положительные делители \\(24\\): \\(1,2,3,4,6,8,12,24\\). Положительные делители \\(36\\): \\(1,2,3,4,6,9,12,18,36\\). Общие делители: \\(1,2,3,4,6,12\\), значит \\(\\operatorname{НОД}(24,36)=12\\).</p>', '<p>Используйте эту задачу как медленный первый пример, чтобы смысл НОД стал конкретным.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'definition';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-002', 2, 1, 'computation', 2, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'First LCM', '<p>Find \\(\\operatorname{lcm}(8,12)\\) by listing positive multiples.</p>', '<p>Write several multiples of \\(8\\) and several multiples of \\(12\\). Look for the first common one.</p>', '<p>Multiples of \\(8\\): \\(8,16,24,32,\\ldots\\). Multiples of \\(12\\): \\(12,24,36,\\ldots\\). The first common positive multiple is \\(24\\), so \\(\\operatorname{lcm}(8,12)=24\\).</p>', '<p>Contrast largest common divisor with smallest common multiple.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Первый НОК', '<p>Найдите \\(\\operatorname{НОК}(8,12)\\), выписав положительные кратные.</p>', '<p>Выпишите несколько кратных \\(8\\) и несколько кратных \\(12\\). Найдите первое общее.</p>', '<p>Кратные \\(8\\): \\(8,16,24,32,\\ldots\\). Кратные \\(12\\): \\(12,24,36,\\ldots\\). Первое общее положительное кратное равно \\(24\\), поэтому \\(\\operatorname{НОК}(8,12)=24\\).</p>', '<p>Сравните идею наибольшего общего делителя и наименьшего общего кратного.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'definition';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-003', 3, 1, 'computation', 3, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'GCD from Prime Powers', '<p>Find \\(\\gcd(72,120)\\) using prime factorisation.</p>', '<p>Use the smaller exponent of each prime.</p>', '<p>We have \\(72=2^3\\cdot3^2\\) and \\(120=2^3\\cdot3\\cdot5\\). The common prime powers are \\(2^3\\) and \\(3^1\\), so \\(\\gcd(72,120)=2^3\\cdot3=24\\).</p>', '<p>Ask students to say out loud: GCD means minimum exponents.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОД через простые степени', '<p>Найдите \\(\\operatorname{НОД}(72,120)\\), используя разложение на простые множители.</p>', '<p>Берите меньший показатель степени у каждого простого числа.</p>', '<p>Имеем \\(72=2^3\\cdot3^2\\) и \\(120=2^3\\cdot3\\cdot5\\). Общие простые степени: \\(2^3\\) и \\(3^1\\), поэтому \\(\\operatorname{НОД}(72,120)=2^3\\cdot3=24\\).</p>', '<p>Попросите учеников проговорить: НОД означает минимальные показатели степеней.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime-factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-004', 4, 1, 'computation', 4, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'LCM from Prime Powers', '<p>Find \\(\\operatorname{lcm}(72,120)\\) using prime factorisation.</p>', '<p>Use the larger exponent of each prime that appears.</p>', '<p>Since \\(72=2^3\\cdot3^2\\) and \\(120=2^3\\cdot3\\cdot5\\), the LCM is \\(2^3\\cdot3^2\\cdot5=360\\).</p>', '<p>Pair this directly after the previous problem.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОК через простые степени', '<p>Найдите \\(\\operatorname{НОК}(72,120)\\), используя разложение на простые множители.</p>', '<p>Берите больший показатель степени у каждого простого числа, которое встречается.</p>', '<p>Так как \\(72=2^3\\cdot3^2\\), а \\(120=2^3\\cdot3\\cdot5\\), получаем \\(\\operatorname{НОК}(72,120)=2^3\\cdot3^2\\cdot5=360\\).</p>', '<p>Дайте эту задачу сразу после предыдущей.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'prime-factorisation';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-005', 5, 1, 'computation', 5, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Product Formula Check', '<p>Verify that \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\) for \\(a=72\\), \\(b=120\\).</p>', '<p>Use your answers from the previous two problems.</p>', '<p>From the previous problems, \\(\\gcd(72,120)=24\\) and \\(\\operatorname{lcm}(72,120)=360\\). Then \\(24\\cdot360=8640\\), and \\(72\\cdot120=8640\\). The identity holds.</p>', '<p>This prepares the formula as a computational shortcut.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Проверка формулы произведения', '<p>Проверьте равенство \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\) для \\(a=72\\), \\(b=120\\).</p>', '<p>Используйте ответы из двух предыдущих задач.</p>', '<p>Из предыдущих задач \\(\\operatorname{НОД}(72,120)=24\\) и \\(\\operatorname{НОК}(72,120)=360\\). Тогда \\(24\\cdot360=8640\\), и \\(72\\cdot120=8640\\). Равенство выполнено.</p>', '<p>Эта задача готовит формулу как вычислительный инструмент.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'identity';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-006', 6, 1, 'computation', 6, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Euclid Algorithm I', '<p>Use the Euclidean algorithm to find \\(\\gcd(252,198)\\).</p>', '<p>Start with \\(252=198\\cdot1+54\\).</p>', '<p>\\(252=198+54\\), \\(198=54\\cdot3+36\\), \\(54=36+18\\), and \\(36=18\\cdot2+0\\). The last non-zero remainder is \\(18\\), so \\(\\gcd(252,198)=18\\).</p>', '<p>Emphasize that the last non-zero remainder is the answer.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Алгоритм Евклида I', '<p>Используйте алгоритм Евклида, чтобы найти \\(\\operatorname{НОД}(252,198)\\).</p>', '<p>Начните с равенства \\(252=198\\cdot1+54\\).</p>', '<p>\\(252=198+54\\), \\(198=54\\cdot3+36\\), \\(54=36+18\\), \\(36=18\\cdot2+0\\). Последний ненулевой остаток равен \\(18\\), значит \\(\\operatorname{НОД}(252,198)=18\\).</p>', '<p>Подчеркните, что ответом является последний ненулевой остаток.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'euclidean-algorithm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-007', 7, 2, 'computation', 7, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Euclid Algorithm II', '<p>Find \\(\\gcd(1001,429)\\) using the Euclidean algorithm.</p>', '<p>Divide \\(1001\\) by \\(429\\), then continue with the remainder.</p>', '<p>\\(1001=429\\cdot2+143\\), and \\(429=143\\cdot3+0\\). Hence \\(\\gcd(1001,429)=143\\).</p>', '<p>A short Euclidean algorithm example helps students build confidence.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Алгоритм Евклида II', '<p>Найдите \\(\\operatorname{НОД}(1001,429)\\), используя алгоритм Евклида.</p>', '<p>Разделите \\(1001\\) на \\(429\\), затем продолжайте с остатком.</p>', '<p>\\(1001=429\\cdot2+143\\), а \\(429=143\\cdot3+0\\). Следовательно, \\(\\operatorname{НОД}(1001,429)=143\\).</p>', '<p>Короткий пример алгоритма Евклида помогает ученикам почувствовать уверенность.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'euclidean-algorithm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-008', 8, 1, 'proof', 8, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Consecutive Integers', '<p>Prove that \\(\\gcd(n,n+1)=1\\) for every integer \\(n\\).</p>', '<p>Any common divisor must divide the difference.</p>', '<p>If \\(d\\mid n\\) and \\(d\\mid n+1\\), then \\(d\\mid (n+1)-n=1\\). Thus \\(d=1\\), so \\(\\gcd(n,n+1)=1\\).</p>', '<p>This is a key olympiad habit: common divisors divide differences.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Последовательные целые числа', '<p>Докажите, что \\(\\operatorname{НОД}(n,n+1)=1\\) для любого целого \\(n\\).</p>', '<p>Любой общий делитель должен делить разность чисел.</p>', '<p>Если \\(d\\mid n\\) и \\(d\\mid n+1\\), то \\(d\\mid (n+1)-n=1\\). Значит, \\(d=1\\), поэтому \\(\\operatorname{НОД}(n,n+1)=1\\).</p>', '<p>Это важная олимпиадная привычка: общие делители делят разности.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'coprime';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-009', 9, 2, 'proof', 9, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Two Apart', '<p>Prove that \\(\\gcd(n,n+2)\\) is either \\(1\\) or \\(2\\).</p>', '<p>A common divisor of \\(n\\) and \\(n+2\\) divides \\((n+2)-n\\).</p>', '<p>Let \\(d=\\gcd(n,n+2)\\). Then \\(d\\mid n\\) and \\(d\\mid n+2\\), so \\(d\\mid2\\). Hence \\(d\\in\\{1,2\\}\\).</p>', '<p>Ask students when each case occurs: odd \\(n\\) gives \\(1\\), even \\(n\\) gives \\(2\\).</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Числа через одно', '<p>Докажите, что \\(\\operatorname{НОД}(n,n+2)\\) равно либо \\(1\\), либо \\(2\\).</p>', '<p>Общий делитель чисел \\(n\\) и \\(n+2\\) делит \\((n+2)-n\\).</p>', '<p>Пусть \\(d=\\operatorname{НОД}(n,n+2)\\). Тогда \\(d\\mid n\\) и \\(d\\mid n+2\\), значит \\(d\\mid2\\). Поэтому \\(d\\in\\{1,2\\}\\).</p>', '<p>Спросите, когда возникает каждый случай: при нечетном \\(n\\) получаем \\(1\\), при четном \\(n\\) получаем \\(2\\).</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'difference';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-010', 10, 1, 'computation', 10, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Find LCM from GCD', '<p>If \\(\\gcd(a,b)=18\\) and \\(ab=9720\\), find \\(\\operatorname{lcm}(a,b)\\).</p>', '<p>Use \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\).</p>', '<p>We have \\(9720=18\\operatorname{lcm}(a,b)\\), so \\(\\operatorname{lcm}(a,b)=9720/18=540\\).</p>', '<p>This is a clean numerical use of the product formula.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Найти НОК через НОД', '<p>Если \\(\\operatorname{НОД}(a,b)=18\\) и \\(ab=9720\\), найдите \\(\\operatorname{НОК}(a,b)\\).</p>', '<p>Используйте формулу \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\).</p>', '<p>Имеем \\(9720=18\\operatorname{НОК}(a,b)\\), поэтому \\(\\operatorname{НОК}(a,b)=9720/18=540\\).</p>', '<p>Это простой числовой пример применения формулы произведения.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'identity';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-011', 11, 2, 'computation', 11, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Coprime Product', '<p>If \\(\\gcd(a,b)=1\\), prove that \\(\\operatorname{lcm}(a,b)=ab\\).</p>', '<p>Use the formula \\(ab=\\gcd(a,b)\\operatorname{lcm}(a,b)\\).</p>', '<p>Since \\(\\gcd(a,b)=1\\), the identity gives \\(ab=1\\cdot\\operatorname{lcm}(a,b)\\). Therefore \\(\\operatorname{lcm}(a,b)=ab\\).</p>', '<p>Connect this to the idea of no shared prime factors.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Произведение взаимно простых чисел', '<p>Если \\(\\operatorname{НОД}(a,b)=1\\), докажите, что \\(\\operatorname{НОК}(a,b)=ab\\).</p>', '<p>Используйте формулу \\(ab=\\operatorname{НОД}(a,b)\\operatorname{НОК}(a,b)\\).</p>', '<p>Так как \\(\\operatorname{НОД}(a,b)=1\\), формула дает \\(ab=1\\cdot\\operatorname{НОК}(a,b)\\). Следовательно, \\(\\operatorname{НОК}(a,b)=ab\\).</p>', '<p>Свяжите это с идеей отсутствия общих простых множителей.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'coprime';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-012', 12, 2, 'computation', 12, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Bezout Backwards', '<p>Find integers \\(x\\) and \\(y\\) such that \\(30x+18y=6\\).</p>', '<p>Use \\(30=18+12\\) and \\(18=12+6\\), then go backwards.</p>', '<p>From \\(30=18+12\\), we get \\(12=30-18\\). From \\(18=12+6\\), we get \\(6=18-12\\). Therefore \\(6=18-(30-18)=2\\cdot18-30\\). Thus one answer is \\(x=-1\\), \\(y=2\\).</p>', '<p>This is the first gentle step toward linear Diophantine equations.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Безу в обратную сторону', '<p>Найдите целые числа \\(x\\) и \\(y\\), такие что \\(30x+18y=6\\).</p>', '<p>Используйте \\(30=18+12\\) и \\(18=12+6\\), затем идите назад.</p>', '<p>Из \\(30=18+12\\) получаем \\(12=30-18\\). Из \\(18=12+6\\) получаем \\(6=18-12\\). Поэтому \\(6=18-(30-18)=2\\cdot18-30\\). Один ответ: \\(x=-1\\), \\(y=2\\).</p>', '<p>Это первый мягкий шаг к линейным диофантовым уравнениям.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'bezout';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'euclidean-algorithm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-013', 13, 1, 'computation', 13, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'GCD of Large Neighbours', '<p>Find \\(\\gcd(2026,2025)\\).</p>', '<p>The numbers differ by \\(1\\).</p>', '<p>Any common divisor divides \\(2026-2025=1\\). Therefore \\(\\gcd(2026,2025)=1\\).</p>', '<p>Good mental exercise: no long computation is needed.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОД больших соседних чисел', '<p>Найдите \\(\\operatorname{НОД}(2026,2025)\\).</p>', '<p>Эти числа отличаются на \\(1\\).</p>', '<p>Любой общий делитель делит \\(2026-2025=1\\). Поэтому \\(\\operatorname{НОД}(2026,2025)=1\\).</p>', '<p>Хорошее устное упражнение: длинные вычисления не нужны.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'difference';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-014', 14, 2, 'computation', 14, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Euclid with Remainders', '<p>Use the Euclidean algorithm to find \\(\\gcd(987,610)\\).</p>', '<p>These numbers are consecutive Fibonacci numbers.</p>', '<p>\\(987=610+377\\), \\(610=377+233\\), \\(377=233+144\\), \\(233=144+89\\), \\(144=89+55\\), \\(89=55+34\\), \\(55=34+21\\), \\(34=21+13\\), \\(21=13+8\\), \\(13=8+5\\), \\(8=5+3\\), \\(5=3+2\\), \\(3=2+1\\), \\(2=2\\cdot1+0\\). The GCD is \\(1\\).</p>', '<p>This shows why Fibonacci pairs are slow for Euclid''s algorithm.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Евклид с остатками', '<p>Используйте алгоритм Евклида, чтобы найти \\(\\operatorname{НОД}(987,610)\\).</p>', '<p>Это соседние числа Фибоначчи.</p>', '<p>\\(987=610+377\\), \\(610=377+233\\), \\(377=233+144\\), \\(233=144+89\\), \\(144=89+55\\), \\(89=55+34\\), \\(55=34+21\\), \\(34=21+13\\), \\(21=13+8\\), \\(13=8+5\\), \\(8=5+3\\), \\(5=3+2\\), \\(3=2+1\\), \\(2=2\\cdot1+0\\). НОД равен \\(1\\).</p>', '<p>Эта задача показывает, почему пары чисел Фибоначчи медленно проходят алгоритм Евклида.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'euclidean-algorithm';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-015', 15, 2, 'proof', 15, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'GCD of Expressions', '<p>Prove that \\(\\gcd(2n+1,4n+3)=1\\) for every integer \\(n\\).</p>', '<p>Subtract twice the first expression from the second.</p>', '<p>Let \\(d\\) divide both \\(2n+1\\) and \\(4n+3\\). Then \\(d\\mid (4n+3)-2(2n+1)=1\\). Hence \\(d=1\\), so the GCD is \\(1\\).</p>', '<p>Excellent bridge from Euclid''s algorithm to expression manipulation.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'НОД выражений', '<p>Докажите, что \\(\\operatorname{НОД}(2n+1,4n+3)=1\\) для любого целого \\(n\\).</p>', '<p>Вычтите из второго выражения удвоенное первое.</p>', '<p>Пусть \\(d\\) делит и \\(2n+1\\), и \\(4n+3\\). Тогда \\(d\\mid (4n+3)-2(2n+1)=1\\). Значит, \\(d=1\\), поэтому НОД равен \\(1\\).</p>', '<p>Отличный мост от алгоритма Евклида к работе с выражениями.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'linear-combination';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-016', 16, 2, 'proof', 16, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Possible GCD', '<p>Show that \\(\\gcd(n+3,n+9)\\) must divide \\(6\\).</p>', '<p>Look at the difference between the two numbers.</p>', '<p>If \\(d=\\gcd(n+3,n+9)\\), then \\(d\\mid n+3\\) and \\(d\\mid n+9\\). Therefore \\(d\\mid (n+9)-(n+3)=6\\).</p>', '<p>Ask students which divisors of \\(6\\) can actually occur.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Возможный НОД', '<p>Докажите, что \\(\\operatorname{НОД}(n+3,n+9)\\) обязательно делит \\(6\\).</p>', '<p>Посмотрите на разность двух чисел.</p>', '<p>Если \\(d=\\operatorname{НОД}(n+3,n+9)\\), то \\(d\\mid n+3\\) и \\(d\\mid n+9\\). Следовательно, \\(d\\mid (n+9)-(n+3)=6\\).</p>', '<p>Спросите учеников, какие делители \\(6\\) действительно могут получиться.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'difference';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-017', 17, 1, 'mixed', 17, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'LCM Word Problem', '<p>Two bells ring every \\(12\\) minutes and every \\(18\\) minutes. If they ring together now, after how many minutes will they next ring together?</p>', '<p>This asks for the least common multiple of \\(12\\) and \\(18\\).</p>', '<p>The next common ringing time is \\(\\operatorname{lcm}(12,18)\\). Since \\(12=2^2\\cdot3\\) and \\(18=2\\cdot3^2\\), the LCM is \\(2^2\\cdot3^2=36\\). They will ring together after \\(36\\) minutes.</p>', '<p>Use this to distinguish LCM contexts from GCD contexts.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Текстовая задача на НОК', '<p>Два звонка звучат каждые \\(12\\) минут и каждые \\(18\\) минут. Если сейчас они прозвучали вместе, через сколько минут они снова прозвучат вместе?</p>', '<p>Здесь нужно найти наименьшее общее кратное чисел \\(12\\) и \\(18\\).</p>', '<p>Следующее общее время - это \\(\\operatorname{НОК}(12,18)\\). Так как \\(12=2^2\\cdot3\\), а \\(18=2\\cdot3^2\\), НОК равен \\(2^2\\cdot3^2=36\\). Звонки прозвучат вместе через \\(36\\) минут.</p>', '<p>Используйте задачу, чтобы отличать ситуации на НОК от ситуаций на НОД.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'lcm';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'word-problem';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-018', 18, 1, 'mixed', 18, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'GCD Word Problem', '<p>A ribbon of length \\(84\\) cm and a ribbon of length \\(126\\) cm are cut into equal pieces with no waste. What is the greatest possible length of each piece?</p>', '<p>The piece length must divide both \\(84\\) and \\(126\\).</p>', '<p>The greatest possible piece length is \\(\\gcd(84,126)\\). Since \\(84=2^2\\cdot3\\cdot7\\) and \\(126=2\\cdot3^2\\cdot7\\), the GCD is \\(2\\cdot3\\cdot7=42\\). The answer is \\(42\\) cm.</p>', '<p>Concrete cutting problems are usually GCD problems.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Текстовая задача на НОД', '<p>Ленты длиной \\(84\\) см и \\(126\\) см разрезают на равные куски без остатка. Какова наибольшая возможная длина каждого куска?</p>', '<p>Длина куска должна делить и \\(84\\), и \\(126\\).</p>', '<p>Наибольшая возможная длина куска равна \\(\\operatorname{НОД}(84,126)\\). Так как \\(84=2^2\\cdot3\\cdot7\\), а \\(126=2\\cdot3^2\\cdot7\\), НОД равен \\(2\\cdot3\\cdot7=42\\). Ответ: \\(42\\) см.</p>', '<p>Задачи на разрезание без остатка обычно являются задачами на НОД.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'word-problem';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-019', 19, 2, 'proof', 19, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Common Divisor of a Linear Combination', '<p>Let \\(d=\\gcd(a,b)\\). Prove that \\(d\\mid 7a-5b\\).</p>', '<p>Since \\(d\\mid a\\) and \\(d\\mid b\\), it divides integer combinations of \\(a\\) and \\(b\\).</p>', '<p>Because \\(d=\\gcd(a,b)\\), we have \\(d\\mid a\\) and \\(d\\mid b\\). Hence \\(d\\mid 7a\\) and \\(d\\mid 5b\\), so \\(d\\mid 7a-5b\\).</p>', '<p>This proof pattern will reappear in modular arithmetic.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Общий делитель линейной комбинации', '<p>Пусть \\(d=\\operatorname{НОД}(a,b)\\). Докажите, что \\(d\\mid 7a-5b\\).</p>', '<p>Так как \\(d\\mid a\\) и \\(d\\mid b\\), число \\(d\\) делит целые комбинации \\(a\\) и \\(b\\).</p>', '<p>Так как \\(d=\\operatorname{НОД}(a,b)\\), имеем \\(d\\mid a\\) и \\(d\\mid b\\). Значит, \\(d\\mid 7a\\) и \\(d\\mid 5b\\), поэтому \\(d\\mid 7a-5b\\).</p>', '<p>Этот шаблон доказательства снова появится в модульной арифметике.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'linear-combination';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';

INSERT INTO problems (chapter_id, problem_code, book_number, difficulty, problem_type, sort_order, is_published, created_at, updated_at) VALUES
(@chapter_id, 'NT-02-020', 20, 3, 'challenge', 20, 1, NOW(), NOW());
SET @problem_id := LAST_INSERT_ID();
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'en', 'Olympiad Warm-Up', '<p>Find all possible values of \\(\\gcd(n^2+n+1,n+1)\\) as \\(n\\) ranges over the integers.</p>', '<p>Reduce \\(n^2+n+1\\) using \\(n+1\\). Since \\(n\\equiv -1\\pmod{n+1}\\), what remains?</p>', '<p>Let \\(d=\\gcd(n^2+n+1,n+1)\\). Since \\(n\\equiv -1\\pmod{n+1}\\), we have \\(n^2+n+1\\equiv 1-1+1=1\\pmod{n+1}\\). Thus any common divisor divides \\(1\\), so the only possible value is \\(1\\).</p>', '<p>This is a compact preview of congruence thinking without requiring a full modular arithmetic lesson.</p>');
INSERT INTO problem_texts (problem_id, lang, title, statement_html, hint_html, solution_html, teacher_note_html) VALUES
(@problem_id, 'ru', 'Олимпиадная разминка', '<p>Найдите все возможные значения \\(\\operatorname{НОД}(n^2+n+1,n+1)\\), когда \\(n\\) пробегает целые числа.</p>', '<p>Упростите \\(n^2+n+1\\) по модулю \\(n+1\\). Если \\(n\\equiv -1\\pmod{n+1}\\), что остается?</p>', '<p>Пусть \\(d=\\operatorname{НОД}(n^2+n+1,n+1)\\). Так как \\(n\\equiv -1\\pmod{n+1}\\), получаем \\(n^2+n+1\\equiv 1-1+1=1\\pmod{n+1}\\). Значит, любой общий делитель делит \\(1\\), поэтому единственное возможное значение - \\(1\\).</p>', '<p>Это короткий предварительный взгляд на мышление через сравнения без полноценного урока по модульной арифметике.</p>');
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'gcd';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'proof';
INSERT IGNORE INTO problem_tags (problem_id, tag_id) SELECT @problem_id, id FROM tags WHERE slug = 'olympiad';

SET FOREIGN_KEY_CHECKS = 1;
