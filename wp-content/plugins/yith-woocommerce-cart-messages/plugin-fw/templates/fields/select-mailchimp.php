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
$multiple_html = ( isset( $multiple ) && $multiple ) ? ' multiple' : '';
?>

<select<?php echo $multiple_html ?>
    id="<?php echo $id ?>"
    name="<?php echo $name ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std ?>"<?php endif ?>
    class="yith-plugin-fw-select"
    <?php echo $custom_attributes ?>
    <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>>
    <?php foreach ( $options as $key => $item ) : ?>
        <option value="<?php echo $key ?>"<?php selected( $key, $value ) ?>><?php echo $item ?></option>
    <?php endforeach; ?>
</select>
<input type="button" class="button-secondary <?php echo isset( $class ) ? $class : ''; ?>" value="<?php echo $button_name ?>"/>
<span class="spinner"></span>
