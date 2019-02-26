<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_ATE_API extends WPML_TM_ATE_Required_Rest_Base {
	const CAPABILITY_CREATE = 'manage_translations';
	const CAPABILITY_READ   = 'translate';

	private $api;

	/**
	 * WPML_TM_REST_AMS_Clients constructor.
	 *
	 * @param WPML_TM_ATE_API $api
	 */
	public function __construct( WPML_TM_ATE_API $api ) {
		parent::__construct();
		$this->api = $api;
	}

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/ate/jobs',
		                        array(
			                        'methods'  => 'POST',
			                        'callback' => array( $this, 'create_jobs' ),
		                        ) );

		parent::register_route( '/ate/jobs/(?P<ateJobId>\d+)',
		                        array(
			                        'methods'  => 'GET',
			                        'callback' => array( $this, 'get_job' ),
		                        ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function create_jobs( WP_REST_Request $request ) {
		return $this->api->create_jobs( $request->get_params() );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_job( WP_REST_Request $request ) {
		$ate_job_id = $request->get_param( 'ateJobId' );

		return $this->api->get_job( $ate_job_id );
	}

	function get_allowed_capabilities( WP_REST_Request $request ) {
		if ( 'GET' === $request->get_method() ) {
			return array( self::CAPABILITY_CREATE, self::CAPABILITY_READ );
		}

		return self::CAPABILITY_CREATE;
	}
}
