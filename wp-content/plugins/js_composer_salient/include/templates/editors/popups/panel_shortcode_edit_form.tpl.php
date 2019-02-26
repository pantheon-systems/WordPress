<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div id="vc_properties-panel"
	class="<?php echo esc_attr( apply_filters( 'vc_edit_form_css_class', 'vc_panel vc_shortcode-edit-form vc_media-xs' ) ) ?>">
	<div class="vc_panel-heading">
		<a title="<?php _e( 'Close panel', 'js_composer' ); ?>" href="#" class="vc_close" data-dismiss="panel"
			aria-hidden="true"><i class="vc_icon"></i></a>
		<a title="<?php _e( 'Hide panel', 'js_composer' ); ?>" href="#" class="vc_transparent" data-transparent="panel"
			aria-hidden="true"><i class="vc_icon"></i></a>

		<h3 class="vc_panel-title"></h3>
	</div>
	<div class="vc_panel-body vc_properties-list wpb-edit-form">
	</div>
	<div class="vc_panel-footer">
		<button type="button" class="vc_btn vc_panel-btn-close vc_close vc_btn-default"
			data-dismiss="panel"><?php _e( 'Close', 'js_composer' ) ?></button>
		<button type="button" class="vc_btn vc_panel-btn-save vc_btn-primary"
			data-save="true"><?php _e( 'Save Changes', 'js_composer' ) ?></button>
	</div>
</div>
