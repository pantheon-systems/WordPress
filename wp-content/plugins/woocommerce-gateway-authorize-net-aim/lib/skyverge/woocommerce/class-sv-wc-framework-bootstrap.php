<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Plugin/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) :


/**
 * # SkyVerge WooCommerce Plugin Framework Bootstrap
 *
 * The purpose of this class is to find and load the highest versioned
 * framework of the activated framework plugins, and then initialize any
 * compatible framework plugins.
 *
 * @since 2.0.0
 */
class SV_WC_Framework_Bootstrap {


	/** @var SV_WC_Framework_Bootstrap The single instance of the class */
	protected static $instance = null;

	/** @var array registered framework plugins */
	protected $registered_plugins = array();

	/** @var array registered and active framework plugins */
	protected $active_plugins = array();

	/** @var array of plugins that need to be updated due to an outdated framework */
	protected $incompatible_framework_plugins = array();

	/** @var array of plugins that require a newer version of WC */
	protected $incompatible_wc_version_plugins = array();

	/** @var array of plugins that require a newer version of WP */
	protected $incompatible_wp_version_plugins = array();


	/**
	 * Hidden constructor
	 *
	 * @since 2.0.0
	 */
	private function __construct() {

		// load framework plugins once all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'load_framework_plugins' ) );

		// deactivate backwards-incompatible framework plugins if the admin isn't ready to upgrade old plugins
		add_action( 'admin_init', array( $this, 'maybe_deactivate_framework_plugins' ) );
	}


	/**
	 * Instantiate the class singleton
	 *
	 * @since 2.0.0
	 * @return SV_WC_Framework_Bootstrap singleton instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Register a frameworked plugin
	 *
	 * @since 2.0.0
	 * @param string $version the framework version
	 * @param string $plugin_name the plugin name
	 * @param string $path the plugin path
	 * @param callable $callback function to initialize the plugin
	 * @param array $args optional plugin arguments.  Possible arguments: 'is_payment_gateway', 'backwards_compatible'
	 */
	public function register_plugin( $version, $plugin_name, $path, $callback, $args = array() ) {
		$this->registered_plugins[] = array( 'version' => $version, 'plugin_name' => $plugin_name, 'path' => $path, 'callback' => $callback, 'args' => $args );
	}


	/**
	 * Loads all registered framework plugins, first initializing the plugin
	 * framework by loading the highest versioned one.
	 *
	 * @since 2.0.0
	 */
	public function load_framework_plugins() {

		// first sort the registered plugins by framework version
		usort( $this->registered_plugins, array( $this, 'compare_frameworks' ) );

		$loaded_framework = null;

		foreach ( $this->registered_plugins as $plugin ) {

			// load the first found (highest versioned) plugin framework class
			if ( ! class_exists( 'SV_WC_Plugin' ) ) {
				require_once( $this->get_plugin_path( $plugin['path'] ) . '/lib/skyverge/woocommerce/class-sv-wc-plugin.php' );
				$loaded_framework = $plugin;

				// the loaded plugin is always considered active (for the
				// purposes of handling conflicts between this and other plugins
				// with incompatible framework versions)
				$this->active_plugins[] = $plugin;
			}

			// if the loaded version of the framework has a backwards compatibility requirement
			//  which is not met by the current plugin add an admin notice and move on without
			//  loading the plugin
			if ( ! empty( $loaded_framework['args']['backwards_compatible'] ) && version_compare( $loaded_framework['args']['backwards_compatible'], $plugin['version'], '>' ) ) {

				$this->incompatible_framework_plugins[] = $plugin;

				// next plugin
				continue;
			}

			// if a plugin defines a minimum WC version which is not met, render a notice and skip loading the plugin
			if ( ! empty( $plugin['args']['minimum_wc_version'] ) && version_compare( $this->get_wc_version(), $plugin['args']['minimum_wc_version'], '<' ) ) {

				$this->incompatible_wc_version_plugins[] = $plugin;

				// next plugin
				continue;
			}

			// if a plugin defines a minimum WP version which is not met, render a notice and skip loading the plugin
			if ( ! empty( $plugin['args']['minimum_wp_version'] ) && version_compare( get_bloginfo( 'version' ), $plugin['args']['minimum_wp_version'], '<' ) ) {

				$this->incompatible_wp_version_plugins[] = $plugin;

				// next plugin
				continue;
			}

			// add this plugin to the active list
			if ( ! in_array( $plugin, $this->active_plugins ) ) {
				$this->active_plugins[] = $plugin;
			}

			// load the first found (highest versioned) payment gateway framework class, as needed
			if ( isset( $plugin['args']['is_payment_gateway'] ) && ! class_exists( 'SV_WC_Payment_Gateway' ) ) {
				require_once( $this->get_plugin_path( $plugin['path'] ) . '/lib/skyverge/woocommerce/payment-gateway/class-sv-wc-payment-gateway-plugin.php' );
			}

			// initialize the plugin
			$plugin['callback']();
		}

		// render update notices
		if ( ( $this->incompatible_framework_plugins || $this->incompatible_wc_version_plugins || $this->incompatible_wp_version_plugins ) && is_admin() && ! defined( 'DOING_AJAX' ) && ! has_action( 'admin_notices', array( $this, 'render_update_notices' ) ) ) {

			add_action( 'admin_notices', array( $this, 'render_update_notices' ) );
		}

		/**
		 * WC Plugin Framework Plugins Loaded Action.
		 *
		 * Fired when all frameworked plugins are loaded. Frameworked plugins can
		 * hook into this action rather than `plugins_loaded`/`woocommerce_loaded`
		 * as needed.
		 *
		 * @since 2.0.0
		 */
		do_action( 'sv_wc_framework_plugins_loaded' );
	}


