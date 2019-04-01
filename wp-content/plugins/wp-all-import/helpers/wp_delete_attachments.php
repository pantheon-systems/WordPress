<?php
/**
 * Delete attachments linked to a specified post
 * @param int $parent_id Parent id of post to delete attachments for
 */
function wp_delete_attachments($parent_id, $unlink = true, $type = 'images') {	

	if ( $type == 'images' and has_post_thumbnail($parent_id) ) delete_post_thumbnail($parent_id);

	$ids = array();

	$attachments = get_posts(array('post_parent' => $parent_id, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null));

    foreach ($attachments as $attach) {
        if ( ($type == 'files' && ! wp_attachment_is_image( $attach->ID )) || ($type == 'images' && wp_attachment_is_image( $attach->ID ))) {
            if ($unlink) {
                if (!empty($attach->ID)) {
                    $file = get_attached_file($attach->ID);
                    if (@file_exists($file)) {
                        wp_delete_attachment($attach->ID, TRUE);
                    }
                }
            }
            else {
                $ids[] = $attach->ID;
            }
        }
    }

    global $wpdb;
				
	if ( ! empty( $ids ) ) {

		$ids_string = implode( ',', $ids );
		// unattach
		$result = $wpdb->query( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND ID IN ( $ids_string )" );

		foreach ( $ids as $att_id ) {
			clean_attachment_cache( $att_id );
		}
	}

	return $ids;
}