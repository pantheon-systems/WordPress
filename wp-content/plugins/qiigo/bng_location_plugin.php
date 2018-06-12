<?php
/**
 * @package Bng_Plugin
 * @version 0.1
 */
/*
Plugin Name: Qiigo Location Tracker 
Plugin URI: http://bitsngeeks.com/
Description: Create Custom Post Types for bitsandgeeks template. Require CMB2 plugin
Author: Bits&Geeks
Version: 1.1
Author URI: http://bitsngeeks.com/
*/
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
}

require_once 'base/BngLocationTool.php';
require_once 'base/BngCustomTypes.php';


add_action( 'init', array( 'BngLocationTool', 'get_instance' ), 0 );
//create post default
register_activation_hook( __FILE__, array('BngCustomTypes','create_post_default' ) );
register_uninstall_hook ( __FILE__ ,array( 'BngCustomTypes' , 'uninstall_unset_session' ) );