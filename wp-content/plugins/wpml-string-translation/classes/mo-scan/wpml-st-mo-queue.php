<?php

class WPML_ST_MO_Queue {
	const DEFAULT_LIMIT = 10000;
	const LOCK_FIELD = '_wpml_st_mo_scan_in_progress';

	/** @var WPML_ST_MO_Dictionary */
	private $mo_dictionary;

	/** @var WPML_ST_MO_Scan */
	private $mo_translation_loader;

	/** @var WPML_ST_MO_Scan_Storage */
	private $mo_scan_storage;

	/** @var array */
	private $language_codes_map;

	/** @var int */
	private $limit;

	private $transient;

	/**
	 * @param WPML_ST_MO_Dictionary $mo_dictionary
	 * @param WPML_ST_MO_Scan $mo_translation_loader
	 * @param WPML_ST_MO_Scan_Storage $mo_scan_storage
	 * @param array $language_code_maps
	 * @param int $limit
	 * @param WPML_Transient $transient
	 */
	public function __construct(
		WPML_ST_MO_Dictionary $mo_dictionary,
		WPML_ST_MO_Scan $mo_translation_loader,
		WPML_ST_MO_Scan_Storage $mo_scan_storage,
		array $language_code_maps,
		$limit = self::DEFAULT_LIMIT,
		WPML_Transient $transient
	) {
		$this->mo_dictionary         = $mo_dictionary;
		$this->mo_translation_loader = $mo_translation_loader;
		$this->mo_scan_storage       = $mo_scan_storage;
		$this->language_codes_map    = $language_code_maps;
		$this->limit                 = $limit;
		$this->transient             = $transient;
	}

	public function import() {
		$files = $this->mo_dictionary->get_not_imported_mo_files();

		$imported = 0;
		foreach ( $files as $file ) {
			if ( $imported >= $this->limit ) {
				break;
			}

			$translations = $this->mo_translation_loader->load_translations( $file->get_full_path() );

			try {
				$number_of_translations = count( $translations );
				if ( ! $number_of_translations ) {
					throw new RuntimeException( 'File is empty' );
				}

				$translations = $this->constrain_translations_number(
					$translations,
					$file->get_imported_strings_count(),
					$this->limit - $imported
				);

				$imported += $imported_in_file = count( $translations );

				$this->mo_scan_storage->save(
					$translations,
					$file->get_domain(),
					$this->map_language_code( $file->get_mo_file_lang() )
				);

				$file->set_imported_strings_count( $file->get_imported_strings_count() + $imported_in_file );

				if ( $file->get_imported_strings_count() >= $number_of_translations ) {
					$file->set_status( WPML_ST_MO_File::IMPORTED );
				} else {
					$file->set_status( WPML_ST_MO_File::PARTLY_IMPORTED );
				}

			} catch ( WPML_ST_Bulk_Strings_Insert_Exception $e ) {
				$file->set_status( WPML_ST_MO_File::PARTLY_IMPORTED );
				break;
			} catch ( Exception $e ) {
				$file->set_status( WPML_ST_MO_File::IMPORTED );
			}
			$this->mo_dictionary->save( $file );

			do_action( 'wpml-st-mo-post-import', $file );
		}
	}

	/**
	 * @param string $locale
	 *
	 * @return string
	 */
	private function map_language_code( $locale ) {
		if ( isset( $this->language_codes_map[ $locale ] ) ) {
			return $this->language_codes_map[ $locale ];
		}

		return $locale;
	}

	/**
	 * @return bool
	 */
	public function is_completed() {
		return 0 === count( $this->mo_dictionary->get_not_imported_mo_files() ) &&
		       0 < count( $this->mo_dictionary->get_imported_mo_files() );
	}

	/**
	 * @return string[]
	 */
	public function get_processed() {
		return wp_list_pluck( $this->mo_dictionary->get_imported_mo_files(), 'path' );
	}

	/**
	 * @return bool
	 */
	public function is_processing() {
		return 0 !== count( $this->mo_dictionary->get_not_imported_mo_files() );
	}

	/**
	 * @return int
	 */
	public function get_pending() {
		return count( $this->mo_dictionary->get_not_imported_mo_files() );
	}

	public function mark_as_finished() {
		foreach ( $this->mo_dictionary->get_imported_mo_files() as $file ) {
			$file->set_status( WPML_ST_MO_File::FINISHED );
			$this->mo_dictionary->save( $file );
		}
	}

	/**
	 * @param array $translations
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	private function constrain_translations_number( array $translations, $offset, $limit ) {
		if ( $limit > count( $translations ) ) {
			return $translations;
		}

		return array_slice( $translations, $offset, $limit );
	}

	public function is_locked() {
		return (bool) $this->transient->get( self::LOCK_FIELD );
	}

	public function lock() {
		$this->transient->set( self::LOCK_FIELD, 1, MINUTE_IN_SECONDS * 5 );
	}

	public function unlock() {
		$this->transient->delete( self::LOCK_FIELD );
	}
}
