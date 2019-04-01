<?php

class WPML_ST_Taxonomy_Strings {

	const CONTEXT_GENERAL  = 'taxonomy general name';
	const CONTEXT_SINGULAR = 'taxonomy singular name';

	const LEGACY_NAME_PREFIX_GENERAL  = 'taxonomy general name: ';
	const LEGACY_NAME_PREFIX_SINGULAR = 'taxonomy singular name: ';
	const LEGACY_STRING_DOMAIN        = 'WordPress';

	/** @var WPML_Tax_Slug_Translation_Records $slug_translation_records */
	private $slug_translation_records;

	/** @var WPML_ST_String_Factory $string_factory */
	private $string_factory;

	private $translated_with_gettext_context = array();

	public function __construct(
		WPML_Tax_Slug_Translation_Records $slug_translation_records,
		WPML_ST_String_Factory $string_factory
	) {
		$this->slug_translation_records = $slug_translation_records;
		$this->string_factory           = $string_factory;
	}

	/**
	 * @param string $text
	 * @param string $domain
	 */
	public function add_to_translated_with_gettext_context( $text, $domain ) {
		if ( ! in_array( $text, $this->translated_with_gettext_context ) ) {
			$this->translated_with_gettext_context[ $text ] = $domain;
		}
	}

	/**
	 * @param string       $text
	 * @param string       $gettext_context
	 * @param string       $domain
	 * @param false|string $name
	 *
	 * @return int
	 */
	private function find_string_id( $text, $gettext_context = '', $domain = '', $name = false ) {
		$context = $this->get_context( $domain, $gettext_context );
		return $this->string_factory->get_string_id( $text, $context, $name );
	}

	/**
	 * @param string       $text
	 * @param string       $gettext_context
	 * @param string       $domain
	 * @param false|string $name
	 *
	 * @return int
	 */
	public function create_string_if_not_exist( $text, $gettext_context = '', $domain = '', $name = false ) {
		$string_id = $this->find_string_id( $text, $gettext_context, $domain, $name );

		if ( ! $string_id ) {
			$context   = $this->get_context( $domain, $gettext_context );
			$string_id = icl_register_string( $context, $name, $text );
		}

		return $string_id;
	}

	private function get_context( $domain, $gettext_context ) {
		return array(
			'domain'  => $domain,
			'context' => $gettext_context,
		);
	}

	/**
	 * @param string $taxonomy_name
	 *
	 * @return WPML_ST_String[]
	 */
	public function get_taxonomy_strings( $taxonomy_name ) {
		$taxonomy = get_taxonomy( $taxonomy_name );

		if ( $taxonomy && isset( $taxonomy->label ) && isset( $taxonomy->labels->singular_name ) ) {
			$general_string  = $this->get_label_string( $taxonomy->label, 'general' );
			$singular_string = $this->get_label_string( $taxonomy->labels->singular_name, 'singular' );
			$slug_string     = $this->get_slug_string( $taxonomy );

			return array( $general_string, $singular_string, $slug_string );
		}

		return null;
	}

	/**
	 * @param string $value
	 * @param string $general_or_singular
	 *
	 * @return WPML_ST_String|null
	 */
	private function get_label_string( $value, $general_or_singular ) {
		$string    = $this->get_label_string_details( $value, $general_or_singular );
		$string_id = $this->find_string_id( $value, $string['context'], $string['domain'], $string['name'] );

		if ( ! $string_id ) {
			$string_id = $this->create_string_if_not_exist( $value, $string['context'], $string['domain'], $string['name'] );
		}

		if ( $string_id ) {
			return $this->string_factory->find_by_id( $string_id );
		}

		return null;
	}

	/**
	 * @param WP_Taxonomy $taxonomy
	 *
	 * @return WPML_ST_String
	 */
	private function get_slug_string( $taxonomy ) {
		$string_id = $this->slug_translation_records->get_slug_id( $taxonomy->name );

		if ( ! $string_id ) {
			$slug      = isset( $taxonomy->rewrite['slug'] ) ? $taxonomy->rewrite['slug'] : $taxonomy->name;
			$string_id = $this->slug_translation_records->register_slug( $taxonomy->name, $slug );
		}

		return $this->string_factory->find_by_id( $string_id );
	}

	/**
	 * @param string $value
	 * @param string $general_or_singular
	 *
	 * @return array
	 */
	private function get_label_string_details( $value, $general_or_singular ) {
		$string_meta = array(
			'context' => '',
			'domain'  => '',
			'name'    => false,
		);

		if ( $this->is_string_translated_with_gettext_context( $value ) ) {
			$string_meta['domain']  = $this->get_domain_for_taxonomy( $value );
			$string_meta['context'] = self::CONTEXT_SINGULAR;

			if ( 'general' === $general_or_singular ) {
				$string_meta['context'] = self::CONTEXT_GENERAL;
			}
		} else {
			$string_meta['domain'] = self::LEGACY_STRING_DOMAIN;
			$string_meta['name']   = self::LEGACY_NAME_PREFIX_SINGULAR . $value;

			if ( 'general' === $general_or_singular ) {
				$string_meta['name'] = self::LEGACY_NAME_PREFIX_GENERAL . $value;
			}
		}

		return $string_meta;
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	private function is_string_translated_with_gettext_context( $value ) {
		return array_key_exists( $value, $this->translated_with_gettext_context );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	private function get_domain_for_taxonomy( $value ) {
		return $this->translated_with_gettext_context[ $value ];
	}
}