<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEnglishIrrationalLadderCommand extends Command
{
    protected $signature = 'olympiad:add-english-irrational-ladder
        {--dry-run : Preview changes only}
        {--apply : Apply changes to database}
        {--force : Update existing non-empty English texts}';

    protected $description = 'Create/update English problem_texts for ladder irrational-part-parity-invariant.';

    private const LADDER_SLUG = 'irrational-part-parity-invariant';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $dryRun = (bool) $this->option('dry-run') || ! $apply;
        $force = (bool) $this->option('force');

        if (! $this->option('dry-run') && ! $this->option('apply')) {
            $this->warn('No mode provided, defaulting to --dry-run.');
        }

        $ladder = DB::table('problem_ladders')->where('slug', self::LADDER_SLUG)->first();
        if (! $ladder) {
            $this->error('Ladder not found: ' . self::LADDER_SLUG);
            return self::FAILURE;
        }

        /** @var Collection<int, object> $steps */
        $steps = DB::table('problem_ladder_steps')
            ->where('ladder_id', (int) $ladder->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->info('Ladder found: ' . self::LADDER_SLUG . ' (id=' . $ladder->id . ')');
        $this->line('Steps found: ' . $steps->count());

        if ($steps->count() !== 10) {
            $this->warn('Expected 10 steps, found ' . $steps->count() . '. Command will process available steps only.');
        }

        $englishLanguage = $this->resolveOrCreateEnglishLanguage($dryRun);
        if (! $englishLanguage) {
            $this->error('Could not resolve English language row.');
            return self::FAILURE;
        }

        $this->line('English language id: ' . $englishLanguage->id);
        $this->line('English language marker: ' . $englishLanguage->marker);

        $content = $this->englishContentByStep();
        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        $runner = function () use ($steps, $content, $dryRun, $force, &$summary): void {
            foreach ($steps as $index => $step) {
                $stepNumber = $index + 1;
                $problemId = (int) $step->problem_id;

                if (! isset($content[$stepNumber])) {
                    $this->warn("Step {$stepNumber}: no English payload provided, skipping.");
                    $summary['skipped']++;
                    continue;
                }

                $payload = $content[$stepNumber];
                $existing = DB::table('problem_texts')
                    ->where('problem_id', $problemId)
                    ->where('lang', 'en')
                    ->first();

                if (! $existing) {
                    $summary['created']++;
                    $this->line("CREATE step {$stepNumber} (problem_id={$problemId})");
                    if (! $dryRun) {
                        DB::table('problem_texts')->insert([
                            'problem_id' => $problemId,
                            'lang' => 'en',
                            'title' => $payload['title'],
                            'statement_html' => $payload['statement_html'],
                            'hint_html' => $payload['hint_html'],
                            'solution_html' => $payload['solution_html'],
                            'teacher_note_html' => null,
                        ]);
                    }
                    continue;
                }

                $shouldUpdate = $force || $this->isEnglishTextEmpty($existing);
                if ($shouldUpdate) {
                    $summary['updated']++;
                    $reason = $force ? 'force' : 'empty existing row';
                    $this->line("UPDATE step {$stepNumber} (problem_id={$problemId}) [{$reason}]");
                    if (! $dryRun) {
                        DB::table('problem_texts')
                            ->where('id', $existing->id)
                            ->update([
                                'title' => $payload['title'],
                                'statement_html' => $payload['statement_html'],
                                'hint_html' => $payload['hint_html'],
                                'solution_html' => $payload['solution_html'],
                            ]);
                    }
                    continue;
                }

                $summary['skipped']++;
                $this->line("SKIP step {$stepNumber} (problem_id={$problemId}) [existing non-empty en text]");
            }
        };

        if ($dryRun) {
            $runner();
        } else {
            DB::transaction($runner);
        }

        $this->newLine();
        $this->info($dryRun ? 'DRY-RUN SUMMARY' : 'APPLY SUMMARY');
        $this->line('created: ' . $summary['created']);
        $this->line('updated: ' . $summary['updated']);
        $this->line('skipped: ' . $summary['skipped']);

        if ($dryRun) {
            $this->warn('No changes saved. Run with --apply to persist.');
        } else {
            $this->info('English texts applied successfully.');
        }

        return self::SUCCESS;
    }

    private function resolveOrCreateEnglishLanguage(bool $dryRun): ?object
    {
        $columns = Schema::getColumnListing('languages');

        $codeColumn = $this->firstExistingColumn($columns, ['code', 'lang', 'slug']);
        $titleColumn = $this->firstExistingColumn($columns, ['title', 'name']);

        if ($codeColumn) {
            $row = DB::table('languages')->where($codeColumn, 'en')->first();
            if ($row) {
                return (object) ['id' => $row->id, 'marker' => "{$codeColumn}=en"];
            }
        }

        if ($titleColumn) {
            $row = DB::table('languages')->whereRaw("LOWER({$titleColumn}) = ?", ['english'])->first();
            if ($row) {
                return (object) ['id' => $row->id, 'marker' => "{$titleColumn}=English"];
            }
        }

        if (! $codeColumn && ! $titleColumn) {
            return null;
        }

        $insert = [];
        if ($codeColumn) {
            $insert[$codeColumn] = 'en';
        }
        if ($titleColumn) {
            $insert[$titleColumn] = 'English';
        }
        if (in_array('is_default', $columns, true)) {
            $insert['is_default'] = 0;
        }
        if (in_array('is_active', $columns, true)) {
            $insert['is_active'] = 1;
        }
        if (in_array('sort_order', $columns, true)) {
            $maxSort = (int) DB::table('languages')->max('sort_order');
            $insert['sort_order'] = $maxSort + 1;
        }

        if ($dryRun) {
            return (object) ['id' => '[dry-run:new]', 'marker' => 'would create English language row'];
        }

        $languageId = DB::table('languages')->insertGetId($insert);
        return (object) ['id' => $languageId, 'marker' => 'created'];
    }

    /**
     * @param array<int, string> $columns
     * @param array<int, string> $candidates
     */
    private function firstExistingColumn(array $columns, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }
        return null;
    }

    private function isEnglishTextEmpty(object $existing): bool
    {
        $fields = [
            (string) ($existing->title ?? ''),
            (string) ($existing->statement_html ?? ''),
            (string) ($existing->hint_html ?? ''),
            (string) ($existing->solution_html ?? ''),
        ];

        foreach ($fields as $field) {
            if (trim(strip_tags($field)) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, array{title: string, statement_html: string, hint_html: string, solution_html: string}>
     */
    private function englishContentByStep(): array
    {
        return [
            1 => [
                'title' => 'Fractional Parts and Integer Difference',
                'statement_html' => '<p>Prove that two real numbers \(u\) and \(v\) have the same fractional part if and only if \(u-v\) is an integer.</p>',
                'hint_html' => '<p>Write each number as the sum of its integer part and fractional part.</p>',
                'solution_html' => <<<'HTML'
<p>Let \(u=m+\alpha\) and \(v=n+\beta\), where \(m,n\in\mathbb Z\) and \(0\le \alpha,\beta<1\). The fractional parts are equal if and only if \(\alpha=\beta\). In that case,</p>
\[
u-v=m-n,
\]
<p>which is an integer. Conversely, if \(u-v\in\mathbb Z\), then the fractional parts of \(u\) and \(v\) must be equal.</p>
HTML,
            ],
            2 => [
                'title' => 'The Irrational Part Cannot Disappear',
                'statement_html' => '<p>Let \(A,B\in\mathbb Z\). Prove that if \(A+B\sqrt2\) is an integer, then \(B=0\).</p>',
                'hint_html' => '<p>What would happen if \(B\ne0\)?</p>',
                'solution_html' => '<p>If \(A+B\sqrt2\in\mathbb Z\), then \(B\sqrt2\in\mathbb Z\). If \(B\ne0\), then \(\sqrt2\) would be rational, which is impossible. Therefore \(B=0\).</p>',
            ],
            3 => [
                'title' => 'Products of Numbers of the Form \(A+B\sqrt2\)',
                'statement_html' => '<p>Prove that the product of two numbers of the form \(A+B\sqrt2\) and \(C+D\sqrt2\), where \(A,B,C,D\in\mathbb Z\), again has the form \(M+N\sqrt2\), where \(M,N\in\mathbb Z\).</p>',
                'hint_html' => '<p>Expand the brackets and use \((\sqrt2)^2=2\).</p>',
                'solution_html' => <<<'HTML'
\[
(A+B\sqrt2)(C+D\sqrt2)=(AC+2BD)+(AD+BC)\sqrt2.
\]
<p>Since \(A,B,C,D\in\mathbb Z\), both \(AC+2BD\) and \(AD+BC\) are integers. Hence the product has the form \(M+N\sqrt2\).</p>
HTML,
            ],
            4 => [
                'title' => 'The Coefficient of \(\sqrt2\) for Two Factors',
                'statement_html' => <<<'HTML'
<p>Let \(a,b\in\mathbb Z\). Find the coefficient of \(\sqrt2\) in</p>
\[
(1+a\sqrt2)(1+b\sqrt2).
\]
<p>Prove that this coefficient has the same parity as \(a+b\).</p>
HTML,
                'hint_html' => '<p>Expand the product.</p>',
                'solution_html' => <<<'HTML'
\[
(1+a\sqrt2)(1+b\sqrt2)=1+2ab+(a+b)\sqrt2.
\]
<p>The coefficient of \(\sqrt2\) is \(a+b\), so it has the same parity as \(a+b\).</p>
HTML,
            ],
            5 => [
                'title' => 'The Coefficient of \(\sqrt2\) for Three Factors',
                'statement_html' => <<<'HTML'
<p>Prove that</p>
\[
(1+a\sqrt2)(1+b\sqrt2)(1+c\sqrt2)
\]
<p>has the form \(A+B\sqrt2\), where \(A,B\in\mathbb Z\), and</p>
\[
B\equiv a+b+c\pmod2.
\]
HTML,
                'hint_html' => '<p>Look separately at the terms containing one factor \(\sqrt2\) and the term containing three factors \(\sqrt2\).</p>',
                'solution_html' => <<<'HTML'
<p>The terms with exactly one factor \(\sqrt2\) contribute</p>
\[
(a+b+c)\sqrt2.
\]
<p>The term with three factors \(\sqrt2\) is</p>
\[
abc(\sqrt2)^3=2abc\sqrt2.
\]
<p>Therefore the coefficient of \(\sqrt2\) is \(a+b+c+2abc\). Hence</p>
\[
B\equiv a+b+c\pmod2.
\]
HTML,
            ],
            6 => [
                'title' => 'The General Lemma',
                'statement_html' => <<<'HTML'
<p>Let \(x_1,\ldots,x_k\in\mathbb Z\). Prove that</p>
\[
(1+x_1\sqrt2)(1+x_2\sqrt2)\cdots(1+x_k\sqrt2)=A+B\sqrt2,
\]
<p>where \(A,B\in\mathbb Z\), and</p>
\[
B\equiv x_1+x_2+\cdots+x_k\pmod2.
\]
HTML,
                'hint_html' => '<p>The coefficient of \(\sqrt2\) comes from terms in which an odd number of factors \(x_i\sqrt2\) are chosen.</p>',
                'solution_html' => <<<'HTML'
<p>When the brackets are expanded, each term is obtained by choosing either \(1\) or \(x_i\sqrt2\) from each factor. A contribution to the coefficient of \(\sqrt2\) appears when an odd number of factors \(x_i\sqrt2\) is chosen. If exactly one such factor is chosen, the contribution is</p>
\[
x_1+\cdots+x_k.
\]
<p>If \(3,5,7,\ldots\) such factors are chosen, then after factoring out \(\sqrt2\), a factor \(2,4,8,\ldots\) remains, so the contribution is even. Therefore, modulo \(2\), only the one-factor contribution remains:</p>
\[
B\equiv x_1+\cdots+x_k\pmod2.
\]
HTML,
            ],
            7 => [
                'title' => 'Parity of the Sums in Two Groups',
                'statement_html' => '<p>The numbers \(1,2,\ldots,2026\) are divided into two groups. Prove that the sums of the numbers in the two groups have different parity.</p>',
                'hint_html' => '<p>Find the value of \(1+2+\cdots+2026\).</p>',
                'solution_html' => <<<'HTML'
\[
1+2+\cdots+2026=\frac{2026\cdot2027}{2}=1013\cdot2027.
\]
<p>This number is odd. If the sum of the two group sums is odd, then one group sum is even and the other is odd. Therefore, the sums of the two groups have different parity.</p>
HTML,
            ],
            8 => [
                'title' => 'A Smaller Version of the Target Problem',
                'statement_html' => <<<'HTML'
<p>Is it possible to divide the numbers</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+6\sqrt2
\]
<p>into two non-empty groups so that the products of the numbers in the two groups have the same fractional part?</p>
HTML,
                'hint_html' => '<p>Use the parity of the coefficient of \(\sqrt2\) and the fact that \(1+2+\cdots+6\) is odd.</p>',
                'solution_html' => <<<'HTML'
<p>Suppose the products in the two groups are \(A+B\sqrt2\) and \(C+D\sqrt2\), where \(A,B,C,D\in\mathbb Z\). By the general lemma, the parity of \(B\) is the parity of the sum of the indices in the first group, and the parity of \(D\) is the parity of the sum of the indices in the second group. But</p>
\[
1+2+\cdots+6=21
\]
<p>is odd, so the two sums have different parity. Hence \(B\ne D\). If the two products had the same fractional part, their difference would be an integer:</p>
\[
(A+B\sqrt2)-(C+D\sqrt2)\in\mathbb Z.
\]
<p>This is possible only if \(B=D\), a contradiction. Therefore, such a division is impossible.</p>
HTML,
            ],
            9 => [
                'title' => 'A General Version',
                'statement_html' => <<<'HTML'
<p>Let \(N\equiv1\) or \(2\pmod4\). Prove that the numbers</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+N\sqrt2
\]
<p>cannot be divided into two non-empty groups so that the products of the numbers in the two groups have the same fractional part.</p>
HTML,
                'hint_html' => '<p>Determine the parity of \(1+2+\cdots+N\).</p>',
                'solution_html' => <<<'HTML'
\[
1+2+\cdots+N=\frac{N(N+1)}2.
\]
<p>If \(N\equiv1\) or \(2\pmod4\), then \(\frac{N(N+1)}2\) is odd. Therefore, in any division into two groups, the sums of the indices in the two groups have different parity. By the general lemma, the coefficients of \(\sqrt2\) in the two products also have different parity, so they are not equal. But if the products had the same fractional part, their difference would be an integer, which is possible only if the coefficients of \(\sqrt2\) are equal. This contradiction proves the claim.</p>
HTML,
            ],
            10 => [
                'title' => 'The Target Problem',
                'statement_html' => <<<'HTML'
<p>Is it possible to divide the \(2026\) numbers</p>
\[
1+\sqrt2,\ 1+2\sqrt2,\ \ldots,\ 1+2026\sqrt2
\]
<p>into two non-empty groups so that the products of the numbers in the two groups have the same fractional part?</p>
HTML,
                'hint_html' => '<p>Show that the coefficients of \(\sqrt2\) in the two products must be equal, but have different parity.</p>',
                'solution_html' => <<<'HTML'
<p>Suppose one group contains</p>
\[
1+x_1\sqrt2,\ldots,1+x_k\sqrt2,
\]
<p>and the other contains</p>
\[
1+y_1\sqrt2,\ldots,1+y_m\sqrt2.
\]
<p>By the general lemma, the two products have the form \(A+B\sqrt2\) and \(C+D\sqrt2\), where</p>
\[
B\equiv x_1+\cdots+x_k\pmod2,\qquad
D\equiv y_1+\cdots+y_m\pmod2.
\]
<p>But</p>
\[
x_1+\cdots+x_k+y_1+\cdots+y_m=1+2+\cdots+2026
=\frac{2026\cdot2027}{2}=1013\cdot2027,
\]
<p>which is odd. Hence the two sums of indices have different parity, so \(B\) and \(D\) have different parity. In particular, \(B\ne D\).</p>
<p>If the two products had the same fractional part, then their difference would be an integer:</p>
\[
(A+B\sqrt2)-(C+D\sqrt2)\in\mathbb Z.
\]
<p>Since \(\sqrt2\) is irrational, this is possible only if \(B-D=0\), that is, \(B=D\). This is a contradiction. Therefore, such a division is impossible.</p>
HTML,
            ],
        ];
    }
}

