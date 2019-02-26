<?php
/**
 * Fires once an existing attachment has been updated.
 *
 * @since 4.4.0
 *
 * @param int     $post_ID      Post ID.
 * @param WP_Post $post_after   Post object following the update.
 * @param WP_Post $post_before  Post object before the update.
 */
function pmxi_attachment_updated($post_ID, $post_after, $post_before){
    // update image filename in pmxi_images table
    if (wp_attachment_is_image($post_ID)){
        $imageRecord = new PMXI_Image_Record();
        $imageRecord->getBy(array(
            'attachment_id' => $post_ID
        ));
        if (!$imageRecord->isEmpty()){
            $image_name = basename(wp_get_attachment_url( $post_ID ));
            $imageRecord->set(array(
                'image_filename' =>  $image_name
            ))->update();
        }
    }
}