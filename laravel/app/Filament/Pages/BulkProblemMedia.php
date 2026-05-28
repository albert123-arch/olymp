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
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use UnitEnum;

class BulkProblemMedia extends Page
{
    use WithFileUploads;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Bulk Diagrams';

    protected static ?string $title = 'Bulk Diagram Manager';

    protected static ?string $slug = 'bulk-problem-media';

    protected string $view = 'filament.pages.bulk-problem-media';

    #[Url(as: 'course_id', except: null)]
    public ?int $selectedCourseId = null;

    #[Url(as: 'chapter_id', except: null)]
    public ?int $selectedChapterId = null;

    #[Url(as: 'lang', except: 'ru')]
    public string $selectedLang = 'ru';

    public bool $onlyMissingStatement = false;

    /** @var array<int, array<int, mixed>> */
    public array $statementUploads = [];

    /** @var array<int, array<int, mixed>> */
    public array $hintUploads = [];

    /** @var array<int, array<int, mixed>> */
    public array $solutionUploads = [];

    /** @var array<int, array<int, mixed>> */
    public array $extraUploads = [];

    public function mount(): void
    {
        $courseId = request()->integer('course_id');
        $chapterId = request()->integer('chapter_id');
        $lang = (string) request()->query('lang', 'ru');

        $this->selectedLang = in_array($lang, ['ru', 'en'], true) ? $lang : 'ru';
        $this->selectedCourseId = $courseId > 0 ? $courseId : $this->defaultCourseId();
        $this->syncSelectedChapter($chapterId > 0 ? $chapterId : null);
    }

    public function updatedSelectedCourseId(): void
    {
        $this->syncSelectedChapter();
    }

    public function updatedSelectedChapterId(): void
    {
        $this->resetUploads();
    }

    public function saveRow(int $problemId): void
    {
        $problem = $this->problemInSelectedChapter($problemId);
        if (! $problem) {
            Notification::make()->title('Problem is not in selected chapter')->danger()->send();
            return;
        }

        $saved = $this->storeUploadsForProblem($problem);

        Notification::make()
            ->title($saved > 0 ? "Uploaded {$saved} media item(s)" : 'No files selected')
            ->success()
            ->send();
    }

    public function saveAll(): void
    {
        if ($this->selectedChapterId === null) {
            Notification::make()->title('Select a chapter first')->warning()->send();
            return;
        }

        $problemIds = collect([
            ...array_keys($this->statementUploads),
            ...array_keys($this->hintUploads),
            ...array_keys($this->solutionUploads),
            ...array_keys($this->extraUploads),
        ])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if ($problemIds->isEmpty()) {
            Notification::make()->title('No files selected')->warning()->send();
            return;
        }

        $problems = Problem::query()
            ->where('chapter_id', $this->selectedChapterId)
            ->whereIn('id', $problemIds)
            ->get();

        $saved = 0;
        foreach ($problems as $problem) {
            $saved += $this->storeUploadsForProblem($problem);
        }

        Notification::make()
            ->title("Uploaded {$saved} media item(s)")
            ->success()
            ->send();
    }

    public function deleteMedia(int $mediaId): void
    {
        if ($this->selectedChapterId === null) {
            return;
        }

        $media = ProblemMedia::query()
            ->where('id', $mediaId)
            ->whereHas('problem', fn ($query) => $query->where('chapter_id', $this->selectedChapterId))
            ->first();

        if (! $media) {
            return;
        }

        $media->texts()->delete();
        ProblemMediaUpload::deleteMediaFileIfSafe($media);
        $media->delete();

        Notification::make()->title('Media deleted')->success()->send();
    }

    protected function getViewData(): array
    {
        $courses = Course::query()->with('texts')->orderBy('sort_order')->orderBy('id')->get();
        $chapters = $this->chapterOptions();
        $selectedChapter = $this->selectedChapterId ? $chapters->firstWhere('id', $this->selectedChapterId) : null;

        return [
            'courses' => $courses,
            'chapters' => $chapters,
            'selectedChapter' => $selectedChapter,
            'rows' => $this->problemRows($selectedChapter),
            'moduleWorkspaceUrl' => url('/admin/module-workspace').$this->contextQuery(['tab' => 'problems']),
            'problemBuilderBaseUrl' => url('/admin/problem-builder'),
        ];
    }

