<?php

class WPML_ST_Element_Slug_Translation_UI_Model {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_ST_Slug_Translation_Settings $settings */
	private $settings;

	/** @var WPML_Slug_Translation_Records $slug_records */
	private $slug_records;

	/** @var WPML_Element_Sync_Settings $sync_settings */
	private $sync_settings;

	/** @var WPML_Simple_Language_Selector $lang_selector */
	private $lang_selector;

	public function __construct(
		SitePress $sitepress,
		WPML_ST_Slug_Translation_Settings $settings,
		WPML_Slug_Translation_Records $slug_records,
		WPML_Element_Sync_Settings $sync_settings,
		WPML_Simple_Language_Selector $lang_selector
	) {
		$this->sitepress         = $sitepress;
		$this->settings          = $settings;
		$this->slug_records      = $slug_records;
		$this->sync_settings     = $sync_settings;
		$this->lang_selector     = $lang_selector;
	}

	/**
	 * @param string                   $type_name
	 * @param WP_Post_Type|WP_Taxonomy $custom_type
	 *
	 * @return null|array
	 */
	public function get( $type_name, $custom_type ) {
		$has_rewrite_slug   = isset( $custom_type->rewrite['slug'] ) && $custom_type->rewrite['slug'];
		$is_translated_mode = $this->sync_settings->is_sync( $type_name );
		$is_slug_translated = $this->settings->is_translated( $type_name );

		if ( ! $has_rewrite_slug || ! $this->settings->is_enabled() ) {
			return null;
		}

		$original_slug_and_lang = $this->get_original_slug_and_lang( $type_name, $custom_type );
		$slug_translations      = $this->get_translations( $type_name );

		$model = array(
			'strings' => array(
				'toggle_slugs_table' => sprintf( __( 'Set different slugs in different languages for %s.', 'wpml-string-translation' ), $custom_type->labels->name ),
				'slug_status_incomplete' => __( "Not marked as 'complete'. Press 'Save' to enable.", 'wpml-string-translation' ),
				'original_label' => __( '(original)', 'wpml-string-translation' ),
			),
			'css_class_wrapper' => 	$is_translated_mode ? '' : 'hidden',
			'type_name' => $type_name,
			'slugs' => array(),
			'has_missing_translations_message' => '',
		);

		if ( $is_slug_translated && ! $original_slug_and_lang->is_registered ) {
			$model['has_missing_translations_message'] = sprintf(
				esc_html__(
					'%s slugs are set to be translated, but they are missing their translation',
					'wpml-string-translation'
				),
				$custom_type->labels->name
			);
		}

		$languages = $this->get_languages( $original_slug_and_lang->language );

		foreach ( $languages as $code => $language ) {
			$slug                       = new stdClass();
			$slug->value                = ! empty( $slug_translations[ $code ]['value'] )
				? $slug_translations[ $code ]['value'] : '';
			$slug->placeholder          = $original_slug_and_lang->value . ' @' . $code;
			$slug->input_id             = sprintf( 'translate_slugs[%s][langs][%s]', $type_name, $code );
			$slug->language_flag        = $this->sitepress->get_flag_img( $code );
			$slug->language_name        = $language['display_name'];
			$slug->language_code        = $code;
			$slug->is_original          = $code == $original_slug_and_lang->language;
			$slug->status_is_incomplete = isset( $slug_translations[ $code ] )
			                              && ICL_TM_COMPLETE != $slug_translations[ $code ]['status'];

			if ( $slug->is_original ) {
				$slug->value 			 = $original_slug_and_lang->value;
				$slug->language_selector = $this->lang_selector->render(
					array(
						'name'               => 'translate_slugs[' . $type_name . '][original]',
						'selected'           => $code,
						'show_please_select' => false,
						'echo'               => false,
						'class'              => 'js-translate-slug-original',
						'data'               => array( 'slug' => $slug->value ),
					)
				);
			}

			$model['slugs'][ $slug->language_code ] = $slug;
		}

		return $model;
	}

	/**
	 * @param string                   $type_name
	 * @param WP_Post_Type|WP_Taxonomy $custom_type
	 *
	 * @return stdClass
	 */
	private function get_original_slug_and_lang( $type_name, $custom_type ) {
		$original_slug_and_lang = $this->slug_records->get_original_slug_and_lang( $type_name );

		if ( $original_slug_and_lang ) {
			$original_slug_and_lang->is_registered = true;
		} else {
			$original_slug_and_lang                = new stdClass();
			$original_slug_and_lang->is_registered = false;
			$original_slug_and_lang->value         = isset( $custom_type->slug )
				? $custom_type->slug : $custom_type->rewrite['slug'];
			$original_slug_and_lang->language      = $this->sitepress->get_default_language();
		}

		return $original_slug_and_lang;
	}

	/**
	 * @param string $type_name
	 *
	 * @return array
	 */
	private function get_translations( $type_name ) {
		$translations = array();
		$rows         = $this->slug_records->get_element_slug_translations( $type_name, false );

		foreach( $rows as $row ) {
			$translations[ $row->language ] = array(
				'value' => $row->value,
				'status' => $row->status
			);
		}

		return $translations;
	}

	/**
	 * @param string $string_lang
	 *
	 * @return array
	 */
	private function get_languages( $string_lang ) {
		$languages = $this->sitepress->get_active_languages();

		if ( ! in_array( $string_lang, array_keys( $languages ) ) ) {
			$all_languages             = $this->sitepress->get_languages();
			$languages[ $string_lang ] = $all_languages[ $string_lang ];
		}

		return $languages;
	}
}
