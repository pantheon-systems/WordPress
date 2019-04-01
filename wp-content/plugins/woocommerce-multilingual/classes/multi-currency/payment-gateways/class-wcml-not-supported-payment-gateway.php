<?php

/**
 * Class WCML_Not_Supported_Payment_Gateway
 */
class WCML_Not_Supported_Payment_Gateway extends WCML_Payment_Gateway{

	const TEMPLATE = 'not-supported.twig';

	protected function get_output_model() {
		return array(
			'strings'       => array(
				'not_supported' => __( 'Not yet supported', 'woocommerce-multilingual' )
			),
			'gateway_title' => $this->get_title()
		);
	}

	protected function get_output_template() {
		return self::TEMPLATE;
	}

}