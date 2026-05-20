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
    syncButton(bookmark, isBookmarked);
    syncButton(solved, isSolved);

    bookmark?.addEventListener('click', async () => {
      const next = !isBookmarked;
      if (bookmark.dataset.apiUrl && problemId > 0) {
        bookmark.disabled = true;
        try {
          const data = await postState(bookmark.dataset.apiUrl, {problem_id: problemId, bookmarked: next});
          if (data.ok) isBookmarked = !!data.bookmarked;
        } catch (e) {
        } finally {
          bookmark.disabled = false;
        }
      } else {
        isBookmarked = next;
        localStorage.setItem(bookmarkKey, isBookmarked ? '1' : '0');
      }
      syncButton(bookmark, isBookmarked);
    });

    solved?.addEventListener('click', async () => {
      const next = !isSolved;
      if (solved.dataset.apiUrl && problemId > 0) {
        solved.disabled = true;
        try {
          const data = await postState(solved.dataset.apiUrl, {problem_id: problemId, solved: next});
          if (data.ok) isSolved = data.status === 'solved';
        } catch (e) {
        } finally {
          solved.disabled = false;
        }
      } else {
        isSolved = next;
        localStorage.setItem(solvedKey, isSolved ? '1' : '0');
      }
      syncButton(solved, isSolved);
    });
  });

  document.querySelectorAll('.collapse').forEach((panel) => {
    panel.addEventListener('shown.bs.collapse', () => {
      if (window.MathJax?.typesetPromise) {
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
})();
