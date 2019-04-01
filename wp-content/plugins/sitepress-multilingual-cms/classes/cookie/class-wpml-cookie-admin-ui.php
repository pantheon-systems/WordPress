<?php

/**
 * Class WPML_Cookie_Admin_UI
 */
class WPML_Cookie_Admin_UI {

	const BOX_TEMPLATE = 'admin-cookie-box.twig';
	const BUTTON_ID    = 'js-wpml-store-frontend-cookie';

	/**
	 * @var WPML_Twig_Template
	 */
	private $template_service;

	/**
	 * @var WPML_Cookie_Setting
	 */
	private $cookie_setting;

	/**
	 * WPML_Cookie_Admin_UI constructor.
	 *
	 * @param WPML_Twig_Template $template_service
	 * @param WPML_Cookie_Setting $cookie_setting
	 */
	public function __construct( WPML_Twig_Template $template_service, WPML_Cookie_Setting $cookie_setting ) {
		$this->template_service = $template_service;
		$this->cookie_setting   = $cookie_setting;
	}

	public function add_hooks() {
		add_action( 'wpml_after_settings', array( $this, 'render_cookie_box' ) );
	}

	public function render_cookie_box() {
		echo $this->template_service->show( $this->get_model(), self::BOX_TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_model() {
		return array(
			'strings' => array(
				'title'       => __( 'Language filtering for AJAX operations', 'sitepress' ),
				'field_name'  => WPML_Cookie_Setting::COOKIE_SETTING_FIELD,
				'field_label' => __( 'Store a language cookie to support language filtering for AJAX', 'sitepress' ),
				'tooltip'     => __( 'Select this option if your theme or plugins use AJAX operations on the front-end, that WPML needs to filter. WPML will set a cookie using JavaScript which will allow it to return the correct content for AJAX operations.', 'sitepress' ),
				'button_text' => __( 'Save', 'sitepress' ),
				'button_id'   => self::BUTTON_ID,
			),
			'ajax_response_id' => WPML_Cookie_Setting_Ajax::AJAX_RESPONSE_ID,
			'nonce_field'      => WPML_Cookie_Setting_Ajax::NONCE_COOKIE_SETTING,
			'nonce_value'      => wp_create_nonce( WPML_Cookie_Setting_Ajax::NONCE_COOKIE_SETTING ),
			'checked'          => checked( $this->cookie_setting->get_setting(), true, false ),
		);
	}
}