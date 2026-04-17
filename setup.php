<?php
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
    add_theme_support( 'custom-logo' );

    register_nav_menus( [
        'main'   => 'Menu Principal',
        'footer' => 'Menu Rodapé',
    ] );
} );