    private function problemRows(?Chapter $chapter): array
    {
        if (! $chapter) {
            return [];
        }

        $relations = ['texts', 'media.texts', 'tags.texts'];
        if (Schema::hasTable('grade_levels')) {
            $relations[] = 'gradeLevels';
        }

        $problems = Problem::query()
            ->where('chapter_id', $chapter->id)
            ->with($relations)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($this->onlyMissingStatement) {
            $problems = $problems
                ->filter(fn (Problem $problem): bool => $problem->media->where('role', 'statement')->isEmpty())
                ->values();
        }

        return $problems->map(function (Problem $problem): array {
            $selectedText = $problem->texts->firstWhere('lang', $this->selectedLang)
                ?? $problem->texts->firstWhere('lang', 'en')
                ?? $problem->texts->firstWhere('lang', 'ru')
                ?? $problem->texts->first();

            $counts = ProblemMediaUpload::mediaCounts($problem->media);
            $missingText = ProblemMediaUpload::hasMissingText($problem->media);

            return [
                'id' => (int) $problem->id,
                'sort_order' => (int) ($problem->sort_order ?? 0),
                'code' => (string) $problem->problem_code,
                'title' => (string) ($selectedText?->title ?? $problem->problem_code),
                'difficulty' => (int) ($problem->difficulty ?? 1),
                'grades' => Schema::hasTable('grade_levels') ? $this->gradeLabels($problem->gradeLevels ?? collect()) : '',
                'media_counts' => $counts,
                'missing_media_text' => $missingText,
                'media' => $problem->media->map(fn (ProblemMedia $media): array => [
                    'id' => (int) $media->id,
                    'role' => ProblemMediaUpload::normalizeRole($media->role),
                    'file_path' => (string) $media->file_path,
                    'mime_type' => (string) $media->mime_type,
                    'original_name' => (string) $media->original_name,
                ])->all(),
                'builder_url' => url('/admin/problem-builder').$this->contextQuery([
                    'problem_id' => (int) $problem->id,
                    'return' => 'module-workspace',
                    'tab' => 'problems',
                ]),
                'edit_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
            ];
        })->all();
    }

    private function storeUploadsForProblem(Problem $problem): int
    {
        $saved = 0;
        foreach ([
            'statement' => $this->statementUploads[$problem->id] ?? [],
            'hint' => $this->hintUploads[$problem->id] ?? [],
            'solution' => $this->solutionUploads[$problem->id] ?? [],
            'extra' => $this->extraUploads[$problem->id] ?? [],
        ] as $role => $files) {
            foreach ((array) $files as $file) {
                try {
                    ProblemMediaUpload::storeUploadedFile($file, $problem, $role);
                    $saved++;
                } catch (\Throwable $exception) {
                    Notification::make()
                        ->title('Upload skipped')
                        ->body($problem->problem_code.': '.$exception->getMessage())
                        ->danger()
                        ->send();
                }
            }
        }

        unset(
            $this->statementUploads[$problem->id],
            $this->hintUploads[$problem->id],
            $this->solutionUploads[$problem->id],
            $this->extraUploads[$problem->id],
        );

        return $saved;
    }

    private function problemInSelectedChapter(int $problemId): ?Problem
    {
        if ($this->selectedChapterId === null) {
            return null;
        }

        return Problem::query()
            ->where('id', $problemId)
            ->where('chapter_id', $this->selectedChapterId)
            ->first();
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

    private function chapterOptions(): EloquentCollection
    {
        if ($this->selectedCourseId === null) {
            return new EloquentCollection();
        }

        return Chapter::query()
            ->where('course_id', $this->selectedCourseId)
            ->with('texts')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function gradeLabels($grades): string
    {
        if (! $grades) {
            return '';
        }

        return collect($grades)
            ->sortBy('grade_number')
            ->map(fn (GradeLevel $grade): string => $this->selectedLang === 'ru' ? $grade->title_ru : $grade->title_en)
            ->filter()
            ->implode(', ');
    }

    private function resetUploads(): void
    {
        $this->statementUploads = [];
        $this->hintUploads = [];
        $this->solutionUploads = [];
        $this->extraUploads = [];
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
        $params['lang'] = $this->selectedLang;
        $params = array_merge($params, $extra);

        return '?'.http_build_query($params);
    }
}
