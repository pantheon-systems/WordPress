<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$tab = esc_attr( preg_replace( '/^vc\-/', '', $page->getSlug() ) );
$editable_roles = get_editable_roles();
require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-roles.php' );
$vc_role = new Vc_Roles();
?>
<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post"
	id="vc_settings-<?php echo $tab ?>"
	class="vc_settings-tab-content vc_settings-tab-content-active"<?php echo apply_filters( 'vc_setting-tab-form-' . $tab, '' ) ?>
	data-vc-roles="form">
	<div class="tab_intro">
		<p><?php _e( 'Control user group role access to the features and options of WPBakery Page Builder - manage WordPress default and custom roles.', 'js_composer' ) ?></p>
	</div>
	<!-- Settings template start -->
	<div class="vc_wp-settings">
		<div class="vc_wp-accordion" data-vc-action="collapseAll">
			<?php foreach ( $editable_roles as $role => $details ) :
				$name = translate_user_role( $details['name'] );
				$unique_id = 'vc_role-' . $role;
				$valid_roles = array();
				foreach ( $vc_role->getParts() as $part ) {
					if ( $vc_role->hasRoleCapability( $role, $vc_role->getPartCapability( $part ) ) ) {
						$valid_roles[] = $part;
					}
				}
				if ( count( $valid_roles ) > 0 ) :
					?>
					<div
						class="vc_wp-accordion-panel vc_ui-settings-roles-role<?php echo ! isset( $next ) ? ' vc_active' : '' ?>"
						data-vc-unique-id="<?php echo esc_attr( $unique_id ) ?>"
						data-vc-content=".vc_wp-accordion-panel-body"
						data-vc-role="<?php echo esc_attr( $role ) ?>">
						<div class="widget" data-vc-accordion=""
							data-vc-container=".vc_wp-accordion"
							data-vc-target="[data-vc-unique-id=<?php echo esc_attr( $unique_id ) ?>]">
							<div class="widget-top">
								<div class="widget-title-action">
									<button type="button" class="widget-action hide-if-no-js" aria-expanded="true">
										<span class="screen-reader-text">Edit widget: Search</span>
										<span class="toggle-indicator" aria-hidden="true"></span>
									</button>
								</div>
								<div class="widget-title">
									<h4>
										<?php echo esc_html( $name ) ?>
										<span class="in-widget-title"></span>
									</h4>
								</div>
							</div>

						</div>

						<div class="vc_wp-accordion-panel-body">
							<table class="form-table">
								<tbody>
								<?php
								$next = true;
								foreach ( $valid_roles as $part ) {
									vc_include_template( 'pages/partials/vc-roles-parts/_' . $part . '.tpl.php', array(
										'part' => $part,
										'role' => $role,
										'vc_role' => $vc_role,
									) );
								}
								?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<span class="vc_settings-spinner vc_ui-wp-spinner" style="display: none;" id="vc_wp-spinner"></span>
	<!-- Settings template end -->
	<?php
	wp_nonce_field( 'vc_settings-' . $tab . '-action', 'vc_nonce_field' );
	$submit_button_attributes = array();
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes', $submit_button_attributes, $tab );
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes-' . $tab, $submit_button_attributes, $tab );
	submit_button( __( 'Save Changes', 'js_composer' ), 'primary', 'submit_btn', true, $submit_button_attributes );
	?>
	<input type="hidden" name="action" value="vc_roles_settings_save"
		id="vc_settings-<?php echo $tab; ?>-action"/>
</form>
