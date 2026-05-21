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

    function key(kind, code) {
        return 'olymp_' + kind + '_' + code;
    }

    document.querySelectorAll('.problem-card').forEach(function (card) {
        const code = card.dataset.problemCode;
        const problemId = card.dataset.problemId;
        const bookmark = card.querySelector('.js-bookmark');
        const solved = card.querySelector('.js-solved');
        localStorage.setItem('olymp_last_problem', code);

        if (bookmark && localStorage.getItem(key('bookmark', code)) === '1') {
            bookmark.textContent = '★';
            bookmark.classList.add('active');
        }
        if (solved && localStorage.getItem(key('solved', code)) === '1') {
            solved.textContent = '✓';
            solved.classList.add('active');
        }

        if (bookmark) {
            bookmark.addEventListener('click', function () {
                const active = bookmark.classList.toggle('active');
                bookmark.textContent = active ? '★' : '☆';
                localStorage.setItem(key('bookmark', code), active ? '1' : '0');
                saveProgress(problemId, 'bookmark', active ? '1' : '0');
            });
        }

        if (solved) {
            solved.addEventListener('click', function () {
                const active = solved.classList.toggle('active');
                solved.textContent = active ? '✓' : '○';
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
})();
