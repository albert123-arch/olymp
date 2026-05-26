<x-filament-panels::page>
    <style>
        .module-workspace-editor {
            min-height: 450px;
            width: 100%;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
            line-height: 1.55;
        }

        .module-workspace-tab {
            border-radius: 0.625rem 0.625rem 0 0;
            border: 1px solid rgb(209 213 219);
            border-bottom: 0;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .module-workspace-tab-active {
            background: rgb(var(--primary-600));
            border-color: rgb(var(--primary-600));
            color: white;
        }

        .module-workspace-preview {
            min-height: 160px;
            max-height: 520px;
            overflow: auto;
        }
    </style>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-sm font-medium mb-1">Course</label>
                <select wire:model.live="selectedCourseId" class="fi-input block w-full rounded-lg border-gray-300">
                    @foreach ($courses as $course)
                        @php
                            $title = optional($course->texts->firstWhere('lang', 'ru'))->title
                                ?? optional($course->texts->firstWhere('lang', 'en'))->title
                                ?? $course->slug;
                        @endphp
                        <option value="{{ $course->id }}">{{ $title }} ({{ $course->slug }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Chapter</label>
                <select wire:model.live="selectedChapterId" class="fi-input block w-full rounded-lg border-gray-300">
                    @foreach ($chapters as $chapter)
                        @php
                            $title = optional($chapter->texts->firstWhere('lang', 'ru'))->title
                                ?? optional($chapter->texts->firstWhere('lang', 'en'))->title
                                ?? $chapter->slug;
                        @endphp
                        <option value="{{ $chapter->id }}">{{ $title }} ({{ $chapter->slug }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Language</label>
                <select wire:model.live="selectedLang" class="fi-input block w-full rounded-lg border-gray-300">
                    <option value="ru">RU</option>
                    <option value="en">EN</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                <a href="{{ $contentStudioUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Content Studio</a>
                <a href="{{ $quickAddUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">+ Quick Add Problem</a>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($publicChapterUrl)
                    <a href="{{ $publicChapterUrl }}" target="_blank" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Open Public Chapter</a>
                @endif
                @if($publicPracticeUrl)
                    <a href="{{ $publicPracticeUrl }}" target="_blank" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Open Practice</a>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2 border-b pb-3">
            @foreach ($tabs as $tab)
                @php
                    $labels = [
                        'overview' => 'Module Overview',
                        'theory' => 'Theory',
                        'examples' => 'Examples',
                        'problems' => 'Problems',
                        'ladders' => 'Ladders',
                        'checklist' => 'Publish Checklist',
                        'preview' => 'Preview',
                    ];
                @endphp
                <button
                    type="button"
                    wire:click="setActiveTab('{{ $tab }}')"
                    class="module-workspace-tab {{ $activeTab === $tab ? 'module-workspace-tab-active' : 'bg-white text-gray-700' }}"
                >
                    {{ $labels[$tab] ?? $tab }}
                </button>
            @endforeach
        </div>

        @if (! $selectedCourse || ! $selectedChapter)
            <x-filament::section>
                <p>Select a course and chapter.</p>
            </x-filament::section>
        @else
            @if ($activeTab === 'overview')
                <x-filament::section>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Chapter title</div>
                            <div class="font-semibold mt-1">{{ $overview['chapter_title'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Chapter slug</div>
                            <div class="font-semibold mt-1">{{ $overview['chapter_slug'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Course</div>
                            <div class="font-semibold mt-1">{{ $overview['course_slug'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Recommended grades</div>
                            <div class="font-semibold mt-1">{{ $overview['recommended_grades'] ?: 'Not set' }}</div>
                        </div>

                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Problems</div>
                            <div class="font-semibold mt-1">{{ $overview['problems_total'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Published</div>
                            <div class="font-semibold mt-1">{{ $overview['published_total'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Unpublished</div>
                            <div class="font-semibold mt-1">{{ $overview['unpublished_total'] }}</div>
                        </div>

                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">RU theory</div>
                            <div class="font-semibold mt-1">{!! $overview['ru_theory_exists'] ? '✅' : '❌' !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">EN theory</div>
                            <div class="font-semibold mt-1">{!! $overview['en_theory_exists'] ? '✅' : '❌' !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">RU examples</div>
                            <div class="font-semibold mt-1">{!! $overview['ru_examples_exists'] ? '✅' : '❌' !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">EN examples</div>
                            <div class="font-semibold mt-1">{!! $overview['en_examples_exists'] ? '✅' : '❌' !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Missing translations</div>
                            <div class="font-semibold mt-1">{{ $overview['missing_translations_count'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">MathJax warnings</div>
                            <div class="font-semibold mt-1">{{ $overview['mathjax_warnings_count'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Encoding warnings</div>
                            <div class="font-semibold mt-1">{{ $overview['encoding_warnings_count'] }}</div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'theory')
                <x-filament::section>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-medium">Theory Editor</h3>
                            <p class="text-sm text-gray-500">Edit RU and EN theory without leaving this chapter.</p>
                        </div>
                        <button type="button" wire:click="saveTheoryBoth" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Save both</button>
                    </div>

                    <div class="grid gap-5 xl:grid-cols-2">
                        <div class="space-y-3 rounded-xl border p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="block text-sm font-semibold">RU theory / notes</label>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$refresh" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Preview</button>
                                    <button type="button" wire:click="saveTheoryRu" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Save RU</button>
                                </div>
                            </div>
                            <textarea wire:model.defer="theoryRu" class="fi-input module-workspace-editor rounded-lg border-gray-300"></textarea>
                            <div class="rounded-lg border bg-gray-50 p-4 module-workspace-preview">
                                <div class="prose max-w-none math-content">
                                    {!! $theoryRu !!}
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 rounded-xl border p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="block text-sm font-semibold">EN theory / notes</label>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$refresh" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Preview</button>
                                    <button type="button" wire:click="saveTheoryEn" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Save EN</button>
                                </div>
                            </div>
                            <textarea wire:model.defer="theoryEn" class="fi-input module-workspace-editor rounded-lg border-gray-300"></textarea>
                            <div class="rounded-lg border bg-gray-50 p-4 module-workspace-preview">
                                <div class="prose max-w-none math-content">
                                    {!! $theoryEn !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'examples')
                <x-filament::section>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-medium">Examples Editor</h3>
                            <p class="text-sm text-gray-500">Edit worked examples in both languages on the same screen.</p>
                        </div>
                        <button type="button" wire:click="saveExamplesBoth" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Save both</button>
                    </div>

                    <div class="grid gap-5 xl:grid-cols-2">
                        <div class="space-y-3 rounded-xl border p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="block text-sm font-semibold">RU examples</label>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$refresh" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Preview</button>
                                    <button type="button" wire:click="saveExamplesRu" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Save RU</button>
                                </div>
                            </div>
                            <textarea wire:model.defer="examplesRu" class="fi-input module-workspace-editor rounded-lg border-gray-300"></textarea>
                            <div class="rounded-lg border bg-gray-50 p-4 module-workspace-preview">
                                <div class="prose max-w-none math-content">
                                    {!! $examplesRu !!}
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 rounded-xl border p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="block text-sm font-semibold">EN examples</label>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$refresh" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Preview</button>
                                    <button type="button" wire:click="saveExamplesEn" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Save EN</button>
                                </div>
                            </div>
                            <textarea wire:model.defer="examplesEn" class="fi-input module-workspace-editor rounded-lg border-gray-300"></textarea>
                            <div class="rounded-lg border bg-gray-50 p-4 module-workspace-preview">
                                <div class="prose max-w-none math-content">
                                    {!! $examplesEn !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'problems')
                <x-filament::section>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-medium">Problems</h3>
                            <p class="text-sm text-gray-500">Manage this chapter without losing the selected course, chapter, or language.</p>
                        </div>
                        <a href="{{ $quickAddUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">+ Quick Add Problem</a>
                    </div>

                    <div class="flex flex-wrap items-end gap-2 mb-4">
                        <button type="button" wire:click="publishSelected" class="fi-btn fi-btn-size-sm fi-btn-color-success">Publish selected</button>
                        <button type="button" wire:click="unpublishSelected" class="fi-btn fi-btn-size-sm fi-btn-color-danger">Unpublish selected</button>

                        @if(!empty($gradeOptions))
                            <div class="flex items-end gap-2">
                                <div>
                                    <label class="block text-xs mb-1">Grade filter</label>
                                    <select wire:model.live="selectedGradeFilter" class="fi-input rounded-md border-gray-300">
                                        <option value="">All grades</option>
                                        @foreach($gradeOptions as $id => $label)
                                            <option value="{{ $id }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-end gap-2">
                            <div>
                                <label class="block text-xs mb-1">Difficulty</label>
                                <select wire:model="bulkDifficulty" class="fi-input rounded-md border-gray-300">
                                    <option value="">--</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">Level {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="button" wire:click="setDifficultySelected" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Set difficulty</button>
                        </div>

                        <div class="flex items-end gap-2">
                            <div>
                                <label class="block text-xs mb-1">Sort start</label>
                                <input type="number" min="0" wire:model="bulkSortStart" class="fi-input rounded-md border-gray-300 w-28">
                            </div>
                            <button type="button" wire:click="setSortSelected" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Set sort</button>
                        </div>

                        @if(!empty($gradeOptions))
                            <div class="flex items-end gap-2">
                                <div>
                                    <label class="block text-xs mb-1">Assign grades</label>
                                    <select wire:model="bulkGradeIds" multiple class="fi-input rounded-md border-gray-300 min-w-48">
                                        @foreach($gradeOptions as $id => $label)
                                            <option value="{{ $id }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" wire:click="assignGradesSelected" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Assign grades</button>
                            </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2 text-left"><input type="checkbox" onclick="document.querySelectorAll('[data-mw-problem-checkbox]').forEach(el => { el.checked = this.checked; el.dispatchEvent(new Event('change')); });" /></th>
                                <th class="p-2 text-left">Code</th>
                                <th class="p-2 text-left">Title</th>
                                <th class="p-2 text-left">Diff</th>
                                <th class="p-2 text-left">Grades</th>
                                <th class="p-2 text-left">Pub</th>
                                <th class="p-2 text-left">RU</th>
                                <th class="p-2 text-left">EN</th>
                                <th class="p-2 text-left">Tags</th>
                                <th class="p-2 text-left">Source</th>
                                <th class="p-2 text-left">Sort</th>
                                <th class="p-2 text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($problems as $problem)
                                <tr class="border-b align-top">
                                    <td class="p-2">
                                        <input
                                            data-mw-problem-checkbox
                                            type="checkbox"
                                            value="{{ $problem['id'] }}"
                                            wire:model.live="selectedProblemIds"
                                        />
                                    </td>
                                    <td class="p-2 font-mono">{{ $problem['code'] }}</td>
                                    <td class="p-2">{{ $problem['title'] }}</td>
                                    <td class="p-2">{{ $problem['difficulty'] }}</td>
                                    <td class="p-2">{{ $problem['grades'] ?: '-' }}</td>
                                    <td class="p-2">{!! $problem['is_published'] ? '✅' : '❌' !!}</td>
                                    <td class="p-2">{!! $problem['has_ru'] ? '✅' : '❌' !!}</td>
                                    <td class="p-2">{!! $problem['has_en'] ? '✅' : '❌' !!}</td>
                                    <td class="p-2">{{ $problem['tags'] }}</td>
                                    <td class="p-2">{{ $problem['source'] ?: '-' }}</td>
                                    <td class="p-2">{{ $problem['sort_order'] }}</td>
                                    <td class="p-2">
                                        <div class="flex flex-wrap gap-1">
                                            <a href="{{ $problem['edit_problem_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit problem</a>
                                            <a href="{{ $problem['edit_ru_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit RU</a>
                                            <a href="{{ $problem['edit_en_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit EN</a>
                                            @if($problem['is_published'])
                                                <button type="button" wire:click="unpublishProblem({{ $problem['id'] }})" class="fi-btn fi-btn-size-xs fi-btn-color-danger">Unpublish</button>
                                            @else
                                                <button type="button" wire:click="publishProblem({{ $problem['id'] }})" class="fi-btn fi-btn-size-xs fi-btn-color-success">Publish</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="p-3 text-gray-500">No problems in this chapter.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'ladders')
                <x-filament::section>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2 text-left">Title</th>
                                <th class="p-2 text-left">Difficulty</th>
                                <th class="p-2 text-left">Grades</th>
                                <th class="p-2 text-left">Published</th>
                                <th class="p-2 text-left">Steps</th>
                                <th class="p-2 text-left">RU/EN</th>
                                <th class="p-2 text-left">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($ladders as $ladder)
                                <tr class="border-b">
                                    <td class="p-2">{{ $ladder['title'] }}</td>
                                    <td class="p-2">{{ $ladder['difficulty'] }}</td>
                                    <td class="p-2">{{ $ladder['grades'] ?: '-' }}</td>
                                    <td class="p-2">{!! $ladder['is_published'] ? '✅' : '❌' !!}</td>
                                    <td class="p-2">{{ $ladder['steps_count'] }}</td>
                                    <td class="p-2">
                                        @if($ladder['missing_translation_warning'])
                                            <span class="text-warning-700">⚠ Missing {{ $ladder['has_ru'] ? '' : 'RU ' }}{{ $ladder['has_en'] ? '' : 'EN' }}</span>
                                        @else
                                            <span>✅</span>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <a href="{{ $ladder['edit_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="p-3 text-gray-500">No ladders in this chapter.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'checklist')
                <x-filament::section>
                    <h3 class="font-medium mb-3">Publish Checklist</h3>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @forelse($checklist['warnings'] as $warning)
                            <li>{{ $warning['message'] }}</li>
                        @empty
                            <li class="list-none text-success-700">No checklist warnings.</li>
                        @endforelse
                    </ul>
                </x-filament::section>
            @endif

            @if ($activeTab === 'preview')
                <x-filament::section>
                    <h3 class="font-medium mb-3">Preview</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <h4 class="font-medium mb-2">Theory</h4>
                            <div class="prose max-w-none math-content">
                                {!! $selectedLang === 'ru' ? $theoryRu : $theoryEn !!}
                            </div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h4 class="font-medium mb-2">Examples</h4>
                            <div class="prose max-w-none math-content">
                                {!! $selectedLang === 'ru' ? $examplesRu : $examplesEn !!}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg border p-4">
                        <h4 class="font-medium mb-3">Problem cards preview</h4>
                        <div class="space-y-3">
                            @forelse($problems as $problem)
                                <div class="rounded-lg border p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-semibold">{{ $problem['code'] }} — {{ $problem['title'] }}</div>
                                        <div class="text-xs text-gray-600">Level {{ $problem['difficulty'] }}</div>
                                    </div>
                                    <div class="prose max-w-none mt-2 math-content">{!! $problem['statement_html'] !!}</div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No problems to preview.</p>
                            @endforelse
                        </div>
                    </div>
                </x-filament::section>
            @endif
        @endif
    </div>

    @include('filament.partials.admin-mathjax')
</x-filament-panels::page>
