<?php

if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.3', '>=')) {
	add_filter('woocommerce_add_to_cart_fragments', 'blocksy_header_cart_item_fragment');
} else {
	add_filter('add_to_cart_fragments', 'blocksy_header_cart_item_fragment');
}

remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

if (! function_exists('blocksy_header_cart_item_fragment')) {
	function blocksy_header_cart_item_fragment($fragments) {
		$header = new Blocksy_Header_Builder_Render();

		$elements = new Blocksy_Header_Builder_Elements();

		$fragments['.ct-dynamic-count-cart'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_count' => true
			]
		]);

		$fragments['.ct-header-cart .ct-cart-content'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_cart' => true
			]
		]);

		$fragments['.ct-header-cart .ct-label'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_totals' => true
			]
		]);

		$fragments['#woo-cart-panel .ct-panel-content'] = $elements->render_cart_offcanvas([
			'has_container' => false,
			'force_output' => true
		]);

		return apply_filters('blocksy:woocommerce:cart-fragments', $fragments);
	}
}
