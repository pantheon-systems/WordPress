<?php
function wp_all_import_get_parent_post($identity, $post_type, $import_type = 'post') {
    $page = 0;
    switch ($import_type) {
        case 'post':
            if ( ! empty($identity) ){
                if (ctype_digit($identity)){
                    $page = get_post($identity);
                }
                else
                {
                    $page = get_page_by_title($identity, OBJECT, $post_type) or $page = get_page_by_path($identity, OBJECT, $post_type);

                    if ( empty($page) ){
                        $args = array(
                            'name' => $identity,
                            'post_type' => $post_type,
                            'post_status' => 'any',
                            'numberposts' => 1
                        );
                        $my_posts = get_posts($args);
                        if ( $my_posts ) {
                            $page = $my_posts[0];
                        }
                    }
                }
            }
            break;

        case 'page':
            $page = get_page_by_title($identity) or $page = get_page_by_path($identity) or ctype_digit($identity) and $page = get_post($identity);
            break;

        default:
            # code...
            break;
    }
    return (!empty($page)) ? (int) $page->ID : 0;
} 