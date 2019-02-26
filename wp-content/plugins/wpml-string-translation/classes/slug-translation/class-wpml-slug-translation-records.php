<?php

abstract class WPML_Slug_Translation_Records {

	const CONTEXT_DEFAULT   = 'default';
	const CONTEXT_WORDPRESS = 'WordPress';

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var WPML_WP_Cache_Factory $cache_factory*/
	private $cache_factory;

	public function __construct( wpdb $wpdb, WPML_WP_Cache_Factory $cache_factory ) {
		$this->wpdb          = $wpdb;
		$this->cache_factory = $cache_factory;
	}

	/**
	 * @param string $type
	 *
	 * @return WPML_ST_Slug
	 */
	public function get_slug( $type ) {
		$cache_item = $this->cache_factory->create_cache_item( $this->get_cache_group(), $type );

		if ( ! $cache_item->exists() ) {
			$slug = new WPML_ST_Slug();

			$original = $this->wpdb->get_row(
				$this->wpdb->prepare(
					"SELECT id, value, language, context, name
					 FROM {$this->wpdb->prefix}icl_strings
					 WHERE name = %s
					    AND (context = %s OR context = %s)",
					$this->get_string_name( $type ),
					self::CONTEXT_DEFAULT,
					self::CONTEXT_WORDPRESS
				)
			);

			if ( $original ) {
				$slug->set_lang_data( $original );

				$translations = $this->wpdb->get_results(
					$this->wpdb->prepare(
						"SELECT value, language, status
					 FROM {$this->wpdb->prefix}icl_string_translations
					 WHERE string_id = %d
						AND value <> ''",
						$original->id
					)
				);

				if ( $translations ) {
					foreach ( $translations as $translation ) {
						$slug->set_lang_data( $translation );
					}
				}
			}

			$cache_item->set( $slug );

		}

		return $cache_item->get();
	}

	/** @return string */
	private function get_cache_group() {
		return __CLASS__ . '::' . $this->get_element_type();
	}

	private function flush_cache() {
		$cache_group = $this->cache_factory->create_cache_group( $this->get_cache_group() );
		$cache_group->flush_group_cache();
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 * @param string $lang
	 *
	 * @return null|string
	 */
	public function get_translation( $type, $lang ) {
		$slug = $this->get_slug( $type );

		if ( $slug->is_translation_complete( $lang ) ) {
			return $slug->get_value( $lang );
		}

		return null;
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 * @param string $lang
	 *
	 * @return null|string
	 */
	public function get_original( $type, $lang = '' ) {
		$slug = $this->get_slug( $type );

		if ( ! $lang || $slug->get_original_lang() === $lang ) {
			return $slug->get_original_value();
		}

		return null;
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 *
	 * @return int|null
	 */
	public function get_slug_id( $type ) {
		$slug = $this->get_slug( $type );

		if ( $slug->get_original_id() ) {
			return $slug->get_original_id();
		}

		return null;
	}

	/**
	 * @param string $type
	 * @param string $slug
	 *
	 * @return int|null
	 */
	public function register_slug( $type, $slug ) {
		$string_id = icl_register_string(
			self::CONTEXT_WORDPRESS,
			$this->get_string_name( $type ),
			$slug
		);

		$this->flush_cache();

		return $string_id;
	}

	/**
	 * @param string $type
	 * @param string $slug
	 */
	public function update_original_slug( $type, $slug ) {
		$this->wpdb->update(
			$this->wpdb->prefix . 'icl_strings',
			 array( 'value' => $slug ),
			 array( 'name'  => $this->get_string_name( $type ) )
		);

		$this->flush_cache();
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 *
	 * @return null|stdClass
	 */
	public function get_original_slug_and_lang( $type ) {
		$original_slug_and_lang = null;

		$slug = $this->get_slug( $type );

		if ( $slug->get_original_id() ) {
			$original_slug_and_lang = (object) array(
				'value' => $slug->get_original_value(),
				'language' => $slug->get_original_lang(),
			);
		}

		return $original_slug_and_lang;
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 * @param bool   $only_status_complete
	 *
	 * @return array
	 */
	public function get_element_slug_translations( $type, $only_status_complete = true ) {
		$slug = $this->get_slug( $type );

		$rows = array();

		foreach ( $slug->get_language_codes() as $lang ) {
			if ( $slug->get_original_lang() === $lang
				|| ( $only_status_complete && ! $slug->is_translation_complete( $lang ) )
			) {
				continue;
			}

			$rows[] = (object) array(
				'value' => $slug->get_value( $lang ),
				'language' => $lang,
				'status' => $slug->get_status( $lang ),
			);
		}

		return $rows;
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function get_all_slug_translations( $types ) {
		$rows = array();

		foreach ( $types as $type ) {
			$slug = $this->get_slug( $type );

			foreach ( $slug->get_language_codes() as $lang ) {
				if ( $slug->get_original_lang() !== $lang ) {
					$rows[] = (object) array(
						'value' => $slug->get_value( $lang ),
						'name'  => $slug->get_name(),
					);
				}
			}
		}

		return $rows;
	}

	/**
	 * @deprecated use `get_slug` instead.
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_slug_translation_languages( $type ) {
		$languages = array();
		$slug      = $this->get_slug( $type );

		foreach ( $slug->get_language_codes() as $lang ) {
			if ( $slug->is_translation_complete( $lang ) ) {
				$languages[] = $lang;
			}
		}

		return $languages;
	}

	/**
	 * Use `WPML_ST_String` only for updating the values in the DB
	 * because it does not have any caching feature.
	 *
	 * @param string $type
	 *
	 * @return null|WPML_ST_String
	 */
	public function get_slug_string( $type ) {
		$string_id = $this->get_slug_id( $type );

		if ( $string_id ) {
			return new WPML_ST_String( $string_id, $this->wpdb );
		}

		return null;
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	abstract protected function get_string_name( $slug );

	/** @return string */
	abstract protected function get_element_type();
}