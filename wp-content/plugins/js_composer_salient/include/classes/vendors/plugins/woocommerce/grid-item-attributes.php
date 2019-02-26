<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get woocommerce data for product
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_woocommerce_product( $value, $data ) {
	$label = '';
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	require_once WC()->plugin_path() . '/includes/abstracts/abstract-wc-product.php';
	$product = new WC_Product( $post );
	if ( preg_match( '/_labeled$/', $data ) ) {
		$data = preg_replace( '/_labeled$/', '', $data );
		$label = apply_filters( 'vc_gitem_template_attribute_woocommerce_product_' . $data . '_label',
		Vc_Vendor_Woocommerce::getProductFieldLabel( $data ) . ': ' );
	}
	switch ( $data ) {
		case 'id':
			$value = (int) $product->is_type( 'variation' ) ? $product->get_id() : $product->id;
			break;
		case 'sku':
			$value = $product->get_sku();
			break;
		case 'price':
			$value = wc_price( $product->get_price() );
			break;
		case 'regular_price':
			$value = wc_price( $product->get_regular_price() );
			break;
		case 'sale_price':
			$value = wc_price( $product->get_sale_price() );
			break;
		case 'price_html':
			$value = $product->get_price_html();
			break;
		case 'reviews_count':
			$value = count( get_comments( array( 'post_id' => $post->ID, 'approve' => 'approve' ) ) );
			break;
		case 'short_description':
			$value = apply_filters( 'woocommerce_short_description', get_post( $product->get_id() )->post_excerpt );
			break;
		case 'dimensions':
			$units = get_option( 'woocommerce_dimension_unit' );
			$value = $product->length . $units . 'x' . $product->width . $units . 'x' . $product->height . $units;
			break;
		case 'rating_count':
			$value = $product->get_rating_count();
			break;
		case 'weight':
			$value = $product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : '';
			break;
		case 'on_sale':
			$value = $product->is_on_sale() ? 'yes' : 'no'; // TODO: change
			break;
		default:
			$value = $product->$data;
	}

	return strlen( $value ) > 0 ? $label . apply_filters( 'vc_gitem_template_attribute_woocommerce_product_'
		. $data . '_value',
	$value ) : '';
}

/**
 * Gte woocommerce data for order
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_woocommerce_order( $value, $data ) {
	$label = '';
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	require_once WC()->plugin_path() . '/includes/class-wc-order.php';
	$order = new WC_Order( $post->ID );
	if ( preg_match( '/_labeled$/', $data ) ) {
		$data = preg_replace( '/_labeled$/', '', $data );
		$label = apply_filters( 'vc_gitem_template_attribute_woocommerce_order_' . $data . '_label',
		Vc_Vendor_Woocommerce::getOrderFieldLabel( $data ) . ': ' );
	}
	switch ( $data ) {
		case 'id':
			$value = $order->id;
			break;
		case 'order_number':
			$value = $order->get_order_number();
			break;
		case 'total':
			$value = sprintf( get_woocommerce_price_format(), wc_format_decimal( $order->get_total(), 2 ),
			$order->order_currency );
			break;
		case 'payment_method':
			$value = $order->payment_method_title;
			break;
		case 'billing_address_city':
			$value = $order->billing_city;
			break;
		case 'billing_address_country':
			$value = $order->billing_country;
			break;
		case 'shipping_address_city':
			$value = $order->shipping_city;
			break;
		case 'shipping_address_country':
			$value = $order->shipping_country;
			break;
		default:
			$value = $order->$data;
	}

	return strlen( $value ) > 0 ? $label . apply_filters( 'vc_gitem_template_attribute_woocommerce_order_' . $data . '_value', $value ) : '';
}

/**
 * Get woocommerce product add to cart url.
 *
 * @param $value
 * @param $data
 *
 * @since 4.5
 *
 * @return string
 */
function vc_gitem_template_attribute_woocommerce_product_link( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	$link = do_shortcode( '[add_to_cart_url id="' . $post->ID . '"]' );

	return apply_filters( 'vc_gitem_template_attribute_woocommerce_product_link_value', $link );
}

add_filter( 'vc_gitem_template_attribute_woocommerce_product', 'vc_gitem_template_attribute_woocommerce_product', 10, 2 );
add_filter( 'vc_gitem_template_attribute_woocommerce_order', 'vc_gitem_template_attribute_woocommerce_order', 10, 2 );
add_filter( 'vc_gitem_template_attribute_woocommerce_product_link', 'vc_gitem_template_attribute_woocommerce_product_link', 10, 2 );
