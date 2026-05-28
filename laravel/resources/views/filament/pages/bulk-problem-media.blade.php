<x-filament-panels::page>
    <style>
        .bulk-media-thumb {
            max-width: 84px;
            max-height: 60px;
            object-fit: contain;
        }
    </style>

    <div class="space-y-6">
        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold">Bulk Diagram Manager</h2>
                    <p class="text-sm text-gray-600">Upload statement, hint, solution, or extra media for many problems in one chapter.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $moduleWorkspaceUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">Module Workspace</a>
                    <button type="button" wire:click="saveAll" class="fi-btn fi-btn-size-sm fi-btn-color-primary">Save all uploaded</button>
                </div>
            </div>
        </x-filament::section>

        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="block text-sm font-medium mb-1">Course</label>
                <select wire:model.live="selectedCourseId" class="fi-input block w-full rounded-lg border-gray-300">
                    @foreach($courses as $course)
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
                    @foreach($chapters as $chapter)
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
            <label class="flex items-center gap-2 pt-7 text-sm font-medium">
                <input wire:model.live="onlyMissingStatement" type="checkbox" class="rounded border-gray-300">
                Only missing statement media
            </label>
        </div>

        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="p-2 text-left">Problem</th>
                        <th class="p-2 text-left">Counts</th>
                        <th class="p-2 text-left">Existing media</th>
                        <th class="p-2 text-left">Upload</th>
                        <th class="p-2 text-left">Warnings</th>
                        <th class="p-2 text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr class="border-b align-top">
                            <td class="p-2 min-w-64">
                                <div class="font-mono text-xs text-gray-500">#{{ $row['sort_order'] }} · {{ $row['code'] }}</div>
                                <div class="font-medium">{{ $row['title'] }}</div>
                                <div class="text-xs text-gray-500">Level {{ $row['difficulty'] }}{{ $row['grades'] ? ' · '.$row['grades'] : '' }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div>S: {{ $row['media_counts']['statement'] ?? 0 }}</div>
                                <div>H: {{ $row['media_counts']['hint'] ?? 0 }}</div>
                                <div>Sol: {{ $row['media_counts']['solution'] ?? 0 }}</div>
                                <div>Extra: {{ $row['media_counts']['extra'] ?? 0 }}</div>
                            </td>
                            <td class="p-2 min-w-72">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($row['media'] as $media)
                                        <div class="rounded border p-2">
                                            @if(str_starts_with((string) $media['mime_type'], 'image/'))
                                                <img src="{{ $media['file_path'] }}" alt="" class="bulk-media-thumb rounded bg-white">
                                            @else
                                                <div class="text-xs">{{ $media['mime_type'] }}</div>
                                            @endif
                                            <div class="text-xs text-gray-600 mt-1">{{ $media['role'] }}</div>
                                            <button
                                                type="button"
                                                onclick="if (confirm('Delete this media row and file?')) { @this.deleteMedia({{ $media['id'] }}) }"
                                                class="fi-btn fi-btn-size-xs fi-btn-color-danger mt-1"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">No media</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-2 min-w-80">
                                <div class="grid gap-2">
                                    <label class="text-xs font-medium">
                                        Statement
                                        <input type="file" multiple wire:model="statementUploads.{{ $row['id'] }}" accept=".svg,.png,.jpg,.jpeg,.webp" class="block w-full">
                                    </label>
                                    <label class="text-xs font-medium">
                                        Hint
                                        <input type="file" multiple wire:model="hintUploads.{{ $row['id'] }}" accept=".svg,.png,.jpg,.jpeg,.webp" class="block w-full">
                                    </label>
                                    <label class="text-xs font-medium">
                                        Solution
                                        <input type="file" multiple wire:model="solutionUploads.{{ $row['id'] }}" accept=".svg,.png,.jpg,.jpeg,.webp" class="block w-full">
                                    </label>
                                    <label class="text-xs font-medium">
                                        Extra
                                        <input type="file" multiple wire:model="extraUploads.{{ $row['id'] }}" accept=".svg,.png,.jpg,.jpeg,.webp,.pdf" class="block w-full">
                                    </label>
                                </div>
                            </td>
                            <td class="p-2">
                                @if($row['missing_media_text'])
                                    <span class="text-warning-700">Missing caption/alt</span>
                                @else
                                    <span class="text-success-700">OK</span>
                                @endif
                            </td>
                            <td class="p-2">
                                <div class="flex flex-wrap gap-1">
                                    <button type="button" wire:click="saveRow({{ $row['id'] }})" class="fi-btn fi-btn-size-xs fi-btn-color-primary">Save row</button>
                                    <a href="{{ $row['builder_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Problem Builder</a>
                                    <a href="{{ $row['edit_url'] }}" class="fi-btn fi-btn-size-xs fi-btn-color-gray">Problem edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-gray-500">No problems match these filters.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
