<?php

if (! function_exists('woocommerce_mini_cart')) {
    return '';
}

if (
	! WC()->cart
	||
	! method_exists(WC()->cart, 'get_cart_contents_count')
) {
	return '';
}

if (! isset($device)) {
	$device = 'desktop';
}

if (isset($render_args['device'])) {
	$device = $render_args['device'];
}

$has_only_item = false;
$has_only_cart = false;
$has_only_totals = false;
$has_only_count = false;

if (isset($render_args['only_item'])) {
	$has_only_item = $render_args['only_item'];
}

if (isset($render_args['only_cart'])) {
	$has_only_cart = $render_args['only_cart'];
}

if (isset($render_args['only_totals'])) {
	$has_only_totals = $render_args['only_totals'];
}

if (isset($render_args['only_count'])) {
	$has_only_count = $render_args['only_count'];
}

$has_badge = blocksy_default_akg('has_cart_badge', $atts, 'yes') === 'yes';

$icon = apply_filters('blocksy:header:cart:icons', [
	'type-1' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.1,1.6C14,0.7,13.3,0,12.4,0H2.7C1.7,0,1,0.7,0.9,1.6L0.1,13.1c0,0.5,0.1,1,0.5,1.3C0.9,14.8,1.3,15,1.8,15h11.4c0.5,0,0.9-0.2,1.3-0.6c0.3-0.4,0.5-0.8,0.5-1.3L14.1,1.6zM13.4,13.4c0,0-0.1,0.1-0.2,0.1H1.8c-0.1,0-0.2-0.1-0.2-0.1c0,0-0.1-0.1-0.1-0.2L2.4,1.7c0-0.1,0.1-0.2,0.2-0.2h9.7c0.1,0,0.2,0.1,0.2,0.2l0.8,11.5C13.4,13.3,13.4,13.4,13.4,13.4z M10,3.2C9.6,3.2,9.2,3.6,9.2,4v1.5c0,1-0.8,1.8-1.8,1.8S5.8,6.5,5.8,5.5V4c0-0.4-0.3-0.8-0.8-0.8S4.2,3.6,4.2,4v1.5c0,1.8,1.5,3.2,3.2,3.2s3.2-1.5,3.2-3.2V4C10.8,3.6,10.4,3.2,10,3.2z"/></svg>',

	'type-2' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M0.6,0.7C0.3,0.7,0,0.8,0,1.1s0.1,0.6,0.6,0.6l0,0h1c0.1,0,0.1,0.1,0.1,0.1L4.2,10c0.3,0.7,0.9,1.2,1.6,1.2H12c0.7,0,1.3-0.6,1.6-1.2L15,4.7c0.1-0.3-0.1-0.6-0.4-0.6h-0.1H3.6L2.8,1.7l0,0c0-0.7-0.6-1-1.2-1H0.6zM6.1,12.2c-0.6,0-1,0.6-1,1c0,0.4,0.6,1,1,1c0.4,0,1-0.6,1-1C7.2,12.8,6.7,12.2,6.1,12.2zM11.7,12.2c-0.6,0-1,0.6-1,1c0,0.4,0.6,1,1,1c0.6,0,1-0.6,1-1C12.7,12.8,12.3,12.2,11.7,12.2z"/></svg>',

	'type-3' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M4.5,1.1C3.9,1.1,3.4,1.4,3.1,2L1.7,5.9H0.6C0.5,5.9,0.1,6.1,0,6.2c0,0,0,0.3,0,0.5L1.7,13c0.1,0.6,0.8,0.9,1.3,0.9h8.9c0.6,0,1.2-0.5,1.4-0.9L15,6.7c0-0.2,0-0.5-0.2-0.6c0-0.1-0.3-0.1-0.4-0.1h-1.1l-1.6-4.1l0,0c-0.3-0.5-0.6-0.8-1.2-0.8H4.5zM4.5,2.5h5.9l1.5,3.5H3.1L4.5,2.5z M4.8,7.9c0.4,0,0.6,0.3,0.6,0.6v2.7c0,0.4-0.3,0.6-0.6,0.6c-0.3,0.2-0.6-0.3-0.6-0.6V8.6C4.1,8.2,4.4,7.9,4.8,7.9zM7.5,7.9c0.5,0,0.6,0.3,0.6,0.6v2.7c0,0.4-0.3,0.6-0.6,0.6c-0.5,0-0.6-0.3-0.6-0.6V8.6C6.9,8.2,7,7.9,7.5,7.9zM10.2,7.9c0.4,0,0.6,0.3,0.6,0.6v2.7c0,0.4-0.3,0.6-0.6,0.6c-0.5,0-0.6-0.3-0.6-0.6V8.6C9.6,8.2,9.9,7.9,10.2,7.9z"/></svg>',

	'type-4' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M11.2,3.5V1.8c0-1-0.8-1.8-1.8-1.8h-4c-1,0-1.8,0.8-1.8,1.8v1.8H0v9.8c0,1,0.8,1.8,1.8,1.8h11.5c1,0,1.8-0.8,1.8-1.8V3.5H11.2zM5.2,1.8c0-0.1,0.1-0.2,0.2-0.2h4c0.1,0,0.2,0.1,0.2,0.2v1.8H5.2V1.8z M13.5,13.2c0,0.1-0.1,0.2-0.2,0.2H1.8c-0.1,0-0.2-0.1-0.2-0.2V5h12V13.2zM5.5,8c0.4,0,0.8-0.3,0.8-0.8S5.9,6.5,5.5,6.5S4.8,6.8,4.8,7.2C4.8,7.7,5.1,8,5.5,8zM9.5,8c0.4,0,0.8-0.3,0.8-0.8S9.9,6.5,9.5,6.5S8.8,6.8,8.8,7.2C8.8,7.7,9.1,8,9.5,8z"/></svg>',

	'type-5' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M4.2,6.3c0-0.4,0.3-0.8,0.8-0.8s0.8,0.3,0.8,0.8c0,1,0.8,1.8,1.8,1.8s1.8-0.8,1.8-1.8c0-0.4,0.3-0.8,0.8-0.8s0.8,0.3,0.8,0.8c0,1.8-1.5,3.2-3.2,3.2S4.2,8.1,4.2,6.3zM14.2,3.6v9.7c0,1-0.8,1.7-1.8,1.7h-10c-1,0-1.8-0.8-1.8-1.7V3.6L3.1,0h8.8L14.2,3.6z M2.9,3h9.2l-1-1.5H3.9L2.9,3zM12.8,4.5H2.2v8.8c0,0.1,0.1,0.2,0.2,0.2h10c0.1,0,0.2-0.1,0.2-0.2V4.5z"/></svg>',

	'type-6' => '<svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M15,6.2c0-0.1,0-0.4-0.1-0.5c-0.3-0.5-0.8-0.5-1.1-0.5h-1.3L8.7,1.3C8.4,0.8,8,0.7,7.5,0.7S6.6,0.8,6.3,1.3L2.6,5.2H1.2c-0.3,0-0.8,0.2-0.9,0.4C0.2,5.8,0,6.1,0,6.4v0.9l0.9,5.1c0.2,1.2,1.2,1.9,2.2,1.9h8.6c1,0,2.1-0.9,2.2-1.9l0.9-5.1C14.9,7.3,15,6.2,15,6.2zM7,2c0.2-0.1,0.3-0.3,0.5-0.3s0.3,0,0.5,0.1L11,5H4.1C4.1,5,7,2,7,2z M4.4,11.6c-0.3,0-0.6-0.1-0.6-0.6L3.6,8.5c0-0.3,0.3-0.6,0.6-0.6s0.4,0.3,0.6,0.6L5,11.2C5,11.3,4.7,11.5,4.4,11.6z M8.1,11c0,0.3-0.3,0.6-0.6,0.6c-0.3,0-0.6-0.3-0.6-0.6V8.3c0-0.3,0.3-0.6,0.6-0.6c0.3,0,0.6,0.3,0.6,0.6C8.1,8.3,8.1,11,8.1,11z M11.4,8.5L11.1,11c0,0.3-0.3,0.6-0.6,0.6s-0.5-0.3-0.5-0.6l0.2-2.7c0-0.3,0.3-0.6,0.6-0.6C11.1,7.7,11.4,8,11.4,8.5C11.4,8.3,11.4,8.3,11.4,8.5z"/></svg>',
]);


