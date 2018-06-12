<?php

/**
 * Find duplicates according to settings
 */
function pmxi_findDuplicates($articleData, $custom_duplicate_name = '', $custom_duplicate_value = '', $duplicate_indicator = 'title', $indicator_value = '') {
    global $wpdb;

    if ('custom field' == $duplicate_indicator) {

        $duplicate_ids = array();

        if (!empty($articleData['post_type'])) {

            switch ($articleData['post_type']) {

                case 'taxonomies':
                    $args = array(
                        'hide_empty' => FALSE,
                        // also retrieve terms which are not used yet
                        'meta_query' => array(
                            array(
                                'key' => $custom_duplicate_name,
                                'value' => $custom_duplicate_value,
                                'compare' => '='
                            )
                        )
                    );

                    $terms = get_terms($articleData['taxonomy'], $args);

                    if (!empty($terms) && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $duplicate_ids[] = $term->term_id;
                        }
                    }

                    break;

                default:

                    $post_types = (class_exists('PMWI_Plugin') and $articleData['post_type'] == 'product') ? array(
                        'product',
                        'product_variation'
                    ) : array($articleData['post_type']);

                    $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS " . $wpdb->posts . ".ID FROM " . $wpdb->posts . " INNER JOIN " . $wpdb->postmeta . " ON ( " . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id ) WHERE 1=1 AND ( ( " . $wpdb->postmeta . ".meta_key = %s AND (" . $wpdb->postmeta . ".meta_value = %s OR " . $wpdb->postmeta . ".meta_value = %s OR REPLACE(REPLACE(REPLACE(" . $wpdb->postmeta . ".meta_value, ' ', ''), '\\t', ''), '\\n', '') = %s) ) ) AND " . $wpdb->posts . ".post_type IN ('" . implode("','", $post_types) . "') AND ((" . $wpdb->posts . ".post_status <> 'trash' AND " . $wpdb->posts . ".post_status <> 'auto-draft')) GROUP BY " . $wpdb->posts . ".ID ORDER BY " . $wpdb->posts . ".ID ASC LIMIT 0, 15", trim($custom_duplicate_name), trim($custom_duplicate_value), htmlspecialchars(trim($custom_duplicate_value)), preg_replace('%[ \\t\\n]%', '', trim($custom_duplicate_value)));

                    $query = $wpdb->get_results($sql);

                    if (!empty($query)) {
                        foreach ($query as $p) {
                            $duplicate_ids[] = $p->ID;
                        }
                    }

                    if (empty($duplicate_ids)) {

                        $query = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS " . $wpdb->posts . ".ID FROM " . $wpdb->posts . " INNER JOIN " . $wpdb->postmeta . " ON (" . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id) WHERE 1=1 AND " . $wpdb->posts . ".post_type IN ('" . implode("','", $post_types) . "') AND (" . $wpdb->posts . ".post_status = 'publish' OR " . $wpdb->posts . ".post_status = 'future' OR " . $wpdb->posts . ".post_status = 'draft' OR " . $wpdb->posts . ".post_status = 'pending' OR " . $wpdb->posts . ".post_status = 'trash' OR " . $wpdb->posts . ".post_status = 'private') AND ( (" . $wpdb->postmeta . ".meta_key = '%s' AND (" . $wpdb->postmeta . ".meta_value = '%s' OR " . $wpdb->postmeta . ".meta_value = '%s' OR " . $wpdb->postmeta . ".meta_value = '%s') ) ) GROUP BY " . $wpdb->posts . ".ID ORDER BY " . $wpdb->posts . ".ID ASC LIMIT 0, 20", trim($custom_duplicate_name), trim($custom_duplicate_value), htmlspecialchars(trim($custom_duplicate_value)), esc_attr(trim($custom_duplicate_value))));

                        if (!empty($query)) {
                            foreach ($query as $p) {
                                $duplicate_ids[] = $p->ID;
                            }
                        }
                    }
                    break;
            }

        }
        else {

            $args = array(
                'meta_query' => array(
                    0 => array(
                        'key' => $custom_duplicate_name,
                        'value' => $custom_duplicate_value,
                        'compare' => '='
                    )
                )
            );
            $user_query = new WP_User_Query($args);

            if (!empty($user_query->results)) {
                foreach ($user_query->results as $user) {
                    $duplicate_ids[] = $user->ID;
                }
            }
            else {
                $query = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS " . $wpdb->users . ".ID FROM " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . " ON (" . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id) WHERE 1=1 AND ( (" . $wpdb->usermeta . ".meta_key = '%s' AND " . $wpdb->usermeta . ".meta_value = '%s') ) GROUP BY " . $wpdb->users . ".ID ORDER BY " . $wpdb->users . ".ID ASC LIMIT 0, 20", $custom_duplicate_name, $custom_duplicate_value));

                if (!empty($query)) {
                    foreach ($query as $p) {
                        $duplicate_ids[] = $p->ID;
                    }
                }
            }
        }

        return $duplicate_ids;

    }
    elseif ('parent' == $duplicate_indicator) {

        $field = 'post_title'; // post_title or post_content
        return $wpdb->get_col($wpdb->prepare("
			SELECT ID FROM " . $wpdb->posts . "
			WHERE
				post_type = %s
				AND ID != %s
				AND post_parent = %s
				AND REPLACE(REPLACE(REPLACE($field, ' ', ''), '\\t', ''), '\\n', '') = %s
			",
            $articleData['post_type'],
            isset($articleData['ID']) ? $articleData['ID'] : 0,
            (!empty($articleData['post_parent'])) ? $articleData['post_parent'] : 0,
            preg_replace('%[ \\t\\n]%', '', $articleData[$field])
        ));
    }
    else {

        if (!empty($articleData['post_type'])) {
            switch ($articleData['post_type']) {
                case 'taxonomies':
                    $field = $duplicate_indicator == 'title' ? 'name' : 'slug';
                    if (empty($indicator_value)) {
                        $indicator_value = $duplicate_indicator == 'title' ? $articleData['post_title'] : $articleData['slug'];
                    }
                    return $wpdb->get_col($wpdb->prepare("
            SELECT t.term_id FROM " . $wpdb->terms . " t
            INNER JOIN " . $wpdb->term_taxonomy . " tt ON (t.term_id = tt.term_id)
            WHERE
                t.term_id != %s
                AND tt.taxonomy LIKE %s
                    AND (REPLACE(REPLACE(REPLACE(t." . $field . ", ' ', ''), '\\t', ''), '\\n', '') = %s
                        OR REPLACE(REPLACE(REPLACE(t." . $field . ", ' ', ''), '\\t', ''), '\\n', '') = %s
                            OR REPLACE(REPLACE(REPLACE(t." . $field . ", ' ', ''), '\\t', ''), '\\n', '') = %s) 
            ",
                        isset($articleData['ID']) ? $articleData['ID'] : 0,
                        isset($articleData['taxonomy']) ? $articleData['taxonomy'] : '%',
                        preg_replace('%[ \\t\\n]%', '', esc_attr($indicator_value)),
                        preg_replace('%[ \\t\\n]%', '', htmlentities($indicator_value)),
                        preg_replace('%[ \\t\\n]%', '', $indicator_value)
                    ));
                    break;
                default:
                    $field = 'post_' . $duplicate_indicator; // post_title or post_content
                    return $wpdb->get_col($wpdb->prepare("
            SELECT ID FROM " . $wpdb->posts . "
            WHERE
                post_type = %s
                AND ID != %s
                AND REPLACE(REPLACE(REPLACE($field, ' ', ''), '\\t', ''), '\\n', '') = %s
            ",
                        $articleData['post_type'],
                        isset($articleData['ID']) ? $articleData['ID'] : 0,
                        preg_replace('%[ \\t\\n]%', '', $articleData[$field])
                    ));
                    break;
            }
        }
        else {
            if ($duplicate_indicator == 'title') {
                $field = 'user_login';
                $u = get_user_by('login', $articleData[$field]);
                return (!empty($u)) ? array($u->ID) : FALSE;
            }
            else {
                $field = 'user_email';
                $u = get_user_by('email', $articleData[$field]);
                return (!empty($u)) ? array($u->ID) : FALSE;
            }
        }
    }
}