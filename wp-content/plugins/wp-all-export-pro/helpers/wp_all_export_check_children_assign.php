<?php
function wp_all_export_check_children_assign( $parent, $taxonomy, $term_ids = array() )
{
	$is_latest_child = true;
    $children = get_term_children( $parent, $taxonomy );
    if ( count($children) > 0 ){
        foreach ($children as $child) {
            if ( in_array($child, $term_ids) ){
                $is_latest_child = false;
                break;
            }
        }
    }
    return $is_latest_child;
}