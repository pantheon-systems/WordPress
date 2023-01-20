<?php

require get_template_directory() . '/inc/components/woocommerce/general.php';

require get_template_directory() . '/inc/components/woocommerce/common/checkout.php';
require get_template_directory() . '/inc/components/woocommerce/common/cart.php';
require get_template_directory() . '/inc/components/woocommerce/common/account.php';
require get_template_directory() . '/inc/components/woocommerce/common/store-notice.php';
require get_template_directory() . '/inc/components/woocommerce/common/mini-cart.php';
require get_template_directory() . '/inc/components/woocommerce/common/sale-flash.php';
require get_template_directory() . '/inc/components/woocommerce/common/stock-badge.php';

require get_template_directory() . '/inc/components/woocommerce/archive/helpers.php';
require get_template_directory() . '/inc/components/woocommerce/archive/index.php';
require get_template_directory() . '/inc/components/woocommerce/archive/product-card.php';
require get_template_directory() . '/inc/components/woocommerce/archive/loop.php';
require get_template_directory() . '/inc/components/woocommerce/archive/loop-elements.php';
require get_template_directory() . '/inc/components/woocommerce/archive/pagination.php';

require get_template_directory() . '/inc/components/woocommerce/single/review-form.php';
require get_template_directory() . '/inc/components/woocommerce/single/single.php';
require get_template_directory() . '/inc/components/woocommerce/single/add-to-cart.php';
require get_template_directory() . '/inc/components/woocommerce/single/woo-gallery.php';
require get_template_directory() . '/inc/components/woocommerce/single/tabs.php';

// if (class_exists('WC_Additional_Variation_Images_Frontend')) {
	require get_template_directory() . '/inc/components/woocommerce/integrations/woocommerce-additional-variation-images.php';
// }

add_action('after_setup_theme', function () {
	add_theme_support('woocommerce');

	if (
		get_theme_mod('has_product_single_lightbox', 'no') === 'yes'
		||
		is_customize_preview()
	) {
		add_theme_support('wc-product-gallery-lightbox');
	}

	if (
		get_theme_mod('has_product_single_zoom', 'yes') === 'yes'
		||
		is_customize_preview()
	) {
		add_theme_support('wc-product-gallery-zoom');
	}
});

if (! wp_doing_ajax()) {
	add_filter('template_include', function ($template) {
		if (
			! blocksy_manager()->screen->uses_woo_default_template()
			||
			! blocksy_woocommerce_has_flexy_view()
		) {
			add_theme_support('wc-product-gallery-slider');
		}

		return $template;
	}, 900000009);
} else {
	add_action('init', function () {
		if (
			! blocksy_manager()->screen->uses_woo_default_template()
			||
			! blocksy_woocommerce_has_flexy_view()
		) {
			add_theme_support('wc-product-gallery-slider');
		}
	});
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

add_action('wp_enqueue_scripts', function () {
	if (
		blocksy_manager()->screen->uses_woo_default_template()
		&&
		blocksy_woocommerce_has_flexy_view()
	) {
		wp_deregister_script('yith_wapo_color_label_frontend');
	}
}, 100);

add_action('wp_enqueue_scripts', function () {
	// return;
	if (! function_exists('is_shop')) return;

	$theme = blocksy_get_wp_parent_theme();

	wp_enqueue_style(
		'ct-woocommerce-styles',
		get_template_directory_uri() . '/static/bundle/woocommerce.min.css',
		[],
		$theme->get('Version')
	);

	// wp_dequeue_style( 'wc-block-style' );
});

add_action(
	'blocksy:widgets_init',
	function ($sidebar_title_tag) {
		register_sidebar(
			[
				'name' => esc_html__('WooCommerce Sidebar', 'blocksy'),
				'id' => 'sidebar-woocommerce',
				'description' => esc_html__('Add widgets here.', 'blocksy'),
				'before_widget' => '<div class="ct-widget %2$s" id="%1$s">',
				'after_widget' => '</div>',
				'before_title' => '<' . $sidebar_title_tag . ' class="widget-title">',
				'after_title' => '</' . $sidebar_title_tag . '>',
			]
		);
	}
);

