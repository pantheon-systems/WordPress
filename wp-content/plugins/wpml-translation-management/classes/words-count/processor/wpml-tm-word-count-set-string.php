<?php

class WPML_TM_Word_Count_Set_String {

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/** @var WPML_TM_Word_Calculator $calculator */
	private $calculator;

	public function __construct( WPML_TM_Word_Count_Records $records, WPML_TM_Word_Calculator $calculator ) {
		$this->records    = $records;
		$this->calculator = $calculator;
	}

	/**
	 * @param int $string_id
	 */
	public function process( $string_id ) {
		$string     = $this->records->get_string_value_and_language( $string_id );
		$word_count = $this->calculator->count_words( $string->value, $string->language );
		$this->records->set_string_word_count( $string_id, $word_count );
	}
}
