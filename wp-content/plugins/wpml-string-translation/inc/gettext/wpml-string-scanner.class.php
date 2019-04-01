<?php

class WPML_String_Scanner {

	const DEFAULT_DOMAIN = 'default';

	/**
	 * @param string|NULL $type 'plugin' or 'theme'
	 */
	protected $current_type;
	protected $current_path;
	protected $text_domain;

	private $domains;
	private $registered_strings;
	private $lang_codes;
	private $currently_scanning;
	private $domains_found;
	private $default_domain;

	/** @var WP_Filesystem_Base */
	private $wp_filesystem;

	/** @var WPML_File $wpml_file */
	private $wpml_file;

	/**
	 * @var array
	 */
	private $scan_stats;
	private $scanned_files;

	/**
	 * @var WPML_File_Name_Converter
	 */
	private $file_name_converter;

	/**
	 * @var WPML_ST_DB_Mappers_String_Positions
	 */
	private $string_positions_mapper;

	/**
	 * @var WPML_ST_DB_Mappers_Strings
	 */
	private $strings_mapper;

	/** @var WPML_ST_File_Hashing */
	protected $file_hashing;

	/**
	 * WPML_String_Scanner constructor.
	 *
	 * @param WP_Filesystem_Base $wp_filesystem
	 * @param WPML_ST_File_Hashing $file_hashing
	 */
	public function __construct( WP_Filesystem_Base $wp_filesystem, WPML_ST_File_Hashing $file_hashing ) {
		global $wpdb;

		$this->domains               = array();
		$this->registered_strings    = array();
		$this->lang_codes            = array();
		$this->domains_found         = array();
		$this->scan_stats            = array();
		$this->scanned_files         = array();

		$this->default_domain        = 'default';
		$this->wp_filesystem         = $wp_filesystem;
		$this->file_hashing          = $file_hashing;
	}

	protected function scan_starting( $scanning ) {
		$this->currently_scanning                         = $scanning;
		$this->domains_found[ $this->currently_scanning ] = array();
		$this->default_domain                             = 'default';
	}

	protected function scan_response() {
		global $__icl_registered_strings, $sitepress;

		$result = array(
			'scan_successful_message' => esc_html__( 'Scan successful! WPML found %s strings.', 'wpml-string-translation' ),
			'files_processed_message' => esc_html__( 'The following files were processed:', 'wpml-string-translation' ),
			'files_processed' => $this->get_scanned_files(),
			'strings_found' => is_array( $__icl_registered_strings ) ? count( $__icl_registered_strings ) : 0,
		);

		if ( $result['strings_found'] ) {
			$result['scan_successful_message'] .= __( ' They were added to the string translation table.', 'wpml-string-translation' );
		}

		$sitepress->get_wp_api()->wp_send_json_success( $result );
	}

	protected final function init_text_domain( $text_domain ) {
		$string_settings = apply_filters( 'wpml_get_setting', false, 'st' );

		$use_header_text_domain = isset( $string_settings[ 'use_header_text_domains_when_missing' ] ) && $string_settings[ 'use_header_text_domains_when_missing' ];
		
		$this->default_domain = 'default';
	
		if ( $use_header_text_domain && $text_domain ) {
			$this->default_domain = $text_domain;
		}
	}

	protected function get_domains_found() {
		return $this->domains_found[ $this->currently_scanning ];
	}
	
	protected function get_default_domain() {
		return $this->default_domain;
	}

	protected function maybe_register_string( $value, $gettext_context ) {
		if ( ! $this->get_string_id( $value, $gettext_context, $gettext_context ) ) {
			$this->store_results( $value, $gettext_context, $gettext_context, '', 0 );
		}
	}

	/**
	 * Get list of files under directory.
	 * @param  string $path       Directory to parse.
	 * @param  object $filesystem WP_Filesystem object
	 * @return array
	 */
	private function extract_files( $path, $filesystem ) {
		$path = $this->add_dir_separator( $path );
		$files = array();
		$list = $filesystem->dirlist( $path );
		foreach ( $list as $single_file ) {
			if ( 'f' === $single_file['type'] ) {
				$files[] = $path . $single_file['name'];
			} else {
				$files = array_merge( $files, $this->extract_files( $path . $single_file['name'], $filesystem ) );
			}
		}
		return $files;
	}

