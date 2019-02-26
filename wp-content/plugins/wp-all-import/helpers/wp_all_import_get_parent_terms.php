<?php
function wp_all_import_get_parent_terms( $term_id, $taxonomy ){

	$ids = array();

	// start from the current term
    $parent  = get_term_by( 'id', $term_id, $taxonomy );
    
    // climb up the hierarchy until we reach a term with parent = '0'
    while ( $parent->parent != '0' ){
        
        $term_id = $parent->parent;

        $parent  = get_term_by( 'id', $term_id, $taxonomy);

        $ids[]   = $parent->term_taxonomy_id;

    }

    return $ids;

}