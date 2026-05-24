<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairLadderTranslationsCommand extends Command
{
    protected $signature = 'olympiad:repair-ladder-translations
        {--dry-run : Preview changes only}
        {--apply : Apply changes to database}';

    protected $description = 'Create/update RU/EN rows in problem_ladder_texts for ladder irrational-part-parity-invariant.';

    private const LADDER_SLUG = 'irrational-part-parity-invariant';

    public function handle(): int
    {
        if (! Schema::hasTable('problem_ladder_texts')) {
            $this->error('Table problem_ladder_texts is missing. Run migrations first.');
            return self::FAILURE;
        }

        $apply = (bool) $this->option('apply');
        $dryRun = (bool) $this->option('dry-run') || ! $apply;

        if (! $this->option('dry-run') && ! $this->option('apply')) {
            $this->warn('No mode provided, defaulting to --dry-run.');
        }

        $ladder = DB::table('problem_ladders')->where('slug', self::LADDER_SLUG)->first();
        if (! $ladder) {
            $this->error('Ladder not found: ' . self::LADDER_SLUG);
            return self::FAILURE;
        }

        $ruLanguageId = $this->findLanguageId('ru', 'Russian');
        $enLanguageId = $this->findLanguageId('en', 'English');
        if (! $ruLanguageId || ! $enLanguageId) {
            $this->error('Could not resolve ru/en language ids.');
            return self::FAILURE;
        }

        $this->info('Ladder found: ' . self::LADDER_SLUG . ' (id=' . $ladder->id . ')');
        $this->line('ru language id: ' . $ruLanguageId);
        $this->line('en language id: ' . $enLanguageId);

        $payloads = [
            'ru' => [
                'language_id' => $ruLanguageId,
                'title' => 'Иррациональная часть и инвариант чётности',
                'description' => 'Лестница задач, которая постепенно подводит к олимпиадной задаче о произведениях чисел вида \(1+k\sqrt2\) и одинаковых дробных частях.',
                'main_method' => 'Иррациональная часть, дробные части, чётность коэффициента при \(\sqrt2\)',
            ],
            'en' => [
                'language_id' => $enLanguageId,
                'title' => 'Irrational Part and Parity Invariant',
                'description' => 'A problem ladder that gradually leads students to an olympiad problem about products of numbers of the form \(1+k\sqrt2\) and equal fractional parts.',
                'main_method' => 'Irrational part, fractional parts, parity of the coefficient of \(\sqrt2\)',
            ],
        ];

        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        $runner = function () use ($ladder, $payloads, $dryRun, &$summary): void {
            foreach ($payloads as $langCode => $payload) {
                $existing = DB::table('problem_ladder_texts')
                    ->where('problem_ladder_id', (int) $ladder->id)
                    ->where('language_id', (int) $payload['language_id'])
                    ->first();

                if (! $existing) {
                    $summary['created']++;
                    $this->line("CREATE {$langCode} translation");
                    if (! $dryRun) {
                        DB::table('problem_ladder_texts')->insert([
                            'problem_ladder_id' => (int) $ladder->id,
                            'language_id' => (int) $payload['language_id'],
                            'title' => $payload['title'],
                            'description' => $payload['description'],
                            'main_method' => $payload['main_method'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    continue;
                }

                $needsUpdate = $this->needsUpdate($existing, $payload);
                if ($needsUpdate) {
                    $summary['updated']++;
                    $this->line("UPDATE {$langCode} translation");
                    if (! $dryRun) {
                        DB::table('problem_ladder_texts')
                            ->where('id', (int) $existing->id)
                            ->update([
                                'title' => $payload['title'],
                                'description' => $payload['description'],
                                'main_method' => $payload['main_method'],
                                'updated_at' => now(),
                            ]);
                    }
                } else {
                    $summary['skipped']++;
                    $this->line("SKIP {$langCode} translation (already up to date)");
                }
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
            $this->warn('No changes saved. Use --apply to persist.');
        } else {
            $this->info('Ladder translations repaired.');
        }

        return self::SUCCESS;
    }

    private function findLanguageId(string $code, string $titleFallback): ?int
    {
        $codeRow = DB::table('languages')->where('code', $code)->first();
        if ($codeRow) {
            return (int) $codeRow->id;
        }

        $titleRow = DB::table('languages')
            ->whereRaw('LOWER(title) = ?', [mb_strtolower($titleFallback, 'UTF-8')])
            ->first();

        return $titleRow ? (int) $titleRow->id : null;
    }

    private function needsUpdate(object $existing, array $payload): bool
    {
        return (string) ($existing->title ?? '') !== $payload['title']
            || (string) ($existing->description ?? '') !== $payload['description']
            || (string) ($existing->main_method ?? '') !== $payload['main_method'];
    }
}

