<?php

class BlocksyExtensionWidgets {
	public function __construct() {
		add_filter('blocksy_widgets_paths', function ($all_widgets) {
			$all_widgets = array_merge(
				$all_widgets,
				glob(
					dirname(__FILE__) . '/widgets/*',
					GLOB_ONLYDIR
				)
			);

			return $all_widgets;
		});

		add_action('wp_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (is_admin()) return;

			wp_enqueue_style(
				'blocksy-ext-widgets-styles',
				BLOCKSY_URL . 'framework/extensions/widgets/static/bundle/main.min.css',
				[
					'ct-main-styles'
				],
				$data['Version']
			);
		}, 50);
	}
}
