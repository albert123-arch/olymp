@extends('layouts.public')

@php
    $cleanReaderTitle = static function (?string $title): string {
        $title = trim((string) $title);
        $pattern = '/^\s*(?:#|\x{2116})?\s*\d+(?:\.\d+)+(?:[.)\]:-])?\s*/u';
        $cleaned = $title;

        for ($i = 0; $i < 2; $i++) {
            $next = trim((string) preg_replace($pattern, '', $cleaned));
            if ($next === $cleaned || $next === '') {
                break;
            }
            $cleaned = $next;
        }

        return $cleaned !== '' ? $cleaned : $title;
    };
@endphp

@section('content')
    <div class="reader-shell">
        @if(count($ladder['steps']) > 0)
            <aside class="reader-sidebar" aria-label="{{ __('public.ladders') }}">
                <div class="reader-sidebar-card">
                    <div class="reader-sidebar-title">{{ __('public.steps_count', ['count' => $ladder['steps_count']]) }}</div>
                    <nav class="reader-nav-list">
                        @foreach($ladder['steps'] as $step)
                            <a class="reader-nav-link reader-step-{{ $step['step_type'] }} {{ $step['step_type'] === 'target' ? 'reader-step-target' : '' }}"
                               href="#step-{{ $step['step_number'] }}"
                               data-reader-nav-link>
                                <span class="reader-nav-number">{{ str_pad((string) $step['step_number'], 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="min-w-0">
                                    <span class="reader-nav-title">{{ $cleanReaderTitle($step['step_title'] ?? '') }}</span>
                                    <span class="reader-nav-meta">{{ $step['step_type_label'] }} &middot; {{ $step['difficulty_stars'] }}</span>
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
                        <p class="muted-label mb-2 text-secondary">{{ __('public.ladders') }}</p>
                        <h1 class="h2 mb-2">{{ $ladder['title'] }}</h1>
                        @if($ladder['description'])
                            <div class="content-html math-content mb-3">{!! $ladder['description'] !!}</div>
                        @endif
                        <div class="d-flex flex-wrap gap-2 small text-secondary">
                            @if($ladder['course'])
                                <span>{{ __('public.course') }}: {{ $ladder['course']['title'] }}</span>
                            @endif
                            @if($ladder['chapter'])
                                <span>{{ __('public.chapter') }}: {{ $ladder['chapter']['title'] }}</span>
                            @endif
                            @if($ladder['main_method'])
                                <span>{{ __('public.main_method') }}: {{ $ladder['main_method'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-lg-end gap-2">
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $ladder['difficulty_stars'] }}</span>
                        @foreach($ladder['grade_badges'] ?? [] as $gradeBadge)
                            <span class="badge text-bg-light border">{{ $gradeBadge }}</span>
                        @endforeach
                        <span class="badge text-bg-light border">{{ __('public.steps_count', ['count' => $ladder['steps_count']]) }}</span>
                        <a class="btn btn-primary btn-sm" href="{{ $ladder['practice_url'] }}">{{ __('public.start_practice') }}</a>
                    </div>
                </div>
            </section>

            @if(count($ladder['steps']) > 0)
                <nav class="reader-chip-nav" aria-label="{{ __('public.ladders') }}">
                    @foreach($ladder['steps'] as $step)
                        <a class="reader-chip" href="#step-{{ $step['step_number'] }}" data-reader-nav-link>{{ str_pad((string) $step['step_number'], 2, '0', STR_PAD_LEFT) }}</a>
                    @endforeach
                </nav>
            @endif

            <section class="row g-3">
                @forelse($ladder['steps'] as $step)
                    <div class="col-12" id="step-{{ $step['step_number'] }}" data-reader-target>
                        <article class="surface-card reader-card p-3 p-lg-4 {{ $step['step_type'] === 'target' ? 'reader-step-target' : '' }}">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="min-w-0">
                                    <div class="small text-secondary mb-1">{{ __('public.step') }} {{ $step['step_number'] }} / {{ count($ladder['steps']) }}</div>
                                    <h2 class="h6 mb-1">
                                        @if($step['step_title'])
                                            {{ $step['step_title'] }}
                                        @else
                                            {{ $step['problem']['title'] }}
                                        @endif
                                    </h2>
                                    @if($step['step_label'])
                                        <div class="small text-secondary mb-1">{{ $step['step_label'] }}</div>
                                    @endif
                                    <a class="text-decoration-none" href="{{ $step['problem']['url'] }}">
                                        {{ $step['problem']['code'] }} &middot; {{ $step['problem']['title'] }}
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap gap-1 justify-content-end reader-meta-desktop">
                                    <span class="badge reader-step-badge reader-step-{{ $step['step_type'] }}">{{ $step['step_type_label'] }}</span>
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $step['difficulty_stars'] }}</span>
                                    @auth
                                        <span class="badge {{ $step['problem']['is_solved'] ? 'text-bg-success' : 'text-bg-light border' }}">{{ $step['problem']['is_solved'] ? __('public.solved') : __('public.unsolved') }}</span>
                                        <span class="badge {{ $step['problem']['is_bookmarked'] ? 'text-bg-warning' : 'text-bg-light border' }}">{{ $step['problem']['is_bookmarked'] ? __('public.bookmarked') : __('public.not_bookmarked') }}</span>
                                    @endauth
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="content-panel p-4">{{ __('public.no_ladder_steps') }}</div>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
