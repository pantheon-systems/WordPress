<?php
/**
 * Fires once an attachment has been added.
 *
 * @since 2.0.0
 *
 * @param int $post_ID Attachment ID.
 */
function pmxi_add_attachment($post_ID){
    // add image filename to pmxi_images table
    if (wp_attachment_is_image($post_ID)){
        $imageRecord = new PMXI_Image_Record();
        $imageRecord->getBy(array(
            'attachment_id' => $post_ID
        ));
        if ($imageRecord->isEmpty()){
            $image_name = basename(wp_get_attachment_url( $post_ID ));
            $imageRecord->set(array(
                'attachment_id' => $post_ID,
                'image_url' => '',
                'image_filename' =>  $image_name
            ))->insert();
        }
    }
}