	/** Admin methods ******************************************************/


	/**
	 * Deactivate backwards-incompatible framework plugins, which will allow
	 * plugins with an older version of the framework to be active. Useful when
	 * the admin isn't ready to upgrade older plugins yet needs them to still
	 * function (e.g. a payment gateway)
	 *
	 * @since 4.0.0
	 */
	public function maybe_deactivate_framework_plugins() {

		if ( isset( $_GET['sv_wc_framework_deactivate_newer'] ) ) {
			if ( 'yes' == $_GET['sv_wc_framework_deactivate_newer'] ) {

				// don't want to just deactivate all active plugins willy-nilly if there's no incompatible plugins
				if ( count( $this->incompatible_framework_plugins ) == 0 ) {
					return;
				}

				$plugins = array();

				foreach ( $this->active_plugins as $plugin ) {
					$plugins[] = plugin_basename( $plugin['path'] );
				}

				// deactivate all "active" frameworked plugins, these will be the newest, backwards-incompatible ones
				deactivate_plugins( $plugins );

				// redirect to the inactive plugin admin page, with a message indicating the number of plugins deactivated
				wp_redirect( admin_url( 'plugins.php?plugin_status=inactive&sv_wc_framework_deactivate_newer=' . count( $plugins ) ) );
				exit;
			} else {
				// we're on the inactive plugin page and we've deactivated one or more plugins
				add_action( 'admin_notices', array( $this, 'render_deactivation_notice' ) );
			}
		}
	}


	/**
	 * Render a notice with a count of the backwards incompatible frameworked
	 * plugins that were deactivated
	 *
	 * @since 4.0.0
	 */
	public function render_deactivation_notice() {
		echo '<div class="updated"><p>';
		echo $_GET['sv_wc_framework_deactivate_newer'] > 1 ?
			sprintf( 'Deactivated %d plugins', $_GET['sv_wc_framework_deactivate_newer'] ) :
			'Deactivated one plugin';
		echo '</p></div>';
	}


