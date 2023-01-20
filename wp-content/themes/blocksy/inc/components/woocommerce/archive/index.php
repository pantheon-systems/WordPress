<?php

add_filter('woocommerce_show_page_title', '__return_false');
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');

remove_action(
	'woocommerce_before_main_content',
	'woocommerce_breadcrumb',
	20
);

remove_action(
	'woocommerce_before_main_content',
	'woocommerce_output_content_wrapper',
	10
);

remove_action(
	'woocommerce_after_main_content',
	'woocommerce_output_content_wrapper_end',
	10
);

remove_action(
	'woocommerce_before_single_product_summary',
	'woocommerce_show_product_sale_flash'
);

remove_action(
	'woocommerce_archive_description',
	'woocommerce_taxonomy_archive_description'
);

remove_action(
	'woocommerce_archive_description',
	'woocommerce_product_archive_description'
);

add_action('woocommerce_before_shop_loop', function () {
	echo '<div class="woo-listing-top">';
}, 12);

add_action('woocommerce_before_shop_loop', function () {
	echo '</div>';
}, 31);

