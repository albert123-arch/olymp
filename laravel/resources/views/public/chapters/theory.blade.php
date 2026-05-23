@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4">
        <p class="muted-label mb-2 text-secondary">{{ __('public.chapter_theory') }}</p>
        <h1 class="h2 mb-3">{{ $chapter['title'] }}</h1>
        @if($chapter['description_html'])
            <div class="content-html math-content mb-4">
                {!! $chapter['description_html'] !!}
            </div>
        @endif
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="{{ route('chapter.show', ['course' => $chapter['course']['slug'], 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}">{{ __('public.back_to_chapter') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('course.show', ['course' => $chapter['course']['slug'], 'lang' => $currentLang]) }}">{{ __('public.back_to_course') }}</a>
        </div>
    </section>

    @if($chapter['theory_html'])
        <section class="content-panel p-4 p-lg-5 mb-4">
            <div class="content-html math-content math-block">
                {!! $chapter['theory_html'] !!}
            </div>
        </section>
    @endif

    @if($chapter['examples_html'])
        <section class="content-panel p-4 p-lg-5 mb-4">
            <div class="content-html math-content math-block">
                {!! $chapter['examples_html'] !!}
            </div>
        </section>
    @endif
@endsection
