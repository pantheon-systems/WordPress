<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_ATE_Public extends WPML_TM_ATE_Required_Rest_Base {
	/**
	 * @var WPML_TM_ATE_Jobs
	 */
	private $ate_jobs;
	/**
	 * @var WPML_TM_ATE_Authentication
	 */
	private $auth;

	/**
	 * WPML_TM_REST_ATE_Jobs constructor.
	 *
	 * @param WPML_TM_ATE_Authentication $auth
	 * @param WPML_TM_ATE_Jobs           $ate_jobs
	 */
	public function __construct( WPML_TM_ATE_Authentication $auth, WPML_TM_ATE_Jobs $ate_jobs ) {
		parent::__construct();
		$this->auth     = $auth;
		$this->ate_jobs = $ate_jobs;
	}

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/ate/jobs/receive/(?P<wpmlJobId>\d+)',
		                        array(
			                        'methods'             => 'POST',
			                        'callback'            => array( $this, 'receive_ate_job' ),
			                        'args'                => array(
				                        'xliff_url' => array(
					                        'required'          => true,
					                        'type'              => 'string',
					                        'validate_callback' => array( 'WPML_REST_Arguments_Validation', 'url' ),
					                        'sanitize_callback' => array( 'WPML_REST_Arguments_Sanitation', 'url' ),
				                        ),
				                        'signature' => array(
					                        'required'          => true,
					                        'type'              => 'string',
					                        'sanitize_callback' => array( 'WPML_REST_Arguments_Sanitation', 'string' ),
				                        ),
			                        ),
			                        'permission_callback' => array( $this, 'validate_external' )
		                        ) );
	}

	public function get_allowed_capabilities( WP_REST_Request $request ) {
		return array();
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function receive_ate_job( WP_REST_Request $request ) {
		$result = false;

		//		if ( current_user_can( self::CAPABILITY )
		//		     && wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' ) ) {

		$wpml_job_id = $request->get_param( 'wpmlJobId' );
		$xliff_url   = $request->get_param( 'xliff_url' );

		//@todo retrieve the XLIFF file
		//@todo update the job

		return $result;
	}

	public function validate_external( WP_REST_Request $request ) {
		$site_url          = wpml_tm_get_wpml_rest()->get_discovery_url();
		$route             = $request->get_route();
		$url_parts         = wp_parse_url( $site_url );
		$url_parts['path'] .= $route;

		$url_to_sign = http_build_url( $url_parts );

		$signature = $request->get_param( 'signature' );

		$body_params = $request->get_query_params();
		unset( $body_params['signature'] );

		$expected_signature = $this->auth->get_signed_url( 'POST', $url_to_sign, $body_params );

		$is_valid_signature = $expected_signature === $signature;

		return true; //$is_valid_signature;
	}
}
