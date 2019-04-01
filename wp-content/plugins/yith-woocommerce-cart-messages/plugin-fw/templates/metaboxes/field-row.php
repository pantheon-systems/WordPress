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
 *
 * [Important Note] the stored value is:
 *  - array                     if WooCommerce version >= 3.0.0
 *  - string (comma-separated)  otherwise
 */


if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

// metabox backward compatibility
if ( isset( $field[ 'label' ] ) )
    $field[ 'title' ] = $field[ 'label' ];

$default_field = array(
    'id'    => '',
    'title' => isset( $field[ 'name' ] ) ? $field[ 'name' ] : '',
    'desc'  => '',
);
$field         = wp_parse_args( $field, $default_field );

$display_field_only = isset( $field[ 'display-field-only' ] ) ? $field[ 'display-field-only' ] : false;
?>
<div id="<?php echo $field[ 'id' ] ?>-container" <?php echo yith_field_deps_data( $field ); ?> class="yith-plugin-fw-metabox-field-row">
    <?php if ( $display_field_only ) :
        yith_plugin_fw_get_field( $field, true );
    else: ?>
        <label for="<?php echo $field[ 'id' ] ?>"><?php echo $field[ 'title' ] ?></label>
        <?php yith_plugin_fw_get_field( $field, true ); ?>
        <div class="clear"></div>
        <span class="description"><?php echo $field[ 'desc' ] ?></span>
    <?php endif; ?>
</div>