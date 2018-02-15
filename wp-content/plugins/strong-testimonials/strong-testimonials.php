<?php
/**
 * Plugin Name: Strong Testimonials
 * Plugin URI: https://strongplugins.com/plugins/strong-testimonials/
 * Description: A full-featured plugin that works right out of the box for beginners and offers advanced features for pros.
 * Author: Chris Dillon
 * Version: 2.30
 *
 * Author URI: https://strongplugins.com/
 * Text Domain: strong-testimonials
 * Domain Path: /languages
 * Requires: 3.7 or higher
 * License: GPLv2 or later
 *
 * Copyright 2014-2018 Chris Dillon chris@strongplugins.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPMTST_VERSION', '2.30' );
define( 'WPMTST_PLUGIN', plugin_basename( __FILE__ ) ); // strong-testimonials/strong-testimonials.php
define( 'WPMTST', dirname( WPMTST_PLUGIN ) );           // strong-testimonials
define( 'STRONGPLUGINS_STORE_URL', 'https://strongplugins.com' );


if ( ! class_exists( 'Strong_Testimonials' ) ) :

/**
 * Main plugin class.
 *
 * @property  Strong_Testimonials_Shortcodes shortcodes
 * @property  Strong_Testimonials_Render render
 * @property  Strong_Mail mail
 * @property  Strong_Templates templates
 * @property  Strong_Testimonials_Form form
 * @since 1.15.0
 */
final class Strong_Testimonials {

	private static $instance;

	private $db_version = '1.0';

	public $plugin_data;

	/**
	 * @var Strong_Testimonials_Shortcodes
	 */
	public $shortcodes;

	/**
	 * @var Strong_Testimonials_Render
	 */
	public $render;

	/**
	 * @var Strong_Mail
	 */
	public $mail;

	/**
	 * @var Strong_Templates
	 */
	public $templates;

	/**
	 * @var Strong_Testimonials_Form
	 */
	public $form;

