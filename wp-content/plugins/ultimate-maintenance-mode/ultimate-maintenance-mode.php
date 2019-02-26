<?php
/*
Plugin Name: Ultimate Maintenance Mode
Plugin URI: http://seedprod.com
Description: Displays a screenshot of website with an overlayed window with the reason your site is down.
Version: 1.7.1
Author: John Turner
Text Domain: ultimate-maintenance-mode
Domain Path: /languages
Author URI: http://seedprod.com
License: GPLv2
Copyright 2011  John Turner (email : john@seedprod.com, twitter : @johnturner)
*/

/**
 * Init
 *
 * @package WordPress
 * @subpackage Ultimate_Maintenence_Mode
 * @since 0.1
 */

/**
 * Require config to get our initial values
 */
define( 'UMM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'UMM_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );

load_plugin_textdomain('ultimate-maintenance-mode',false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

require_once('framework/framework.php');
require_once('inc/config.php');
