<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold">Quick Problem Draft</h2>
                    <p class="text-sm text-gray-600">Create one problem with RU + EN texts under a single <code>problem_id</code>.</p>
                </div>
                <a href="{{ $contentStudioUrl }}" class="fi-btn fi-btn-size-sm fi-btn-color-gray">
                    ← Back to Content Studio
                </a>
            </div>
        </x-filament::section>

        <form wire:submit.prevent="saveProblem" class="space-y-6">
            <x-filament::section>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Course</label>
                        <select wire:model.live="selectedCourseId" class="fi-input block w-full rounded-lg border-gray-300">
                            @foreach ($courses as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('selectedCourseId') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Chapter</label>
                        <select wire:model.live="selectedChapterId" class="fi-input block w-full rounded-lg border-gray-300">
                            @foreach ($chapters as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('selectedChapterId') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Code</label>
                        <div class="flex gap-2">
                            <input wire:model.defer="problemCode" type="text" class="fi-input block w-full rounded-lg border-gray-300" placeholder="COM-M02-P008">
                            <button type="button" wire:click="suggestCode" class="fi-btn fi-btn-size-sm fi-btn-color-gray whitespace-nowrap">Suggest</button>
                        </div>
                        @error('problemCode') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Sort order</label>
                        <input wire:model.defer="sortOrder" type="number" min="0" class="fi-input block w-full rounded-lg border-gray-300">
                        @error('sortOrder') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Difficulty level</label>
                        <select wire:model.defer="difficulty" class="fi-input block w-full rounded-lg border-gray-300">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                        @error('difficulty') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-6">
                        <input wire:model.defer="isPublished" id="is_published" type="checkbox" class="rounded border-gray-300">
                        <label for="is_published" class="text-sm font-medium">Published</label>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 mt-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Existing tags (multi-select)</label>
                        <select wire:model.defer="selectedTagIds" multiple class="fi-input block w-full rounded-lg border-gray-300 min-h-36">
                            @foreach ($tags as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">New tags (comma separated)</label>
                        <input wire:model.defer="newTags" type="text" class="fi-input block w-full rounded-lg border-gray-300" placeholder="parity, counting, induction">
                        <p class="text-xs text-gray-500 mt-1">New tags are created safely and attached to this problem.</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">Russian text (RU)</h3>
                <div class="grid gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">RU title</label>
                        <input wire:model.defer="ruTitle" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                        @error('ruTitle') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">RU body_html</label>
                        <textarea wire:model.defer="ruBody" rows="7" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                        @error('ruBody') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">RU hint_html</label>
                        <textarea wire:model.defer="ruHint" rows="4" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">RU solution_html</label>
                        <textarea wire:model.defer="ruSolution" rows="7" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">English text (EN)</h3>
                <div class="grid gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">EN title</label>
                        <input wire:model.defer="enTitle" type="text" class="fi-input block w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">EN body_html</label>
                        <textarea wire:model.defer="enBody" rows="7" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">EN hint_html</label>
                        <textarea wire:model.defer="enHint" rows="4" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">EN solution_html</label>
                        <textarea wire:model.defer="enSolution" rows="7" class="fi-input block w-full rounded-lg border-gray-300 font-mono"></textarea>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">Pre-save warnings</h3>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @forelse ($warnings as $warning)
                        <li class="text-warning-700">{{ $warning }}</li>
                    @empty
                        <li class="list-none text-success-700">No warnings detected.</li>
                    @endforelse
                </ul>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-medium mb-3">Preview (trusted HTML)</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border p-4">
                        <h4 class="font-medium mb-2">RU preview</h4>
                        <div class="prose max-w-none math-content">
                            <h5>{{ $ruTitle }}</h5>
                            {!! $ruBody !!}
                            @if (filled($ruHint))
                                <hr>
                                <strong>Подсказка</strong>
                                {!! $ruHint !!}
                            @endif
                            @if (filled($ruSolution))
                                <hr>
                                <strong>Решение</strong>
                                {!! $ruSolution !!}
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg border p-4">
                        <h4 class="font-medium mb-2">EN preview</h4>
                        <div class="prose max-w-none math-content">
                            <h5>{{ $enTitle }}</h5>
                            {!! $enBody !!}
                            @if (filled($enHint))
                                <hr>
                                <strong>Hint</strong>
                                {!! $enHint !!}
                            @endif
                            @if (filled($enSolution))
                                <hr>
                                <strong>Solution</strong>
                                {!! $enSolution !!}
                            @endif
                        </div>
                    </div>
                </div>
            </x-filament::section>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary">Save problem</button>
                @if($createdProblemId)
                    <span class="text-sm text-success-700 self-center">Created problem ID: {{ $createdProblemId }}</span>
                @endif
            </div>
        </form>
    </div>

    <script>
        function typesetQuickProblemMath() {
            if (window.MathJax && window.MathJax.typesetPromise) {
                MathJax.typesetPromise(document.querySelectorAll('.math-content'));
            }
        }
        document.addEventListener('DOMContentLoaded', () => setTimeout(typesetQuickProblemMath, 100));
        document.addEventListener('livewire:navigated', () => setTimeout(typesetQuickProblemMath, 100));
    </script>
</x-filament-panels::page>