	/**
	 * A singleton instance.
	 *
	 * Used for preprocessing shortcodes and widgets to properly enqueue styles and scripts
	 * (1) to improve overall plugin flexibility,
	 * (2) to improve compatibility with page builder plugins, and
	 * (3) to maintain conditional loading best practices.
	 *
	 * Also used to store testimonial form data during Post-Redirect-Get.
	 *
	 * @return Strong_Testimonials  Strong_Testimonials object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Strong_Testimonials ) ) {
			self::$instance = new Strong_Testimonials;
			self::$instance->setup_constants();
			self::$instance->includes();

			add_action( 'init', array( self::$instance, 'init' ) );

			self::$instance->add_actions();
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.21.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'strong-testimonials' ), '1.21' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.21.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'strong-testimonials' ), '1.21' );
	}

	/**
	 * Plugin activation
	 */
	static function plugin_activation() {
		wpmtst_update_tables();
		wpmtst_register_cpt();
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	static function plugin_deactivation() {
		flush_rewrite_rules();

		/**
		 * Unset stored version number to allow rollback and beta testing.
		 *
		 * @since 2.28.0
		 */
		delete_option( 'wpmtst_plugin_version' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {
		defined( 'WPMTST_DIR' ) || define( 'WPMTST_DIR', plugin_dir_path( __FILE__ ) );
		defined( 'WPMTST_URL' ) || define( 'WPMTST_URL', plugin_dir_url( __FILE__ ) );

		defined( 'WPMTST_INC' ) || define( 'WPMTST_INC', WPMTST_DIR . 'includes/' );

		defined( 'WPMTST_ADMIN' ) || define( 'WPMTST_ADMIN', WPMTST_DIR . 'admin/' );
		defined( 'WPMTST_ADMIN_URL' ) || define( 'WPMTST_ADMIN_URL', WPMTST_URL . 'admin/' );

		defined( 'WPMTST_PUBLIC' ) || define( 'WPMTST_PUBLIC', WPMTST_DIR . 'public/' );
		defined( 'WPMTST_PUBLIC_URL' ) || define( 'WPMTST_PUBLIC_URL', WPMTST_URL . 'public/' );

		defined( 'WPMTST_DEF_TPL' ) || define( 'WPMTST_DEF_TPL', WPMTST_DIR . 'templates/default/' );
		defined( 'WPMTST_DEF_TPL_URI' ) || define( 'WPMTST_DEF_TPL_URI', WPMTST_URL . 'templates/default/' );

		defined( 'WPMTST_TPL' ) || define( 'WPMTST_TPL', WPMTST_DIR . 'templates' );
		defined( 'WPMTST_TPL_URI' ) || define( 'WPMTST_TPL_URI', WPMTST_URL . 'templates' );
	}

	/**
	 * Instantiate our classes.
	 */
	public function init() {
		$this->shortcodes = new Strong_Testimonials_Shortcodes();
		$this->render     = new Strong_Testimonials_Render();
		$this->mail       = new Strong_Mail();
		$this->templates  = new Strong_Templates();
		$this->form       = new Strong_Testimonials_Form();
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.21.0
	 * @return void
	 */
	private function includes() {
		require_once WPMTST_INC . 'class-strong-testimonials-shortcodes.php';
		require_once WPMTST_INC . 'class-strong-testimonials-render.php';
		require_once WPMTST_INC . 'class-strong-view.php';
		require_once WPMTST_INC . 'class-strong-view-display.php';
		require_once WPMTST_INC . 'class-strong-view-slideshow.php';
		require_once WPMTST_INC . 'class-strong-view-form.php';

		require_once WPMTST_INC . 'class-strong-templates.php';
		require_once WPMTST_INC . 'class-strong-mail.php';
		require_once WPMTST_INC . 'class-strong-form.php';
		require_once WPMTST_INC . 'class-walker-strong-category-checklist-front.php';

		require_once WPMTST_INC . 'deprecated.php';
		require_once WPMTST_INC . 'filters.php';
		require_once WPMTST_INC . 'functions.php';
		require_once WPMTST_INC . 'functions-content.php';
		require_once WPMTST_INC . 'functions-rating.php';
		require_once WPMTST_INC . 'functions-image.php';
		require_once WPMTST_INC . 'functions-template.php';
		require_once WPMTST_INC . 'functions-template-form.php';
		require_once WPMTST_INC . 'post-types.php';
		require_once WPMTST_INC . 'retro.php';
		require_once WPMTST_INC . 'scripts.php';
		require_once WPMTST_INC . 'widget2.php';

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

			require_once WPMTST_ADMIN . 'menu/class-strong-testimonials-menu.php';
			require_once WPMTST_ADMIN . 'menu/class-strong-testimonials-menu-fields.php';
			require_once WPMTST_ADMIN . 'menu/class-strong-testimonials-menu-settings.php';
			require_once WPMTST_ADMIN . 'menu/class-strong-testimonials-menu-views.php';

			require_once WPMTST_ADMIN . 'settings/class-strong-testimonials-settings.php';
			require_once WPMTST_ADMIN . 'settings/class-strong-testimonials-settings-general.php';
			require_once WPMTST_ADMIN . 'settings/class-strong-testimonials-settings-form.php';
			require_once WPMTST_ADMIN . 'settings/class-strong-testimonials-settings-compat.php';
			require_once WPMTST_ADMIN . 'settings/class-strong-testimonials-settings-licenses.php';

			require_once WPMTST_ADMIN . 'about/class-strong-testimonials-about.php';

			require_once WPMTST_ADMIN . 'class-strong-testimonials-defaults.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-list-table.php';
			require_once WPMTST_ADMIN . 'class-strong-views-list-table.php';
			require_once WPMTST_ADMIN . 'class-walker-strong-category-checklist.php';
			require_once WPMTST_ADMIN . 'class-walker-strong-form-category-checklist.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-help.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-admin-scripts.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-admin-list.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-admin-category-list.php';
			require_once WPMTST_ADMIN . 'class-strong-testimonials-post-editor.php';

			require_once WPMTST_ADMIN . 'admin.php';
			require_once WPMTST_ADMIN . 'admin-notices.php';
			require_once WPMTST_ADMIN . 'compat.php';
			require_once WPMTST_ADMIN . 'custom-fields.php';
			require_once WPMTST_ADMIN . 'custom-fields-ajax.php';
			require_once WPMTST_ADMIN . 'form-preview.php';
			require_once WPMTST_ADMIN . 'views.php';
			require_once WPMTST_ADMIN . 'views-ajax.php';
			require_once WPMTST_ADMIN . 'view-list-order.php';
			require_once WPMTST_ADMIN . 'views-validate.php';

			/**
			 * Add-on plugin updater.
			 *
			 * @since 2.1
			 */
			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				include WPMTST_ADMIN . 'edd/EDD_SL_Plugin_Updater.php';
			}
			include WPMTST_ADMIN . 'edd/Strong_Plugin_Updater.php';
		}
	}

	/**
	 * Text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'strong-testimonials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Action and filters.
	 */
	private function add_actions() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		/**
		 * Plugin setup.
		 */
		add_action( 'init', array( $this, 'l10n_check' ) );
		add_action( 'init', array( $this, 'reorder_check' ) );
		add_action( 'init', array( $this, 'font_check' ) );

		/**
		 * Theme support for thumbnails.
		 */
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );

		/**
		 * Add image size for widget.
		 */
		add_action( 'after_setup_theme', array( $this, 'add_image_size' ) );
	}

