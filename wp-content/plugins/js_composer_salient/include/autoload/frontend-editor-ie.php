<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'vc_frontend_editor_render_template', 'vc_add_ie9_degradation' );
