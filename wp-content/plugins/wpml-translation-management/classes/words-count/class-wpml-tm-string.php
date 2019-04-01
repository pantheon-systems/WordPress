<?php

class WPML_TM_String extends WPML_TM_Translatable_Element {

	protected function init( $id ) {}

	protected function get_type() {
		return 'string';
	}

	protected function get_total_words() {
		return $this->word_count_records->get_string_word_count( $this->id );
	}

	public function get_type_name( $label = null ) {
		return __( 'String', 'wpml-translation-management' );
	}
}