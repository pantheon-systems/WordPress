<?php

class WPML_Translator_View extends WPML_Twig_Template_Loader implements IWPML_Translation_Roles_View {

	const TEMPLATE_PATH = '/templates/translators';

	/** @var WPML_Language_Collection $languages */
	private $languages;

	public function __construct( WPML_Language_Collection $languages ) {
		$this->languages = $languages;
		parent::__construct( array( WPML_TM_PATH . self::TEMPLATE_PATH ) );
	}

	public function show( $model, $template ) {
		$model['strings']   = self::get_strings();
		$model['languages'] = $this->languages;

		$model['strings'] = apply_filters(
			'wpml_tm_translators_view_strings',
			$model['strings'],
			true
		);

		return $this->get_template()->show( $model, $template );
	}

	public static function get_strings() {
		return array(
			'edit'           => __( 'Edit user', 'wpml-translation-management' ),
			'remove'         => __( 'Remove', 'wpml-translation-management' ),
			'edit_languages' => __( 'Edit Languages', 'wpml-translation-management' ),
			'no'             => __( 'No', 'wpml-translation-management' ),
		);
	}


}