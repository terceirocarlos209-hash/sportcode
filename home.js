/**
 * Home Page — carrega widgets via REST API
 * Responsável: Lena Pixel
 */
document.addEventListener('DOMContentLoaded', async () => {

  // ── Utilitários ──────────────────────────────────────────────

  function el(id) { return document.getElementById(id); }

  function matchCard(m) {
    const isLive     = m.status === 'live';
    const isFinished = m.status === 'finished';
    const scoreHtml  = (isLive || isFinished)
      ? `<div class="match-score ${isLive ? 'is-live' : ''}">${m.score_home ?? m.home_score ?? 0} × ${m.score_away ?? m.away_score ?? 0}</div>
         ${isLive ? `<div class="match-minute">${m.minute}'</div>` : ''}`
      : `<div class="match-date-time" style="font-size:.95rem;font-weight:600;color:var(--blue-700)">${formatDate(m.date)}</div>`;

    const logoHome = m.home_logo
      ? `<img class="match-team-logo" src="${escHtml(m.home_logo)}" alt="${escHtml(m.home)}" loading="lazy">`
      : `<div class="match-team-logo-placeholder">⚽</div>`;

    const logoAway = m.away_logo
      ? `<img class="match-team-logo" src="${escHtml(m.away_logo)}" alt="${escHtml(m.away)}" loading="lazy">`
      : `<div class="match-team-logo-placeholder">⚽</div>`;

    const matchUrl = `/partida/?id=${m.id}`;

    return `
      <a href="${matchUrl}" style="text-decoration:none;display:block">
        <div class="match-card ${isLive ? 'is-live' : ''}">
          <div class="match-header">
            <span class="match-competition">${escHtml(m.competition || 'Brasileirão')}</span>
            <span class="match-status-badge ${m.status}">${statusLabel(m.status)}</span>
          </div>
          <div class="match-body">
            <div class="match-team ${isCruzeiro(m.home) ? 'is-cruzeiro' : ''}">
              ${logoHome}
              <span class="match-team-name">${escHtml(m.home)}</span>
            </div>
            <div class="match-score-block">${scoreHtml}</div>
            <div class="match-team ${isCruzeiro(m.away) ? 'is-cruzeiro' : ''}">
              ${logoAway}
              <span class="match-team-name">${escHtml(m.away)}</span>
            </div>
          </div>
        </div>
      </a>`;
  }

  function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function statusLabel(s) {
    return { live:'Ao Vivo', finished:'Encerrado', scheduled:'Agendado', postponed:'Adiado' }[s] ?? s;
  }

  function isCruzeiro(name) {
    return /cruzeiro/i.test(name ?? '');
  }

  function formatDate(str) {
    if (!str) return '—';
    try {
      const d = new Date(str);
      return d.toLocaleString('pt-BR', {
        day:'2-digit', month:'2-digit',
        hour:'2-digit', minute:'2-digit', timeZone:'America/Sao_Paulo'
      });
    } catch { return str; }
  }

  function empty(icon, msg) {
    return `<div class="widget-empty"><div class="widget-empty-icon">${icon}</div><p>${msg}</p></div>`;
  }

  // ── Loaders ──────────────────────────────────────────────────

  async function loadLive() {
    const container = el('live-now');
    if (!container) return;
    try {
      const data = await sportscore.get('/matches/live');
      if (!data || !data.length) {
        container.innerHTML = empty('⚽', 'Nenhum jogo ao vivo agora.');
        return;
      }
      container.innerHTML = data.map(matchCard).join('');
    } catch {
      container.innerHTML = empty('⚠️', 'Erro ao carregar jogos ao vivo.');
    }
  }

  async function loadNext() {
    const container = el('next-matches');
    if (!container) return;
    try {
      const data = await sportscore.get('/matches/upcoming?limit=3');
      if (!data || !data.length) {
        container.innerHTML = empty('📅', 'Nenhum jogo agendado em breve.');
        return;
      }
      container.innerHTML = data.map(matchCard).join('');
    } catch {
      container.innerHTML = empty('⚠️', 'Erro ao carregar próximos jogos.');
    }
  }

  async function loadRecent() {
    const container = el('recent-matches');
    if (!container) return;
    try {
      const data = await sportscore.get('/matches/recent?limit=3');
      if (!data || !data.length) {
        container.innerHTML = empty('🏆', 'Nenhum resultado recente.');
        return;
      }
      container.innerHTML = data.map(matchCard).join('');
    } catch {
      container.innerHTML = empty('⚠️', 'Erro ao carregar resultados.');
    }
  }

  async function loadStandings() {
    const container = el('standings');
    if (!container) return;
    try {
      const data = await sportscore.get('/standings');
      if (!data || !data.length) {
        container.innerHTML = empty('📊', 'Classificação indisponível.');
        return;
      }

      const rows = data.slice(0, 10).map(t => {
        const isCruz = isCruzeiro(t.team);
        const form   = (t.form || '').split('').slice(-5).map(c => {
          const cls = c === 'W' ? 'w' : c === 'D' ? 'd' : 'l';
          const lbl = c === 'W' ? 'V' : c === 'D' ? 'E' : 'D';
          return `<span class="${cls}" title="${lbl}">${lbl}</span>`;
        }).join('');

        const zone = t.position <= 4 ? 'libertadores'
                   : t.position <= 6 ? 'sulamericana'
                   : t.position >= 17 ? 'relegation' : '';

        const logo = t.team_logo
          ? `<img class="st-team-logo" src="${escHtml(t.team_logo)}" alt="${escHtml(t.team)}" loading="lazy">`
          : '<span style="font-size:.9rem">⚽</span>';

        return `
          <tr ${isCruz ? 'class="is-cruzeiro"' : ''} ${zone ? `data-zone="${zone}"` : ''}>
            <td class="st-pos">${t.position}</td>
            <td><div class="st-team">${logo}<span class="st-team-name">${escHtml(t.team)}</span></div></td>
            <td class="st-pts">${t.points}</td>
            <td>${t.played}</td>
            <td style="display:none">${t.won}</td>
            <td style="display:none">${t.drawn}</td>
            <td style="display:none">${t.lost}</td>
            <td>${t.goal_diff > 0 ? '+' : ''}${t.goal_diff}</td>
            <td><div class="st-form">${form}</div></td>
          </tr>`;
      }).join('');

      container.innerHTML = `
        <table class="standings-table">
          <thead>
            <tr>
              <th class="st-pos">#</th>
              <th style="text-align:left">Clube</th>
              <th title="Pontos">Pts</th>
              <th title="Jogos">J</th>
              <th title="Saldo">SG</th>
              <th title="Forma">Forma</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
        <div style="display:flex;gap:12px;padding:12px 20px;font-size:.7rem;color:var(--gray-400)">
          <span><span style="display:inline-block;width:3px;height:12px;background:#1B5E20;border-radius:2px;margin-right:4px"></span>Libertadores</span>
          <span><span style="display:inline-block;width:3px;height:12px;background:#E65100;border-radius:2px;margin-right:4px"></span>Sul-Americana</span>
          <span><span style="display:inline-block;width:3px;height:12px;background:var(--red);border-radius:2px;margin-right:4px"></span>Rebaixamento</span>
        </div>`;
    } catch {
      container.innerHTML = empty('⚠️', 'Erro ao carregar classificação.');
    }
  }

  // ── Boot ─────────────────────────────────────────────────────

  await Promise.allSettled([
    loadLive(),
    loadNext(),
    loadRecent(),
    loadStandings(),
  ]);

  // Polling ao vivo a cada 60s
  const liveInterval = (window.sseConfig && window.sseConfig.liveInterval) || 60000;
  setInterval(() => {
    loadLive();
    loadStandings();
  }, liveInterval);

});
