<?php
add_action( 'wp_enqueue_scripts', function () {
    $ver = wp_get_theme()->get( 'Version' );
    $uri = get_template_directory_uri();

    // Estilos
    wp_enqueue_style( 'sportscore-style', get_stylesheet_uri(), [], $ver );

    // Scripts — base API client
    wp_enqueue_script( 'sportscore-api',
        $uri . '/assets/js/api.js', [], $ver, true );

    // Página inicial
    if ( is_front_page() ) {
        wp_enqueue_script( 'sportscore-home',
            $uri . '/assets/js/home.js', [ 'sportscore-api' ], $ver, true );
    }

    // Match Center
    if ( is_page_template( 'page-templates/page-match-center.php' ) ) {
        wp_enqueue_script( 'sportscore-match-center',
            $uri . '/assets/js/match-center.js', [ 'sportscore-api' ], $ver, true );
    }

    // Live ticker em todas as páginas
    wp_enqueue_script( 'sportscore-live-ticker',
        $uri . '/assets/js/live-ticker.js', [ 'sportscore-api' ], $ver, true );

    // Config global JS
    wp_localize_script( 'sportscore-api', 'sseConfig', [
        'apiBase'     => rest_url( 'sportscore/v1' ),
        'nonce'       => wp_create_nonce( 'wp_rest' ),
        'liveInterval' => 60000,
        'isLivePage'  => is_page_template( 'page-templates/page-match-center.php' ) ? 1 : 0,
        'matchId'     => isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0,
        'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
    ] );
} );
