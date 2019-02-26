<?php

/**
 * Class IWPML_Page_Builders_Module
 */
interface IWPML_Page_Builders_Module {
	/**
	 * @param string|int $node_id
	 * @param mixed $element
	 * @param WPML_PB_String[] $strings
	 *
	 * @return WPML_PB_String[]
	 */
	public function get( $node_id, $element, $strings );

	/**
	 * @param string|int $node_id
	 * @param mixed $element
	 * @param WPML_PB_String $string
	 *
	 * @return array
	 */
	public function update( $node_id, $element, WPML_PB_String $string );
}