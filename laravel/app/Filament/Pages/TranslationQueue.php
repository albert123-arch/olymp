<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ChapterTextResource;
use App\Filament\Resources\ProblemLadderResource;
use App\Filament\Resources\ProblemResource;
use App\Filament\Resources\ProblemTextResource;
use App\Models\Chapter;
use App\Models\ChapterText;
use App\Models\Course;
use App\Models\Problem;
use App\Models\ProblemLadder;
use App\Models\ProblemLadderText;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class TranslationQueue extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-language';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Translation Queue';

    protected static ?string $title = 'Translation Queue';

    protected static ?string $slug = 'translation-queue';

    protected string $view = 'filament.pages.translation-queue';

    public ?int $selectedCourseId = null;

    public ?int $selectedChapterId = null;

    public string $contentType = 'all';

    public string $issueType = 'all';

    public function mount(): void
    {
        $courseId = request()->integer('course_id');
        $chapterId = request()->integer('chapter_id');

        $defaultCourse = Course::query()->orderBy('sort_order')->orderBy('id')->first();
        $this->selectedCourseId = $courseId > 0 ? $courseId : ($defaultCourse ? (int) $defaultCourse->id : null);
        $this->syncSelectedChapter($chapterId > 0 ? $chapterId : null);
    }

    public function updatedSelectedCourseId(): void
    {
        $this->syncSelectedChapter();
    }

    public function getViewData(): array
    {
        $courses = Course::query()->with('texts')->orderBy('sort_order')->orderBy('id')->get();
        $chapters = $this->chapterOptions();

        $problemMissing = $this->missingProblemTranslations();
        $chapterMissing = $this->missingChapterTranslations();
        $ladderMissing = $this->missingLadderTranslations();
        $encoding = $this->encodingIssues();
        $mathjax = $this->mathJaxIssues();
        $unpublished = $this->unpublishedProblems();
        $duplicates = $this->duplicateTextIssues();

        return [
            'courses' => $courses,
            'chapters' => $chapters,
            'contentTypes' => [
                'all' => 'All',
                'problems' => 'Problems',
                'chapters' => 'Chapters',
                'ladders' => 'Ladders',
            ],
            'issueTypes' => [
                'all' => 'All',
                'missing_en' => 'Missing EN',
                'missing_ru' => 'Missing RU',
                'broken_encoding' => 'Broken encoding',
                'broken_mathjax' => 'Broken MathJax',
                'unpublished' => 'Unpublished',
                'duplicate_texts' => 'Duplicate texts',
            ],
            'missingProblemRuToEn' => $problemMissing['ru_to_en'],
            'missingProblemEnToRu' => $problemMissing['en_to_ru'],
            'missingChapterRows' => $chapterMissing,
            'missingLadderRows' => $ladderMissing,
            'encodingRows' => $encoding,
            'mathjaxRows' => $mathjax,
            'unpublishedRows' => $unpublished,
            'duplicateRows' => $duplicates,
        ];
    }

    public function showSection(string $section): bool
    {
        $contentTypeMap = [
            'missing-problems' => 'problems',
            'missing-chapters' => 'chapters',
            'missing-ladders' => 'ladders',
            'encoding' => 'all',
            'mathjax' => 'all',
            'unpublished' => 'problems',
            'duplicates' => 'all',
        ];
        $issueTypeMap = [
            'missing-problems' => ['all', 'missing_en', 'missing_ru'],
            'missing-chapters' => ['all', 'missing_en', 'missing_ru'],
            'missing-ladders' => ['all', 'missing_en', 'missing_ru'],
            'encoding' => ['all', 'broken_encoding'],
            'mathjax' => ['all', 'broken_mathjax'],
            'unpublished' => ['all', 'unpublished'],
            'duplicates' => ['all', 'duplicate_texts'],
        ];

        $sectionContent = $contentTypeMap[$section] ?? 'all';
        $contentAllowed = $this->contentType === 'all' || $sectionContent === 'all' || $sectionContent === $this->contentType;
        $issueAllowed = in_array($this->issueType, $issueTypeMap[$section] ?? ['all'], true);

        return $contentAllowed && $issueAllowed;
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

    private function scopedProblemsQuery()
    {
        $query = Problem::query()
            ->with(['chapter.texts', 'texts'])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($this->selectedChapterId !== null) {
            $query->where('chapter_id', $this->selectedChapterId);
        } elseif ($this->selectedCourseId !== null) {
            $query->whereIn(
                'chapter_id',
                Chapter::query()->where('course_id', $this->selectedCourseId)->pluck('id')
            );
        }

        return $query;
    }

    private function scopedChapterTextQuery()
    {
        $query = ChapterText::query()
            ->with(['chapter.texts'])
            ->orderBy('chapter_id')
            ->orderBy('id');

        if ($this->selectedChapterId !== null) {
            $query->where('chapter_id', $this->selectedChapterId);
        } elseif ($this->selectedCourseId !== null) {
            $query->whereIn(
                'chapter_id',
                Chapter::query()->where('course_id', $this->selectedCourseId)->pluck('id')
            );
        }

        return $query;
    }

    private function scopedLaddersQuery()
    {
        $query = ProblemLadder::query()
            ->with(['chapter.texts', 'texts.language'])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($this->selectedChapterId !== null) {
            $query->where('chapter_id', $this->selectedChapterId);
        } elseif ($this->selectedCourseId !== null) {
            $query->where('course_id', $this->selectedCourseId);
        }

        return $query;
    }

    private function missingProblemTranslations(): array
    {
        $ruToEn = [];
        $enToRu = [];

        foreach ($this->scopedProblemsQuery()->get() as $problem) {
            $ru = $problem->texts->firstWhere('lang', 'ru');
            $en = $problem->texts->firstWhere('lang', 'en');

            $hasRu = $ru && filled($ru->statement_html);
            $hasEn = $en && filled($en->statement_html);

            $base = [
                'code' => (string) $problem->problem_code,
                'chapter' => (string) ($problem->chapter?->slug ?? ''),
                'ru_title' => (string) ($ru?->title ?? ''),
                'en_title' => (string) ($en?->title ?? ''),
                'difficulty' => (int) ($problem->difficulty ?? 1),
                'is_published' => (bool) $problem->is_published,
                'edit_problem_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
                'edit_ru_url' => $ru
                    ? ProblemTextResource::getUrl('edit', ['record' => $ru->id])
                    : ProblemTextResource::getUrl('create', ['problem_id' => $problem->id, 'lang' => 'ru']),
                'edit_en_url' => $en
                    ? ProblemTextResource::getUrl('edit', ['record' => $en->id])
                    : ProblemTextResource::getUrl('create', ['problem_id' => $problem->id, 'lang' => 'en']),
            ];

            if ($hasRu && ! $hasEn) {
                $ruToEn[] = $base;
            }
            if ($hasEn && ! $hasRu) {
                $enToRu[] = $base;
            }
        }

        return ['ru_to_en' => $ruToEn, 'en_to_ru' => $enToRu];
    }

    private function missingChapterTranslations(): array
    {
        $rows = [];

        $chapterIds = $this->scopedChapterTextQuery()->get()->pluck('chapter_id')->unique()->values();
        foreach ($chapterIds as $chapterId) {
            $ru = ChapterText::query()->where('chapter_id', $chapterId)->where('lang', 'ru')->first();
            $en = ChapterText::query()->where('chapter_id', $chapterId)->where('lang', 'en')->first();
            $chapter = Chapter::query()->with('texts')->find($chapterId);
            if (! $chapter) {
                continue;
            }

            $ruTheory = filled($ru?->theory_html);
            $enTheory = filled($en?->theory_html);
            $ruExamples = filled($ru?->examples_html);
            $enExamples = filled($en?->examples_html);

            $issues = [];
            if ($ruTheory && ! $enTheory) {
                $issues[] = 'EN theory missing';
            }
            if ($enTheory && ! $ruTheory) {
                $issues[] = 'RU theory missing';
            }
            if ($ruExamples && ! $enExamples) {
                $issues[] = 'EN examples missing';
            }
            if ($enExamples && ! $ruExamples) {
                $issues[] = 'RU examples missing';
            }

            if ($issues === []) {
                continue;
            }

            $rows[] = [
                'chapter' => $chapter->slug,
                'issues' => $issues,
                'edit_ru_url' => $ru
                    ? ChapterTextResource::getUrl('edit', ['record' => $ru->id])
                    : ChapterTextResource::getUrl('create', ['chapter_id' => $chapter->id, 'lang' => 'ru']),
                'edit_en_url' => $en
                    ? ChapterTextResource::getUrl('edit', ['record' => $en->id])
                    : ChapterTextResource::getUrl('create', ['chapter_id' => $chapter->id, 'lang' => 'en']),
            ];
        }

        return $rows;
    }

    private function missingLadderTranslations(): array
    {
        $rows = [];

        foreach ($this->scopedLaddersQuery()->get() as $ladder) {
            $hasRu = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'ru' && filled($text->title));
            $hasEn = $ladder->texts->contains(fn (ProblemLadderText $text): bool => optional($text->language)->code === 'en' && filled($text->title));

            if ($hasRu && $hasEn) {
                continue;
            }

            $issues = [];
            if (! $hasRu) {
                $issues[] = 'RU missing';
            }
            if (! $hasEn) {
                $issues[] = 'EN missing';
            }

            $ladderTextResourceClass = 'App\\Filament\\Resources\\ProblemLadderTextResource';
            $ladderTextUrl = null;
            if (class_exists($ladderTextResourceClass) && method_exists($ladderTextResourceClass, 'getUrl')) {
                $ladderTextUrl = $ladderTextResourceClass::getUrl();
            }

            $rows[] = [
                'slug' => $ladder->slug,
                'title' => $ladder->title,
                'issues' => $issues,
                'edit_ladder_url' => ProblemLadderResource::getUrl('edit', ['record' => $ladder]),
                'edit_ladder_text_url' => $ladderTextUrl,
            ];
        }

        return $rows;
    }

    private function encodingIssues(): array
    {
        $issues = [];

        $check = function (?string $value): array {
            $flags = [];
            if (! is_string($value) || $value === '') {
                return $flags;
            }
            if (preg_match('/&#\d{2,6};/u', $value) === 1) {
                $flags[] = 'numeric-entity';
            }
            if (preg_match('/Ð|Ñ|Ã|Â/u', $value) === 1) {
                $flags[] = 'mojibake';
            }
            return $flags;
        };

        if ($this->contentType === 'all' || $this->contentType === 'problems') {
            foreach ($this->scopedProblemsQuery()->get() as $problem) {
                foreach ($problem->texts as $text) {
                    foreach (['title', 'statement_html', 'hint_html', 'solution_html'] as $field) {
                        $flags = $check($text->{$field});
                        if ($flags !== []) {
                            $issues[] = [
                                'scope' => 'problem_texts',
                                'code' => $problem->problem_code,
                                'lang' => $text->lang,
                                'field' => $field,
                                'flags' => $flags,
                                'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                                'edit_url' => ProblemTextResource::getUrl('edit', ['record' => $text->id]),
                            ];
                        }
                    }
                }
            }
        }

        if ($this->contentType === 'all' || $this->contentType === 'chapters') {
            $chapterTexts = $this->scopedChapterTextQuery()->get();
            foreach ($chapterTexts as $text) {
                foreach (['title', 'description_html', 'theory_html', 'examples_html'] as $field) {
                    $flags = $check($text->{$field});
                    if ($flags !== []) {
                        $issues[] = [
                            'scope' => 'chapter_texts',
                            'code' => (string) $text->chapter?->slug,
                            'lang' => $text->lang,
                            'field' => $field,
                            'flags' => $flags,
                            'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                            'edit_url' => ChapterTextResource::getUrl('edit', ['record' => $text->id]),
                        ];
                    }
                }
            }
        }

        if ($this->contentType === 'all' || $this->contentType === 'ladders') {
            foreach ($this->scopedLaddersQuery()->get() as $ladder) {
                foreach ($ladder->texts as $text) {
                    foreach (['title', 'description', 'main_method'] as $field) {
                        $flags = $check($text->{$field});
                        if ($flags !== []) {
                            $issues[] = [
                                'scope' => 'problem_ladder_texts',
                                'code' => $ladder->slug,
                                'lang' => (string) (optional($text->language)->code ?? ''),
                                'field' => $field,
                                'flags' => $flags,
                                'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                                'edit_url' => ProblemLadderResource::getUrl('edit', ['record' => $ladder]),
                            ];
                        }
                    }
                }
            }
        }

        return $issues;
    }

    private function mathJaxIssues(): array
    {
        $issues = [];
        if (! in_array($this->contentType, ['all', 'problems', 'chapters', 'ladders'], true)) {
            return $issues;
        }

        $check = function (?string $value): array {
            $flags = [];
            if (! is_string($value) || $value === '') {
                return $flags;
            }

            if (str_contains($value, '$$')) {
                $flags[] = '$$ delimiter';
            }
            if (str_contains($value, '\\\\(') || str_contains($value, '\\\\[')) {
                $flags[] = 'double-escaped backslashes';
            }

            $hasBrokenCommand = function (string $html): bool {
                preg_match_all('/\\\\\\((.*?)\\\\\\)|\\\\\\[(.*?)\\\\\\]|\\$(.*?)\\$/su', $html, $matches, PREG_SET_ORDER);
                if (empty($matches)) {
                    return false;
                }
                $patterns = [
                    '/(?<!\\\\)\bcdot\b/u',
                    '/(?<!\\\\)\bfrac\b/u',
                    '/(?<!\\\\)\bsqrt\b/u',
                    '/(?<!\\\\)\bbinom\b/u',
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
            };

            if ($hasBrokenCommand($value)) {
                $flags[] = 'possible broken command';
            }

            return $flags;
        };

        if ($this->contentType === 'all' || $this->contentType === 'problems') {
            foreach ($this->scopedProblemsQuery()->get() as $problem) {
                foreach ($problem->texts as $text) {
                    foreach (['statement_html', 'hint_html', 'solution_html'] as $field) {
                        $flags = $check($text->{$field});
                        if ($flags !== []) {
                            $issues[] = [
                                'scope' => 'problem_texts',
                                'code' => $problem->problem_code,
                                'lang' => $text->lang,
                                'field' => $field,
                                'flags' => $flags,
                                'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                                'edit_url' => ProblemTextResource::getUrl('edit', ['record' => $text->id]),
                            ];
                        }
                    }
                }
            }
        }

        if ($this->contentType === 'all' || $this->contentType === 'chapters') {
            foreach ($this->scopedChapterTextQuery()->get() as $text) {
                foreach (['description_html', 'theory_html', 'examples_html'] as $field) {
                    $flags = $check($text->{$field});
                    if ($flags !== []) {
                        $issues[] = [
                            'scope' => 'chapter_texts',
                            'code' => (string) $text->chapter?->slug,
                            'lang' => $text->lang,
                            'field' => $field,
                            'flags' => $flags,
                            'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                            'edit_url' => ChapterTextResource::getUrl('edit', ['record' => $text->id]),
                        ];
                    }
                }
            }
        }

        if ($this->contentType === 'all' || $this->contentType === 'ladders') {
            foreach ($this->scopedLaddersQuery()->get() as $ladder) {
                foreach ($ladder->texts as $text) {
                    foreach (['description', 'main_method'] as $field) {
                        $flags = $check($text->{$field});
                        if ($flags !== []) {
                            $issues[] = [
                                'scope' => 'problem_ladder_texts',
                                'code' => $ladder->slug,
                                'lang' => (string) (optional($text->language)->code ?? ''),
                                'field' => $field,
                                'flags' => $flags,
                                'snippet' => mb_substr(strip_tags((string) $text->{$field}), 0, 120, 'UTF-8'),
                                'edit_url' => ProblemLadderResource::getUrl('edit', ['record' => $ladder]),
                            ];
                        }
                    }
                }
            }
        }

        return $issues;
    }

    private function unpublishedProblems(): array
    {
        return $this->scopedProblemsQuery()
            ->where('is_published', false)
            ->get()
            ->map(function (Problem $problem): array {
                $ru = $problem->texts->firstWhere('lang', 'ru');
                $en = $problem->texts->firstWhere('lang', 'en');

                return [
                    'code' => (string) $problem->problem_code,
                    'chapter' => (string) ($problem->chapter?->slug ?? ''),
                    'difficulty' => (int) ($problem->difficulty ?? 1),
                    'ru_title' => (string) ($ru?->title ?? ''),
                    'en_title' => (string) ($en?->title ?? ''),
                    'edit_problem_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
                ];
            })
            ->all();
    }

    private function duplicateTextIssues(): array
    {
        $rows = [];

        $problemQuery = DB::table('problem_texts')
            ->join('problems', 'problems.id', '=', 'problem_texts.problem_id')
            ->select('problem_texts.problem_id', 'problem_texts.lang', DB::raw('COUNT(*) as c'))
            ->groupBy('problem_texts.problem_id', 'problem_texts.lang')
            ->having('c', '>', 1);

        if ($this->selectedChapterId !== null) {
            $problemQuery->where('problems.chapter_id', $this->selectedChapterId);
        } elseif ($this->selectedCourseId !== null) {
            $chapterIds = Chapter::query()->where('course_id', $this->selectedCourseId)->pluck('id');
            $problemQuery->whereIn('problems.chapter_id', $chapterIds);
        }

        $problemDup = $problemQuery->get();
        if ($problemDup->isNotEmpty()) {
            $problemMap = Problem::query()->whereIn('id', $problemDup->pluck('problem_id'))->get()->keyBy('id');
            foreach ($problemDup as $row) {
                $problem = $problemMap->get($row->problem_id);
                if (! $problem) {
                    continue;
                }
                $rows[] = [
                    'scope' => 'problem_texts',
                    'code' => (string) $problem->problem_code,
                    'lang' => (string) $row->lang,
                    'count' => (int) $row->c,
                    'edit_url' => ProblemResource::getUrl('edit', ['record' => $problem]),
                ];
            }
        }

        $chapterQuery = DB::table('chapter_texts')
            ->join('chapters', 'chapters.id', '=', 'chapter_texts.chapter_id')
            ->select('chapter_texts.chapter_id', 'chapter_texts.lang', DB::raw('COUNT(*) as c'))
            ->groupBy('chapter_texts.chapter_id', 'chapter_texts.lang')
            ->having('c', '>', 1);

        if ($this->selectedChapterId !== null) {
            $chapterQuery->where('chapter_texts.chapter_id', $this->selectedChapterId);
        } elseif ($this->selectedCourseId !== null) {
            $chapterQuery->where('chapters.course_id', $this->selectedCourseId);
        }

        $chapterDup = $chapterQuery->get();
        if ($chapterDup->isNotEmpty()) {
            $chapterMap = Chapter::query()->whereIn('id', $chapterDup->pluck('chapter_id'))->get()->keyBy('id');
            foreach ($chapterDup as $row) {
                $chapter = $chapterMap->get($row->chapter_id);
                if (! $chapter) {
                    continue;
                }
                $rows[] = [
                    'scope' => 'chapter_texts',
                    'code' => (string) $chapter->slug,
                    'lang' => (string) $row->lang,
                    'count' => (int) $row->c,
                    'edit_url' => ChapterTextResource::getUrl(),
                ];
            }
        }

        return $rows;
    }
}

