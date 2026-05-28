(function () {
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const storageGet = function (key) {
        try {
            return localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    };

    const storageSet = function (key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (error) {
            // Reader preferences are optional; keep the page usable if storage is blocked.
        }
    };

    const clampMediaSize = function (value) {
        const parsed = Number.parseInt(value, 10);
        if (!Number.isFinite(parsed)) {
            return 100;
        }

        return Math.min(160, Math.max(45, parsed));
    };

    const mediaSizeStorageKey = function (figure) {
        const mediaId = figure.dataset.problemMediaId || 'unknown';
        const user = figure.dataset.problemMediaUser || 'guest';

        return `problemMediaSize:${user}:${mediaId}`;
    };

    const applyMediaSize = function (figure, value, persist) {
        const size = clampMediaSize(value);
        const key = mediaSizeStorageKey(figure);

        document.querySelectorAll('[data-problem-media-resizable]').forEach(function (item) {
            if (mediaSizeStorageKey(item) !== key) {
                return;
            }

            item.style.setProperty('--problem-media-size', `${size}%`);
            const slider = item.querySelector('[data-problem-media-slider]');
            const output = item.querySelector('[data-problem-media-size-value]');

            if (slider) {
                slider.value = String(size);
            }
            if (output) {
                output.textContent = `${size}%`;
            }
        });

        if (persist) {
            storageSet(key, String(size));
        }
    };

    const setupProblemMediaResizers = function () {
        document.querySelectorAll('[data-problem-media-resizable]').forEach(function (figure) {
            if (figure.dataset.mediaResizeReady === '1') {
                return;
            }

            figure.dataset.mediaResizeReady = '1';
            const slider = figure.querySelector('[data-problem-media-slider]');
            if (!slider) {
                return;
            }

            applyMediaSize(figure, storageGet(mediaSizeStorageKey(figure)) || slider.value || '100', false);

            slider.addEventListener('input', function () {
                applyMediaSize(figure, slider.value, true);
            });
        });
    };

    const readerLabels = function () {
        const lang = (document.documentElement.lang || '').toLowerCase();
        const isRu = lang.startsWith('ru');

        return {
            hideList: isRu ? '\u0421\u043A\u0440\u044B\u0442\u044C \u0441\u043F\u0438\u0441\u043E\u043A' : 'Hide list',
            showList: isRu ? '\u041F\u043E\u043A\u0430\u0437\u0430\u0442\u044C \u0441\u043F\u0438\u0441\u043E\u043A' : 'Show list',
            light: isRu ? '\u0421\u0432\u0435\u0442\u043B\u0430\u044F' : 'Light',
            dark: isRu ? '\u0422\u0435\u043C\u043D\u0430\u044F' : 'Dark',
            theme: isRu ? '\u0422\u0435\u043C\u0430' : 'Theme'
        };
    };

    const systemTheme = function () {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const currentReaderTheme = function () {
        return document.documentElement.dataset.readerTheme || storageGet('readerTheme') || systemTheme();
    };

    const setReaderTheme = function (theme, persist) {
        const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
        document.documentElement.dataset.readerTheme = normalizedTheme;
        if (persist) {
            storageSet('readerTheme', normalizedTheme);
        }
    };

    const updateThemeButtons = function () {
        const labels = readerLabels();
        const theme = currentReaderTheme();
        document.querySelectorAll('[data-public-theme-toggle], [data-reader-theme-toggle]').forEach(function (button) {
            const label = button.querySelector('[data-public-theme-label]');
            const isReaderButton = button.hasAttribute('data-reader-theme-toggle');
            const nextLabel = theme === 'dark' ? labels.light : labels.dark;
            const buttonText = isReaderButton ? `${labels.theme}: ${nextLabel}` : nextLabel;
            if (label) {
                label.textContent = buttonText;
            } else {
                button.textContent = buttonText;
            }
            button.setAttribute('aria-label', nextLabel);
        });
    };

    const typesetMath = function (scope) {
        if (window.MathJax && window.MathJax.typesetPromise) {
            window.MathJax.typesetPromise(scope ? [scope] : undefined);
        }
    };

    const setReaderToggleState = function (button, isOpen) {
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        const label = button.querySelector('[data-reader-toggle-label]');
        const nextText = isOpen ? button.dataset.readerCloseLabel : button.dataset.readerOpenLabel;

        if (!nextText) {
            return;
        }

        if (label) {
            label.textContent = nextText;
        } else {
            button.textContent = nextText;
        }

        const icon = button.querySelector('[data-reader-toggle-icon]');
        if (icon) {
            icon.textContent = isOpen ? '-' : '+';
        }
    };

    const applyProgressState = function (button, isActive, activeClass) {
        button.dataset.state = isActive ? '1' : '0';
        button.textContent = isActive
            ? (button.dataset.activeChar || '\u2713')
            : (button.dataset.idleChar || '\u25CB');
        button.classList.toggle(activeClass, isActive);
        button.classList.toggle('btn-outline-secondary', !isActive);
    };

    const handleReaderToggle = function (button) {
        if (button.classList.contains('disabled')) {
            return;
        }

        const targetId = button.dataset.readerTargetId;
        const section = targetId ? document.getElementById(targetId) : null;
        if (!section) {
            return;
        }

        const willOpen = section.hidden;
        section.hidden = !willOpen;
        setReaderToggleState(button, willOpen);

        if (willOpen) {
            typesetMath(section);
        }
    };

    const handleProgressToggle = async function (button, card) {
        if (button.disabled) {
            const loginUrl = card?.dataset.loginUrl || document.querySelector('[data-problem-page]')?.dataset.loginUrl;
            if (loginUrl) {
                window.location.href = loginUrl;
            }
            return;
        }

        const targetUrl = button.dataset.url;
        const token = csrfToken();
        if (!targetUrl || !token) {
            return;
        }

        const action = button.dataset.action || button.dataset.problemAction;
        const activeClass = button.dataset.activeClass || (action === 'toggle-bookmark' ? 'btn-warning' : 'btn-success');

        button.disabled = true;
        try {
            const response = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
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
                applyProgressState(button, data.solved, activeClass);
            }
            if (action === 'toggle-bookmark' && typeof data.bookmarked === 'boolean') {
                applyProgressState(button, data.bookmarked, activeClass);
            }
        } finally {
            button.disabled = false;
        }
    };

    document.addEventListener('click', function (event) {
        const readerToggle = event.target.closest('[data-reader-toggle]');
        if (readerToggle) {
            event.preventDefault();
            handleReaderToggle(readerToggle);
            return;
        }

        const progressToggle = event.target.closest('[data-action], [data-problem-action]');
        if (!progressToggle) {
            return;
        }

        const action = progressToggle.dataset.action || progressToggle.dataset.problemAction;
        if (action !== 'toggle-solved' && action !== 'toggle-bookmark') {
            return;
        }

        const card = progressToggle.closest('[data-problem-card], [data-problem-page]');
        if (!card) {
            return;
        }

        event.preventDefault();
        handleProgressToggle(progressToggle, card);
    });

    document.addEventListener('toggle', function (event) {
        if (event.target.matches('.reader-details') && event.target.open) {
            typesetMath(event.target);
        }
    }, true);

    const setupActiveReaderNav = function () {
        const targets = Array.from(document.querySelectorAll('[data-reader-target]'));
        const links = Array.from(document.querySelectorAll('[data-reader-nav-link]'));
        if (!targets.length || !links.length || !('IntersectionObserver' in window)) {
            return;
        }

        const linksByHash = new Map(links.map((link) => [link.hash, link]));
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const active = linksByHash.get('#' + entry.target.id);
                if (!active) {
                    return;
                }

                links.forEach((link) => link.classList.toggle('is-active', link === active));
            });
        }, {
            rootMargin: '-25% 0px -65% 0px',
            threshold: 0
        });

        targets.forEach((target) => observer.observe(target));
    };

    const setupReaderToolbar = function () {
        const readerMain = document.querySelector('.reader-main');
        if (!readerMain || readerMain.querySelector(':scope > .reader-toolbar')) {
            return;
        }

        document.body.classList.add('reader-active');

        const labels = readerLabels();
        const shell = document.querySelector('.reader-shell');
        const toolbar = document.createElement('div');
        toolbar.className = 'reader-toolbar';

        const themeButton = document.createElement('button');
        themeButton.type = 'button';
        themeButton.className = 'reader-toolbar-button reader-theme-toggle';
        themeButton.setAttribute('aria-live', 'polite');
        themeButton.setAttribute('data-reader-theme-toggle', '');
        themeButton.innerHTML = '<span data-public-theme-label></span>';

        if (shell && shell.querySelector('.reader-sidebar')) {
            const sidebarButton = document.createElement('button');
            sidebarButton.type = 'button';
            sidebarButton.className = 'reader-toolbar-button reader-sidebar-toggle';

            const updateSidebarButton = function () {
                const isCollapsed = shell.classList.contains('is-sidebar-collapsed');
                sidebarButton.textContent = isCollapsed ? labels.showList : labels.hideList;
                sidebarButton.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            };

            const storedSidebarState = storageGet('readerSidebarCollapsed');
            if (storedSidebarState === 'true') {
                shell.classList.add('is-sidebar-collapsed');
            }

            sidebarButton.addEventListener('click', function () {
                const isCollapsed = shell.classList.toggle('is-sidebar-collapsed');
                storageSet('readerSidebarCollapsed', isCollapsed ? 'true' : 'false');
                updateSidebarButton();
            });

            updateSidebarButton();
            toolbar.appendChild(sidebarButton);
        }

        toolbar.appendChild(themeButton);

        if (toolbar.children.length > 0) {
            readerMain.prepend(toolbar);
            updateThemeButtons();
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (!storageGet('readerTheme')) {
            setReaderTheme(systemTheme(), false);
        }
        updateThemeButtons();
        document.addEventListener('click', function (event) {
            const themeButton = event.target.closest('[data-public-theme-toggle], [data-reader-theme-toggle]');
            if (!themeButton) {
                return;
            }

            event.preventDefault();
            const nextTheme = currentReaderTheme() === 'dark' ? 'light' : 'dark';
            setReaderTheme(nextTheme, true);
            updateThemeButtons();
            typesetMath(document.body);
        });
        setupReaderToolbar();
        setupProblemMediaResizers();
        setupActiveReaderNav();
        setTimeout(function () {
            typesetMath(document.body);
        }, 120);
    });

    if (window.matchMedia) {
        const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        colorSchemeQuery.addEventListener?.('change', function () {
            if (!storageGet('readerTheme')) {
                setReaderTheme(systemTheme(), false);
                updateThemeButtons();
            }
        });
    }
})();
