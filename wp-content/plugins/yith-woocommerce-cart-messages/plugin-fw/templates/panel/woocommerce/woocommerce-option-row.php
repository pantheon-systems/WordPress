<?php
/**
 * @var array  $field
 * @var string $description
 */
$default_field = array(
    'id'    => '',
    'title' => isset( $field[ 'name' ] ) ? $field[ 'name' ] : '',
    'desc'  => '',
);
$field         = wp_parse_args( $field, $default_field );

$display_row = !in_array( $field[ 'type' ], array( 'hidden', 'html', 'sep', 'simple-text', 'title' ) );
$display_row = isset( $field[ 'yith-display-row' ] ) ? !!$field[ 'yith-display-row' ] : $display_row;

$extra_row_classes = apply_filters( 'yith_plugin_fw_panel_wc_extra_row_classes', array(), $field );
$extra_row_classes = is_array( $extra_row_classes ) ? implode( ' ', $extra_row_classes ) : '';

?>
<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo $field[ 'type' ] ?> <?php echo $extra_row_classes ?>" <?php echo yith_field_deps_data( $field ) ?>>
    <?php if ( $display_row ) : ?>
        <th scope="row" class="titledesc">
            <label for="<?php echo esc_attr( $field[ 'id' ] ); ?>"><?php echo esc_html( $field[ 'title' ] ); ?></label>
        </th>
        <td class="forminp forminp-<?php echo sanitize_title( $field[ 'type' ] ) ?>">
            <?php yith_plugin_fw_get_field( $field, true ); ?>
            <?php echo '<span class="description">' . wp_kses_post( $field[ 'desc' ] ) . '</span>'; ?>
        </td>
    <?php else: ?>
        <td colspan="2">
            <?php yith_plugin_fw_get_field( $field, true ); ?>
        </td>
    <?php endif; ?>
</tr>