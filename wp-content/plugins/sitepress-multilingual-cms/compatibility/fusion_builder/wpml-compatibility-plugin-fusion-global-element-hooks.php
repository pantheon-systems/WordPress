<?php

class WPML_Compatibility_Plugin_Fusion_Global_Element_Hooks implements IWPML_Action {

	const BEFORE_ADD_GLOBAL_ELEMENTS_PRIORITY = 5;
	const GLOBAL_SHORTCODE_START = '[fusion_global id="';

	/** @var IWPML_Current_Language $current_language */
	private $current_language;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var WPML_Custom_Columns $custom_columns */
	private $custom_columns;

	public function __construct(
		IWPML_Current_Language $current_language,
		WPML_Translation_Element_Factory $element_factory,
		WPML_Custom_Columns $custom_columns
	) {
		$this->current_language = $current_language;
		$this->element_factory  = $element_factory;
		$this->custom_columns   = $custom_columns;
	}

	public function add_hooks() {
		add_filter(
			'content_edit_pre',
			array( $this, 'translate_global_element_ids' ),
			self::BEFORE_ADD_GLOBAL_ELEMENTS_PRIORITY
		);

		if ( is_admin() ) {
			add_filter( 'manage_fusion_element_posts_columns', array( $this, 'add_language_column_header' ) );
			add_action( 'manage_fusion_element_custom_column', array( $this, 'add_language_column_content' ), 10, 2 );
		}
	}

	public function translate_global_element_ids( $content ) {
		$pattern = '/' . preg_quote( self::GLOBAL_SHORTCODE_START, '[' ) . '([\d]+)"\]/';
		return preg_replace_callback( $pattern, array( $this, 'replace_global_id' ), $content );
	}

	private function replace_global_id( array $matches ) {
		$global_id       = (int) $matches[1];
		$element         = $this->element_factory->create( $global_id, 'post' );
		$translation     = $element->get_translation( $this->current_language->get_current_language() );

		if ( $translation ) {
			$global_id = $translation->get_element_id();
		}

		return self::GLOBAL_SHORTCODE_START . $global_id . '"]';
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_language_column_header( $columns ) {
		return $this->custom_columns->add_posts_management_column( $columns );
	}

	/** @param string $column_id */
	public function add_language_column_content( $column_id ) {
		$this->custom_columns->add_content_for_posts_management_column( $column_id );
	}
}
