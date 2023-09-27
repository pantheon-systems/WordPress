<?php

add_action(
	'woocommerce_before_add_to_cart_form',
	function () {
		global $product;
		global $root_product;

		$root_product = $product;
	}
);

add_action('woocommerce_post_class', function ($classes) {
	global $product;

	if (! (
		is_product()
		||
		wp_doing_ajax()
	)) {
		return $classes;
	}

	if (! $product) {
		return $classes;
	}

	if ($product->is_type('external')) {
		return $classes;
	}

	$ajax_add_to_cart_id = 'has_ajax_add_to_cart';

	if (get_theme_mod($ajax_add_to_cart_id, 'no') === 'yes') {
		$classes[] = 'ct-ajax-add-to-cart';
	}

	return $classes;
});

if (! function_exists('blocksy_woo_output_cart_action_open')) {
	function blocksy_woo_output_cart_action_open() {
		$attr = [
			'class' => 'ct-cart-actions'
		];

		if (
			(is_product() || wp_doing_ajax())
			&&
			! blocksy_manager()->screen->uses_woo_default_template()
		) {
			$attr['class'] = 'ct-cart-actions-builder';
			return;
		}

		$attr = apply_filters('blocksy:woocommerce:cart-actions:attr', $attr);

		echo '<div ' . blocksy_attr_to_html($attr) . '>';
	}
}

add_action(
	'woocommerce_before_add_to_cart_quantity',
	function () {
		global $product;
		global $root_product;

		if (! $root_product) {
			return;
		}

		if (
			! $root_product->is_type('simple')
			&&
			! $root_product->is_type('variable')
			&&
			! $root_product->is_type('subscription')
			&&
			! $root_product->is_type('variable-subscription')
		) {
			return;
		}

		blocksy_woo_output_cart_action_open();
	},
	PHP_INT_MAX
);

add_action(
	'woocommerce_before_add_to_cart_button',
	function () {
		global $product;
		global $root_product;

		if (! $root_product) {
			return;
		}

		if (
			! $root_product->is_type('grouped')
			&&
			! $root_product->is_type('external')
		) {
			return;
		}

		blocksy_woo_output_cart_action_open();
	},
	PHP_INT_MAX
);

add_action(
	'woocommerce_after_add_to_cart_button',
	function () {
		global $product;

		if (! $product) {
			return;
		}

		if (
			! $product->is_type('simple')
			&&
			! $product->is_type('variable')
			&&
			! $product->is_type('subscription')
			&&
			! $product->is_type('variable-subscription')
			&&
			! $product->is_type('grouped')
			&&
			! $product->is_type('external')
		) {
			return;
		}

		if (
			(
				$product->is_type('simple')
				||
				$product->is_type('variable')
				||
				$product->is_type('subscription')
				||
				$product->is_type('variable-subscription')
			)
			&&
			! did_action('woocommerce_before_add_to_cart_quantity')
		) {
			return;
		}

		if (
			(is_product() || wp_doing_ajax())
			&&
			! blocksy_manager()->screen->uses_woo_default_template()
		) {
			return;
		}

		echo '</div>';
	},
	100
);
