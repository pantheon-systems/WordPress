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
$multiple      = isset( $multiple ) && $multiple;
$multiple_html = ( $multiple ) ? ' multiple' : '';

if ( $multiple && !is_array( $value ) )
    $value = array();

$class = isset( $class ) ? $class : 'yith-plugin-fw-select';
?>
    <select<?php echo $multiple_html ?>
        id="<?php echo $id ?>"
        name="<?php echo $name ?><?php if ( $multiple ) echo "[]" ?>" <?php if ( isset( $std ) ) : ?>
        data-std="<?php echo ( $multiple ) ? implode( ' ,', $std ) : $std ?>"<?php endif ?>
        class="<?php echo $class ?>"
        <?php echo $custom_attributes ?>
        <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>>
        <?php foreach ( $options as $key => $item ) : ?>
            <option value="<?php echo esc_attr( $key ) ?>" <?php if ( $multiple ): selected( true, in_array( $key, $value ) );
            else: selected( $key, $value ); endif; ?> ><?php echo $item ?></option>
        <?php endforeach; ?>
    </select>

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