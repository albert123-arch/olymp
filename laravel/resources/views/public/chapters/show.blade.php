@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <p class="muted-label mb-2 text-secondary">{{ __('public.chapter') }}</p>
        <h1 class="h2 mb-3">{{ $chapter['title'] }}</h1>
        @if($chapter['description_html'])
            <div class="content-html math-content mb-4">
                {!! $chapter['description_html'] !!}
            </div>
        @endif
        <div class="small text-secondary mb-3">
            @if(($chapter['progress']['total'] ?? 0) > 0 && auth()->check())
                {{ __('public.progress') }}: {{ $chapter['progress']['solved'] }}/{{ $chapter['progress']['total'] }} ({{ $chapter['progress']['percent'] }}%)
            @elseif(($chapter['progress']['total'] ?? 0) > 0)
                {{ __('public.progress_login_required') }}
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3 chapter-tabs">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('course.show', ['course' => $chapter['course']['slug'], 'lang' => $currentLang]) }}">{{ __('public.back_to_course') }}</a>
            <a class="btn btn-sm btn-outline-primary" href="{{ $chapter['practice_url'] }}">{{ __('public.practice') }}</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ $chapter['ladders_url'] }}">{{ __('public.ladders') }}</a>
        </div>

        <div class="d-flex flex-wrap gap-2 sticky-top pt-2 pb-1 bg-white bg-opacity-75 chapter-tabs">
            <a class="btn btn-sm btn-outline-primary" href="#theory">{{ __('public.theory') }}</a>
            <a class="btn btn-sm btn-outline-primary" href="#examples">{{ __('public.examples') }}</a>
            <a class="btn btn-sm btn-primary" href="{{ $chapter['practice_url'] }}">{{ __('public.practice') }}</a>
            <a class="btn btn-sm btn-outline-secondary" href="#ladders">{{ __('public.ladders') }}</a>
        </div>
    </section>

    @if($chapter['theory_html'])
        <section id="theory" class="content-panel p-4 p-lg-5 mb-4">
            <p class="muted-label mb-2 text-secondary">{{ __('public.theory') }}</p>
            <div class="content-html math-content math-block mb-4">
                {!! $chapter['theory_html'] !!}
            </div>
        </section>
    @endif

    @if($chapter['examples_html'])
        <section id="examples" class="content-panel p-4 p-lg-5 mb-4">
            <p class="muted-label mb-2 text-secondary">{{ __('public.examples') }}</p>
            <div class="content-html math-content math-block">
                {!! $chapter['examples_html'] !!}
            </div>
        </section>
    @endif

    <section id="problems">
        <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
            <div>
                <p class="muted-label mb-1 text-secondary">{{ __('public.problems') }}</p>
                <h2 class="h3 mb-0">{{ __('public.problems') }}</h2>
            </div>
        </div>

        <div class="row g-3">
            @forelse($chapter['problems'] as $problem)
                <div class="col-12">
                    @include('public.partials.problem-card', ['problem' => $problem, 'currentLang' => $currentLang])
                </div>
            @empty
                <div class="col-12">
                    <div class="content-panel p-4">{{ __('public.no_problems') }}</div>
                </div>
            @endforelse
        </div>
    </section>

    <section id="ladders" class="content-panel p-4 p-lg-5 mt-4">
        <p class="muted-label mb-2 text-secondary">{{ __('public.ladders') }}</p>
        @if(!empty($chapter['ladders']))
            <div class="row g-3">
                @foreach($chapter['ladders'] as $ladder)
                    <div class="col-12">
                        <article class="surface-card p-3">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
                                <div>
                                    <h3 class="h6 mb-1">{{ $ladder['title'] }}</h3>
                                    @if($ladder['main_method'])
                                        <div class="small text-secondary">{{ __('public.main_method') }}: {{ $ladder['main_method'] }}</div>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-start">
                                    <span class="badge text-bg-light border">{{ __('public.steps_count', ['count' => $ladder['steps_count']]) }}</span>
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $ladder['difficulty_stars'] }}</span>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ $ladder['show_url'] }}">{{ __('public.open') }}</a>
                                    <a class="btn btn-sm btn-primary" href="{{ $ladder['practice_url'] }}">{{ __('public.start_practice') }}</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-secondary">{{ __('public.no_ladders') }}</div>
        @endif
    </section>

    @if($chapter['previous_chapter'] || $chapter['next_chapter'])
        <section class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
            <div>
                @if($chapter['previous_chapter'])
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('chapter.show', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['previous_chapter']['slug'], 'lang' => $currentLang]) }}">
                        {{ __('public.previous_chapter') }}
                    </a>
                @endif
            </div>
            <div>
                @if($chapter['next_chapter'])
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('chapter.show', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['next_chapter']['slug'], 'lang' => $currentLang]) }}">
                        {{ __('public.next_chapter') }}
                    </a>
                @endif
            </div>
        </section>
    @endif
@endsection