	/**
	 * Make sure that the last character is second argument.
	 * @param  string $path
	 * @param  string $separator
	 * @return string
	 */
	private function add_dir_separator( $path, $separator = DIRECTORY_SEPARATOR ) {
		if ( strlen( $path ) > 0 ) {
			if ( substr( $path, -1 ) !== $separator ) {
				return $path . $separator;
			} else {
				return $path;
			}
		} else {
			return $path;
		}
	}
	
	private function fix_existing_string_with_wrong_context( $original_value, $new_string_context, $gettext_context ) {
		if ( ! isset( $this->current_type ) || ! isset( $this->current_path ) ) {
			return;
		}

        $old_context = $this->get_old_context( );

		$new_context_string_id = $this->get_string_id( $original_value, $new_string_context, $gettext_context );

		if ( ! $new_context_string_id ) {
			$old_context_string_id = $this->get_string_id( $original_value, $old_context, $gettext_context );
			if ( $old_context_string_id ) {
				$this->fix_string_context( $old_context_string_id, $new_string_context );
				unset( $this->registered_strings[ $old_context ] );
				unset( $this->registered_strings[ $new_string_context ] );
			}
		}
	}
    
    private function get_old_context( ) {
		
        $plugin_or_theme_path = $this->current_path;

		$name    = basename( $plugin_or_theme_path );
		$old_context = $this->current_type . ' ' . $name;

        return $old_context;
        
    }

	private function get_lang_code( $lang_locale ) {
		global $wpdb;

		if ( ! isset( $this->lang_codes[ $lang_locale ] ) ) {
			$this->lang_codes[ $lang_locale ] = $wpdb->get_var( $wpdb->prepare( "SELECT code FROM {$wpdb->prefix}icl_locale_map WHERE locale=%s", $lang_locale ) );
		}

		return $this->lang_codes[ $lang_locale ];
	}

	private function get_string_id( $original, $domain, $gettext_context ) {

		$this->warm_cache( $domain );

		$string_context_name = md5( $gettext_context . md5( $original ) );
		$string_id  = isset( $this->registered_strings[ $domain ] [ 'context-name' ] [ $string_context_name ] ) ? $this->registered_strings[ $domain ] [ 'context-name' ] [ $string_context_name ] : null;

		return $string_id;
	}

	private function fix_string_context( $string_id, $new_string_context ) {
		global $wpdb;

		$string = $wpdb->get_row( $wpdb->prepare( "SELECT gettext_context, name FROM {$wpdb->prefix}icl_strings WHERE id=%d", $string_id ) );

		$domain_name_context_md5 = md5( $new_string_context . $string->name . $string->gettext_context );
		
		$wpdb->update( $wpdb->prefix . 'icl_strings',
						array(
							  'context'                 => $new_string_context,
							  'domain_name_context_md5' => $domain_name_context_md5
							  ),
						array( 'id' => $string_id ), array( '%s', '%s') , '%d' );
		


	}

	protected function set_stats( $key, $item ) {
		$string_settings = apply_filters( 'wpml_get_setting', false, 'st' );

		foreach( $this->get_domains_found() as $name => $count ) {
			$old_count = isset( $string_settings[ $key ][ $item ][ $name ] ) ?
				$string_settings[ $key ][ $item ][ $name ] :
				0;

			$string_settings[ $key ][ $item ][ $name ] = $old_count + $count;
		}

		do_action( 'wpml_set_setting', 'st', $string_settings, true );
	}

