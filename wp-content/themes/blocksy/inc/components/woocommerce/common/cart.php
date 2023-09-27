<?php

add_action('elementor/widget/before_render_content', function($widget) {
	if (! class_exists('ElementorPro\Modules\Woocommerce\Widgets\Cart')) {
		return;
	}

	if ($widget instanceof ElementorPro\Modules\Woocommerce\Widgets\Cart) {
		global $ct_skip_cart;
		$ct_skip_cart = true;
	}
}, 10 , 1);

add_filter('wc_get_template', function ($template, $template_name, $args, $template_path, $default_path) {
	if ($template_name !== 'cart/cart.php') {
		return $template;
	}

	global $ct_skip_cart;

	if ($ct_skip_cart) {
		$default_path = WC()->plugin_path() . '/templates/';
		return $default_path . $template_name;
	}

	return $template;
}, 10, 5);
