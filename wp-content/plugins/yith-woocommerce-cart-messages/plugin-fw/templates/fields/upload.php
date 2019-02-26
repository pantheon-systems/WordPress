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
?>
<input type="text" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?php echo esc_attr( $value ) ?>" <?php if ( isset( $default ) ) : ?>data-std="<?php echo $default ?>"<?php endif ?> class="yith-plugin-fw-upload-img-url"/>
<input type="button" class="button-secondary yith-plugin-fw-upload-button" id="<?php echo $id ?>-button" value="<?php _e( 'Upload', 'yith-plugin-fw' ) ?>"/>
<input type="button" value="<?php _e( 'Reset', 'yith-plugin-fw' ) ?>" id="<?php echo $id ?>-button-reset" class="yith-plugin-fw-upload-button-reset button"
       data-default="<?php echo isset( $default ) ? $default : '' ?>"/>
<div class="yith-plugin-fw-upload-img-preview" style="margin-top:10px;">
    <?php
    $file = $value;
    if ( preg_match( '/(jpg|jpeg|png|gif|ico)$/', $file ) ) {
        echo "<img src='$file' style='max-width:600px; max-height:300px;' />";
    }
    ?>
</div>