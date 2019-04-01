<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var $param Vc_Column_Offset
 * @var $sizes Vc_Column_Offset::$size_types
 */
$layouts = array(
	'xs' => 'portrait-smartphones',
	'sm' => 'portrait-tablets',
	'md' => 'landscape-tablets',
	'lg' => 'default',
);

?>
<div class="vc_column-offset" data-column-offset="true">
	<?php if ( '1' === vc_settings()->get( 'not_responsive_css' ) ) : ?>
		<div class="wpb_alert wpb_content_element vc_alert_rounded wpb_alert-warning">
			<div class="messagebox_text">
				<p><?php printf( __( 'Responsive design settings are currently disabled. You can enable them in WPBakery Page Builder <a href="%s">settings page</a> by unchecking "Disable responsive content elements".', 'js_composer' ), admin_url( 'admin.php?page=vc-general' ) ) ?></p>
			</div>
		</div>
	<?php endif ?>
	<input name="<?php echo esc_attr( $settings['param_name'] ) ?>"
			class="wpb_vc_param_value <?php echo esc_attr( $settings['param_name'] ) ?>
	<?php echo esc_attr( $settings['type'] ) ?> '_field" type="hidden" value="<?php echo esc_attr( $value ) ?>"/>
	<table class="vc_table vc_column-offset-table">
		<tr>
			<th>
				<?php _e( 'Device', 'js_composer' ) ?>
			</th>
			<th>
				<?php _e( 'Offset', 'js_composer' ) ?>
			</th>
			<th>
				<?php _e( 'Width', 'js_composer' ) ?>
			</th>
			<th>
				<?php _e( 'Hide on device?', 'js_composer' ) ?>
			</th>
		</tr>
		<?php foreach ( $sizes as $key => $size ) : ?>
			<tr class="vc_size-<?php echo $key ?>">
				<td class="vc_screen-size vc_screen-size-<?php echo $key ?>">
					<span title="<?php echo $size ?>">
						<i class="vc-composer-icon vc-c-icon-layout_<?php echo isset( $layouts[ $key ] ) ? $layouts[ $key ] : $key ?>"></i>
					</span>
				</td>
				<td>
					<?php echo $param->offsetControl( $key ) ?>
				</td>
				<td>
					<?php echo $param->sizeControl( $key ) ?>
				</td>
				<td>
					<label>
						<input type="checkbox" name="vc_hidden-<?php echo $key ?>"
								value="yes"<?php echo in_array( 'vc_hidden-' . $key, $data ) ? ' checked="true"' : '' ?>
								class="vc_column_offset_field">
					</label>
				</td>
			</tr>
		<?php endforeach ?>
	</table>
</div>
<script type="text/javascript">
	window.VcI8nColumnOffsetParam = <?php echo json_encode( array(
		'inherit' => __( 'Inherit: ', 'js_composer' ),
		'inherit_default' => __( 'Inherit from default', 'js_composer' ),
	) ) ?>;
</script>
