<?php

add_action('wp', function () {
	if (! get_theme_mod('blocksy_has_checkout_coupon', false)) {
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
	}
});

add_action('elementor/widget/before_render_content', function($widget) {
	if (! class_exists('ElementorPro\Modules\Woocommerce\Widgets\Checkout')) {
		return;
	}

	if ($widget instanceof ElementorPro\Modules\Woocommerce\Widgets\Checkout) {
		global $ct_skip_checkout;
		$ct_skip_checkout = true;
	}
}, 10 , 1);

add_action('woocommerce_before_checkout_form', function () {
	add_action('sellkit_checkout_one_page_express_methods', function() {
		global $ct_skip_checkout;
		$ct_skip_checkout = true;
	});
}, 10, 1);

add_action('wpfunnels/before_gb_checkout_form', function($widget) {
	global $ct_skip_checkout;
	$ct_skip_checkout = true;
}, 10 , 1);

add_action('cfw_checkout_main_container_start', function($widget) {
	global $ct_skip_checkout;
	$ct_skip_checkout = true;
}, 10, 1);

add_action('wp', function () {
	if (class_exists('FluidCheckout')) {
		return;
	}

	global $post;

	if ($post && $post->post_type === 'cartflows_step') {
		return;
	}

	add_action('woocommerce_checkout_before_customer_details', function () {
		global $ct_skip_checkout;

		if ($ct_skip_checkout) {
			return;
		}

		echo '<div class="ct-customer-details">';
	}, PHP_INT_MIN);

	add_action('woocommerce_checkout_after_customer_details', function () {
		global $ct_skip_checkout;

		if ($ct_skip_checkout) {
			return;
		}

		echo '</div>';
	}, PHP_INT_MAX);

	add_action('woocommerce_checkout_before_order_review_heading', function () {
		global $ct_skip_checkout;

		if ($ct_skip_checkout) {
			return;
		}

		echo '<div class="ct-order-review">';
	}, PHP_INT_MIN);

	add_action('woocommerce_checkout_after_order_review', function () {
		global $ct_skip_checkout;

		if ($ct_skip_checkout) {
			return;
		}

		echo '</div>';
	}, PHP_INT_MAX);
});

add_action(
	'woocommerce_before_template_part',
	function ($template_name, $template_path, $located, $args) {
		if (! class_exists('Woocommerce_German_Market')) {
			return;
		}

		if ($template_name !== 'checkout/form-checkout.php') {
			return;
		}

		ob_start();
	},
	1,
	4
);

add_action(
	'woocommerce_after_template_part',
	function ($template_name, $template_path, $located, $args) {
		if (! class_exists('Woocommerce_German_Market')) {
			return;
		}

		if ($template_name !== 'checkout/form-checkout.php') {
			return;
		}

		$result = ob_get_clean();

		$search = '/' . preg_quote('<h3 id="order_review_heading">', '/') . '/';

		echo preg_replace(
			$search,
			'<div class="ct-order-review"><h3 id="order_review_heading">',
			$result,
			1
		);
	},
	1,
	4
);
