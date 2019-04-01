<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Edit row layout
 *
 * @since   4.3
 */
class Vc_Edit_Layout {
	public function renderUITemplate() {
		global $vc_row_layouts;
		vc_include_template( 'editors/popups/vc_ui-panel-row-layout.tpl.php', array(
			'vc_row_layouts' => $vc_row_layouts,
		) );
	}
}
