(function () {
  const lang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('olymp_lang') || 'en';
  localStorage.setItem('olymp_lang', lang);

  function key(type, code) {
    return `olymp_${type}_${code}`;
  }

  async function postState(url, payload) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      credentials: 'same-origin',
      keepalive: true,
      body: JSON.stringify(payload),
    });
    return response.json();
  }

  document.querySelectorAll('.problem-card').forEach((card) => {
    const code = card.dataset.problemCode;
    if (!code) return;

    const problemId = Number(card.dataset.problemId || 0);
    const bookmark = card.querySelector('.js-bookmark');
    const solved = card.querySelector('.js-solved');
    const solvedBadge = card.querySelector('.solved-state-badge');

    function syncButton(button, active) {
      if (!button) return;
      button.classList.toggle('active', active);
      const label = active ? button.dataset.active : button.dataset.default;
      button.setAttribute('aria-label', label);
      button.setAttribute('title', label);
      if (button === solved) {
        card.classList.toggle('is-solved', active);
        if (solvedBadge) solvedBadge.hidden = !active;
      }
    }

    const bookmarkKey = key('bookmark', code);
    const solvedKey = key('solved', code);
    let isBookmarked = bookmark?.dataset.apiUrl ? card.dataset.bookmarked === '1' : localStorage.getItem(bookmarkKey) === '1';
    let isSolved = solved?.dataset.apiUrl ? card.dataset.solved === '1' : localStorage.getItem(solvedKey) === '1';
    let bookmarkSaveTimer = null;
    let solvedSaveTimer = null;
    syncButton(bookmark, isBookmarked);
    syncButton(solved, isSolved);

    function saveLater(timer, callback) {
      if (timer) window.clearTimeout(timer);
      return window.setTimeout(callback, 80);
    }

    bookmark?.addEventListener('click', () => {
      isBookmarked = !isBookmarked;
      syncButton(bookmark, isBookmarked);
      if (bookmark.dataset.apiUrl && problemId > 0) {
        bookmarkSaveTimer = saveLater(bookmarkSaveTimer, () => {
          postState(bookmark.dataset.apiUrl, {problem_id: problemId, bookmarked: isBookmarked}).catch(() => {});
        });
      } else {
        localStorage.setItem(bookmarkKey, isBookmarked ? '1' : '0');
      }
    });

    solved?.addEventListener('click', () => {
      isSolved = !isSolved;
      syncButton(solved, isSolved);
      if (solved.dataset.apiUrl && problemId > 0) {
        solvedSaveTimer = saveLater(solvedSaveTimer, () => {
          postState(solved.dataset.apiUrl, {problem_id: problemId, solved: isSolved}).catch(() => {});
        });
      } else {
        localStorage.setItem(solvedKey, isSolved ? '1' : '0');
      }
    });
  });

  document.querySelectorAll('.js-reveal').forEach((panel) => {
    panel.addEventListener('toggle', () => {
      if (panel.open && window.MathJax?.typesetPromise) {
        window.MathJax.typesetPromise([panel]).catch(() => {});
      }
    });
  });

  const searchInput = document.getElementById('searchInput');
  const difficultyFilter = document.getElementById('difficultyFilter');
  const typeFilter = document.getElementById('typeFilter');
  const tagFilter = document.getElementById('tagFilter');

  function filterProblems() {
    const query = (searchInput?.value || '').trim().toLowerCase();
    const difficulty = difficultyFilter?.value || '';
    const type = typeFilter?.value || '';
    const tag = tagFilter?.value || '';
    document.querySelectorAll('.problem-card').forEach((card) => {
      const okSearch = !query || (card.dataset.search || '').includes(query);
      const okDifficulty = !difficulty || card.dataset.difficulty === difficulty;
      const okType = !type || card.dataset.type === type;
      const okTag = !tag || (` ${card.dataset.tags || ''} `).includes(` ${tag} `);
      card.hidden = !(okSearch && okDifficulty && okType && okTag);
    });
  }

  [searchInput, difficultyFilter, typeFilter, tagFilter].forEach((el) => {
    el?.addEventListener('input', filterProblems);
    el?.addEventListener('change', filterProblems);
  });

  function activateTabFromHash() {
    const hash = window.location.hash;
    if (!hash) return;
    const trigger = document.querySelector(`.nav-tabs [data-bs-toggle="tab"][href="${hash}"], .nav-tabs [data-bs-toggle="tab"][data-bs-target="${hash}"]`);
    if (!trigger) return;
    const tab = bootstrap.Tab.getOrCreateInstance(trigger);
    tab.show();
  }

  window.addEventListener('hashchange', activateTabFromHash);
  activateTabFromHash();
})();
