<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Language;
use App\Models\Problem;
use App\Models\ProblemLadder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    private ?Collection $activeLanguages = null;

    public function home(): View
    {
        $lang = $this->setLocale();

        $courses = $this->publishedCourses()
            ->where($this->usesStatusColumn() ? 'status' : 'is_published', $this->usesStatusColumn() ? 'active' : 1)
            ->with(['texts', 'chapters' => function ($query): void {
                $query->where('is_published', true);
            }])
            ->get();

        return view('public.home', [
            ...$this->baseViewData($lang),
            'courses' => $courses->map(fn (Course $course): array => $this->courseCardData($course, $lang, true))->all(),
        ]);
    }

    public function dashboard(): View
    {
        $lang = $this->setLocale();
        $user = auth()->user();
        abort_unless($user !== null, 403);

        $solvedCount = DB::table('user_problem_progress')
            ->where('user_id', (int) $user->id)
            ->where('status', 'solved')
            ->count();
        $bookmarksCount = DB::table('bookmarks')
            ->where('user_id', (int) $user->id)
            ->count();

        $course = $this->publishedCourses()->with('texts')->first();
        $coursePayload = null;
        if ($course) {
            $coursePayload = [
                'title' => $this->resolveText($course->texts, $lang)?->title ?? $course->slug,
                'url' => route('course.show', ['course' => $course->slug, 'lang' => $lang]),
                'practice_url' => route('course.practice', ['course' => $course->slug, 'lang' => $lang]),
            ];
        }

        return view('public.dashboard', [
            ...$this->baseViewData($lang),
            'dashboard' => [
                'course' => $coursePayload,
                'solved_count' => $solvedCount,
                'bookmarks_count' => $bookmarksCount,
                'is_admin' => method_exists($user, 'isAdminUser') ? $user->isAdminUser() : false,
            ],
        ]);
    }

    public function courses(): View
    {
        $lang = $this->setLocale();

        $courses = $this->publishedCourses()
            ->with(['texts', 'chapters' => function ($query): void {
                $query->where('is_published', true);
            }])
            ->get();

        return view('public.courses.index', [
            ...$this->baseViewData($lang),
            'courses' => $courses->map(fn (Course $course): array => $this->courseCardData($course, $lang, false))->all(),
        ]);
    }

    public function course(Course $course): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);

        $course->load([
            'texts',
            'chapters' => function ($query): void {
                $query
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->with('texts')
                    ->withCount([
                        'problems as published_problems_count' => function ($problemsQuery): void {
                            $problemsQuery->where('is_published', true);
                        },
                        'ladders as published_ladders_count' => function ($ladderQuery): void {
                            $ladderQuery->where('is_published', true);
                        },
                    ]);
            },
        ]);

        $problemIds = Problem::query()
            ->whereIn('chapter_id', $course->chapters->pluck('id'))
            ->where('is_published', true)
            ->pluck('id');
        $interactionState = $this->interactionStateForProblems($problemIds);
        $progress = $this->buildProgressData($problemIds, $interactionState);

        return view('public.courses.show', [
            ...$this->baseViewData($lang),
            'course' => $this->coursePageData($course, $lang, $progress),
        ]);
    }

    public function courseTheory(Course $course): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);

        $course->load([
            'texts',
            'chapters' => function ($query): void {
                $query->where('is_published', true)->orderBy('sort_order')->orderBy('id')->with('texts');
            },
        ]);

        return view('public.courses.theory', [
            ...$this->baseViewData($lang),
            'course' => $this->courseTheoryData($course, $lang),
        ]);
    }

    public function chapter(Course $course, Chapter $chapter): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);
        $this->ensureChapterBelongsToCourse($course, $chapter);
        $problemRelations = $this->problemCardRelations();

        $orderedChapters = Chapter::query()
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with('texts')
            ->get()
            ->values();
        $chapterIndex = $orderedChapters->search(fn (Chapter $item): bool => (int) $item->id === (int) $chapter->id);
        $chapterIndex = is_int($chapterIndex) ? $chapterIndex : 0;
        $previousChapter = $chapterIndex > 0 ? $orderedChapters->get($chapterIndex - 1) : null;
        $nextChapter = $chapterIndex < ($orderedChapters->count() - 1) ? $orderedChapters->get($chapterIndex + 1) : null;

        $chapter->load([
            'course.texts',
            'texts',
            'problems' => function ($query) use ($problemRelations): void {
                $query->where('is_published', true)->orderBy('sort_order')->orderBy('id')->with($problemRelations);
            },
        ]);
        $chapterLadders = ProblemLadder::query()
            ->where('is_published', true)
            ->where('chapter_id', $chapter->id)
            ->with('texts.language')
            ->withCount('steps')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $problemIds = $chapter->problems->pluck('id');
        $interactionState = $this->interactionStateForProblems($problemIds);
        $progress = $this->buildProgressData($problemIds, $interactionState);

        return view('public.chapters.show', [
            ...$this->baseViewData($lang),
            'chapter' => $this->chapterPageData(
                $chapter,
                $lang,
                $chapterIndex + 1,
                $previousChapter,
                $nextChapter,
                $interactionState,
                $progress,
                $chapterLadders->map(fn (ProblemLadder $ladder): array => $this->ladderCardData($ladder, $lang))->all()
            ),
        ]);
    }

    public function chapterTheory(Course $course, Chapter $chapter): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);
        $this->ensureChapterBelongsToCourse($course, $chapter);

        $chapter->load([
            'course.texts',
            'texts',
        ]);

        return view('public.chapters.theory', [
            ...$this->baseViewData($lang),
            'chapter' => $this->chapterTheoryData($chapter, $lang),
        ]);
    }

    public function chapterBySlug(Chapter $chapter, Request $request): RedirectResponse
    {
        $course = Course::query()->findOrFail($chapter->course_id);

        return redirect()->route('chapter.show', [
            'course' => $course->slug,
            'chapter' => $chapter->slug,
            'lang' => $request->query('lang', $this->currentLanguage()),
        ]);
    }

    public function chapterTheoryBySlug(Chapter $chapter, Request $request): RedirectResponse
    {
        $course = Course::query()->findOrFail($chapter->course_id);

        return redirect()->route('chapter.theory', [
            'course' => $course->slug,
            'chapter' => $chapter->slug,
            'lang' => $request->query('lang', $this->currentLanguage()),
        ]);
    }

    public function ladders(Request $request): View
    {
        $lang = $this->setLocale();
        $selectedCourseSlug = (string) $request->query('course', '');
        $selectedChapterSlug = (string) $request->query('chapter', '');
        $selectedGrade = $this->readGradeFilter($request);
        $ladderRelations = ['course.texts', 'chapter.texts', 'texts.language'];
        if ($this->gradeLevelsAvailable()) {
            $ladderRelations[] = 'gradeLevels';
        }

        $ladders = ProblemLadder::query()
            ->where('is_published', true)
            ->with($ladderRelations)
            ->withCount('steps')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($selectedCourseSlug !== '') {
            $ladders = $ladders
                ->filter(fn (ProblemLadder $ladder): bool => optional($ladder->course)->slug === $selectedCourseSlug)
                ->values();
        }

        if ($selectedGrade !== null && $this->gradeLevelsAvailable()) {
            $ladders = $ladders
                ->filter(fn (ProblemLadder $ladder): bool => $ladder->gradeLevels->contains('grade_number', $selectedGrade))
                ->values();
        }

        if ($selectedChapterSlug !== '') {
            $ladders = $ladders
                ->filter(fn (ProblemLadder $ladder): bool => optional($ladder->chapter)->slug === $selectedChapterSlug)
                ->values();
        }

        $courses = Course::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with('texts')
            ->get()
            ->map(fn (Course $course): array => [
                'slug' => $course->slug,
                'title' => $this->resolveText($course->texts, $lang)?->title ?? $course->slug,
            ])
            ->all();

        $chaptersQuery = Chapter::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['texts', 'course.texts']);
        if ($selectedCourseSlug !== '') {
            $selectedCourse = Course::query()->where('slug', $selectedCourseSlug)->first();
            if ($selectedCourse) {
                $chaptersQuery->where('course_id', $selectedCourse->id);
            }
        }
        $chapters = $chaptersQuery->get()
            ->map(fn (Chapter $chapter): array => [
                'slug' => $chapter->slug,
                'title' => $this->resolveText($chapter->texts, $lang)?->title ?? $chapter->slug,
            ])
            ->all();

        return view('public.ladders.index', [
            ...$this->baseViewData($lang),
            'filters' => [
                'course' => $selectedCourseSlug,
                'chapter' => $selectedChapterSlug,
                'grade' => $selectedGrade,
            ],
            'courses' => $courses,
            'chapters' => $chapters,
            'gradeOptions' => $this->publicGradeOptions($lang),
            'ladders' => $ladders
                ->map(fn (ProblemLadder $ladder): array => $this->ladderCardData($ladder, $lang))
                ->all(),
        ]);
    }

    public function ladder(ProblemLadder $ladder): View
    {
        $lang = $this->setLocale();
        abort_unless((bool) $ladder->is_published, 404);

        $ladder->load([
            'course.texts',
            'chapter.texts',
            'texts.language',
            'steps' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('id');
            },
            'steps.problem.texts',
            'steps.problem.tags.texts',
        ]);

        $problemIds = $ladder->steps
            ->pluck('problem.id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->values();
        $interactionState = $this->interactionStateForProblems($problemIds);

        return view('public.ladders.show', [
            ...$this->baseViewData($lang),
            'ladder' => $this->ladderPageData($ladder, $lang, $interactionState),
        ]);
    }

    public function ladderPractice(ProblemLadder $ladder): View
    {
        $lang = $this->setLocale();
        abort_unless((bool) $ladder->is_published, 404);
        $problemRelations = $this->problemCardRelations('steps.problem.');

        $ladder->load([
            'course.texts',
            'chapter.texts',
            'texts.language',
            'steps' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('id');
            },
            ...$problemRelations,
        ]);

        $stepProblems = $ladder->steps
            ->filter(fn ($step): bool => $step->problem !== null && (bool) $step->problem->is_published)
            ->values();
        $problemIds = $stepProblems
            ->pluck('problem.id')
            ->map(fn ($id): int => (int) $id)
            ->values();
        $interactionState = $this->interactionStateForProblems($problemIds);

        $steps = $stepProblems->map(function ($step, int $index) use ($lang, $interactionState): array {
            $problemData = $this->problemCardData(
                $step->problem,
                $lang,
                null,
                null,
                $interactionState
            );
            $problemData['display_number'] = '#'.($index + 1);

            return [
                'id' => (int) $step->id,
                'step_number' => $index + 1,
                'step_type' => (string) $step->step_type,
                'step_type_label' => $this->stepTypeLabel((string) $step->step_type),
                'step_title' => $problemData['title'],
                'step_label' => null,
                'difficulty' => $this->normalizeDifficultyLevel($step->difficulty_level),
                'difficulty_stars' => $this->difficultyStars($this->normalizeDifficultyLevel($step->difficulty_level)),
                'problem' => $problemData,
            ];
        })->all();

        return view('public.ladders.practice', [
            ...$this->baseViewData($lang),
            'ladder' => [
                'slug' => $ladder->slug,
                ...$this->resolveLadderText($ladder, $lang),
                'course' => $ladder->course ? $this->courseBreadcrumbData($ladder->course, $lang) : null,
                'chapter' => $ladder->chapter ? $this->chapterNavData($ladder->chapter, $lang) : null,
                'difficulty' => $this->normalizeDifficultyLevel($ladder->difficulty_level),
                'difficulty_stars' => $this->difficultyStars($this->normalizeDifficultyLevel($ladder->difficulty_level)),
                'steps' => $steps,
                'show_url' => route('ladders.show', ['ladder' => $ladder->slug, 'lang' => $lang]),
            ],
        ]);
    }

    public function problem(Problem $problem): View
    {
        $lang = $this->setLocale();
        $problem->load($this->problemPageRelations());

        abort_unless((bool) $problem->is_published, 404);
        abort_unless((bool) $problem->chapter->is_published, 404);
        abort_unless((bool) $problem->chapter->course->is_published, 404);

        $orderedProblems = Problem::query()
            ->where('chapter_id', $problem->chapter_id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'problem_code']);

        $currentIndex = $orderedProblems->search(fn ($item): bool => (int) $item->id === (int) $problem->id);
        $currentIndex = is_int($currentIndex) ? $currentIndex : 0;
        $previous = $currentIndex > 0 ? $orderedProblems->get($currentIndex - 1) : null;
        $next = $currentIndex < ($orderedProblems->count() - 1) ? $orderedProblems->get($currentIndex + 1) : null;
        $interactionState = $this->interactionStateForProblems(collect([(int) $problem->id]));

        return view('public.problems.show', [
            ...$this->baseViewData($lang),
            'problem' => $this->problemPageData(
                $problem,
                $lang,
                $interactionState,
                $previous?->problem_code,
                $next?->problem_code
            ),
        ]);
    }

    public function coursePractice(Course $course, Request $request): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);
        $problemRelations = $this->problemCardRelations();

        $course->load([
            'texts',
            'chapters' => function ($query) use ($problemRelations): void {
                $query
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->with('texts')
                    ->with(['problems' => function ($problemsQuery) use ($problemRelations): void {
                        $problemsQuery
                            ->where('is_published', true)
                            ->orderBy('sort_order')
                            ->orderBy('id')
                            ->with($problemRelations);
                    }]);
            },
        ]);

        $filters = $this->readPracticeFilters($request);
        $allProblems = $course->chapters->flatMap(fn (Chapter $chapter): Collection => $chapter->problems)->values();
        $interactionState = $this->interactionStateForProblems($allProblems->pluck('id'));
        $effectiveStatus = $this->effectivePracticeStatus($filters['status'], $interactionState['can_interact']);

        $courseSlug = $course->slug;
        $chapters = $course->chapters
            ->values()
            ->map(function (Chapter $chapter, int $chapterIndex) use ($lang, $filters, $effectiveStatus, $interactionState, $courseSlug): array {
                $visibleProblems = $chapter->problems
                    ->values()
                    ->filter(fn (Problem $problem): bool => $this->matchesPracticeFilters($problem, $filters['level'], $filters['grade'], $effectiveStatus, $interactionState))
                    ->values();

                return [
                    'slug' => $chapter->slug,
                    'title' => $this->resolveText($chapter->texts, $lang)?->title ?? $chapter->slug,
                    'display_index' => $chapterIndex + 1,
                    'problem_count' => $visibleProblems->count(),
                    'practice_url' => route('chapter.practice', [
                        'course' => $courseSlug,
                        'chapter' => $chapter->slug,
                        'lang' => $lang,
                    ]),
                    'problems' => $visibleProblems
                        ->map(fn (Problem $problem, int $problemIndex): array => $this->problemCardData(
                            $problem,
                            $lang,
                            $chapterIndex + 1,
                            $problemIndex + 1,
                            $interactionState
                        ))
                        ->all(),
                ];
            })
            ->all();

        return view('public.practice.course', [
            ...$this->baseViewData($lang),
            'course' => [
                'slug' => $course->slug,
                'title' => $this->resolveText($course->texts, $lang)?->title ?? $course->slug,
            ],
            'chapters' => $chapters,
            'filters' => [
                'status' => $filters['status'],
                'effective_status' => $effectiveStatus,
                'level' => $filters['level'],
                'grade' => $filters['grade'],
                'requires_auth' => $filters['status'] !== $effectiveStatus,
            ],
            'gradeOptions' => $this->publicGradeOptions($lang),
            'progress' => $this->buildProgressData($allProblems->pluck('id'), $interactionState),
            'canTrackProgress' => $interactionState['can_interact'],
        ]);
    }

    public function chapterPractice(Course $course, Chapter $chapter, Request $request): View
    {
        $lang = $this->setLocale();
        $this->ensurePublishedCourse($course);
        $this->ensureChapterBelongsToCourse($course, $chapter);
        $problemRelations = $this->problemCardRelations();

        $orderedChapters = Chapter::query()
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with('texts')
            ->get()
            ->values();
        $chapterIndex = $orderedChapters->search(fn (Chapter $item): bool => (int) $item->id === (int) $chapter->id);
        $chapterIndex = is_int($chapterIndex) ? $chapterIndex : 0;
        $previousChapter = $chapterIndex > 0 ? $orderedChapters->get($chapterIndex - 1) : null;
        $nextChapter = $chapterIndex < ($orderedChapters->count() - 1) ? $orderedChapters->get($chapterIndex + 1) : null;

        $chapter->load([
            'course.texts',
            'texts',
            'problems' => function ($query) use ($problemRelations): void {
                $query->where('is_published', true)->orderBy('sort_order')->orderBy('id')->with($problemRelations);
            },
        ]);

        $filters = $this->readPracticeFilters($request);
        $interactionState = $this->interactionStateForProblems($chapter->problems->pluck('id'));
        $effectiveStatus = $this->effectivePracticeStatus($filters['status'], $interactionState['can_interact']);

        $visibleProblems = $chapter->problems
            ->values()
            ->filter(fn (Problem $problem): bool => $this->matchesPracticeFilters($problem, $filters['level'], $filters['grade'], $effectiveStatus, $interactionState))
            ->values();

        return view('public.practice.chapter', [
            ...$this->baseViewData($lang),
            'chapter' => [
                'slug' => $chapter->slug,
                'display_index' => $chapterIndex + 1,
                'title' => $this->resolveText($chapter->texts, $lang)?->title ?? $chapter->slug,
                'course' => $this->courseBreadcrumbData($chapter->course, $lang),
                'previous_chapter' => $previousChapter ? $this->chapterNavData($previousChapter, $lang) : null,
                'next_chapter' => $nextChapter ? $this->chapterNavData($nextChapter, $lang) : null,
                'problems' => $visibleProblems
                    ->map(fn (Problem $problem, int $problemIndex): array => $this->problemCardData(
                        $problem,
                        $lang,
                        $chapterIndex + 1,
                        $problemIndex + 1,
                        $interactionState
                    ))
                    ->all(),
            ],
            'filters' => [
                'status' => $filters['status'],
                'effective_status' => $effectiveStatus,
                'level' => $filters['level'],
                'grade' => $filters['grade'],
                'requires_auth' => $filters['status'] !== $effectiveStatus,
            ],
            'gradeOptions' => $this->publicGradeOptions($lang),
            'progress' => $this->buildProgressData($chapter->problems->pluck('id'), $interactionState),
            'canTrackProgress' => $interactionState['can_interact'],
        ]);
    }

    public function chapterPracticeBySlug(Chapter $chapter, Request $request): RedirectResponse
    {
        $course = Course::query()->findOrFail($chapter->course_id);

        return redirect()->route('chapter.practice', [
            'course' => $course->slug,
            'chapter' => $chapter->slug,
            'lang' => $request->query('lang', $this->currentLanguage()),
            'status' => $request->query('status'),
            'level' => $request->query('level'),
            'grade' => $request->query('grade'),
        ]);
    }

    public function toggleBookmark(Request $request, Problem $problem): JsonResponse|RedirectResponse
    {
        if (! auth()->check()) {
            return $this->interactionAuthRequiredResponse($request);
        }

        abort_unless((bool) $problem->is_published, 404);
        $userId = (int) auth()->id();

        $exists = DB::table('bookmarks')
            ->where('user_id', $userId)
            ->where('problem_id', $problem->id)
            ->exists();

        if ($exists) {
            DB::table('bookmarks')
                ->where('user_id', $userId)
                ->where('problem_id', $problem->id)
                ->delete();
        } else {
            DB::table('bookmarks')->insert([
                'user_id' => $userId,
                'problem_id' => (int) $problem->id,
                'created_at' => now(),
            ]);
        }

        $bookmarked = ! $exists;
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'bookmarked' => $bookmarked,
            ]);
        }

        return back()->with('status', $bookmarked ? 'bookmarked' : 'unbookmarked');
    }

    public function toggleSolved(Request $request, Problem $problem): JsonResponse|RedirectResponse
    {
        if (! auth()->check()) {
            return $this->interactionAuthRequiredResponse($request);
        }

        abort_unless((bool) $problem->is_published, 404);
        $userId = (int) auth()->id();
        $existing = DB::table('user_problem_progress')
            ->where('user_id', $userId)
            ->where('problem_id', (int) $problem->id)
            ->first();

        $isCurrentlySolved = $existing?->status === 'solved';
        $now = now();

        if ($existing === null) {
            DB::table('user_problem_progress')->insert([
                'user_id' => $userId,
                'problem_id' => (int) $problem->id,
                'status' => 'solved',
                'attempts' => 1,
                'started_at' => $now,
                'solved_at' => $now,
                'updated_at' => $now,
            ]);
            $solved = true;
        } elseif ($isCurrentlySolved) {
            DB::table('user_problem_progress')
                ->where('user_id', $userId)
                ->where('problem_id', (int) $problem->id)
                ->update([
                    'status' => 'started',
                    'solved_at' => null,
                    'updated_at' => $now,
                ]);
            $solved = false;
        } else {
            DB::table('user_problem_progress')
                ->where('user_id', $userId)
                ->where('problem_id', (int) $problem->id)
                ->update([
                    'status' => 'solved',
                    'solved_at' => $now,
                    'updated_at' => $now,
                ]);
            $solved = true;
        }

        $chapterProgress = $this->calculateChapterProgress((int) $problem->chapter_id, $userId);
        if ($chapterProgress !== null) {
            DB::table('chapter_progress')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'chapter_id' => (int) $problem->chapter_id,
                ],
                [
                    'status' => $chapterProgress['status'],
                    'percent_complete' => $chapterProgress['percent_complete'],
                    'updated_at' => $now,
                ]
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'solved' => $solved,
            ]);
        }

        return back()->with('status', $solved ? 'solved' : 'unsolved');
    }

    private function setLocale(): string
    {
        $lang = $this->currentLanguage();
        App::setLocale($lang);

        return $lang;
    }

    private function currentLanguage(): string
    {
        $requested = (string) request()->query('lang', '');
        $codes = $this->availableLanguageCodes();

        if ($requested !== '' && in_array($requested, $codes, true)) {
            return $requested;
        }

        if (in_array('ru', $codes, true)) {
            return 'ru';
        }

        if (in_array('en', $codes, true)) {
            return 'en';
        }

        return $codes[0] ?? 'en';
    }

    /**
     * @return array<int, string>
     */
    private function availableLanguageCodes(): array
    {
        return $this->languages()->pluck('code')->filter()->values()->all();
    }

    private function languages(): Collection
    {
        if ($this->activeLanguages instanceof Collection) {
            return $this->activeLanguages;
        }

        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->activeLanguages = $languages->isNotEmpty()
            ? $languages
            : collect([
                (object) ['code' => 'ru', 'title' => 'Русский'],
                (object) ['code' => 'en', 'title' => 'English'],
            ]);

        return $this->activeLanguages;
    }

    private function baseViewData(string $lang): array
    {
        return [
            'currentLang' => $lang,
            'availableLanguages' => $this->languages(),
        ];
    }

    private function publishedCourses()
    {
        return Course::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    private function ensurePublishedCourse(Course $course): void
    {
        abort_unless((bool) $course->is_published, 404);
    }

    private function ensureChapterBelongsToCourse(Course $course, Chapter $chapter): void
    {
        abort_unless((int) $chapter->course_id === (int) $course->id, 404);
        abort_unless((bool) $chapter->is_published, 404);
    }

    private function courseCardData(Course $course, string $lang, bool $featured): array
    {
        $text = $this->resolveText($course->texts, $lang);

        return [
            'slug' => $course->slug,
            'title' => $text?->title ?? $course->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'chapter_count' => $course->chapters->count(),
            'is_active' => $this->isActiveCourse($course),
            'featured' => $featured,
        ];
    }

    private function coursePageData(Course $course, string $lang, array $progress): array
    {
        $text = $this->resolveText($course->texts, $lang);

        return [
            'slug' => $course->slug,
            'title' => $text?->title ?? $course->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'status' => $course->status ?? null,
            'chapters' => $course->chapters->map(fn (Chapter $chapter): array => $this->chapterCardData($chapter, $lang))->all(),
            'progress' => $progress,
        ];
    }

    private function courseTheoryData(Course $course, string $lang): array
    {
        $text = $this->resolveText($course->texts, $lang);

        return [
            'slug' => $course->slug,
            'title' => $text?->title ?? $course->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'chapters' => $course->chapters->map(fn (Chapter $chapter): array => $this->chapterTheoryBlockData($chapter, $lang))->all(),
        ];
    }

    private function chapterPageData(
        Chapter $chapter,
        string $lang,
        int $chapterDisplayIndex,
        ?Chapter $previousChapter = null,
        ?Chapter $nextChapter = null,
        array $interactionState = ['can_interact' => false, 'solved' => [], 'bookmarked' => []],
        array $progress = ['total' => 0, 'solved' => 0, 'percent' => 0],
        array $ladders = []
    ): array {
        $text = $this->resolveText($chapter->texts, $lang);
        $problems = $chapter->problems->values();

        return [
            'slug' => $chapter->slug,
            'display_index' => $chapterDisplayIndex,
            'title' => $text?->title ?? $chapter->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'theory_html' => $this->normalizeMathHtml($text?->theory_html),
            'examples_html' => $this->normalizeMathHtml($text?->examples_html),
            'problems' => $problems
                ->map(fn (Problem $problem, int $index): array => $this->problemCardData(
                    $problem,
                    $lang,
                    $chapterDisplayIndex,
                    $index + 1,
                    $interactionState
                ))
                ->all(),
            'course' => $this->courseBreadcrumbData($chapter->course, $lang),
            'previous_chapter' => $previousChapter ? $this->chapterNavData($previousChapter, $lang) : null,
            'next_chapter' => $nextChapter ? $this->chapterNavData($nextChapter, $lang) : null,
            'progress' => $progress,
            'practice_url' => route('chapter.practice', [
                'course' => $chapter->course->slug,
                'chapter' => $chapter->slug,
                'lang' => $lang,
            ]),
            'ladders_url' => route('ladders.index', [
                'course' => $chapter->course->slug,
                'chapter' => $chapter->slug,
                'lang' => $lang,
            ]),
            'ladders' => $ladders,
        ];
    }

    private function chapterTheoryData(Chapter $chapter, string $lang): array
    {
        $text = $this->resolveText($chapter->texts, $lang);

        return [
            'slug' => $chapter->slug,
            'title' => $text?->title ?? $chapter->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'theory_html' => $this->normalizeMathHtml($text?->theory_html),
            'examples_html' => $this->normalizeMathHtml($text?->examples_html),
            'course' => $this->courseBreadcrumbData($chapter->course, $lang),
        ];
    }

    private function chapterCardData(Chapter $chapter, string $lang): array
    {
        $text = $this->resolveText($chapter->texts, $lang);

        return [
            'slug' => $chapter->slug,
            'title' => $text?->title ?? $chapter->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'problem_count' => (int) ($chapter->published_problems_count ?? 0),
            'ladders_count' => (int) ($chapter->published_ladders_count ?? 0),
        ];
    }

    private function chapterTheoryBlockData(Chapter $chapter, string $lang): array
    {
        $text = $this->resolveText($chapter->texts, $lang);

        return [
            'slug' => $chapter->slug,
            'title' => $text?->title ?? $chapter->slug,
            'description_html' => $this->normalizeMathHtml($text?->description_html),
            'theory_html' => $this->normalizeMathHtml($text?->theory_html),
            'examples_html' => $this->normalizeMathHtml($text?->examples_html),
        ];
    }

    private function problemCardData(
        Problem $problem,
        string $lang,
        ?int $chapterDisplayIndex = null,
        ?int $problemDisplayIndex = null,
        array $interactionState = ['can_interact' => false, 'solved' => [], 'bookmarked' => []]
    ): array {
        $text = $this->resolveText($problem->texts, $lang);
        $mainTag = $problem->tags->first();
        $mainTagText = $mainTag ? $this->resolveText($mainTag->texts, $lang) : null;
        $difficultyLevel = $this->normalizeDifficultyLevel($problem->difficulty);
        $displayNumber = null;
        if ($chapterDisplayIndex !== null && $problemDisplayIndex !== null) {
            $displayNumber = '#' . $chapterDisplayIndex . '.' . $problemDisplayIndex;
        }

        return [
            'id' => (int) $problem->id,
            'display_number' => $displayNumber,
            'code' => $problem->problem_code,
            'book_number' => $problem->book_number,
            'title' => $text?->title ?? $problem->problem_code,
            'statement_html' => $this->normalizeMathHtml($text?->statement_html),
            'hint_html' => $this->normalizeMathHtml($text?->hint_html),
            'solution_html' => $this->normalizeMathHtml($text?->solution_html),
            'main_tag' => $mainTagText?->title ?? null,
            'grade_badges' => $this->gradeBadges($problem->relationLoaded('gradeLevels') ? $problem->gradeLevels : collect(), $lang),
            'difficulty' => $difficultyLevel,
            'difficulty_label' => $this->difficultyLabel($difficultyLevel),
            'difficulty_stars' => $this->difficultyStars($difficultyLevel),
            'difficulty_class' => $this->difficultyClass($difficultyLevel),
            'url' => route('problem.show', ['problem' => $problem->problem_code, 'lang' => $lang]),
            'bookmark_toggle_url' => route('problem.bookmark.toggle', ['problem' => $problem->problem_code, 'lang' => $lang]),
            'solved_toggle_url' => route('problem.solved.toggle', ['problem' => $problem->problem_code, 'lang' => $lang]),
            'is_bookmarked' => isset($interactionState['bookmarked'][(int) $problem->id]),
            'is_solved' => isset($interactionState['solved'][(int) $problem->id]),
            'can_track_progress' => (bool) ($interactionState['can_interact'] ?? false),
            'login_url' => route('login', ['lang' => $lang]),
            'source_label' => $this->sourceLabel($problem, $lang),
        ];
    }

    private function problemPageData(
        Problem $problem,
        string $lang,
        array $interactionState,
        ?string $previousProblemCode,
        ?string $nextProblemCode
    ): array {
        $text = $this->resolveText($problem->texts, $lang);
        $difficultyLevel = $this->normalizeDifficultyLevel($problem->difficulty);

        return [
            'id' => (int) $problem->id,
            'code' => $problem->problem_code,
            'book_number' => $problem->book_number,
            'title' => $text?->title ?? $problem->problem_code,
            'statement_html' => $this->normalizeMathHtml($text?->statement_html),
            'hint_html' => $this->normalizeMathHtml($text?->hint_html),
            'solution_html' => $this->normalizeMathHtml($text?->solution_html),
            'teacher_note_html' => $this->normalizeMathHtml($text?->teacher_note_html),
            'media' => $this->problemMediaData($problem, $lang),
            'grade_badges' => $this->gradeBadges($problem->relationLoaded('gradeLevels') ? $problem->gradeLevels : collect(), $lang),
            'difficulty' => $difficultyLevel,
            'difficulty_label' => $this->difficultyLabel($difficultyLevel),
            'difficulty_stars' => $this->difficultyStars($difficultyLevel),
            'difficulty_class' => $this->difficultyClass($difficultyLevel),
            'course' => $this->courseBreadcrumbData($problem->chapter->course, $lang),
            'chapter' => [
                'slug' => $problem->chapter->slug,
                'title' => $this->resolveText($problem->chapter->texts, $lang)?->title ?? $problem->chapter->slug,
            ],
            'is_bookmarked' => isset($interactionState['bookmarked'][(int) $problem->id]),
            'is_solved' => isset($interactionState['solved'][(int) $problem->id]),
            'can_track_progress' => (bool) ($interactionState['can_interact'] ?? false),
            'bookmark_toggle_url' => route('problem.bookmark.toggle', ['problem' => $problem->problem_code, 'lang' => $lang]),
            'solved_toggle_url' => route('problem.solved.toggle', ['problem' => $problem->problem_code, 'lang' => $lang]),
            'practice_url' => route('chapter.practice', [
                'course' => $problem->chapter->course->slug,
                'chapter' => $problem->chapter->slug,
                'lang' => $lang,
            ]),
            'course_practice_url' => route('course.practice', [
                'course' => $problem->chapter->course->slug,
                'lang' => $lang,
            ]),
            'previous_problem_url' => $previousProblemCode
                ? route('problem.show', ['problem' => $previousProblemCode, 'lang' => $lang])
                : null,
            'next_problem_url' => $nextProblemCode
                ? route('problem.show', ['problem' => $nextProblemCode, 'lang' => $lang])
                : null,
            'login_url' => route('login', ['lang' => $lang]),
            'source_label' => $this->sourceLabel($problem, $lang),
        ];
    }

    private function ladderCardData(ProblemLadder $ladder, string $lang): array
    {
        $text = $this->resolveLadderText($ladder, $lang);

        return [
            'id' => (int) $ladder->id,
            'slug' => $ladder->slug,
            'title' => $text['title'],
            'description' => $text['description'],
            'course' => $ladder->course ? $this->courseBreadcrumbData($ladder->course, $lang) : null,
            'chapter' => $ladder->chapter ? $this->chapterNavData($ladder->chapter, $lang) : null,
            'main_method' => $text['main_method'],
            'difficulty' => $this->normalizeDifficultyLevel($ladder->difficulty_level),
            'difficulty_stars' => $this->difficultyStars($this->normalizeDifficultyLevel($ladder->difficulty_level)),
            'grade_badges' => $this->gradeBadges($ladder->relationLoaded('gradeLevels') ? $ladder->gradeLevels : collect(), $lang),
            'steps_count' => (int) ($ladder->steps_count ?? 0),
            'show_url' => route('ladders.show', ['ladder' => $ladder->slug, 'lang' => $lang]),
            'practice_url' => route('ladders.practice', ['ladder' => $ladder->slug, 'lang' => $lang]),
        ];
    }

    private function problemMediaData(Problem $problem, string $lang): array
    {
        $groups = [
            'statement' => [],
            'hint' => [],
            'solution' => [],
            'extra' => [],
        ];

        if (! $problem->relationLoaded('media')) {
            return $groups;
        }

        foreach ($problem->media as $media) {
            if (isset($media->is_published) && ! (bool) $media->is_published) {
                continue;
            }

            $mediaLang = trim((string) ($media->lang ?? ''));
            if ($mediaLang !== '' && ! in_array($mediaLang, [$lang, 'all', '*'], true)) {
                continue;
            }

            $item = $this->problemMediaItemData($media, $lang);
            if ($item === null) {
                continue;
            }

            $role = mb_strtolower(trim((string) ($media->role ?? 'statement')), 'UTF-8');
            $group = match ($role) {
                'hint' => 'hint',
                'solution', 'answer' => 'solution',
                'extra', 'teacher', 'teacher_note' => 'extra',
                default => 'statement',
            };

            $groups[$group][] = $item;
        }

        return $groups;
    }

    private function problemMediaItemData(mixed $media, string $lang): ?array
    {
        $path = trim((string) ($media->file_path ?? ''));
        if ($path === '') {
            return null;
        }

        $text = $media->relationLoaded('texts') ? $this->resolveText($media->texts, $lang) : null;

        return [
            'id' => (int) $media->id,
            'role' => (string) ($media->role ?? ''),
            'url' => $this->mediaUrl($path),
            'name' => $media->original_name ?: basename($path),
            'mime_type' => (string) ($media->mime_type ?? ''),
            'caption' => $this->normalizeMathHtml($text?->caption_html),
            'alt' => $text?->alt_text ?: ($media->original_name ?: ''),
        ];
    }

    private function mediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return Storage::url($path);
    }

    private function ladderPageData(ProblemLadder $ladder, string $lang, array $interactionState): array
    {
        $stepProblems = $ladder->steps
            ->filter(fn ($step): bool => $step->problem !== null && (bool) $step->problem->is_published)
            ->values();

        return [
            ...$this->ladderCardData($ladder, $lang),
            'steps' => $stepProblems->map(function ($step, int $index) use ($lang, $interactionState): array {
                $problem = $step->problem;
                $problemText = $this->resolveText($problem->texts, $lang);

                return [
                    'id' => (int) $step->id,
                    'step_number' => $index + 1,
                    'step_type' => (string) $step->step_type,
                    'step_type_label' => $this->stepTypeLabel((string) $step->step_type),
                    'step_label' => null,
                    'step_title' => $problemText?->title ?? $problem->problem_code,
                    'difficulty' => $this->normalizeDifficultyLevel($step->difficulty_level),
                    'difficulty_stars' => $this->difficultyStars($this->normalizeDifficultyLevel($step->difficulty_level)),
                    'problem' => [
                        'id' => (int) $problem->id,
                        'code' => $problem->problem_code,
                        'title' => $problemText?->title ?? $problem->problem_code,
                        'url' => route('problem.show', ['problem' => $problem->problem_code, 'lang' => $lang]),
                        'is_solved' => isset($interactionState['solved'][(int) $problem->id]),
                        'is_bookmarked' => isset($interactionState['bookmarked'][(int) $problem->id]),
                    ],
                ];
            })->all(),
        ];
    }

    private function resolveLadderText(ProblemLadder $ladder, string $lang): array
    {
        $fallback = [
            'title' => (string) ($ladder->title ?: $ladder->slug),
            'description' => $this->normalizeMathHtml($ladder->description),
            'main_method' => $ladder->main_method,
        ];

        if (! $ladder->relationLoaded('texts')) {
            return $fallback;
        }

        $texts = $ladder->texts;
        if ($texts->isEmpty()) {
            return $fallback;
        }

        $preferredCodes = array_values(array_unique([$lang, 'en', 'ru']));
        foreach ($preferredCodes as $code) {
            $candidate = $texts->first(function ($item) use ($code): bool {
                return optional($item->language)->code === $code;
            });
            if ($candidate) {
                return [
                    'title' => $candidate->title ?: $fallback['title'],
                    'description' => $this->normalizeMathHtml($candidate->description ?: $fallback['description']),
                    'main_method' => $candidate->main_method ?: $fallback['main_method'],
                ];
            }
        }

        $first = $texts->first();
        if (! $first) {
            return $fallback;
        }

        return [
            'title' => $first->title ?: $fallback['title'],
            'description' => $this->normalizeMathHtml($first->description ?: $fallback['description']),
            'main_method' => $first->main_method ?: $fallback['main_method'],
        ];
    }

    private function courseBreadcrumbData(Course $course, string $lang): array
    {
        $text = $this->resolveText($course->texts, $lang);

        return [
            'slug' => $course->slug,
            'title' => $text?->title ?? $course->slug,
        ];
    }

    private function resolveText(EloquentCollection $texts, string $lang): mixed
    {
        if ($texts->isEmpty()) {
            return null;
        }

        return $texts->firstWhere('lang', $lang)
            ?? $texts->firstWhere('lang', 'en')
            ?? $texts->first();
    }

    private function isActiveCourse(Course $course): bool
    {
        if ($this->usesStatusColumn()) {
            return ($course->status ?? null) === 'active';
        }

        return (bool) $course->is_published;
    }

    private function usesStatusColumn(): bool
    {
        return Schema::hasColumn('courses', 'status');
    }

    private function chapterNavData(Chapter $chapter, string $lang): array
    {
        return [
            'slug' => $chapter->slug,
            'title' => $this->resolveText($chapter->texts, $lang)?->title ?? $chapter->slug,
        ];
    }

    private function difficultyLabel(int $difficulty): string
    {
        return __('public.level_label', ['level' => $difficulty]);
    }

    private function difficultyStars(int $difficulty): string
    {
        $difficulty = max(1, min(5, $difficulty));

        return str_repeat('★', $difficulty) . str_repeat('☆', 5 - $difficulty);
    }

    private function stepTypeLabel(string $stepType): string
    {
        return match ($stepType) {
            'warmup' => __('public.step_warmup'),
            'lemma' => __('public.step_lemma'),
            'direct' => __('public.step_direct'),
            'mixed' => __('public.step_mixed'),
            'target' => __('public.step_target'),
            'challenge' => __('public.step_challenge'),
            default => $stepType,
        };
    }

    private function difficultyClass(int $difficulty): string
    {
        return match (max(1, min(5, $difficulty))) {
            1 => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            2 => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            3 => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            4 => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            default => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        };
    }

    private function normalizeDifficultyLevel(mixed $raw): int
    {
        if (is_numeric($raw)) {
            return (int) max(1, min(5, (int) $raw));
        }

        $value = mb_strtolower(trim((string) $raw), 'UTF-8');
        if ($value === '') {
            return 1;
        }

        if (str_contains($value, 'easy') || str_contains($value, 'low') || str_contains($value, 'низ') || str_contains($value, 'легк')) {
            return 1;
        }
        if (str_contains($value, 'medium') || str_contains($value, 'med') || str_contains($value, 'средн')) {
            return 3;
        }
        if (str_contains($value, 'very hard') || str_contains($value, 'очень') || str_contains($value, 'expert')) {
            return 5;
        }
        if (str_contains($value, 'hard') || str_contains($value, 'high') || str_contains($value, 'высок')) {
            return 4;
        }

        return 1;
    }

    private function normalizeMathHtml(?string $html): string
    {
        if (! is_string($html) || $html === '') {
            return '';
        }

        if (
            str_contains($html, '\\\\(')
            || str_contains($html, '\\\\)')
            || str_contains($html, '\\\\[')
            || str_contains($html, '\\\\]')
        ) {
            return str_replace(
                ['\\\\(', '\\\\)', '\\\\[', '\\\\]'],
                ['\\(', '\\)', '\\[', '\\]'],
                $html
            );
        }

        return $html;
    }

    private function readPracticeFilters(Request $request): array
    {
        $status = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'unsolved', 'solved', 'bookmarked'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $level = $request->query('level');
        $level = is_numeric($level) ? (int) $level : null;
        if ($level !== null) {
            $level = max(1, min(5, $level));
        }

        return [
            'status' => $status,
            'level' => $level,
            'grade' => $this->readGradeFilter($request),
        ];
    }

    private function readGradeFilter(Request $request): ?int
    {
        $grade = $request->query('grade');
        if (! is_numeric($grade)) {
            return null;
        }

        $grade = (int) $grade;

        return ($grade >= 5 && $grade <= 11) ? $grade : null;
    }

    private function gradeLevelsAvailable(): bool
    {
        return Schema::hasTable('grade_levels');
    }

    /**
     * @return list<string>
     */
    private function problemCardRelations(string $prefix = ''): array
    {
        $relations = [
            $prefix.'texts',
            $prefix.'tags.texts',
        ];

        if ($this->gradeLevelsAvailable()) {
            $relations[] = $prefix.'gradeLevels';
        }

        return $relations;
    }

    /**
     * @return list<string>
     */
    private function problemPageRelations(): array
    {
        $relations = ['chapter.course', 'chapter.texts', 'texts', 'media.texts', 'tags.texts'];

        if ($this->gradeLevelsAvailable()) {
            $relations[] = 'gradeLevels';
        }

        return $relations;
    }

    private function publicGradeOptions(string $lang): array
    {
        if (! $this->gradeLevelsAvailable()) {
            return [];
        }

        return DB::table('grade_levels')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('grade_number')
            ->get()
            ->map(fn ($grade): array => [
                'number' => (int) $grade->grade_number,
                'label' => $lang === 'ru' ? (string) $grade->title_ru : (string) $grade->title_en,
            ])
            ->all();
    }

    private function gradeBadges(Collection $grades, string $lang): array
    {
        return $grades
            ->sortBy('grade_number')
            ->map(fn ($grade): string => $lang === 'ru' ? (string) $grade->title_ru : (string) $grade->title_en)
            ->filter()
            ->values()
            ->all();
    }

    private function sourceLabel(Problem $problem, string $lang): ?string
    {
        $separator = ' '."\u{00B7}".' ';
        $parts = array_filter([
            $problem->source_name,
            $problem->source_year,
            $problem->source_grade ? ($lang === 'ru' ? "\u{041A}\u{043B}\u{0430}\u{0441}\u{0441} ".$problem->source_grade : 'Grade '.$problem->source_grade) : null,
            $problem->source_problem_number ? ($lang === 'ru' ? "\u{0417}\u{0430}\u{0434}\u{0430}\u{0447}\u{0430} ".$problem->source_problem_number : 'Problem '.$problem->source_problem_number) : null,
        ]);

        return $parts === [] ? null : implode($separator, $parts);
    }

    private function interactionStateForProblems(Collection $problemIds): array
    {
        $userId = auth()->id();
        if (! $userId) {
            return [
                'can_interact' => false,
                'bookmarked' => [],
                'solved' => [],
            ];
        }

        $ids = $problemIds
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [
                'can_interact' => true,
                'bookmarked' => [],
                'solved' => [],
            ];
        }

        $bookmarkedIds = DB::table('bookmarks')
            ->where('user_id', (int) $userId)
            ->whereIn('problem_id', $ids)
            ->pluck('problem_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $solvedIds = DB::table('user_problem_progress')
            ->where('user_id', (int) $userId)
            ->where('status', 'solved')
            ->whereIn('problem_id', $ids)
            ->pluck('problem_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return [
            'can_interact' => true,
            'bookmarked' => array_fill_keys($bookmarkedIds, true),
            'solved' => array_fill_keys($solvedIds, true),
        ];
    }

    private function buildProgressData(Collection $problemIds, array $interactionState): array
    {
        $uniqueIds = $problemIds
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
        $total = $uniqueIds->count();

        if (! ($interactionState['can_interact'] ?? false) || $total === 0) {
            return [
                'total' => $total,
                'solved' => 0,
                'percent' => 0,
            ];
        }

        $solvedCount = collect(array_keys($interactionState['solved'] ?? []))
            ->map(fn ($id): int => (int) $id)
            ->intersect($uniqueIds)
            ->count();

        return [
            'total' => $total,
            'solved' => $solvedCount,
            'percent' => $total > 0 ? (int) round(($solvedCount / $total) * 100) : 0,
        ];
    }

    private function effectivePracticeStatus(string $requestedStatus, bool $canInteract): string
    {
        if (! $canInteract && in_array($requestedStatus, ['solved', 'bookmarked'], true)) {
            return 'all';
        }

        return $requestedStatus;
    }

    private function matchesPracticeFilters(Problem $problem, ?int $level, ?int $grade, string $status, array $interactionState): bool
    {
        if ($level !== null && $this->normalizeDifficultyLevel($problem->difficulty) !== $level) {
            return false;
        }

        if ($grade !== null && $this->gradeLevelsAvailable()) {
            $hasGrade = $problem->relationLoaded('gradeLevels')
                && $problem->gradeLevels->contains('grade_number', $grade);
            if (! $hasGrade) {
                return false;
            }
        }

        $problemId = (int) $problem->id;
        $isSolved = isset($interactionState['solved'][$problemId]);
        $isBookmarked = isset($interactionState['bookmarked'][$problemId]);

        return match ($status) {
            'solved' => $isSolved,
            'unsolved' => ! $isSolved,
            'bookmarked' => $isBookmarked,
            default => true,
        };
    }

    private function interactionAuthRequiredResponse(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => 'Authentication required.',
                'login_url' => route('login', ['lang' => (string) $request->query('lang', $this->currentLanguage())]),
            ], 401);
        }

        return redirect()->route('login', ['lang' => (string) $request->query('lang', $this->currentLanguage())]);
    }

    private function calculateChapterProgress(int $chapterId, int $userId): ?array
    {
        $total = Problem::query()
            ->where('chapter_id', $chapterId)
            ->where('is_published', true)
            ->count();

        if ($total === 0) {
            return null;
        }

        $solved = DB::table('user_problem_progress')
            ->join('problems', 'problems.id', '=', 'user_problem_progress.problem_id')
            ->where('user_problem_progress.user_id', $userId)
            ->where('user_problem_progress.status', 'solved')
            ->where('problems.chapter_id', $chapterId)
            ->where('problems.is_published', true)
            ->count();

        $percent = (int) round(($solved / $total) * 100);
        $status = 'not_started';
        if ($solved > 0 && $solved < $total) {
            $status = 'in_progress';
        } elseif ($solved >= $total) {
            $status = 'completed';
        }

        return [
            'status' => $status,
            'percent_complete' => $percent,
        ];
    }
}
