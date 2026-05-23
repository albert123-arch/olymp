@extends('layouts.public')

@section('content')
    <section class="hero-panel p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-8">
                <div class="h-100 d-flex flex-column justify-content-center">
                    <p class="muted-label mb-2 text-uppercase text-secondary">{{ __('public.home') }}</p>
                    <h1 class="display-5 fw-semibold mb-3">{{ __('public.platform_title') }}</h1>
                    <p class="lead mb-4">{{ __('public.platform_intro') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary px-4" href="{{ route('courses.index', ['lang' => $currentLang]) }}">{{ __('public.courses') }}</a>
                        <a class="btn btn-outline-primary px-4" href="{{ route('courses.index', ['lang' => $currentLang]) }}#active-courses">{{ __('public.start_learning') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-figure rounded-4 p-4 h-100">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div>
                            <p class="text-uppercase text-secondary small mb-2">{{ __('public.active_courses') }}</p>
                            <div class="display-6 fw-semibold text-primary">{{ count($courses) }}</div>
                        </div>
                        <p class="mb-0 text-secondary">{{ __('public.home_hero_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="active-courses">
        <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
            <div>
                <p class="muted-label mb-1 text-secondary">{{ __('public.active_courses') }}</p>
                <h2 class="h3 mb-0">{{ __('public.start_learning') }}</h2>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('courses.index', ['lang' => $currentLang]) }}">{{ __('public.view_all_courses') }}</a>
        </div>

        <div class="row g-3">
            @forelse($courses as $course)
                <div class="col-md-6 col-xl-4">
                    @include('public.partials.course-card', ['course' => $course, 'currentLang' => $currentLang, 'fullWidth' => false])
                </div>
            @empty
                <div class="col-12">
                    <div class="content-panel p-4">{{ __('public.no_courses') }}</div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
