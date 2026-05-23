@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <p class="muted-label mb-2 text-secondary">{{ __('public.course_theory') }}</p>
        <h1 class="h2 mb-3">{{ $course['title'] }}</h1>
        @if($course['description_html'])
            <div class="content-html math-content mb-4">
                {!! $course['description_html'] !!}
            </div>
        @endif
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="{{ route('course.show', ['course' => $course['slug'], 'lang' => $currentLang]) }}">{{ __('public.back_to_course') }}</a>
        </div>
    </section>

    @foreach($course['chapters'] as $chapter)
        <section class="content-panel p-4 p-lg-5 mb-4">
            <p class="muted-label mb-2 text-secondary">{{ __('public.chapter') }}</p>
            <h2 class="h3 mb-3">{{ $chapter['title'] }}</h2>
            @if($chapter['description_html'])
                <div class="content-html math-content mb-4">
                    {!! $chapter['description_html'] !!}
                </div>
            @endif
            @if($chapter['theory_html'])
                <div class="content-html math-content math-block mb-4">
                    {!! $chapter['theory_html'] !!}
                </div>
            @endif
            @if($chapter['examples_html'])
                <div class="content-html math-content math-block">
                    {!! $chapter['examples_html'] !!}
                </div>
            @endif
        </section>
    @endforeach
@endsection
