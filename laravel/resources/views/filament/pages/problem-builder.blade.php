<x-filament-panels::page>
    <style>
        .problem-builder-editor {
            min-height: 220px;
            width: 100%;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
            line-height: 1.55;
        }

        .problem-builder-preview {
            max-height: 520px;
            overflow: auto;
        }

        .problem-builder-thumb {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }
    </style>

    <div class="space-y-6">
        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold">Problem Builder</h2>
                    <p class="text-sm text-gray-600">Create or edit one problem with RU/EN texts, tags, grades, source metadata, and diagrams.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($problemEditUrl)
                        <a href="{{ $problemEditUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Filament Edit</a>
                    @endif
                    <a href="{{ $backUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">{{ $backLabel }}</a>
                </div>
            </div>
        </x-filament::section>

        <form wire:submit.prevent="saveProblem" class="space-y-6">
            <x-filament::section>
                <h3 class="font-medium mb-3">Structure</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Course</label>
                        <select wire:model.live="selectedCourseId" class="fi-input block w-full rounded-lg border-gray-300">
                            @foreach($courses as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('selectedCourseId') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Chapter</label>
                        <select wire:model.live="selectedChapterId" class="fi-input block w-full rounded-lg border-gray-300">
                            @foreach($chapters as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('selectedChapterId') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Problem code</label>
                        <div class="flex gap-2">
                            <input wire:model.defer="problemCode" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            @if(! $problemId)
                                <button type="button" wire:click="suggestCode" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Suggest</button>
                            @endif
                        </div>
                        @error('problemCode') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Sort order</label>
                        <input wire:model.defer="sortOrder" type="number" min="0" class="fi-input block w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Difficulty level</label>
                        <select wire:model.defer="difficulty" class="fi-input block w-full rounded-lg border-gray-300">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <label class="flex items-center gap-2 pt-7 text-sm font-medium">
                        <input wire:model.defer="isPublished" type="checkbox" class="rounded border-gray-300">
                        Published
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2 mt-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Existing tags</label>
                        <select wire:model.defer="selectedTagIds" multiple class="fi-input block w-full rounded-lg border-gray-300 min-h-36">
                            @foreach($tags as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">New tags (comma separated)</label>
                        <input wire:model.defer="newTags" type="text" class="fi-input block w-full rounded-lg border-gray-300" placeholder="angle chasing, geometry">
                    </div>
                </div>

                @if($hasGradeLevels)
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Grade levels</label>
                        <select wire:model.defer="selectedGradeIds" multiple class="fi-input block w-full rounded-lg border-gray-300 min-h-32">
                            @foreach($grades as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </x-filament::section>

            @if($hasSourceMetadata)
                <x-filament::section>
                    <details>
                        <summary class="cursor-pointer font-medium">Source / Olympiad metadata</summary>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Source name</label>
                                <input wire:model.defer="sourceName" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Year</label>
                                <input wire:model.defer="sourceYear" type="number" min="1800" max="2200" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Round</label>
                                <input wire:model.defer="sourceRound" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Original grade</label>
                                <input wire:model.defer="sourceGrade" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Problem number</label>
                                <input wire:model.defer="sourceProblemNumber" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Source URL</label>
                                <input wire:model.defer="sourceUrl" type="url" class="fi-input block w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium mb-1">Internal source note</label>
                            <textarea wire:model.defer="sourceNote" rows="4" class="fi-input block w-full rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                    </details>
                </x-filament::section>
            @endif

            <div class="grid gap-6 xl:grid-cols-2">
                <x-filament::section>
                    <h3 class="font-medium mb-3">Russian text</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">RU title</label>
                            <input wire:model.defer="ruTitle" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                            @error('ruTitle') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">RU statement_html</label>
                            <textarea wire:model.defer="ruStatement" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                            @error('ruStatement') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">RU hint_html</label>
                            <textarea wire:model.defer="ruHint" rows="5" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">RU solution_html</label>
                            <textarea wire:model.defer="ruSolution" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">RU teacher_note_html</label>
                            <textarea wire:model.defer="ruTeacherNote" rows="5" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <h3 class="font-medium mb-3">English text</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">EN title</label>
                            <input wire:model.defer="enTitle" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">EN statement_html</label>
                            <textarea wire:model.defer="enStatement" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">EN hint_html</label>
                            <textarea wire:model.defer="enHint" rows="5" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">EN solution_html</label>
                            <textarea wire:model.defer="enSolution" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">EN teacher_note_html</label>
                            <textarea wire:model.defer="enTeacherNote" rows="5" class="fi-input rounded-lg border-gray-300 problem-builder-editor"></textarea>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <x-filament::section>
                <h3 class="font-medium mb-3">Media / Diagrams</h3>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach(['statement' => 'Statement media', 'hint' => 'Hint media', 'solution' => 'Solution media', 'extra' => 'Extra / reference media'] as $role => $label)
                        <div class="rounded-lg border p-3">
                            <label class="block text-sm font-medium mb-2">{{ $label }}</label>
                            <input
                                type="file"
                                multiple
                                wire:model="{{ $role }}Uploads"
                                class="block w-full text-sm"
                                accept="{{ $role === 'extra' ? '.svg,.png,.jpg,.jpeg,.webp,.pdf' : '.svg,.png,.jpg,.jpeg,.webp' }}"
                            >
                            <p class="text-xs text-gray-500 mt-2">Max 5 MB. Stored in /uploads/problems/{problem_id}/.</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2 text-left">Preview</th>
                            <th class="p-2 text-left">Role</th>
                            <th class="p-2 text-left">Sort</th>
                            <th class="p-2 text-left">Published</th>
                            <th class="p-2 text-left">RU caption / alt</th>
                            <th class="p-2 text-left">EN caption / alt</th>
                            <th class="p-2 text-left">Path</th>
                            <th class="p-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($mediaRows as $mediaId => $row)
                            <tr class="border-b align-top">
                                <td class="p-2">
                                    @if(str_starts_with((string) $row['mime_type'], 'image/'))
                                        <img src="{{ $row['file_path'] }}" alt="" class="problem-builder-thumb rounded border bg-white">
                                    @else
                                        <span class="text-xs text-gray-500">{{ $row['mime_type'] }}</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <select wire:model.defer="mediaRows.{{ $mediaId }}.role" class="fi-input rounded-md border-gray-300">
                                        @foreach($roleLabels as $role => $label)
                                            <option value="{{ $role }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input wire:model.defer="mediaRows.{{ $mediaId }}.sort_order" type="number" class="fi-input w-20 rounded-md border-gray-300">
                                </td>
                                <td class="p-2">
                                    <input wire:model.defer="mediaRows.{{ $mediaId }}.is_published" type="checkbox" class="rounded border-gray-300">
                                </td>
                                <td class="p-2 min-w-64">
                                    <input wire:model.defer="mediaRows.{{ $mediaId }}.alt_ru" type="text" class="fi-input mb-2 block w-full rounded-md border-gray-300" placeholder="RU alt text">
                                    <textarea wire:model.defer="mediaRows.{{ $mediaId }}.caption_ru" rows="2" class="fi-input block w-full rounded-md border-gray-300" placeholder="RU caption_html"></textarea>
                                </td>
                                <td class="p-2 min-w-64">
                                    <input wire:model.defer="mediaRows.{{ $mediaId }}.alt_en" type="text" class="fi-input mb-2 block w-full rounded-md border-gray-300" placeholder="EN alt text">
                                    <textarea wire:model.defer="mediaRows.{{ $mediaId }}.caption_en" rows="2" class="fi-input block w-full rounded-md border-gray-300" placeholder="EN caption_html"></textarea>
                                </td>
                                <td class="p-2 font-mono text-xs">
                                    <a href="{{ $row['file_path'] }}" target="_blank">{{ $row['file_path'] }}</a>
                                    <div class="text-gray-500">{{ $row['original_name'] }}</div>
                                </td>
                                <td class="p-2">
                                    <button
                                        type="button"
                                        onclick="if (confirm('Delete this media row and file?')) { @this.deleteMedia({{ $mediaId }}) }"
                                        class="fi-btn fi-btn-size-xs fi-btn-color-danger"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-3 text-gray-500">No media uploaded yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">Validation warnings</h3>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @forelse($warnings as $warning)
                        <li class="text-warning-700">{{ $warning }}</li>
                    @empty
                        <li class="list-none text-success-700">No warnings detected.</li>
                    @endforelse
                </ul>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">Preview</h3>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-lg border p-4 problem-builder-preview">
                        <h4 class="font-medium mb-2">RU</h4>
                        <div class="prose max-w-none math-content">
                            <h5>{{ $ruTitle }}</h5>
                            {!! $ruStatement !!}
                            @if(filled($ruHint))
                                <hr><strong>Подсказка</strong>
                                {!! $ruHint !!}
                            @endif
                            @if(filled($ruSolution))
                                <hr><strong>Решение</strong>
                                {!! $ruSolution !!}
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg border p-4 problem-builder-preview">
                        <h4 class="font-medium mb-2">EN</h4>
                        <div class="prose max-w-none math-content">
                            <h5>{{ $enTitle }}</h5>
                            {!! $enStatement !!}
                            @if(filled($enHint))
                                <hr><strong>Hint</strong>
                                {!! $enHint !!}
                            @endif
                            @if(filled($enSolution))
                                <hr><strong>Solution</strong>
                                {!! $enSolution !!}
                            @endif
                        </div>
                    </div>
                </div>
            </x-filament::section>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary">Save problem</button>
                @if($problemId)
                    <span class="self-center text-sm text-gray-600">Problem ID: {{ $problemId }}</span>
                @endif
            </div>
        </form>
    </div>

    @include('filament.partials.admin-mathjax')
</x-filament-panels::page>
