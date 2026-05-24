<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedIrrationalPartLadderCommand extends Command
{
    protected $signature = 'olympiad:seed-irrational-part-ladder
        {--dry-run : Preview changes only}
        {--apply : Apply changes to database}
        {--course= : Course slug (optional)}
        {--chapter= : Chapter slug (optional)}';

    protected $description = 'Creates/updates the "Иррациональная часть и инвариант чётности" ladder with 10 linked problems.';

    private const LADDER_SLUG = 'irrational-part-parity-invariant';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $dryRun = (bool) $this->option('dry-run') || ! $apply;

        if (! $this->option('dry-run') && ! $this->option('apply')) {
            $this->warn('No mode provided, defaulting to --dry-run.');
        }

        $course = $this->resolveCourse();
        if (! $course) {
            return self::FAILURE;
        }

        $chapter = $this->resolveChapter((int) $course->id);
        if (! $chapter) {
            return self::FAILURE;
        }

        $this->info("Course: {$course->slug} (id={$course->id})");
        $this->info("Chapter: {$chapter->slug} (id={$chapter->id})");

        if ($dryRun) {
            $this->warn('Dry-run mode: no database writes will be made.');
        }

        $summary = [
            'problems_created' => 0,
            'problems_updated' => 0,
            'texts_created' => 0,
            'texts_updated' => 0,
            'ladder_created' => 0,
            'ladder_updated' => 0,
            'steps_created' => 0,
            'steps_updated' => 0,
            'tags_created' => 0,
            'tag_links_created' => 0,
        ];

        $runner = function () use ($chapter, $course, $dryRun, &$summary): void {
            $problemsData = $this->problemsData();
            $problemIdsByCode = [];

            foreach ($problemsData as $row) {
                $existing = DB::table('problems')
                    ->where('problem_code', $row['problem_code'])
                    ->first();

                $problemPayload = [
                    'chapter_id' => (int) $chapter->id,
                    'book_number' => $row['book_number'],
                    'difficulty' => $row['difficulty'],
                    'problem_type' => $row['problem_type'],
                    'sort_order' => $row['sort_order'],
                    'is_published' => 1,
                    'updated_at' => now(),
                ];

                if ($existing) {
                    $problemId = (int) $existing->id;
                    $summary['problems_updated']++;
                    if (! $dryRun) {
                        DB::table('problems')->where('id', $problemId)->update($problemPayload);
                    }
                } else {
                    $problemId = $this->nextProblemId();
                    $summary['problems_created']++;
                    if (! $dryRun) {
                        DB::table('problems')->insert(array_merge($problemPayload, [
                            'id' => $problemId,
                            'problem_code' => $row['problem_code'],
                            'created_at' => now(),
                        ]));
                    }
                }

                $problemIdsByCode[$row['problem_code']] = $problemId;

                $existingText = DB::table('problem_texts')
                    ->where('problem_id', $problemId)
                    ->where('lang', 'ru')
                    ->first();

                $textPayload = [
                    'title' => $row['title'],
                    'statement_html' => $row['statement_html'],
                    'hint_html' => $row['hint_html'],
                    'solution_html' => $row['solution_html'],
                    'teacher_note_html' => $row['teacher_note_html'],
                ];

                if ($existingText) {
                    $summary['texts_updated']++;
                    if (! $dryRun) {
                        DB::table('problem_texts')
                            ->where('id', $existingText->id)
                            ->update($textPayload);
                    }
                } else {
                    $summary['texts_created']++;
                    if (! $dryRun) {
                        DB::table('problem_texts')->insert(array_merge($textPayload, [
                            'problem_id' => $problemId,
                            'lang' => 'ru',
                        ]));
                    }
                }
            }

            $ladderExisting = DB::table('problem_ladders')
                ->where('slug', self::LADDER_SLUG)
                ->first();

            $ladderPayload = [
                'course_id' => (int) $course->id,
                'chapter_id' => (int) $chapter->id,
                'title' => 'Иррациональная часть и инвариант чётности',
                'description' => 'Лестница задач, которая постепенно подводит к олимпиадной задаче о произведениях чисел вида 1+k√2 и одинаковых дробных частях.',
                'main_method' => 'Иррациональная часть, дробные части, чётность коэффициента при √2',
                'difficulty_level' => 5,
                'sort_order' => 500,
                'is_published' => 1,
                'updated_at' => now(),
            ];

            if ($ladderExisting) {
                $ladderId = (int) $ladderExisting->id;
                $summary['ladder_updated']++;
                if (! $dryRun) {
                    DB::table('problem_ladders')->where('id', $ladderId)->update($ladderPayload);
                }
            } else {
                $ladderId = $this->nextLadderId();
                $summary['ladder_created']++;
                if (! $dryRun) {
                    DB::table('problem_ladders')->insert(array_merge($ladderPayload, [
                        'id' => $ladderId,
                        'slug' => self::LADDER_SLUG,
                        'created_at' => now(),
                    ]));
                }
            }

            foreach ($problemsData as $index => $row) {
                $stepNumber = $index + 1;
                $problemId = $problemIdsByCode[$row['problem_code']];
                $stepExisting = DB::table('problem_ladder_steps')
                    ->where('ladder_id', $ladderId)
                    ->where('problem_id', $problemId)
                    ->first();

                $stepPayload = [
                    'ladder_id' => $ladderId,
                    'problem_id' => $problemId,
                    'step_label' => 'Шаг ' . $stepNumber,
                    'step_title' => $row['title'],
                    'step_type' => $row['step_type'],
                    'difficulty_level' => $row['difficulty'],
                    'sort_order' => $stepNumber,
                    'hint_html' => $row['hint_html'],
                    'teacher_note_html' => $row['teacher_note_html'],
                    'updated_at' => now(),
                ];

                if ($stepExisting) {
                    $summary['steps_updated']++;
                    if (! $dryRun) {
                        DB::table('problem_ladder_steps')->where('id', $stepExisting->id)->update($stepPayload);
                    }
                } else {
                    $summary['steps_created']++;
                    if (! $dryRun) {
                        DB::table('problem_ladder_steps')->insert(array_merge($stepPayload, [
                            'created_at' => now(),
                        ]));
                    }
                }
            }

            $this->applyTagIfAvailable(array_values($problemIdsByCode), $dryRun, $summary);
        };

        if ($dryRun) {
            $runner();
        } else {
            DB::transaction($runner);
        }

        $this->newLine();
        $this->info($dryRun ? 'DRY-RUN SUMMARY' : 'APPLY SUMMARY');
        foreach ($summary as $k => $v) {
            $this->line(str_pad($k, 20) . ': ' . $v);
        }

        $this->newLine();
        $this->line('Ladder URL: /ladders/' . self::LADDER_SLUG . '?lang=ru');
        $this->line('Ladder practice URL: /ladders/' . self::LADDER_SLUG . '/practice?lang=ru');

        if ($dryRun) {
            $this->warn('No changes saved. Use --apply to persist.');
        } else {
            $this->info('Done. Ladder data applied.');
        }

        return self::SUCCESS;
    }

    private function resolveCourse(): ?object
    {
        $courseOption = (string) $this->option('course');
        if ($courseOption !== '') {
            $course = DB::table('courses')->where('slug', $courseOption)->first();
            if (! $course) {
                $this->error("Course '{$courseOption}' not found.");
                return null;
            }
            return $course;
        }

        foreach (['number-theory', 'algebra'] as $slug) {
            $course = DB::table('courses')->where('slug', $slug)->first();
            if ($course) {
                return $course;
            }
        }

        $this->error('No suitable course found. Expected one of: number-theory, algebra.');
        return null;
    }

    private function resolveChapter(int $courseId): ?object
    {
        $chapterOption = (string) $this->option('chapter');
        if ($chapterOption !== '') {
            $chapter = DB::table('chapters')
                ->where('course_id', $courseId)
                ->where('slug', $chapterOption)
                ->first();
            if (! $chapter) {
                $this->error("Chapter '{$chapterOption}' not found in selected course.");
                return null;
            }
            return $chapter;
        }

        $preferredSlugs = [
            'irrational-part-parity-invariant',
            'irrational-expressions',
            'fractional-parts',
            'invariants',
            'parity-invariant',
        ];

        foreach ($preferredSlugs as $slug) {
            $chapter = DB::table('chapters')
                ->where('course_id', $courseId)
                ->where('slug', $slug)
                ->first();
            if ($chapter) {
                return $chapter;
            }
        }

        $fallbackSlugs = [
            'modular-arithmetic',
            'congruences-and-remainders',
        ];

        foreach ($fallbackSlugs as $slug) {
            $chapter = DB::table('chapters')
                ->where('course_id', $courseId)
                ->where('slug', $slug)
                ->first();
            if ($chapter) {
                $this->warn("No dedicated invariants/irrational chapter found. Using fallback chapter '{$slug}'.");
                $this->warn("Recommended chapter slug for future content: 'irrational-part-parity-invariant'.");
                return $chapter;
            }
        }

        $this->error('No suitable chapter found in selected course.');
        $this->line("Recommended chapter slug to create first: 'irrational-part-parity-invariant'.");
        return null;
    }

    private function nextProblemId(): int
    {
        $max = (int) DB::table('problems')->max('id');
        return $max + 1;
    }

    private function nextLadderId(): int
    {
        $max = (int) DB::table('problem_ladders')->max('id');
        return $max + 1;
    }

    private function applyTagIfAvailable(array $problemIds, bool $dryRun, array &$summary): void
    {
        if (
            ! Schema::hasTable('tags')
            || ! Schema::hasTable('tag_texts')
            || ! Schema::hasTable('problem_tags')
        ) {
            return;
        }

        $tag = DB::table('tags')->where('slug', 'irrational-part-parity')->first();
        if (! $tag) {
            $tagId = (int) DB::table('tags')->max('id') + 1;
            $summary['tags_created']++;
            if (! $dryRun) {
                DB::table('tags')->insert([
                    'id' => $tagId,
                    'slug' => 'irrational-part-parity',
                    'created_at' => now(),
                ]);
                DB::table('tag_texts')->insert([
                    'tag_id' => $tagId,
                    'lang' => 'ru',
                    'title' => 'Иррациональная часть и чётность',
                ]);
            }
        } else {
            $tagId = (int) $tag->id;
        }

        foreach ($problemIds as $problemId) {
            $exists = DB::table('problem_tags')
                ->where('problem_id', $problemId)
                ->where('tag_id', $tagId)
                ->exists();

            if (! $exists) {
                $summary['tag_links_created']++;
                if (! $dryRun) {
                    DB::table('problem_tags')->insert([
                        'problem_id' => $problemId,
                        'tag_id' => $tagId,
                    ]);
                }
            }
        }
    }

    private function problemsData(): array
    {
        return [
            [
                'problem_code' => 'IPPI-001',
                'book_number' => 9001,
                'difficulty' => 1,
                'problem_type' => 'proof',
                'sort_order' => 9001,
                'step_type' => 'warmup',
                'title' => 'Дробные части и целая разность',
                'statement_html' => <<<'HTML'
<p>Докажите, что два действительных числа \(u\) и \(v\) имеют одинаковую дробную часть тогда и только тогда, когда \(u-v\) — целое число.</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Запишите каждое число как сумму целой и дробной части.</p>
HTML,
                'solution_html' => <<<'HTML'
<p>Пусть \(u=m+\alpha\), \(v=n+\beta\), где \(m,n\in\mathbb Z\), \(0\le\alpha,\beta<1\). Тогда дробные части равны тогда и только тогда, когда \(\alpha=\beta\). В этом случае \(u-v=m-n\), то есть разность целая. Обратно, если \(u-v\in\mathbb Z\), то дробные части \(u\) и \(v\) совпадают.</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Ученик должен понять, почему условие про одинаковые дробные части можно заменить условием \(u-v\in\mathbb Z\).</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-002',
                'book_number' => 9002,
                'difficulty' => 1,
                'problem_type' => 'proof',
                'sort_order' => 9002,
                'step_type' => 'lemma',
                'title' => 'Иррациональная часть не может исчезнуть',
                'statement_html' => <<<'HTML'
<p>Пусть \(A,B\in\mathbb Z\). Докажите, что если \(A+B\sqrt2\) является целым числом, то \(B=0\).</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Что произойдёт, если \(B\ne0\)?</p>
HTML,
                'solution_html' => <<<'HTML'
<p>Если \(A+B\sqrt2\in\mathbb Z\), то \(B\sqrt2\in\mathbb Z\). Если \(B\ne0\), то \(\sqrt2\) было бы рациональным числом, что невозможно. Следовательно, \(B=0\).</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Подготовить переход \(A+B\sqrt2\in\mathbb Z\Rightarrow B=0\).</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-003',
                'book_number' => 9003,
                'difficulty' => 1,
                'problem_type' => 'proof',
                'sort_order' => 9003,
                'step_type' => 'lemma',
                'title' => 'Произведение чисел вида \(A+B\sqrt2\)',
                'statement_html' => <<<'HTML'
<p>Докажите, что произведение двух чисел вида \(A+B\sqrt2\) и \(C+D\sqrt2\), где \(A,B,C,D\in\mathbb Z\), снова имеет вид \(M+N\sqrt2\), где \(M,N\in\mathbb Z\).</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Раскройте скобки и используйте равенство \((\sqrt2)^2=2\).</p>
HTML,
                'solution_html' => <<<'HTML'
\[
(A+B\sqrt2)(C+D\sqrt2)=(AC+2BD)+(AD+BC)\sqrt2.
\]
<p>Так как \(A,B,C,D\in\mathbb Z\), то \(AC+2BD\) и \(AD+BC\) — целые числа. Поэтому произведение имеет вид \(M+N\sqrt2\).</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Ученик видит, что числа вида \(A+B\sqrt2\) замкнуты относительно умножения.</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-004',
                'book_number' => 9004,
                'difficulty' => 2,
                'problem_type' => 'proof',
                'sort_order' => 9004,
                'step_type' => 'direct',
                'title' => 'Коэффициент при \(\sqrt2\) для двух множителей',
                'statement_html' => <<<'HTML'
<p>Пусть \(a,b\in\mathbb Z\). Найдите коэффициент при \(\sqrt2\) в произведении</p>
\[
(1+a\sqrt2)(1+b\sqrt2).
\]
<p>Докажите, что этот коэффициент имеет ту же чётность, что и \(a+b\).</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Раскройте скобки.</p>
HTML,
                'solution_html' => <<<'HTML'
\[
(1+a\sqrt2)(1+b\sqrt2)=1+2ab+(a+b)\sqrt2.
\]
<p>Коэффициент при \(\sqrt2\) равен \(a+b\), поэтому он, конечно, имеет ту же чётность, что и \(a+b\).</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Первая простая версия главного инварианта.</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-005',
                'book_number' => 9005,
                'difficulty' => 2,
                'problem_type' => 'proof',
                'sort_order' => 9005,
                'step_type' => 'direct',
                'title' => 'Коэффициент при \(\sqrt2\) для трёх множителей',
                'statement_html' => <<<'HTML'
<p>Докажите, что произведение</p>
\[
(1+a\sqrt2)(1+b\sqrt2)(1+c\sqrt2)
\]
<p>имеет вид \(A+B\sqrt2\), где \(A,B\in\mathbb Z\), причём</p>
\[
B\equiv a+b+c\pmod2.
\]
HTML,
                'hint_html' => <<<'HTML'
<p>Посмотрите отдельно на слагаемые с одним множителем \(\sqrt2\) и с тремя множителями \(\sqrt2\).</p>
HTML,
                'solution_html' => <<<'HTML'
<p>Слагаемые с одним множителем \(\sqrt2\) дают вклад \((a+b+c)\sqrt2\). Слагаемое с тремя множителями \(\sqrt2\) равно</p>
\[
abc(\sqrt2)^3=2abc\sqrt2.
\]
<p>Значит коэффициент при \(\sqrt2\) равен \(a+b+c+2abc\). Поэтому</p>
\[
B\equiv a+b+c\pmod2.
\]
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Ученик замечает, что дополнительные слагаемые дают чётный вклад в коэффициент при \(\sqrt2\).</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-006',
                'book_number' => 9006,
                'difficulty' => 3,
                'problem_type' => 'proof',
                'sort_order' => 9006,
                'step_type' => 'lemma',
                'title' => 'Общая лемма',
                'statement_html' => <<<'HTML'
<p>Пусть \(x_1,\ldots,x_k\in\mathbb Z\). Докажите, что</p>
\[
(1+x_1\sqrt2)(1+x_2\sqrt2)\cdots(1+x_k\sqrt2)=A+B\sqrt2,
\]
<p>где \(A,B\in\mathbb Z\), причём</p>
\[
B\equiv x_1+x_2+\cdots+x_k\pmod2.
\]
HTML,
                'hint_html' => <<<'HTML'
<p>Коэффициент при \(\sqrt2\) появляется из слагаемых, где выбрано нечётное число множителей \(x_i\sqrt2\).</p>
HTML,
                'solution_html' => <<<'HTML'
<p>При раскрытии скобок каждое слагаемое получается выбором из каждого множителя либо \(1\), либо \(x_i\sqrt2\). Коэффициент при \(\sqrt2\) возникает, когда выбрано нечётное число множителей \(x_i\sqrt2\). Если выбран ровно один такой множитель, вклад в коэффициент равен \(x_1+\cdots+x_k\). Если выбрано \(3,5,7,\ldots\) множителей, то после вынесения \(\sqrt2\) остаётся множитель \(2,4,8,\ldots\), то есть вклад чётный. Поэтому по модулю \(2\) остаётся только</p>
\[
B\equiv x_1+\cdots+x_k\pmod2.
\]
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Это техническое ядро исходной задачи.</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-007',
                'book_number' => 9007,
                'difficulty' => 3,
                'problem_type' => 'proof',
                'sort_order' => 9007,
                'step_type' => 'direct',
                'title' => 'Чётность сумм в двух группах',
                'statement_html' => <<<'HTML'
<p>Числа \(1,2,\ldots,2026\) разбили на две группы. Докажите, что суммы чисел в этих двух группах имеют разную чётность.</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Найдите сумму всех чисел \(1+2+\cdots+2026\).</p>
HTML,
                'solution_html' => <<<'HTML'
\[
1+2+\cdots+2026=\frac{2026\cdot2027}{2}=1013\cdot2027.
\]
<p>Это нечётное число. Если сумма двух групп нечётна, то одна из двух сумм чётная, а другая нечётная. Значит, суммы чисел в группах имеют разную чётность.</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Подготовить финальное противоречие в исходной задаче.</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-008',
                'book_number' => 9008,
                'difficulty' => 4,
                'problem_type' => 'challenge',
                'sort_order' => 9008,
                'step_type' => 'mixed',
                'title' => 'Упрощённая версия исходной задачи',
                'statement_html' => <<<'HTML'
<p>Можно ли числа</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+6\sqrt2
\]
<p>разбить на две непустые группы так, чтобы произведения чисел в группах имели одинаковую дробную часть?</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Используйте чётность коэффициента при \(\sqrt2\) и тот факт, что \(1+2+\cdots+6\) нечётно.</p>
HTML,
                'solution_html' => <<<'HTML'
<p>Пусть произведения в двух группах равны \(A+B\sqrt2\) и \(C+D\sqrt2\), где \(A,B,C,D\in\mathbb Z\). По общей лемме чётность \(B\) равна чётности суммы номеров в первой группе, а чётность \(D\) — чётности суммы номеров во второй группе. Но</p>
\[
1+2+\cdots+6=21
\]
<p>нечётно, поэтому эти две суммы имеют разную чётность. Значит, \(B\ne D\). Если дробные части произведений одинаковы, то их разность должна быть целым числом:</p>
\[
(A+B\sqrt2)-(C+D\sqrt2)\in\mathbb Z.
\]
<p>Это возможно только при \(B=D\), противоречие. Следовательно, такого разбиения не существует.</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Та же идея, что в исходной задаче, но с меньшим числом множителей.</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-009',
                'book_number' => 9009,
                'difficulty' => 4,
                'problem_type' => 'challenge',
                'sort_order' => 9009,
                'step_type' => 'challenge',
                'title' => 'Обобщённая версия',
                'statement_html' => <<<'HTML'
<p>Пусть \(N\equiv1\) или \(2\pmod4\). Докажите, что числа</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+N\sqrt2
\]
<p>нельзя разбить на две непустые группы так, чтобы произведения чисел в группах имели одинаковую дробную часть.</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Определите чётность суммы \(1+2+\cdots+N\).</p>
HTML,
                'solution_html' => <<<'HTML'
\[
1+2+\cdots+N=\frac{N(N+1)}2.
\]
<p>Если \(N\equiv1\) или \(2\pmod4\), то \(\frac{N(N+1)}2\) нечётно. Поэтому при любом разбиении суммы номеров в двух группах имеют разную чётность. По общей лемме коэффициенты при \(\sqrt2\) в двух произведениях также имеют разную чётность, значит они не равны. Но если дробные части произведений одинаковы, то разность произведений должна быть целым числом, а это возможно только при равенстве коэффициентов при \(\sqrt2\). Получаем противоречие.</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Показать, что число \(2026\) не случайно; важна нечётность суммы \(1+2+\cdots+N\).</p>
HTML,
            ],
            [
                'problem_code' => 'IPPI-010',
                'book_number' => 9010,
                'difficulty' => 5,
                'problem_type' => 'challenge',
                'sort_order' => 9010,
                'step_type' => 'target',
                'title' => 'Исходная задача',
                'statement_html' => <<<'HTML'
<p>Можно ли \(2026\) чисел</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+2026\sqrt2
\]
<p>разбить на две непустые группы так, чтобы произведения чисел в группах имели одинаковую дробную часть?</p>
HTML,
                'hint_html' => <<<'HTML'
