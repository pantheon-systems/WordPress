<?php

class WPML_Theme_Plugin_Localization_UI_Hooks {

	/** @var WPML_Theme_Plugin_Localization_UI */
	private $localization_ui;

	/** @var WPML_Theme_Plugin_Localization_Options_UI */
	private $options_ui;

	/**
	 * WPML_Theme_Plugin_Localization_UI_Hooks constructor.
	 *
	 * @param WPML_Theme_Plugin_Localization_UI $localization_ui
	 * @param WPML_Theme_Plugin_Localization_Options_UI $options_ui
	 */
	public function __construct(
		WPML_Theme_Plugin_Localization_UI $localization_ui,
		WPML_Theme_Plugin_Localization_Options_UI $options_ui ) {

		$this->localization_ui = $localization_ui;
		$this->options_ui = $options_ui;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wpml_custom_localization_type', array( $this, 'render_options_ui' ), 1 );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'wpml-theme-plugin-localization', ICL_PLUGIN_URL . '/res/css/theme-plugin-localization.css', array( 'wpml-tooltip' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_script( 'wpml-theme-plugin-localization', ICL_PLUGIN_URL . '/res/js/theme-plugin-localization.js', array( 'jquery' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_script( 'wpml-tooltip' );
	}

	public function render_options_ui() {
		echo $this->localization_ui->render( $this->options_ui );
	}
}