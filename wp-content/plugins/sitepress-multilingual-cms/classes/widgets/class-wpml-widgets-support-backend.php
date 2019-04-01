<?php

/**
 * This code is inspired by WPML Widgets (https://wordpress.org/plugins/wpml-widgets/),
 * created by Jeroen Sormani
 *
 * @author OnTheGo Systems
 */
class WPML_Widgets_Support_Backend implements IWPML_Action {
	private $active_languages;
	private $template_service;

	/**
	 * WPML_Widgets constructor.
	 *
	 * @param array                  $active_languages
	 * @param IWPML_Template_Service $template_service
	 */
	public function __construct( array $active_languages, IWPML_Template_Service $template_service ) {
		$this->active_languages = $active_languages;
		$this->template_service = $template_service;
	}

	public function add_hooks() {
		add_action( 'in_widget_form', array( $this, 'language_selector' ), 10, 3 );
		add_filter( 'widget_update_callback', array( $this, 'update' ), 10, 4 );
	}

	/**
	 * @param WP_Widget|null $widget
	 * @param string|null    $form
	 * @param array          $instance
	 */
	public function language_selector( $widget, $form, $instance ) {

		$languages        = $this->active_languages;
		$languages['all'] = array(
			'code'        => 'all',
			'native_name' => __( 'All Languages', 'sitepress' ),
		);

		$model = array(
			'strings'           => array(
				'label' => __( 'Display on language:', 'sitepress' ),
			),
			'languages'         => $languages,
			'selected_language' => isset( $instance['wpml_language'] ) ? $instance['wpml_language'] : 'all',
			'nonce'             => wp_create_nonce( 'wpml-language-' . $widget->id ),
		);

		echo $this->template_service->show( $model, 'language-selector.twig' );
	}

	/**
	 * @param array     $instance
	 * @param array     $new_instance
	 * @param array     $old_instance
	 * @param WP_Widget $widget_instance
	 *
	 * @return array
	 */
	public function update( $instance, $new_instance, $old_instance, $widget_instance ) {
		$is_valid = array_key_exists( 'wpml-language-nonce', $_POST ) && wp_verify_nonce( $_POST['wpml-language-nonce'], 'wpml-language-' . $widget_instance->id );
		if ( $is_valid && array_key_exists( 'wpml_language', $_POST ) ) {
			$new_language = filter_var( $_POST['wpml_language'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );

			if ( 'all' === $new_language || array_key_exists( $new_language, $this->active_languages ) ) {
				$instance['wpml_language'] = $new_language;
			}
		}

		return $instance;
	}
}
