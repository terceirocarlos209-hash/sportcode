/**
 * Live Ticker — top bar com placar ao vivo
 * Faz polling a cada 60s. Para quando aba fica em background.
 * Responsável: Lena Pixel
 */
(function () {
  const INTERVAL = (window.sseConfig && window.sseConfig.liveInterval) || 60000;
  let timer = null;
  let retries = 0;
  const MAX_RETRIES = 5;
  const prevScores = {};

  const tickerBar    = document.querySelector('.sse-ticker-bar');
  const tickerScroll = document.getElementById('sse-live-ticker');

  if (!tickerScroll) return;

  async function fetchScoreboard() {
    try {
      const data = await sportscore.get('/matches/scoreboard');
      retries = 0;
      renderTicker(Array.isArray(data) ? data : []);
    } catch (err) {
      retries++;
      console.warn('[SSE Ticker] Erro #' + retries + ':', err.message);
      if (retries >= MAX_RETRIES) stopPolling();
    }
  }

  function renderTicker(matches) {
    if (!matches.length) {
      hideTicker();
      return;
    }

    tickerScroll.innerHTML = matches.map(m => `
      <div class="sse-ticker-item" data-match-id="${m.id}">
        <span class="sse-ticker-teams">
          ${sportscore.escapeHtml(m.home)}
          <strong class="sse-score">${m.home_score} – ${m.away_score}</strong>
          ${sportscore.escapeHtml(m.away)}
        </span>
        <span class="sse-ticker-minute">${m.minute}'</span>
      </div>
    `).join('');

    showTicker();
    checkGoals(matches);
  }

  function checkGoals(matches) {
    matches.forEach(m => {
      const curr = `${m.home_score}-${m.away_score}`;
      if (prevScores[m.id] && prevScores[m.id] !== curr) {
        document.dispatchEvent(new CustomEvent('sse:goal', { detail: m }));
      }
      prevScores[m.id] = curr;
    });
  }

  function showTicker() {
    tickerBar && tickerBar.classList.remove('sse-hidden');
  }
  function hideTicker() {
    tickerBar && tickerBar.classList.add('sse-hidden');
  }

  function startPolling() {
    clearInterval(timer);
    timer = setInterval(fetchScoreboard, INTERVAL);
  }
  function stopPolling() {
    clearInterval(timer);
  }

  // Pausa quando aba fica em background (economiza requisições)
  document.addEventListener('visibilitychange', () => {
    document.hidden ? stopPolling() : startPolling();
  });

  // Boot
  document.addEventListener('DOMContentLoaded', () => {
    fetchScoreboard();
    startPolling();
  });
})();