<p>Докажите, что коэффициенты при \(\sqrt2\) в двух произведениях должны быть равны, но имеют разную чётность.</p>
HTML,
                'solution_html' => <<<'HTML'
<p>Пусть одна группа содержит числа \(1+x_1\sqrt2,\ldots,1+x_k\sqrt2\), а другая — \(1+y_1\sqrt2,\ldots,1+y_m\sqrt2\). По общей лемме произведения имеют вид \(A+B\sqrt2\) и \(C+D\sqrt2\), причём</p>
\[
B\equiv x_1+\cdots+x_k\pmod2,\qquad
D\equiv y_1+\cdots+y_m\pmod2.
\]
<p>Но</p>
\[
x_1+\cdots+x_k+y_1+\cdots+y_m=1+2+\cdots+2026=\frac{2026\cdot2027}{2}=1013\cdot2027,
\]
<p>а это число нечётно. Значит, суммы номеров в двух группах имеют разную чётность, поэтому \(B\) и \(D\) имеют разную чётность. Следовательно, \(B\ne D\).</p>
<p>Если дробные части двух произведений одинаковы, то их разность является целым числом:</p>
\[
(A+B\sqrt2)-(C+D\sqrt2)\in\mathbb Z.
\]
<p>Так как \(\sqrt2\) иррационально, это возможно только при \(B-D=0\), то есть \(B=D\). Получили противоречие. Значит, такое разбиение невозможно.</p>
HTML,
                'teacher_note_html' => <<<'HTML'
<p>Финальная задача лестницы.</p>
HTML,
            ],
        ];
    }
}