	/**
	 * Add theme support for this custom post type only.
	 *
	 * @since 1.4.0
	 * @since 1.19.1 Appends our testimonial post type to the existing array.
	 * @since 2.26.5 Simply using add_theme_support(). Let the chips fall where they may.
	 */
	public function add_theme_support() {
		/**
		 * This will fail if the theme uses add_theme_support incorrectly;
		 * e.g. add_theme_support( 'post-thumbnails', 'post' );
		 * which WordPress does not catch.
		 *
		 * The plugin attempted to handle this in versions 1.19.1 - 2.26.4
		 * but now it lets the condition occur so the underlying problem
		 * will surface and can be fixed.
		 */
		add_theme_support( 'post-thumbnails', array( 'wpm-testimonial' ) );
	}

	/**
	 * Add widget thumbnail size.
	 *
	 * @since 1.21.0
	 */
	public function add_image_size() {
		// name, width, height, crop = false
		add_image_size( 'widget-thumbnail', 75, 75, true );
	}

	/**
	 * Load specific files for translation plugins.
	 */
	public function l10n_check() {
		// WPML
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			require_once WPMTST_INC . 'l10n-wpml.php';
		}

		// Polylang
		if ( defined( 'POLYLANG_VERSION' ) ) {
			require_once WPMTST_INC . 'l10n-polylang.php';
		}

		// WP Globus
		if ( defined( 'WPGLOBUS_VERSION' ) ) {
			// Translate
			remove_filter( 'wpmtst_l10n', 'wpmtst_l10n_default' );
			add_filter( 'wpmtst_the_content', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
			add_filter( 'wpmtst_get_the_excerpt', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
		}
	}

	/**
	 * Load reorder class if enabled.
	 */
	public function reorder_check() {
		$options = get_option( 'wpmtst_options' );
		if ( isset( $options['reorder'] ) && $options['reorder'] ) {
			require_once WPMTST_INC . 'class-strong-testimonials-order.php';
		}
	}

	/**
	 * Forgo Font Awesome.
	 */
	public function font_check() {
		$options = get_option( 'wpmtst_options' );
		if ( isset( $options['load_font_awesome'] ) && ! $options['load_font_awesome'] ) {
			add_filter( 'wpmtst_load_font_awesome', '__return_false' );
		}
	}

	/**
	 * Get att(s).
	 *
	 * @param null $keys
	 *
	 * @return array|bool
	 */
	public function atts( $keys = null ) {
		// return all
		if ( ! $keys ) {
			return $this->render->view_atts;
		}

		// return some
		if ( is_array( $keys ) ) {
			$found = array();
			foreach ( $keys as $key ) {
				if ( isset( $this->render->view_atts[ $key ] ) ) {
					$found[ $key ] = $this->render->view_atts[ $key ];
				}
			}

			return $found;
		}

		// return one
		if ( isset( $this->render->view_atts[ $keys ] ) ) {
			return $this->render->view_atts[ $keys ];
		}

		// return none
		return false;
	}

	/**
	 * Set atts.
	 *
	 * @param $atts
	 */
	public function set_atts( $atts ) {
		$this->render->set_atts ($atts );
	}

	/**
	 * Store current query.
	 *
	 * @param $query
	 */
	public function set_query( $query ) {
		$this->render->query = $query;
	}

	/**
	 * Return current query.
	 *
	 * @return mixed
	 */
	public function get_query() {
		return $this->render->query;
	}

	/**
	 * Get database tables version.
	 *
	 * @return string
	 */
	public function get_db_version() {
		return $this->db_version;
	}

	/**
	 * Set plugin data.
	 *
	 * @since 2.12.0
	 */
	public function set_plugin_data() {
		//$this->plugin_data = get_plugin_data( __FILE__, false );
		$this->plugin_data = array(
			'Version' => WPMTST_VERSION,
		);
	}

	/**
	 * Get plugin data.
	 *
	 * @since 2.12.0
	 *
	 * @return array
	 */
	public function get_plugin_data() {
		return $this->plugin_data;
	}

	/**
	 * Return plugin info.
	 *
	 * @deprecated
	 *
	 * @return array
	 */
	public function get_plugin_info() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			if ( file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/admin.php';
			}
		}

		return get_file_data( __FILE__, array( 'name' => 'Plugin Name', 'version' => 'Version' ) );
	}

}

endif; // class_exists check

register_activation_hook( __FILE__, array( 'Strong_Testimonials', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Strong_Testimonials', 'plugin_deactivation' ) );

function WPMST() {
	return Strong_Testimonials::instance();
}

// Get plugin running
WPMST();
