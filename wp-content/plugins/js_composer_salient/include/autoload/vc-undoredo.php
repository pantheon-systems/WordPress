<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function vc_navbar_undoredo() {
	if ( vc_is_frontend_editor() || is_admin() ) {
		require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar-undoredo.php' );
		new Vc_Navbar_Undoredo();
	}
}

add_action( 'admin_init', 'vc_navbar_undoredo' );
