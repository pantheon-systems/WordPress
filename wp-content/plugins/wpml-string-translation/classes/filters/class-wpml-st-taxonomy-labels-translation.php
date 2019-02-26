<?php

class WPML_ST_Taxonomy_Labels_Translation implements IWPML_Action {

	const NONCE_TAXONOMY_TRANSLATION = 'wpml_taxonomy_translation_nonce';
	const PRIORITY_GET_LABEL = 10;

	/** @var WPML_ST_Taxonomy_Strings $taxonomy_strings */
	private $taxonomy_strings;

	/** @var WPML_ST_Tax_Slug_Translation_Settings $slug_translation_settings */
	private $slug_translation_settings;

	/** @var WPML_Super_Globals_Validation $super_globals */
	private $super_globals;

	/** @var array $active_languages */
	private $active_languages;

	public function __construct(
		WPML_ST_Taxonomy_Strings $taxonomy_strings,
		WPML_ST_Tax_Slug_Translation_Settings $slug_translation_settings,
		WPML_Super_Globals_Validation $super_globals,
		array $active_languages
	) {
		$this->taxonomy_strings          = $taxonomy_strings;
		$this->slug_translation_settings = $slug_translation_settings;
		$this->super_globals             = $super_globals;
		$this->active_languages          = $active_languages;
	}

	public function add_hooks() {
		add_filter( 'gettext_with_context', array( $this, 'block_translation_and_init_strings' ), PHP_INT_MAX, 4 );
		add_filter( 'wpml_label_translation_data', array( $this, 'get_label_translations' ), self::PRIORITY_GET_LABEL, 2 );
		add_action( 'wp_ajax_wpml_tt_save_labels_translation', array( $this, 'save_label_translations' ) );
		add_action( 'wp_ajax_wpml_tt_change_tax_strings_language', array( $this, 'change_taxonomy_strings_language' ) );
	}

	/**
	 * @param string $translation
	 * @param string $text
	 * @param string $gettext_context
	 * @param string $domain
	 *
	 * @return mixed
	 */
	public function block_translation_and_init_strings( $translation, $text, $gettext_context, $domain ) {
		if ( WPML_ST_Taxonomy_Strings::CONTEXT_GENERAL === $gettext_context
		     || WPML_ST_Taxonomy_Strings::CONTEXT_SINGULAR === $gettext_context
		) {
			$this->taxonomy_strings->create_string_if_not_exist( $text, $gettext_context, $domain );
			$this->taxonomy_strings->add_to_translated_with_gettext_context( $text, $domain );

			// We need to return the original string here so the rest of
			// the label translation UI works.
			return $text;
		}

		return $translation;
	}

	/**
	 * @param        $false
	 * @param string $taxonomy
	 *
	 * @return array|null
	 */
	public function get_label_translations( $false, $taxonomy ) {
		list( $general, $singular, $slug ) = $this->taxonomy_strings->get_taxonomy_strings( $taxonomy );

		if ( ! $general || ! $singular || ! $slug ) {
			return null;
		}

		$source_lang = $general->get_language();

		$general_translations  = $this->get_translations( $general );
		$singular_translations = $this->get_translations( $singular );
		$slug_translations     = $this->get_translations( $slug );

		$data = array(
			'st_default_lang' => $source_lang,
		);

		foreach ( array_keys( $this->active_languages ) as $lang ) {
			if ( $lang === $source_lang ) {
				continue;
			}

			$data[ $lang ]['general']  = $this->get_translation_value( $lang, $general_translations );
			$data[ $lang ]['singular'] = $this->get_translation_value( $lang, $singular_translations );
			$data[ $lang ]['slug']     = $this->get_translation_value( $lang, $slug_translations );

			$data[ $lang ] = array_filter( $data[ $lang ] );
		}

		$data[ $source_lang ] = array(
			'general'                      => $general->get_value(),
			'singular'                     => $singular->get_value(),
			'slug'                         => $slug->get_value(),
			'original'                     => true,
			'globalSlugTranslationEnabled' => $this->slug_translation_settings->is_enabled(),
			'showSlugTranslationField'     => true,
		);

		return $data;
	}

