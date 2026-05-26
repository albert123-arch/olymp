@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
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

    <section class="row g-3">
        @forelse($ladder['steps'] as $step)
            <div class="col-12" id="step-{{ $step['step_number'] }}">
                <div class="mb-2 d-flex flex-wrap align-items-center gap-2">
                    <span class="badge text-bg-light border">{{ __('public.step') }} {{ $step['step_number'] }}</span>
                    <span class="badge text-bg-light border">{{ $step['step_type_label'] }}</span>
                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $step['difficulty_stars'] }}</span>
                    @if($step['step_title'])
                        <span class="small fw-semibold">{{ $step['step_title'] }}</span>
                    @endif
                </div>
                @include('public.partials.problem-card', ['problem' => $step['problem'], 'currentLang' => $currentLang])

                <div class="d-flex justify-content-between mt-2">
                    <div>
                        @if($step['step_number'] > 1)
                            <a class="small text-decoration-none" href="#step-{{ $step['step_number'] - 1 }}">{{ __('public.previous_step') }}</a>
                        @endif
                    </div>
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
@endsection
