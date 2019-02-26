<?php

class WPML_ST_MO_Scan_Storage {
	/** @var wpdb */
	private $wpdb;

	/** @var WPML_ST_Bulk_Strings_Insert */
	private $bulk_insert;

	/**
	 * @param wpdb $wpdb
	 * @param WPML_ST_Bulk_Strings_Insert $bulk_insert
	 */
	public function __construct( wpdb $wpdb, WPML_ST_Bulk_Strings_Insert $bulk_insert ) {
		$this->wpdb = $wpdb;
		$this->bulk_insert = $bulk_insert;
	}


	public function save( array $translations, $domain, $lang ) {
		$this->bulk_insert->insert_strings( $this->build_string_collection( $translations, $domain ) );

		$string_translations = $this->build_string_translation_collection(
			$translations,
			$lang,
			$this->get_string_maps( $domain )
		);

		$this->bulk_insert->insert_string_translations( $string_translations );
	}

	/**
	 * @param WPML_ST_MO_Translation[] $translations
	 * @param string $domain
	 *
	 * @return WPML_ST_Models_String[]
	 */
	private function build_string_collection( array $translations, $domain ) {
		$result = array();

		foreach ( $translations as $translation ) {
			$result[] = new WPML_ST_Models_String(
				'en',
				$domain,
				$translation->get_context(),
				$translation->get_original(),
				ICL_TM_NOT_TRANSLATED
			);
		}

		return $result;
	}

	/**
	 * @param string $domain
	 *
	 * @return array
	 */
	private function get_string_maps( $domain ) {
		$sql = "
			SELECT id, value, gettext_context FROM {$this->wpdb->prefix}icl_strings
			WHERE context = %s
		";

		$rowset = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $domain ) );
		$result = array();

		foreach ( $rowset as $row ) {
			$result[ $row->value ][ $row->gettext_context ] = $row->id;
		}

		return $result;
	}

	/**
	 * @param WPML_ST_MO_Translation[] $translations
	 * @param string $lang
	 * @param array $value_id_map
	 *
	 * @return WPML_ST_Models_String_Translation[]
	 */
	private function build_string_translation_collection( array $translations, $lang, $value_id_map ) {
		$result = array();

		foreach ( $translations as $translation ) {
			if ( ! isset( $value_id_map[ $translation->get_original() ] ) ) {
				continue;
			}

			$result[] = new WPML_ST_Models_String_Translation(
				$value_id_map[ $translation->get_original() ][ $translation->get_context() ],
				$lang,
				ICL_TM_NOT_TRANSLATED,
				null,
				$translation->get_translation()
			);
		}

		return $result;
	}
}