	public function store_results( $string, $domain, $_gettext_context, $file, $line ) {

		global $wpdb;

		$domain = $domain ? $domain : 'WordPress';

		if ( ! isset( $this->domains_found[ $this->currently_scanning ] [ $domain ] ) ) {
			$this->domains_found[ $this->currently_scanning ] [ $domain ] = 1;
		} else {
			$this->domains_found[ $this->currently_scanning ] [ $domain ] += 1;
		}

		if ( ! in_array( $domain, $this->domains ) ) {
			$this->domains[ ] = $domain;

			// clear existing entries (both source and page type)
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_string_positions WHERE string_id IN
                (SELECT id FROM {$wpdb->prefix}icl_strings WHERE context = %s)", $domain ) );
		}

        $string = str_replace( '\n', "\n", $string );
		$string = str_replace( array( '\"', "\\'" ), array( '"', "'" ), $string );
		//replace extra backslashes added by _potx_process_file
		$string = str_replace( array( '\\\\' ), array( '\\' ), $string );
		$string = stripcslashes( $string );

		global $__icl_registered_strings;

		if ( ! isset( $__icl_registered_strings ) ) {
			$__icl_registered_strings = array();
		}

		if ( ! isset( $__icl_registered_strings[ $domain . '||' . $string . '||' . $_gettext_context ] ) ) {

			$name = md5( $string );
			$this->fix_existing_string_with_wrong_context( $string, $domain, $_gettext_context );
			$this->register_string( $domain, $_gettext_context, $name, $string );

			$__icl_registered_strings[ $domain . '||' . $string . '||' . $_gettext_context ] = true;
		}

		// store position in source
		$this->track_string( $string,
							 array( 'domain' => $domain,
								    'context' => $_gettext_context
								  ),
							ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_SOURCE,
							$file,
							$line );
	}

	private function register_string( $domain, $context, $name, $string ) {

		$this->warm_cache( $domain );

		if ( ! isset( $this->registered_strings[ $domain ] [ 'context-name' ] [ md5( $context . $name ) ] ) ) {
			
			if ( $context ) {
				$string_context = array( 'domain'  => $domain,
										 'context' => $context
										);
			} else {
				$string_context = $domain;
			}
			$string_id = icl_register_string( $string_context, $name, $string );
			
			$this->registered_strings[ $domain ] [ 'context-name' ] [ md5( $context . $name ) ] = $string_id;
		}
	}

	private function warm_cache( $domain ) {
		if ( ! isset( $this->registered_strings[ $domain ] ) ) {

			$this->registered_strings[ $domain ] = array(
				'context-name' => array(),
				'value'      => array(),
			);

			$results = $this->get_strings_mapper()->get_all_by_context( $domain );
			foreach ( $results as $result ) {
				$this->registered_strings[ $domain ] ['context-name'] [ md5( $result['gettext_context'] . $result['name'] ) ] = $result['id'];
			}
		}
	}

	public function track_string( $text, $context, $kind = ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_PAGE, $file = null, $line = null ) {
		list ( $domain, $gettext_context ) = wpml_st_extract_context_parameters( $context );
		
		// get string id
		$string_id = $this->get_string_id( $text, $domain, $gettext_context );
		if ( $string_id ) {
			$str_pos_mapper = $this->get_string_positions_mapper();
			$string_records_count = $str_pos_mapper->get_count_of_positions_by_string_and_kind( $string_id, $kind );

			if ( ICL_STRING_TRANSLATION_STRING_TRACKING_THRESHOLD > $string_records_count ) {
				if ( $kind == ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_PAGE ) {
					// get page url
					$https    = isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ? 's' : '';
					$position = 'http' . $https . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
				} else {
					$file = $this->get_file_name_converter()->transform_realpath_to_reference( $file );
					$position = $file . '::' . $line;
				}

				if ( ! $str_pos_mapper->is_string_tracked( $string_id, $position, $kind ) && ! $this->is_string_preview() ) {
					$str_pos_mapper->insert( $string_id, $position, $kind );
				}
			}
		}
	}

	protected function add_stat( $text ) {
		$this->scan_stats[] = $text;
	}

	protected function get_scan_stats() {
		return $this->scan_stats;
	}

	protected function add_scanned_file( $file ) {
		$this->scanned_files[ ] = $this->format_path_for_display( $file );
	}

	protected function get_scanned_files() {
		return $this->scanned_files;
	}
    
