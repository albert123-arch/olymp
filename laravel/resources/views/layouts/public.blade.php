<!doctype html>
<html lang="{{ $currentLang ?? app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f766e">
    <title>{{ $pageTitle ?? config('app.name', 'Olympiad Mathematics') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <script>
        (function () {
            try {
                const storedTheme = localStorage.getItem('readerTheme');
                const systemTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.dataset.readerTheme = storedTheme || systemTheme;
            } catch (error) {
                document.documentElement.dataset.readerTheme = 'light';
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Serif:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/olymp-reader.css') }}" rel="stylesheet">
    <style>
        :root {
            --site-navy: #123a52;
            --site-teal: #1f7a8c;
            --site-surface: #f5f9fc;
            --site-border: #d9e5ee;
            --site-text: #17324a;
        }

        body {
            font-family: "Noto Sans", sans-serif;
            color: var(--site-text);
            background:
                radial-gradient(circle at top left, rgba(31, 122, 140, 0.08), transparent 32%),
                radial-gradient(circle at top right, rgba(18, 58, 82, 0.07), transparent 28%),
                var(--site-surface);
        }

        h1, h2, h3, .brand-serif {
            font-family: "Noto Serif", serif;
        }

        .app-shell {
            min-height: 100vh;
        }

        .site-nav {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(217, 229, 238, 0.9);
        }

        .hero-panel, .content-panel, .surface-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--site-border);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(18, 58, 82, 0.06);
        }

        .hero-panel {
            overflow: hidden;
        }

        .hero-figure {
            background: linear-gradient(135deg, rgba(31, 122, 140, 0.12), rgba(18, 58, 82, 0.06));
            border-left: 1px solid rgba(217, 229, 238, 0.8);
            min-height: 100%;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        .card-link:hover .surface-card {
            border-color: rgba(31, 122, 140, 0.5);
            transform: translateY(-1px);
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .content-html p:last-child {
            margin-bottom: 0;
        }

        .content-html img {
            max-width: 100%;
            height: auto;
        }

        .math-block {
            overflow-x: auto;
        }

        .problem-card h3,
        .chapter-card h3,
        .course-card h3 {
            line-height: 1.25;
        }

        .muted-label {
            letter-spacing: 0.02em;
            text-transform: uppercase;
            font-size: 0.74rem;
        }
    </style>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)'], ['$', '$']],
                displayMath: [['\\[', '\\]'], ['$$', '$$']],
                processEscapes: true,
                packages: {'[+]': ['ams']}
            },
            svg: {
                fontCache: 'global'
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            }
        };
    </script>
    <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
</head>
<body>
<div class="app-shell d-flex flex-column">
    <nav class="site-nav navbar navbar-expand-lg sticky-top">
        <div class="container py-2">
            <a class="navbar-brand fw-semibold text-decoration-none" href="{{ route('home', ['lang' => $currentLang]) }}">
                Olympiad Mathematics
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="siteNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}" href="{{ route('home', ['lang' => $currentLang]) }}">{{ __('public.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courses.index') ? 'active fw-semibold' : '' }}" href="{{ route('courses.index', ['lang' => $currentLang]) }}">{{ __('public.courses') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ladders.*') ? 'active fw-semibold' : '' }}" href="{{ route('ladders.index', ['lang' => $currentLang]) }}">{{ __('public.ladders') }}</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active fw-semibold' : '' }}" href="{{ route('login', ['lang' => $currentLang]) }}">{{ __('public.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active fw-semibold' : '' }}" href="{{ route('register', ['lang' => $currentLang]) }}">{{ __('public.register') }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard', ['lang' => $currentLang]) }}">{{ __('public.dashboard') }}</a>
                        </li>
                        @if(method_exists(auth()->user(), 'isAdminUser') && auth()->user()->isAdminUser())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin') }}">{{ __('public.admin') }}</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <span class="nav-link text-secondary">{{ auth()->user()->name ?: auth()->user()->email }}</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout', ['lang' => $currentLang]) }}">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link p-0">{{ __('public.logout') }}</button>
                            </form>
                        </li>
                    @endguest
                    <li class="nav-item ms-lg-2">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Language switcher">
                            @foreach($availableLanguages as $language)
                                <a class="btn btn-outline-secondary {{ $currentLang === $language->code ? 'active' : '' }}" href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except('lang'), ['lang' => $language->code])) }}">
                                    {{ strtoupper($language->code) }}
                                </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4 py-lg-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="py-4">
        <div class="container">
            <div class="small text-muted">
                <span>Albert</span>
                <span class="mx-2">•</span>
                <a href="https://olymp.maths4u.sbs/" target="_blank" rel="noreferrer">olymp.maths4u.sbs</a>
                <span class="mx-2">•</span>
                <a href="mailto:support@maths4u.sbs">support@maths4u.sbs</a>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/olymp-reader.js') }}" defer></script>
<script>
    function typesetMath() {
        if (window.MathJax && window.MathJax.typesetPromise) {
            const nodes = document.querySelectorAll('.math-content');
            MathJax.typesetPromise(nodes);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(typesetMath, 100);
    });

    document.addEventListener('shown.bs.collapse', function () {
        setTimeout(typesetMath, 50);
    });
</script>
</body>
</html>
