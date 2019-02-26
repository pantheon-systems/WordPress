<?php

/**
 * Class WPML_TF_Frontend_Hooks_Factory
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Hooks_Factory implements IWPML_Frontend_Action_Loader, IWPML_Deferred_Action_Loader {

	/** @var  WPML_Queried_Object $queried_object */
	private $queried_object;

	/** @var WPML_TF_Frontend_Display_Requirements $display_requirements */
	private $display_requirements;

	/**
	 * WPML_TF_Frontend_Hooks_Factory constructor.
	 *
	 * @param WPML_Queried_Object                   $queried_object
	 * @param WPML_TF_Frontend_Display_Requirements $display_requirements
	 */
	public function __construct(
		WPML_Queried_Object $queried_object = null,
		WPML_TF_Frontend_Display_Requirements $display_requirements = null
	) {
		$this->queried_object       = $queried_object;
		$this->display_requirements = $display_requirements;
	}

	/**
	 * The frontend hooks must be loaded when the request has been parsed (in "wp")
	 * to avoid unnecessary instantiation if the current page is not a translation
	 *
	 * @return string
	 */
	public function get_load_action() {
		return 'wp';
	}

	/**
	 * @return null|WPML_TF_Frontend_Hooks
	 */
	public function create() {
		global $sitepress;

		$hooks = null;

		if ( $this->get_display_requirements()->verify() ) {
			$template_loader = new WPML_Twig_Template_Loader(
				array( WPML_PLUGIN_PATH . WPML_TF_Frontend_Feedback_View::TEMPLATE_FOLDER )
			);

			$template_service = $template_loader->get_template();

			$settings_reader = new WPML_TF_Settings_Read();
			/** @var WPML_TF_Settings $settings */
			$settings = $settings_reader->get( 'WPML_TF_Settings' );

			$frontend_feedback_form = new WPML_TF_Frontend_Feedback_View(
				$template_service,
				$sitepress,
				$this->get_queried_object(),
				$settings
			);

			$hooks = new WPML_TF_Frontend_Hooks(
				$frontend_feedback_form,
				new WPML_TF_Frontend_Scripts(),
				new WPML_TF_Frontend_Styles()
			);
		}

		return $hooks;
	}

	/**
	 * @return WPML_Queried_Object
	 */
	private function get_queried_object() {
		global $sitepress;

		if ( ! $this->queried_object ) {
			$this->queried_object = new WPML_Queried_Object( $sitepress );
		}

		return $this->queried_object;
	}

	/**
	 * @return WPML_TF_Frontend_Display_Requirements
	 */
	private function get_display_requirements() {
		if ( ! $this->display_requirements ) {
			$settings_read = new WPML_TF_Settings_Read();
			$settings      = $settings_read->get( 'WPML_TF_Settings' );
			/** @var WPML_TF_Settings $settings */
			$this->display_requirements = new WPML_TF_Frontend_Display_Requirements( $this->get_queried_object(), $settings );
		}

		return $this->display_requirements;
	}
}
