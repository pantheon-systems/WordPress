<?php
/**
 * WP Smush plugin
 *
 * Reduce image file sizes, improve performance and boost your SEO using the
 * <a href="https://premium.wpmudev.org/">WPMU DEV</a> WordPress Smush API.
 *
 * @link              http://premium.wpmudev.org/projects/wp-smush-pro/
 * @since             1.0.0
 * @package           WP_Smush
 *
 * @wordpress-plugin
 * Plugin Name:       Smush Pro
 * Plugin URI:        http://premium.wpmudev.org/projects/wp-smush-pro/
 * Description:       Reduce image file sizes, improve performance and boost your SEO using the free <a href="https://premium.wpmudev.org/">WPMU DEV</a> WordPress Smush API.
 * Version:           2.8.1
 * Author:            WPMU DEV
 * Author URI:        https://premium.wpmudev.org/
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-smushit
 * Domain Path:       /languages
 * WDP ID:            912164
 */

/*
Copyright 2007-2018 Incsub (http://incsub.com)
Author - Aaron Edwards, Sam Najian, Umesh Kumar

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WP_SMUSH_VERSION' ) ) {
	define( 'WP_SMUSH_VERSION', '2.8.1' );
}
// Used to define body class.
if ( ! defined( 'WP_SHARED_UI_VERSION' ) ) {
	define( 'WP_SHARED_UI_VERSION', 'sui-2-2-7' );
}
if ( ! defined( 'WP_SMUSH_BASENAME' ) ) {
	define( 'WP_SMUSH_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'WP_SMUSH_API' ) ) {
	define( 'WP_SMUSH_API', 'https://smushpro.wpmudev.org/1.0/' );
}
if ( ! defined( 'WP_SMUSH_UA' ) ) {
	define( 'WP_SMUSH_UA', 'WP Smush/' . WP_SMUSH_VERSION . '; ' . network_home_url() );
}
if ( ! defined( 'WP_SMUSH_DIR' ) ) {
	define( 'WP_SMUSH_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WP_SMUSH_URL' ) ) {
	define( 'WP_SMUSH_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WP_SMUSH_MAX_BYTES' ) ) {
	define( 'WP_SMUSH_MAX_BYTES', 1000000 );
}
if ( ! defined( 'WP_SMUSH_PREMIUM_MAX_BYTES' ) ) {
	define( 'WP_SMUSH_PREMIUM_MAX_BYTES', 32000000 );
}
if ( ! defined( 'WP_SMUSH_PREFIX' ) ) {
	define( 'WP_SMUSH_PREFIX', 'wp-smush-' );
}
if ( ! defined( 'WP_SMUSH_TIMEOUT' ) ) {
	define( 'WP_SMUSH_TIMEOUT', apply_filters( 'WP_SMUSH_API_TIMEOUT', 150 ) );
}

/**
 * To support Smushing on staging sites like SiteGround staging where staging site urls are different
 * but redirects to main site url. Remove the protocols and www, and get the domain name.*
 * If Set to false, WP Smush switch backs to the Old Sync Optimisation.
 */
$site_url = str_replace( array( 'http://', 'https://', 'www.' ), '', site_url() );
if ( ! defined( 'WP_SMUSH_ASYNC' ) && ! empty( $_SERVER['SERVER_NAME'] ) && ( 0 !== strpos( $site_url, $_SERVER['SERVER_NAME'] ) ) ) { // Input var ok.
	define( 'WP_SMUSH_ASYNC', false );
} elseif ( ! defined( 'WP_SMUSH_ASYNC' ) ) {
	define( 'WP_SMUSH_ASYNC', true );
}

add_action( 'admin_init', 'deactivate_smush_org' );
if ( ! function_exists( 'deactivate_smush_org' ) ) {
	/**
	 * Deactivate the .org version, if pro version is active.
	 */
	function deactivate_smush_org() {
		if ( is_plugin_active( 'wp-smush-pro/wp-smush.php' ) && is_plugin_active( 'wp-smushit/wp-smush.php' ) ) {
			deactivate_plugins( 'wp-smushit/wp-smush.php' );
			// Store in database, in order to show a notice on page load.
			update_site_option( 'smush_deactivated', 1 );
		}
	}
}

// Include core class.
if ( ! class_exists( 'WP_Smush' ) ) {
	/* @noinspection PhpIncludeInspection */
	require_once plugin_dir_path( __FILE__ ) . 'lib/class-wp-smush.php';
}

global $wp_smush;
$wp_smush = WP_Smush::get_instance();

if ( ! function_exists( 'wp_smush_rating_message' ) ) {
	/**
	 * Filters the rating message, include stats if greater than 1Mb
	 *
	 * @param string $message  Message text.
	 *
	 * @return string
	 */
	function wp_smush_rating_message( $message ) {
		/* @var WpSmushitAdmin $wpsmushit_admin */
		global $wpsmushit_admin;

		if ( empty( $wpsmushit_admin->stats ) ) {
			$wpsmushit_admin->setup_global_stats();
		}

		$savings    = $wpsmushit_admin->stats;
		$show_stats = false;

		// If there is any saving, greater than 1Mb, show stats.
		if ( ! empty( $savings ) && ! empty( $savings['bytes'] ) && $savings['bytes'] > 1048576 ) {
			$show_stats = true;
		}

		$message = "Hey %s, you've been using %s for a while now, and we hope you're happy with it.";

		// Conditionally Show stats in rating message.
		if ( $show_stats ) {
			$message .= sprintf( " You've smushed <strong>%s</strong> from %d images already, improving the speed and SEO ranking of this site!", $savings['human'], $savings['total_images'] );
		}
		$message .= " We've spent countless hours developing this free plugin for you, and we would really appreciate it if you dropped us a quick rating!";

		return $message;
	}
}

