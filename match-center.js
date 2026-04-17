/**
 * Match Center — página de partida ao vivo
 *
 * v2.0 — Consome endpoint /match/{id}/center (payload unificado):
 *   { match, events, stats }
 *
 * Tabs: Timeline | Estatísticas | Escalações
 * Polling: 15s ao vivo, 60s encerrada, pausa em background
 *
 * Responsável: Lena Pixel
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('match-center');
  if (!root) return;

  const matchId = parseInt(root.dataset.id, 10);
  if (!matchId) return;

  // ── Estado ───────────────────────────────────────────────────
  let prevHome = null;
  let prevAway = null;
  let isLive   = false;
  let timer    = null;

  // ── Elementos DOM ────────────────────────────────────────────
  const els = {
    competition: document.getElementById('mc-competition'),
    statusBadge: document.getElementById('mc-status-badge'),
    homeName:    document.getElementById('mc-home-name'),
    awayName:    document.getElementById('mc-away-name'),
    homeTeam:    document.getElementById('mc-home-team'),
    awayTeam:    document.getElementById('mc-away-team'),
    scoreHome:   document.getElementById('mc-score-home'),
    scoreAway:   document.getElementById('mc-score-away'),
    scoreSep:    document.getElementById('mc-score-sep'),
    minute:      document.getElementById('mc-minute'),
    matchDate:   document.getElementById('mc-match-date'),
    stadium:     document.getElementById('mc-stadium'),
    timeline:    document.getElementById('mc-timeline'),
    stats:       document.getElementById('mc-stats'),
    lineups:     document.getElementById('mc-lineups'),
  };

  // ── Tabs ─────────────────────────────────────────────────────
  document.querySelectorAll('.mc-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.mc-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => (p.style.display = 'none'));
      tab.classList.add('active');
      const panel = document.getElementById('tab-' + tab.dataset.tab);
      if (panel) panel.style.display = 'block';
    });
  });

  // ── Helpers ──────────────────────────────────────────────────
  function esc(s) {
    return String(s ?? '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function formatDate(str) {
    if (!str) return '';
    try {
      return new Date(str).toLocaleString('pt-BR', {
        weekday: 'long', day: '2-digit', month: 'long',
        hour: '2-digit', minute: '2-digit', timeZone: 'America/Sao_Paulo',
      });
    } catch { return str; }
  }

  function statusLabel(s) {
    return { live: 'Ao Vivo', finished: 'Encerrado', scheduled: 'Agendado', postponed: 'Adiado' }[s] ?? s;
  }

  /**
   * Ícone por event_type canônico (tabela wp_ss_match_events).
   */
  function eventIcon(type) {
    const icons = {
      goal:         '⚽',
      own_goal:     '🥅',
      penalty:      '🎯',
      yellow_card:  '🟨',
      red_card:     '🟥',
      substitution: '🔄',
      var:          '📺',
    };
    return icons[type] ?? icons[(type || '').toLowerCase()] ?? '•';
  }

  function eventLabel(type) {
    const labels = {
      goal:         'Gol',
      own_goal:     'Gol Contra',
      penalty:      'Pênalti',
      yellow_card:  'Cartão Amarelo',
      red_card:     'Cartão Vermelho',
      substitution: 'Substituição',
      var:          'VAR',
    };
    return labels[type] ?? type;
  }

  // ── Scoreboard ───────────────────────────────────────────────
  function renderScoreboard(m) {
    if (els.competition) els.competition.textContent = m.competition || 'Partida';
    if (els.homeName)    els.homeName.textContent    = m.home || '—';
    if (els.awayName)    els.awayName.textContent    = m.away || '—';
    if (els.matchDate)   els.matchDate.textContent   = formatDate(m.date);
    if (els.stadium && m.stadium) els.stadium.textContent = '🏟️ ' + m.stadium;

    const sh = m.score_home ?? 0;
    const sa = m.score_away ?? 0;

    // Animação de gol
    if (prevHome !== null && (sh !== prevHome || sa !== prevAway)) {
      flashGoal();
    }
    prevHome = sh;
    prevAway = sa;

    if (els.scoreHome) els.scoreHome.textContent = sh;
    if (els.scoreAway) els.scoreAway.textContent = sa;

    updateLogo('mc-home-team', m.home_logo, m.home);
    updateLogo('mc-away-team', m.away_logo, m.away);

    isLive = m.status === 'live';
    if (els.statusBadge) els.statusBadge.style.display = isLive ? 'inline-flex' : 'none';
    if (els.minute) {
      els.minute.style.display = (isLive && m.minute) ? 'block' : 'none';
      els.minute.textContent   = m.minute ? m.minute + "'" : '';
    }

    const board = document.querySelector('.mc-score-board');
    if (board) board.style.color = isLive ? 'var(--live-red)' : 'var(--white)';
  }

  function updateLogo(containerId, logoUrl, name) {
    const container = document.getElementById(containerId);
    if (!container || !logoUrl) return;
    const existing = container.querySelector('img.mc-team-logo');
    if (existing) return; // já tem logo
    const placeholder = container.querySelector('.mc-team-logo-placeholder');
    if (!placeholder) return;
    const img = document.createElement('img');
    img.className = 'mc-team-logo';
    img.src       = logoUrl;
    img.alt       = name || '';
    img.loading   = 'lazy';
    placeholder.replaceWith(img);
  }

  // ── Timeline (wp_ss_match_events) ────────────────────────────
  function renderTimeline(events) {
    if (!els.timeline) return;

    if (!events || !events.length) {
      els.timeline.innerHTML = `
        <div class="widget-empty">
          <div class="widget-empty-icon">⏱️</div>
          <p>Nenhum evento registrado ainda.</p>
        </div>`;
      return;
    }

    // Ordenação: mais recente primeiro
    const sorted = [...events].sort((a, b) => {
      const ma = Number(b.minute ?? 0);
      const mb = Number(a.minute ?? 0);
      return ma - mb;
    });

    els.timeline.innerHTML = sorted.map(e => {
      const min     = e.minute ?? '—';
      const extra   = e.extra_minute ? `+${e.extra_minute}` : '';
      const type    = e.event_type ?? '';
      const icon    = eventIcon(type);
      const label   = esc(eventLabel(type));
      const player  = esc(e.player_name || '—');
      const assist  = e.assist_name ? `<span style="color:var(--gray-400);font-size:.78rem"> ▸ ${esc(e.assist_name)}</span>` : '';
      const detail  = e.detail      ? `<span style="color:var(--gray-500)"> · ${esc(e.detail)}</span>` : '';

      return `
        <div class="timeline-event">
          <div class="timeline-minute">${min}'${extra}</div>
          <div class="timeline-icon">${icon}</div>
          <div>
            <div class="timeline-text">
              <span class="timeline-player">${player}</span>${assist}
            </div>
            <div class="timeline-detail">${label}${detail}</div>
          </div>
        </div>`;
    }).join('');
  }

  // ── Estatísticas (wp_ss_match_stats) ─────────────────────────
  function renderStats(statsRows, homeName, awayName) {
    if (!els.stats) return;

    if (!statsRows || statsRows.length < 2) {
      els.stats.innerHTML = `
        <div class="widget-empty">
          <div class="widget-empty-icon">📊</div>
          <p>Estatísticas indisponíveis.</p>
        </div>`;
      return;
    }

    const home = statsRows[0];
    const away = statsRows[1];

    const statItems = [
      { key: 'possession',      label: 'Posse de Bola',    unit: '%' },
      { key: 'shots',           label: 'Chutes',           unit: '' },
      { key: 'shots_on_target', label: 'Chutes no Gol',    unit: '' },
      { key: 'passes',          label: 'Passes',           unit: '' },
      { key: 'passes_accuracy', label: 'Precisão de Passes', unit: '%' },
      { key: 'fouls',           label: 'Faltas',           unit: '' },
      { key: 'corners',         label: 'Escanteios',       unit: '' },
      { key: 'offsides',        label: 'Impedimentos',     unit: '' },
      { key: 'yellow_cards',    label: 'Cartões Amarelos', unit: '' },
      { key: 'red_cards',       label: 'Cartões Vermelhos',unit: '' },
      { key: 'saves',           label: 'Defesas',          unit: '' },
    ];

    const header = `
      <div style="display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:var(--space-3) 0 var(--space-4);border-bottom:2px solid var(--gray-200);margin-bottom:var(--space-4)">
        <span style="font-family:var(--font-display);font-size:.82rem;font-weight:800;color:var(--blue-700);text-transform:uppercase">${esc(home.team_name || homeName || 'Casa')}</span>
        <span style="font-size:.7rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.1em;padding:0 var(--space-4)">Estatísticas</span>
        <span style="font-family:var(--font-display);font-size:.82rem;font-weight:800;color:var(--gray-600);text-transform:uppercase;text-align:right">${esc(away.team_name || awayName || 'Fora')}</span>
      </div>`;

    const rows = statItems.map(({ key, label, unit }) => {
      const hVal  = Number(home[key] ?? 0);
      const aVal  = Number(away[key] ?? 0);
      const total = hVal + aVal || 1;
      const hPct  = Math.round((hVal / total) * 100);
      const hDisplay = hVal + unit;
      const aDisplay = aVal + unit;

      return `
        <div class="stats-row">
          <div class="stats-label">
            <span class="stats-val" style="color:var(--blue-600)">${esc(hDisplay)}</span>
            <span class="stats-key">${esc(label)}</span>
            <span class="stats-val">${esc(aDisplay)}</span>
          </div>
          <div class="stats-bar">
            <div class="stats-bar-home" style="width:${hPct}%"></div>
            <div class="stats-bar-away"></div>
          </div>
        </div>`;
    }).join('');

    els.stats.innerHTML = header + rows;
  }

  // ── Escalações ───────────────────────────────────────────────
  function renderLineups(lineups, homeName, awayName) {
    if (!els.lineups) return;

    if (!lineups || !lineups.length) {
      els.lineups.innerHTML = `
        <div class="widget-empty">
          <div class="widget-empty-icon">👕</div>
          <p>Escalações não divulgadas.</p>
        </div>`;
      return;
    }

    const home = lineups[0];
    const away = lineups[1];

    const playerList = (players) =>
      (players || []).map(p => `
        <div class="lineup-player">
          <span class="lineup-number">${esc(p.player?.number ?? p.number ?? '')}</span>
          <span class="lineup-name">${esc(p.player?.name ?? p.name ?? '—')}</span>
          <span class="lineup-pos">${esc(p.player?.pos ?? p.pos ?? '')}</span>
        </div>`).join('');

    const formation = (team) =>
      team?.formation
        ? `<span style="font-size:.7rem;color:var(--gray-400);font-weight:500;margin-left:8px">${esc(team.formation)}</span>`
        : '';

    els.lineups.innerHTML = `
      <div class="lineups-grid">
        <div>
          <div class="lineup-col-title">
            ${esc(home?.team?.name || homeName || 'Casa')}${formation(home)}
          </div>
          ${playerList(home?.startXI)}
          ${home?.substitutes?.length
            ? `<div class="lineup-col-title" style="margin-top:var(--space-5);font-size:.75rem;color:var(--gray-400)">Reservas</div>${playerList(home.substitutes)}`
            : ''}
        </div>
        <div>
          <div class="lineup-col-title">
            ${esc(away?.team?.name || awayName || 'Fora')}${formation(away)}
          </div>
          ${playerList(away?.startXI)}
          ${away?.substitutes?.length
            ? `<div class="lineup-col-title" style="margin-top:var(--space-5);font-size:.75rem;color:var(--gray-400)">Reservas</div>${playerList(away.substitutes)}`
            : ''}
        </div>
      </div>`;
  }

  // ── Animação de gol ──────────────────────────────────────────
  function flashGoal() {
    const board = document.querySelector('.mc-scoreboard');
    if (!board) return;
    board.style.transition = 'background .4s ease';
    board.style.background = 'linear-gradient(160deg, #7B0000, #C41E3A)';
    setTimeout(() => { board.style.background = ''; }, 1800);
  }

  // ── Skeleton loading ─────────────────────────────────────────
  function showSkeleton() {
    if (els.timeline) {
      els.timeline.innerHTML = [1, 2, 3].map(() => `
        <div style="display:flex;gap:12px;padding:16px 0;border-bottom:1px solid var(--gray-100)">
          <div class="sse-skeleton skeleton-line" style="width:36px;height:14px;flex-shrink:0"></div>
          <div class="sse-skeleton skeleton-line" style="width:24px;height:24px;border-radius:50%;flex-shrink:0"></div>
          <div style="flex:1">
            <div class="sse-skeleton skeleton-line w-75" style="height:13px;margin-bottom:6px"></div>
            <div class="sse-skeleton skeleton-line w-50" style="height:11px"></div>
          </div>
        </div>`).join('');
    }
  }

  // ── Load principal via /match/{id}/center ────────────────────
  async function load() {
    try {
      showSkeleton();

      // Endpoint unificado: partida + eventos + stats em uma chamada
      const payload = await sportscore.get('/match/' + matchId + '/center');
      if (!payload) return;

      const { match: m, events, stats } = payload;

      renderScoreboard(m);
      renderTimeline(events);
      renderStats(stats, m.home, m.away);
      renderLineups(m.lineups, m.home, m.away);

      // Ajusta polling: 15s ao vivo, 60s encerrada
      const interval = isLive ? 15000 : 60000;
      clearInterval(timer);
      timer = setInterval(load, interval);

    } catch (err) {
      console.error('[SSE Match Center v2]', err.message);
      if (els.timeline) {
        els.timeline.innerHTML = `
          <div class="widget-empty">
            <div class="widget-empty-icon">⚠️</div>
            <p>Erro ao carregar dados. Tentando novamente...</p>
          </div>`;
      }
      // Retry em 30s em caso de erro
      clearInterval(timer);
      timer = setTimeout(load, 30000);
    }
  }

  // Pausa quando aba fica em background (economia de requests)
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      clearInterval(timer);
    } else {
      load();
    }
  });

  load();
});