    protected function cleanup_wrong_contexts( ) {
        global $wpdb;
		
        $old_context = $this->get_old_context( );

	    /** @var array $results */
		$results = $wpdb->get_results( $wpdb->prepare( "
	        SELECT id, name, value
	        FROM {$wpdb->prefix}icl_strings
	        WHERE context = %s",
			$old_context
			) );
		
		foreach( $results as $string ) {
			// See if the string has no translations

			/** @var array $old_translations */
			$old_translations = $wpdb->get_results( $wpdb->prepare( "
				SELECT id, language, status, value
				FROM {$wpdb->prefix}icl_string_translations
				WHERE string_id = %d",
				$string->id
				) );

			if ( ! $old_translations ) {
				// We don't have any translations so we can delete the string.
				
				$wpdb->delete( $wpdb->prefix . 'icl_strings', array( 'id' => $string->id ), array( '%d' ) );
			} else {
				// check if we have a new string in the right context
				
				$domains = $this->get_domains_found( );
				
				foreach ( $domains as $domain => $count ) {
					$new_string_id = $wpdb->get_var( $wpdb->prepare( "
						SELECT id
						FROM {$wpdb->prefix}icl_strings
						WHERE context = %s AND name = %s AND value = %s",
						$domain, $string->name, $string->value
						) );
					
					if ( $new_string_id ) {
						
						// See if it has the same translations

						/** @var array $new_translations */
						$new_translations = $wpdb->get_results( $wpdb->prepare( "
							SELECT id, language, status, value
							FROM {$wpdb->prefix}icl_string_translations
							WHERE string_id = %d",
							$new_string_id
							) );
						
						foreach ( $new_translations as $new_translation) {
							foreach ( $old_translations as $index => $old_translation ) {
								if ( $new_translation->language == $old_translation->language &&
										$new_translation->status == $old_translation->status &&
										$new_translation->value == $old_translation->value ) {
									unset( $old_translations[$index] );
								}
							}
						}
						if ( ! $old_translations ) {
							// We don't have any old translations that are not in the new strings so we can delete the string.
							
							$wpdb->delete( $wpdb->prefix . 'icl_strings', array( 'id' => $string->id ), array( '%d' ) );
							break;
						}
						
					}					
					
				}
				
			}
		}
		
		// Rename the context for any strings that are in the old context
		// This way the update message will no longer show.
		
		$obsolete_context = str_replace( 'plugin ', '', $old_context );
		$obsolete_context = str_replace( 'theme ', '', $obsolete_context );
		$obsolete_context = $obsolete_context . ' (obsolete)';

	    $string_update_context = $wpdb->get_results( $wpdb->prepare( "
									 SELECT id FROM {$wpdb->prefix}icl_strings
									 WHERE context = %s
									 ",
		                             $old_context ), ARRAY_A );


	    if ( $string_update_context ) {
		    $wpdb->query( $wpdb->prepare( "
									 UPDATE {$wpdb->prefix}icl_strings
									 SET context = %s
									 WHERE id IN ( " . implode( ',', wp_list_pluck( $string_update_context, 'id' ) ) . ' )
									 ',
			    $obsolete_context ) );
	    }
        
    }
	
	protected function copy_old_translations( $contexts, $prefix ) {
		foreach ( $contexts as $context ) {
			$old_strings = $this->get_strings_by_context( $prefix . ' ' . $context );
			if ( 0 === count( $old_strings ) ) {
				continue;
			}

			$old_translations = $this->get_strings_translations( $old_strings );

			$new_strings = $this->get_strings_by_context( $context );
			$new_translations = $this->get_strings_translations( $new_strings );

			/** @var array $old_translations */
			foreach( $old_translations as $old_translation ) {
				// see if we have a new translation.
				$found = false;
				/** @var array $new_translations */
				foreach ( $new_translations as $new_translation ) {
					if ( $new_translation->string_id == $old_translation->string_id &&
							$new_translation->language == $old_translation->language ) {
						$found = true;
						break;
					}
				}
				
				if ( ! $found ) {
					// Copy the old translation to the new string.
					
					// Find the original
					foreach ( $old_strings as $old_string ) {
						if ( $old_string->id == $old_translation->string_id ) {
							// See if we have the same string in the new strings
							foreach ( $new_strings as $new_string ) {
								if ( $new_string->value == $old_string->value ) {
									// Add the old translation to new string.
									icl_add_string_translation( $new_string->id, $old_translation->language, $old_translation->value, ICL_TM_COMPLETE );
									break;
								}
							}
							break;
						}
					}
					
				}
				
			}
		}
			
	}

	/**
	 * @param string $context
	 *
	 * @return array
	 */
	private function get_strings_by_context( $context ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "
				SELECT id, name, value
				FROM {$wpdb->prefix}icl_strings
				WHERE context = %s",
			$context
		) );
	}

	/**
	 * @param array $strings
	 *
	 * @return array
	 */
	private function get_strings_translations( $strings ) {
		global $wpdb;

		$translations = array();

		if (count($strings)) {
			foreach ( array_chunk( $strings, 100 ) as $chunk ) {
				$ids = array();
				foreach ( $chunk as $string ) {
					$ids[] = $string->id;
				}
				$ids = implode( ',', $ids );

				$rows = $wpdb->get_results( "
							SELECT id, string_id, language, status, value
							FROM {$wpdb->prefix}icl_string_translations
							WHERE string_id IN ({$ids})"
				);

				$translations = array_merge( $translations, $rows );
			}
		}

		return $translations;
	}

	protected function remove_notice( $notice_id ) {
		global $wpml_st_admin_notices;
		if ( isset( $wpml_st_admin_notices ) ) {
			/** @var WPML_ST_Themes_And_Plugins_Updates $wpml_st_admin_notices */
			$wpml_st_admin_notices->remove_notice( $notice_id );
		}
	}


	/**
	 * @return WPML_ST_DB_Mappers_Strings
	 */
	public function get_strings_mapper() {
		if ( null === $this->strings_mapper ) {
			global $wpdb;
			$this->strings_mapper = new WPML_ST_DB_Mappers_Strings( $wpdb );
		}

		return $this->strings_mapper;
	}

	/**
	 * @param WPML_ST_DB_Mappers_Strings $strings_mapper
	 */
	public function set_strings_mapper( WPML_ST_DB_Mappers_Strings $strings_mapper ) {
		$this->strings_mapper = $strings_mapper;
	}

	/**
	 * @return WPML_ST_DB_Mappers_String_Positions
	 */
	public function get_string_positions_mapper() {
		if ( null === $this->string_positions_mapper ) {
			global $wpdb;
			$this->string_positions_mapper = new WPML_ST_DB_Mappers_String_Positions( $wpdb );
		}

		return $this->string_positions_mapper;
	}

	/**
	 * @param WPML_ST_DB_Mappers_String_Positions $string_positions_mapper
	 */
	public function set_string_positions_mapper( WPML_ST_DB_Mappers_String_Positions $string_positions_mapper ) {
		$this->string_positions_mapper = $string_positions_mapper;
	}

	/**
	 * @return WPML_File_Name_Converter
	 */
	public function get_file_name_converter() {
		if ( null === $this->file_name_converter ) {
			$this->file_name_converter = new WPML_File_Name_Converter();
		}

		return $this->file_name_converter;
	}

	/**
	 * @param WPML_File_Name_Converter $converter
	 */
	public function set_file_name_converter(WPML_File_Name_Converter $converter) {
		$this->file_name_converter = $converter;
	}

	/**
	 * @return WPML_File
	 */
	protected function get_wpml_file() {
		if ( ! $this->wpml_file ) {
			$this->wpml_file = new WPML_File();
		}

		return $this->wpml_file;
	}

	private function is_string_preview() {
		$is_string_preview = false;
		if ( array_key_exists( 'icl_string_track_value', $_GET ) || array_key_exists( 'icl_string_track_context', $_GET ) ) {
			$is_string_preview = true;
		}

		return $is_string_preview;
	}

	/**
	 * @param string $type
	 * @param string $path
	 *
	 * @return bool
	 */
	public function update_last_mo_scan_timestamp( $type, $path ) {
		$name = basename( $path );
		$existing_dates = get_option( self::UPDATE_LAST_MO_SCAN_TIMESTAMP, array() );
		$existing_dates[ $type ][ $name ] = time();
		return update_option( self::UPDATE_LAST_MO_SCAN_TIMESTAMP, $existing_dates, false );
	}

	/** @return bool */
	protected function scan_php_and_mo_files() {
		return array_key_exists( 'scan_mo_files', $_POST );
	}

	protected function scan_only_mo_files() {
		return array_key_exists( 'scan_only_mo_files', $_POST );
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	private function format_path_for_display( $path ) {
		$path = stripslashes( $path );
		$path = $this->get_wpml_file()->get_relative_path( $path );
		$path = $this->get_wpml_file()->fix_dir_separator( $path );
		return $path;
	}
}

