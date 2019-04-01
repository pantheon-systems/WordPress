<?php

/**
 * Class WPML_TF_Backend_Options_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Options_Hooks implements IWPML_Action {

	const WPML_LOVE_ID = '#lang-sec-10';

	/** @var  WPML_TF_Backend_Options_View $options_view */
	private $options_view;

	/** @var WPML_TF_Backend_Options_Scripts $scripts */
	private $scripts;

	/** @var WPML_TF_Backend_Options_Styles $styles */
	private $styles;

	/** @var WPML_TF_Translation_Service $translation_service */
	private $translation_service;

	/**
	 * WPML_TF_Backend_Options_Hooks constructor.
	 *
	 * @param WPML_TF_Backend_Options_View     $options_view
	 * @param WPML_TF_Backend_Options_Scripts  $scripts
	 * @param WPML_TF_Backend_Options_Styles   $styles
	 * @param WPML_TF_Translation_Service $translation_service
	 */
	public function __construct(
		WPML_TF_Backend_Options_View $options_view,
		WPML_TF_Backend_Options_Scripts $scripts,
		WPML_TF_Backend_Options_Styles $styles,
		WPML_TF_Translation_Service $translation_service
	) {
		$this->options_view        = $options_view;
		$this->scripts             = $scripts;
		$this->styles              = $styles;
		$this->translation_service = $translation_service;
	}

	/**
	 * Method add_hooks
	 */
	public function add_hooks() {
		if ( $this->translation_service->allows_translation_feedback() ) {
			add_action( 'wpml_after_settings', array( $this, 'display_options_ui' ) );
			add_filter( 'wpml_admin_languages_navigation_items', array( $this, 'insert_navigation_item' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_action' ) );
		}
	}

	/**
	 * Method to render the options UI
	 */
	public function display_options_ui() {
		echo $this->options_view->render();
	}

	/**
	 * @param array $items
	 *
	 * @return mixed
	 */
	public function insert_navigation_item( $items ) {
		$insert_key_index = array_search( self::WPML_LOVE_ID, array_keys( $items ) );
		$insert_position  = false === $insert_key_index ? count( $items ) : $insert_key_index + 1;

		return array_merge(
			array_slice( $items, 0, $insert_position ),
			array( '#wpml-translation-feedback-options' => esc_html__( 'Translation Feedback', 'sitepress' ) ),
			array_slice( $items, $insert_position )
		);
	}

	public function enqueue_scripts_action() {
		$this->scripts->enqueue();
		$this->styles->enqueue();
	}
}
