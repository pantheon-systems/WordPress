<?php

class BlocksyExtensionProductReviewsPreBoot {
	public function __construct() {
		add_action('admin_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (! function_exists('blocksy_is_dashboard_page')) return;
			if (! blocksy_is_dashboard_page()) return;

			wp_enqueue_script(
				'blocksy-ext-product-reviews-admin-dashboard-scripts',
				BLOCKSY_URL . 'framework/extensions/product-reviews/static/bundle/dashboard.js',
				['ct-options-scripts', 'ct-dashboard-scripts'],
				$data['Version']
			);

			wp_enqueue_style(
				'blocksy-ext-product-reviews-admin-dashboard-styles',
				BLOCKSY_URL . 'framework/extensions/product-reviews/static/bundle/main-admin.min.css',
				[],
				$data['Version']
			);
		});
	}

	public function ext_action($payload) {
		$ext = \Blocksy\Plugin::instance()->extensions->get('product-reviews');

		if (
			! isset($payload['type'])
			||
			! isset($payload['settings'])
			||
			$payload['type'] !== 'persist'
			||
			! $ext
		) {
			return;
		}

		$ext->set_settings($payload['settings']);

		global $wp_rewrite;
		$wp_rewrite->flush_rules();

		return $this->ext_data([
			'settings' => $payload['settings']
		]);
	}

	public function ext_data($args = []) {
		return wp_parse_args($args, [
			'settings' => get_option('blocksy_ext_product_reviews_settings', [
				'single_slug' => 'product-review',
				'category_slug' => 'product-review-category',
			])
		]);
	}
}

