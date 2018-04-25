<?php
/**
 *    AJAX action export processing
 */
function pmxe_wp_ajax_wpallexport()
{

    if (!check_ajax_referer('wp_all_export_secure', 'security', false)) {
        exit(__('Security check', 'wp_all_export_plugin'));
    }

    if (!current_user_can(PMXE_Plugin::$capabilities)) {
        exit(__('Security check', 'wp_all_export_plugin'));
    }

    $input = new PMXE_Input();
    $export_id = $input->get('id', 0);
    if (empty($export_id)) {
        $export_id = (!empty(PMXE_Plugin::$session->update_previous)) ? PMXE_Plugin::$session->update_previous : 0;
    }

    $wp_uploads = wp_upload_dir();

    $export = new PMXE_Export_Record();

    $export->getById($export_id);

    if ($export->isEmpty()) {
        exit(__('Export is not defined.', 'wp_all_export_plugin'));
    }

    $exportOptions = $export->options + PMXE_Plugin::get_default_import_options();

    wp_reset_postdata();

    XmlExportEngine::$exportOptions = $exportOptions;
    XmlExportEngine::$is_user_export = $exportOptions['is_user_export'];
    XmlExportEngine::$is_comment_export = $exportOptions['is_comment_export'];
    XmlExportEngine::$is_taxonomy_export = empty($exportOptions['is_taxonomy_export']) ? false : $exportOptions['is_taxonomy_export'];
    XmlExportEngine::$exportID = $export_id;
    XmlExportEngine::$exportRecord = $export;

    if (class_exists('SitePress') && !empty(XmlExportEngine::$exportOptions['wpml_lang'])) {
        do_action('wpml_switch_language', XmlExportEngine::$exportOptions['wpml_lang']);
    }

    $errors = new WP_Error();
    $engine = new XmlExportEngine($exportOptions, $errors);

    $posts_per_page = $exportOptions['records_per_iteration'];

    if ($exportOptions['export_type'] == 'advanced') {
        if (XmlExportEngine::$is_user_export) {
            add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
            $exportQuery = eval('return new WP_User_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => ' . $export->exported . ', \'number\' => ' . $posts_per_page . ' ));');
            remove_action('pre_user_query', 'wp_all_export_pre_user_query');
        } elseif (XmlExportEngine::$is_comment_export) {
            add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
            $exportQuery = eval('return new WP_Comment_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => ' . $export->exported . ', \'number\' => ' . $posts_per_page . ' ));');
            remove_action('comments_clauses', 'wp_all_export_comments_clauses');
        } else {
            remove_all_actions('parse_query');
            remove_all_actions('pre_get_posts');
            remove_all_filters('posts_clauses');

            add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);
            add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
            $code = 'return new WP_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => ' . $export->exported . ', \'posts_per_page\' => ' . $posts_per_page . ' ));';
            $exportQuery = eval($code);

            remove_filter('posts_where', 'wp_all_export_posts_where');
            remove_filter('posts_join', 'wp_all_export_posts_join');
        }
    } else {
        XmlExportEngine::$post_types = $exportOptions['cpt'];

        // $is_products_export = ($exportOptions['cpt'] == 'product' and class_exists('WooCommerce'));

        if (in_array('users', $exportOptions['cpt']) or in_array('shop_customer', $exportOptions['cpt'])) {
            add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
            $exportQuery = new WP_User_Query(array('orderby' => 'ID', 'order' => 'ASC', 'number' => $posts_per_page, 'offset' => $export->exported));
            remove_action('pre_user_query', 'wp_all_export_pre_user_query');
        } elseif (in_array('taxonomies', $exportOptions['cpt'])) {
            add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
            $exportQuery = new WP_Term_Query(array('taxonomy' => $exportOptions['taxonomy_to_export'], 'orderby' => 'term_id', 'order' => 'ASC', 'number' => $posts_per_page, 'offset' => $export->exported, 'hide_empty' => false));
            $postCount = count($exportQuery->get_terms());
            remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
        } elseif (in_array('comments', $exportOptions['cpt'])) {
            add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);

            global $wp_version;

            if (version_compare($wp_version, '4.2.0', '>=')) {
                $exportQuery = new WP_Comment_Query(array('orderby' => 'comment_ID', 'order' => 'ASC', 'number' => $posts_per_page, 'offset' => $export->exported));
            } else {
                $exportQuery = get_comments(array('orderby' => 'comment_ID', 'order' => 'ASC', 'number' => $posts_per_page, 'offset' => $export->exported));
            }
            remove_action('comments_clauses', 'wp_all_export_comments_clauses');
        } else {
            remove_all_actions('parse_query');
            remove_all_actions('pre_get_posts');
            remove_all_filters('posts_clauses');

            add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);
            add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
            $exportQuery = new WP_Query(array('post_type' => $exportOptions['cpt'], 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC', 'offset' => $export->exported, 'posts_per_page' => $posts_per_page));
            remove_filter('posts_where', 'wp_all_export_posts_where');
            remove_filter('posts_join', 'wp_all_export_posts_join');
        }
    }

    XmlExportEngine::$exportQuery = $exportQuery;

    $engine->init_additional_data();

    // get total founded records
    if (XmlExportEngine::$is_comment_export) {
        global $wp_version;

        if (version_compare($wp_version, '4.2.0', '>=')) {
            $postCount = count($exportQuery->get_comments());
            add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
            $result = new WP_Comment_Query(array('orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));
            $foundPosts = $result->get_comments();
            remove_action('comments_clauses', 'wp_all_export_comments_clauses');
        } else {
            $postCount = count($exportQuery);
            add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
            $foundPosts = get_comments(array('orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));
            remove_action('comments_clauses', 'wp_all_export_comments_clauses');
        }
    } elseif (XmlExportEngine::$is_taxonomy_export) {
        add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
        $result = new WP_Term_Query(array('taxonomy' => $exportOptions['taxonomy_to_export'], 'orderby' => 'term_id', 'order' => 'ASC', 'hide_empty' => false));
        $foundPosts = count($result->get_terms());
        remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
    } else {

        if(XmlExportEngine::$is_user_export) {
            $foundPosts = $exportQuery->get_total();
            $postCount = count($exportQuery->get_results());
        } else {
            $foundPosts = $exportQuery->found_posts;
            $postCount = $exportQuery->post_count;
        }
    }
    // [ \get total founded records ]

    if (!$export->exported) {
        $attachment_list = $export->options['attachment_list'];
        if (!empty($attachment_list)) {
            foreach ($attachment_list as $attachment) {
                if (!is_numeric($attachment)) {
                    @unlink($attachment);
                }
            }
        }
        $exportOptions['attachment_list'] = array();
        $export->set(array(
            'options' => $exportOptions
        ))->save();

        $is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

        if ($is_secure_import and !empty($exportOptions['filepath'])) {

            $exportOptions['filepath'] = '';
        }

        PMXE_Plugin::$session->set('count', $foundPosts);
        PMXE_Plugin::$session->save_data();
    }

    $functions = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
    if (@file_exists($functions))
        require_once $functions;

    // Export posts
    XmlCsvExport::export();

    $export->set(array(
        'exported' => $export->exported + $postCount,
        'last_activity' => date('Y-m-d H:i:s')
    ))->save();


    if ($posts_per_page != -1 && $postCount && !isAdvancedSingleItemExport($postCount, $foundPosts)) {

        $percentage = ceil(($export->exported / $foundPosts) * 100);

        wp_send_json(array(
            'export_id' => $export->id,
            'queue_export' => false,
            'exported' => $export->exported,
            'percentage' => $percentage,
            'done' => false,
            'posts' => $postCount,
            'code' => $code,
            'records_per_request' => $exportOptions['records_per_iteration']
        ));
    } else {
        if (file_exists(PMXE_Plugin::$session->file)) {

            if ($exportOptions['export_to'] == 'xml') {
                switch (XmlExportEngine::$exportOptions['xml_template_type']) {
                    case 'custom':
                        require_once PMXE_ROOT_DIR . '/classes/XMLWriter.php';
                        file_put_contents(PMXE_Plugin::$session->file, PMXE_XMLWriter::preprocess_xml(XmlExportEngine::$exportOptions['custom_xml_template_footer']), FILE_APPEND);
                        break;
                    default:

                        break;
                }

                if (!in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) {
                    $main_xml_tag = apply_filters('wp_all_export_main_xml_tag', $exportOptions['main_xml_tag'], $export->id);

                    file_put_contents(PMXE_Plugin::$session->file, '</' . $main_xml_tag . '>', FILE_APPEND);

                    $xml_footer = apply_filters('wp_all_export_xml_footer', '', $export->id);

                    if (!empty($xml_footer)) file_put_contents(PMXE_Plugin::$session->file, $xml_footer, FILE_APPEND);
                }
            }

            $is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

            if (!$is_secure_import) {

                if (!$export->isEmpty()) {

                    $wp_filetype = wp_check_filetype(basename(PMXE_Plugin::$session->file), null);
                    $attachment_data = array(
                        'guid' => $wp_uploads['baseurl'] . '/' . _wp_relative_upload_path(PMXE_Plugin::$session->file),
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename(PMXE_Plugin::$session->file)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    if (empty($export->attch_id)) {
                        $attach_id = wp_insert_attachment($attachment_data, PMXE_Plugin::$session->file);
                    } elseif ($export->options['creata_a_new_export_file']) {
                        $attach_id = wp_insert_attachment($attachment_data, PMXE_Plugin::$session->file);
                    } else {
                        $attach_id = $export->attch_id;
                        $attachment = get_post($attach_id);
                        if ($attachment) {
                            update_attached_file($attach_id, PMXE_Plugin::$session->file);
                            wp_update_attachment_metadata($attach_id, $attachment_data);
                        } else {
                            $attach_id = wp_insert_attachment($attachment_data, PMXE_Plugin::$session->file);
                        }
                    }

                    if (!in_array($attach_id, $exportOptions['attachment_list'])) $exportOptions['attachment_list'][] = $attach_id;

                    $export->set(array(
                        'attch_id' => $attach_id,
                        'options' => $exportOptions
                    ))->save();
                }

            } else {
                $exportOptions['filepath'] = wp_all_export_get_relative_path(PMXE_Plugin::$session->file);

                if (!$export->isEmpty()) {
                    $export->set(array(
                        'options' => $exportOptions
                    ))->save();
                }

            }

            PMXE_Wpallimport::generateImportTemplate($export, PMXE_Plugin::$session->file, PMXE_Plugin::$session->count);

        }

        $export->set(array(
            'executing' => 0,
            'canceled' => 0,
            'iteration' => ++$export->iteration
        ))->save();

        do_action('pmxe_after_export', $export->id, $export);

        $queue_exports = empty($export->parent_id) ? array() : get_option('wp_all_export_queue_' . $export->parent_id);

        // update child exports with correct WHERE & JOIN filters
        if (!empty($export->options['cpt']) and class_exists('WooCommerce') and in_array('shop_order', $export->options['cpt']) and empty($export->parent_id)) {
            $queue_exports = XmlExportWooCommerceOrder::prepare_child_exports($export);
        }

        if (!empty($queue_exports) and !empty($export->parent_id)) {
            array_shift($queue_exports);
        }

        if (empty($queue_exports)) {
            delete_option('wp_all_export_queue_' . (empty($export->parent_id) ? $export->id : $export->parent_id));
        } else {
            update_option('wp_all_export_queue_' . (empty($export->parent_id) ? $export->id : $export->parent_id), $queue_exports);
        }

        wp_send_json(array(
            'export_id' => $export->id,
            'queue_export' => empty($queue_exports) ? false : $queue_exports[0],
            'exported' => $export->exported,
            'percentage' => 100,
            'done' => true,
            'records_per_request' => $exportOptions['records_per_iteration']
        ));
    }
}

/**
 * @param $postCount
 * @param $foundPosts
 * @return bool
 */
function isAdvancedSingleItemExport($postCount, $foundPosts)
{
    return ($postCount == 1 && $foundPosts == 1);
}