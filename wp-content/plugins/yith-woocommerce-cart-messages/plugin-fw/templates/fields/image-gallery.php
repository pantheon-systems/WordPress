<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );
$array_id = array();
if ( !empty( $value ) ) {
    $array_id = array_filter( explode( ',', $value ) );
}
?>
<ul id="<?php echo $id ?>-extra-images" class="slides-wrapper extra-images ui-sortable clearfix">
    <?php if ( !empty( $array_id ) ) : ?>
        <?php foreach ( $array_id as $image_id ) : ?>
            <li class="image" data-attachment_id= <?php echo esc_attr( $image_id ) ?>>
                <a href="#">
                    <?php
                    if ( function_exists( 'yit_image' ) ) :
                        yit_image( "id=$image_id&size=admin-post-type-thumbnails" );
                    else:
                        echo wp_get_attachment_image( $image_id, array( 80, 80 ) );
                    endif; ?>
                </a>
                <ul class="actions">
                    <li><a href="#" class="delete" title="<?php _e( 'Delete image', 'yith-plugin-fw' ); ?>">x</a></li>
                </ul>
            </li>
        <?php endforeach; endif; ?>
</ul>
<input type="button" data-choose="<?php _e( 'Add Images to Gallery', 'yith-plugin-fw' ); ?>" data-update="<?php _e( 'Add to gallery', 'yith-plugin-fw' ); ?>" value="<?php _e( 'Add images', 'yith-plugin-fw' ) ?>" data-delete="<?php _e( 'Delete image', 'yith-plugin-fw' ); ?>" data-text="<?php _e( 'Delete', 'yith-plugin-fw' ); ?>" id="<?php echo $id ?>-button" class="image-gallery-button button"/>
<input type="hidden" class="image_gallery_ids" id="image_gallery_ids" name="<?php echo $name ?>" value="<?php echo esc_attr( $value ); ?>"/>
