(function () {
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

    document.addEventListener('DOMContentLoaded', function () {
        setupActiveReaderNav();
        setTimeout(function () {
            typesetMath(document.body);
        }, 120);
    });
})();
