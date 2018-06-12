<?php
if (!function_exists('is_exists_term')){
	
	function is_exists_term( $term, $taxonomy = '', $parent = null ){		

		return apply_filters( 'wp_all_import_term_exists', term_exists( $term, $taxonomy, $parent ), $taxonomy, $term, $parent );
		
	}
}