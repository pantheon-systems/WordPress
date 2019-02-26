<?php

class WPML_WP_Post_Type implements IWPML_WP_Element_Type {

	/**
	 * @param string $post_type
	 *
	 * @return null|WP_Post_Type
	 */
	public function get_wp_element_type_object( $post_type ) {
		return get_post_type_object( $post_type );
	}

}
