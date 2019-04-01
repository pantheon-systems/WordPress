<?php

/**
 * Enqueue scripts and styles
 */
function elixinol1_enqueue_styles() {
	// Load parent styles
	$nectar_theme_version = nectar_get_theme_version();
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'), $nectar_theme_version);
  if ( is_rtl() ) {
		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
	}

	// Load child styles
	$theme_version = wp_get_theme()->get('Version');
	wp_enqueue_style( 'main-styles', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style', 'rgs' ), $theme_version);

}
add_action( 'wp_enqueue_scripts', 'elixinol1_enqueue_styles');

/**
 * Implements the nectar_hook_after_body_open action
 * @author Christopher Cook
 */
function elixinol1_after_body_open() {
	// Add the Google Tag Manager code if present
	if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) {
		return gtm4wp_the_gtm_tag();
	}
}
add_action( 'nectar_hook_after_body_open', 'elixinol1_after_body_open' );

/**
 * Fix issue with mobile menu breaking
 * @author Mayur Mohit
 */
add_filter( 'woocommerce_get_script_data', 'elixinol1_cart_fragments_params', 20);
function elixinol1_cart_fragments_params( $params ) {
	if ( false === $params ) {
		$params = array( 'wc_ajax_url' => '/' );
	}
	return $params;
}

/**
 * @snippet       Disable Variable Product Price Range
 * @how-to        Watch tutorial @ http://businessbloomer.com/?p=19055
 * @sourcecode    http://businessbloomer.com/disable-variable-product-price-range-woocommerce/
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.7
 * @notes         Was previously used in my-custom-functions plugin but has no visible effect now so removed
 */
//add_filter('woocommerce_variable_price_html', 'elixinol1_custom_variation_price', 10, 2);
function elixinol1_custom_variation_price( $price, $product ) {
	$price = '';
	if ( !$product->min_variation_price || $product->min_variation_price !== $product->max_variation_price ) {
		$price .= '<span class="from">' . _x('', 'min_price', 'woocommerce') . ' </span>';
	}
	$price .= woocommerce_price($product->min_variation_price);
	return $price;
}

