<?php

// https://woocommerce.com/products/woocommerce-additional-variation-images/

add_action('init', function () {
	if (! blocksy_woocommerce_has_flexy_view()) {
		return;
	}

	add_action(
		'wp_enqueue_scripts',
		function () {
			wp_dequeue_script('wc_additional_variation_images_script');
		},
		500
	);
});

add_filter(
	'woocommerce_available_variation',
	function ($result, $product, $variation) {
		if (! blocksy_woocommerce_has_flexy_view()) {
			return $result;
		}

		$variation_values = get_post_meta(
			$variation->get_id(),
			'blocksy_post_meta_options'
		);

		if (empty($variation_values)) {
			$variation_values = [[]];
		}

		if (! $variation_values[0]) {
			$variation_values[0] = [];
		}

		$variation_values = $variation_values[0];

		$original_image = wc_get_product_attachment_props(
			$product->get_image_id()
		);

		$original_image['id'] = $product->get_image_id();

		$result['blocksy_original_image'] = $original_image;

		unset($result['blocksy_original_image']['url']);

		$result['blocksy_gallery_source'] = blocksy_akg(
			'gallery_source', $variation_values, 'default'
		);

		if (wp_doing_ajax()) {
			$gallery_args = [
				'product' => $product,
				'forced_single' => true,
			];

			remove_action(
				'woocommerce_product_thumbnails',
				'woocommerce_show_product_thumbnails',
				20
			);

			global $blocksy_current_variation;

			if ($variation) {
				$blocksy_current_variation = $variation;
			}

			$result['blocksy_gallery_html'] = blocksy_render_view(
				dirname(__FILE__) . '/../single/woo-gallery-template.php',
				$gallery_args
			);

			$blocksy_current_variation = null;

			if (get_theme_mod('gallery_style', 'horizontal') === 'vertical') {
				$result['blocksy_gallery_style'] =  'thumbs-left';
			} else {
				$result['blocksy_gallery_style'] =  'thumbs-bottom';
			}
		}

		return $result;
	},
	10, 3
);

add_action(
	'wp_ajax_blocksy_get_product_view_for_variation',
	'blocksy_get_product_view_for_variation'
);

add_action(
	'wp_ajax_nopriv_blocksy_get_product_view_for_variation',
	'blocksy_get_product_view_for_variation'
);

function blocksy_get_product_view_for_variation() {
	if (! isset($_GET['product_id'])) {
		wp_send_json_error();
	}

	$product = wc_get_product(absint($_GET['product_id']));

	if (! $product) {
		wp_send_json_error();
	}

	$gallery_args = [
		'product' => $product,
		'forced_single' => true,
	];

	if (isset($_GET['is_quick_view']) && $_GET['is_quick_view'] === 'yes') {
		global $blocksy_is_quick_view;
		$blocksy_is_quick_view = true;

		$gallery_args['forced_single'] = false;
	}

	remove_action(
		'woocommerce_product_thumbnails',
		'woocommerce_show_product_thumbnails',
		20
	);

	if (isset($_GET['variation_id'])) {
		$variation_id = isset($_GET['variation_id']) ? absint($_GET['variation_id']) : false;
		$variation = $variation_id ? wc_get_product($variation_id) : false;

		global $blocksy_current_variation;

		if ($variation) {
			$blocksy_current_variation = $variation;
		}
	}

	$blocksy_gallery_style = 'thumbs-bottom';

	if (get_theme_mod('gallery_style', 'horizontal') === 'vertical') {
		$blocksy_gallery_style = 'thumbs-left';
	}

	wp_send_json_success([
		'html' => blocksy_render_view(
			dirname(__FILE__) . '/../single/woo-gallery-template.php',
			$gallery_args
		),
		'blocksy_gallery_style' => $blocksy_gallery_style
	]);
}
