@extends('layouts.public')

@php
    $isRu = ($currentLang ?? app()->getLocale()) === 'ru';
    $hideHintLabel = $isRu ? "\u{0421}\u{043A}\u{0440}\u{044B}\u{0442}\u{044C} \u{043F}\u{043E}\u{0434}\u{0441}\u{043A}\u{0430}\u{0437}\u{043A}\u{0443}" : 'Hide hint';
    $hideSolutionLabel = $isRu ? "\u{0421}\u{043A}\u{0440}\u{044B}\u{0442}\u{044C} \u{0440}\u{0435}\u{0448}\u{0435}\u{043D}\u{0438}\u{0435}" : 'Hide solution';
    $hideTeacherNoteLabel = $isRu ? "\u{0421}\u{043A}\u{0440}\u{044B}\u{0442}\u{044C} \u{0437}\u{0430}\u{043C}\u{0435}\u{0442}\u{043A}\u{0443}" : 'Hide teacher note';
@endphp

@section('content')
    <div class="reader-main mx-auto" data-problem-page data-login-url="{{ $problem['login_url'] }}">
        <section id="problem-reader" class="content-panel reader-intro p-4 p-lg-5 mb-4" data-reader-target>
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div class="min-w-0">
                    <p class="muted-label mb-2 text-secondary">{{ __('public.problem') }}</p>
                    <h1 class="h2 mb-2">{{ $problem['code'] }} {{ $problem['title'] }}</h1>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge text-bg-light border">{{ $problem['book_number'] ? '#'.$problem['book_number'] : $problem['code'] }}</span>
                        @foreach($problem['grade_badges'] ?? [] as $gradeBadge)
                            <span class="badge text-bg-light border reader-meta-desktop">{{ $gradeBadge }}</span>
                        @endforeach
                        <span class="badge {{ $problem['difficulty_class'] }}">{{ $problem['difficulty_stars'] }} {{ $problem['difficulty_label'] }}</span>
                        <button type="button"
                                class="btn btn-sm {{ $problem['is_solved'] ? 'btn-success' : 'btn-outline-secondary' }} reader-icon-button"
                                data-action="toggle-solved"
                                data-url="{{ $problem['solved_toggle_url'] }}"
                                data-active-char="&#10003;"
                                data-idle-char="&#9675;"
                                @disabled(!$problem['can_track_progress'])>{!! $problem['is_solved'] ? '&#10003;' : '&#9675;' !!}</button>
                        <button type="button"
                                class="btn btn-sm {{ $problem['is_bookmarked'] ? 'btn-warning' : 'btn-outline-secondary' }} reader-icon-button"
                                data-action="toggle-bookmark"
                                data-url="{{ $problem['bookmark_toggle_url'] }}"
                                data-active-char="&#9733;"
                                data-idle-char="&#9734;"
                                @disabled(!$problem['can_track_progress'])>{!! $problem['is_bookmarked'] ? '&#9733;' : '&#9734;' !!}</button>
                    </div>
                    @if($problem['statement_html'])
                        <div class="reader-statement content-html math-content math-block mb-4">
                            {!! $problem['statement_html'] !!}
                        </div>
                    @endif

                    @foreach($problem['media']['statement'] ?? [] as $media)
                        @include('public.partials.problem-media', ['media' => $media, 'class' => 'problem-media-statement'])
                    @endforeach

                    @if(!empty($problem['source_label']))
                        <div class="small text-secondary mb-3">{{ $problem['source_label'] }}</div>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-2 align-self-lg-start">
                    <a class="btn btn-outline-primary btn-sm" href="{{ $problem['practice_url'] }}">{{ __('public.back_to_chapter') }}</a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ $problem['course_practice_url'] }}">{{ __('public.practice') }}</a>
                </div>
            </div>
        </section>

        @if($problem['hint_html'])
            <section class="content-panel p-4 p-lg-5 mb-4">
                <button class="reader-accordion-toggle w-100 btn btn-light border d-flex align-items-center justify-content-between"
                        type="button"
                        data-reader-toggle="hint"
                        data-reader-target-id="problemHintBox"
                        data-reader-open-label="{{ __('public.hint') }}"
                        data-reader-close-label="{{ $hideHintLabel }}"
                        aria-expanded="false"
                        aria-controls="problemHintBox">
                    <span data-reader-toggle-label>{{ __('public.hint') }}</span>
                    <span data-reader-toggle-icon aria-hidden="true">+</span>
                </button>
                <div id="problemHintBox" class="reader-accordion-content mt-3" hidden>
                    <div class="content-html math-content math-block">
                        {!! $problem['hint_html'] !!}
                    </div>
                    @foreach($problem['media']['hint'] ?? [] as $media)
                        @include('public.partials.problem-media', ['media' => $media, 'class' => 'problem-media-hint'])
                    @endforeach
                </div>
            </section>
        @endif

        @if($problem['solution_html'])
            <section class="content-panel p-4 p-lg-5 mb-4">
                <button class="reader-accordion-toggle w-100 btn btn-light border d-flex align-items-center justify-content-between"
                        type="button"
                        data-reader-toggle="solution"
                        data-reader-target-id="problemSolutionBox"
                        data-reader-open-label="{{ __('public.solution') }}"
                        data-reader-close-label="{{ $hideSolutionLabel }}"
                        aria-expanded="false"
                        aria-controls="problemSolutionBox">
                    <span data-reader-toggle-label>{{ __('public.solution') }}</span>
                    <span data-reader-toggle-icon aria-hidden="true">+</span>
                </button>
                <div id="problemSolutionBox" class="reader-accordion-content mt-3" hidden>
                    <div class="content-html math-content math-block">
                        {!! $problem['solution_html'] !!}
                    </div>
                    @foreach($problem['media']['solution'] ?? [] as $media)
                        @include('public.partials.problem-media', ['media' => $media, 'class' => 'problem-media-solution'])
                    @endforeach
                </div>
            </section>
        @endif

        @if($problem['teacher_note_html'])
            <section class="content-panel p-4 p-lg-5 mb-4">
                <button class="reader-accordion-toggle w-100 btn btn-light border d-flex align-items-center justify-content-between"
                        type="button"
                        data-reader-toggle="teacher-note"
                        data-reader-target-id="problemTeacherNoteBox"
                        data-reader-open-label="{{ __('public.teacher_note') }}"
                        data-reader-close-label="{{ $hideTeacherNoteLabel }}"
                        aria-expanded="false"
                        aria-controls="problemTeacherNoteBox">
                    <span data-reader-toggle-label>{{ __('public.teacher_note') }}</span>
                    <span data-reader-toggle-icon aria-hidden="true">+</span>
                </button>
                <div id="problemTeacherNoteBox" class="reader-accordion-content mt-3" hidden>
                    <div class="content-html math-content math-block">
                        {!! $problem['teacher_note_html'] !!}
                    </div>
                </div>
            </section>
        @endif

        @foreach($problem['media']['extra'] ?? [] as $media)
            @include('public.partials.problem-media', ['media' => $media, 'class' => 'problem-media-extra'])
        @endforeach

        <section class="reader-bottom-nav mt-4">
            <div>
                @if($problem['previous_problem_url'])
                    <a class="btn btn-sm btn-outline-secondary" href="{{ $problem['previous_problem_url'] }}">{{ __('public.previous_problem') }}</a>
                @endif
            </div>
            <div>
                @if($problem['next_problem_url'])
                    <a class="btn btn-sm btn-outline-secondary" href="{{ $problem['next_problem_url'] }}">{{ __('public.next_problem') }}</a>
                @endif
            </div>
        </section>
    </div>
@endsection