	/**
	 * @param WPML_ST_String $string
	 *
	 * @return array
	 */
	private function get_translations( WPML_ST_String $string ) {
		$translations = array();

		foreach ( $string->get_translations() as $translation ) {
			$translations[ $translation->language ] = $translation;
		}

		return $translations;
	}

	/**
	 * @param string $lang
	 * @param array  $translations
	 *
	 * @return string|null
	 */
	private function get_translation_value( $lang, array $translations ) {
		$value = null;

		if ( isset( $translations[ $lang ] ) ) {
			if ( $translations[ $lang ]->value ) {
				$value = $translations[ $lang ]->value;
			} elseif ( $translations[ $lang ]->mo_string ) {
				$value = $translations[ $lang ]->mo_string;
			}
		}

		return $value;
	}

	public function save_label_translations() {
		if ( ! $this->check_nonce() ) {
			return;
		}

		$general_translation  = $this->get_string_var_from_post( 'plural' );
		$singular_translation = $this->get_string_var_from_post( 'singular' );
		$slug_translation     = $this->get_string_var_from_post( 'slug' );
		$taxonomy_name        = $this->get_string_var_from_post( 'taxonomy' );
		$language             = $this->get_string_var_from_post( 'taxonomy_language_code' );

		if ( $general_translation && $singular_translation && $taxonomy_name && $language ) {
			list( $general, $singular, $slug ) = $this->taxonomy_strings->get_taxonomy_strings( $taxonomy_name );

			if ( $general && $singular && $slug ) {
				$general->set_translation( $language, $general_translation, ICL_STRING_TRANSLATION_COMPLETE );
				$singular->set_translation( $language, $singular_translation, ICL_STRING_TRANSLATION_COMPLETE );
				$slug->set_translation( $language, $slug_translation, ICL_STRING_TRANSLATION_COMPLETE );

				$slug_translation_enabled = $this->has_slug_translation( $slug );
				$this->slug_translation_settings->set_type( $taxonomy_name, $slug_translation_enabled );
				$this->slug_translation_settings->save();

				$result = array(
					'general'  => $general_translation,
					'singular' => $singular_translation,
					'slug'     => $slug_translation,
					'lang'     => $language
				);

				wp_send_json_success( $result );
				return;
			}
		}

		wp_send_json_error();
	}

	private function has_slug_translation( WPML_ST_String $slug ) {
		$translations = $slug->get_translations();

		if ( $translations ) {
			foreach ( $translations as $translation ) {
				if ( trim( $translation->value ) && ICL_STRING_TRANSLATION_COMPLETE === (int) $translation->status ) {
					return true;
				}
			}
		}

		return false;
	}

	public function change_taxonomy_strings_language() {
		if ( ! $this->check_nonce() ) {
			return;
		}

		$taxonomy    = $this->get_string_var_from_post( 'taxonomy' );
		$source_lang = $this->get_string_var_from_post( 'source_lang' );

		if ( ! $taxonomy || ! $source_lang ) {
			wp_send_json_error( __( 'Missing parameters', 'wpml-string-translation' ) );
			return;
		}

		list( $general_string, $singular_string, $slug ) = $this->taxonomy_strings->get_taxonomy_strings( $taxonomy );

		$general_string->set_language( $source_lang );
		$singular_string->set_language( $source_lang );
		$slug->set_language( $source_lang );

		wp_send_json_success();
	}

	/**
	 * @param string $key
	 *
	 * @return false|string
	 */
	private function get_string_var_from_post( $key ) {
		$value = $this->super_globals->post( $key );
		return null !== $value ? sanitize_text_field( $value ) : false;
	}

	private function check_nonce() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], self::NONCE_TAXONOMY_TRANSLATION ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'wpml-string-translation' ) );
			return false;
		}

		return true;
	}
}