	/**
	 * Render a notice to update any plugins with incompatible framework
	 * versions, or incompatiblities with the current WooCommerce or WordPress
	 * versions
	 *
	 * @since 2.0.0
	 */
	public function render_update_notices() {

		// must update plugin notice
		if ( ! empty( $this->incompatible_framework_plugins ) ) {

			$plugin_count = count( $this->incompatible_framework_plugins );

			echo '<div class="error">';

				// describe the problem
				echo '<p>';
					echo esc_html( _n( 'The following plugin is disabled because it is out of date and incompatible with newer plugins on your site:', 'The following plugins are disabled because they are out of date and incompatible with newer plugins on your site:', $plugin_count, 'woocommerce-plugin-framework' ) );
				echo '</p>';

				// add a incompatible plugin list
				echo '<ul>';
					foreach ( $this->incompatible_framework_plugins as $plugin ) {
						printf( '<li>%s</li>', $plugin['plugin_name'] );
					}
				echo '</ul>';

				// describe the way to fix it
				echo '<p>';
					printf(
						/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <em> tag, %4$s - </em> tag, %5$s - <a> tag, %6$s - </a> tag, %7$s - <a> tag, %8$s - </a> tag */
						esc_html( _n( 'To resolve this, please %1$supdate%2$s (recommended) %3$sor%4$s %5$sdeactivate%6$s the above plugin, or %7$sdeactivate the following%8$s:', 'To resolve this, please %1$supdate%2$s (recommended) %3$sor%4$s %5$sdeactivate%6$s the above plugins, or %7$sdeactivate the following%8$s:', $plugin_count, 'woocommerce-plugin-framework' ) ),
						'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
						'<em>', '</em>',
						'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
						'<a href="' . esc_url( admin_url( 'plugins.php?sv_wc_framework_deactivate_newer=yes' ) ) . '">', '</a>'
					);
				echo '</p>';

				// add the list of active plugins
				echo '<ul>';
					foreach ( $this->active_plugins as $plugin ) {
						printf( '<li>%s</li>', $plugin['plugin_name'] );
					}
				echo '</ul>';

			echo '</div>';
		}

		// must update WC notice
		if ( ! empty( $this->incompatible_wc_version_plugins ) ) {

			$minimum_versions = array();

			printf( '<div class="error"><p>%s</p><ul>', count( $this->incompatible_wc_version_plugins ) > 1 ? esc_html__( 'The following plugins are inactive because they require a newer version of WooCommerce:', 'woocommerce-plugin-framework' ) : esc_html__( 'The following plugin is inactive because it requires a newer version of WooCommerce:', 'woocommerce-plugin-framework' ) );

			foreach ( $this->incompatible_wc_version_plugins as $plugin ) {

				$minimum_versions[] = $plugin['args']['minimum_wc_version'];

				/* translators: Placeholders: %1$s - plugin name, %2$s - WooCommerce version number */
				echo '<li>' . sprintf( esc_html__( '%1$s requires WooCommerce %2$s or newer', 'woocommerce-plugin-framework' ), $plugin['plugin_name'], $plugin['args']['minimum_wc_version'] ) . '</li>';
			}

			echo '</ul><p>';

			// sort the min WC versions from lowest to highest
			// below we'll get the highest to build the download link
			usort( $minimum_versions, 'version_compare' );

			printf(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <a> tag, %4$s - </a> tag */
				esc_html__( 'Please %1$supdate WooCommerce%2$s to the latest version, or %3$sdownload the minimum required version &raquo;%4$s', 'woocommerce-plugin-framework' ),
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . end( $minimum_versions ) . '.zip' ) . '">', '</a>'
			);

			echo '</p></div>';
		}

		// must update WP notice
		if ( ! empty( $this->incompatible_wp_version_plugins ) ) {

			printf( '<div class="error"><p>%s</p><ul>', count( $this->incompatible_wp_version_plugins ) > 1 ? 'The following plugins are inactive because they require a newer version of WordPress:' : 'The following plugin is inactive because it requires a newer version of WordPress:' );

			foreach ( $this->incompatible_wp_version_plugins as $plugin ) {
				printf( '<li>%s requires WordPress %s or newer</li>', $plugin['plugin_name'], $plugin['args']['minimum_wp_version'] );
			}

			echo '</ul><p>Please <a href="' . admin_url( 'update-core.php' ) . '">update WordPress&nbsp;&raquo;</a></p></div>';
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Is the WooCommerce plugin installed and active? This method is handy for
	 * frameworked plugins that are listed on wordpress.org and thus don't have
	 * access to the Woo Helper functions bundled with WooThemes-listed plugins.
	 *
	 * Notice: For now you can't rely on this method being available, since the
	 * bootstrap class is the only piece of the framework which is loaded
	 * simply according to the lexical order of plugin directories. Therefore
	 * to use, you should first check that this method exists, or if you really
	 * need to check for WooCommerce being active, define your own method.
	 *
	 * @since 4.0.0
	 * @return boolean true if the WooCommerce plugin is installed and active
	 */
	public static function is_woocommerce_active() {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}


	/**
	 * Compare the two framework versions.  Returns -1 if $a is less than $b, 0 if
	 * they're equal, and 1 if $a is greater than $b
	 *
	 * @since 2.0.0
	 * @param array $a first registered plugin to compare
	 * @param array $b second registered plugin to compare
	 * @return int -1 if $a is less than $b, 0 if they're equal, and 1 if $a is greater than $b
	 */
	public function compare_frameworks( $a, $b ) {
		// compare versions without the operator argument, so we get a -1, 0 or 1 result
		return version_compare( $b['version'], $a['version'] );
	}


	/**
	 * Returns the plugin path for the given $file
	 *
	 * @since 2.0.0
	 * @param string $file the file
	 * @return string plugin path
	 */
	public function get_plugin_path( $file ) {
		return untrailingslashit( plugin_dir_path( $file ) );
	}


	/**
	 * Returns the WooCommerce version number, backwards compatible to
	 * WC 1.5
	 *
	 * @since 3.0.0
	 * @return null|string
	 */
	protected function get_wc_version() {

		if ( defined( 'WC_VERSION' )          && WC_VERSION )          return WC_VERSION;
		if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) return WOOCOMMERCE_VERSION;

		return null;
	}

}


// instantiate the class
SV_WC_Framework_Bootstrap::instance();

endif;
