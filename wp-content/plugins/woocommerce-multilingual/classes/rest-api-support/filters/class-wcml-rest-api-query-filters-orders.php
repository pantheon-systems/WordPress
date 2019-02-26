<?php

class WCML_REST_API_Query_Filters_Orders{

	/** @var wpdb */
	private $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks(){
		add_filter( 'woocommerce_rest_shop_order_object_query', array( $this, 'filter_orders_by_language' ), 20, 2 );
		add_action( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'filter_order_items_by_language' ), 10, 3 );
	}

	/**
	 * @param array $args
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function filter_orders_by_language( $args, $request ){

		$lang = $request->get_param( 'lang' );

		if( ! is_null( $lang ) && $lang !== 'all' ){

			$args['meta_query'][] = array(
				'key'   => 'wpml_language',
				'value' => strval( $lang )
			);

		}

		return $args;
	}

	/**
	 * Filters the items of an order according to a given languages
	 *
	 * @param WP_REST_Response $response
	 * @param WC_Order $order
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */

	public function filter_order_items_by_language( $response, $order, $request ){

		$lang = get_query_var('lang');
		$order_lang = get_post_meta( $order->get_id(), 'wpml_language', true );

		if( $order_lang !== $lang ){

			foreach( $response->data['line_items'] as $k => $item ){

				$translated_product_id = $this->get_translated_product_id( $item['id'], $lang );
				if( $translated_product_id ){
					$translated_product = get_post( $translated_product_id );
					$response->data['line_items'][$k]['product_id'] = $translated_product_id;
					if( $translated_product->post_type == 'product_variation' ){
						$post_parent = get_post( $translated_product->post_parent );
						$post_name = $post_parent->post_title;
					} else {
						$post_name = $translated_product->post_title;
					}
					$response->data['line_items'][$k]['name'] = $post_name;
				}

			}

		}

		return $response;
	}

	private function get_translated_product_id( $item_id, $lang ){
		$translated_product_id = false;

		$sql = "SELECT meta_value FROM {$this->wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id=%d AND meta_key='_product_id'";
		$product_id = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $item_id ) );

		if( $product_id ) {
			$translated_product_id = apply_filters( 'translate_object_id', $product_id, 'product', true, $lang );
		}

		return $translated_product_id;
	}

}
