<?php

class WPML_ST_MO_File_Registration {

	/** @var WPML_ST_MO_Dictionary */
	private $mo_dictionary;

	/** @var WPML_File */
	private $wpml_file;

	/** @var WPML_ST_MO_Component_Details */
	private $components_find;

	/** @var array */
	private $active_languages;

	/** @var array */
	private $cache = array();

	/**
	 * @param WPML_ST_MO_Dictionary $mo_dictionary
	 * @param WPML_File $wpml_file
	 * @param WPML_ST_MO_Component_Details $components_find
	 * @param array $active_languages
	 */
	public function __construct(
		WPML_ST_MO_Dictionary $mo_dictionary,
		WPML_File $wpml_file,
		WPML_ST_MO_Component_Details $components_find,
		array $active_languages
	) {
		$this->mo_dictionary    = $mo_dictionary;
		$this->wpml_file        = $wpml_file;
		$this->components_find  = $components_find;
		$this->active_languages = $active_languages;
	}

	public function add_hooks() {
		add_filter( 'override_load_textdomain', array( $this, 'cached_save_mo_file_info' ), 11, 3 );
	}

	public function cached_save_mo_file_info( $override, $domain, $mo_file_path ) {
		if ( !isset( $this->cache[ $mo_file_path ] ) ) {
			$this->cache[ $mo_file_path ] = $this->save_mo_file_info( $override, $domain, $mo_file_path );
		}

		return $this->cache[ $mo_file_path ];
	}

	public function save_mo_file_info( $override, $domain, $mo_file_path ) {
		$file_path_pattern = $this->get_file_path_pattern( $mo_file_path );

		foreach ( $this->active_languages as $lang_data ) {
			$mo_file_path_in_lang = sprintf( $file_path_pattern, $lang_data['default_locale'] );
			$this->register_single_file( $domain, $mo_file_path_in_lang );
		}

		return $override;
	}

	private function get_file_path_pattern( $mo_file_path ) {
		$pattern = '#(-)?([a-z]+)([_A-Z]*)\.mo$#i';

		return preg_replace( $pattern, '${1}%s.mo', $mo_file_path );
	}

	/**
	 * @param $domain
	 * @param $mo_file_path
	 */
	private function register_single_file( $domain, $mo_file_path ) {
		if ( ! $this->wpml_file->file_exists( $mo_file_path ) ) {
			return ;
		}

		$relative_path = $this->wpml_file->get_relative_path( $mo_file_path );
		$last_modified = $this->wpml_file->get_file_modified_timestamp( $mo_file_path );
		$file          = $this->mo_dictionary->find_file_info_by_path( $relative_path );

		if ( ! $file ) {
			if ( ! $this->components_find->is_component_active( $mo_file_path ) ) {
				return;
			}

			$file = new WPML_ST_MO_File( $relative_path, $domain );
			$file->set_last_modified( $last_modified );

			list( $component_type, $component_id ) = $this->components_find->find_details( $mo_file_path );
			$file->set_component_type( $component_type );
			$file->set_component_id( $component_id );

			$this->mo_dictionary->save( $file );
		} elseif ( $file->get_last_modified() !== $last_modified ) {
			$file->set_status( WPML_ST_MO_File::NOT_IMPORTED );
			$file->set_last_modified( $last_modified );
			$file->set_imported_strings_count( 0 );

			$this->mo_dictionary->save( $file );
		}
	}
}

