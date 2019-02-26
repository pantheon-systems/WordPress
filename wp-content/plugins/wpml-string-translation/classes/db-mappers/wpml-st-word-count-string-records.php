<?php

class WPML_ST_Word_Count_String_Records {

	/** @var wpdb */
	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/** @return int */
	public function get_total_words() {
		return (int) $this->wpdb->get_var( "SELECT SUM(word_count) FROM {$this->wpdb->prefix}icl_strings" );
	}

	/** @return array */
	public function get_all_values_without_word_count() {
		$query = "
			SELECT id, value FROM {$this->wpdb->prefix}icl_strings
			WHERE word_count IS NULL
		";

		return $this->wpdb->get_results( $query );
	}

	/**
	 * @param string      $lang
	 * @param null|string $package_id
	 *
	 * @return int
	 */
	public function get_words_to_translate_per_lang( $lang, $package_id = null ) {
		$query = "
			SELECT SUM(word_count) FROM {$this->wpdb->prefix}icl_strings AS s
			LEFT JOIN {$this->wpdb->prefix}icl_string_translations AS st
				ON st.string_id = s.id AND st.language = %s
			WHERE (st.status <> %d OR st.status IS NULL)
		";

		$prepare_args = array(
			$lang,
			ICL_STRING_TRANSLATION_COMPLETE,
		);

		if ( $package_id ) {
			$query          .= " AND s.string_package_id = %d";
			$prepare_args[] = $package_id;
		}

		return (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $prepare_args ) );
	}

	/**
	 * @param int $string_id
	 *
	 * @return stdClass
	 */
	public function get_value_and_language( $string_id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT value, language FROM {$this->wpdb->prefix}icl_strings WHERE id = %d",
				$string_id
			)
		);
	}

	/**
	 * @param int $string_id
	 * @param int $word_count
	 */
	public function set_word_count( $string_id, $word_count ) {
		$this->wpdb->update(
			$this->wpdb->prefix . 'icl_strings',
			array( 'word_count' => $word_count ),
			array( 'id' => $string_id )
		);
	}

	/**
	 * @param int $string_id
	 *
	 * @return int
	 */
	public function get_word_count( $string_id ) {
		return (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT word_count FROM {$this->wpdb->prefix}icl_strings WHERE ID = %d",
				$string_id
			)
		);
	}

	public function reset_all() {
		$this->wpdb->query( "UPDATE {$this->wpdb->prefix}icl_strings SET word_count = NULL" );
	}

	/**
	 * @param array $package_ids
	 *
	 * @return array
	 */
	public function get_ids_from_package_ids( array $package_ids ) {
		if ( ! $package_ids ) {
			return array();
		}

		$query = "SELECT id FROM {$this->wpdb->prefix}icl_strings
				  WHERE string_package_id IN(" . wpml_prepare_in( $package_ids ) . ")";

		return array_map( 'intval', $this->wpdb->get_col( $query ) );
	}
}
