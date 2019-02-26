<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_XLIFF extends WPML_TM_ATE_Required_Rest_Base {
	const CAPABILITY = 'translate';

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/xliff/fetch/(?P<jobId>\d+)',
		                        array(
			                        'methods'  => 'GET',
			                        'callback' => array( $this, 'fetch_xliff' ),
		                        ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function fetch_xliff( WP_REST_Request $request ) {
		$result = null;

		$wpml_translation_job_factory = wpml_tm_load_job_factory();
		$iclTranslationManagement     = wpml_load_core_tm();

		$job_id = $request->get_param( 'jobId' );

		$writer = new WPML_TM_Xliff_Writer( $wpml_translation_job_factory );
		$xliff  = base64_encode( $writer->generate_job_xliff( $job_id ) );

		$job = $iclTranslationManagement->get_translation_job( (int) $job_id, false, false, 1 );

		$result = array(
			'content'    => $xliff,
			'sourceLang' => $job->source_language_code,
			'targetLang' => $job->language_code,
		);

		return $result;
	}

	function get_allowed_capabilities( WP_REST_Request $request ) {
		return self::CAPABILITY;
	}
}
