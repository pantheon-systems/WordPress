<?php
/**
 * Class Google\Site_Kit_Dev_Settings\Plugin
 *
 * @package   Google\Site_Kit_Dev_Settings
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 */

namespace Google\Site_Kit_Dev_Settings;

/**
 * Main class for the plugin.
 *
 * @since 0.1.0
 */
class Plugin {

	/**
	 * Absolute path to the plugin main file.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $main_file;

	/**
	 * The setting instance.
	 *
	 * @since 0.1.0
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Main instance of the plugin.
	 *
	 * @since 0.1.0
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Sets the plugin main file.
	 *
	 * @since 0.1.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( $main_file ) {
		$this->main_file = $main_file;

		$this->setting = new Setting();
	}

	/**
	 * Registers the plugin with WordPress.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		$this->setting->register();

		$hooks = new Hooks( $this->setting );
		$hooks->register();

		$updater = new Updater();
		$updater->register();

		add_action(
			'admin_menu',
			function() {
				$admin_screen = new Admin\Settings_Screen( $this->setting );
				$admin_screen->register_menu();
			},
			20
		);
	}

	/**
	 * Gets the plugin basename, which consists of the plugin directory name and main file name.
	 *
	 * @since 0.1.0
	 *
	 * @return string Plugin basename.
	 */
	public function basename() {
		return plugin_basename( $this->main_file );
	}

	/**
	 * Gets the absolute path for a path relative to the plugin directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Absolute path.
	 */
	public function path( $relative_path = '/' ) {
		return plugin_dir_path( $this->main_file ) . ltrim( $relative_path, '/' );
	}

	/**
	 * Gets the full URL for a path relative to the plugin directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Full URL.
	 */
	public function url( $relative_path = '/' ) {
		return plugin_dir_url( $this->main_file ) . ltrim( $relative_path, '/' );
	}

	/**
	 * Gets the URL to the plugin's settings screen.
	 *
	 * @since 0.1.0
	 *
	 * @return string Settings screen URL.
	 */
	public function settings_screen_url() {
		$parent_page = Admin\Settings_Screen::PARENT_SLUG;
		if ( '.php' !== substr( $parent_page, -4 ) ) {
			$parent_page = 'admin.php';
		}
		return add_query_arg( 'page', Admin\Settings_Screen::SLUG, admin_url( $parent_page ) );
	}

	/**
	 * Retrieves the main instance of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return Plugin Plugin main instance.
	 */
	public static function instance() {
		return static::$instance;
	}

	/**
	 * Loads the plugin main instance and initializes it.
	 *
	 * @since 0.1.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @return bool True if the plugin main instance could be loaded, false otherwise.
	 */
	public static function load( $main_file ) {
		if ( null !== static::$instance ) {
			return false;
		}

		static::$instance = new static( $main_file );
		static::$instance->register();

		return true;
	}
}
