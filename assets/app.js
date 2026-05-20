(function () {
  const lang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('olymp_lang') || 'en';
  localStorage.setItem('olymp_lang', lang);

  function key(type, code) {
    return `olymp_${type}_${code}`;
  }

  document.querySelectorAll('.problem-card').forEach((card) => {
    const code = card.dataset.problemCode;
    if (!code) return;

    const bookmark = card.querySelector('.js-bookmark');
    const solved = card.querySelector('.js-solved');

    function syncButton(button, storageKey) {
      if (!button) return;
      const active = localStorage.getItem(storageKey) === '1';
      button.classList.toggle('active', active);
      button.textContent = active ? button.dataset.active : button.dataset.default;
    }

    const bookmarkKey = key('bookmark', code);
    const solvedKey = key('solved', code);
    syncButton(bookmark, bookmarkKey);
    syncButton(solved, solvedKey);

    bookmark?.addEventListener('click', () => {
      localStorage.setItem(bookmarkKey, localStorage.getItem(bookmarkKey) === '1' ? '0' : '1');
      syncButton(bookmark, bookmarkKey);
    });

    solved?.addEventListener('click', () => {
      localStorage.setItem(solvedKey, localStorage.getItem(solvedKey) === '1' ? '0' : '1');
      syncButton(solved, solvedKey);
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
