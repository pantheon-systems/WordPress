<?php

/**
 * Class WPML_PB_String_Registration
 */
class WPML_PB_String_Registration {

	/** @var IWPML_PB_Strategy $strategy */
	private $strategy;
	/** @var WPML_ST_String_Factory $string_factory */
	private $string_factory;
	/** @var  WPML_ST_Package_Factory $package_factory */
	private $package_factory;
	/** @var WPML_Translate_Link_Targets $translate_link_targets */
	private $translate_link_targets;

	private $active_languages;
	/** @var  bool $migration_mode */
	private $migration_mode;

	/**
	 * WPML_PB_String_Registration constructor.
	 *
	 * @param IWPML_PB_Strategy $strategy
	 * @param WPML_ST_String_Factory $string_factory
	 * @param WPML_ST_Package_Factory $package_factory
	 * @param WPML_Translate_Link_Targets $translate_link_targets
	 * @param array $active_languages
	 * @param bool $migration_mode
	 */
	public function __construct(
		IWPML_PB_Strategy $strategy,
		WPML_ST_String_Factory $string_factory,
		WPML_ST_Package_Factory $package_factory,
		WPML_Translate_Link_Targets $translate_link_targets,
		array $active_languages,
		$migration_mode = false
	) {
		$this->strategy               = $strategy;
		$this->string_factory         = $string_factory;
		$this->package_factory        = $package_factory;
		$this->translate_link_targets = $translate_link_targets;
		$this->active_languages       = $active_languages;
		$this->migration_mode         = $migration_mode;
	}

	/**
	 * @param int $post_id
	 * @param string $content
	 *
	 * @return null|int
	 */
	public function get_string_id_from_package( $post_id, $content, $name = '' ) {
		$package_data = $this->strategy->get_package_key( $post_id );
		$package      = $this->package_factory->create( $package_data );
		$string_name  = $name ? $name : md5( $content );
		$string_name  = $package->sanitize_string_name( $string_name );
		$string_value = $content;

		return apply_filters( 'wpml_string_id_from_package', null, $package, $string_name, $string_value );
	}

	public function get_string_title( $string_id ) {
		return apply_filters( 'wpml_string_title_from_id', null, $string_id );
	}

	/**
	 * @param int $post_id
	 * @param string $content
	 * @param string $type
	 * @param string $title
	 * @param string $name
	 * @param int $location
	 *
	 * @return int $string_id
	 */
	public function register_string( $post_id, $content = '', $type = 'LINE', $title = '', $name = '', $location = 0 ) {

		$string_id = 0;

		if ( trim( $content ) ) {

			$string_name = $name ? $name : md5( $content );

			if ( $this->migration_mode ) {

				$string_id = $this->get_string_id_from_package( $post_id, $content, $string_name );
				$this->set_location( $string_id, $location );

			} else {

				if ( 'LINK' === $type && ! $this->translate_link_targets->is_internal_url( $content ) ) {
					$type = 'LINE';
				}

				$string_value = $content;
				$package      = $this->strategy->get_package_key( $post_id );
				$string_title = $title ? $title : $string_value;
				do_action( 'wpml_register_string', $string_value, $string_name, $package, $string_title, $type );

				$string_id = $this->get_string_id_from_package( $post_id, $content, $string_name );
				$this->set_location( $string_id, $location );

				if ( 'LINK' === $type ) {
					$this->set_link_translations( $string_id );
				}
			}
		}

		return $string_id;
	}

	/**
	 * @param int $string_id
	 * @param int $location
	 */
	private function set_location( $string_id, $location ) {
		$string = $this->string_factory->find_by_id( $string_id );
		$string->set_location( $location );
	}

	private function set_link_translations( $string_id ) {
		$string   = $this->string_factory->find_by_id( $string_id );
		$statuses = $string->get_translation_statuses();
		foreach ( $this->active_languages as $language ) {
			$language = $language['code'];
			if ( $language != $string->get_language() ) {
				$value = $this->has_translation( $statuses, $language ) ? null : $string->get_value();
				$string->set_translation( $language, $value );
			}
		}
	}

	private function has_translation( $statuses, $language ) {
		foreach ( $statuses as $status ) {
			if ( $status->language == $language ) {
				return true;
			}
		}

		return false;
	}
}