if ( ! function_exists( 'wp_smush_email_message' ) ) {
	/**
	 * NewsLetter
	 *
	 * @param string $message  Message text.
	 *
	 * @return string
	 */
	function wp_smush_email_message( $message ) {
		$message = "You're awesome for installing %s! Site speed isn't all image optimization though, so we've collected all the best speed resources we know in a single email - just for users of Smush!";

		return $message;
	}
}

if ( ! function_exists( 'get_plugin_dir' ) ) {
	/**
	 * Returns the dir path for the plugin
	 *
	 * @return string
	 */
	function get_plugin_dir() {
		$dir_path = plugin_dir_path( __FILE__ );

		return $dir_path;
	}
}

if ( is_admin() ) {
	$dir_path = get_plugin_dir();

	// Only for wordpress.org members.
	if ( strpos( $dir_path, 'wp-smushit' ) !== false ) {
		/* @noinspection PhpIncludeInspection */
		require_once( WP_SMUSH_DIR . 'extras/free-dashboard/module.php' );

		// Register the current plugin.
		do_action(
			'wdev-register-plugin',
			/* 1             Plugin ID */
			plugin_basename( __FILE__ ),
			/* 2          Plugin Title */
			'WP Smush',
			/* 3 https://wordpress.org */
			'/plugins/wp-smushit/',
			/* 4      Email Button CTA */
			__( 'Get Fast', 'wp-smushit' ),
			/* 5  getdrip Plugin param */
			'Smush'
		);

		// The rating message contains 2 variables: user-name, plugin-name.
		add_filter(
			'wdev-rating-message-' . plugin_basename( __FILE__ ),
			'wp_smush_rating_message'
		);

		// The email message contains 1 variable: plugin-name.
		add_filter(
			'wdev-email-message-' . plugin_basename( __FILE__ ),
			'wp_smush_email_message'
		);
	} elseif ( strpos( $dir_path, 'wp-smush-pro' ) !== false && file_exists( WP_SMUSH_DIR . 'extras/dash-notice/wpmudev-dash-notification.php' ) ) {
		// Only for WPMU DEV Members.
		/* @noinspection PhpIncludeInspection */
		require_once( WP_SMUSH_DIR . 'extras/dash-notice/wpmudev-dash-notification.php' );

		// Register items for the dashboard plugin.
		global $wpmudev_notices;
		$wpmudev_notices[] = array(
			'id'      => 912164,
			'name'    => 'WP Smush Pro',
			'screens' => array(
				'upload',
				'toplevel_page_smush',
				'toplevel_page_smush-network',
			),
		);
	} // End if().
} // End if().

// Show the required notice.
add_action( 'network_admin_notices', 'smush_deactivated' );
add_action( 'admin_notices', 'smush_deactivated' );

if ( ! function_exists( 'smush_deactivated' ) ) {
	/**
	 * Display a admin Notice about plugin deactivation.
	 */
	function smush_deactivated() {
		// Display only in backend for administrators.
		if ( is_admin() && is_super_admin() && get_site_option( 'smush_deactivated' ) ) { ?>
			<div class="updated">
				<p><?php esc_html_e( 'Smush Free was deactivated. You have Smush Pro active!', 'wp-smushit' ); ?></p>
			</div> <?php
			delete_site_option( 'smush_deactivated' );
		}
	}
}

if ( ! function_exists( 'smush_sanitize_hex_color' ) ) {
	/**
	 * Sanitizes a hex color.
	 *
	 * @param string $color  HEX color code.
	 *
	 * @return string Returns either '', a 3 or 6 digit hex color (with #), or nothing
	 */
	function smush_sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return false;
	}
}

if ( ! function_exists( 'smush_sanitize_hex_color_no_hash' ) ) {
	/**
	 * Sanitizes a hex color without hash
	 *
	 * @param string $color  HEX color code with hash.
	 *
	 * @return string Returns either '', a 3 or 6 digit hex color (with #), or nothing
	 */
	function smush_sanitize_hex_color_no_hash( $color ) {
		$color = ltrim( $color, '#' );

		if ( '' === $color ) {
			return '';
		}

		return smush_sanitize_hex_color( '#' . $color ) ? $color : null;
	}
}

add_action( 'plugins_loaded', 'smush_i18n' );
if ( ! function_exists( 'smush_i18n' ) ) {
	/**
	 * Load translation files.
	 */
	function smush_i18n() {
		$path = path_join( dirname( plugin_basename( __FILE__ ) ), 'languages/' );
		load_plugin_textdomain( 'wp-smushit', false, $path );
	}
}

// Add Share UI Class.
add_filter( 'admin_body_class', 'smush_body_classes', 99 );

if ( ! function_exists( 'smush_body_classes' ) ) {
	/**
	 * Add Share UI Class.
	 *
	 * @param string $classes  Classes string.
	 *
	 * @return string
	 */
	function smush_body_classes( $classes ) {
		global $wpsmushit_admin;

		// Exit if function doesn't exists.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $classes;
		}

		$current_screen = get_current_screen();

		// If not on plugin page.
		if ( ! in_array( $current_screen->id, $wpsmushit_admin->plugin_pages, true ) ) {
			return $classes;
		}

		// Remove old wpmud class from body of smush page to avoid style conflict.
		$classes = str_replace( 'wpmud ', '', $classes );

		$classes .= ' ' . WP_SHARED_UI_VERSION;

		return $classes;
	}
}

register_activation_hook( 'lib/class-wp-smush-installer.php', array( 'WP_Smush_Installer', 'smush_activated' ) );