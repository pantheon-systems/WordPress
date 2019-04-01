<?php

interface IWPML_WP_Element_Type {

	/**
	 * @param string $element_name
	 *
	 * @return mixed
	 */
	public function get_wp_element_type_object( $element_name );

}
