<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_AMS_Clients extends WPML_REST_Base {

	private $api;
	private $ams_user_records;

	/** @var WPML_TM_AMS_Translator_Activation_Records $translator_activation_records */
	private $translator_activation_records;

	/**
	 * @var WPML_TM_ATE_AMS_Endpoints
	 */
	private $strings;

	public function __construct(
		WPML_TM_AMS_API $api,
		WPML_TM_AMS_Users $ams_user_records,
		WPML_TM_AMS_Translator_Activation_Records $translator_activation_records,
		WPML_TM_MCS_ATE_Strings $strings
	) {
		parent::__construct( 'wpml/tm/v1' );

		$this->api                           = $api;
		$this->ams_user_records              = $ams_user_records;
		$this->translator_activation_records = $translator_activation_records;
		$this->strings                       = $strings;
	}

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/ams/register_manager',
		                        array(
			                        'methods'  => 'POST',
			                        'callback' => array( $this, 'register_manager' ),
		                        ) );

		parent::register_route( '/ams/synchronize/translators',
		                        array(
			                        'methods'  => 'GET',
			                        'callback' => array( $this, 'synchronize_translators' ),
		                        ) );
		parent::register_route( '/ams/synchronize/managers',
		                        array(
			                        'methods'  => 'GET',
			                        'callback' => array( $this, 'synchronize_managers' ),
		                        ) );

		parent::register_route( '/ams/status',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_status' ),
			) );

		parent::register_route( '/ams/console',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_console' ),
			) );
	}

	/**
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function register_manager() {
		$current_user = wp_get_current_user();
		$translators  = $this->ams_user_records->get_translators();
		$managers     = $this->ams_user_records->get_managers();

		$result = $this->api->register_manager( $current_user->ID,
		                                        $current_user,
		                                        $translators,
		                                        $managers );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array( 'enabled' => $result );
	}

	/**
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_translators() {
		$translators = $this->ams_user_records->get_translators();

		$result = $this->api->synchronize_translators( $translators );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$this->translator_activation_records->update( $result['translators'] );

		return array( 'result' => $result );
	}

	/**
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_managers() {
		$managers = $this->ams_user_records->get_managers();

		$result = $this->api->synchronize_managers( $managers );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array( 'result' => $result );
	}

	/**
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_status() {
		return $this->api->get_status();
	}

	public function get_console() {
		return $this->strings->get_auto_login();
	}

	function get_allowed_capabilities( WP_REST_Request $request ) {
		return array( 'manage_translations', 'manage_options' );
	}

}
