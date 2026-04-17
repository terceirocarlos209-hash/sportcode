<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-inner">

      <div class="footer-brand">
        <a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <div class="site-logo-icon">⚽</div>
          <div class="site-logo-text">
            <span class="site-logo-name">Cruzeiro SportScore</span>
            <span class="site-logo-sub">Portal Esportivo</span>
          </div>
        </a>
        <p class="footer-desc">
          O maior portal de dados do Cruzeiro Esporte Clube.
          Partidas ao vivo, estatísticas, histórico completo e muito mais.
        </p>
      </div>

      <div class="footer-col">
        <h4 class="footer-col-title">Navegação</h4>
        <ul class="footer-links">
          <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a></li>
          <li><a href="<?php echo esc_url( home_url( '/partidas/' ) ); ?>">Partidas</a></li>
          <li><a href="<?php echo esc_url( home_url( '/classificacao/' ) ); ?>">Classificação</a></li>
          <li><a href="<?php echo esc_url( home_url( '/elenco/' ) ); ?>">Elenco</a></li>
          <li><a href="<?php echo esc_url( home_url( '/historia/' ) ); ?>">História</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-col-title">Competições</h4>
        <ul class="footer-links">
          <li><a href="#">Brasileirão Série A</a></li>
          <li><a href="#">Copa do Brasil</a></li>
          <li><a href="#">Libertadores</a></li>
          <li><a href="#">Mineiro</a></li>
          <li><a href="#">Histórico</a></li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <span>© <?php echo date( 'Y' ); ?> Cruzeiro SportScore. Todos os direitos reservados.</span>
      <span class="footer-powered">⚡ Powered by SportScore Engine</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
