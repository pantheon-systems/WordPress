<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
function vc_settings_tabs_vc_roles( $tabs ) {
	//inster after vc-general tab
	if ( array_key_exists( 'vc-general', $tabs ) ) {
		$new = array();
		foreach ( $tabs as $key => $value ) {
			$new[ $key ] = $value;
			if ( 'vc-general' === $key ) {
				$new['vc-roles'] = __( 'Role Manager', 'js_composer' );
			}
		}
		$tabs = $new;
	} else {
		$tabs['vc-roles'] = __( 'Roles Manager', 'js_composer' );
	}

	return $tabs;
}

if ( ! is_network_admin() ) {
	add_filter( 'vc_settings_tabs', 'vc_settings_tabs_vc_roles' );
}

function vc_settings_render_tab_vc_roles() {
	return 'pages/vc-settings/tab-vc-roles.php';
}

add_filter( 'vc_settings-render-tab-vc-roles', 'vc_settings_render_tab_vc_roles' );

function vc_roles_settings_save() {
	if ( check_admin_referer( 'vc_settings-roles-action', 'vc_nonce_field' ) && current_user_can( 'manage_options' ) ) {
		require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-roles.php' );
		$vc_roles = new Vc_Roles();
		$data = $vc_roles->save( vc_request_param( 'vc_roles', array() ) );
		echo json_encode( $data );
		die();
	}
}

add_action( 'wp_ajax_vc_roles_settings_save', 'vc_roles_settings_save' );
if ( 'vc-roles' == vc_get_param( 'page' ) ) {
	function vc_settings_render_tab_vc_roles_scripts() {
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
	}

	add_action( 'admin_init', 'vc_settings_render_tab_vc_roles_scripts' );

}

