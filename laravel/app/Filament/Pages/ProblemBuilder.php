<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ProblemResource;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\Problem;
use App\Models\ProblemMedia;
use App\Models\Tag;
use App\Support\ProblemMediaUpload;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use UnitEnum;

class ProblemBuilder extends Page
{
    use WithFileUploads;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Problem Builder';

    protected static ?string $title = 'Problem Builder';

    protected static ?string $slug = 'problem-builder';

    protected string $view = 'filament.pages.problem-builder';

    public ?int $problemId = null;

    public ?int $selectedCourseId = null;

    public ?int $selectedChapterId = null;

    public string $problemCode = '';

    public ?int $sortOrder = null;

    public int $difficulty = 1;

    public bool $isPublished = true;

    /** @var array<int> */
    public array $selectedTagIds = [];

    /** @var array<int> */
    public array $selectedGradeIds = [];

    public string $newTags = '';

    public string $sourceName = '';
    public string|int|null $sourceYear = null;
    public string $sourceRound = '';
    public string $sourceGrade = '';
    public string $sourceProblemNumber = '';
    public string $sourceUrl = '';
    public string $sourceNote = '';

    public string $ruTitle = '';
    public string $ruStatement = '';
    public string $ruHint = '';
    public string $ruSolution = '';
    public string $ruTeacherNote = '';

    public string $enTitle = '';
    public string $enStatement = '';
    public string $enHint = '';
    public string $enSolution = '';
    public string $enTeacherNote = '';

    /** @var array<int, mixed> */
    public array $statementUploads = [];

    /** @var array<int, mixed> */
    public array $hintUploads = [];

    /** @var array<int, mixed> */
    public array $solutionUploads = [];

    /** @var array<int, mixed> */
    public array $extraUploads = [];

    /** @var array<int, array<string, mixed>> */
    public array $mediaRows = [];

    public string $returnTo = '';

    public string $returnLang = 'ru';

    public string $returnTab = 'problems';

