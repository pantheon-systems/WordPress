<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_MCS_ATE extends WPML_Twig_Template_Loader {
	/**
	 * @var WPML_TM_ATE_Authentication
	 */
	private $authentication;
	private $authentication_data;
	/**
	 * @var WPML_TM_ATE_AMS_Endpoints
	 */
	private $endpoints;
	/**
	 * @var WPML_TM_MCS_ATE_Strings
	 */
	private $strings;

	private $model = array();

	/**
	 *
	 * /**
	 * WPML_TM_MCS_ATE constructor.
	 *
	 * @param WPML_TM_ATE_Authentication $authentication
	 * @param WPML_TM_ATE_AMS_Endpoints  $endpoints
	 *
	 * @param WPML_TM_MCS_ATE_Strings    $strings
	 */
	public function __construct(
		WPML_TM_ATE_Authentication $authentication,
		WPML_TM_ATE_AMS_Endpoints $endpoints,
		WPML_TM_MCS_ATE_Strings $strings
	) {
		parent::__construct( array(
			                     $this->get_template_path(),
		                     ) );

		$this->authentication = $authentication;
		$this->endpoints      = $endpoints;
		$this->strings        = $strings;

		$this->authentication_data = get_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, array() );

		$this->model = array(
			'status_button_text'      => $this->get_status_button_text(),
			'synchronize_button_text' => $this->strings->get_synchronize_button_text(),
		);
	}

	/**
	 * @return string
	 */
	public function get_template_path() {
		return WPML_TM_PATH . '/templates/ATE';
	}

	public function init_hooks() {
		add_action( 'wpml_tm_mcs_' . ICL_TM_TMETHOD_ATE, array( $this, 'render' ) );
	}

	public function get_model() {
		return $this->model;
	}

	public function render() {
		echo $this->get_template()
		          ->show( $this->get_model(), 'mcs-ate-controls.twig' );
	}

	public function get_strings() {
		return $this->strings;
	}

	private function has_translators() {
		/** @var TranslationManagement $iclTranslationManagement */
		global $iclTranslationManagement;

		return $iclTranslationManagement->has_translators();
	}

	/**
	 * @return mixed
	 */
	private function get_status_button_text() {
		return $this->strings->get_current_status_attribute( 'button' );
	}

	/**
	 * @return array
	 */
	public function get_script_data() {
		return array(
			'hasTranslators' => $this->has_translators(),
			'currentStatus'  => $this->strings->get_status(),
			'statuses'       => $this->strings->get_statuses(),
		);
	}

}