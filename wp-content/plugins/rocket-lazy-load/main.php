<?php
defined('ABSPATH') || die('Cheatin&#8217; uh?');

// Composer autoload.
if (file_exists(ROCKET_LL_PATH . 'vendor/autoload.php')) {
    require ROCKET_LL_PATH . 'vendor/autoload.php';
}

$rocket_lazyload = new RocketLazyLoadPlugin\Plugin();

add_action('plugins_loaded', [ $rocket_lazyload, 'load' ]);
