@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mb-4" data-problem-page data-login-url="{{ $problem['login_url'] }}">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <p class="muted-label mb-2 text-secondary">{{ __('public.problem') }}</p>
                <h1 class="h2 mb-2">{{ $problem['code'] }} {{ $problem['title'] }}</h1>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge text-bg-light border">{{ $problem['book_number'] ? '#'.$problem['book_number'] : $problem['code'] }}</span>
                    @foreach($problem['grade_badges'] ?? [] as $gradeBadge)
                        <span class="badge text-bg-light border">{{ $gradeBadge }}</span>
                    @endforeach
                    <span class="badge {{ $problem['difficulty_class'] }}">{{ $problem['difficulty_stars'] }} {{ $problem['difficulty_label'] }}</span>
                    <button type="button"
                            class="btn btn-sm {{ $problem['is_solved'] ? 'btn-success' : 'btn-outline-secondary' }} px-2 py-0"
                            data-problem-action="toggle-solved"
                            data-url="{{ $problem['solved_toggle_url'] }}"
                            @disabled(!$problem['can_track_progress'])>{{ $problem['is_solved'] ? '✓' : '○' }}</button>
                    <button type="button"
                            class="btn btn-sm {{ $problem['is_bookmarked'] ? 'btn-warning' : 'btn-outline-secondary' }} px-2 py-0"
                            data-problem-action="toggle-bookmark"
                            data-url="{{ $problem['bookmark_toggle_url'] }}"
                            @disabled(!$problem['can_track_progress'])>{{ $problem['is_bookmarked'] ? '★' : '☆' }}</button>
                </div>
                @if($problem['statement_html'])
                    <div class="content-html math-content math-block mb-4">
                        {!! $problem['statement_html'] !!}
                    </div>
                @endif
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
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <p class="muted-label mb-0 text-secondary">{{ __('public.hint') }}</p>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#problemHintBox">
                    {{ __('public.hint') }}
                </button>
            </div>
            <div id="problemHintBox" class="collapse">
                <div class="content-html math-content math-block">
                    {!! $problem['hint_html'] !!}
                </div>
            </div>
        </section>
    @endif

    @if($problem['solution_html'])
        <section class="content-panel p-4 p-lg-5 mb-4">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <p class="muted-label mb-0 text-secondary">{{ __('public.solution') }}</p>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#problemSolutionBox">
                    {{ __('public.show_solution') }}
                </button>
            </div>
            <div id="problemSolutionBox" class="collapse">
                <div class="content-html math-content math-block">
                    {!! $problem['solution_html'] !!}
                </div>
            </div>
        </section>
    @endif

    @if($problem['teacher_note_html'])
        <section class="content-panel p-4 p-lg-5">
            <p class="muted-label mb-2 text-secondary">{{ __('public.teacher_note') }}</p>
            <div class="content-html math-content math-block">
                {!! $problem['teacher_note_html'] !!}
            </div>
        </section>
    @endif

    <section class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('[data-problem-page]');
            if (!root) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const loginUrl = root.dataset.loginUrl;

            const applyState = function (button, isActive, activeChar, idleChar, activeClass) {
                button.textContent = isActive ? activeChar : idleChar;
                button.classList.toggle(activeClass, isActive);
                button.classList.toggle('btn-outline-secondary', !isActive);
            };

            root.querySelectorAll('[data-problem-action]').forEach(function (button) {
                button.addEventListener('click', async function () {
                    if (button.disabled) {
                        if (loginUrl) {
                            window.location.href = loginUrl;
                        }
                        return;
                    }

                    const url = button.dataset.url;
                    if (!url || !csrfToken) {
                        return;
                    }

                    button.disabled = true;
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({})
                        });

                        if (response.status === 401) {
                            const payload = await response.json().catch(() => ({}));
                            if (payload.login_url) {
                                window.location.href = payload.login_url;
                            }
                            return;
                        }

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        if (button.dataset.problemAction === 'toggle-solved' && typeof data.solved === 'boolean') {
                            applyState(button, data.solved, '✓', '○', 'btn-success');
                        }
                        if (button.dataset.problemAction === 'toggle-bookmark' && typeof data.bookmarked === 'boolean') {
                            applyState(button, data.bookmarked, '★', '☆', 'btn-warning');
                        }
                    } finally {
                        button.disabled = false;
                    }
                });
            });
        });
    </script>
@endsection
