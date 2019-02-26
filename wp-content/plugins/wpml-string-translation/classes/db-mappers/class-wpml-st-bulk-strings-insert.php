<?php

class WPML_ST_Bulk_Strings_Insert_Exception extends Exception {

}

class WPML_ST_Bulk_Strings_Insert {
	/** @var wpdb */
	private $wpdb;

	/** @var int  */
	private $chunk_size = 1000;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param int $chunk_size
	 */
	public function set_chunk_size( $chunk_size ) {
		$this->chunk_size = $chunk_size;
	}


	/**
	 * @param WPML_ST_Models_String[] $strings
	 */
	public function insert_strings( array $strings ) {
		foreach ( array_chunk( $strings, $this->chunk_size ) as $chunk ) {

			$query = "INSERT IGNORE INTO {$this->wpdb->prefix}icl_strings "
			         . '(`language`, `context`, `gettext_context`, `domain_name_context_md5`, `name`, `value`, `status`) VALUES ';

			$query .= implode( ',', array_map( array( $this, 'build_string_row' ), $chunk ) );

			$this->wpdb->suppress_errors = true;
			$this->wpdb->query( $query );
			$this->wpdb->suppress_errors = false;
			if ( $this->wpdb->last_error ) {
				throw new WPML_ST_Bulk_Strings_Insert_Exception( 'Deadlock with bulk insert' );
			}
		}
	}

	/**
	 * @param WPML_ST_Models_String_Translation[] $translations
	 */
	public function insert_string_translations( array $translations ) {
		foreach ( array_chunk( $translations, $this->chunk_size ) as $chunk ) {
			$query = "INSERT IGNORE INTO {$this->wpdb->prefix}icl_string_translations "
			         . '(`string_id`, `language`, `status`, `mo_string`) VALUES ';

			$query .= implode( ',', array_map( array( $this, 'build_translation_row' ), $chunk ) );

			$query .= ' ON DUPLICATE KEY UPDATE `mo_string`=VALUES(`mo_string`)';

			$this->wpdb->suppress_errors = true;
			$this->wpdb->query( $query );
			$this->wpdb->suppress_errors = false;
			if ( $this->wpdb->last_error ) {
				throw new WPML_ST_Bulk_Strings_Insert_Exception( 'Deadlock with bulk insert' );
			}
		}
	}

	/**
	 * @param WPML_ST_Models_String $string
	 *
	 * @return string
	 */
	private function build_string_row( WPML_ST_Models_String $string ) {
		return $this->wpdb->prepare(
			'(%s, %s, %s, %s, %s, %s, %d)',
			$string->get_language(),
			$string->get_domain(),
			$string->get_context(),
			$string->get_domain_name_context_md5(),
			$string->get_name(),
			$string->get_value(),
			$string->get_status()
		);
	}

	/**
	 * @param WPML_ST_Models_String_Translation $translation
	 *
	 * @return string
	 */
	private function build_translation_row( WPML_ST_Models_String_Translation $translation ) {
		return $this->wpdb->prepare(
			'(%d, %s, %d, %s)',
			$translation->get_string_id(),
			$translation->get_language(),
			$translation->get_status(),
			$translation->get_mo_string()
		);
	}
}
