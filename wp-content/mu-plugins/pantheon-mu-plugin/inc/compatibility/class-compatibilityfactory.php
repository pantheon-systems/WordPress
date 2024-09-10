<?php
/**
 * Factory class bootstrapping the compatibility layer logic.
 *
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use function add_action;
use function array_column;
use function array_key_exists;
use function array_map;
use function basename;
use function file_exists;
use function function_exists;
use function get_option;
use function glob;
use function in_array;
use function time;
use function wp_next_scheduled;
use function wp_schedule_event;

/**
 * Class CompatibilityFactory
 *
 * @package Pantheon\Compatibility
 */
class CompatibilityFactory {

	/**
	 * Plugins targeted by the automated compatibility layer.
	 *
	 * @var array $targets
	 */
	public static $targets = [];
	/**
	 * Class instance.
	 *
	 * @var CompatibilityFactory
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->require_files();
		$this->setup_targets();

		add_action( 'plugins_loaded', [ $this, 'init' ] );
		add_action( 'pantheon_cron', [ $this, 'daily_pantheon_cron' ] );
	}

	/**
	 * Require all the compatibility layer files.
	 *
	 * @return void
	 */
	private function require_files() {
		require_once __DIR__ . '/base.php';
		foreach ( glob( __DIR__ . '/*.php' ) as $file ) {
			if ( in_array( $file, [ 'base.php', basename( __FILE__ ) ], true ) ) {
				continue;
			}
			require_once $file;
		}

		foreach ( glob( __DIR__ . '/fixes/*.php' ) as $file ) {
			require_once $file;
		}
	}

	/**
	 * Build the list of target plugins,
	 * with values for plugins slugs and names, keyed by class names.
	 *
	 * @return void
	 */
	private function setup_targets() {
		static::$targets = [
			AcceleratedMobilePages::class => [ 'slug' => 'accelerated-mobile-pages/accelerated-mobile-pages.php' ],
			Auth0::class => [ 'slug' => 'auth0/WP_Auth0.php' ],
			Autoptimize::class => [ 'slug' => 'autoptimize/autoptimize.php' ],
			BetterSearchReplace::class => [ 'slug' => 'better-search-replace/better-search-replace.php' ],
			BrokenLinkChecker::class => [ 'slug' => 'broken-link-checker/broken-link-checker.php' ],
			ContactFormSeven::class => [ 'slug' => 'contact-form-7/wp-contact-form-7.php' ],
			EventEspresso::class => [ 'slug' => 'event-espresso-decaf/espresso.php' ],
			FastVelocityMinify::class => [ 'slug' => 'fast-velocity-minify/fvm.php' ],
			ForceLogin::class => [ 'slug' => 'wp-force-login/wp-force-login.php' ],
			OfficialFacebookPixel::class => [ 'slug' => 'official-facebook-pixel/facebook-for-wordpress.php' ],
			Polylang::class => [ 'slug' => 'polylang/polylang.php' ],
			Redirection::class => [ 'slug' => 'redirection/redirection.php' ],
			SliderRevolution::class => [ 'slug' => 'slider-revolution/slider-revolution.php' ],
			TweetOldPost::class => [ 'slug' => 'tweet-old-post/tweet-old-post.php' ],
			WPRocket::class => [ 'slug' => 'wp-rocket/wp-rocket.php' ],
			WooZone::class => [ 'slug' => 'woozone/plugin.php' ],
			YITHWoocommerce::class => [ 'slug' => 'yith-woocommerce-request-a-quote/yith-woocommerce-request-a-quote.php' ],
		];
		$this->add_names_to_targets();
	}

	/**
	 * Add plugin names to the target plugins array.
	 *
	 * @return void
	 */
	private function add_names_to_targets() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		array_walk( static::$targets, static function ( &$plugin ) {
			$file = WP_PLUGIN_DIR . '/' . $plugin['slug'];
			if ( ! file_exists( $file ) ) {
				$plugin['name'] = $plugin['slug'];

				return;
			}
			$plugin_data = get_plugin_data( $file, false, false );
			$plugin['name'] = $plugin_data['Name'];
		});
	}

	/**
	 * Get an instance of the class.
	 *
	 * @return CompatibilityFactory
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate classes and register cron job.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->instantiate_compatibility_layers();

		if ( ! wp_next_scheduled( 'pantheon_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'pantheon_cron' );
		}
	}

	/**
	 * Instantiate compatibility layer classes.
	 *
	 * @return void
	 */
	private function instantiate_compatibility_layers() {
		foreach ( static::$targets as $class => $plugin ) {
			new $class( $plugin['slug'] );
		}
	}

	/**
	 * Fallback method to apply fixes hooked to a daily cron job.
	 *
	 * @return void
	 */
	public function daily_pantheon_cron() {
		$compat_classes = static::$targets;
		// get list of applied fixes.
		$pantheon_applied_fixes = get_option( 'pantheon_applied_fixes' ) ?: [];
		// filter list of active plugins by fix availability & fix status, then initialize compatibility layers.
		array_map( static function ( $plugin ) use ( $compat_classes, $pantheon_applied_fixes ) {
			$compat_layer = array_search(
				$plugin,
				array_combine( array_keys( $compat_classes ), array_column( $compat_classes, 'slug' ) ),
				true
			);
			if (
				array_key_exists( $plugin, $pantheon_applied_fixes ) ||
				! $compat_layer
			) {
				return;
			}
			$instance = new $compat_layer( $plugin );
			$instance->apply_fix();
		}, (array) get_option( 'active_plugins' ) ?: [] );
	}
}
