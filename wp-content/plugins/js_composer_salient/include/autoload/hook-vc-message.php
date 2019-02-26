<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
	VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Message' );

	add_filter( 'vc_edit_form_fields_attributes_vc_message', array(
		'WPBakeryShortCode_VC_Message',
		'convertAttributesToMessageBox2',
	) );
}
