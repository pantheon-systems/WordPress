<?php

add_action(
	'woocommerce_before_template_part',
	function ($template_name, $template_path, $located, $args) {
		if ($template_name !== 'single-product/product-image.php') {
			return;
		}

		if (! blocksy_woocommerce_has_flexy_view()) {
			return;
		}

		echo blocksy_render_view(dirname(__FILE__) . '/woo-gallery-template.php');

		ob_start();
	},
	4, 4
);

add_action(
	'woocommerce_after_template_part',
	function ($template_name, $template_path, $located, $args) {
		if ($template_name !== 'single-product/product-image.php') {
			return;
		}

		if (! blocksy_woocommerce_has_flexy_view()) {
			return;
		}

		ob_get_clean();
	},
	4, 4
);

if (! function_exists('blocksy_retrieve_product_default_variation')) {
	function blocksy_retrieve_product_default_variation($product) {
		$is_default_variation = false;
		$current_variation = null;

		foreach($product->get_available_variations() as $variation_values ) {
			foreach ($variation_values['attributes'] as $key => $attribute_value) {
				$attribute_name = str_replace( 'attribute_', '', $key );
				$default_value = $product->get_variation_default_attribute($attribute_name);

				if ($default_value == $attribute_value) {
					$is_default_variation = true;
				} else {
					$is_default_variation = false;
					break;
				}
			}

			if ($is_default_variation ) {
				$variation_id = $variation_values['variation_id'];
				break;
			}
		}

		if ($is_default_variation) {
			$default_variation = wc_get_product($variation_id);
			$current_variation = $default_variation;
		}

		$maybe_variation = (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
			$product,
			$_GET
		);

		if ($maybe_variation) {
			$current_variation = wc_get_product($maybe_variation);
		}

		return $current_variation;
	}
}