$class = 'ct-header-cart';

$item_visibility = blocksy_default_akg('header_cart_visibility', $atts, [
	'tablet' => true,
	'mobile' => true,
]);

$class .= ' ' . blocksy_visibility_classes($item_visibility);

$has_cart_dropdown = blocksy_default_akg(
	'has_cart_dropdown',
	$atts,
	'yes'
) === 'yes';

$cart_drawer_type = blocksy_default_akg('cart_drawer_type', $atts, 'dropdown');

$cart_total_class = 'ct-label';

$cart_subtotal_visibility = blocksy_default_akg(
	'cart_subtotal_visibility',
	$atts,
	[
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	]
);

$cart_total_class .= ' ' . blocksy_visibility_classes($cart_subtotal_visibility);
$has_subtotal = (
	blocksy_some_device($cart_subtotal_visibility)
	||
	is_customize_preview()
);

$cart_total_position = blocksy_expand_responsive_value(
	blocksy_akg('cart_total_position', $atts, 'left')
);

$icon_type = blocksy_default_akg('mini_cart_type', $atts, 'type-1');

if (empty($icon_type)) {
	$icon_type = 'type-1';
}

$icon = $icon[$icon_type];

if (function_exists('blc_get_icon')) {
	$icon_source = blocksy_default_akg('icon_source', $atts, 'default');

	if ( $icon_source === 'custom' ) {
		$icon = blc_get_icon([
			'icon_descriptor' => blocksy_akg('icon', $atts, [
				'icon' => 'blc blc-cart'
			]),
			'icon_container' => false
		]);
	}

}

