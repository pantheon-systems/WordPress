<?php

$config = [
	'name' => __('Cart', 'blocksy'),
	'enabled' => (
		function_exists('is_woocommerce')
	),
	'typography_keys' => ['cart_total_font'],
	'selective_refresh' => [
		'has_cart_dropdown',
		'mini_cart_type',
		'icon_source',
		'icon'
	]
];
