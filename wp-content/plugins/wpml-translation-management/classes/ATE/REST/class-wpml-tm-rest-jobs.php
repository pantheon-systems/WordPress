<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_Jobs extends WPML_REST_Base {
	const CAPABILITY = 'translate';

	/**
	 * WPML_TM_REST_Jobs constructor.
	 */
	public function __construct() {
		parent::__construct( 'wpml/tm/v1' );
	}

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/jobs/assign',
		                        array(
			                        'methods'  => 'POST',
			                        'callback' => array( $this, 'assign_job' ),
			                        'args'     => array(
				                        'jobId'        => array(
					                        'required'          => true,
					                        'validate_callback' => array( 'WPML_REST_Arguments_Validation', 'integer' ),
					                        'sanitize_callback' => array( 'WPML_REST_Arguments_Sanitation', 'integer' ),
				                        ),
				                        'translatorId' => array(
					                        'required'          => true,
					                        'validate_callback' => array( 'WPML_REST_Arguments_Validation', 'integer' ),
					                        'sanitize_callback' => array( 'WPML_REST_Arguments_Sanitation', 'integer' ),
				                        ),
			                        ),
		                        ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function assign_job( WP_REST_Request $request ) {
		$result = null;

		$job_id           = $request->get_param( 'jobId' );
		$translator_email = $request->get_param( 'translatorId' );
		$user             = get_user_by( 'ID', $translator_email );

		if ( $user ) {

			$wpml_translation_job_factory = wpml_tm_load_job_factory();

			$job       = $wpml_translation_job_factory->get_translation_job( $job_id, false, 0, true );
			$assign_to = $job->assign_to( $user->ID );
			$result    = array( 'assigned' => $assign_to, );
		}

		return $result;
	}

	function get_allowed_capabilities( WP_REST_Request $request ) {
		return self::CAPABILITY;
	}

	public function validate_translator( $value, $request, $key ) {
		if ( WPML_REST_Arguments_Validation::email( $value, $request, $key ) ) {
			$user = get_user_by( 'ID', $value );
			if ( $user && $user->has_cap( self::CAPABILITY ) ) {
				return true;
			}
		}

		return false;
	}
}
