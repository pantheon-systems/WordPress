<?php

/**
 * @author OnTheGo Systems
 */
abstract class WPML_TM_ATE_Required_Rest_Base extends WPML_REST_Base {
	/**
	 * WPML_TM_ATE_Required_Rest_Base constructor.
	 */
	public function __construct() {
		parent::__construct( 'wpml/tm/v1' );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function validate_permission( WP_REST_Request $request ) {
		return WPML_TM_ATE_Status::is_enabled() && parent::validate_permission( $request );
	}
}