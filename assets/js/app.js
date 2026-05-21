(function () {
    const lang = new URLSearchParams(window.location.search).get('lang');
    if (lang) {
        localStorage.setItem('olymp_lang', lang);
        document.cookie = 'lang=' + encodeURIComponent(lang) + ';path=/;max-age=31536000;samesite=lax';
    } else {
        const saved = localStorage.getItem('olymp_lang');
        if (saved && !window.location.search.includes('lang=')) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', saved);
            window.location.replace(url.toString());
        }
    }

    function typeset(node) {
        if (window.MathJax && window.MathJax.typesetPromise) {
            window.MathJax.typesetPromise(node ? [node] : undefined).catch(function () {});
        }
    }

    window.addEventListener('load', function () {
        typeset(document.body);
    });

    function key(kind, code) {
        return 'olymp_' + kind + '_' + code;
    }

    const svgIcons = {
        bookmark: '<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18l-6-3-6 3z"/></svg>',
        'bookmark-fill': '<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path fill="currentColor" d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18l-6-3-6 3z"/></svg>',
        circle: '<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/></svg>',
        'check-circle': '<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>'
    };

    function setButtonIcon(button, name) {
        if (button && svgIcons[name]) {
            button.innerHTML = svgIcons[name];
        }
    }

    document.querySelectorAll('.problem-card').forEach(function (card) {
        const code = card.dataset.problemCode;
        const problemId = card.dataset.problemId;
        const bookmark = card.querySelector('.js-bookmark');
        const solved = card.querySelector('.js-solved');
        localStorage.setItem('olymp_last_problem', code);

        if (bookmark && localStorage.getItem(key('bookmark', code)) === '1') {
            setButtonIcon(bookmark, bookmark.dataset.iconOn);
            bookmark.classList.add('active');
        }
        if (solved && localStorage.getItem(key('solved', code)) === '1') {
            setButtonIcon(solved, solved.dataset.iconOn);
            solved.classList.add('active');
        }

        if (bookmark) {
            bookmark.addEventListener('click', function () {
                const active = bookmark.classList.toggle('active');
                setButtonIcon(bookmark, active ? bookmark.dataset.iconOn : bookmark.dataset.iconOff);
                localStorage.setItem(key('bookmark', code), active ? '1' : '0');
                saveProgress(problemId, 'bookmark', active ? '1' : '0');
            });
        }

        if (solved) {
            solved.addEventListener('click', function () {
                const active = solved.classList.toggle('active');
                setButtonIcon(solved, active ? solved.dataset.iconOn : solved.dataset.iconOff);
                localStorage.setItem(key('solved', code), active ? '1' : '0');
                saveProgress(problemId, 'solved', active ? '1' : '0');
            });
        }
    });

    function saveProgress(problemId, kind, value) {
        if (!window.OLYMP_AUTH || !window.OLYMP_AUTH.loggedIn || !problemId || problemId === '0') {
            return;
        }
        const body = new URLSearchParams();
        body.set('csrf_token', window.OLYMP_AUTH.csrf);
        body.set('problem_id', problemId);
        body.set('kind', kind);
        body.set('value', value);
        fetch(window.OLYMP_AUTH.progressUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
            credentials: 'same-origin'
        }).catch(function () {});
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-toggle-panel');
        if (!button) {
            return;
        }
        const panel = document.querySelector(button.dataset.target);
        if (!panel) {
            return;
        }
        panel.classList.toggle('d-none');
        if (!panel.classList.contains('d-none')) {
            typeset(panel);
        }
    });

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = document.querySelector(event.target.dataset.bsTarget);
            if (target) {
                typeset(target);
            }
        });
    });
})();
