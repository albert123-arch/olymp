<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairMathjaxTheoryCommand extends Command
{
    protected $signature = 'olympiad:repair-mathjax-theory {--dry-run : Show changes only} {--apply : Apply changes to database}';

    protected $description = 'Repairs broken MathJax LaTeX commands in course/chapter theory HTML fields, only inside math delimiters.';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $dryRun = (bool) $this->option('dry-run') || ! $apply;

        if ($dryRun && ! $this->option('dry-run') && ! $this->option('apply')) {
            $this->warn('No mode provided, running in dry-run mode by default.');
        }

        $targets = [
            'course_texts' => ['content_html', 'description_html'],
            'chapter_texts' => ['content_html', 'description_html', 'theory_html', 'examples_html'],
        ];

        $totals = [
            'rows_scanned' => 0,
            'fields_changed' => 0,
            'rows_changed' => 0,
        ];
        $examples = [];

        foreach ($targets as $table => $columnCandidates) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
                continue;
            }

            $columns = array_values(array_filter(
                $columnCandidates,
                fn (string $column): bool => Schema::hasColumn($table, $column)
            ));

            if ($columns === []) {
                $this->line("Skip {$table}: no target html columns found.");
                continue;
            }

            $query = DB::table($table)->select(array_merge(['id', 'lang'], $columns))->orderBy('id');

            $query->chunkById(200, function ($rows) use ($table, $columns, $apply, &$totals, &$examples): void {
                foreach ($rows as $row) {
                    $totals['rows_scanned']++;
                    $updates = [];

                    foreach ($columns as $column) {
                        $original = (string) ($row->{$column} ?? '');
                        if ($original === '') {
                            continue;
                        }

                        $repaired = $this->repairMathInHtml($original);
                        if ($repaired === $original) {
                            continue;
                        }

                        $updates[$column] = $repaired;
                        $totals['fields_changed']++;

                        if (count($examples) < 8) {
                            $examples[] = [
                                'table' => $table,
                                'id' => (int) $row->id,
                                'lang' => (string) ($row->lang ?? ''),
                                'column' => $column,
                                'before' => $this->trimPreview($original),
                                'after' => $this->trimPreview($repaired),
                            ];
                        }
                    }

                    if ($updates === []) {
                        continue;
                    }

                    $totals['rows_changed']++;

                    if ($apply) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            }, 'id');
        }

        $this->newLine();
        $this->info($dryRun ? 'DRY-RUN summary:' : 'APPLY summary:');
        $this->line('Rows scanned: ' . $totals['rows_scanned']);
        $this->line('Rows changed: ' . $totals['rows_changed']);
        $this->line('Fields changed: ' . $totals['fields_changed']);

        if ($examples !== []) {
            $this->newLine();
            $this->info('Examples:');
            foreach ($examples as $example) {
                $this->line("[{$example['table']}#{$example['id']} {$example['lang']} {$example['column']}]");
                $this->line('  BEFORE: ' . $example['before']);
                $this->line('  AFTER : ' . $example['after']);
            }
        }

        if ($apply) {
            $this->newLine();
            $this->info('Applied successfully.');
        } else {
            $this->newLine();
            $this->warn('No database changes were made. Run with --apply to persist.');
        }

        return self::SUCCESS;
    }

    private function repairMathInHtml(string $html): string
    {
        $html = (string) preg_replace_callback(
            '/(?:\\\\){1,2}\((.*?)(?:\\\\){1,2}\)/su',
            fn (array $matches): string => '\(' . $this->repairMathTokens($matches[1] ?? '') . '\)',
            $html
        );

        $html = (string) preg_replace_callback(
            '/(?:\\\\){1,2}\[(.*?)(?:\\\\){1,2}\]/su',
            fn (array $matches): string => '\[' . $this->repairMathTokens($matches[1] ?? '') . '\]',
            $html
        );

        $html = (string) preg_replace_callback(
            '/\$\$(.*?)\$\$/su',
            fn (array $matches): string => '$$' . $this->repairMathTokens($matches[1] ?? '') . '$$',
            $html
        );

        return (string) preg_replace_callback(
            '/(?<!\\\\)\$(.+?)(?<!\\\\)\$/su',
            fn (array $matches): string => '$' . $this->repairMathTokens($matches[1] ?? '') . '$',
            $html
        );
    }

    private function repairMathTokens(string $math): string
    {
        $commands = '(gcd|operatorname|prod|alpha|beta|tau|min|max|cdot|quad|mid|nmid|sqrt|Rightarrow|le|ge|ne|pmod)';

        $math = (string) preg_replace_callback(
            '/\\\\{2}(?=' . $commands . ')/u',
            fn (): string => '\\',
            $math
        );

        $patterns = [
            '/(?<!\\\\)operatorname\s*\{?\s*lcm\s*\}?/iu' => '\operatorname{lcm}',
            '/(?<!\\\\)operatornamelcm/iu' => '\operatorname{lcm}',
            '/(?<!\\\\)gcd(?=\s*\()/iu' => '\gcd',
            '/(?<!\\\\)min(?=\s*\()/iu' => '\min',
            '/(?<!\\\\)max(?=\s*\()/iu' => '\max',
            '/(?<![A-Za-z\\\\])prod\b/iu' => '\prod',
            '/(?<![A-Za-z\\\\])alpha(?=[^A-Za-z]|$)/iu' => '\alpha',
            '/(?<![A-Za-z\\\\])beta(?=[^A-Za-z]|$)/iu' => '\beta',
            '/(?<![A-Za-z\\\\])tau(?=[^A-Za-z]|$)/iu' => '\tau',
            '/(?<![A-Za-z\\\\])cdot\b/u' => '\cdot',
            '/(?<![A-Za-z\\\\])quad\b/u' => '\quad',
            '/(?<![A-Za-z\\\\])nmid\b/u' => '\nmid',
            '/(?<![A-Za-z\\\\])mid\b/u' => '\mid',
            '/(?<![A-Za-z\\\\])sqrt\b/u' => '\sqrt',
            '/(?<![A-Za-z\\\\])Rightarrow\b/u' => '\Rightarrow',
            '/(?<![A-Za-z\\\\])le\b/u' => '\le',
            '/(?<![A-Za-z\\\\])ge\b/u' => '\ge',
            '/(?<![A-Za-z\\\\])ne\b/u' => '\ne',
            '/(?<![A-Za-z\\\\])pmod\b/u' => '\pmod',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $math = (string) preg_replace_callback(
                $pattern,
                fn () => $replacement,
                $math
            );
        }

        return $math;
    }

    private function trimPreview(string $text): string
    {
        $oneLine = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $oneLine = trim($oneLine);

        if (mb_strlen($oneLine) <= 170) {
            return $oneLine;
        }

        return mb_substr($oneLine, 0, 167) . '...';
    }
}
