<?php
/*
 Plugin Name: Duplicate Post
 Plugin URI: https://duplicate-post.lopo.it/
 Description: Clone posts and pages.
 Version: 3.2.2
 Author: Enrico Battocchi
 Author URI: https://lopo.it
 Text Domain: duplicate-post
 */

/*  Copyright 2009-2012	Enrico Battocchi  (email : enrico.battocchi@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version of the plugin
define('DUPLICATE_POST_CURRENT_VERSION', '3.2.2' );


/**
 * Initialise the internationalisation domain
 */
function duplicate_post_load_plugin_textdomain() {
    load_plugin_textdomain( 'duplicate-post', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'duplicate_post_load_plugin_textdomain' );


add_filter("plugin_action_links_".plugin_basename(__FILE__), "duplicate_post_plugin_actions", 10, 4);

function duplicate_post_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
	array_unshift($actions, "<a href=\"".menu_page_url('duplicatepost', false)."\">".esc_html__("Settings")."</a>");
	return $actions;
}

require_once (dirname(__FILE__).'/duplicate-post-common.php');

if (is_admin()){
	require_once (dirname(__FILE__).'/duplicate-post-admin.php');
}