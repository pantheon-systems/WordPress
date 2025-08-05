<?php
/**
 * Base class for plugin compatibility fixes.
 *
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use function file_exists;
use function function_exists;
use function get_plugin_data;

use const WP_PLUGIN_DIR;

/**
 * Base class for plugin compatibility fixes.
 */
abstract class Base {

	/**
	 * The plugin's slug.
	 *
	 * @var string
	 */
	public static $plugin_slug;

	/**
	 * The plugin's name.
	 *
	 * @var string
	 */
	public static $plugin_name;

	/**
	 * Run fix on plugin activation flag.
	 *
	 * @var bool
	 */
	protected $run_on_plugin_activation = false;

	/**
	 * Run fix after plugin activation flag.
	 *
	 * @var bool
	 */
	protected $run_after_plugin_activation = false;

	/**
	 * Run fix on dashboard only flag.
	 *
	 * @var bool
	 */
	protected $run_fix_on_dashboard_only = false;

	/**
	 * Run fix on frontend only flag.
	 *
	 * @var bool
	 */
	protected $run_fix_on_frontend_only = false;

	/**
	 * Run fix everytime either frontend or dashboard.
	 *
	 * @var bool
	 */
	protected $run_fix_everytime = false;

	/**
	 * Base constructor registering deactivation hook and conditionally executing activate method.
	 *
	 * @param string $slug The plugin's basename.
	 * @see https://developer.wordpress.org/reference/functions/plugin_basename/
	 */
	public function __construct( $slug ) {
		static::$plugin_slug = $slug;
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$file = WP_PLUGIN_DIR . '/' . $slug;
		if ( ! file_exists( $file ) ) {
			static::$plugin_name = static::$plugin_slug;
		} else {
			$plugin_data = get_plugin_data( $file, false, false );
			static::$plugin_name = $plugin_data['Name'];
		}
		register_deactivation_hook( WP_PLUGIN_DIR . '/' . static::$plugin_slug, [ $this, 'deactivate' ] );
		if ( $this->is_plugin_active() ) {
			$this->activate();
		}
	}

	/**
	 * Check if the plugin is active by $plugin_slug parameter.
	 *
	 * @return bool
	 */
	protected function is_plugin_active() {
		return ( static::$plugin_slug && is_plugin_active( static::$plugin_slug ) );
	}

	/**
	 * Set up compatibility hooks and persists compatibility status.
	 *
	 * @return void
	 */
	protected function activate() {
		$plugin_methods = [];
		// Fix will run only one time on plugin activation.
		if ( $this->run_on_plugin_activation ) {
			$this->run_on_plugin_activation();
			$plugin_methods[] = 'run_on_plugin_activation';
		}

		// Fix will run only one time after plugin activation.
		if ( $this->run_after_plugin_activation ) {
			$this->add_action_after_plugin_activation();
			$plugin_methods[] = 'run_after_plugin_activation';
		}

		// Fix will run only on dashboard everytime.
		if ( $this->run_fix_on_dashboard_only && is_admin() ) {
			$this->run_fix_on_dashboard_only();
			$plugin_methods[] = 'run_fix_on_dashboard_only';
		}

		// Fix will run only on frontend everytime.
		if ( $this->run_fix_on_frontend_only && ! is_admin() ) {
			$this->run_fix_on_frontend_only();
			$plugin_methods[] = 'run_fix_on_frontend_only';
		}

		// Fix will run everytime either frontend or dashboard.
		if ( $this->run_fix_everytime ) {
			$this->run_fix_everytime();
			$plugin_methods[] = 'run_fix_everytime';
		}

		$this->persist_data( $plugin_methods );
	}

	/**
	 * Trigger compatibility layer on plugin activation.
	 *
	 * @return void
	 */
	protected function run_on_plugin_activation() {
		$this->apply_fix();
	}

	/**
	 * Apply the compatibility fix.
	 *
	 * @return mixed
	 */
	abstract public function apply_fix();

	/**
	 * Register after plugin activation hooks.
	 *
	 * @return void
	 */
	protected function add_action_after_plugin_activation() {
		add_action( 'activated_plugin', function ( $plugin ) {
			if ( static::$plugin_slug === $plugin ) {
				// This code will run after the plugin has been activated.
				$this->run_after_plugin_activation();
			}
		}, PHP_INT_MAX );
	}

	/**
	 * Trigger compatibility layer after plugin activation.
	 *
	 * @return void
	 */
	protected function run_after_plugin_activation() {
		$this->apply_fix();
	}

	/**
	 * Trigger compatibility layer on WP Dashboard.
	 *
	 * @return void
	 */
	protected function run_fix_on_dashboard_only() {
		$this->apply_fix();
	}

	/**
	 * Trigger compatibility layer on WP frontend.
	 *
	 * @return void
	 */
	protected function run_fix_on_frontend_only() {
		$this->apply_fix();
	}

	/**
	 * Trigger compatibility layer on each request.
	 *
	 * @return void
	 */
	protected function run_fix_everytime() {
		$this->apply_fix();
	}

	/**
	 * Persist the compatibility layer data to the database.
	 *
	 * @return void
	 */
	protected function persist_data( array $plugin_methods = [] ) {
		$pantheon_applied_fixes = get_option( 'pantheon_applied_fixes' ) ?: [];
		$old = $pantheon_applied_fixes[ static::$plugin_slug ] ?? [];
		$pantheon_applied_fixes[ static::$plugin_slug ] = [
			'plugin_slug' => static::$plugin_slug,
			'plugin_name' => static::$plugin_name,
			'plugin_status' => $plugin_methods ? 'automated' : 'waiting',
			'plugin_message' => esc_html__( 'Manual fixes can be safely removed.', 'pantheon' ),
			'plugin_class' => static::class,
			'plugin_methods' => implode( ',', $plugin_methods ),
			'plugin_timestamp' => time(),
		];
		// Update the option with the modified array.
		if ( $pantheon_applied_fixes[ static::$plugin_slug ] !== $old ) {
			update_option( 'pantheon_applied_fixes', $pantheon_applied_fixes );
		}
	}

	/**
	 * Check if the plugin is installed.
	 *
	 * @return bool
	 */
	public function is_plugin_installed() {
		return file_exists( WP_PLUGIN_DIR . '/' . static::$plugin_slug );
	}

	/**
	 * Rollback the compatibility layer.
	 *
	 * @return void
	 */
	public function deactivate() {
		$this->remove_fix();
		$this->remove_persisted_data();
	}

	/**
	 * Remove the compatibility layer fix.
	 *
	 * @return mixed
	 */
	abstract public function remove_fix();

	/**
	 * Remove the compatibility layer's persisted data.
	 *
	 * @return void
	 */
	protected function remove_persisted_data() {
		// Retrieve the array of persisted fixes.
		$pantheon_applied_fixes = get_option( 'pantheon_applied_fixes' );

		// Check if the plugin's data exists in the persisted fixes.
		if ( isset( $pantheon_applied_fixes[ static::$plugin_slug ] ) ) {
			// Remove the plugin's data from the array.
			unset( $pantheon_applied_fixes[ static::$plugin_slug ] );

			// Update the option with the modified array.
			update_option( 'pantheon_applied_fixes', $pantheon_applied_fixes );
		}
	}
}
