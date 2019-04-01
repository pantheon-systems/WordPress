<?php
/**
 * Plugin Name: Lazy Load by WP Rocket
 * Plugin URI: http://wordpress.org/plugins/rocket-lazy-load/
 * Description: The tiny Lazy Load script for WordPress without jQuery or others libraries.
 * Version: 2.1.5
 * Author: WP Rocket
 * Author URI: https://wp-rocket.me
 * Text Domain: rocket-lazy-load
 * Domain Path: /languages
 *
 * @package RocketLazyloadPlugin
 *
 * Copyright 2015-2019 WP Media
 *
 * This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation; either version 2 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('ABSPATH') || die('Cheatin\' uh?');

define('ROCKET_LL_VERSION', '2.1.5');
define('ROCKET_LL_WP_VERSION', '4.7');
define('ROCKET_LL_PHP_VERSION', '5.4');
define('ROCKET_LL_BASENAME', plugin_basename(__FILE__));
define('ROCKET_LL_PATH', realpath(plugin_dir_path(__FILE__)) . '/');
define('ROCKET_LL_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');
define('ROCKET_LL_FRONT_JS_URL', ROCKET_LL_ASSETS_URL . 'js/');
define('ROCKET_LL_INT_MAX', PHP_INT_MAX - 15);

require ROCKET_LL_PATH . 'src/rocket-lazyload-requirements-check.php';

/**
 * Loads plugin translations
 *
 * @since 2.0
 * @author Remy Perona
 *
 * @return void
 */
function rocket_lazyload_textdomain()
{
    // Load translations from the languages directory.
    $locale = get_locale();

    // This filter is documented in /wp-includes/l10n.php.
    $locale = apply_filters('plugin_locale', $locale, 'rocket-lazy-load'); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound.
    load_textdomain('rocket-lazy-load', WP_LANG_DIR . '/plugins/rocket-lazy-load-' . $locale . '.mo');

    load_plugin_textdomain('rocket-lazy-load', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'rocket_lazyload_textdomain');

$rocket_lazyload_requirement_checks = new Rocket_Lazyload_Requirements_Check(
    array(
        'plugin_name'    => 'Lazy Load by WP Rocket',
        'plugin_version' => ROCKET_LL_VERSION,
        'wp_version'     => ROCKET_LL_WP_VERSION,
        'php_version'    => ROCKET_LL_PHP_VERSION,
    )
);

if ($rocket_lazyload_requirement_checks->check()) {
    require ROCKET_LL_PATH . 'main.php';
}

unset($rocket_lazyload_requirement_checks);
