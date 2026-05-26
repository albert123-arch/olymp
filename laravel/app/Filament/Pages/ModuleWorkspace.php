<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ChapterTextResource;
use App\Filament\Resources\ProblemLadderResource;
use App\Filament\Resources\ProblemResource;
use App\Filament\Resources\ProblemTextResource;
use App\Models\Chapter;
use App\Models\ChapterText;
use App\Models\Course;
use App\Models\GradeLevel;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class ModuleWorkspace extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Module Workspace';

    protected static ?string $title = 'Module Workspace';

    protected static ?string $slug = 'module-workspace';

    protected string $view = 'filament.pages.module-workspace';

    #[Url(as: 'course_id', except: null)]
    public ?int $selectedCourseId = null;

    #[Url(as: 'chapter_id', except: null)]
    public ?int $selectedChapterId = null;

    #[Url(as: 'lang', except: 'ru')]
    public string $selectedLang = 'ru';

    #[Url(as: 'tab', except: 'overview')]
    public string $activeTab = 'overview';

    public string $theoryRu = '';
    public string $theoryEn = '';
    public string $examplesRu = '';
    public string $examplesEn = '';

    /** @var array<int> */
    public array $selectedProblemIds = [];
    public ?int $bulkDifficulty = null;
    public ?int $bulkSortStart = null;
    public ?int $selectedGradeFilter = null;
    /** @var array<int> */
    public array $bulkGradeIds = [];

    public function mount(): void
    {
        $courseId = request()->integer('course_id');
        $chapterId = request()->integer('chapter_id');
        $lang = (string) request()->query('lang', $this->selectedLang);
        $tab = (string) request()->query('tab', $this->activeTab);

        $this->selectedLang = in_array($lang, ['ru', 'en'], true) ? $lang : 'ru';
        $this->activeTab = $this->normalizeTab($tab);

        $defaultCourse = Course::query()->orderBy('sort_order')->orderBy('id')->first();
        $this->selectedCourseId = $courseId > 0 ? $courseId : ($defaultCourse ? (int) $defaultCourse->id : null);
        $this->syncSelectedChapter($chapterId > 0 ? $chapterId : null);
        $this->loadChapterEditorValues();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->syncSelectedChapter();
        $this->selectedProblemIds = [];
        $this->loadChapterEditorValues();
    }

    public function updatedSelectedChapterId(): void
    {
        $this->selectedProblemIds = [];
        $this->loadChapterEditorValues();
    }

    public function updatedSelectedLang(): void
    {
        if (! in_array($this->selectedLang, ['ru', 'en'], true)) {
            $this->selectedLang = 'ru';
        }
    }

    public function setActiveTab(string $tab): void
    {
        $tab = $this->normalizeTab($tab);

        if (in_array($tab, $this->tabs(), true)) {
            $this->activeTab = $tab;
        }
    }

    public function saveTheory(): void
    {
        $this->saveTheoryBoth();
    }

    public function saveTheoryRu(): void
    {
        $this->saveEditorContent('ru', 'theory', $this->theoryRu, 'RU theory saved');
    }

    public function saveTheoryEn(): void
    {
        $this->saveEditorContent('en', 'theory', $this->theoryEn, 'EN theory saved');
    }

    public function saveTheoryBoth(): void
    {
        if ($this->selectedChapterId === null) {
            Notification::make()->title('Select chapter first')->warning()->send();
            return;
        }

        $this->saveChapterContent('ru', 'theory', $this->theoryRu);
        $this->saveChapterContent('en', 'theory', $this->theoryEn);
        Notification::make()->title('RU and EN theory saved')->success()->send();
    }

    public function saveExamples(): void
    {
        $this->saveExamplesBoth();
    }

    public function saveExamplesRu(): void
    {
        $this->saveEditorContent('ru', 'examples', $this->examplesRu, 'RU examples saved');
    }

    public function saveExamplesEn(): void
    {
        $this->saveEditorContent('en', 'examples', $this->examplesEn, 'EN examples saved');
    }

    public function saveExamplesBoth(): void
    {
        if ($this->selectedChapterId === null) {
            Notification::make()->title('Select chapter first')->warning()->send();
            return;
        }

        $this->saveChapterContent('ru', 'examples', $this->examplesRu);
        $this->saveChapterContent('en', 'examples', $this->examplesEn);
        Notification::make()->title('RU and EN examples saved')->success()->send();
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
            Notification::make()->title('Select problems and difficulty')->warning()->send();
            return;
        }

        $level = max(1, min(5, (int) $this->bulkDifficulty));
        Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $this->selectedProblemIds)
            ->update(['difficulty' => $level]);

        Notification::make()->title('Difficulty updated')->success()->send();
    }

    public function setSortSelected(): void
    {
        if ($this->selectedChapterId === null || empty($this->selectedProblemIds) || $this->bulkSortStart === null) {
            Notification::make()->title('Select problems and start sort order')->warning()->send();
            return;
        }

        $start = max(0, (int) $this->bulkSortStart);
        $problems = Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $this->selectedProblemIds)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($problems as $index => $problem) {
            $problem->sort_order = $start + $index;
            $problem->save();
        }

        Notification::make()->title('Sort order updated')->success()->send();
    }

    public function assignGradesSelected(): void
    {
        if ($this->selectedChapterId === null || empty($this->selectedProblemIds) || empty($this->bulkGradeIds)) {
            Notification::make()->title('Select problems and grades')->warning()->send();
            return;
        }

        if (! Schema::hasTable('grade_levels')) {
            Notification::make()->title('Grade tables are not installed yet')->warning()->send();
            return;
        }

        $gradeIds = collect($this->bulkGradeIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $problems = Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $this->selectedProblemIds)
            ->get();

        foreach ($problems as $problem) {
            $problem->gradeLevels()->syncWithoutDetaching($gradeIds);
        }

        Notification::make()->title('Grades assigned')->success()->send();
    }

    protected function getViewData(): array
    {
        $courses = Course::query()->with('texts')->orderBy('sort_order')->orderBy('id')->get();
        $chapters = $this->chapterOptions();
        $selectedCourse = $this->selectedCourseId ? $courses->firstWhere('id', $this->selectedCourseId) : null;
        $selectedChapter = $this->selectedChapterId ? $chapters->firstWhere('id', $this->selectedChapterId) : null;

        $overview = $this->overviewData($selectedCourse, $selectedChapter);
        $problems = $this->problemRows($selectedChapter);
        $ladders = $this->ladderRows($selectedChapter);
        $checklist = $this->publishChecklist($selectedChapter, $problems, $ladders);

        return [
            'tabs' => $this->tabs(),
            'courses' => $courses,
            'chapters' => $chapters,
            'selectedCourse' => $selectedCourse,
            'selectedChapter' => $selectedChapter,
            'overview' => $overview,
            'problems' => $problems,
            'ladders' => $ladders,
            'checklist' => $checklist,
            'gradeOptions' => $this->gradeOptions(),
            'quickAddUrl' => url('/admin/quick-problem') . $this->selectedParamsQuery(['return' => 'module-workspace']),
            'contentStudioUrl' => url('/admin/content-studio') . $this->selectedParamsQuery(),
            'publicChapterUrl' => $selectedChapter ? route('chapter.show.simple', ['chapter' => $selectedChapter->slug, 'lang' => $this->selectedLang]) : null,
            'publicPracticeUrl' => $selectedChapter ? route('chapter.practice.simple', ['chapter' => $selectedChapter->slug, 'lang' => $this->selectedLang]) : null,
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
            'checklist',
            'preview',
        ];
    }

    private function normalizeTab(string $tab): string
    {
        $tab = match ($tab) {
            'theory-editor' => 'theory',
            'examples-editor' => 'examples',
            'publish-checklist' => 'checklist',
            default => $tab,
        };

        return in_array($tab, $this->tabs(), true) ? $tab : 'overview';
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

    private function chapterOptions(): EloquentCollection
    {
        if ($this->selectedCourseId === null) {
            return new EloquentCollection();
        }

        $relations = ['texts'];
        if (Schema::hasTable('grade_levels')) {
            $relations[] = 'gradeLevels';
        }

        return Chapter::query()
            ->where('course_id', $this->selectedCourseId)
            ->with($relations)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<int, string>
     */
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

    /**
     * @return list<string>
     */
    private function problemWorkspaceRelations(): array
    {
        $relations = ['texts', 'tags.texts'];

        if (Schema::hasTable('grade_levels')) {
            $relations[] = 'gradeLevels';
        }

        return $relations;
    }

    private function gradeLabels(Collection $grades, string $lang): string
    {
        return $grades
            ->sortBy('grade_number')
            ->map(fn (GradeLevel $grade): string => $lang === 'ru' ? $grade->title_ru : $grade->title_en)
            ->filter()
            ->implode(', ');
    }

    private function loadChapterEditorValues(): void
    {
        $this->theoryRu = '';
        $this->theoryEn = '';
        $this->examplesRu = '';
        $this->examplesEn = '';

        if ($this->selectedChapterId === null) {
            return;
        }

        if ($this->usesTypedChapterText()) {
            $this->theoryRu = $this->fetchTypedChapterContent($this->selectedChapterId, 'ru', 'notes');
            $this->theoryEn = $this->fetchTypedChapterContent($this->selectedChapterId, 'en', 'notes');
            $this->examplesRu = $this->fetchTypedChapterContent($this->selectedChapterId, 'ru', 'examples');
            $this->examplesEn = $this->fetchTypedChapterContent($this->selectedChapterId, 'en', 'examples');
            return;
        }

        $texts = ChapterText::query()->where('chapter_id', $this->selectedChapterId)->get()->keyBy('lang');
        $this->theoryRu = (string) ($texts->get('ru')?->theory_html ?? '');
        $this->theoryEn = (string) ($texts->get('en')?->theory_html ?? '');
        $this->examplesRu = (string) ($texts->get('ru')?->examples_html ?? '');
        $this->examplesEn = (string) ($texts->get('en')?->examples_html ?? '');
    }

    private function saveChapterContent(string $lang, string $kind, string $value): void
    {
        if ($this->selectedChapterId === null) {
            return;
        }

        if ($this->usesTypedChapterText()) {
            $type = $kind === 'theory' ? 'notes' : 'examples';
            $this->saveTypedChapterContent($this->selectedChapterId, $lang, $type, $value);
            return;
        }

        $attributes = ['chapter_id' => $this->selectedChapterId, 'lang' => $lang];
        $existing = ChapterText::query()->where($attributes)->first();
        $title = $existing?->title ?: (Chapter::query()->find($this->selectedChapterId)?->slug ?? 'chapter');

        $updates = ['title' => $title];
        if ($kind === 'theory') {
            $updates['theory_html'] = $value;
        } else {
            $updates['examples_html'] = $value;
        }

        ChapterText::query()->updateOrCreate($attributes, $updates);
    }

    private function saveEditorContent(string $lang, string $kind, string $value, string $message): void
    {
        if ($this->selectedChapterId === null) {
            Notification::make()->title('Select chapter first')->warning()->send();
            return;
        }

        $this->saveChapterContent($lang, $kind, $value);

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }

    private function usesTypedChapterText(): bool
    {
        return Schema::hasColumn('chapter_texts', 'type');
    }

    private function chapterTextLangColumn(): string
    {
        return Schema::hasColumn('chapter_texts', 'language_id') ? 'language_id' : 'lang';
    }

    private function chapterTextContentColumn(): string
    {
        foreach (['content_html', 'body_html', 'html', 'theory_html'] as $column) {
            if (Schema::hasColumn('chapter_texts', $column)) {
                return $column;
            }
        }

        return 'content_html';
    }

    private function languageIdByCode(string $code): ?int
    {
        $lang = Language::query()->where('code', $code)->first();
        return $lang ? (int) $lang->id : null;
    }

    private function fetchTypedChapterContent(int $chapterId, string $langCode, string $type): string
    {
        $langColumn = $this->chapterTextLangColumn();
        $contentColumn = $this->chapterTextContentColumn();

        $query = DB::table('chapter_texts')
            ->where('chapter_id', $chapterId)
            ->where('type', $type);

        if ($langColumn === 'language_id') {
            $langId = $this->languageIdByCode($langCode);
            if ($langId === null) {
                return '';
            }
            $query->where('language_id', $langId);
        } else {
            $query->where('lang', $langCode);
        }

        $row = $query->first();
        if (! $row) {
            return '';
        }

        return (string) ($row->{$contentColumn} ?? '');
    }

    private function saveTypedChapterContent(int $chapterId, string $langCode, string $type, string $value): void
    {
        $langColumn = $this->chapterTextLangColumn();
        $contentColumn = $this->chapterTextContentColumn();

        $where = [
            'chapter_id' => $chapterId,
            'type' => $type,
        ];

        if ($langColumn === 'language_id') {
            $langId = $this->languageIdByCode($langCode);
            if ($langId === null) {
                return;
            }
            $where['language_id'] = $langId;
        } else {
            $where['lang'] = $langCode;
        }

        $existing = DB::table('chapter_texts')->where($where)->first();
        $chapterSlug = Chapter::query()->find($chapterId)?->slug ?? 'chapter';

        $updates = [
            $contentColumn => $value,
        ];

        if (Schema::hasColumn('chapter_texts', 'title')) {
            $updates['title'] = (string) ($existing->title ?? $chapterSlug);
        }
        if (Schema::hasColumn('chapter_texts', 'updated_at')) {
            $updates['updated_at'] = now();
        }
        if (! $existing && Schema::hasColumn('chapter_texts', 'created_at')) {
            $updates['created_at'] = now();
        }

        if ($existing) {
            DB::table('chapter_texts')->where($where)->update($updates);
        } else {
            DB::table('chapter_texts')->insert(array_merge($where, $updates));
        }
    }

    private function overviewData(?Course $course, ?Chapter $chapter): array
    {
        if (! $course || ! $chapter) {
            return [
                'chapter_title' => '',
                'chapter_slug' => '',
                'course_slug' => '',
                'problems_total' => 0,
                'published_total' => 0,
                'unpublished_total' => 0,
                'ru_theory_exists' => false,
                'en_theory_exists' => false,
                'ru_examples_exists' => false,
                'en_examples_exists' => false,
                'missing_translations_count' => 0,
                'mathjax_warnings_count' => 0,
                'encoding_warnings_count' => 0,
            ];
        }

        $texts = $chapter->texts;
        $ru = $texts->firstWhere('lang', 'ru');
        $en = $texts->firstWhere('lang', 'en');
        $recommendedGrades = Schema::hasTable('grade_levels')
            ? $this->gradeLabels($chapter->relationLoaded('gradeLevels') ? $chapter->gradeLevels : $chapter->gradeLevels()->get(), $this->selectedLang)
            : '';
        if ($this->usesTypedChapterText()) {
            $ruTheory = $this->fetchTypedChapterContent((int) $chapter->id, 'ru', 'notes');
            $enTheory = $this->fetchTypedChapterContent((int) $chapter->id, 'en', 'notes');
            $ruExamples = $this->fetchTypedChapterContent((int) $chapter->id, 'ru', 'examples');
            $enExamples = $this->fetchTypedChapterContent((int) $chapter->id, 'en', 'examples');
        } else {
            $ruTheory = (string) ($ru?->theory_html ?? '');
            $enTheory = (string) ($en?->theory_html ?? '');
            $ruExamples = (string) ($ru?->examples_html ?? '');
            $enExamples = (string) ($en?->examples_html ?? '');
        }

        $problemQuery = Problem::query()->where('chapter_id', $chapter->id);
        $problemsTotal = (int) $problemQuery->count();
        $published = (int) Problem::query()->where('chapter_id', $chapter->id)->where('is_published', true)->count();

        $problemRows = $this->problemRows($chapter);
        $checklist = $this->publishChecklist($chapter, $problemRows, $this->ladderRows($chapter));

        return [
            'chapter_title' => (string) ($ru?->title ?? $en?->title ?? $chapter->slug),
            'chapter_slug' => $chapter->slug,
            'course_slug' => $course->slug,
            'problems_total' => $problemsTotal,
            'published_total' => $published,
            'unpublished_total' => $problemsTotal - $published,
            'ru_theory_exists' => filled($ruTheory),
            'en_theory_exists' => filled($enTheory),
            'ru_examples_exists' => filled($ruExamples),
            'en_examples_exists' => filled($enExamples),
            'missing_translations_count' => collect($checklist['warnings'])->filter(fn (array $w): bool => str_contains($w['key'], 'missing'))->count(),
            'mathjax_warnings_count' => collect($checklist['warnings'])->filter(fn (array $w): bool => str_contains($w['key'], 'mathjax') || str_contains($w['key'], 'command'))->count(),
            'encoding_warnings_count' => collect($checklist['warnings'])->filter(fn (array $w): bool => str_contains($w['key'], 'encoding'))->count(),
            'recommended_grades' => $recommendedGrades,
        ];
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
            ->with($this->problemWorkspaceRelations())
            ->get();

        if ($this->selectedGradeFilter !== null && Schema::hasTable('grade_levels')) {
            $problems = $problems
                ->filter(fn (Problem $problem): bool => $problem->gradeLevels->contains('id', $this->selectedGradeFilter))
                ->values();
        }

        return $problems->map(function (Problem $problem): array {
            $ruText = $problem->texts->firstWhere('lang', 'ru');
            $enText = $problem->texts->firstWhere('lang', 'en');
            $selectedText = $problem->texts->firstWhere('lang', $this->selectedLang) ?? $enText ?? $ruText ?? $problem->texts->first();

            $tags = $problem->tags->map(function (Tag $tag): string {
                $text = $tag->texts->firstWhere('lang', $this->selectedLang)
                    ?? $tag->texts->firstWhere('lang', 'en')
                    ?? $tag->texts->firstWhere('lang', 'ru')
                    ?? $tag->texts->first();

                return (string) ($text?->title ?? $tag->slug);
            })->filter()->implode(', ');

            return [
                'id' => (int) $problem->id,
                'code' => (string) $problem->problem_code,
                'title' => (string) ($selectedText?->title ?? $problem->problem_code),
                'difficulty' => (int) ($problem->difficulty ?? 1),
                'is_published' => (bool) $problem->is_published,
                'has_ru' => $ruText !== null && filled($ruText->statement_html),
                'has_en' => $enText !== null && filled($enText->statement_html),
                'tags' => $tags,
                'grades' => Schema::hasTable('grade_levels') ? $this->gradeLabels($problem->gradeLevels ?? collect(), $this->selectedLang) : '',
                'source' => $problem->source_compact,
                'sort_order' => (int) ($problem->sort_order ?? 0),
                'statement_html' => (string) ($selectedText?->statement_html ?? ''),
                'edit_problem_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
                'edit_ru_url' => $ruText
                    ? ProblemTextResource::getUrl('edit', ['record' => $ruText->id])
                    : ProblemTextResource::getUrl('create', ['problem_id' => $problem->id, 'lang' => 'ru']),
                'edit_en_url' => $enText
                    ? ProblemTextResource::getUrl('edit', ['record' => $enText->id])
                    : ProblemTextResource::getUrl('create', ['problem_id' => $problem->id, 'lang' => 'en']),
            ];
        })->all();
    }

    private function ladderRows(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [];
        }

        $ladderRelations = ['texts.language', 'steps'];
        if (Schema::hasTable('grade_levels')) {
            $ladderRelations[] = 'gradeLevels';
        }

        $ladders = ProblemLadder::query()
            ->where('chapter_id', $chapter->id)
            ->with($ladderRelations)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $ladders->map(function (ProblemLadder $ladder): array {
            $hasRu = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'ru' && filled($text->title));
            $hasEn = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en' && filled($text->title));
            $current = $this->resolveLadderText($ladder->texts, $this->selectedLang);

            return [
                'id' => (int) $ladder->id,
                'slug' => $ladder->slug,
                'title' => (string) ($current?->title ?? $ladder->title),
                'difficulty' => (int) max(1, min(5, (int) $ladder->difficulty_level)),
                'is_published' => (bool) $ladder->is_published,
                'steps_count' => (int) $ladder->steps->count(),
                'has_ru' => $hasRu,
                'has_en' => $hasEn,
                'grades' => Schema::hasTable('grade_levels') ? $this->gradeLabels($ladder->gradeLevels ?? collect(), $this->selectedLang) : '',
                'missing_translation_warning' => (! $hasRu || ! $hasEn),
                'edit_url' => ProblemLadderResource::getUrl('edit', ['record' => $ladder]),
            ];
        })->all();
    }

    private function resolveLadderText(EloquentCollection $texts, string $lang): ?ProblemLadderText
    {
        return $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === $lang)
            ?? $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en')
            ?? $texts->first(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'ru')
            ?? $texts->first();
    }

    private function publishChecklist(?Chapter $chapter, array $problemRows, array $ladders): array
    {
        $warnings = [];
        if (! $chapter) {
            return ['warnings' => $warnings];
        }

        $ruTheory = $this->usesTypedChapterText()
            ? $this->fetchTypedChapterContent((int) $chapter->id, 'ru', 'notes')
            : (string) ($chapter->texts->firstWhere('lang', 'ru')?->theory_html ?? '');
        $enTheory = $this->usesTypedChapterText()
            ? $this->fetchTypedChapterContent((int) $chapter->id, 'en', 'notes')
            : (string) ($chapter->texts->firstWhere('lang', 'en')?->theory_html ?? '');
        $ruExamples = $this->usesTypedChapterText()
            ? $this->fetchTypedChapterContent((int) $chapter->id, 'ru', 'examples')
            : (string) ($chapter->texts->firstWhere('lang', 'ru')?->examples_html ?? '');
        $enExamples = $this->usesTypedChapterText()
            ? $this->fetchTypedChapterContent((int) $chapter->id, 'en', 'examples')
            : (string) ($chapter->texts->firstWhere('lang', 'en')?->examples_html ?? '');

        if (! filled($ruTheory)) {
            $warnings[] = ['key' => 'missing_ru_theory', 'message' => 'Missing RU theory'];
        }
        if (! filled($enTheory)) {
            $warnings[] = ['key' => 'missing_en_theory', 'message' => 'Missing EN theory'];
        }
        if (! filled($ruExamples)) {
            $warnings[] = ['key' => 'missing_ru_examples', 'message' => 'Missing RU examples'];
        }
        if (! filled($enExamples)) {
            $warnings[] = ['key' => 'missing_en_examples', 'message' => 'Missing EN examples'];
        }

        $unpublishedCount = collect($problemRows)->where('is_published', false)->count();
        if ($unpublishedCount > 0) {
            $warnings[] = ['key' => 'unpublished_problems', 'message' => "Unpublished problems: {$unpublishedCount}"];
        }

        $missingRu = collect($problemRows)->where('has_ru', false)->count();
        if ($missingRu > 0) {
            $warnings[] = ['key' => 'missing_ru_problem_text', 'message' => "Problems without RU text: {$missingRu}"];
        }
        $missingEn = collect($problemRows)->where('has_en', false)->count();
        if ($missingEn > 0) {
            $warnings[] = ['key' => 'missing_en_problem_text', 'message' => "Problems without EN text: {$missingEn}"];
        }

        $allContent = collect([$ruTheory, $enTheory, $ruExamples, $enExamples])
            ->merge(collect($problemRows)->flatMap(function (array $problem): array {
                $items = [$problem['title'] ?? '', $problem['statement_html'] ?? ''];
                return $items;
            }))
            ->merge(collect($ladders)->map(fn (array $ladder): string => (string) $ladder['title']))
            ->filter(fn ($value): bool => is_string($value) && $value !== '')
            ->values();

        if ($allContent->contains(fn (string $text): bool => preg_match('/&#\d{2,6};/u', $text) === 1)) {
            $warnings[] = ['key' => 'encoding_numeric_entities', 'message' => 'Found numeric HTML entities like &#1050;'];
        }
        if ($allContent->contains(fn (string $text): bool => preg_match('/Ð|Ñ|Ã|Â/u', $text) === 1)) {
            $warnings[] = ['key' => 'encoding_mojibake', 'message' => 'Found possible mojibake (Ð, Ñ, Ã, Â)'];
        }
        if ($allContent->contains(fn (string $text): bool => str_contains($text, '$$'))) {
            $warnings[] = ['key' => 'mathjax_dollar_dollar', 'message' => 'Found $$ delimiters (use \\( \\) or \\[ \\])'];
        }
        if ($allContent->contains(fn (string $text): bool => $this->hasLikelyBrokenMathCommand($text))) {
            $warnings[] = ['key' => 'mathjax_broken_command', 'message' => 'Found likely broken commands (cdot/sqrt/frac/binom without backslash)'];
        }

        return ['warnings' => $warnings];
    }

    private function hasLikelyBrokenMathCommand(string $html): bool
    {
        preg_match_all('/\\\\\\((.*?)\\\\\\)|\\\\\\[(.*?)\\\\\\]|\\$(.*?)\\$/su', $html, $matches, PREG_SET_ORDER);
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

    private function selectedParamsQuery(array $extra = []): string
    {
        $params = [];
        if ($this->selectedCourseId !== null) {
            $params['course_id'] = $this->selectedCourseId;
        }
        if ($this->selectedChapterId !== null) {
            $params['chapter_id'] = $this->selectedChapterId;
        }
        $params['lang'] = $this->selectedLang;
        $params['tab'] = $this->activeTab;
        $params = array_merge($params, $extra);

        return $params === [] ? '' : ('?' . http_build_query($params));
    }
}
