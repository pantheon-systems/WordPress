<?php

class WPML_TM_Count_Composite implements IWPML_TM_Count {

	/** @var IWPML_TM_Count[] $counts */
	private $counts = array();

	public function add_count( IWPML_TM_Count $count ) {
		$this->counts[] = $count;
	}

	/** @var IWPML_TM_Count[] $counts */
	public function add_counts( $counts ) {
		foreach ( $counts as $count ) {
			$this->add_count( $count );
		}
	}

	/**
	 * @param string $lang
	 *
	 * @return int
	 */
	public function get_words_to_translate( $lang ) {
		$words = 0;

		foreach ( $this->counts as $count ) {
			$words += $count->get_words_to_translate( $lang );
		}

		return $words;
	}

	/** @return int */
	public function get_total_words() {
		$words = 0;

		foreach ( $this->counts as $count ) {
			$words += $count->get_total_words();
		}

		return $words;
	}
}