@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <p class="muted-label mb-2 text-secondary">{{ __('public.ladders') }}</p>
                <h1 class="h2 mb-0">{{ __('public.ladders') }}</h1>
            </div>
            <a class="btn btn-outline-secondary btn-sm align-self-lg-start" href="{{ route('courses.index', ['lang' => $currentLang]) }}">{{ __('public.courses') }}</a>
        </div>
    </section>

    <section class="content-panel p-3 p-lg-4 mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="lang" value="{{ $currentLang }}">
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary mb-1">{{ __('public.course') }}</label>
                <select name="course" class="form-select form-select-sm">
                    <option value="">{{ __('public.filter_all') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course['slug'] }}" @selected($filters['course'] === $course['slug'])>{{ $course['title'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary mb-1">{{ __('public.chapter') }}</label>
                <select name="chapter" class="form-select form-select-sm">
                    <option value="">{{ __('public.filter_all') }}</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter['slug'] }}" @selected($filters['chapter'] === $chapter['slug'])>{{ $chapter['title'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">{{ __('public.filter') }}</button>
                <a class="btn btn-sm btn-light border" href="{{ route('ladders.index', ['lang' => $currentLang]) }}">{{ __('public.reset_filters') }}</a>
            </div>
        </form>
    </section>

    <section class="row g-3">
        @forelse($ladders as $ladder)
            <div class="col-12">
                <article class="surface-card p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div class="flex-grow-1">
                            <h2 class="h5 mb-2">{{ $ladder['title'] }}</h2>
                            @if($ladder['description'])
                                <div class="content-html math-content mb-2">{!! $ladder['description'] !!}</div>
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
                            <span class="badge text-bg-light border">{{ __('public.steps_count', ['count' => $ladder['steps_count']]) }}</span>
                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $ladder['difficulty_stars'] }}</span>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ $ladder['show_url'] }}">{{ __('public.open') }}</a>
                                <a class="btn btn-sm btn-primary" href="{{ $ladder['practice_url'] }}">{{ __('public.start_practice') }}</a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="content-panel p-4">{{ __('public.no_ladders') }}</div>
            </div>
        @endforelse
    </section>
@endsection
