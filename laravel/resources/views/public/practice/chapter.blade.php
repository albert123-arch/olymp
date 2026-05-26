@extends('layouts.public')

@php
    $readerItems = [];
    foreach ($chapter['problems'] as $problem) {
        $readerItems[] = [
            'id' => 'problem-' . $problem['id'],
            'number' => $problem['display_number'] ?? $problem['code'],
            'title' => $problem['title'],
            'solved' => (bool) ($problem['is_solved'] ?? false),
        ];
    }
@endphp

@section('content')
    <div class="reader-shell">
        @if(count($readerItems) > 0)
            <aside class="reader-sidebar" aria-label="{{ __('public.problems') }}">
                <div class="reader-sidebar-card">
                    <div class="reader-sidebar-title">{{ __('public.practice') }}</div>
                    <nav class="reader-nav-list">
                        @foreach($readerItems as $item)
                            <a class="reader-nav-link {{ $item['solved'] ? 'is-solved' : '' }}"
                               href="#{{ $item['id'] }}"
                               data-reader-nav-link>
                                <span class="reader-nav-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="min-w-0">
                                    <span class="reader-nav-title">{{ $item['number'] }} {{ $item['title'] }}</span>
                                    @if($item['solved'])
                                        <span class="reader-nav-meta">{{ __('public.solved') }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>
        @endif

        <div class="reader-main">
            <section class="content-panel reader-intro p-4 p-lg-5 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <p class="muted-label mb-2 text-secondary">{{ __('public.practice') }}</p>
                        <h1 class="h2 mb-2">#{{ $chapter['display_index'] }} {{ $chapter['title'] }}</h1>
                        <div class="small text-secondary">
                            @if($canTrackProgress)
                                {{ __('public.progress') }}: {{ $progress['solved'] }}/{{ $progress['total'] }} ({{ $progress['percent'] }}%)
                            @else
                                {{ __('public.progress_login_required') }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-self-lg-start">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('course.show', ['course' => $chapter['course']['slug'], 'lang' => $currentLang]) }}">{{ __('public.back_to_course') }}</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('chapter.show', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}#problems">{{ __('public.back_to_chapter') }}</a>
                    </div>
                </div>
            </section>

            @if(count($readerItems) > 0)
                <nav class="reader-chip-nav" aria-label="{{ __('public.problems') }}">
                    @foreach($readerItems as $item)
                        <a class="reader-chip" href="#{{ $item['id'] }}" data-reader-nav-link>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</a>
                    @endforeach
                </nav>
            @endif

            <section class="content-panel reader-filter p-3 p-lg-4 mb-4">
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="hidden" name="lang" value="{{ $currentLang }}">
                    <input type="hidden" name="status" value="{{ $filters['status'] ?? 'all' }}">
                    <input type="hidden" name="level" value="{{ $filters['level'] ?? '' }}">
                    <input type="hidden" name="grade" value="{{ $filters['grade'] ?? '' }}">
                    <span class="small text-secondary me-1">{{ __('public.filter') }}:</span>

                    @foreach(['all' => __('public.filter_all'), 'unsolved' => __('public.filter_unsolved'), 'solved' => __('public.filter_solved'), 'bookmarked' => __('public.filter_bookmarked')] as $statusValue => $statusLabel)
                        @php
                            $isSelected = $filters['status'] === $statusValue;
                            $disabled = !$canTrackProgress && in_array($statusValue, ['solved', 'bookmarked'], true);
                        @endphp
                        <button type="submit"
                                class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }}"
                                name="status"
                                value="{{ $statusValue }}"
                                @disabled($disabled)>
                            {{ $statusLabel }}
                        </button>
                    @endforeach

                    @for($i = 1; $i <= 5; $i++)
                        <button type="submit"
                                class="btn btn-sm {{ (int)($filters['level'] ?? 0) === $i ? 'btn-primary' : 'btn-outline-secondary' }}"
                                name="level"
                                value="{{ $i }}">
                            {{ __('public.level_short', ['level' => $i]) }}
                        </button>
                    @endfor

                    @foreach($gradeOptions ?? [] as $grade)
                        <button type="submit"
                                class="btn btn-sm {{ (int)($filters['grade'] ?? 0) === (int)$grade['number'] ? 'btn-primary' : 'btn-outline-secondary' }}"
                                name="grade"
                                value="{{ $grade['number'] }}">
                            {{ $grade['label'] }}
                        </button>
                    @endforeach

                    <a class="btn btn-sm btn-light border ms-lg-2" href="{{ route('chapter.practice', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}">{{ __('public.reset_filters') }}</a>
                </form>

                @if($filters['requires_auth'])
                    <div class="small text-warning mt-2">{{ __('public.filter_login_required') }}</div>
                @endif
            </section>

            <section class="row g-3">
                @forelse($chapter['problems'] as $problem)
                    <div class="col-12">
                        @include('public.partials.problem-card', ['problem' => $problem, 'currentLang' => $currentLang])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="content-panel p-4">{{ __('public.no_problems_for_filters') }}</div>
                    </div>
                @endforelse
            </section>

            @if($chapter['previous_chapter'] || $chapter['next_chapter'])
                <section class="reader-bottom-nav mt-4">
                    <div>
                        @if($chapter['previous_chapter'])
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('chapter.practice', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['previous_chapter']['slug'], 'lang' => $currentLang]) }}">
                                {{ __('public.previous_chapter') }}
                            </a>
                        @endif
                    </div>
                    <div>
                        @if($chapter['next_chapter'])
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('chapter.practice', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['next_chapter']['slug'], 'lang' => $currentLang]) }}">
                                {{ __('public.next_chapter') }}
                            </a>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
