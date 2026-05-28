@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <p class="muted-label mb-2 text-secondary">{{ __('public.course') }}</p>
                <h1 class="h2 mb-3">{{ $course['title'] }}</h1>
                @if($course['description_html'])
                    <div class="content-html math-content reader-module-list">
                        {!! $course['description_html'] !!}
                    </div>
                @endif
                <div class="small text-secondary mt-2">
                    @if(($course['progress']['total'] ?? 0) > 0 && auth()->check())
                        {{ __('public.progress') }}: {{ $course['progress']['solved'] }}/{{ $course['progress']['total'] }} ({{ $course['progress']['percent'] }}%)
                    @elseif(($course['progress']['total'] ?? 0) > 0)
                        {{ __('public.progress_login_required') }}
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-self-lg-start course-tabs">
                <a class="btn btn-primary" href="{{ route('course.theory', ['course' => $course['slug'], 'lang' => $currentLang]) }}">{{ __('public.theory') }}</a>
                <a class="btn btn-outline-primary" href="#chapters">{{ __('public.examples') }}</a>
                <a class="btn btn-outline-secondary" href="{{ route('course.practice', ['course' => $course['slug'], 'lang' => $currentLang]) }}">{{ __('public.practice') }}</a>
                <a class="btn btn-outline-secondary" href="{{ route('ladders.index', ['course' => $course['slug'], 'lang' => $currentLang]) }}">{{ __('public.ladders') }}</a>
            </div>
        </div>
    </section>

    <section id="chapters">
        <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
            <div>
                <p class="muted-label mb-1 text-secondary">{{ __('public.chapters') }}</p>
                <h2 class="h3 mb-0">{{ __('public.chapters') }}</h2>
            </div>
        </div>

        <div class="row g-3">
            @forelse($course['chapters'] as $chapter)
                <div class="col-12">
                    @include('public.partials.chapter-card', ['chapter' => $chapter, 'courseSlug' => $course['slug'], 'currentLang' => $currentLang])
                </div>
            @empty
                <div class="col-12">
                    <div class="content-panel p-4">{{ __('public.coming_soon') }}</div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
