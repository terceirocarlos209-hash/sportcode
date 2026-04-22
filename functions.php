<?php
/**
 * Sportscore Pro — functions.php
 * Apenas bootstrapping do tema. Lógica fica no plugin Engine.
 */

// Load Auto-Loader for Clean Architecture
require_once get_template_directory() . '/src/Autoloader.php';
\Sportscore\Autoloader::register();

// Initialize Clean Architecture
\Sportscore\Bootstrap::init();

// Include theme files
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/helpers.php';
