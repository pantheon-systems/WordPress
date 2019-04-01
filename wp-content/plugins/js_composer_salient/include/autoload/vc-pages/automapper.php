<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Build and enqueue js/css for automapper settings tab.
 * @since 4.5
 */
function vc_automapper_init() {
	if ( vc_user_access()
		->wpAny( 'manage_options' )
		->part( 'settings' )
		->can( 'vc-automapper-tab' )
		->get()
	) {
		vc_automapper()->addAjaxActions();
	}

}

/**
 * Returns automapper template.
 *
 * @since 4.5
 * @return string
 */
function vc_page_automapper_build() {
	return 'pages/vc-settings/vc-automapper.php';
}

// TODO: move to separate file in autoload
add_filter( 'vc_settings-render-tab-vc-automapper', 'vc_page_automapper_build' );
is_admin() && ( 'vc_automapper' === vc_request_param( 'action' ) || 'vc-automapper' === vc_get_param( 'page' ) ) && add_action( 'admin_init', 'vc_automapper_init' );
