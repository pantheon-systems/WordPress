<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once 'abstract-class-woe-formatter-sv.php';

class WOE_Formatter_Csv extends WOE_Formatter_sv {
	protected function delete_linebreaks_from_array( &$data ) {
		$data = array_map( array( $this, 'delete_linebreaks_callback' ), $data );
	}

	protected function delete_linebreaks_callback( $value ) {
		// show linebreaks as literals
		$value = str_replace( "\n", '\n', $value );
		$value = str_replace( "\r", '\r', $value );

		return $value;
	}

	protected function prepare_array( &$arr ) {
		if ( $this->settings['delete_linebreaks'] ) {
			$this->delete_linebreaks_from_array( $arr );
		}
		parent::prepare_array( $arr );
	}

}