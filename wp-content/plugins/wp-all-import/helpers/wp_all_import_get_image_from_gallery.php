<?php

function wp_all_import_get_image_from_gallery($image_name, $targetDir = FALSE, $bundle_type = 'images', $logger = false) {

    global $wpdb;

    $original_image_name = $image_name;

    if (!$targetDir) {
        $wp_uploads = wp_upload_dir();
        $targetDir = $wp_uploads['path'];
    }

    $attch = '';

    // search attachment by attached file
    $attachment_metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', $image_name, "%/" . $image_name));

    if (!empty($attachment_metas)) {
        foreach ($attachment_metas as $attachment_meta) {
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id));
            if (!empty($attch)) {
                $logger and call_user_func($logger, sprintf(__('- Found existing image with ID `%s` by meta key _wp_attached_file equals to `%s`...', 'wp_all_import_plugin'), $attch->ID, trim($image_name)));
                break;
            }
        }
    }

    if (empty($attch)) {
        $attachment_metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', sanitize_file_name($image_name), "%/" . sanitize_file_name($image_name)));

        if (!empty($attachment_metas)) {
            foreach ($attachment_metas as $attachment_meta) {
                $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id));
                if (!empty($attch)) {
                    $logger and call_user_func($logger, sprintf(__('- Found existing image with ID `%s` by meta key _wp_attached_file equals to `%s`...', 'wp_all_import_plugin'), $attch->ID, sanitize_file_name($image_name)));
                    break;
                }
            }
        }
    }

    if (empty($attch)) {
        $wp_filetype = wp_check_filetype(basename($image_name), NULL);

        if (!empty($wp_filetype['type'])) {
            // search attachment by file name with extension
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = %s AND post_mime_type = %s;", $image_name, preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name), sanitize_title($image_name), "attachment", $wp_filetype['type']));
        }

        if (!empty($attch)){
            $logger and call_user_func($logger, sprintf(__('- Found existing image with ID `%s` by post_title or post_name equals to `%s`...', 'wp_all_import_plugin'), $attch->ID, preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name)));
        }

        // search attachment by file name without extension
        if (empty($attch) and !empty($wp_filetype['type'])) {
            $attachment_title = explode(".", $image_name);
            if (is_array($attachment_title) and count($attachment_title) > 1) {
                array_pop($attachment_title);
            }
            $image_name = implode(".", $attachment_title);
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = %s AND post_mime_type LIKE %s;", $image_name, preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name), sanitize_title($image_name), "attachment", $wp_filetype['type']));
            if (!empty($attch)){
                $logger and call_user_func($logger, sprintf(__('- Found existing image with ID `%s` by post_title or post_name equals to `%s`...', 'wp_all_import_plugin'), $attch->ID, preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name)));
            }
        }
    }

    // search attachment by file headers
    if (empty($attch) and @file_exists($targetDir . DIRECTORY_SEPARATOR . $original_image_name)) {
        if ($bundle_type == 'images' and ($img_meta = wp_read_image_metadata($targetDir . DIRECTORY_SEPARATOR . $original_image_name))) {
            if (trim($img_meta['title']) && !is_numeric(sanitize_title($img_meta['title']))) {
                $img_title = $img_meta['title'];
                $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = %s AND post_mime_type LIKE %s;", $img_title, "attachment", "image%"));
                if (!empty($attch)){
                    $logger and call_user_func($logger, sprintf(__('- Found existing image with ID `%s` by post_title equals to `%s`...', 'wp_all_import_plugin'), $attch->ID, $img_title));
                }
            }
        }
        if (empty($attch)){
            @unlink($targetDir . DIRECTORY_SEPARATOR . $original_image_name);
        }
    }

    return apply_filters('wp_all_import_get_image_from_gallery', $attch, $original_image_name, $targetDir);
} 