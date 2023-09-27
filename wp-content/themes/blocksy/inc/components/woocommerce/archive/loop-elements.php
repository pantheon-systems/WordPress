<?php

add_action(
	'wp',
	function () {
		if (get_theme_mod('has_shop_sort', 'yes') !== 'yes') {
			remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
		}

		if (get_theme_mod('has_shop_results_count', 'yes') !== 'yes') {
			remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
		}
	},
	5
);

add_action('woocommerce_after_subcategory', function ( $category ) {
	$has_excerpt = get_theme_mod('has_excerpt', 'no') === 'yes';

	if (! $has_excerpt) {
		return;
	}

	echo blocksy_entry_excerpt(
		get_theme_mod('excerpt_length', '40'),
		'entry-excerpt',
		null,
		'custom',
		$category->description
	);
}, 15 );
