<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

$backward_compatibility = false;
if ( !isset( $field[ 'buttons' ] ) ) {
    // backward compatibility
    $backward_compatibility = true;
    $button_data            = array();

    if ( isset( $field[ 'button-class' ] ) )
        $button_data[ 'class' ] = $field[ 'button-class' ];
    if ( isset( $field[ 'button-name' ] ) )
        $button_data[ 'name' ] = $field[ 'button-name' ];
    if ( isset( $field[ 'data' ] ) )
        $button_data[ 'data' ] = $field[ 'data' ];

    $buttons = array( $button_data );
}
$class = isset( $class ) ? $class : 'yith-plugin-fw-text-input';
?>
<input type="text" name="<?php echo $name ?>"
       id="<?php echo $id ?>"
       value="<?php echo esc_attr( $value ) ?>"
       class="<?php echo $class ?>"
       <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std ?>"<?php endif ?>
    <?php echo $custom_attributes ?>
    <?php if ( !$backward_compatibility && isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>/>

<?php
/* --------- BUTTONS ----------- */
if ( isset( $buttons ) ) {
    $button_field = array(
        'type'    => 'buttons',
        'buttons' => $buttons
    );
    yith_plugin_fw_get_field( $button_field, true );
}
?>
