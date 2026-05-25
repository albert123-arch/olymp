<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ChapterResource;
use App\Filament\Resources\ChapterTextResource;
use App\Filament\Resources\ProblemLadderResource;
use App\Filament\Resources\ProblemLadderStepResource;
use App\Filament\Resources\ProblemResource;
use App\Filament\Resources\ProblemTextResource;
use App\Models\Chapter;
use App\Models\ChapterText;
use App\Models\Course;
use App\Models\Language;
use App\Models\Problem;
use App\Models\ProblemLadder;
use App\Models\ProblemLadderText;
use App\Models\Tag;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use UnitEnum;

class ContentStudio extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Content Studio';

    protected static ?string $slug = 'content-studio';

    protected string $view = 'filament.pages.content-studio';

    public ?int $selectedCourseId = null;

    public ?int $selectedChapterId = null;

    public string $selectedLang = 'ru';

    public string $activeTab = 'overview';

    /** @var array<int> */
    public array $selectedProblemIds = [];

    public ?int $bulkDifficulty = null;

    public string $bulkTags = '';

    public function mount(): void
    {
        $this->selectedLang = in_array($this->selectedLang, ['ru', 'en'], true) ? $this->selectedLang : 'ru';

        $course = Course::query()->orderBy('sort_order')->orderBy('id')->first();
        if ($course) {
            $this->selectedCourseId = (int) $course->id;
        }

        $this->syncSelectedChapter();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->syncSelectedChapter();
        $this->selectedProblemIds = [];
    }

    public function updatedSelectedChapterId(): void
    {
        $this->selectedProblemIds = [];
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, $this->tabs(), true)) {
            $this->activeTab = $tab;
        }
    }

    public function publishProblem(int $problemId): void
    {
        $this->updateProblemPublishState([$problemId], true);
    }

    public function unpublishProblem(int $problemId): void
    {
        $this->updateProblemPublishState([$problemId], false);
    }

    public function publishSelected(): void
    {
        $this->updateProblemPublishState($this->selectedProblemIds, true);
    }

    public function unpublishSelected(): void
    {
        $this->updateProblemPublishState($this->selectedProblemIds, false);
    }

    public function setDifficultySelected(): void
    {
        if ($this->selectedChapterId === null || empty($this->selectedProblemIds) || $this->bulkDifficulty === null) {
            Notification::make()->title('Select problems and a level')->warning()->send();
            return;
        }

        $level = max(1, min(5, (int) $this->bulkDifficulty));
        Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $this->selectedProblemIds)
            ->update(['difficulty' => $level]);

        Notification::make()->title('Difficulty updated')->success()->send();
    }

    public function applyTagsSelected(): void
    {
        if ($this->selectedChapterId === null || empty($this->selectedProblemIds)) {
            Notification::make()->title('Select problems first')->warning()->send();
            return;
        }

        $tokens = collect(explode(',', $this->bulkTags))
            ->map(fn (string $token): string => trim($token))
            ->filter()
            ->values();

        if ($tokens->isEmpty()) {
            Notification::make()->title('Enter tags, separated by commas')->warning()->send();
            return;
        }

        $tagIds = [];
        foreach ($tokens as $token) {
            $slug = Str::slug($token);
            if ($slug === '') {
                continue;
            }

            $tag = Tag::query()->firstOrCreate(['slug' => $slug]);

            // Keep tag texts simple and safe: only create if missing.
            $hasRu = $tag->texts()->where('lang', 'ru')->exists();
            if (! $hasRu) {
                $tag->texts()->create(['lang' => 'ru', 'title' => $token]);
            }

            $hasEn = $tag->texts()->where('lang', 'en')->exists();
            if (! $hasEn) {
                $tag->texts()->create(['lang' => 'en', 'title' => $token]);
            }

            $tagIds[] = (int) $tag->id;
        }

        if (empty($tagIds)) {
            Notification::make()->title('No valid tags parsed')->warning()->send();
            return;
        }

        $problems = Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $this->selectedProblemIds)
            ->get();

        foreach ($problems as $problem) {
            $problem->tags()->syncWithoutDetaching($tagIds);
        }

        Notification::make()->title('Tags attached')->success()->send();
    }

    protected function getViewData(): array
    {
        $courses = Course::query()
            ->with('texts')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $chapters = collect();
        if ($this->selectedCourseId !== null) {
            $chapters = Chapter::query()
                ->where('course_id', $this->selectedCourseId)
                ->with('texts')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $selectedCourse = $this->selectedCourseId ? $courses->firstWhere('id', $this->selectedCourseId) : null;
        $selectedChapter = $this->selectedChapterId ? $chapters->firstWhere('id', $this->selectedChapterId) : null;

        return [
            'tabs' => $this->tabs(),
            'courses' => $courses,
            'chapters' => $chapters,
            'selectedCourse' => $selectedCourse,
            'selectedChapter' => $selectedChapter,
            'languages' => $this->languageOptions(),
            'overview' => $this->overviewData($selectedCourse, $selectedChapter),
            'theory' => $this->chapterTextBlocks($selectedChapter, 'theory_html'),
            'examples' => $this->chapterTextBlocks($selectedChapter, 'examples_html'),
            'problems' => $this->problemRows($selectedChapter),
            'ladders' => $this->ladderRows($selectedChapter),
            'missing' => $this->missingTranslationsData($selectedChapter),
            'validation' => $this->validationWarnings($selectedChapter),
            'quickAddUrl' => url('/admin/quick-problem') . $this->quickAddQueryString(),
            'translationQueueUrl' => url('/admin/translation-queue') . $this->quickAddQueryString(),
        ];
    }

    /**
     * @return list<string>
     */
    private function tabs(): array
    {
        return [
            'overview',
            'theory',
            'examples',
            'problems',
            'ladders',
            'missing-translations',
            'validation',
        ];
    }

    private function syncSelectedChapter(): void
    {
        if ($this->selectedCourseId === null) {
            $this->selectedChapterId = null;
            return;
        }

        $chapterIds = Chapter::query()
            ->where('course_id', $this->selectedCourseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        if ($chapterIds->isEmpty()) {
            $this->selectedChapterId = null;
            return;
        }

        if ($this->selectedChapterId === null || ! $chapterIds->contains($this->selectedChapterId)) {
            $this->selectedChapterId = (int) $chapterIds->first();
        }
    }

    private function languageOptions(): array
    {
        $options = Language::query()
            ->whereIn('code', ['ru', 'en'])
            ->orderBy('sort_order')
            ->pluck('title', 'code')
            ->all();

        if (! isset($options['ru'])) {
            $options['ru'] = 'Russian';
        }
        if (! isset($options['en'])) {
            $options['en'] = 'English';
        }

        return $options;
    }

    private function overviewData(?Course $course, ?Chapter $chapter): array
    {
        if (! $course || ! $chapter) {
            return [
                'problems_total' => 0,
                'problems_published' => 0,
                'problems_unpublished' => 0,
                'ladders_total' => 0,
                'ru_theory' => false,
                'en_theory' => false,
                'ru_examples' => false,
                'en_examples' => false,
            ];
        }

        $problemsTotal = Problem::query()->where('chapter_id', $chapter->id)->count();
        $published = Problem::query()->where('chapter_id', $chapter->id)->where('is_published', true)->count();
        $ladders = ProblemLadder::query()->where('chapter_id', $chapter->id)->count();

        $ruText = ChapterText::query()->where('chapter_id', $chapter->id)->where('lang', 'ru')->first();
        $enText = ChapterText::query()->where('chapter_id', $chapter->id)->where('lang', 'en')->first();

        return [
            'problems_total' => $problemsTotal,
            'problems_published' => $published,
            'problems_unpublished' => $problemsTotal - $published,
            'ladders_total' => $ladders,
            'ru_theory' => filled($ruText?->theory_html),
            'en_theory' => filled($enText?->theory_html),
            'ru_examples' => filled($ruText?->examples_html),
            'en_examples' => filled($enText?->examples_html),
        ];
    }

    private function chapterTextBlocks(?Chapter $chapter, string $field): array
    {
        if (! $chapter) {
            return [];
        }

        $texts = ChapterText::query()->where('chapter_id', $chapter->id)->get()->keyBy('lang');

        return [
            'ru' => [
                'content' => (string) ($texts->get('ru')?->{$field} ?? ''),
                'edit_url' => $this->chapterTextEditUrl($chapter->id, 'ru', $texts->get('ru')?->id),
            ],
            'en' => [
                'content' => (string) ($texts->get('en')?->{$field} ?? ''),
                'edit_url' => $this->chapterTextEditUrl($chapter->id, 'en', $texts->get('en')?->id),
            ],
        ];
    }

    private function chapterTextEditUrl(int $chapterId, string $lang, ?int $textId): string
    {
        if ($textId !== null) {
            return ChapterTextResource::getUrl('edit', ['record' => $textId]);
        }

        return ChapterTextResource::getUrl('create', ['chapter_id' => $chapterId, 'lang' => $lang]);
    }

    private function problemRows(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [];
        }

        $problems = Problem::query()
            ->where('chapter_id', $chapter->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['texts', 'tags.texts'])
            ->get();

        return $problems->map(function (Problem $problem): array {
            $ruText = $problem->texts->firstWhere('lang', 'ru');
            $enText = $problem->texts->firstWhere('lang', 'en');
            $selected = $problem->texts->firstWhere('lang', $this->selectedLang) ?? $enText ?? $ruText ?? $problem->texts->first();

            $tagTitles = $problem->tags->map(function (Tag $tag): string {
                $text = $tag->texts->firstWhere('lang', $this->selectedLang)
                    ?? $tag->texts->firstWhere('lang', 'en')
                    ?? $tag->texts->firstWhere('lang', 'ru')
                    ?? $tag->texts->first();

                return (string) ($text?->title ?? $tag->slug);
            })->filter()->implode(', ');

            return [
                'id' => (int) $problem->id,
                'code' => (string) $problem->problem_code,
                'title' => (string) ($selected?->title ?? $problem->problem_code),
                'difficulty' => (int) $problem->difficulty,
                'is_published' => (bool) $problem->is_published,
                'has_ru' => $ruText !== null,
                'has_en' => $enText !== null,
                'tags' => $tagTitles,
                'sort_order' => (int) $problem->sort_order,
                'edit_problem_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
                'edit_ru_url' => $this->problemTextEditUrl((int) $problem->id, 'ru', $ruText?->id),
                'edit_en_url' => $this->problemTextEditUrl((int) $problem->id, 'en', $enText?->id),
            ];
        })->all();
    }

    private function problemTextEditUrl(int $problemId, string $lang, ?int $textId): string
    {
        if ($textId !== null) {
            return ProblemTextResource::getUrl('edit', ['record' => $textId]);
        }

        return ProblemTextResource::getUrl('create', ['problem_id' => $problemId, 'lang' => $lang]);
    }

    private function ladderRows(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [];
        }

        $ladders = ProblemLadder::query()
            ->where('chapter_id', $chapter->id)
            ->with(['texts.language', 'steps'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $ladders->map(function (ProblemLadder $ladder): array {
            $selectedText = $this->resolveLadderText($ladder->texts, $this->selectedLang);
            $hasRu = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'ru' && filled($text->title));
            $hasEn = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en' && filled($text->title));

            return [
                'id' => (int) $ladder->id,
                'title' => (string) ($selectedText?->title ?? $ladder->title),
                'main_method' => (string) ($selectedText?->main_method ?? $ladder->main_method ?? ''),
                'difficulty' => (int) max(1, min(5, (int) $ladder->difficulty_level)),
                'steps_count' => (int) $ladder->steps->count(),
                'is_published' => (bool) $ladder->is_published,
                'has_ru' => $hasRu,
                'has_en' => $hasEn,
                'edit_ladder_url' => ProblemLadderResource::getUrl('edit', ['record' => $ladder]),
                'edit_steps_url' => ProblemLadderStepResource::getUrl(),
            ];
        })->all();
    }

    private function resolveLadderText(EloquentCollection $texts, string $lang): ?ProblemLadderText
    {
        if ($texts->isEmpty()) {
            return null;
        }

        return $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === $lang)
            ?? $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en')
            ?? $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'ru')
            ?? $texts->first();
    }

    private function missingTranslationsData(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [
                'problems_ru_missing_en' => [],
                'problems_en_missing_ru' => [],
                'ladders_missing_en' => [],
                'chapter_missing_en' => [],
            ];
        }

        $problems = Problem::query()
            ->where('chapter_id', $chapter->id)
            ->with('texts')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $ruMissingEn = [];
        $enMissingRu = [];
        foreach ($problems as $problem) {
            $hasRu = $problem->texts->contains(fn ($text): bool => $text->lang === 'ru' && filled($text->statement_html));
            $hasEn = $problem->texts->contains(fn ($text): bool => $text->lang === 'en' && filled($text->statement_html));

            if ($hasRu && ! $hasEn) {
                $ruMissingEn[] = ['code' => $problem->problem_code, 'id' => (int) $problem->id];
            }
            if ($hasEn && ! $hasRu) {
                $enMissingRu[] = ['code' => $problem->problem_code, 'id' => (int) $problem->id];
            }
        }

        $ladders = ProblemLadder::query()
            ->where('chapter_id', $chapter->id)
            ->with('texts.language')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $laddersMissingEn = $ladders
            ->filter(function (ProblemLadder $ladder): bool {
                return ! $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en' && filled($text->title));
            })
            ->map(fn (ProblemLadder $ladder): array => [
                'id' => (int) $ladder->id,
                'slug' => (string) $ladder->slug,
                'title' => (string) $ladder->title,
            ])->values()->all();

        $enChapterText = ChapterText::query()->where('chapter_id', $chapter->id)->where('lang', 'en')->first();
        $chapterMissing = [];
        if (! $enChapterText || ! filled($enChapterText->theory_html)) {
            $chapterMissing[] = 'EN theory missing';
        }
        if (! $enChapterText || ! filled($enChapterText->examples_html)) {
            $chapterMissing[] = 'EN examples missing';
        }

        return [
            'problems_ru_missing_en' => $ruMissingEn,
            'problems_en_missing_ru' => $enMissingRu,
            'ladders_missing_en' => $laddersMissingEn,
            'chapter_missing_en' => $chapterMissing,
        ];
    }

    private function validationWarnings(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [];
        }

        $warnings = [];

        $problemRows = Problem::query()
            ->where('chapter_id', $chapter->id)
            ->with('texts')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $chapterTexts = ChapterText::query()->where('chapter_id', $chapter->id)->get();
        $ladderTexts = ProblemLadderText::query()
            ->whereIn('problem_ladder_id', ProblemLadder::query()->where('chapter_id', $chapter->id)->pluck('id'))
            ->get();

        $containsEntity = fn (?string $value): bool => is_string($value) && preg_match('/&#\d{2,6};/u', $value) === 1;
        $containsMojibake = fn (?string $value): bool => is_string($value) && preg_match('/Ð|Ñ|Ã|Â/u', $value) === 1;
        $containsDoubleDollar = fn (?string $value): bool => is_string($value) && str_contains($value, '$$');

        foreach ($chapterTexts as $text) {
            foreach (['description_html', 'theory_html', 'examples_html'] as $field) {
                $value = $text->{$field};
                if ($containsEntity($value)) {
                    $warnings[] = "ChapterText #{$text->id} ({$text->lang}, {$field}) has numeric HTML entities.";
                }
                if ($containsMojibake($value)) {
                    $warnings[] = "ChapterText #{$text->id} ({$text->lang}, {$field}) has possible mojibake.";
                }
                if ($containsDoubleDollar($value)) {
                    $warnings[] = "ChapterText #{$text->id} ({$text->lang}, {$field}) uses $$ math delimiter.";
                }
                if ($this->hasLikelyBrokenMathCommand($value)) {
                    $warnings[] = "ChapterText #{$text->id} ({$text->lang}, {$field}) may contain broken LaTeX commands.";
                }
            }
        }

        foreach ($problemRows as $problem) {
            if ($problem->texts->isEmpty()) {
                $warnings[] = "Problem {$problem->problem_code} has no text rows.";
            }

            foreach ($problem->texts as $text) {
                foreach (['statement_html', 'hint_html', 'solution_html'] as $field) {
                    $value = $text->{$field};
                    if (! filled($value)) {
                        continue;
                    }
                    if ($containsEntity($value)) {
                        $warnings[] = "ProblemText #{$text->id} ({$problem->problem_code}, {$text->lang}, {$field}) has numeric HTML entities.";
                    }
                    if ($containsMojibake($value)) {
                        $warnings[] = "ProblemText #{$text->id} ({$problem->problem_code}, {$text->lang}, {$field}) has possible mojibake.";
                    }
                    if ($containsDoubleDollar($value)) {
                        $warnings[] = "ProblemText #{$text->id} ({$problem->problem_code}, {$text->lang}, {$field}) uses $$ math delimiter.";
                    }
                    if ($this->hasLikelyBrokenMathCommand($value)) {
                        $warnings[] = "ProblemText #{$text->id} ({$problem->problem_code}, {$text->lang}, {$field}) may contain broken LaTeX commands.";
                    }
                }
            }
        }

        foreach ($ladderTexts as $text) {
            foreach (['title', 'description', 'main_method'] as $field) {
                $value = $text->{$field};
                if (! filled($value)) {
                    continue;
                }
                if ($containsEntity($value)) {
                    $warnings[] = "LadderText #{$text->id} ({$field}) has numeric HTML entities.";
                }
                if ($containsMojibake($value)) {
                    $warnings[] = "LadderText #{$text->id} ({$field}) has possible mojibake.";
                }
                if ($containsDoubleDollar($value)) {
                    $warnings[] = "LadderText #{$text->id} ({$field}) uses $$ math delimiter.";
                }
            }
        }

        $duplicateCodes = Problem::query()
            ->where('chapter_id', $chapter->id)
            ->whereNotNull('problem_code')
            ->where('problem_code', '!=', '')
            ->selectRaw('problem_code, COUNT(*) as c')
            ->groupBy('problem_code')
            ->having('c', '>', 1)
            ->get();

        foreach ($duplicateCodes as $row) {
            $warnings[] = "Duplicate problem_code in chapter: {$row->problem_code} ({$row->c} rows).";
        }

        $duplicateTexts = \DB::table('problem_texts')
            ->join('problems', 'problems.id', '=', 'problem_texts.problem_id')
            ->where('problems.chapter_id', $chapter->id)
            ->selectRaw('problem_texts.problem_id, problem_texts.lang, COUNT(*) as c')
            ->groupBy('problem_texts.problem_id', 'problem_texts.lang')
            ->having('c', '>', 1)
            ->get();

        foreach ($duplicateTexts as $row) {
            $problemCode = $problemRows->firstWhere('id', $row->problem_id)?->problem_code ?? ('ID ' . $row->problem_id);
            $warnings[] = "Duplicate problem_texts for {$problemCode} / {$row->lang} ({$row->c} rows).";
        }

        $unpublishedCount = $problemRows->where('is_published', false)->count();
        if ($unpublishedCount > 0) {
            $warnings[] = "Unpublished problems in this chapter: {$unpublishedCount}.";
        }

        return array_values(array_unique($warnings));
    }

    private function hasLikelyBrokenMathCommand(?string $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        preg_match_all('/\\\\\\((.*?)\\\\\\)|\\\\\\[(.*?)\\\\\\]|\\$(.*?)\\$/su', $value, $matches, PREG_SET_ORDER);
        if (empty($matches)) {
            return false;
        }

        $patterns = [
            '/(?<!\\\\)\bcdot\b/u',
            '/(?<!\\\\)\bsqrt\b/u',
            '/(?<!\\\\)\bfrac\b/u',
            '/(?<!\\\\)\boperatorname\b/u',
        ];

        foreach ($matches as $match) {
            $math = '';
            for ($i = 1; $i <= 3; $i++) {
                if (! empty($match[$i])) {
                    $math = $match[$i];
                    break;
                }
            }

            if ($math === '') {
                continue;
            }

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $math) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<int>  $ids
     */
    private function updateProblemPublishState(array $ids, bool $published): void
    {
        if ($this->selectedChapterId === null || empty($ids)) {
            Notification::make()->title('No problems selected')->warning()->send();
            return;
        }

        $count = Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $ids)
            ->update(['is_published' => $published]);

        Notification::make()
            ->title($published ? 'Published' : 'Unpublished')
            ->body("Updated {$count} problem(s).")
            ->success()
            ->send();
    }

    private function quickAddQueryString(): string
    {
        $params = [];
        if ($this->selectedCourseId !== null) {
            $params['course_id'] = $this->selectedCourseId;
        }
        if ($this->selectedChapterId !== null) {
            $params['chapter_id'] = $this->selectedChapterId;
        }

        if ($params === []) {
            return '';
        }

        return '?' . http_build_query($params);
    }
}
