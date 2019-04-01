<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<!-- Modal -->
<div class="vc_general modal fade" id="vc_edit-element-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTitle"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabelTitle"></h4>
			</div>
			<div class="modal-body vc_properties-list"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default"
					data-dismiss="modal"><?php _e( 'Close', 'js_composer' ); ?></button>
				<button type="button"
					class="btn btn-primary vc_save"><?php _e( 'Save Changes', 'js_composer' ); ?></button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div><!-- /.modal -->
