<?php

/**
 * Class WPML_TF_XML_RPC_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_XML_RPC_Hooks implements IWPML_Action {

	/** @var WPML_TF_XML_RPC_Feedback_Update_Factory $xml_rpc_feedback_update_factory */
	private $xml_rpc_feedback_update_factory;

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct(
		WPML_TF_XML_RPC_Feedback_Update_Factory $xml_rpc_feedback_update_factory,
		WPML_WP_API $wp_api
	) {
		$this->xml_rpc_feedback_update_factory = $xml_rpc_feedback_update_factory;
		$this->wp_api                          = $wp_api;
	}

	public function add_hooks() {
		if ( $this->wp_api->constant( 'XMLRPC_REQUEST' ) ) {
			add_filter( 'xmlrpc_methods', array( $this, 'add_tf_xmlrpc_methods' ) );
		}
	}

	/**
	 * @param array $methods
	 *
	 * @return array
	 */
	public function add_tf_xmlrpc_methods( $methods ) {
		$methods['translationproxy.update_feedback_status'] = array( $this, 'update_feedback_status' );
		return $methods;
	}

	/** @param array $args */
	public function update_feedback_status( array $args ) {
		$xml_rpc_feedback_update = $this->xml_rpc_feedback_update_factory->create();
		$xml_rpc_feedback_update->set_status( $args );
	}
}
