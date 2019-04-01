<?php

class WPML_TM_Translation_Services_Admin_Section implements IWPML_TM_Admin_Section {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;
	/**
	 * @var mixed $template
	 */
	private $template;

	public function __construct(
		SitePress $sitepress,
		$template
	) {
		$this->sitepress = $sitepress;
		$this->wp_api    = $sitepress->get_wp_api();
		$this->template  = $template;
	}

	public function render() {
		$this->template->render();
	}

	public function get_template() {
		return $this->template;
	}

	/**
	 * @return bool
	 */
	public function is_visible() {
		return ! $this->wp_api->constant( 'ICL_HIDE_TRANSLATION_SERVICES' ) &&
		       ( $this->wp_api->constant( 'WPML_BYPASS_TS_CHECK' ) || ! $this->sitepress->get_setting( 'translation_service_plugin_activated' ) );
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return 'translation-services';
	}

	/**
	 * @return string|array
	 */
	public function get_capabilities() {
		return array( WPML_Manage_Translations_Role::CAPABILITY, 'manage_options' );
	}

	/**
	 * @return string
	 */
	public function get_caption() {
		return __( 'Translation Services', 'wpml-translation-management' );
	}

	/**
	 * @return callable
	 */
	public function get_callback() {
		return array( $this, 'render' );
	}
}