    public function mount(): void
    {
        $this->problemId = request()->integer('problem_id') ?: null;
        $this->returnTo = (string) request()->query('return', '');
        $lang = (string) request()->query('lang', 'ru');
        $this->returnLang = in_array($lang, ['ru', 'en'], true) ? $lang : 'ru';
        $this->returnTab = $this->normalizeReturnTab((string) request()->query('tab', 'problems'));

        if ($this->problemId !== null) {
            $this->loadProblem($this->problemId);
            return;
        }

        $courseId = request()->integer('course_id') ?: null;
        $chapterId = request()->integer('chapter_id') ?: null;

        $this->selectedCourseId = $courseId ?: $this->defaultCourseId();
        $this->syncSelectedChapter($chapterId);
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
        if ($this->selectedChapterId !== null && $this->problemId === null) {
            $this->problemCode = $this->suggestedCodeForChapter($this->selectedChapterId);
        }
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
                Rule::unique('problems', 'problem_code')->ignore($this->problemId),
            ],
            'sortOrder' => ['nullable', 'integer', 'min:0'],
            'difficulty' => ['required', 'integer', 'min:1', 'max:5'],
            'sourceName' => ['nullable', 'string', 'max:255'],
            'sourceYear' => ['nullable', 'integer', 'min:1800', 'max:2200'],
            'sourceRound' => ['nullable', 'string', 'max:100'],
            'sourceGrade' => ['nullable', 'string', 'max:50'],
            'sourceProblemNumber' => ['nullable', 'string', 'max:50'],
            'sourceUrl' => ['nullable', 'url', 'max:500'],
            'sourceNote' => ['nullable', 'string'],
            'ruTitle' => ['required', 'string', 'max:255'],
            'ruStatement' => ['required', 'string'],
            'enTitle' => ['nullable', 'string', 'max:255'],
        ]);

        $chapter = Chapter::query()
            ->where('id', $this->selectedChapterId)
            ->where('course_id', $this->selectedCourseId)
            ->first();

        if (! $chapter) {
            Notification::make()->title('Chapter does not belong to selected course')->danger()->send();
            return;
        }

        if ($this->isPublished && ! filled($this->ruStatement)) {
            Notification::make()->title('Published problems need a RU statement')->danger()->send();
            return;
        }

        $problem = null;

        DB::transaction(function () use ($chapter, &$problem): void {
            $payload = [
                'chapter_id' => (int) $chapter->id,
                'problem_code' => trim($this->problemCode),
                'difficulty' => max(1, min(5, (int) $this->difficulty)),
                'problem_type' => 'mixed',
                'sort_order' => (int) ($this->sortOrder ?? $this->nextSortOrder((int) $chapter->id)),
                'is_published' => (bool) $this->isPublished,
            ];

            if (Schema::hasColumn('problems', 'source_name')) {
                $payload = array_merge($payload, [
                    'source_name' => $this->blankToNull($this->sourceName),
                    'source_year' => $this->blankToNull($this->sourceYear),
                    'source_round' => $this->blankToNull($this->sourceRound),
                    'source_grade' => $this->blankToNull($this->sourceGrade),
                    'source_problem_number' => $this->blankToNull($this->sourceProblemNumber),
                    'source_url' => $this->blankToNull($this->sourceUrl),
                    'source_note' => $this->blankToNull($this->sourceNote),
                ]);
            }

            $problem = $this->problemId
                ? tap(Problem::query()->findOrFail($this->problemId))->update($payload)
                : Problem::query()->create(array_merge(['book_number' => null], $payload));

            $this->problemId = (int) $problem->id;

            $this->saveProblemText($problem, 'ru', [
                'title' => $this->ruTitle,
                'statement_html' => $this->ruStatement,
                'hint_html' => $this->ruHint,
                'solution_html' => $this->ruSolution,
                'teacher_note_html' => $this->ruTeacherNote,
            ]);

            if ($this->hasAnyEnglishText()) {
                $this->saveProblemText($problem, 'en', [
                    'title' => $this->enTitle !== '' ? $this->enTitle : $this->ruTitle,
                    'statement_html' => $this->enStatement,
                    'hint_html' => $this->enHint,
                    'solution_html' => $this->enSolution,
                    'teacher_note_html' => $this->enTeacherNote,
                ]);
            }

            $this->syncTags($problem);
            $this->syncGrades($problem);
            $this->saveMediaRows($problem);
        });

        if ($problem instanceof Problem) {
            $this->storePendingUploads($problem);
            $this->loadMediaRows($problem);
        }

        $this->resetUploadInputs();

        Notification::make()
            ->title('Problem saved')
            ->body('Texts, tags, grades, source metadata, and media rows were saved.')
            ->success()
            ->send();
    }

    public function deleteMedia(int $mediaId): void
    {
        if ($this->problemId === null) {
            return;
        }

        $media = ProblemMedia::query()
            ->where('problem_id', $this->problemId)
            ->where('id', $mediaId)
            ->first();

        if (! $media) {
            return;
        }

        DB::transaction(function () use ($media): void {
            $media->texts()->delete();
            ProblemMediaUpload::deleteMediaFileIfSafe($media);
            $media->delete();
        });

        $problem = Problem::query()->with('media.texts')->find($this->problemId);
        if ($problem) {
            $this->loadMediaRows($problem);
        }

        Notification::make()->title('Media deleted')->success()->send();
    }

    protected function getViewData(): array
    {
        $problem = $this->problemId ? Problem::query()->find($this->problemId) : null;

        return [
            'courses' => $this->courseOptions(),
            'chapters' => $this->chapterOptions(),
            'tags' => $this->tagOptions(),
            'grades' => $this->gradeOptions(),
            'hasGradeLevels' => Schema::hasTable('grade_levels'),
            'hasSourceMetadata' => Schema::hasColumn('problems', 'source_name'),
            'roleLabels' => ProblemMediaUpload::roleLabels(),
            'warnings' => $this->collectWarnings(),
            'problemEditUrl' => $problem ? ProblemResource::getUrl('edit', ['record' => $problem]) : null,
            'backUrl' => $this->backUrl(),
            'backLabel' => $this->returnTo === 'module-workspace' ? 'Back to Module Workspace' : 'Back to Content Studio',
        ];
    }

    private function loadProblem(int $problemId): void
    {
        $problem = Problem::query()
            ->with(['chapter.course', 'texts', 'tags', 'gradeLevels', 'media.texts'])
            ->findOrFail($problemId);

        $this->selectedChapterId = (int) $problem->chapter_id;
        $this->selectedCourseId = (int) $problem->chapter?->course_id;
        $this->problemCode = (string) $problem->problem_code;
        $this->sortOrder = (int) $problem->sort_order;
        $this->difficulty = (int) ($problem->difficulty ?? 1);
        $this->isPublished = (bool) $problem->is_published;

        $this->sourceName = (string) ($problem->source_name ?? '');
        $this->sourceYear = $problem->source_year;
        $this->sourceRound = (string) ($problem->source_round ?? '');
        $this->sourceGrade = (string) ($problem->source_grade ?? '');
        $this->sourceProblemNumber = (string) ($problem->source_problem_number ?? '');
        $this->sourceUrl = (string) ($problem->source_url ?? '');
        $this->sourceNote = (string) ($problem->source_note ?? '');

        $ru = $problem->texts->firstWhere('lang', 'ru');
        $en = $problem->texts->firstWhere('lang', 'en');

        $this->ruTitle = (string) ($ru?->title ?? '');
        $this->ruStatement = (string) ($ru?->statement_html ?? '');
        $this->ruHint = (string) ($ru?->hint_html ?? '');
        $this->ruSolution = (string) ($ru?->solution_html ?? '');
        $this->ruTeacherNote = (string) ($ru?->teacher_note_html ?? '');

        $this->enTitle = (string) ($en?->title ?? '');
        $this->enStatement = (string) ($en?->statement_html ?? '');
        $this->enHint = (string) ($en?->hint_html ?? '');
        $this->enSolution = (string) ($en?->solution_html ?? '');
        $this->enTeacherNote = (string) ($en?->teacher_note_html ?? '');

        $this->selectedTagIds = $problem->tags->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->selectedGradeIds = Schema::hasTable('grade_levels')
            ? $problem->gradeLevels->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];

        $this->loadMediaRows($problem);
    }

    private function loadMediaRows(Problem $problem): void
    {
        $this->mediaRows = $problem->media
            ->mapWithKeys(function (ProblemMedia $media): array {
                $ru = $media->texts->firstWhere('lang', 'ru');
                $en = $media->texts->firstWhere('lang', 'en');

                return [
                    (int) $media->id => [
                        'id' => (int) $media->id,
                        'role' => ProblemMediaUpload::normalizeRole($media->role),
                        'lang' => (string) ($media->lang ?? ''),
                        'file_path' => (string) $media->file_path,
                        'original_name' => (string) $media->original_name,
                        'mime_type' => (string) $media->mime_type,
                        'file_size' => (int) $media->file_size,
                        'sort_order' => (int) ($media->sort_order ?? 0),
                        'is_published' => (bool) $media->is_published,
                        'caption_ru' => (string) ($ru?->caption_html ?? ''),
                        'alt_ru' => (string) ($ru?->alt_text ?? ''),
                        'caption_en' => (string) ($en?->caption_html ?? ''),
                        'alt_en' => (string) ($en?->alt_text ?? ''),
                    ],
                ];
            })
            ->all();
    }

    private function saveProblemText(Problem $problem, string $lang, array $payload): void
    {
        $problem->texts()->updateOrCreate(['lang' => $lang], $payload);
    }

    private function syncTags(Problem $problem): void
    {
        $tagIds = collect($this->selectedTagIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

        $newTags = collect(explode(',', $this->newTags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values();

        foreach ($newTags as $title) {
            $slug = Str::slug($title);
            if ($slug === '') {
                continue;
            }

            $tag = Tag::query()->firstOrCreate(['slug' => $slug]);
            $this->ensureTagText($tag, 'ru', $title);
            $this->ensureTagText($tag, 'en', $title);
            $tagIds[] = (int) $tag->id;
        }

        $problem->tags()->sync(array_values(array_unique($tagIds)));
    }

    private function syncGrades(Problem $problem): void
    {
        if (! Schema::hasTable('grade_levels')) {
            return;
        }

        $gradeIds = collect($this->selectedGradeIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $problem->gradeLevels()->sync($gradeIds);
    }

    private function saveMediaRows(Problem $problem): void
    {
        foreach ($this->mediaRows as $mediaId => $row) {
            $media = ProblemMedia::query()
                ->where('problem_id', $problem->id)
                ->where('id', (int) $mediaId)
                ->first();

            if (! $media) {
                continue;
            }

            $media->update([
                'role' => ProblemMediaUpload::normalizeRole((string) ($row['role'] ?? 'statement')),
                'lang' => $this->blankToNull($row['lang'] ?? null),
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'is_published' => (bool) ($row['is_published'] ?? false),
            ]);

            $this->saveMediaText($media, 'ru', (string) ($row['caption_ru'] ?? ''), (string) ($row['alt_ru'] ?? ''));
            $this->saveMediaText($media, 'en', (string) ($row['caption_en'] ?? ''), (string) ($row['alt_en'] ?? ''));
        }
    }

    private function saveMediaText(ProblemMedia $media, string $lang, string $caption, string $alt): void
    {
        $existing = $media->texts()->where('lang', $lang)->exists();
        if (! $existing && ! filled($caption) && ! filled($alt)) {
            return;
        }

        $media->texts()->updateOrCreate(
            ['lang' => $lang],
            ['caption_html' => $caption, 'alt_text' => $alt]
        );
    }

    private function storePendingUploads(Problem $problem): void
    {
        foreach ([
            'statement' => $this->statementUploads,
            'hint' => $this->hintUploads,
            'solution' => $this->solutionUploads,
            'extra' => $this->extraUploads,
        ] as $role => $files) {
            foreach ((array) $files as $file) {
                try {
                    ProblemMediaUpload::storeUploadedFile($file, $problem, $role);
                } catch (\Throwable $exception) {
                    Notification::make()
                        ->title('Media upload skipped')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            }
        }
    }

    private function collectWarnings(): array
    {
        $warnings = [];
        if (! filled($this->ruTitle)) {
            $warnings[] = 'Missing RU title.';
        }
        if (! filled($this->ruStatement)) {
            $warnings[] = 'Missing RU statement.';
        }
        if (! filled($this->enTitle) || ! filled($this->enStatement)) {
            $warnings[] = 'EN title or statement is missing.';
        }
        if (! filled($this->ruSolution) && ! filled($this->enSolution)) {
            $warnings[] = 'No solution is filled yet.';
        }

        foreach ($this->textFieldsForWarnings() as $label => $value) {
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
                $warnings[] = "{$label}: possible broken LaTeX command.";
            }
        }

        foreach ($this->mediaRows as $row) {
            if (empty($row['role'])) {
                $warnings[] = 'Media row has no role.';
            }
            if (empty($row['file_path'])) {
                $warnings[] = 'Media row has no file path.';
            }
            if (! filled($row['caption_ru'] ?? '') && ! filled($row['alt_ru'] ?? '') && ! filled($row['caption_en'] ?? '') && ! filled($row['alt_en'] ?? '')) {
                $warnings[] = 'Media without RU/EN caption or alt text: '.($row['original_name'] ?? $row['file_path'] ?? 'media');
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

        foreach ($matches as $match) {
            $math = collect([$match[1] ?? '', $match[2] ?? '', $match[3] ?? ''])->first(fn ($item): bool => $item !== '');
            if (! is_string($math) || $math === '') {
                continue;
            }

            if (preg_match('/(?<!\\\\)\b(cdot|sqrt|frac|binom)\b/u', $math) === 1) {
                return true;
            }
        }

        return false;
    }

    private function textFieldsForWarnings(): array
    {
        return [
            'RU title' => $this->ruTitle,
            'RU statement' => $this->ruStatement,
            'RU hint' => $this->ruHint,
            'RU solution' => $this->ruSolution,
            'RU teacher note' => $this->ruTeacherNote,
            'EN title' => $this->enTitle,
            'EN statement' => $this->enStatement,
            'EN hint' => $this->enHint,
            'EN solution' => $this->enSolution,
            'EN teacher note' => $this->enTeacherNote,
        ];
    }

    private function hasAnyEnglishText(): bool
    {
        return filled($this->enTitle)
            || filled($this->enStatement)
            || filled($this->enHint)
            || filled($this->enSolution)
            || filled($this->enTeacherNote);
    }

    private function resetUploadInputs(): void
    {
        $this->statementUploads = [];
        $this->hintUploads = [];
        $this->solutionUploads = [];
        $this->extraUploads = [];
    }

    private function defaultCourseId(): ?int
    {
        $course = Course::query()->orderBy('sort_order')->orderBy('id')->first();

        return $course ? (int) $course->id : null;
    }

    private function syncSelectedChapter(?int $preferredChapterId = null): void
    {
        if ($this->selectedCourseId === null) {
            $this->selectedChapterId = null;
            return;
        }

        $ids = Chapter::query()
            ->where('course_id', $this->selectedCourseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->selectedChapterId = null;
            return;
        }

        if ($preferredChapterId !== null && $ids->contains($preferredChapterId)) {
            $this->selectedChapterId = $preferredChapterId;
            return;
        }

        if ($this->selectedChapterId === null || ! $ids->contains($this->selectedChapterId)) {
            $this->selectedChapterId = (int) $ids->first();
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
            return 'CH'.$chapterId.'-P001';
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
                ->where('problem_code', 'like', $base.'%')
                ->pluck('problem_code');

            $maxNum = 0;
            foreach ($existing as $code) {
                if (preg_match('/-P(\d{3,4})$/', (string) $code, $match) === 1) {
                    $maxNum = max($maxNum, (int) $match[1]);
                }
            }

            return $base.str_pad((string) ($maxNum + 1), 3, '0', STR_PAD_LEFT);
        }

        return 'CH'.$chapterId.'-P'.str_pad((string) $this->nextSortOrder($chapterId), 3, '0', STR_PAD_LEFT);
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

    private function courseOptions(): array
    {
        return Course::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('slug', 'id')
            ->all();
    }

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

    private function tagOptions(): array
    {
        return Tag::query()
            ->with('texts')
            ->orderBy('slug')
            ->get()
            ->mapWithKeys(function (Tag $tag): array {
                $title = $tag->texts->firstWhere('lang', 'ru')?->title
                    ?? $tag->texts->firstWhere('lang', 'en')?->title
                    ?? $tag->slug;

                return [(int) $tag->id => $title.' ('.$tag->slug.')'];
            })
            ->all();
    }

    private function gradeOptions(): array
    {
        if (! Schema::hasTable('grade_levels')) {
            return [];
        }

        return GradeLevel::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('grade_number')
            ->get()
            ->mapWithKeys(fn (GradeLevel $grade): array => [(int) $grade->id => $grade->title_en.' / '.$grade->title_ru])
            ->all();
    }

    private function ensureTagText(Tag $tag, string $lang, string $title): void
    {
        if (! $tag->texts()->where('lang', $lang)->exists()) {
            $tag->texts()->create(['lang' => $lang, 'title' => $title]);
        }
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeReturnTab(string $tab): string
    {
        return in_array($tab, ['overview', 'theory', 'examples', 'problems', 'ladders', 'checklist', 'preview'], true)
            ? $tab
            : 'problems';
    }

    private function backUrl(): string
    {
        if ($this->returnTo === 'module-workspace') {
            return url('/admin/module-workspace').$this->contextQuery(['tab' => $this->returnTab]);
        }

        return url('/admin/content-studio').$this->contextQuery();
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

        return '?'.http_build_query($params);
    }
}
