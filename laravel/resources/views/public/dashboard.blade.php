@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <p class="muted-label mb-2 text-secondary">{{ __('public.dashboard') }}</p>
        <h1 class="h3 mb-2">{{ __('public.hello_user', ['name' => auth()->user()->name ?: auth()->user()->email]) }}</h1>
        <p class="text-secondary mb-0">{{ __('public.dashboard_note') }}</p>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <article class="surface-card p-3 p-lg-4 h-100">
                <h2 class="h6 mb-3">{{ __('public.progress') }}</h2>
                <div class="d-flex flex-wrap gap-3">
                    <span class="badge text-bg-light border">{{ __('public.solved') }}: {{ $dashboard['solved_count'] }}</span>
                    <span class="badge text-bg-light border">{{ __('public.bookmarked') }}: {{ $dashboard['bookmarks_count'] }}</span>
                </div>
            </article>
        </div>
        <div class="col-12 col-lg-6">
            <article class="surface-card p-3 p-lg-4 h-100">
                <h2 class="h6 mb-3">{{ __('public.continue_learning') }}</h2>
                <div class="d-flex flex-column gap-2">
                    @if($dashboard['course'])
                        <a class="btn btn-sm btn-outline-primary text-start" href="{{ $dashboard['course']['url'] }}">{{ $dashboard['course']['title'] }}</a>
                        <a class="btn btn-sm btn-primary text-start" href="{{ $dashboard['course']['practice_url'] }}">{{ __('public.start_practice') }}</a>
                        <a class="btn btn-sm btn-outline-secondary text-start" href="{{ $dashboard['course']['practice_url'] . '&status=bookmarked' }}">{{ __('public.filter_bookmarked') }}</a>
                    @else
                        <span class="text-secondary small">{{ __('public.no_courses') }}</span>
                    @endif
                </div>
            </article>
        </div>
    </section>

    @if($dashboard['is_admin'])
        <section class="content-panel p-3 p-lg-4">
            <h2 class="h6 mb-3">{{ __('public.add_material') }}</h2>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/courses/create') }}">{{ __('public.add_course') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/chapters/create') }}">{{ __('public.add_chapter') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/problems/create') }}">{{ __('public.add_problem') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/problem-texts/create') }}">{{ __('public.add_problem_text') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/problem-ladders/create') }}">{{ __('public.add_ladder') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/problem-ladder-steps/create') }}">{{ __('public.add_ladder_step') }}</a>
            </div>
            <div class="mt-3">
                <a class="btn btn-sm btn-primary" href="{{ url('/admin') }}">{{ __('public.go_to_admin') }}</a>
            </div>
        </section>
    @endif
@endsection
