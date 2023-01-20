<?php

require_once dirname(__FILE__) . '/helpers.php';

class BlocksyExtensionCookiesConsent {
	public static function should_display_notification() {
		return ! isset($_COOKIE['blocksy_cookies_consent_accepted']);
	}

	public static function has_consent() {
		return (
			isset($_COOKIE['blocksy_cookies_consent_accepted'])
			&&
			$_COOKIE['blocksy_cookies_consent_accepted'] === 'true'
		);
	}

	public function __construct() {
		add_filter('blocksy:footer:offcanvas-drawer', function ($els) {
			$els[] = blocksy_ext_cookies_consent_output();
			return $els;
		});

		add_filter('blocksy-async-scripts-handles', function ($d) {
			$d[] = 'blocksy-ext-cookies-consent-scripts';
			return $d;
		});

		add_filter(
			'blocksy_extensions_customizer_options',
			[$this, 'add_options_panel']
		);

		add_action(
			'customize_preview_init',
			function () {
				if (! function_exists('get_plugin_data')){
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$data = get_plugin_data(BLOCKSY__FILE__);

				wp_enqueue_script(
					'blocksy-cookies-consent-customizer-sync',
					BLOCKSY_URL . 'framework/extensions/cookies-consent/static/bundle/sync.js',
					[ 'ct-scripts', 'customize-preview' ],
					$data['Version'],
					true
				);
			}
		);

		add_action('wp_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (is_admin()) {
				return;
			}

			wp_enqueue_style(
				'blocksy-ext-cookies-consent-styles',
				BLOCKSY_URL . 'framework/extensions/cookies-consent/static/bundle/main.min.css',
				['ct-main-styles'],
				$data['Version']
			);

			wp_enqueue_script(
				'blocksy-ext-cookies-consent-scripts',
				BLOCKSY_URL . 'framework/extensions/cookies-consent/static/bundle/main.js',
				[],
				$data['Version'],
				true
			);
		}, 50);

		add_action(
			'blocksy:global-dynamic-css:enqueue',
			'BlocksyExtensionCookiesConsent::add_global_styles',
			10, 3
		);
	}

	static public function add_global_styles($args) {
		blocksy_theme_get_dynamic_styles(array_merge([
			'path' => dirname(__FILE__) . '/global.php',
			'chunk' => 'global',
		], $args));
	}

	static public function onDeactivation() {
		remove_action(
			'blocksy:global-dynamic-css:enqueue',
			'BlocksyExtensionCookiesConsent::add_global_styles',
			10, 3
		);
	}

	public function add_options_panel($options) {
		$options['cookie_consent_ext'] = blc_call_fn(
			[
				'fn' => 'blocksy_get_options',
				'default' => 'array'
			],
			dirname(__FILE__) . '/customizer.php',
			[], false
		);

		return $options;
	}
}

