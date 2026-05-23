@php
    $problemUid = 'problem-' . ($problem['id'] ?? $problem['code']);
    $hasHint = !empty($problem['hint_html']);
    $hasSolution = !empty($problem['solution_html']);
    $canTrack = (bool)($problem['can_track_progress'] ?? false);
    $isSolved = (bool)($problem['is_solved'] ?? false);
    $isBookmarked = (bool)($problem['is_bookmarked'] ?? false);
@endphp

<article class="surface-card problem-card p-3 p-lg-4" data-problem-card data-problem-id="{{ $problem['id'] }}" data-login-url="{{ $problem['login_url'] }}">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <div class="min-w-0 flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge text-bg-light border">{{ $problem['display_number'] ?? ($problem['book_number'] ? '#'.$problem['book_number'] : $problem['code']) }}</span>
                <h3 class="h6 mb-0 text-truncate">{{ $problem['title'] }}</h3>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-1 justify-content-end">
            @if($problem['main_tag'])
                <span class="badge text-bg-light border">{{ $problem['main_tag'] }}</span>
            @endif
            <span class="badge {{ $problem['difficulty_class'] }}" aria-label="{{ $problem['difficulty_label'] }}">{{ $problem['difficulty_stars'] }}</span>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary px-2 py-0 {{ $hasHint ? '' : 'disabled' }}"
                    data-action="toggle-hint"
                    data-target="#{{ $problemUid }}-hint"
                    title="{{ __('public.hint') }}"
                    aria-label="{{ __('public.hint') }}">?</button>

            <button type="button"
                    class="btn btn-sm {{ $isSolved ? 'btn-success' : 'btn-outline-secondary' }} px-2 py-0"
                    data-action="toggle-solved"
                    data-url="{{ $problem['solved_toggle_url'] }}"
                    data-state="{{ $isSolved ? '1' : '0' }}"
                    title="{{ __('public.mark_solved') }}"
                    aria-label="{{ __('public.mark_solved') }}"
                    @disabled(!$canTrack)>{{ $isSolved ? '✓' : '○' }}</button>

            <button type="button"
                    class="btn btn-sm {{ $isBookmarked ? 'btn-warning' : 'btn-outline-secondary' }} px-2 py-0"
                    data-action="toggle-bookmark"
                    data-url="{{ $problem['bookmark_toggle_url'] }}"
                    data-state="{{ $isBookmarked ? '1' : '0' }}"
                    title="{{ __('public.bookmark') }}"
                    aria-label="{{ __('public.bookmark') }}"
                    @disabled(!$canTrack)>{{ $isBookmarked ? '★' : '☆' }}</button>
        </div>
    </div>

    @if($problem['statement_html'])
        <div class="content-html math-content math-block mb-3">
            {!! $problem['statement_html'] !!}
        </div>
    @endif

    @if($hasHint)
        <div id="{{ $problemUid }}-hint" class="d-none mb-3">
            <div class="small fw-semibold mb-1">{{ __('public.hint') }}</div>
            <div class="content-html math-content math-block">
                {!! $problem['hint_html'] !!}
            </div>
        </div>
    @endif

    @if($hasSolution)
        <div id="{{ $problemUid }}-solution" class="d-none mb-3">
            <div class="small fw-semibold mb-1">{{ __('public.solution') }}</div>
            <div class="content-html math-content math-block">
                {!! $problem['solution_html'] !!}
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <div>
            @if($hasSolution)
                <button type="button"
                        class="btn btn-sm btn-link text-decoration-none p-0"
                        data-action="toggle-solution"
                        data-target="#{{ $problemUid }}-solution"
                        title="{{ __('public.solution') }}"
                        aria-label="{{ __('public.solution') }}">
                    <span class="me-1" aria-hidden="true">💡</span>{{ __('public.solution') }}
                </button>
            @endif
        </div>
        <a class="small text-decoration-none" href="{{ $problem['url'] }}">{{ __('public.open_full_problem') }}</a>
    </div>
</article>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const safeTypeset = function (scope) {
                if (window.MathJax && window.MathJax.typesetPromise) {
                    MathJax.typesetPromise(scope ? [scope] : undefined);
                }
            };

            const applyState = function (button, isActive, activeChar, idleChar, activeClass) {
                button.dataset.state = isActive ? '1' : '0';
                button.textContent = isActive ? activeChar : idleChar;
                button.classList.toggle(activeClass, isActive);
                button.classList.toggle('btn-outline-secondary', !isActive);
            };

            document.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-action]');
                if (!button || button.classList.contains('disabled')) {
                    return;
                }

                const card = button.closest('[data-problem-card]');
                if (!card) {
                    return;
                }

                const action = button.dataset.action;

                if (action === 'toggle-hint' || action === 'toggle-solution') {
                    const targetSelector = button.dataset.target;
                    if (!targetSelector) {
                        return;
                    }

                    const section = card.querySelector(targetSelector);
                    if (!section) {
                        return;
                    }

                    section.classList.toggle('d-none');
                    safeTypeset(section);
                    return;
                }

                if (button.disabled) {
                    const loginUrl = card.dataset.loginUrl;
                    if (loginUrl) {
                        window.location.href = loginUrl;
                    }
                    return;
                }

                const targetUrl = button.dataset.url;
                if (!targetUrl || !csrfToken) {
                    return;
                }

                button.disabled = true;
                try {
                    const response = await fetch(targetUrl, {
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
                    if (action === 'toggle-solved' && typeof data.solved === 'boolean') {
                        applyState(button, data.solved, '✓', '○', 'btn-success');
                    }
                    if (action === 'toggle-bookmark' && typeof data.bookmarked === 'boolean') {
                        applyState(button, data.bookmarked, '★', '☆', 'btn-warning');
                    }
                } finally {
                    button.disabled = false;
                }
            });
        });
    </script>
@endonce
