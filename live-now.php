<section class="card widget-live" aria-label="Jogos ao vivo">
  <div class="card-header">
    <h2 class="card-title">
      <span class="sse-live-dot" style="width:10px;height:10px;display:inline-block;border-radius:50%;background:var(--live-red);animation:sse-pulse 1.4s infinite"></span>
      Ao Vivo
    </h2>
    <a class="card-link" href="<?php echo esc_url( home_url( '/partidas/' ) ); ?>">Ver todas →</a>
  </div>
  <div class="card-body">
    <div id="live-now">
      <!-- Skeleton inicial -->
      <div class="sse-skeleton skeleton-line"></div>
      <div class="sse-skeleton skeleton-line w-75" style="margin-top:8px"></div>
    </div>
  </div>
</section>
