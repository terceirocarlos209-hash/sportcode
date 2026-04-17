<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#001A3D">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/global/live-ticker' ); ?>

<header class="site-header">
  <div class="container">
    <div class="header-inner">

      <a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
        <div class="site-logo-icon">⚽</div>
        <div class="site-logo-text">
          <span class="site-logo-name">Cruzeiro</span>
          <span class="site-logo-sub">SportScore Portal</span>
        </div>
      </a>

      <nav class="site-nav" aria-label="Navegação principal">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
           class="<?php echo is_front_page() ? 'active' : ''; ?>">Início</a>
        <a href="<?php echo esc_url( home_url( '/partidas/' ) ); ?>"
           class="<?php echo is_page( 'partidas' ) ? 'active' : ''; ?>">Partidas</a>
        <a href="<?php echo esc_url( home_url( '/classificacao/' ) ); ?>">Classificação</a>
        <a href="<?php echo esc_url( home_url( '/elenco/' ) ); ?>">Elenco</a>
        <a href="<?php echo esc_url( home_url( '/historia/' ) ); ?>">História</a>
        <?php
        wp_nav_menu( [
            'theme_location' => 'main',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => false,
            'link_before'    => '',
            'link_after'     => '',
        ] );
        ?>
      </nav>

    </div>
  </div>
</header>
