<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 30/10/17
 * Time: 9:09 PM
 */

class WPML_WP_Taxonomy implements IWPML_WP_Element_Type {

	public static function get_linked_post_types( $taxonomy ) {
		global $wp_taxonomies;

		$post_types = array();
		if ( isset( $wp_taxonomies[ $taxonomy ] ) && isset( $wp_taxonomies[ $taxonomy ]->object_type ) ) {
			$post_types = $wp_taxonomies[ $taxonomy ]->object_type;
		}

		return $post_types;
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return false|WP_Taxonomy
	 */
	public function get_wp_element_type_object( $taxonomy ) {
		return get_taxonomy( $taxonomy );
	}
}
