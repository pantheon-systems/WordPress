<?php
function pmxi_pmxi_after_xml_import( $import_id, $import )
{
    if ($import->options['custom_type'] == 'taxonomies') {
        $parent_terms = get_option('wp_all_import_taxonomies_hierarchy_' . $import_id);
        if (!empty($parent_terms)){
            foreach ($parent_terms as $term_id => $pterm){
                $parent_term = get_term_by('slug', $pterm, $import->options['taxonomy_type']) or $parent_term = get_term_by('name', $pterm, $import->options['taxonomy_type']) or ctype_digit($pterm) and $parent_term = get_term_by('id', $pterm, $import->options['taxonomy_type']);
                if (!empty($parent_term) && !is_wp_error($parent_term)){
                    wp_update_term($term_id, $import->options['taxonomy_type'], array(
                        'parent'      => $parent_term->term_id,
                    ));
                }
            }
        }
        delete_option('wp_all_import_taxonomies_hierarchy_' . $import_id);
    }
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