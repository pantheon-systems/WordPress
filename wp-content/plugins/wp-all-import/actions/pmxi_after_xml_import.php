<?php
function pmxi_pmxi_after_xml_import( $import_id, $import )
{    
    if ( ! in_array($import->options['custom_type'], array('taxonomies', 'import_users')) ) {
        $custom_type = get_post_type_object( $import->options['custom_type'] );
        if ( ! empty($custom_type) && $custom_type->hierarchical ){
            $parent_posts = get_option('wp_all_import_posts_hierarchy_' . $import_id);
            if (!empty($parent_posts)){
                foreach ($parent_posts as $pid => $identity){
                    $parent_post = wp_all_import_get_parent_post($identity, $import->options['custom_type'], $import->options['type']);
                    if (!empty($parent_post) && !is_wp_error($parent_post)){
                        wp_update_post(array(
                            'ID' => $pid,
                            'post_parent' => $parent_post
                        ));
                    }
                }
            }
            delete_option('wp_all_import_posts_hierarchy_' . $import_id);
        }
    }
}