$item_class = 'ct-cart-item';

$url = wc_get_cart_url();

$auto_open_output = '';

if ($has_cart_dropdown && $cart_drawer_type === 'offcanvas') {
	$item_class .= ' ct-offcanvas-trigger';
	$url = '#woo-cart-panel';

	$auto_open_cart = blocksy_default_akg('auto_open_cart', $atts, [
		'archive' => false,
		'product' => false,
	]);

	$components = [];


	if ($auto_open_cart['archive']) {
		$components[] = 'archive';
	}

	if ($auto_open_cart['product']) {
		$components[] = 'product';
	}

	if (! empty($components)) {
		$auto_open_output = 'data-auto-open="' . implode(':', $components) . '"';
	}
}

$url = apply_filters('blocksy:header:cart:url', $url);

ob_start();

$count_output = '';
$current_count = 0;

if (WC()->cart && method_exists(WC()->cart, 'get_cart_contents_count')) {
	$current_count = WC()->cart->get_cart_contents_count();
}

$count_output = blocksy_html_tag(
	'span',
	[
		'class' => 'ct-dynamic-count-cart',
		'data-count' => $has_badge ? $current_count : 0
	],
	$current_count
);

$totals_output = '';

if ($has_subtotal && WC()->cart) {
	$totals_output = blocksy_html_tag(
		'span',
		[ 'class' => $cart_total_class ],
		WC()->cart->get_cart_subtotal()
	);
}

?>

<a class="<?php echo $item_class ?>"
	href="<?php echo esc_attr($url) ?>"
	data-label="<?php echo $cart_total_position[$device] ?>"
	aria-label="<?php echo __('Shopping cart', 'blocksy') ?>"
	<?php echo $auto_open_output ?>>

	<?php echo $totals_output; ?>

	<span class="ct-icon-container">
		<?php
			echo $count_output;

			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * The value used here escapes the value properly.
			 * It contains an inline SVG, which is safe.
			 */
			echo $icon;
		?>
	</span>
</a>

<?php

$item = ob_get_clean();

if ($has_only_item) {
	echo $item;
	return;
}

ob_start();
woocommerce_mini_cart();
$content = ob_get_clean();

$cart_otput = blocksy_html_tag(
	'div',
	[
		'class' => 'ct-cart-content',
		'data-count' => $current_count
	],
	$content
);

if ($has_only_cart) {
	echo $cart_otput;
	return;
}

if ($has_only_totals) {
	echo $totals_output;
	return;
}

if ($has_only_count) {
	echo $count_output;
	return;
}

?>

<div
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>>

	<?php
		echo $item;

		if ($has_cart_dropdown && $cart_drawer_type === 'dropdown') {
			echo $cart_otput;
		}
	?>
</div>
