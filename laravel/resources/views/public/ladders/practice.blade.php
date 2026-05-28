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
            <aside class="reader-sidebar" aria-label="{{ __('public.ladder_practice') }}">
                <div class="reader-sidebar-card">
                    <div class="reader-sidebar-title">{{ __('public.ladder_practice') }}</div>
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
                        <p class="muted-label mb-2 text-secondary">{{ __('public.ladder_practice') }}</p>
                        <h1 class="h2 mb-2">{{ $ladder['title'] }}</h1>
                        @if($ladder['description'])
                            <div class="content-html math-content mb-2">{!! $ladder['description'] !!}</div>
                        @endif
                        <div class="small text-secondary">
                            @if($ladder['main_method'])
                                {{ __('public.main_method') }}: {{ $ladder['main_method'] }}
                            @endif
                        </div>
                        @if(!empty($ladder['grade_badges']))
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($ladder['grade_badges'] as $gradeBadge)
                                    <span class="badge text-bg-light border">{{ $gradeBadge }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-self-lg-start">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('ladders.index', ['lang' => $currentLang]) }}">{{ __('public.ladders') }}</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ $ladder['show_url'] }}">{{ __('public.open') }}</a>
                    </div>
                </div>
            </section>

            @if(count($ladder['steps']) > 0)
                <nav class="reader-chip-nav" aria-label="{{ __('public.ladder_practice') }}">
                    @foreach($ladder['steps'] as $step)
                        <a class="reader-chip" href="#step-{{ $step['step_number'] }}" data-reader-nav-link>{{ str_pad((string) $step['step_number'], 2, '0', STR_PAD_LEFT) }}</a>
                    @endforeach
                </nav>
            @endif

            <section class="row g-3">
                @forelse($ladder['steps'] as $step)
                    <div class="col-12 {{ $step['step_type'] === 'target' ? 'reader-step-target-scope' : '' }}" id="step-{{ $step['step_number'] }}" data-reader-target>
                        <div class="reader-step-header">
                            <span class="badge text-bg-light border">{{ __('public.step') }} {{ $step['step_number'] }} / {{ count($ladder['steps']) }}</span>
                            <span class="badge reader-step-badge reader-step-{{ $step['step_type'] }}">{{ $step['step_type_label'] }}</span>
                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $step['difficulty_stars'] }}</span>
                            @if($step['step_title'])
                                <span class="small fw-semibold">{{ $step['step_title'] }}</span>
                            @endif
                        </div>
                        @include('public.partials.problem-card', ['problem' => $step['problem'], 'currentLang' => $currentLang])

                        <div class="reader-bottom-nav mt-2">
                            <div>
                                @if($step['step_number'] > 1)
                                    <a class="small text-decoration-none" href="#step-{{ $step['step_number'] - 1 }}">{{ __('public.previous_step') }}</a>
                                @endif
                            </div>
                            <div class="small text-secondary">{{ __('public.step') }} {{ $step['step_number'] }} / {{ count($ladder['steps']) }}</div>
                            <div>
                                @if($step['step_number'] < count($ladder['steps']))
                                    <a class="small text-decoration-none" href="#step-{{ $step['step_number'] + 1 }}">{{ __('public.next_step') }}</a>
                                @endif
                            </div>
                        </div>
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
