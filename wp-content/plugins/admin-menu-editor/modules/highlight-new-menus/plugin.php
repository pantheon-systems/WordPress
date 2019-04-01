<?php

/**
 * Plugin Name: Highlight New Admin Menus
 * Plugin URI: http://adminmenueditor.com
 * Version: 1.0
 * Author: Janis Elsts
 * Author URI: http://w-shadow.com/
 * Description: Highlights new admin menu items.
 */

require 'wsNewMenuHighlighter.php';
if ( is_admin() ) {
	new wsNewMenuHighlighter();
}
