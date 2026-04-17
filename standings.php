<section class="card" aria-label="Classificação">
  <div class="card-header">
    <h2 class="card-title">📊 Classificação</h2>
    <a class="card-link" href="<?php echo esc_url( home_url( '/classificacao/' ) ); ?>">Completa →</a>
  </div>
  <div class="card-body" style="padding:var(--space-2) 0 0">
    <div id="standings">
      <!-- Skeleton -->
      <?php for ( $i = 0; $i < 6; $i++ ) : ?>
        <div style="display:flex;align-items:center;gap:8px;padding:10px 20px;border-bottom:1px solid var(--gray-100)">
          <div class="sse-skeleton skeleton-line" style="width:20px;flex-shrink:0"></div>
          <div class="sse-skeleton skeleton-line" style="flex:1"></div>
          <div class="sse-skeleton skeleton-line" style="width:28px;flex-shrink:0"></div>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>
