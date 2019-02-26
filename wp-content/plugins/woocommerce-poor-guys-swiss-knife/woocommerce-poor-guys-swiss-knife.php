<?php 
/*
 * Plugin Name: Woocommerce Poor Guys Swiss Knife 
 * Plugin URI: http://takebarcelona.com/woocommerce-poor-guys-swiss-knife/
 * Description: A Swiss Knife for WooCommerce.
 * Donation Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJ4K2X953H8CC
 * Contributor: ulih
 * Author: Uli Hake
 * Author URI: http://takebarcelona.com/authorship/uli-hake
 * Version: 2.2.4
 * @package WordPress
 * @subpackage WooCommerce Poor Guys Swiss Knife
 * @author Uli Hake
 * @since 1.1.0
*/
/*  Copyright 2013 Uli Hake (uli|dot|hake|at|gmail|dot|com) (if not stated otherwise)

	This program including all files in this directory and its subdirectories is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin name
global $wcpgsk_name;
$wcpgsk_name = 'WooCommerce Poor Guys Swiss Knife';
// Define plugin text domain
if ( !defined('WCPGSK_DOMAIN') ) :
	define('WCPGSK_DOMAIN', 'wcpgsk');
endif;
if ( !defined('WCPGSK_NAME') ) :
	define('WCPGSK_NAME', 'WooCommerce Poor Guys Swiss Knife');
endif;
if ( !defined('WCPGSK_SLUG') ) :
	define( 'WCPGSK_SLUG', 'woocommerce-poor-guys-swiss-knife' );
endif;

global $wcpgsk_woocommerce_active;

$wcpgsk_woocommerce_active = false;

//Function to check availability of WooCommerce
if ( !function_exists('wcpgsk_is_woocommerce_active') ) {
function wcpgsk_is_woocommerce_active() {
	$active_plugins = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() )
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}
}
$wcpgsk_woocommerce_active = wcpgsk_is_woocommerce_active();
add_action( 'admin_notices', 'wcpgsk_plugin_activation_message', 0 ) ;


//Check if WooCommerce is available and place a admin message if not
if ( ! function_exists('wcpgsk_plugin_activation_message') ) {
function wcpgsk_plugin_activation_message() {
	//step out with message
	global $wcpgsk_woocommerce_active;
	if ( !$wcpgsk_woocommerce_active ) :
		deactivate_plugins( plugin_basename( __FILE__ ) );			
		$html = '<div class="error">';
			$html .= '<p>';
				$html .= __( 'You have to install and activate WooCommerce before you can use <strong>' . WCPGSK_NAME . '</strong>', WCPGSK_DOMAIN );
			$html .= '</p>';
		$html .= '</div><!-- /.updated -->';
		echo $html;
		
	endif;
}
}

//register_activation_hook( __FILE__, 'wcpgsk_activation' );

if ( !function_exists( 'wcpgsk_activation' ) ) {
	function wcpgsk_activation() {
		global $wcpgsk_woocommerce_active;
		if ( !$wcpgsk_woocommerce_active ) :
			deactivate_plugins( plugin_basename( __FILE__ ) );	
			return new WP_Error( 'dependency', __( 'You have to install and activate WooCommerce before you can use <strong>' . WCPGSK_NAME . '</strong>', WCPGSK_DOMAIN ) );			
			//wp_die(  );
		endif;
	}
}
//Check if WooCommerce is available and place a admin message if not
if ( ! function_exists('wcpgsk_woocommerce_version_message') ) {
function wcpgsk_woocommerce_version_message() {
	//step out with message
	$html = '<div class="error">';
		$html .= '<p>';
			$html .= __( 'You need at least WooCommerce 2.0+ before you can use <strong>' . WCPGSK_NAME . '</strong>', WCPGSK_DOMAIN );
		$html .= '</p>';
	$html .= '</div><!-- /.updated -->';
	echo $html;	
}
}

add_action( 'woocommerce_init', 'wcpgsk_init' );
if ( !function_exists('wcpgsk_init') ) {
function wcpgsk_init() {
	global $wcpgsk, $wcpgsk_about, $wcpgsk_options, $wcpgsk_session, $wcpgsk_woocommerce_active;	
	//only continue loading
	if ( $wcpgsk_woocommerce_active && version_compare( WOOCOMMERCE_VERSION, "2.0" ) >= 0 ) {
		$wcpgsk_options = get_option('wcpgsk_settings', true);
		require_once( 'classes/woocommerce-poor-guys-swiss-knife.php' );
		require_once( 'classes/woocommerce-poor-guys-swiss-knife-about.php' );	
		require_once( 'wcpgsk-af.php' );
		
		if ( !is_admin() ) :
			add_action( 'plugins_loaded', 'wcpgsk_load_wcsession_helper' );
		endif;
		//load into our global
		$wcpgsk = new WCPGSK_Main( __FILE__ );
		$wcpgsk->version = '2.2.4';	
		$wcpgsk->wcpgsk_hook_woocommerce_filters();
		
		
	} elseif ( version_compare( WOOCOMMERCE_VERSION, "2.0" ) < 0 ) {
		add_action( 'admin_notices', 'wcpgsk_woocommerce_version_message', 0 ) ;	
		return;
	} else {
		return;
	}
}
}

add_action( 'after_setup_theme', 'wcpgsk_template_functions', 1 );

function wcpgsk_template_functions() {
	include_once( 'wcpgsk-fa.php' );
}

register_uninstall_hook( __FILE__, 'wcpgsk_uninstaller' );
function wcpgsk_uninstaller()
{
	delete_option( 'wcpgsk_settings' );
	delete_option( 'wcpgsk_locale' );
	delete_option( 'wcpgsk_checkoutjs' );
	delete_option( WCPGSK_DOMAIN . '-version' );
}
?>