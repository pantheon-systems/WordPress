<?php

require_once dirname(__FILE__) . '/helpers.php';

class BlocksyExtensionTrending {
	private $result = null;

	public function __construct() {
		add_action('wp_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (is_admin()) {
				return;
			}

			wp_enqueue_style(
				'blocksy-ext-trending-styles',
				BLOCKSY_URL . 'framework/extensions/trending/static/bundle/main.min.css',
				['ct-main-styles'],
				$data['Version']
			);
		}, 50);

		add_filter('blocksy:frontend:dynamic-js-chunks', function ($chunks) {
			$chunks[] = [
				'id' => 'blocksy_ext_trending',
				'selector' => '.ct-trending-block [class*="ct-arrow"]',
				'url' => blc_call_fn(
					[
						'fn' => 'blocksy_cdn_url',
						'default' => BLOCKSY_URL . 'framework/extensions/trending/static/bundle/main.js'
					],
					BLOCKSY_URL . 'framework/extensions/trending/static/bundle/main.js'
				),
				'trigger' => 'click'
			];

			return $chunks;
		});

		add_filter(
			'blocksy_extensions_customizer_options',
			function ($opts) {
				$opts['trending_posts_ext'] = blc_call_fn(
					[
						'fn' => 'blocksy_get_options',
						'default' => 'array'
					],
					dirname(__FILE__) . '/customizer.php',
					[], false
				);

				return $opts;
			}
		);

		add_action('wp', function () {
			$location = 'blocksy:template:after';

			if (function_exists('blc_fs') && blc_fs()->can_use_premium_code()) {
				$location = get_theme_mod(
					'trending_block_location',
					'blocksy:content:bottom'
				);
			}

			$this->result = blc_get_trending_posts_value();

			add_action(
				$location,
				function () {
					if (function_exists('blc_fs') && blc_fs()->can_use_premium_code()) {
						$conditions = get_theme_mod(
							'trending_block_conditions',
							[
								[
									'type' => 'include',
									'rule' => 'everywhere',
								]
							]
						);

						$conditions_manager = new \Blocksy\ConditionsManager();

						if (! $conditions_manager->condition_matches($conditions)) {
							return;
						}
					}

					echo blc_get_trending_block($this->result);
				},
				50
			);
		});


		add_action(
			'customize_preview_init',
			function () {
				if (! function_exists('get_plugin_data')) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$data = get_plugin_data(BLOCKSY__FILE__);

				wp_enqueue_script(
					'blocksy-trending-customizer-sync',
					BLOCKSY_URL . 'framework/extensions/trending/static/bundle/sync.js',
					['customize-preview', 'ct-scripts', 'ct-customizer'],
					$data['Version'],
					true
				);
			}
		);

		add_action(
			'blocksy:global-dynamic-css:enqueue',
			'BlocksyExtensionTrending::add_global_styles',
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
			'BlocksyExtensionTrending::add_global_styles',
			10, 3
		);
	}
}
