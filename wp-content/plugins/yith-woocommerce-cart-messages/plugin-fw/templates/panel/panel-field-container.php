<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Field Container for YIT Panel
 *
 * @package    YITH
 * @author     Leanza Francesco <leanzafrancesco@gmail.com>
 * @since      3.0.0
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$id   = $this->get_id_field( $option[ 'id' ] );
$name = $this->get_name_field( $option[ 'id' ] );
$type = $option[ 'type' ];

$field            = $option;
$field[ 'id' ]    = $id;
$field[ 'name' ]  = $name;
$field[ 'value' ] = $db_value;
if ( !empty( $custom_attributes ) )
    $field[ 'custom_attributes' ] = $custom_attributes;

?>
<div id="<?php echo $id ?>-container" class="yit_options yith-plugin-fw-field-wrapper yith-plugin-fw-<?php echo $type ?>-field-wrapper" <?php echo yith_panel_field_deps_data( $option, $this ) ?>>
    <div class="option">
        <?php yith_plugin_fw_get_field( $field, true, false ); ?>
    </div>
    <span class="description"><?php echo $option[ 'desc' ] ?></span>

    <div class="clear"></div>
</div>

