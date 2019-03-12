<?php

/**
 Class for WooCommerce Advanced Ajax Layered Navigation
 */

class WCML_Ajax_Layered_Nav_Widget {
	function __construct() {
		add_filter('wc_ajax_layered_nav_sizeselector_term_id', array($this, 'wc_ajax_layered_nav_sizeselector_term_id'));
		add_filter('wc_ajax_layered_nav_query_editor', array($this, 'wc_ajax_layered_nav_query_editor'),10,3);
	}

	function wc_ajax_layered_nav_sizeselector_term_id($term_id) {
		$ulanguage_code = apply_filters( 'wpml_default_language', null );
		$term_id 		= apply_filters( 'wpml_object_id', $term_id, 'category', true, $ulanguage_code );
		return $term_id;
	}

function wc_ajax_layered_nav_query_editor($posts, $attribute, $value){
	$posts = get_posts(
		array(
			'post_type' 	=> 'product',
			'numberposts' 	=> -1,
			'post_status' 	=> 'publish',
			'fields' 		=> 'ids',
			'no_found_rows' => true,
			'tax_query' => array(
				array(
					'taxonomy' 	=> $attribute,
					'terms' 	=> $value,
					'field' 	=> 'term_id'
				)
			)
		)
	);
	return $posts;
	}
}
