<?php

class WCML_Payment_Method_Filter {
	/** @var array  */
	private $payment_gateway_cache = array();

	/** @var array  */
	private $post_type_cache = array();

	public function add_hooks() {
		add_filter( 'get_post_metadata', array( $this, 'payment_method_string' ), 10, 3 );
	}

	public function payment_method_string( $title, $object_id, $meta_key ) {
		if ( '_payment_method_title' === $meta_key && !empty( $title ) && $object_id && 'shop_order' === $this->get_post_type( $object_id ) ) {
			$payment_gateway = $this->get_payment_gateway( $object_id );

			if( isset( $_POST['payment_method'] ) && $payment_gateway->id !== $_POST['payment_method'] && WC()->payment_gateways() ){
				$payment_gateways = WC()->payment_gateways->payment_gateways();
				if( isset( $payment_gateways[ $_POST['payment_method'] ] ) ){
					$payment_gateway = $payment_gateways[ $_POST['payment_method'] ];
				}
			}

			if( $payment_gateway ){
				$title = icl_translate( 'woocommerce', $payment_gateway->id . '_gateway_title', $payment_gateway->title );
			}
		}

		return $title;
	}

	/**
	 * @param int $object_id
	 *
	 * @return string
	 */
	private function get_post_type( $object_id ) {
		if ( ! array_key_exists( $object_id, $this->post_type_cache ) ) {
			$this->post_type_cache[ $object_id ] = get_post_type( $object_id );
		}

		return $this->post_type_cache[ $object_id ];
	}

	/**
	 * @param int $object_id
	 *
	 * @return bool|WC_Payment_Gateway
	 */
	private function get_payment_gateway( $object_id ) {
		if ( ! array_key_exists( $object_id, $this->payment_gateway_cache ) ) {
			remove_filter( 'get_post_metadata', array( $this, 'payment_method_string' ), 10, 3 );
			$payment_gateway = wc_get_payment_gateway_by_order( $object_id );
			add_filter( 'get_post_metadata', array( $this, 'payment_method_string' ), 10, 3 );
			$this->payment_gateway_cache[ $object_id ] = $payment_gateway;
		}

		return $this->payment_gateway_cache[ $object_id ];
	}
}