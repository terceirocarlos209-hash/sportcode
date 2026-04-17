<section class="card" style="margin-top:var(--space-6)" aria-label="Próximos jogos">
  <div class="card-header">
    <h2 class="card-title">📅 Próximos Jogos</h2>
    <a class="card-link" href="<?php echo esc_url( home_url( '/partidas/' ) ); ?>">Ver todos →</a>
  </div>
  <div class="card-body" style="padding:var(--space-4)">
    <div id="next-matches">
      <div class="sse-skeleton skeleton-line"></div>
      <div class="sse-skeleton skeleton-line w-50" style="margin-top:8px"></div>
    </div>
  </div>
</section>
