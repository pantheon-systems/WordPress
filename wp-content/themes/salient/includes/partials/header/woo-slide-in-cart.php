<?php
/**
 * WooCommerce slide in cart
 *
 * @package    Salient WordPress Theme
 * @subpackage Partials
 * @version    9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
global $woocommerce;

$options = get_nectar_theme_options();

$nav_cart_style = ( ! empty( $options['ajax-cart-style'] ) ) ? $options['ajax-cart-style'] : 'default';

if ( $woocommerce && $nav_cart_style == 'slide_in' ) {
	echo '<div class="nectar-slide-in-cart">';
	// Check for WooCommerce 2.0 and display the cart widget
	if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0' ) >= 0 ) {
		the_widget( 'WC_Widget_Cart' );
	} else {
		the_widget( 'WooCommerce_Widget_Cart', 'title= ' );
	}
	echo '</div>';
}