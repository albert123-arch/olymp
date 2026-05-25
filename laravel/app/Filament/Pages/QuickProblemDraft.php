<?php

namespace App\Filament\Pages;

use App\Models\Chapter;
use App\Models\Language;
use App\Models\Problem;
use App\Models\Tag;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use UnitEnum;

class QuickProblemDraft extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Quick Problem';

    protected static ?string $title = 'Quick Problem Draft';

    protected static ?string $slug = 'quick-problem';

    protected string $view = 'filament.pages.quick-problem-draft';

    public ?int $selectedCourseId = null;

    public ?int $selectedChapterId = null;

    public string $problemCode = '';

    public ?int $sortOrder = null;

    public int $difficulty = 1;

    public bool $isPublished = true;

    /** @var array<int> */
    public array $selectedTagIds = [];

    public string $newTags = '';

    public string $ruTitle = '';
    public string $ruBody = '';
    public string $ruHint = '';
    public string $ruSolution = '';

    public string $enTitle = '';
    public string $enBody = '';
    public string $enHint = '';
    public string $enSolution = '';

    public ?int $createdProblemId = null;

    public string $returnTo = '';

    public string $returnLang = 'ru';

    public string $returnTab = 'problems';

    public function mount(): void
    {
        $courseId = request()->integer('course_id');
        $chapterId = request()->integer('chapter_id');
        $lang = (string) request()->query('lang', 'ru');
        $tab = (string) request()->query('tab', 'problems');

        $this->selectedCourseId = $courseId > 0 ? $courseId : $this->defaultCourseId();
        $this->returnTo = (string) request()->query('return', '');
        $this->returnLang = in_array($lang, ['ru', 'en'], true) ? $lang : 'ru';
        $this->returnTab = $this->normalizeReturnTab($tab);
        $this->syncSelectedChapter($chapterId > 0 ? $chapterId : null);
        $this->refreshDefaults();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->syncSelectedChapter();
        $this->refreshDefaults(true);
    }

    public function updatedSelectedChapterId(): void
    {
        $this->refreshDefaults(true);
    }

    public function suggestCode(): void
    {
        if ($this->selectedChapterId === null) {
            return;
        }

        $this->problemCode = $this->suggestedCodeForChapter($this->selectedChapterId);
    }

    public function saveProblem(): void
    {
        $this->validate([
            'selectedCourseId' => ['required', 'integer', Rule::exists('courses', 'id')],
            'selectedChapterId' => ['required', 'integer', Rule::exists('chapters', 'id')],
            'problemCode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('problems', 'problem_code'),
            ],
            'sortOrder' => ['nullable', 'integer', 'min:0'],
            'difficulty' => ['required', 'integer', 'min:1', 'max:5'],
            'ruTitle' => ['required', 'string', 'max:255'],
            'ruBody' => ['required', 'string'],
            'ruHint' => ['nullable', 'string'],
            'ruSolution' => ['nullable', 'string'],
            'enTitle' => ['nullable', 'string', 'max:255'],
            'enBody' => ['nullable', 'string'],
            'enHint' => ['nullable', 'string'],
            'enSolution' => ['nullable', 'string'],
        ], [], [
            'ruTitle' => 'RU title',
            'ruBody' => 'RU body',
        ]);

        $chapter = Chapter::query()
            ->where('id', $this->selectedChapterId)
            ->where('course_id', $this->selectedCourseId)
            ->first();

        if (! $chapter) {
            Notification::make()->title('Chapter does not belong to selected course')->danger()->send();
            return;
        }

        $warnings = $this->collectWarnings();
        if (! empty($warnings)) {
            Notification::make()
                ->title('Validation warnings found')
                ->body('Please review warnings below before publishing broadly.')
                ->warning()
                ->send();
        }

        $sortOrder = $this->sortOrder ?? $this->nextSortOrder((int) $chapter->id);

        DB::transaction(function () use ($chapter, $sortOrder): void {
            $problem = Problem::query()->create([
                'chapter_id' => (int) $chapter->id,
                'problem_code' => trim($this->problemCode),
                'book_number' => null,
                'difficulty' => max(1, min(5, (int) $this->difficulty)),
                'problem_type' => 'mixed',
                'sort_order' => (int) $sortOrder,
                'is_published' => (bool) $this->isPublished,
            ]);

            $problem->texts()->create([
                'lang' => 'ru',
                'title' => $this->ruTitle,
                'statement_html' => $this->ruBody,
                'hint_html' => $this->ruHint,
                'solution_html' => $this->ruSolution,
            ]);

            if (filled($this->enTitle) || filled($this->enBody) || filled($this->enHint) || filled($this->enSolution)) {
                $problem->texts()->updateOrCreate(
                    ['lang' => 'en'],
                    [
                        'title' => $this->enTitle !== '' ? $this->enTitle : $this->ruTitle,
                        'statement_html' => $this->enBody,
                        'hint_html' => $this->enHint,
                        'solution_html' => $this->enSolution,
                    ]
                );
            }

            $tagIds = collect($this->selectedTagIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

            $newTagTokens = collect(explode(',', $this->newTags))
                ->map(fn (string $token): string => trim($token))
                ->filter()
                ->unique()
                ->values();

            foreach ($newTagTokens as $token) {
                $slug = Str::slug($token);
                if ($slug === '') {
                    continue;
                }

                $tag = Tag::query()->firstOrCreate(['slug' => $slug]);
                $this->ensureTagText($tag, 'ru', $token);
                $this->ensureTagText($tag, 'en', $token);
                $tagIds[] = (int) $tag->id;
            }

            if (! empty($tagIds)) {
                $problem->tags()->syncWithoutDetaching(array_values(array_unique($tagIds)));
            }

            $this->createdProblemId = (int) $problem->id;
        });

        Notification::make()->title('Problem saved')->success()->send();

        if ($this->returnTo === 'module-workspace') {
            $this->redirect($this->moduleWorkspaceUrl(), navigate: true);
            return;
        }

        $this->resetTextInputs();
        $this->selectedTagIds = [];
        $this->newTags = '';
        $this->refreshDefaults(true);
    }

    public function getViewData(): array
    {
        return [
            'courses' => $this->courseOptions(),
            'chapters' => $this->chapterOptions(),
            'tags' => $this->tagOptions(),
            'warnings' => $this->collectWarnings(),
            'contentStudioUrl' => url('/admin/content-studio') . ($this->selectedChapterId ? ('?chapter_id=' . $this->selectedChapterId) : ''),
            'backUrl' => $this->returnTo === 'module-workspace' ? $this->moduleWorkspaceUrl() : url('/admin/content-studio') . $this->contextQuery(),
            'backLabel' => $this->returnTo === 'module-workspace' ? 'Back to Module Workspace' : 'Back to Content Studio',
            'languages' => $this->availableLangCodes(),
        ];
    }

    private function normalizeReturnTab(string $tab): string
    {
        $tab = match ($tab) {
            'theory-editor' => 'theory',
            'examples-editor' => 'examples',
            'publish-checklist' => 'checklist',
            default => $tab,
        };

        return in_array($tab, ['overview', 'theory', 'examples', 'problems', 'ladders', 'checklist', 'preview'], true)
            ? $tab
            : 'problems';
    }

    private function moduleWorkspaceUrl(): string
    {
        return url('/admin/module-workspace') . $this->contextQuery(['tab' => $this->returnTab]);
    }

    private function contextQuery(array $extra = []): string
    {
        $params = [];
        if ($this->selectedCourseId !== null) {
            $params['course_id'] = $this->selectedCourseId;
        }
        if ($this->selectedChapterId !== null) {
            $params['chapter_id'] = $this->selectedChapterId;
        }
        $params['lang'] = $this->returnLang;
        $params = array_merge($params, $extra);

        return $params === [] ? '' : ('?' . http_build_query($params));
    }

    private function defaultCourseId(): ?int
    {
        $course = DB::table('courses')->orderBy('sort_order')->orderBy('id')->first();
        return $course ? (int) $course->id : null;
    }

    private function syncSelectedChapter(?int $preferredChapterId = null): void
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

        if ($preferredChapterId !== null && $chapterIds->contains($preferredChapterId)) {
            $this->selectedChapterId = $preferredChapterId;
            return;
        }

        if ($this->selectedChapterId === null || ! $chapterIds->contains($this->selectedChapterId)) {
            $this->selectedChapterId = (int) $chapterIds->first();
        }
    }

    private function refreshDefaults(bool $regenerateCode = false): void
    {
        if ($this->selectedChapterId === null) {
            return;
        }

        $this->sortOrder = $this->nextSortOrder($this->selectedChapterId);

        if ($regenerateCode || trim($this->problemCode) === '') {
            $this->problemCode = $this->suggestedCodeForChapter($this->selectedChapterId);
        }
    }

    private function nextSortOrder(int $chapterId): int
    {
        $max = Problem::query()->where('chapter_id', $chapterId)->max('sort_order');
        return is_numeric($max) ? ((int) $max + 1) : 1;
    }

    private function suggestedCodeForChapter(int $chapterId): string
    {
        $chapter = Chapter::query()->with('course')->find($chapterId);
        if (! $chapter) {
            return 'CH' . $chapterId . '-P001';
        }

        $coursePrefix = $this->coursePrefix((string) $chapter->course?->slug);
        $chapterSlug = (string) $chapter->slug;
        $moduleNumber = null;

        if (preg_match('/module[-_]?(\d{1,2})/i', $chapterSlug, $matches) === 1) {
            $moduleNumber = str_pad((string) (int) $matches[1], 2, '0', STR_PAD_LEFT);
        } elseif (preg_match('/(?:^|[-_])m(\d{1,2})(?:$|[-_])/i', $chapterSlug, $matches) === 1) {
            $moduleNumber = str_pad((string) (int) $matches[1], 2, '0', STR_PAD_LEFT);
        }

        if ($moduleNumber !== null) {
            $base = "{$coursePrefix}-M{$moduleNumber}-P";
            $existing = Problem::query()
                ->where('chapter_id', $chapterId)
                ->where('problem_code', 'like', $base . '%')
                ->pluck('problem_code');

            $maxNum = 0;
            foreach ($existing as $code) {
                if (preg_match('/-P(\d{3,4})$/', (string) $code, $m) === 1) {
                    $maxNum = max($maxNum, (int) $m[1]);
                }
            }
            return $base . str_pad((string) ($maxNum + 1), 3, '0', STR_PAD_LEFT);
        }

        $count = Problem::query()->where('chapter_id', $chapterId)->count() + 1;
        return 'CH' . $chapterId . '-P' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }

    private function coursePrefix(string $slug): string
    {
        return match ($slug) {
            'number-theory' => 'NT',
            'algebra' => 'ALG',
            'geometry' => 'GEO',
            'combinatorics' => 'COM',
            'inequalities' => 'INEQ',
            'functional-equations' => 'FE',
            default => strtoupper(substr(Str::slug($slug), 0, 3)) ?: 'CRS',
        };
    }

    /**
     * @return array<int, string>
     */
    private function courseOptions(): array
    {
        return Chapter::query()
            ->select('courses.id as course_id', 'courses.slug as course_slug')
            ->join('courses', 'courses.id', '=', 'chapters.course_id')
            ->groupBy('courses.id', 'courses.slug')
            ->orderBy('courses.slug')
            ->get()
            ->pluck('course_slug', 'course_id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function chapterOptions(): array
    {
        if ($this->selectedCourseId === null) {
            return [];
        }

        return Chapter::query()
            ->where('course_id', $this->selectedCourseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('slug', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function tagOptions(): array
    {
        $tags = Tag::query()
            ->with('texts')
            ->orderBy('slug')
            ->get();

        return $tags->mapWithKeys(function (Tag $tag): array {
            $ru = $tag->texts->firstWhere('lang', 'ru')?->title;
            $en = $tag->texts->firstWhere('lang', 'en')?->title;
            $label = $ru ?: ($en ?: $tag->slug);

            return [(int) $tag->id => $label . ' (' . $tag->slug . ')'];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    private function collectWarnings(): array
    {
        $fields = [
            'RU title' => $this->ruTitle,
            'RU body' => $this->ruBody,
            'RU hint' => $this->ruHint,
            'RU solution' => $this->ruSolution,
            'EN title' => $this->enTitle,
            'EN body' => $this->enBody,
            'EN hint' => $this->enHint,
            'EN solution' => $this->enSolution,
        ];

        $warnings = [];
        foreach ($fields as $label => $value) {
            if ($value === '') {
                continue;
            }

            if (preg_match('/&#\d{2,6};/u', $value) === 1) {
                $warnings[] = "{$label}: numeric HTML entities detected.";
            }
            if (preg_match('/Ð|Ñ|Ã|Â/u', $value) === 1) {
                $warnings[] = "{$label}: possible mojibake detected.";
            }
            if (str_contains($value, '$$')) {
                $warnings[] = "{$label}: use \\( ... \\) or \\[ ... \\], not $$ ... $$.";
            }
            if ($this->containsLikelyBrokenMath($value)) {
                $warnings[] = "{$label}: possible broken LaTeX command (cdot/sqrt/frac/binom without backslash).";
            }
        }

        return array_values(array_unique($warnings));
    }

    private function containsLikelyBrokenMath(string $value): bool
    {
        preg_match_all('/\\\\\\((.*?)\\\\\\)|\\\\\\[(.*?)\\\\\\]|\\$(.*?)\\$/su', $value, $matches, PREG_SET_ORDER);
        if (empty($matches)) {
            return false;
        }

        $patterns = [
            '/(?<!\\\\)\bcdot\b/u',
            '/(?<!\\\\)\bsqrt\b/u',
            '/(?<!\\\\)\bfrac\b/u',
            '/(?<!\\\\)\bbinom\b/u',
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

    private function ensureTagText(Tag $tag, string $lang, string $title): void
    {
        if (! $tag->texts()->where('lang', $lang)->exists()) {
            $tag->texts()->create([
                'lang' => $lang,
                'title' => $title,
            ]);
        }
    }

    private function resetTextInputs(): void
    {
        $this->ruTitle = '';
        $this->ruBody = '';
        $this->ruHint = '';
        $this->ruSolution = '';
        $this->enTitle = '';
        $this->enBody = '';
        $this->enHint = '';
        $this->enSolution = '';
    }

    /**
     * @return array<int, string>
     */
    private function availableLangCodes(): array
    {
        return Language::query()
            ->whereIn('code', ['ru', 'en'])
            ->orderBy('sort_order')
            ->pluck('code')
            ->values()
            ->all();
    }
}
