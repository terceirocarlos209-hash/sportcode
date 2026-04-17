<?php
// Remove versão do WP
remove_action( 'wp_head', 'wp_generator' );

// Remove links desnecessários do head
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Desabilita XML-RPC (evita força bruta)
add_filter( 'xmlrpc_enabled', '__return_false' );

// Headers de segurança básicos
add_action( 'send_headers', function () {
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
} );
