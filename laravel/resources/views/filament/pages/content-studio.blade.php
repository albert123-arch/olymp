<x-filament-panels::page>
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
                    @foreach ($languages as $code => $label)
                        <option value="{{ $code }}">{{ strtoupper($code) }} — {{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ $moduleWorkspaceUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Module Workspace</a>
            <a href="{{ $translationQueueUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Translation Queue</a>
        </div>

        <div class="flex flex-wrap gap-2 border-b pb-3">
            @foreach ($tabs as $tab)
                @php
                    $labels = [
                        'overview' => 'Overview',
                        'theory' => 'Theory',
                        'examples' => 'Examples',
                        'problems' => 'Problems',
                        'ladders' => 'Ladders',
                        'missing-translations' => 'Missing Translations',
                        'validation' => 'Validation',
                    ];
                @endphp
                <button
                    type="button"
                    wire:click="setActiveTab('{{ $tab }}')"
                    class="px-3 py-1.5 text-sm rounded-md border {{ $activeTab === $tab ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300' }}"
                >
                    {{ $labels[$tab] ?? $tab }}
                </button>
            @endforeach
        </div>

        @if (! $selectedCourse || ! $selectedChapter)
            <x-filament::section>
                <p>Select a course and chapter to start.</p>
            </x-filament::section>
        @else
            @if ($activeTab === 'overview')
                <x-filament::section>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Selected course</div>
                            <div class="font-semibold mt-1">{{ $selectedCourse->slug }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Selected chapter</div>
                            <div class="font-semibold mt-1">{{ $selectedChapter->slug }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Ladders</div>
                            <div class="font-semibold mt-1">{{ $overview['ladders_total'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Problems total</div>
                            <div class="font-semibold mt-1">{{ $overview['problems_total'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Published</div>
                            <div class="font-semibold mt-1">{{ $overview['problems_published'] }}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="text-sm text-gray-500">Unpublished</div>
                            <div class="font-semibold mt-1">{{ $overview['problems_unpublished'] }}</div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <div class="font-medium mb-2">Theory availability</div>
                            <ul class="text-sm space-y-1">
                                <li>RU: {!! $overview['ru_theory'] ? '✅' : '❌' !!}</li>
                                <li>EN: {!! $overview['en_theory'] ? '✅' : '❌' !!}</li>
                            </ul>
                        </div>
                        <div class="rounded-lg border p-4">
                            <div class="font-medium mb-2">Examples availability</div>
                            <ul class="text-sm space-y-1">
                                <li>RU: {!! $overview['ru_examples'] ? '✅' : '❌' !!}</li>
                                <li>EN: {!! $overview['en_examples'] ? '✅' : '❌' !!}</li>
                            </ul>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'theory')
                <x-filament::section>
                    <div class="flex gap-2 mb-4">
                        <a href="{{ $theory['ru']['edit_url'] }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Edit RU Theory</a>
                        <a href="{{ $theory['en']['edit_url'] }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Edit EN Theory</a>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">RU Preview</h3>
                            <div class="prose max-w-none math-content">{!! $theory['ru']['content'] !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">EN Preview</h3>
                            <div class="prose max-w-none math-content">{!! $theory['en']['content'] !!}</div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'examples')
                <x-filament::section>
                    <div class="flex gap-2 mb-4">
                        <a href="{{ $examples['ru']['edit_url'] }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Edit RU Examples</a>
                        <a href="{{ $examples['en']['edit_url'] }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Edit EN Examples</a>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">RU Preview</h3>
                            <div class="prose max-w-none math-content">{!! $examples['ru']['content'] !!}</div>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">EN Preview</h3>
                            <div class="prose max-w-none math-content">{!! $examples['en']['content'] !!}</div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'problems')
                <x-filament::section>
                    <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
                        <a href="{{ $quickAddUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary">+ Quick Add Problem</a>
                    </div>
                    <div class="flex flex-wrap items-end gap-2 mb-4">
                        <button type="button" wire:click="publishSelected" class="fi-btn fi-btn-size-sm fi-btn-color-success">Publish selected</button>
                        <button type="button" wire:click="unpublishSelected" class="fi-btn fi-btn-size-sm fi-btn-color-danger">Unpublish selected</button>

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
                                <label class="block text-xs mb-1">Tags (comma separated)</label>
                                <input type="text" wire:model="bulkTags" class="fi-input rounded-md border-gray-300 min-w-64" placeholder="divisibility, parity">
                            </div>
                            <button type="button" wire:click="applyTagsSelected" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Set tags</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2 text-left"><input type="checkbox" onclick="document.querySelectorAll('[data-problem-checkbox]').forEach(el => { el.checked = this.checked; el.dispatchEvent(new Event('change')); });" /></th>
                                    <th class="p-2 text-left">Code</th>
                                    <th class="p-2 text-left">Title</th>
                                    <th class="p-2 text-left">Difficulty</th>
                                    <th class="p-2 text-left">Published</th>
                                    <th class="p-2 text-left">RU</th>
                                    <th class="p-2 text-left">EN</th>
                                    <th class="p-2 text-left">Tags</th>
                                    <th class="p-2 text-left">Sort</th>
                                    <th class="p-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($problems as $problem)
                                    <tr class="border-b align-top">
                                        <td class="p-2">
                                            <input
                                                data-problem-checkbox
                                                type="checkbox"
                                                value="{{ $problem['id'] }}"
                                                wire:model.live="selectedProblemIds"
                                            />
                                        </td>
                                        <td class="p-2 font-mono">{{ $problem['code'] }}</td>
                                        <td class="p-2">{{ $problem['title'] }}</td>
                                        <td class="p-2">{{ $problem['difficulty'] }}</td>
                                        <td class="p-2">{!! $problem['is_published'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">{!! $problem['has_ru'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">{!! $problem['has_en'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">{{ $problem['tags'] }}</td>
                                        <td class="p-2">{{ $problem['sort_order'] }}</td>
                                        <td class="p-2">
                                            <div class="flex flex-wrap gap-1">
                                                <a href="{{ $problem['edit_problem_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit problem</a>
                                                <a href="{{ $problem['edit_ru_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit RU text</a>
                                                <a href="{{ $problem['edit_en_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit EN text</a>
                                                @if ($problem['is_published'])
                                                    <button type="button" wire:click="unpublishProblem({{ $problem['id'] }})" class="fi-btn fi-btn-size-xs fi-btn-color-danger">Unpublish</button>
                                                @else
                                                    <button type="button" wire:click="publishProblem({{ $problem['id'] }})" class="fi-btn fi-btn-size-xs fi-btn-color-success">Publish</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="p-3 text-gray-500">No problems for this chapter.</td></tr>
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
                                    <th class="p-2 text-left">Method</th>
                                    <th class="p-2 text-left">Difficulty</th>
                                    <th class="p-2 text-left">Steps</th>
                                    <th class="p-2 text-left">Published</th>
                                    <th class="p-2 text-left">RU</th>
                                    <th class="p-2 text-left">EN</th>
                                    <th class="p-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ladders as $ladder)
                                    <tr class="border-b">
                                        <td class="p-2">{{ $ladder['title'] }}</td>
                                        <td class="p-2">{{ $ladder['main_method'] }}</td>
                                        <td class="p-2">{{ $ladder['difficulty'] }}</td>
                                        <td class="p-2">{{ $ladder['steps_count'] }}</td>
                                        <td class="p-2">{!! $ladder['is_published'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">{!! $ladder['has_ru'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">{!! $ladder['has_en'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">
                                            <div class="flex flex-wrap gap-1">
                                                <a href="{{ $ladder['edit_ladder_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit ladder</a>
                                                <a href="{{ $ladder['edit_ladder_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit ladder translations</a>
                                                <a href="{{ $ladder['edit_steps_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit steps</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="p-3 text-gray-500">No ladders linked to this chapter.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'missing-translations')
                <x-filament::section>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">Problems: RU exists, EN missing</h3>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @forelse ($missing['problems_ru_missing_en'] as $row)
                                    <li>{{ $row['code'] }}</li>
                                @empty
                                    <li class="list-none text-gray-500">No items.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">Problems: EN exists, RU missing</h3>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @forelse ($missing['problems_en_missing_ru'] as $row)
                                    <li>{{ $row['code'] }}</li>
                                @empty
                                    <li class="list-none text-gray-500">No items.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">Ladders missing EN</h3>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @forelse ($missing['ladders_missing_en'] as $row)
                                    <li>{{ $row['slug'] }} — {{ $row['title'] }}</li>
                                @empty
                                    <li class="list-none text-gray-500">No items.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium mb-2">Chapter EN blocks</h3>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @forelse ($missing['chapter_missing_en'] as $row)
                                    <li>{{ $row }}</li>
                                @empty
                                    <li class="list-none text-gray-500">No missing chapter EN blocks.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if ($activeTab === 'validation')
                <x-filament::section>
                    <h3 class="font-medium mb-3">Warnings only (no auto-fix)</h3>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @forelse ($validation as $warning)
                            <li>{{ $warning }}</li>
                        @empty
                            <li class="list-none text-green-700">No warnings for selected chapter.</li>
                        @endforelse
                    </ul>
                </x-filament::section>
            @endif
        @endif
    </div>

    <script>
        function contentStudioTypesetMath() {
            if (window.MathJax && window.MathJax.typesetPromise) {
                MathJax.typesetPromise(document.querySelectorAll('.math-content'));
            }
        }
        document.addEventListener('livewire:navigated', () => setTimeout(contentStudioTypesetMath, 120));
        document.addEventListener('DOMContentLoaded', () => setTimeout(contentStudioTypesetMath, 120));
    </script>
</x-filament-panels::page>
