<?php

class WPML_ST_MO_Component_Details {
	/** @var WPML_ST_MO_Components_Find[] */
	private $finders;

	/** @var WPML_File $file */
	private $file;

	/** @var string */
	private $plugin_dir;

	/** @var string */
	private $theme_dir;

	/** @var string */
	private $languages_plugin_dir;

	/** @var string */
	private $languages_theme_dir;

	/** @var array */
	private $cache = array();

	/**
	 * @param WPML_ST_MO_Components_Find_Plugin $plugin_id_finder
	 * @param WPML_ST_MO_Components_Find_Theme  $theme_id_finder
	 * @param WPML_File                         $wpml_file
	 */
	public function __construct(
		WPML_ST_MO_Components_Find_Plugin $plugin_id_finder,
		WPML_ST_MO_Components_Find_Theme $theme_id_finder,
		WPML_File $wpml_file
	) {
		$this->finders['plugin'] = $plugin_id_finder;
		$this->finders['theme']  = $theme_id_finder;
		$this->file              = $wpml_file;

		$this->theme_dir  = $this->file->fix_dir_separator( get_theme_root() );
		$this->plugin_dir = $this->file->fix_dir_separator( realpath( WP_PLUGIN_DIR ) );

		$wp_content_dir = realpath( WP_CONTENT_DIR );

		$this->languages_plugin_dir = $this->file->fix_dir_separator( $wp_content_dir . '/languages/plugins' );
		$this->languages_theme_dir  = $this->file->fix_dir_separator( $wp_content_dir . '/languages/themes' );
	}

	/**
	 * @param string $mo_file_full_path
	 *
	 * @return array
	 */
	public function find_details( $mo_file_full_path ) {
		$mo_file_full_path = $this->file->fix_dir_separator( $mo_file_full_path );

		if ( ! isset( $this->cache[ $mo_file_full_path ] ) ) {
			$type = $this->find_type( $mo_file_full_path );
			if ( 'other' === $type ) {
				$this->cache[ $mo_file_full_path ] = array( $type, null );
			} else {
				$this->cache[ $mo_file_full_path ] = array( $type, $this->find_id( $type, $mo_file_full_path ) );
			}
		}

		return $this->cache[ $mo_file_full_path ];
	}

	/**
	 * @param string $component_type
	 * @param string $mo_file_full_path
	 *
	 * @return null|string
	 */
	public function find_id( $component_type, $mo_file_full_path ) {
		if ( ! isset( $this->finders[ $component_type ] ) ) {
			return null;
		}

		return $this->finders[ $component_type ]->find_id( $mo_file_full_path );
	}

	/**
	 * @param string $mo_file_full_path
	 *
	 * @return string
	 */
	public function find_type( $mo_file_full_path ) {
		if ( $this->theme_dir && ( $this->string_contains( $mo_file_full_path, $this->theme_dir ) || $this->string_contains( $mo_file_full_path, $this->languages_theme_dir ) ) ) {
			return 'theme';
		}

		if ( $this->string_contains( $mo_file_full_path, $this->plugin_dir ) || $this->string_contains( $mo_file_full_path, $this->languages_plugin_dir ) ) {
			return 'plugin';
		}

		return 'other';
	}

	/**
	 * @param string $mo_file_full_path
	 *
	 * @return bool
	 */
	public function is_component_active( $mo_file_full_path ) {
		list( $type, $id ) = $this->find_details( $mo_file_full_path );

		if ( 'other' === $type ) {
			return true;
		}

		if ( ! $id ) {
			return false;
		}

		if ( 'plugin' === $type ) {
			return is_plugin_active( $id );
		} else {
			return get_stylesheet_directory() === get_theme_root() . '/' . $id;
		}
	}

	private function string_contains( $haystack, $needle ) {
		return false !== strpos( $haystack, $needle );
	}
}
