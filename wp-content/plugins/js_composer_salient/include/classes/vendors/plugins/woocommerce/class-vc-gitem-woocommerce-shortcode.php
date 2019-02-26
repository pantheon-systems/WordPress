<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Vc_Gitem_Woocommerce_Shortcode extends WPBakeryShortCode {
	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed|void
	 */
	protected function content( $atts, $content = null ) {
		$key = '';
		/**
		 * @var string $el_class
		 * @var string $post_type
		 * @var string $product_field_key
		 * @var string $order_field_key
		 * @var string $product_custom_key
		 * @var string $order_custom_key
		 * @var string $show_label
		 * @var string $align
		 */
		$atts = shortcode_atts( array(
			'el_class' => '',
			'post_type' => 'product',
			'product_field_key' => 'sku',
			'product_custom_key' => '',
			'order_field_key' => 'order_number',
			'order_custom_key' => '',
			'show_label' => '',
			'align' => '',
		), $atts );
		extract( $atts );
		if ( 'product' === $post_type ) {
			$key = '_custom_' === $product_field_key ? $product_custom_key : $product_field_key;
		} elseif ( 'order' === $post_type ) {
			$key = '_custom_' === $order_field_key ? $order_custom_key : $order_field_key;
		}
		if ( 'yes' === $show_label ) {
			$key .= '_labeled';
		}
		$css_class = 'vc_gitem-woocommerce vc_gitem-woocommerce-' . $post_type . '-' . $key
		             . ( strlen( $el_class ) ? ' ' . $el_class : '' )
		             . ( strlen( $align ) ? ' vc_gitem-align-' . $align : '' );

		return '<div class="' . esc_attr( $css_class ) . '">'
		       . '{{ woocommerce_' . $post_type . ':' . $key . ' }}'
		       . '</div>';
	}
}
