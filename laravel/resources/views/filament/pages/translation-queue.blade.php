<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
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
                    <label class="block text-sm font-medium mb-1">Content type</label>
                    <select wire:model.live="contentType" class="fi-input block w-full rounded-lg border-gray-300">
                        @foreach ($contentTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Issue type</label>
                    <select wire:model.live="issueType" class="fi-input block w-full rounded-lg border-gray-300">
                        @foreach ($issueTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filament::section>

        @if ($this->showSection('missing-problems'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">1. Missing problem translations</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border p-4">
                        <h4 class="font-medium mb-2">RU exists, EN missing</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2 text-left">Code</th>
                                    <th class="p-2 text-left">Chapter</th>
                                    <th class="p-2 text-left">RU title</th>
                                    <th class="p-2 text-left">Diff</th>
                                    <th class="p-2 text-left">Pub</th>
                                    <th class="p-2 text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($missingProblemRuToEn as $row)
                                    <tr class="border-b align-top">
                                        <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                        <td class="p-2">{{ $row['chapter'] }}</td>
                                        <td class="p-2">{{ $row['ru_title'] }}</td>
                                        <td class="p-2">{{ $row['difficulty'] }}</td>
                                        <td class="p-2">{!! $row['is_published'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">
                                            <div class="flex flex-wrap gap-1">
                                                <a href="{{ $row['edit_problem_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit problem</a>
                                                <a href="{{ $row['edit_ru_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit RU</a>
                                                <a href="{{ $row['edit_en_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit EN</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-3 text-gray-500">No items.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="rounded-lg border p-4">
                        <h4 class="font-medium mb-2">EN exists, RU missing</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2 text-left">Code</th>
                                    <th class="p-2 text-left">Chapter</th>
                                    <th class="p-2 text-left">EN title</th>
                                    <th class="p-2 text-left">Diff</th>
                                    <th class="p-2 text-left">Pub</th>
                                    <th class="p-2 text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($missingProblemEnToRu as $row)
                                    <tr class="border-b align-top">
                                        <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                        <td class="p-2">{{ $row['chapter'] }}</td>
                                        <td class="p-2">{{ $row['en_title'] }}</td>
                                        <td class="p-2">{{ $row['difficulty'] }}</td>
                                        <td class="p-2">{!! $row['is_published'] ? '✅' : '❌' !!}</td>
                                        <td class="p-2">
                                            <div class="flex flex-wrap gap-1">
                                                <a href="{{ $row['edit_problem_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit problem</a>
                                                <a href="{{ $row['edit_ru_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit RU</a>
                                                <a href="{{ $row['edit_en_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit EN</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-3 text-gray-500">No items.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('missing-chapters'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">2. Missing chapter translations</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Chapter</th>
                            <th class="p-2 text-left">Issues</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($missingChapterRows as $row)
                            <tr class="border-b">
                                <td class="p-2">{{ $row['chapter'] }}</td>
                                <td class="p-2">{{ implode(', ', $row['issues']) }}</td>
                                <td class="p-2">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ $row['edit_ru_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit RU</a>
                                        <a href="{{ $row['edit_en_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit EN</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('missing-ladders'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">3. Missing ladder translations</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Slug</th>
                            <th class="p-2 text-left">Title</th>
                            <th class="p-2 text-left">Issues</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($missingLadderRows as $row)
                            <tr class="border-b">
                                <td class="p-2 font-mono">{{ $row['slug'] }}</td>
                                <td class="p-2">{{ $row['title'] }}</td>
                                <td class="p-2">{{ implode(', ', $row['issues']) }}</td>
                                <td class="p-2">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ $row['edit_ladder_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Edit ladder</a>
                                        @if($row['edit_ladder_text_url'])
                                            <a href="{{ $row['edit_ladder_text_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit ladder texts</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('encoding'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">4. Encoding issues</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Scope</th>
                            <th class="p-2 text-left">Code</th>
                            <th class="p-2 text-left">Lang</th>
                            <th class="p-2 text-left">Field</th>
                            <th class="p-2 text-left">Flags</th>
                            <th class="p-2 text-left">Snippet</th>
                            <th class="p-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($encodingRows as $row)
                            <tr class="border-b align-top">
                                <td class="p-2">{{ $row['scope'] }}</td>
                                <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                <td class="p-2">{{ $row['lang'] }}</td>
                                <td class="p-2">{{ $row['field'] }}</td>
                                <td class="p-2">{{ implode(', ', $row['flags']) }}</td>
                                <td class="p-2">{{ $row['snippet'] }}</td>
                                <td class="p-2"><a href="{{ $row['edit_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('mathjax'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">5. MathJax issues</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Scope</th>
                            <th class="p-2 text-left">Code</th>
                            <th class="p-2 text-left">Lang</th>
                            <th class="p-2 text-left">Field</th>
                            <th class="p-2 text-left">Flags</th>
                            <th class="p-2 text-left">Snippet</th>
                            <th class="p-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($mathjaxRows as $row)
                            <tr class="border-b align-top">
                                <td class="p-2">{{ $row['scope'] }}</td>
                                <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                <td class="p-2">{{ $row['lang'] }}</td>
                                <td class="p-2">{{ $row['field'] }}</td>
                                <td class="p-2">{{ implode(', ', $row['flags']) }}</td>
                                <td class="p-2">{{ $row['snippet'] }}</td>
                                <td class="p-2"><a href="{{ $row['edit_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('unpublished'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">6. Unpublished problems</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Code</th>
                            <th class="p-2 text-left">Chapter</th>
                            <th class="p-2 text-left">RU title</th>
                            <th class="p-2 text-left">EN title</th>
                            <th class="p-2 text-left">Difficulty</th>
                            <th class="p-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($unpublishedRows as $row)
                            <tr class="border-b">
                                <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                <td class="p-2">{{ $row['chapter'] }}</td>
                                <td class="p-2">{{ $row['ru_title'] }}</td>
                                <td class="p-2">{{ $row['en_title'] }}</td>
                                <td class="p-2">{{ $row['difficulty'] }}</td>
                                <td class="p-2"><a href="{{ $row['edit_problem_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if ($this->showSection('duplicates'))
            <x-filament::section>
                <h3 class="text-base font-semibold mb-3">7. Duplicate texts</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Scope</th>
                            <th class="p-2 text-left">Code</th>
                            <th class="p-2 text-left">Lang</th>
                            <th class="p-2 text-left">Count</th>
                            <th class="p-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($duplicateRows as $row)
                            <tr class="border-b">
                                <td class="p-2">{{ $row['scope'] }}</td>
                                <td class="p-2 font-mono">{{ $row['code'] }}</td>
                                <td class="p-2">{{ $row['lang'] }}</td>
                                <td class="p-2">{{ $row['count'] }}</td>
                                <td class="p-2"><a href="{{ $row['edit_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-3 text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

