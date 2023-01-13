<?php
remove_filter( 'ocean_main_metaboxes_post_types', array( 'Ocean_Extra_New_Theme_Panel', 'control_metaboxes' ), 9999 );
$metabox_posttypes          = apply_filters(
	'ocean_main_metaboxes_post_types',
	array(
		'post',
		'page',
		'product',
		'elementor_library',
		'ae_global_templates',
	)
);
if ( ! is_plugin_active( oceanwp_theme_panel()->find_plugin_path( 'anywhere-elementor' ) ) ) {
	$ae_global_templates_index = array_search( 'ae_global_templates', $metabox_posttypes );
	if( $ae_global_templates_index !== false ) {
		unset( $metabox_posttypes[ $ae_global_templates_index ] );
	}
}
$metabox_posttypes_settings = get_option( 'oe_metabox_posttypes_settings', 0 );
if ( empty( $metabox_posttypes_settings ) && $metabox_posttypes_settings !== 0 ) {
	$metabox_posttypes_settings = array();
}
$oe_metabox_descriptions = oe_get_metabox_descriptions();
?>

<div id="ocean-metaboxes-control" class="column-wrap clr">
	<form class="save_panel_settings">
		<input type="hidden" name="option_name" value="oe_metabox_posttypes_settings" />

		<div id="ocean-metabox-disable-bulk" class="oceanwp-tp-switcher column-wrap clr">
			<label for="oceanwp-switch-metabox-disable-bulk" class="column-name">
				<input type="checkbox" role="checkbox" name="metabox-disable-bulk" value="true" <?php checked( ( $metabox_posttypes_settings === 0 ) ); ?> id="oceanwp-switch-metabox-disable-bulk" class="oe-switcher-bulk" />
				<span class="slider round"></span>
			</label>
		</div>
		<div id="ocean-metabox-items" class="column-wrap clr">
			<?php foreach ( $metabox_posttypes as $post_type ) : ?>
				<?php
				$metabox_posttype_obj = get_post_type_object( $post_type );
				if ( ! empty( $metabox_posttype_obj ) ) {
					$label = $metabox_posttype_obj->labels->singular_name;
				} else {
					$label = ucwords( str_replace( array( '_', '-' ), ' ', $post_type ) );
				}
				if ( $metabox_posttypes_settings === 0 ) {
					$checked_val = true;
				} else {
					$checked_val = ! empty( $metabox_posttypes_settings[ $post_type ] );
				}
				$description = ! empty( $oe_metabox_descriptions[ $post_type ] ) ? $oe_metabox_descriptions[ $post_type ] : '';
				?>
				<div id="ocean-metabox-<?php echo $post_type; ?>" class="oceanwp-tp-small-block column-wrap clr">
					<h3 class="title"><?php echo esc_attr( $label ); ?></h3>
					<label for="oceanwp-metabox-switch-[<?php echo esc_attr( $post_type ); ?>]" class="oceanwp-tp-switcher column-name">
						<input type="checkbox" role="checkbox" name="oe_metabox_posttypes_settings[<?php echo esc_attr( $post_type ); ?>]" value="true" id="oceanwp-metabox-switch-[<?php echo esc_attr( $post_type ); ?>]" <?php checked( $checked_val ); ?>>
						<span class="slider round"></span>
					</label>
					<?php if( $description ) : ?>
						<span class="desc"><?php echo $description; ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php submit_button(); ?>
	</form>
</div>
