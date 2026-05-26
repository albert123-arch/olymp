@php
    $problemUid = 'problem-' . ($problem['id'] ?? $problem['code']);
    $displayNumber = $problem['display_number'] ?? ($problem['book_number'] ? '#'.$problem['book_number'] : $problem['code']);
    $hasHint = !empty($problem['hint_html']);
    $hasSolution = !empty($problem['solution_html']);
    $canTrack = (bool)($problem['can_track_progress'] ?? false);
    $isSolved = (bool)($problem['is_solved'] ?? false);
    $isBookmarked = (bool)($problem['is_bookmarked'] ?? false);
    $isRu = ($currentLang ?? app()->getLocale()) === 'ru';
    $detailsLabel = $isRu ? "\u{0414}\u{0435}\u{0442}\u{0430}\u{043B}\u{0438}" : 'Details';
    $hideHintLabel = $isRu ? "\u{0421}\u{043A}\u{0440}\u{044B}\u{0442}\u{044C} \u{043F}\u{043E}\u{0434}\u{0441}\u{043A}\u{0430}\u{0437}\u{043A}\u{0443}" : 'Hide hint';
    $hideSolutionLabel = $isRu ? "\u{0421}\u{043A}\u{0440}\u{044B}\u{0442}\u{044C} \u{0440}\u{0435}\u{0448}\u{0435}\u{043D}\u{0438}\u{0435}" : 'Hide solution';
@endphp

<article id="{{ $problemUid }}"
         class="surface-card problem-card reader-card p-3 p-lg-4"
         data-reader-target
         data-problem-card
         data-problem-id="{{ $problem['id'] }}"
         data-login-url="{{ $problem['login_url'] }}">
    <div class="reader-card-header">
        <div class="reader-title-block">
            <div class="reader-mobile-kicker">{{ $displayNumber }}</div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge text-bg-light border reader-number-badge">{{ $displayNumber }}</span>
                <h3 class="h6 mb-0 reader-problem-title">{{ $problem['title'] }}</h3>
            </div>
        </div>

        <div class="reader-meta-actions">
            @if($problem['main_tag'])
                <span class="badge text-bg-light border reader-tag-chip">{{ $problem['main_tag'] }}</span>
            @endif
            @foreach($problem['grade_badges'] ?? [] as $gradeBadge)
                <span class="badge text-bg-light border reader-meta-desktop">{{ $gradeBadge }}</span>
            @endforeach
            <span class="badge {{ $problem['difficulty_class'] }} reader-difficulty" aria-label="{{ $problem['difficulty_label'] }}">
                {{ $problem['difficulty_stars'] }}
            </span>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary reader-icon-button {{ $hasHint ? '' : 'disabled' }}"
                    data-reader-toggle="hint"
                    data-reader-target-id="{{ $problemUid }}-hint"
                    data-reader-open-label="?"
                    data-reader-close-label="{{ $hideHintLabel }}"
                    aria-expanded="false"
                    aria-controls="{{ $problemUid }}-hint"
                    title="{{ __('public.hint') }}"
                    aria-label="{{ __('public.hint') }}">?</button>

            <button type="button"
                    class="btn btn-sm {{ $isSolved ? 'btn-success' : 'btn-outline-secondary' }} reader-icon-button"
                    data-action="toggle-solved"
                    data-url="{{ $problem['solved_toggle_url'] }}"
                    data-state="{{ $isSolved ? '1' : '0' }}"
                    data-active-char="&#10003;"
                    data-idle-char="&#9675;"
                    title="{{ __('public.mark_solved') }}"
                    aria-label="{{ __('public.mark_solved') }}"
                    @disabled(!$canTrack)>{!! $isSolved ? '&#10003;' : '&#9675;' !!}</button>

            <button type="button"
                    class="btn btn-sm {{ $isBookmarked ? 'btn-warning' : 'btn-outline-secondary' }} reader-icon-button"
                    data-action="toggle-bookmark"
                    data-url="{{ $problem['bookmark_toggle_url'] }}"
                    data-state="{{ $isBookmarked ? '1' : '0' }}"
                    data-active-char="&#9733;"
                    data-idle-char="&#9734;"
                    title="{{ __('public.bookmark') }}"
                    aria-label="{{ __('public.bookmark') }}"
                    @disabled(!$canTrack)>{!! $isBookmarked ? '&#9733;' : '&#9734;' !!}</button>
        </div>
    </div>

    @if($problem['statement_html'])
        <div class="reader-statement content-html math-content math-block mb-3">
            {!! $problem['statement_html'] !!}
        </div>
    @endif

    @if(!empty($problem['source_label']))
        <div class="small text-secondary mb-3 reader-source-desktop">{{ $problem['source_label'] }}</div>
    @endif

    @if($hasHint)
        <section id="{{ $problemUid }}-hint" class="reader-accordion-content mb-3" hidden>
            <div class="small fw-semibold mb-1">{{ __('public.hint') }}</div>
            <div class="content-html math-content math-block">
                {!! $problem['hint_html'] !!}
            </div>
        </section>
    @endif

    <div class="reader-card-footer">
        <div>
            @if($hasSolution)
                <button type="button"
                        class="btn btn-sm btn-link text-decoration-none p-0 reader-solution-button"
                        data-reader-toggle="solution"
                        data-reader-target-id="{{ $problemUid }}-solution"
                        data-reader-open-label="{{ __('public.solution') }}"
                        data-reader-close-label="{{ $hideSolutionLabel }}"
                        aria-expanded="false"
                        aria-controls="{{ $problemUid }}-solution"
                        title="{{ __('public.solution') }}"
                        aria-label="{{ __('public.solution') }}">
                    <span class="me-1" aria-hidden="true">&#128161;</span><span data-reader-toggle-label>{{ __('public.solution') }}</span>
                </button>
            @endif
        </div>
        <a class="small text-decoration-none reader-open-link" href="{{ $problem['url'] }}">{{ __('public.open_full_problem') }}</a>
    </div>

    @if($hasSolution)
        <section id="{{ $problemUid }}-solution" class="reader-accordion-content mt-3" hidden>
            <div class="small fw-semibold mb-1">{{ __('public.solution') }}</div>
            <div class="content-html math-content math-block">
                {!! $problem['solution_html'] !!}
            </div>
        </section>
    @endif

    <details class="reader-details mt-3">
        <summary>{{ $detailsLabel }}</summary>
        <div class="reader-details-body">
            <div><strong>{{ __('public.problem') }}:</strong> {{ $problem['code'] }}</div>
            <div><strong>{{ __('public.difficulty') }}:</strong> {{ $problem['difficulty_label'] }}</div>
            @if($problem['main_tag'])
                <div><strong>Tag:</strong> {{ $problem['main_tag'] }}</div>
            @endif
            @if(!empty($problem['grade_badges']))
                <div><strong>Grade:</strong> {{ implode(', ', $problem['grade_badges']) }}</div>
            @endif
            @if(!empty($problem['source_label']))
                <div><strong>Source:</strong> {{ $problem['source_label'] }}</div>
            @endif
        </div>
    </details>
</article>
