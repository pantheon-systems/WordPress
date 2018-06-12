<?php

function wp_all_import_get_image_from_gallery($image_name, $targetDir = FALSE, $bundle_type = 'images') {
    global $wpdb;

    $original_image_name = $image_name;

    if (!$targetDir) {
        $wp_uploads = wp_upload_dir();
        $targetDir = $wp_uploads['path'];
    }

    $attch = '';

    // search attachment by attached file
    $attachment_metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', $image_name, "%/" . $image_name));

//  if (empty($attachment_metas)){
//      $attachment_metas  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', sanitize_file_name($image_name), "%/" . sanitize_file_name($image_name) ) );
//  }

    if (!empty($attachment_metas)) {
        foreach ($attachment_metas as $attachment_meta) {
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id));
            if (!empty($attch)) {
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

        // search attachment by file name without extension
        if (empty($attch)) {
            $attachment_title = explode(".", $image_name);
            if (is_array($attachment_title) and count($attachment_title) > 1) {
                array_pop($attachment_title);
            }
            $image_name = implode(".", $attachment_title);
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = %s AND post_mime_type LIKE %s;", $image_name, preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name), sanitize_title($image_name), "attachment", "image%"));
        }
    }

    // search attachment by file headers
    if (empty($attch) and @file_exists($targetDir . DIRECTORY_SEPARATOR . $original_image_name)) {
        if ($bundle_type == 'images' and ($img_meta = wp_read_image_metadata($targetDir . DIRECTORY_SEPARATOR . $original_image_name))) {
            if (trim($img_meta['title']) && !is_numeric(sanitize_title($img_meta['title']))) {
                $img_title = $img_meta['title'];
                $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = %s AND post_mime_type LIKE %s;", $img_title, "attachment", "image%"));
            }
        }
        if (empty($attch)){
            @unlink($targetDir . DIRECTORY_SEPARATOR . $original_image_name);
        }
    }

    return apply_filters('wp_all_import_get_image_from_gallery', $attch, $original_image_name, $targetDir);
} 