<?php

if (! function_exists('blocksy_get_products_listing_layout')) {
	function blocksy_get_products_listing_layout() {
		return get_theme_mod('shop_cards_type', 'type-1');
	}
}

if (! function_exists('blocksy_products_layout_attr')) {
	function blocksy_products_layout_attr() {
		$shop_structure = blocksy_get_products_listing_layout();
		// $alignment = '';

		// if ($shop_structure === 'type-1') {
		// 	$alignment = ' data-alignment="' . get_theme_mod('shop_cards_alignment_1', 'left') . '"';
		// }

		// return 'data-products="' . $shop_structure . '"' . $alignment;
		return 'data-products="' . $shop_structure . '"';
	}
}

add_action(
	'uael_before_product_loop_start',
	function ($args) {
		wc_set_loop_prop('name', 'ultimate_addons');
	}
);

add_filter(
	'woocommerce_product_loop_start',
	function ($content) {
		$attr = '';

		if (wc_get_loop_prop('name', 'default') !== 'ultimate_addons') {
			$hover_attr = '';
			$hover_value = get_theme_mod('product_image_hover', 'none');

			$other_attr = [];

			if ($hover_value !== 'none') {
				$other_attr['data-hover'] = $hover_value;
			}

			if (function_exists('blocksy_quick_view_attr')) {
				$other_attr = array_merge(blocksy_quick_view_attr(), $other_attr);
			}

			$attr = blocksy_products_layout_attr() . ' ' . blocksy_attr_to_html(
				$other_attr
			);
		}

		return str_replace(
			'class="products',
			$attr . 'class="products',
			$content
		);
	}
);
