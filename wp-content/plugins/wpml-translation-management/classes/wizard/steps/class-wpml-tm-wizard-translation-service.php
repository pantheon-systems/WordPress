<?php

class WPML_TM_Wizard_Translation_Service_Step extends WPML_Twig_Template_Loader {

	private $model = array();

	/** @var WPML_TM_Translation_Services_Admin_Section_Factory $translation_services_factory */
	private $translation_services_factory;

	public function __construct( WPML_TM_Translation_Services_Admin_Section_Factory $translation_services_factory ) {
		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
			)
		);
		$this->translation_services_factory = $translation_services_factory;
	}

	public function render() {
		$this->add_strings();

		$this->build_translation_services_table();

		return $this->get_template()->show( $this->model, 'translation-service-step.twig' );
	}

	private function build_translation_services_table() {
		$this->handle_translation_service_params();

		$renderer = $this->translation_services_factory->create();

		ob_start();
		$renderer->render();

		$this->model['translation_services_table'] = ob_get_clean();
	}

	public function add_strings() {

		$this->model['strings'] = array(
			'title'                     => __( 'Select Translation Service', 'wpml-translation-management' ),
			'sub_title'                 => __( 'Here is the full list of translation services that are integrated with WPML. With any service that you choose, you enjoy a streamlined process.', 'wpml-translation-management' ),
			'skip_translation_services' => __( 'Skip this step - I want to add my own translators', 'wpml-translation-management' ),
			'activate_title'            => __( 'Do you already have an account at %PLACEHOLDER%?', 'wpml-translation-management' ),
			'no_account_text'           => __( 'No, I need to create an account', 'wpml-translation-management' ),
			'yes_account_text'          => __( 'Yes, I already have an account', 'wpml-translation-management' ),
			'connect_site_title'        => __( 'Connect this site to your %PLACEHOLDER% account', 'wpml-translation-management' ),
			'connect_desc'              => __( 'Inside your %PLACEHOLDER% account, you will find an "API token". This token allows WPML to connect to your account at %PLACEHOLDER% to send and receive jobs.', 'wpml-translation-management' ),
			'connect_how_to_find'       => __( 'How to find API token in %PLACEHOLDER%', 'wpml-translation-management' ),
			'continue'                  => __( 'Authenticate & Continue', 'wpml-translation-management' ),
			'go_back'                   => __( 'Use a different translation service', 'wpml-translation-management' ),
			'api_token'                 => __( 'API token:', 'wpml-translation-management' ),
		);
	}

	private function handle_translation_service_params() {

		$query_args = array();
		foreach ( array( 'orderby', 'order', 'paged' ) as $param ) {
			if ( isset( $_POST[ $param ] ) ) {
				$query_args[ $param ] = $_POST[ $param ];
				$_GET[ $param ]       = $_POST[ $param ];
			}
		}
		if ( ! empty( $query_args ) ) {
			$_SERVER['REQUEST_URI'] = add_query_arg( $query_args, $_SERVER['REQUEST_URI'] );
		}
	}
}