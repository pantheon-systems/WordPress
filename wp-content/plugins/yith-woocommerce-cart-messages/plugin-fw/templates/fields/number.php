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

$min_max_attr = $step_attr = '';

if ( isset( $min ) ) {
    $min_max_attr .= " min='{$min}'";
}

if ( isset( $max ) ) {
    $min_max_attr .= " max='{$max}'";
}

if ( isset( $step ) ) {
    $step_attr .= "step='{$step}'";
}
?>
<input type="number" id="<?php echo $id ?>"
    <?php echo !empty( $class ) ? "class='$class'" : ''; ?>
       name="<?php echo $name ?>" <?php echo $step_attr ?> <?php echo $min_max_attr ?>
       value="<?php echo esc_attr( $value ) ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std ?>"<?php endif ?>
    <?php echo $custom_attributes ?>
    <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>/>