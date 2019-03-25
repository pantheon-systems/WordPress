<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Default Tiny MCE paste-to-text
 * Description:       This plugin pre-activates the paste-as-text button on TinyMCE
 * Version:           1.0.0
 * Author:            Zvi Epner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter('tiny_mce_before_init', 'ag_tinymce_paste_as_text');
function ag_tinymce_paste_as_text( $init ) {
	$init['paste_as_text'] = true;
	return $init;
}