<?php

/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 16/06/17
 * Time: 10:57 AM
 */
class WPML_ST_Package_Storage {

	/** @var  int $package_id */
	private $package_id;
	/** @var  wpdb $this ->wpdb */
	private $wpdb;

	/**
	 * WPML_ST_Package_Storage constructor.
	 *
	 * @param int $package_id
	 * @param wpdb $this ->wpdb
	 */
	public function __construct( $package_id, wpdb $wpdb ) {
		$this->package_id = $package_id;
		$this->wpdb       = $wpdb;
	}

	/**
	 * @param string $string_title
	 * @param string $string_type
	 * @param string $string_value
	 * @param int $string_id
	 *
	 * @return bool
	 */
	public function update( $string_title, $string_type, $string_value, $string_id ) {

		$update_where = array( 'id' => $string_id );

		$update_data = array(
			'type'  => $string_type,
			'title' => $this->truncate_long_string( $string_title ),
		);
		$type_or_title_updated = $this->wpdb->update( $this->wpdb->prefix . 'icl_strings', $update_data, $update_where );

		$update_data  = array(
			'string_package_id' => $this->package_id,
			'value' => $string_value
		);
		$package_id_or_value_updated = $this->wpdb->update( $this->wpdb->prefix . 'icl_strings', $update_data, $update_where );

		if ( $package_id_or_value_updated ) {
			$this->set_string_status_to_needs_update_if_translated( $string_id );

			$this->set_translations_to_needs_update();

		}

		return $type_or_title_updated || $package_id_or_value_updated;
	}

	private function set_string_status_to_needs_update_if_translated( $string_id ) {
		$this->wpdb->query(
			$this->wpdb->prepare(
				"UPDATE {$this->wpdb->prefix}icl_strings
							SET status=%d
							WHERE id=%d AND status<>%d",
				ICL_TM_NEEDS_UPDATE,
				$string_id,
				ICL_TM_NOT_TRANSLATED
			)
		);
		$this->wpdb->query(
			$this->wpdb->prepare(
				"UPDATE {$this->wpdb->prefix}icl_string_translations
							SET status=%d
							WHERE string_id=%d AND status<>%d",
				ICL_TM_NEEDS_UPDATE,
				$string_id,
				ICL_TM_NOT_TRANSLATED
			)
		);
	}

	private function set_translations_to_needs_update() {

		$translation_ids = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT translation_id
                      FROM {$this->wpdb->prefix}icl_translations
                      WHERE trid = ( SELECT trid
                      FROM {$this->wpdb->prefix}icl_translations
                      WHERE element_id = %d
                        AND element_type LIKE 'package%%'
                      LIMIT 1 )"
				, $this->package_id ) );
		if ( ! empty( $translation_ids ) ) {
			$this->wpdb->query(
				"UPDATE {$this->wpdb->prefix}icl_translation_status
                          SET needs_update = 1
                          WHERE translation_id IN (" . wpml_prepare_in( $translation_ids, '%d' ) . " ) " );
		}
	}

	private function truncate_long_string( $string ) {
		return strlen( $string ) > WPML_STRING_TABLE_NAME_CONTEXT_LENGTH
			? mb_substr( $string, 0, WPML_STRING_TABLE_NAME_CONTEXT_LENGTH )
			: $string;
	}


}