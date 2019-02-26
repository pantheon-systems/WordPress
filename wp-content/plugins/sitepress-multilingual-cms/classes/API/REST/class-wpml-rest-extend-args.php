<?php

/**
 * @author OnTheGo Systems
 */
class WPML_REST_Extend_Args implements IWPML_Action {

	const REST_LANGUAGE_ARGUMENT = 'wpml_language';

	/** @var \SitePress $sitepress */
	private $sitepress;

	/** @var string $current_language_backup */
	private $current_language_backup;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	function add_hooks() {
		add_filter( 'rest_endpoints', array( $this, 'rest_endpoints' ) );
		add_filter( 'rest_request_before_callbacks', array( $this, 'rest_request_before_callbacks' ), 10, 3 );
		add_filter( 'rest_request_after_callbacks', array( $this, 'rest_request_after_callbacks' ) );
	}

	/**
	 * Adds the `wpml_language` argument (optional) to all REST calls with arguments.
	 *
	 * @param array $endpoints
	 *
	 * @return array
	 */
	public function rest_endpoints( array $endpoints ) {
		$valid_language_codes = $this->get_active_language_codes();

		foreach ( $endpoints as $route => &$endpoint ) {
			foreach ( $endpoint as $key => &$data ) {
				if ( is_numeric( $key ) ) {
					$data['args'][ self::REST_LANGUAGE_ARGUMENT ] = array(
						'type'        => 'string',
						'description' => "WPML's language code",
						'required'    => false,
						'enum'        => $valid_language_codes,
					);
				}
			}
		}

		return $endpoints;
	}

	/**
	 * If `wpml_language` is provided, backups the current language, then switch to the provided one.
	 *
	 * @param \WP_REST_Response|array|mixed $response
	 * @param \WP_REST_Server|array|mixed   $rest_server
	 * @param \WP_REST_Request              $request
	 *
	 * @return mixed
	 */
	public function rest_request_before_callbacks( $response, $rest_server, $request ) {
		$this->current_language_backup = null;
		$current_language              = $this->sitepress->get_current_language();
		$rest_language                 = $request->get_param( self::REST_LANGUAGE_ARGUMENT );

		if ( $rest_language && $rest_language !== $current_language ) {
			$this->current_language_backup = $current_language;
			$this->sitepress->switch_lang( $rest_language );
		}

		return $response;
	}


	/**
	 * Restore the backup language, if set.
	 *
	 * @param \WP_REST_Response|array|mixed $response
	 *
	 * @return mixed
	 */
	public function rest_request_after_callbacks( $response ) {
		if ( $this->current_language_backup ) {
			$this->sitepress->switch_lang( $this->current_language_backup );
		}

		return $response;
	}

	/**
	 * @return array
	 */
	private function get_active_language_codes() {
		return array_keys( $this->sitepress->get_active_languages() );
	}
}
