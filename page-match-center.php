<?php
/**
 * Template Name: Match Center
 *
 * Página de partida ao vivo — a joia do portal.
 * Consume REST API do Sportscore Engine v2 (/match/{id}/center).
 *
 * Tabs: Timeline | Estatísticas | Escalações
 */
$match_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
get_header();
?>

<main class="container">
  <div class="mc-layout" id="match-center" data-id="<?php echo esc_attr( $match_id ); ?>">

    <?php if ( ! $match_id ) : ?>

      <div class="card">
        <div class="card-body" style="text-align:center;padding:var(--space-12)">
          <p style="font-size:1.1rem;color:var(--gray-400)">
            Nenhuma partida selecionada. Acesse via lista de jogos.
          </p>
          <a href="<?php echo esc_url( home_url( '/partidas/' ) ); ?>"
             style="display:inline-block;margin-top:var(--space-4);padding:var(--space-3) var(--space-6);background:var(--blue-600);color:#fff;border-radius:var(--radius);font-family:var(--font-display);font-weight:700;text-transform:uppercase;letter-spacing:.06em">
            Ver Partidas
          </a>
        </div>
      </div>

    <?php else : ?>

      <!-- ══════════════════════════════════════════════════════
           SCOREBOARD HERO
           ══════════════════════════════════════════════════════ -->
      <div id="mc-scoreboard" class="mc-scoreboard">

        <div class="mc-header">
          <div class="mc-competition" id="mc-competition">Carregando...</div>
          <span class="mc-status-live" id="mc-status-badge" style="display:none">
            <span class="sse-live-dot"></span> AO VIVO
          </span>
        </div>

        <div class="mc-teams-row">

          <!-- Time da casa -->
          <div class="mc-team" id="mc-home-team">
            <div class="mc-team-logo-placeholder"
                 style="width:80px;height:80px;margin:0 auto 16px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem">
              ⚽
            </div>
            <div class="mc-team-name" id="mc-home-name">—</div>
          </div>

          <!-- Placar -->
          <div class="mc-score-center">
            <div class="mc-score-board">
              <span id="mc-score-home">0</span>
              <span class="mc-score-sep" id="mc-score-sep">×</span>
              <span id="mc-score-away">0</span>
            </div>
            <div class="mc-minute" id="mc-minute" style="display:none"></div>
            <div style="font-size:.82rem;color:rgba(255,255,255,.5);margin-top:var(--space-2)" id="mc-match-date"></div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.35);margin-top:4px" id="mc-stadium"></div>
          </div>

          <!-- Time visitante -->
          <div class="mc-team" id="mc-away-team">
            <div class="mc-team-logo-placeholder"
                 style="width:80px;height:80px;margin:0 auto 16px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem">
              ⚽
            </div>
            <div class="mc-team-name" id="mc-away-name">—</div>
          </div>

        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════
           TABS — Timeline | Estatísticas | Escalações
           ══════════════════════════════════════════════════════ -->
      <div class="card">
        <div class="card-body" style="padding:0">

          <div class="mc-tabs" role="tablist">
            <div class="mc-tab active" data-tab="timeline" role="tab" aria-selected="true"  tabindex="0">
              ⏱ Timeline
            </div>
            <div class="mc-tab" data-tab="stats"    role="tab" aria-selected="false" tabindex="-1">
              📊 Estatísticas
            </div>
            <div class="mc-tab" data-tab="lineups"  role="tab" aria-selected="false" tabindex="-1">
              👕 Escalações
            </div>
          </div>

          <!-- Tab: Timeline (wp_ss_match_events) -->
          <div id="tab-timeline" class="tab-panel" role="tabpanel" style="padding:var(--space-5)">
            <div id="mc-timeline">
              <div class="widget-empty">
                <div class="widget-empty-icon">⏱️</div>
                <p>Aguardando eventos da partida...</p>
              </div>
            </div>
          </div>

          <!-- Tab: Estatísticas (wp_ss_match_stats) -->
          <div id="tab-stats" class="tab-panel" role="tabpanel" style="display:none;padding:var(--space-5)">
            <div id="mc-stats">
              <div class="widget-empty">
                <div class="widget-empty-icon">📊</div>
                <p>Estatísticas indisponíveis no momento.</p>
              </div>
            </div>
          </div>

          <!-- Tab: Escalações (lineups JSON) -->
          <div id="tab-lineups" class="tab-panel" role="tabpanel" style="display:none;padding:var(--space-5)">
            <div id="mc-lineups">
              <div class="widget-empty">
                <div class="widget-empty-icon">👕</div>
                <p>Escalações ainda não divulgadas.</p>
              </div>
            </div>
          </div>

        </div>
      </div>

    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
