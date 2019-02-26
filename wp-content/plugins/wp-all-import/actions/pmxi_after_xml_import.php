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

            // Update term count after import process is complete.
            foreach ( (array) get_object_taxonomies( $import->options['custom_type'] ) as $taxonomy ) {
                $term_ids = get_terms(
                    array(
                        'taxonomy'   => $taxonomy,
                        'hide_empty' => false,
                        'fields' => 'ids',
                    )
                );
                wp_update_term_count_now( $term_ids, $taxonomy );
            }
        }

        // Update post count only once after import process is completed.
        wp_all_import_update_post_count();
    }

    // Add removed action during import.
    add_action( 'transition_post_status', '_update_term_count_on_transition_post_status', 10, 3 );
    add_action( 'transition_post_status', '_update_posts_count_on_transition_post_status', 10, 3 );
    add_action( 'post_updated', 'wp_save_post_revision', 10, 1 );
}