<?php
/**
 * @author OnTheGo Systems
 */

class WPML_TM_REST_Settings_Translation_Editor extends WPML_REST_Base {

	private $sitepress;

	/**
	 * WPML_TM_REST_AMS_Clients constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		parent::__construct( 'wpml/tm/v1' );

		$this->sitepress = $sitepress;
	}

	function add_hooks() {
		$this->register_routes();
	}

	function register_routes() {
		parent::register_route( '/settings/translation_editor',
		                        array(
			                        'methods'  => 'POST',
			                        'callback' => array( $this, 'set_translation_editor' ),
			                        'args'     => array(
				                        'editor_type' => array(
					                        'required'          => true,
					                        'validate_callback' => array( $this, 'validate_editor_type' ),
					                        'sanitize_callback' => array( 'WPML_REST_Arguments_Sanitation', 'string' ),
				                        ),
			                        ),
		                        ) );
	}

	public function get_allowed_capabilities( WP_REST_Request $request ) {
		return WPML_Manage_Translations_Role::CAPABILITY;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 */
	public function set_translation_editor( WP_REST_Request $request ) {
		$editor_type = $request->get_param( 'editor_type' );

		$tm_settings                           = $this->sitepress->get_setting( 'translation-management', array() );
		$tm_settings['doc_translation_method'] = $editor_type;

		$result = $this->sitepress->set_setting( 'translation-management', $tm_settings, true );

		return array( 'saved' => (int) $result );
	}

	public function validate_editor_type( $value, $request, $key ) {
		$valid_types = array(
			strtoupper( (string) ICL_TM_TMETHOD_MANUAL ),
			strtoupper( (string) ICL_TM_TMETHOD_EDITOR ),
			strtoupper( ICL_TM_TMETHOD_ATE ),
		);

		return in_array( strtoupper( $value ), $valid_types, true );
	}
}
