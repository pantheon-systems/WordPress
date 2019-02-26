<?php

class WPML_TM_Translation_Basket_Dialog_View {

	const TEMPLATE_FILE = 'dialog.twig';

	/** @var IWPML_Template_Service $template_service */
	private $template_service;

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct( IWPML_Template_Service $template_service, WPML_WP_API $wp_api ) {
		$this->template_service = $template_service;
		$this->wp_api           = $wp_api;
	}

	/**
	 * @return string
	 */
	public function render() {
		$model = array(
			'strings'      => self::get_strings(),
			'redirect_url' => $this->wp_api->get_tm_url(),
		);

		return $this->template_service->show( $model, self::TEMPLATE_FILE );
	}

	public static function get_strings() {
		return array(
			'title'               => __( 'Sending for translation', 'wpml-translation-management' ),
			'sent_to_translation' => __( 'Items sent for translation!', 'wpml-translation-management' ),
			'button_done'         => __( 'Done', 'wpml-translation-management' ),
		);
	}
}