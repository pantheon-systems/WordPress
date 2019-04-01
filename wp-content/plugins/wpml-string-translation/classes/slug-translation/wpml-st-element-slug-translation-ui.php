<?php

class WPML_ST_Element_Slug_Translation_UI {

	const TEMPLATE_FILE = 'slug-translation-ui.twig';

	/** @var WPML_ST_Element_Slug_Translation_UI_Model $model */
	private $model;

	/** @var IWPML_Template_Service $template_service */
	private $template_service;

	public function __construct(
		WPML_ST_Element_Slug_Translation_UI_Model $model,
		IWPML_Template_Service $template_service
	) {
		$this->model             = $model;
		$this->template_service  = $template_service;
	}

	/** @return WPML_ST_Element_Slug_Translation_UI */
	public function init() {
		wp_enqueue_script(
			'wpml-custom-type-slug-ui',
			WPML_ST_URL . '/res/js/wpml-custom-type-slug-ui.js',
			array( 'jquery' ),
			WPML_ST_VERSION,
			true
		);

		return $this;
	}

	/**
	 * @param string                   $type_name
	 * @param WP_Post_Type|WP_Taxonomy $custom_type
	 *
	 * @return string
	 */
	public function render( $type_name, $custom_type ) {
		$model = $this->model->get( $type_name, $custom_type );

		if ( ! $model ) {
			return '';
		}

		if ( ! empty( $model['has_missing_translations_message'] ) ) {
			ICL_AdminNotifier::displayInstantMessage(
				$model['has_missing_translations_message'],
				'error',
				'below-h2',
				false
			);
		}

		return $this->template_service->show( $model, self::TEMPLATE_FILE );
	}
}
