<?php 
/*
Plugin Name: Quick Page/Post Redirect Plugin
Plugin URI: http://www.anadnet.com/quick-pagepost-redirect-plugin/
Description: Redirect Pages, Posts or Custom Post Types to another location quickly (for internal or external URLs). Includes individual post/page options, redirects for Custom Post types, non-existant 301 Quick Redirects (helpful for sites converted to WordPress), New Window functionality, and rel=nofollow functionality.
Author: anadnet
Author URI: http://www.anadnet.com/
Donate link: 
Version: 5.1.8
Text Domain: quick-pagepost-redirect-plugin
Domain Path: /lang
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

 * Copyright (C) 2009-2015 anadnet.com <info [at] anadnet [dot] com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the [GNU General Public License](http://wordpress.org/about/gpl/)
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * on an "AS IS", but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see [GNU General Public Licenses](http://www.gnu.org/licenses/),
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301, USA.
===================
* See the file 'filters-and-hooks.txt' in the plugin folder for filter and action usage.
===================
*/

global $newqppr, $redirect_plugin, $qppr_setting_links;
$qppr_setting_links = false;
start_ppr_class();
	
//=======================================
// Main Plugin Redirect Class.
//=======================================
class quick_page_post_reds {
	public $ppr_nofollow;
	public $ppr_newindow;
	public $ppr_url;
	public $ppr_url_rewrite;
	public $ppr_type;
	public $ppr_curr_version;
	public $ppr_metaurlnew;
	public $thepprversion;
	public $thepprmeta;
	public $quickppr_redirects;
	public $tohash;
	public $fcmlink;
	public $adminlink;
	public $ppr_all_redir_array;
	public $homelink;
	public $updatemsg;
	public $pproverride_nofollow;
	public $pproverride_newwin;
	public $pproverride_type;
	public $pproverride_active;
	public $pproverride_URL;
	public $pproverride_rewrite;
	public $pprmeta_seconds;
	public $pprmeta_message;
	public $quickppr_redirectsmeta;
	public $pproverride_casesensitive;
	public $ppruse_jquery;
	public $pprptypes_ok;
	
	function __construct() {
		$this->ppr_curr_version 		= '5.1.8';
		$this->ppr_nofollow 			= array();
		$this->ppr_newindow 			= array();
		$this->ppr_url 					= array();
		$this->ppr_url_rewrite 			= array();
		$this->thepprversion 			= get_option( 'ppr_version');
		$this->thepprmeta 				= get_option( 'ppr_meta_clean');
		$this->quickppr_redirects 		= get_option( 'quickppr_redirects', array() );
		$this->quickppr_redirectsmeta	= get_option( 'quickppr_redirects_meta', array() );
		$this->homelink 				= get_option( 'home');
		$this->pproverride_nofollow 	= get_option( 'ppr_override-nofollow' );
		$this->pproverride_newwin 		= get_option( 'ppr_override-newwindow' );
		$this->ppruse_jquery	 		= get_option( 'ppr_use-jquery' );
		$this->pprptypes_ok				= get_option( 'ppr_qpprptypeok', array() );
		$this->pproverride_type 		= get_option( 'ppr_override-redirect-type' );
		$this->pproverride_active 		= get_option( 'ppr_override-active', '0' );
		$this->pproverride_URL 			= get_option( 'ppr_override-URL', '' );
		$this->pproverride_rewrite		= get_option( 'ppr_override-rewrite', '0' );
		$this->pprmeta_message			= get_option( 'qppr_meta_addon_content', get_option( 'ppr_meta-message', '' ) );
		$this->pprmeta_seconds			= get_option( 'qppr_meta_addon_sec', get_option( 'ppr_meta-seconds', 0 ) );
		$this->pproverride_casesensitive= get_option( 'ppr_override-casesensitive' );
		$this->adminlink 				= admin_url('/', 'admin');
		$this->fcmlink					= 'http://www.anadnet.com/quick-pagepost-redirect-plugin/';
		$this->ppr_metaurl				= '';
		$this->updatemsg				= '';
		$this->pprshowcols				= get_option( 'ppr_show-columns', '1' );
		//if($this->pprmeta_seconds==''){$this->pprmeta_seconds='0';}
		
		//these are for all the time - even if there are overrides
		add_action( 'admin_init', array( $this, 'save_quick_redirects_fields' ) );
		add_action( 'admin_init', array( $this, 'ppr_init_check_version' ), 1 );								// checks version of plugin in DB and updates if needed.
		add_action( 'admin_init', array( $this, 'qppr_meta_plugin_has_addon' ) );
	  	add_action( 'init', array( $this, 'ppr_parse_request_new' ) );											// parse query vars
		add_action(	'save_post', array( $this,'ppr_save_metadata' ), 11, 2 ); 									// save the custom fields
		add_action( 'admin_menu', array( $this,'ppr_add_menu_and_metaboxes' ) ); 								// add the menu items & Metaboxes needed
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this,'ppr_filter_plugin_actions' ) ); // adds links to plugin list page
		add_filter( 'plugin_row_meta', array( $this, 'ppr_filter_plugin_links' ), 10, 2 );						// adds links to plugin list page
		add_action( 'plugins_loaded', array( $this, 'qppr_load_textdomain' ) );									// loads the plugin textdomain - 5.1.2
		add_filter( 'query_vars', array( $this, 'ppr_queryhook' ) ); 											// parse out some form submissions (mainly for import and export)
		add_action( 'admin_enqueue_scripts' , array( $this, 'qppr_admin_scripts' ) );							// admin scripts & styles
		add_action( 'admin_enqueue_scripts', array( $this, 'qppr_pointer_load' ), 1000 ); 						// for new features pointers - 5.0.7
		add_action( 'wp_enqueue_scripts' , array( $this, 'qppr_frontend_scripts' ) );							// front end scripts - 5.0.7
		add_action( 'wp_ajax_qppr_delete_all_settings', array( $this, 'qppr_delete_all_settings_ajax' )  );		// register ajax delete ALL Settings - 5.1.0
		add_action( 'wp_ajax_qppr_delete_all_iredirects', array( $this, 'qppr_delete_all_ireds_ajax' )  );		// register ajax delete ALL Individual Redirects - 5.1.0
		add_action( 'wp_ajax_qppr_delete_all_qredirects', array( $this, 'qppr_delete_all_qreds_ajax' )  );		// register ajax delete ALL Quick Redirects - 5.1.0
		add_action( 'wp_ajax_qppr_delete_quick_redirect', array( $this, 'qppr_delete_quick_redirect_ajax' )  );	// register ajax delete quick redirect - 5.0.7
		add_action( 'wp_ajax_qppr_save_quick_redirect', array( $this, 'qppr_save_quick_redirect_ajax' )  );		// register ajax save quick redirect - 5.0.7
		add_action( 'wp_ajax_qppr_pprhidemessage_ajax', array( $this, 'qppr_pprhidemessage_ajax' )  );			// register ajax messages quick redirect - 5.0.7
		add_filter( 'qppr_admin_pointers-toplevel_page_redirect-updates', array( $this, 'qppr_register_pointer_existing' ) );  // add pointers filter
		add_filter( 'qppr_admin_pointers-quick-redirects_page_redirect-options', array( $this, 'qppr_register_pointer_use_jquery' ) ); 	// add pointers filter
		add_filter( 'qppr_admin_pointers-quick-redirects_page_meta_addon', array( $this, 'qppr_register_pointer_meta' ) ); 	// add pointers filter
		//add_filter( 'wp_feed_cache_transient_lifetime',array($this,'ppr_wp_feed_options',10, 2)); 			// for testing FAQ page only
		
		if( $this->pproverride_active != '1' && !is_admin() ){ 									// don't run these if override active is set
			add_action( 'init', array( $this, 'redirect' ), 1 ); 								// add the 301 redirect action, high priority
			add_action( 'init', array( $this, 'redirect_post_type' ), 1 ); 						// add the normal redirect action, high priority
			add_action( 'ppr_meta_head_hook', array( $this, 'override_ppr_metahead' ), 1, 3 );  // takes care of Meta Redirects as of 5.1.1
			add_action( 'template_redirect', array( $this, 'ppr_do_redirect' ), 1);				// do the redirects
			add_filter( 'wp_get_nav_menu_items', array( $this, 'ppr_new_nav_menu_fix' ), 1, 1 );// hook into nav menus
			add_filter( 'wp_list_pages', array( $this, 'ppr_fix_targetsandrels' ) );			// hook into wp_list_pages function
			add_filter( 'page_link', array( $this, 'ppr_filter_page_links' ), 20, 2 );			// hook into page_link function
			add_filter( 'post_link', array( $this, 'ppr_filter_page_links' ), 20, 2 );			// hook into post_link function
			add_filter( 'post_type_link', array( $this, 'ppr_filter_page_links' ), 20, 2 );		// hook into custom post type link function
			add_filter( 'get_permalink', array( $this, 'ppr_filter_links' ), 20, 2 );			// hook into get_permalink function
			add_filter( 'redirect_canonical', array( $this, 'wordpress_no_guess_canonical' ) );	// stops 404 on canonical redirect as of 5.1.5
		}
		
		if( $this->pprshowcols == '1')
			add_filter( 'pre_get_posts', array( $this,'add_custom_columns' ) ); 				// add custom columns
	}
	/*
	 * Try to stop canonical redirect on 404 before quick redirect can happen.
	 * if a redirect is found, returns URL otherwise returns original url.
	 *
	 * @since 5.1.5
	*/
	function wordpress_no_guess_canonical( $redirect_url ){
		if ( is_404() ) {
			$redirects 		= get_option( 'quickppr_redirects', array() );
			$request_URI  	= isset( $_SERVER['REQUEST_URI'] ) ? rtrim( $_SERVER['REQUEST_URI'], '/' ) . '/' : '';
			if( isset( $redirects[$request_URI] ) && !empty( $redirects[$request_URI] ) )
				return  $redirects[$request_URI];
		}
		return $redirect_url;
	}
	
	/**
	 * Load plugin textdomain.
	 *
	 * @since 5.1.2
	 */
	function qppr_load_textdomain() {
		load_plugin_textdomain( 'quick-pagepost-redirect-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
	}
	
	/**
	 * Try to clear Cache files when certain plugins are present.
	 * Only happens after redirects or settings are saved or deleted.
	 *
	 * Expirimental to try to stop some caching plugins from holding the cached redirects.
	 * @since 5.1.2
	 */
	function qppr_try_to_clear_cache_plugins(){
		// make sure the function is present
		if ( ! function_exists('is_plugin_active'))
    		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		// WP Super Cache
		if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) && function_exists( 'wp_cache_clear_cache' ) )
			wp_cache_clear_cache();
		// W3 Total Cache
		if( is_plugin_active( 'w3-total-cache/w3-total-cache.php') && function_exists( 'w3tc_pgcache_flush' ) )
			w3tc_pgcache_flush();
		// WP Fast Cache
		if( is_plugin_active( 'wp-fastest-cache/wpFastestCache.php') && class_exists( 'WpFastestCache' ) ){
			$newCache = new WpFastestCache();
			$newCache->deleteCache();
		}
	}
	
	function qppr_delete_all_settings_ajax(){
		check_ajax_referer( 'qppr_ajax_delete_ALL_verify', 'security', true );
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			//delete Individual
			$sql = "DELETE FROM {$wpdb->postmeta} WHERE `meta_key` IN ( '_pprredirect_meta_secs','qppr_meta_trigger','qppr_meta_load','qppr_meta_content','qppr_meta_append','_pprredirect_active','_pprredirect_rewritelink','_pprredirect_newwindow','_pprredirect_relnofollow','_pprredirect_type','_pprredirect_url');";
			$wpdb->query($sql);
			//delete Quick
				delete_option( 'quickppr_redirects' );
				delete_option( 'quickppr_redirects_meta' );
			//Delete Options
				delete_option( 'ppr_version');
				delete_option( 'ppr_meta_clean');
				delete_option( 'ppr_override-nofollow' );
				delete_option( 'ppr_override-newwindow' );
				delete_option( 'ppr_use-jquery' );
				delete_option( 'ppr_qpprptypeok' );
				delete_option( 'ppr_override-redirect-type' );
				delete_option( 'ppr_override-active' );
				delete_option( 'ppr_override-URL' );
				delete_option( 'ppr_override-rewrite' );
				delete_option( 'qppr_meta_addon_content' );
				delete_option( 'ppr_meta-message' );
				delete_option( 'qppr_meta_addon_sec' );
				delete_option( 'ppr_meta-seconds' );
				delete_option( 'ppr_override-casesensitive' );
				delete_option( 'ppr_show-columns' );
				delete_option( 'ppr_use-custom-post-types' );
				delete_option( 'qppr_jQuery_hide_message2' );	
				delete_option( 'qppr_meta_addon_load' );
				delete_option( 'qppr_meta_addon_trigger' );
				delete_option( 'qppr_meta_append_to' );
				$this->qppr_try_to_clear_cache_plugins();
			echo 'success';
		}else{
			echo 'no permission';	
		}
		exit;
	}
	function qppr_delete_all_ireds_ajax(){
		check_ajax_referer( 'qppr_ajax_delete_ALL_verify', 'security', true );
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$sql = "DELETE FROM {$wpdb->postmeta} WHERE `meta_key` IN ( '_pprredirect_meta_secs','qppr_meta_trigger','qppr_meta_load','qppr_meta_content','qppr_meta_append','_pprredirect_active','_pprredirect_rewritelink','_pprredirect_newwindow','_pprredirect_relnofollow','_pprredirect_type','_pprredirect_url');";
			$wpdb->query($sql);
			$this->qppr_try_to_clear_cache_plugins();
			echo 'success';
		}else{
			echo 'no permission';	
		}
		exit;
	}
	
	function qppr_delete_all_qreds_ajax(){
		check_ajax_referer( 'qppr_ajax_delete_ALL_verify', 'security', true );
		if( current_user_can( 'manage_options' ) ){
			delete_option( 'quickppr_redirects' );
			delete_option( 'quickppr_redirects_meta' );
			$this->qppr_try_to_clear_cache_plugins();
			echo 'success';
		}else{
			echo 'no permission';	
		}
		exit;
	}
	
	function qppr_pointer_load( $hook_suffix ) {
		if ( get_bloginfo( 'version' ) < '3.3' )
			return;
		$screen 	= get_current_screen();
		$screen_id 	= $screen->id;
		$pointers 	= apply_filters( 'qppr_admin_pointers-' . $screen_id, array() );
		if ( ! $pointers || ! is_array( $pointers ) )
			return;
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$valid_pointers =array();
		foreach ( $pointers as $pointer_id => $pointer ) {
			if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
				continue;
			$pointer['pointer_id'] = $pointer_id;
			$valid_pointers['pointers'][] =  $pointer;
		}
		if ( empty( $valid_pointers ) )
			return;
		wp_enqueue_style( 'wp-pointer' );
		//wp_enqueue_script( 'qppr-pointer', plugins_url( 'js/qppr_pointers.js', __FILE__ ), array( 'wp-pointer' ) );
		wp_enqueue_script( 'qppr-pointer', plugins_url( 'js/qppr_pointers.min.js', __FILE__ ), array( 'wp-pointer' ) );
		wp_localize_script( 'qppr-pointer', 'qpprPointer', $valid_pointers );
	}
	
	function qppr_register_pointer_meta( $p ) {
		$p['qppr-meta-options'] = array(
			'target' => '.wrap > h2:first-child',
			'options' => array(
				'content' => sprintf( '<h3>%s</h3><p>%s</p>',
					__( 'New Meta Redirect options.' ,'quick-pagepost-redirect-plugin'),
					__( 'Please view the Help Tab above to see more information about the Meta Redirect Settings.', 'quick-pagepost-redirect-plugin')
				),
				'position' => array( 'edge' => 'top', 'align' => 'right' )
			)
		);
		return $p;
	}

	function qppr_register_pointer_existing( $p ) {
		$p['existing-redirects'] = array(
			'target' => '#qppr-existing-redirects',
			'options' => array(
				'content' => sprintf( '<h3>%s</h3><p>%s</p><p>%s</p>',
					__( 'New Layout of Existing Redirects' ,'quick-pagepost-redirect-plugin'),
					__( 'The existing <strong>Quick Redirects</strong> are now laid out in a list format instead of form fields. When you have a lot of Redirects, this helps eliminate the "max_input_vars" configuration issue where redirects were not saving correctly.','quick-pagepost-redirect-plugin'),
					__( 'To edit an existing redirect, click the pencil icon','quick-pagepost-redirect-plugin'). ' (<span class="dashicons dashicons-edit"></span>) ' .__( 'and the row will become editable. Click the trash can icon','quick-pagepost-redirect-plugin').' (<span class="dashicons dashicons-trash"></span>) '.__( 'and the redirect will be deleted. Click the trash can icon','quick-pagepost-redirect-plugin')
				),
				'position' => array( 'edge' => 'bottom', 'align' => 'left' )
			)
		);
		return $p;
	}
	
	function qppr_register_pointer_use_jquery( $p ) {
		$p['qppr-use-jquery'] = array(
			'target' => '#ppr_use-jquery',
			'options' => array(
				'content' => sprintf( '<h3>%s</h3><p>%s</p><p>%s</p><p>%s</p>',
					__( 'New Option to Use jQuery' ,'quick-pagepost-redirect-plugin'),
					__( 'To increase the effectiveness of the plugin\'s ability to add new window and nofollow functionality, you can use the jQuery option.','quick-pagepost-redirect-plugin'),
					__( 'This adds JavaScript/jQuery scripting to check the links in the output HTML of the page and add the correct functionality if needed.','quick-pagepost-redirect-plugin'),
					__( 'If you experience JavaScript/jQuery conflicts, try turning this option off.','quick-pagepost-redirect-plugin')
				),
				'position' => array( 'edge' => 'left', 'align' => 'middle' )
			)
		);
		return $p;
	}
	
	function qppr_delete_quick_redirect_ajax(){
		check_ajax_referer( 'qppr_ajax_verify', 'security', true );
		$request 		= isset($_POST['request']) && esc_url($_POST['request']) != '' ? esc_url($_POST['request']) : '';
		$curRedirects 	= get_option( 'quickppr_redirects', array() );
		$curMeta 		= get_option( 'quickppr_redirects_meta', array() );
		if( isset( $curRedirects[ $request ] ) && isset( $curMeta[ $request ] ) ){
			unset( $curRedirects[ $request ] , $curMeta[ $request ] );
			update_option('quickppr_redirects', $curRedirects);
			update_option('quickppr_redirects_meta', $curMeta);
			$this->qppr_try_to_clear_cache_plugins();
			echo 'redirect deleted';
		}else{
			echo 'error';	
		}
		exit;
	}
	
	function qppr_save_quick_redirect_ajax(){
		check_ajax_referer( 'qppr_ajax_verify', 'security', true );
		$protocols 		= apply_filters('qppr_allowed_protocols',array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
		$request 		= isset($_POST['request']) && trim($_POST['request']) != '' ? esc_url(str_replace(' ','%20',trim($_POST['request'])), null, 'appip') : '';
		$requestOrig	= isset($_POST['original']) && trim($_POST['original']) != '' ? esc_url(str_replace(' ','%20',trim($_POST['original'])), null, 'appip') : '';
		$destination 	= isset($_POST['destination']) && trim($_POST['destination']) != '' ? esc_url(str_replace(' ','%20',trim($_POST['destination'])), null, 'appip') : '';
		$newWin 		= isset($_POST['newwin']) && (int) trim($_POST['newwin']) == 1 ? 1 : 0;
		$noFollow 		= isset($_POST['nofollow']) && (int) trim($_POST['nofollow']) == 1 ? 1 : 0;
		$updateRow 		= isset($_POST['row']) && $_POST['row'] != '' ? (int) str_replace('rowpprdel-','',$_POST['row']) : -1;
		$curRedirects 	= get_option('quickppr_redirects', array());
		$curMeta 		= get_option('quickppr_redirects_meta', array());
		$rkeys			= array_keys($curRedirects);
		$mkeys			= array_keys($curMeta);
		if( $updateRow == -1 || $requestOrig == '' || $request == '' || $destination == '' || empty( $curRedirects ) || empty( $curMeta) ){
			echo 'error';
			exit;	
		}
		$toDelete 			= array();
		$newRedirects 		= array();
		$newMeta 			= array();
		$orkey 				= array_search($requestOrig, $rkeys);
		$omkey 				= array_search($requestOrig, $mkeys);
		
		if( is_array( $rkeys ) && ! empty( $rkeys ) ){
			foreach( $rkeys as $key => $val ){
				$newRedirects[] = array( 'request' => $val, 'destination' => $curRedirects[$val] );
			}
		}
		if( is_array( $mkeys ) && ! empty( $mkeys ) ){
			foreach( $mkeys as $key => $val ){
				$newMeta[] = array( 'key' => $val, 'newwindow' => ( isset( $curMeta[$val]['newwindow'] ) && $curMeta[$val]['newwindow'] != '' ? $curMeta[$val]['newwindow'] : 0 ), 'nofollow' => ( isset( $curMeta[$val]['nofollow'] ) && $curMeta[$val]['nofollow'] != '' ? $curMeta[$val]['nofollow'] : 0 ) );
			}
		}
		$originalRowKey 	= isset($rkeys[$orkey]) ? $rkeys[$orkey] : '';
		$originalRowMetaKey = isset($mkeys[$omkey]) ? $mkeys[$omkey] : '';
		if( $originalRowKey == $request ){
			//if row to update has same request value then just update destination
			$newRedirects[$orkey] =  array( 'request' => $request, 'destination' => $destination );
		}else{
			if( isset( $curRedirects[$request] ) ){
				echo 'duplicate';
				exit;
			}else{
				$newRedirects[$orkey] 	= array( 'request' => $request, 'destination' => $destination );
			}
		}
		if( !empty( $newRedirects ) ){
			$curRedirects = array();
			foreach($newRedirects as $red){
				$curRedirects[$red['request']] = $red['destination'];
			}
		}
		if( $originalRowMetaKey == $request ){
			//if row to udpate has same request value then just update data	
			$newMeta[$omkey]['key'] 		= $request;
			$newMeta[$omkey]['newwindow'] 	= $newWin;
			$newMeta[$omkey]['nofollow'] 	= $noFollow;	
		}else{
			if( isset( $curMeta[$request] ) ){
				echo 'duplicate';
				exit;
			}else{
				$newMeta[$omkey]['key'] 	  = $request;
				$newMeta[$omkey]['newwindow'] = $newWin;
				$newMeta[$omkey]['nofollow']  = $noFollow;
			}
		}
		if( !empty( $newMeta ) ){
			$curMeta = array();
			foreach($newMeta as $meta){
				$curMeta[$meta['key']]['newwindow'] = $meta['newwindow'];
				$curMeta[$meta['key']]['nofollow'] 	= $meta['nofollow'];
			}
		}
		// now save data back to the db options
		update_option('quickppr_redirects', $curRedirects);
		update_option('quickppr_redirects_meta', $curMeta);
		$this->qppr_try_to_clear_cache_plugins();
		echo 'saved';
		exit;
	}
	
	function save_quick_redirects_fields(){
		if( isset( $_POST['submit_301'] ) ) {
			if( check_admin_referer( 'add_qppr_redirects' )){
				$this->quickppr_redirects 	= $this->save_redirects( $_POST['quickppr_redirects'] );
				$this->updatemsg 			= __( 'Quick Redirects Updated.', 'quick-pagepost-redirect-plugin' );
				$this->qppr_try_to_clear_cache_plugins();
			}
		} //if submitted and verified, process the data
	}

	function save_redirects($data){
	// Save the redirects from the options page to the database
	// As of version 5.0.7 the redirects are saved by adding to the existing ones, not resaving all of them from form -
	// this was to prevent the max_input_vars issue when that was set low and there were a lot of redirects.
		$currRedirects 	= get_option( 'quickppr_redirects', array() );
		$currMeta 		= get_option( 'quickppr_redirects_meta', array() );
		//TODO: Add Back up Redirects
		//TODO: Add New Redirects to TOP not Bottom.
		
		$protocols 		= apply_filters( 'qppr_allowed_protocols', array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
		
		for($i = 0; $i < sizeof($data['request']); ++$i) {
			$request 		= esc_url(str_replace(' ','%20',trim($data['request'][$i])), null, 'appip');
			$destination 	= esc_url(str_replace(' ','%20',trim($data['destination'][$i])), null, 'appip');
			$newwin 		= isset($data['newwindow'][$i]) && (int)(trim($data['newwindow'][$i])) == 1 ? 1 : 0;
			$nofoll 		= isset($data['nofollow'][$i]) && (int)(trim($data['nofollow'][$i])) == 1 ? 1 : 0;
			if( strpos($request,'/',0) !== 0 && !$this->qppr_strposa($request,$protocols)){
				$request = '/'.$request;
			} // adds root marker to front if not there
			if((strpos($request,'.') === false && strpos($request,'?') === false) && strpos($request,'/',strlen($request)-1) === false){
				$request = $request.'/';
			} // adds end folder marker if not a file end
			if (($request == '' || $request == '/') && $destination == '') { 
				continue; //if nothing there do nothing
			} elseif ($request != '' && $request != '/' && $destination == '' ){
				$currRedirects[$request] = '/';
			} else { 
				$currRedirects[$request] = $destination;
			}
			$currMeta[$request]['newwindow'] = $newwin;
			$currMeta[$request]['nofollow']  = $nofoll;
		}
		
		update_option( 'quickppr_redirects', sanitize_option( 'quickppr_redirects', $currRedirects ) );
		update_option( 'quickppr_redirects_meta', sanitize_option( 'quickppr_redirects_meta', $currMeta ) );
		$this->quickppr_redirectsmeta 	= get_option( 'quickppr_redirects_meta', array() );
		$this->quickppr_redirects 		= get_option( 'quickppr_redirects', array() );
		return $currRedirects;
	}
	
	function qppr_strposa($haystack, $needle, $offset = 0) {
		if( !is_array( $needle ) ) 
			$needle = array( $needle );
		foreach( $needle as $key => $query ) {
			if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
		}
		return false;
	}

	function add_custom_columns(){
		/* Add Column Headers */
		$usetypes = get_option( 'ppr_use-custom-post-types', 0 ) != 0 ? 1 : 0;
		if( $usetypes == 1 ){
			$post_types_temp = get_post_types( array( 'public' => true ) );
			if( count( $post_types_temp ) == 0){
				$post_types_temp = array(
					'page' 			=> 'page',
					'post' 			=> 'post',
					'attachment' 	=> 'attachment',
					'nav_menu_item' => 'nav_menu_item'
				);
			}
			unset( $post_types_temp['revision'] ); 		// remove revions from array if present as they are not needed.
			unset( $post_types_temp['attachment'] ); 	// remove from array if present as they are not needed.
			unset( $post_types_temp['nav_menu_item'] ); // remove from array if present as they are not needed.
			$ptypesNOTok = is_array( $this->pprptypes_ok ) ? $this->pprptypes_ok : array();
			foreach( $post_types_temp as $type ){
				if( in_array( $type, $ptypesNOTok ) ){
					continue;
				}else{
					if( $type == 'post' ){
						add_filter( "manage_post_posts_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
						add_action( "manage_post_posts_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
					}elseif( $type == 'page' ){
						//add_filter( "manage_page_pages_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
						//add_action( "manage_page_pages_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
						add_filter( "manage_page_posts_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
						add_action( "manage_page_posts_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
					}else{
						add_filter( "manage_{$type}_posts_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
						add_action( "manage_{$type}_posts_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
						add_filter( "manage_{$type}_pages_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
						add_action( "manage_{$type}_pages_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
					}
				}
			}
		}else{
			//if not use custom post types, just use pages and posts.
			add_filter( "manage_post_posts_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
			add_action( "manage_post_posts_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
			//add_filter( "manage_page_pages_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
			//add_action( "manage_page_pages_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
			add_filter( "manage_page_posts_columns", array( $this, 'set_custom_edit_qppr_columns' ) );
			add_action( "manage_page_posts_custom_column" , array( $this, 'custom_qppr_column' ), 10, 2 );
		}
	
	}
	
	function set_custom_edit_qppr_columns($columns) {
		$columns['qppr_redirect'] = __( 'Redirect', 'quick-pagepost-redirect-plugin' );
		return $columns;
	}

	function custom_qppr_column( $column, $post_id ) {
		switch ( $column ) {
			case 'qppr_redirect' :
				$qppr_url 	= get_post_meta( $post_id , '_pprredirect_url', true ) != '' ? get_post_meta( $post_id , '_pprredirect_url', true ) : ''; 
				if( $qppr_url != '' ){
					$qppr_type 		= get_post_meta( $post_id , '_pprredirect_type', true ); 
					$qppr_active 	= get_post_meta( $post_id , '_pprredirect_active', true ); 
					$qppr_rewrite 	= get_post_meta( $post_id , '_pprredirect_rewritelink', true ); 
					$qppr_newwin 	= get_post_meta( $post_id , '_pprredirect_newwindow', true ); 
					$qppr_nofoll 	= get_post_meta( $post_id , '_pprredirect_relnofollow', true ); 
					$rediricon 		= $qppr_newwin != '' ? '<span class="dashicons dashicons-external" title="New Window"></span>' : '<span class="dashicons dashicons-arrow-right-alt" title="Redirects to"></span>';
					if($qppr_active == '1'){
						echo '<div class="qpprfont-on" title="on">('.$qppr_type.') ' . $rediricon . ' <code>'.$qppr_url.'</code></div>';
					}else{
						echo '<div class="qpprfont-not" title="off">('.$qppr_type.') ' . $rediricon . ' <code>'.$qppr_url.'</code></div>';
					}
				}
				break;
		}
	}
		
	function ppr_add_menu_and_metaboxes(){
		/* add menus */
		$qppr_add_page 	= add_menu_page( 'Quick Redirects', 'Quick Redirects', 'manage_options', 'redirect-updates', array( $this, 'ppr_options_page' ), 'dashicons-external' );
		add_submenu_page( 'redirect-updates', 'Quick Redirects', 'Quick Redirects', 'manage_options', 'redirect-updates', array( $this,'ppr_options_page' ) );
		$qppr_exp_page 	= add_submenu_page( 'redirect-updates', 'Import/Export', 'Import/Export', 'manage_options', 'redirect-import-export', array( $this, 'ppr_import_export_page' ) );
		add_submenu_page( 'redirect-updates', 'Redirect Summary', 'Redirect Summary', 'manage_options', 'redirect-summary', array( $this,'ppr_summary_page' ) );
		add_submenu_page( 'redirect-updates', 'Redirect Options', 'Redirect Options', 'manage_options', 'redirect-options', array( $this, 'ppr_settings_page' ) );
		$qppr_meta_page = add_submenu_page( 'redirect-updates', 'Meta Options', 'Meta Options', 'manage_options', 'meta_addon', array( $this, 'qppr_meta_addon_page' ) );
		add_submenu_page( 'redirect-updates', 'FAQs/Help', 'FAQs/Help', 'manage_options', 'redirect-faqs', array( $this, 'ppr_faq_page' ) );
		add_action( 'admin_init', array( $this, 'register_pprsettings' ) );
		add_action( 'load-'.$qppr_meta_page, array( $this, 'qppr_options_help_tab' ) );
		add_action( 'load-'.$qppr_add_page, array( $this, 'qppr_options_help_tab' ) );
		add_action( 'load-'.$qppr_exp_page, array( $this, 'qppr_options_help_tab' ) );

		/* Add Metaboxes */
		$usetypes = get_option( 'ppr_use-custom-post-types', 0 ) != 0 ? 1 : 0;
		if( $usetypes == 1 ){
			$post_types_temp = get_post_types();
			if( count( $post_types_temp ) == 0){
				$post_types_temp = array(
					'page' 			=> 'page',
					'post' 			=> 'post',
					'attachment' 	=> 'attachment',
					'nav_menu_item' => 'nav_menu_item'
				);
			}
			unset( $post_types_temp['revision'] ); 		// remove revions from array if present as they are not needed.
			unset( $post_types_temp['attachment'] ); 	// remove from array if present as they are not needed.
			unset( $post_types_temp['nav_menu_item'] ); // remove from array if present as they are not needed.
		}else{
			//use only for Page && Post if not set to use custom post types
			$post_types_temp = array(
				'page' => 'page',
				'post' => 'post'
			);
		}
		
		$ptypesNOTok = is_array( $this->pprptypes_ok ) ? $this->pprptypes_ok : array();
		
		foreach( $post_types_temp as $type ){
			if( !in_array( $type, $ptypesNOTok ) ){
				$context 	= apply_filters('appip_metabox_context_filter','normal');
				$priority 	= apply_filters('appip_metabox_priority_filter','high');
				add_meta_box( 'edit-box-ppr', __( 'Quick Page/Post Redirect', 'quick-pagepost-redirect-plugin' ) , array( $this, 'edit_box_ppr_1' ), $type, $context, $priority ); 
			}
		}
	}
	
	function qppr_admin_scripts($hook){
		if(in_array( $hook, array( 'post-new.php', 'edit.php', 'post.php', 'toplevel_page_redirect-updates', 'quick-redirects_page_redirect-options', 'quick-redirects_page_redirect-summary', 'quick-redirects_page_redirect-faqs', 'quick-redirects_page_redirect-import-export', 'quick-redirects_page_meta_addon' ) ) ){
			$ajax_add_nonce = wp_create_nonce( 'qppr_ajax_verify' );
			$secDeleteNonce = wp_create_nonce( 'qppr_ajax_delete_ALL_verify' );
			$protocols 		= apply_filters( 'qppr_allowed_protocols', array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
			wp_enqueue_style( 'qppr_admin_meta_style', plugins_url( '/css/qppr_admin_style.css', __FILE__ ) , null , $this->ppr_curr_version );
			//wp_enqueue_script( 'qppr_admin_meta_script', plugins_url( '/js/qppr_admin_script.js', __FILE__ ) , array('jquery'), $this->ppr_curr_version );
			wp_enqueue_script( 'qppr_admin_meta_script', plugins_url( '/js/qppr_admin_script.min.js', __FILE__ ) , array('jquery'), $this->ppr_curr_version );
			wp_localize_script( 'qppr_admin_meta_script', 'qpprData', array( 'msgAllDeleteConfirm' => __( 'Are you sure you want to PERMANENTLY Delete ALL Redirects and Settings (this cannot be undone)?', 'quick-pagepost-redirect-plugin' ),'msgQuickDeleteConfirm' => __( 'Are you sure you want to PERMANENTLY Delete ALL Quick Redirects?', 'quick-pagepost-redirect-plugin' ), 'msgIndividualDeleteConfirm' => __( 'Are you sure you want to PERMANENTLY Delets ALL Individual Redirects?', 'quick-pagepost-redirect-plugin' ), 'securityDelete' => $secDeleteNonce, 'protocols' => $protocols, 'msgDuplicate' => __( 'Redirect could not be saved as a redirect already exists with the same Request URL.', 'quick-pagepost-redirect-plugin' ) , 'msgDeleteConfirm' => __( 'Are you sure you want to delete this redirect?', 'quick-pagepost-redirect-plugin' ) , 'msgErrorSave' => __( 'Error Saving Redirect\nTry refreshing the page and trying again.', 'quick-pagepost-redirect-plugin' ) , 'msgSelect' => 'select a file', 'msgFileType' => __( 'File type not allowed,\nAllowed file type: *.txt', 'quick-pagepost-redirect-plugin' ) , 'adminURL' => admin_url('admin.php'),'ajaxurl'=> admin_url('admin-ajax.php'), 'security' => $ajax_add_nonce, 'error' => __('Please add at least one redirect before submitting form', 'quick-pagepost-redirect-plugin')));
		}
		return;
	}	
	
	function qppr_frontend_scripts(){
		global $qppr_setting_links;
		$qppr_setting_links = true;
		$turnOff 		= get_option( 'ppr_override-active', '0' ) ;
		$useJQ 			= get_option( 'ppr_use-jquery', '0' ) ;
		if( (int) $useJQ == 0 || (int) $turnOff == 1 )
			return;
		global $wpdb;
		$rewrite		= ($this->pproverride_rewrite == '0' || $this->pproverride_rewrite == '') ? false : true;
		$allNewWin 		= get_option( 'ppr_override-newwindow', '0' ) ;
		$allNoFoll 		= get_option( 'ppr_override-nofollow', '0' ) ;
		$noFollNewWin 	= get_option( 'quickppr_redirects_meta', array() );
		$mainQuick 		= get_option( 'quickppr_redirects', array() );
		$linkData 		= array();
		if(is_array($noFollNewWin) && !empty($noFollNewWin)){
			foreach( $noFollNewWin as $key => $val ){
				if( (int) $allNewWin == 1 && (int) $allNoFoll == 1 ){
					$linkData[$key] = array( 1, 1 );
				}elseif( ( (int) $val['nofollow'] !== 0 || (int) $allNoFoll == 1 ) || ( (int) $val['newwindow'] !== 0 || (int) $allNewWin == 1 ) ){
					$newwinval 	= (int) $allNewWin == 1 ? 1 : (int) $val['newwindow'];
					$nofollval 	= (int) $allNoFoll == 1 ? 1 : (int) $val['nofollow'];
					$rewriteval = $rewrite && isset($mainQuick[$key]) && $mainQuick[$key] != '' ? $mainQuick[$key] : '';
					$linkData[$key] = array( $newwinval, $nofollval, $rewriteval );
				}
			};
		}
		$joinSQL	= ((int) $allNewWin == 1 || (int) $allNoFoll == 1 || $rewrite ) ? "" : " INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ( {$wpdb->prefix}posts.ID = mt3.post_id ) ";
		$whereSQL 	= ((int) $allNewWin == 1 || (int) $allNoFoll == 1 || $rewrite ) ? "" : " ( m1.meta_key IN ( '_pprredirect_newwindow' ,'_pprredirect_relnofollow', '_pprredirect_rewritelink', '_pprredirect_url' ) AND m1.meta_value !='0' AND  m1.meta_value !='' ) AND ";
		$finalSQL 	= "SELECT * FROM {$wpdb->prefix}postmeta as `m1` WHERE {$whereSQL} m1.post_id IN ( SELECT post_id FROM {$wpdb->prefix}postmeta as `m` WHERE 1 = 1 AND m.meta_key ='_pprredirect_active' AND m.meta_value = '1');";
		$indReds 	= $wpdb->get_results($finalSQL);
		$parray = array();
		if( is_array($indReds) && !empty($indReds) ){
			foreach( $indReds as $key => $qpost ){
				$postid = $qpost->post_id;
				$postky = $qpost->meta_key;
				$postvl = $qpost->meta_value;
				$parray[ $postid ][ $postky ] = $postvl;
			}
		}
		if( is_array($parray) && !empty($parray) ){
			foreach( $parray as $key => $val ){
				$destURL 	= isset($val['_pprredirect_url']) && $val['_pprredirect_url'] != '' ? $val['_pprredirect_url'] : ''; //get_post_meta( $qpost->ID, '_pprredirect_url', true );
				$rwMeta 	= isset($val['_pprredirect_rewritelink']) && (int)$val['_pprredirect_rewritelink'] == 1 ? true : false; //(int) get_post_meta( $qpost->ID, '_pprredirect_rewritelink', true ) == 1 ? true : false;
				$noFoll 	= (int) $allNoFoll == 1 ? 1 : ( isset($val['_pprredirect_relnofollow']) && (int)$val['_pprredirect_relnofollow'] == 1 ? 1 : 0);//(int) $allNoFoll == 1 ? 1 : ( (int) get_post_meta( $qpost->ID, '_pprredirect_relnofollow', true ) );
				$newWin 	= (int) $allNewWin == 1 ? 1 : ( isset($val['_pprredirect_newwindow']) && $val['_pprredirect_newwindow'] != '' ? 1 : 0);//( get_post_meta( $qpost->ID, '_pprredirect_newwindow', true ) != '' ? 1 : 0 );
				$rewriteval = ($rewrite || $rwMeta) && $destURL != '' ? $destURL : '';
				$redURL 	= get_permalink( $key );
				$linkData[$redURL] = array( $newWin, $noFoll, $rewriteval );
			}
		}
		
		$qppr_setting_links = false;
		//wp_enqueue_script( 'qppr_frontend_scripts', plugins_url( '/js/qppr_frontend_script.js', __FILE__ ) , array('jquery'), $this->ppr_curr_version, true );
		wp_enqueue_script( 'qppr_frontend_scripts', plugins_url( '/js/qppr_frontend_script.min.js', __FILE__ ) , array('jquery'), $this->ppr_curr_version, true );
		wp_localize_script( 'qppr_frontend_scripts', 'qpprFrontData', array( 'linkData' => $linkData, 'siteURL' => site_url(), 'siteURLq' => $this->getQAddress() ) );
	}
	
	function register_pprsettings() {
		register_setting( 'ppr-settings-group', 'ppr_use-custom-post-types' );
		register_setting( 'ppr-settings-group', 'ppr_override-nofollow' );
		register_setting( 'ppr-settings-group', 'ppr_override-newwindow' );
		register_setting( 'ppr-settings-group', 'ppr_override-redirect-type' );
		register_setting( 'ppr-settings-group', 'ppr_override-active' );
		register_setting( 'ppr-settings-group', 'ppr_override-URL' );
		register_setting( 'ppr-settings-group', 'ppr_override-rewrite' );
		register_setting( 'ppr-settings-group', 'ppr_use-jquery' );
		register_setting( 'ppr-settings-group', 'ppr_qpprptypeok' );
		register_setting( 'ppr-settings-group', 'ppr_override-casesensitive' );
		register_setting( 'ppr-settings-group', 'ppr_show-columns' );
		//meta settings
		register_setting( 'qppr-meta-settings-group', 'qppr_meta_addon_sec' );
		register_setting( 'qppr-meta-settings-group', 'qppr_meta_addon_load' );
		register_setting( 'qppr-meta-settings-group', 'qppr_meta_append_to' );
		register_setting( 'qppr-meta-settings-group', 'qppr_meta_addon_trigger' );
		register_setting( 'qppr-meta-settings-group', 'qppr_meta_addon_content' );
		register_setting( 'qppr-meta-settings-group', 'ppr_meta-seconds' );
		register_setting( 'qppr-meta-settings-group', 'ppr_meta-message' );
	}

	function ppr_wp_feed_options( $cache, $url ){
		// this is only for testing cached FAQ
		if( $url == "http://www.anadnet.com/?feed=qppr_faqs" )
			$cache = '1';
		return $cache;
	}
	
	function ppr_faq_page(){
		include_once(ABSPATH . WPINC . '/feed.php');
		echo '
		<div class="wrap">
		 	<h2>' . __( 'Quick Page/Post Redirect FAQs/Help', 'quick-pagepost-redirect-plugin' ) . '</h2>
			<div align="left"><p>' . __( 'The FAQS are now on a feed that can be updated on the fly. If you have a question and don\'t see an answer, please send an email to <a href="mailto:info@anadnet.com">info@anadnet.com</a> and ask your question. If it is relevant to the plugin, it will be added to the FAQs feed so it will show up here. Please be sure to include the plugin you are asking a question about (Quick Page/Post Redirect Plugin) and any other information like your WordPress version and examples if the plugin is not working correctly for you. THANKS!', 'quick-pagepost-redirect-plugin' ) . '</p>
			<hr noshade color="#C0C0C0" size="1" />
		';
		$rss 			= fetch_feed( 'http://www.anadnet.com/?feed=qppr_faqs&ver=' . $this->ppr_curr_version . '&loc=' . urlencode( $this->homelink ) );
		$linkfaq 		= array();
		$linkcontent 	= array();
		if (!is_wp_error( $rss ) ) : 
		    $maxitems 	= $rss->get_item_quantity( 100 ); 
		    $rss_items 	= $rss->get_items( 0, $maxitems ); 
		endif;
			$aqr = 0;
		    if ($maxitems != 0){
			    foreach ( $rss_items as $item ) :
			    	$aqr++; 
			    	$linkfaq[]		= '<li class="faq-top-item"><a href="#faq-'.$aqr.'">'.esc_html( $item->get_title() ).'</a></li>';
				    $linkcontent[] 	= '<li class="faq-item"><a name="faq-'.$aqr.'"></a><h3 class="qa"><span class="qa">Q. </span>'.esc_html( $item->get_title() ).'</h3><div class="qa-content"><span class="qa answer">A. </span>'.$item->get_content().'</div><div class="toplink"><a href="#faq-top">top &uarr;</a></li>';
			    endforeach;
			}
		echo '<a name="faq-top"></a><h2>'.__('Table of Contents','quick-pagepost-redirect-plugin').'</h2>';
		echo '<ol class="qppr-faq-links">';
		echo implode( "\n", $linkfaq );
		echo '</ol>';
		echo '<h2>' . __( 'Questions/Answers', 'quick-pagepost-redirect-plugin' ) . '</h2>';
		echo '<ul class="qppr-faq-answers">';
		echo implode( "\n", $linkcontent );
		echo '</ul>';
		echo '
			</div>
		</div>';
	}
	
	function ppr_summary_page() {
?>
<div class="wrap">
	<h2><?php echo __( 'Quick Page Post Redirect Summary', 'quick-pagepost-redirect-plugin' );?></h2>
	<p><?php echo __( 'This is a summary of Individual &amp; Quick 301 Redirects.', 'quick-pagepost-redirect-plugin' );?></p>
	<br/>
	<?php if($this->updatemsg!=''){?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><strong><?php echo $this->updatemsg;?></strong></p>
	</div>
	<?php } ?>
	<?php $this->updatemsg ='';?>
	<h2 style="font-size:20px;"><?php echo __( 'Summary', 'quick-pagepost-redirect-plugin' );?></h2>
	<div align="left">
		<?php 		    		
			if($this->pproverride_active =='1'){echo '<div class="ppr-acor" style="margin:1px 0;width: 250px;font-weight: bold;padding: 2px;">' . __( 'Acitve Override is on - All Redirects are OFF!', 'quick-pagepost-redirect-plugin' ) . '</div>';}
			if($this->pproverride_nofollow =='1'){echo '<div class="ppr-nfor" style="margin:1px 0;width: 200px;font-weight: bold;padding: 2px;">' . __( 'No Follow Override is on!', 'quick-pagepost-redirect-plugin' ) . '</div>';}
			if($this->pproverride_newwin =='1'){echo '<div class="ppr-nwor" style="margin:1px 0;width: 200px;font-weight: bold;padding: 2px;">' . __( 'New Window Override is on!', 'quick-pagepost-redirect-plugin' ) . '</div>';}
			if($this->pproverride_rewrite =='1'){echo '<div class="ppr-rrlor" style="margin:1px 0;width: 200px;font-weight: bold;padding: 2px;">' . __( 'Rewrite Override is on!', 'quick-pagepost-redirect-plugin' ) . '</div>';}
			$labels 	= array(
				__( 'ID', 'quick-pagepost-redirect-plugin' ),
				__( 'post type', 'quick-pagepost-redirect-plugin' ),
				__( 'active', 'quick-pagepost-redirect-plugin' ),
				__( 'no follow', 'quick-pagepost-redirect-plugin' ),
				__( 'new window', 'quick-pagepost-redirect-plugin' ),
				__( 'type', 'quick-pagepost-redirect-plugin' ),
				__( 'rewrite link', 'quick-pagepost-redirect-plugin' ),
				__( 'original URL', 'quick-pagepost-redirect-plugin' ),
				__( 'redirect to URL', 'quick-pagepost-redirect-plugin' )
			);
			$labelsTD 	= array(
				'<span>'.$labels[0].' :</span>',
				'<span>'.$labels[1].' :</span>',
				'<span>'.$labels[2].' :</span>',
				'<span>'.$labels[3].' :</span>',
				'<span>'.$labels[4].' :</span>',
				'<span>'.$labels[5].' :</span>',
				'<span>'.$labels[6].' :</span>',
				'<span>'.$labels[7].' :</span>',
				'<span>'.$labels[8].' :</span>',
			)
			?>
		<table class="form-table qform-table" width="100%">
			<thead>
				<tr scope="col" class="headrow">
					<th align="center"><?php echo $labels[0];?></th>
					<th align="center"><?php echo $labels[1];?></th>
					<th align="center"><?php echo $labels[2];?></th>
					<th align="center"><?php echo $labels[3];?></th>
					<th align="center"><?php echo $labels[4];?></th>
					<th align="center"><?php echo $labels[5];?></th>
					<th align="center"><?php echo $labels[6];?></th>
					<th align="left"><?php echo $labels[7];?></th>
					<th align="left"><?php echo $labels[8];?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$tempReportArray = array();
			$tempa = array();
			$tempQTReportArray = array();
			if( !empty( $this->quickppr_redirects)){
				foreach( $this->quickppr_redirects as $key => $redir ){
					$tempQTReportArray 	= array('url'=>$key,'destinaition'=>$redir);
					$qr_nofollow 		= isset($this->quickppr_redirectsmeta[$key]['nofollow']) && $this->quickppr_redirectsmeta[$key]['nofollow'] != '' ? $this->quickppr_redirectsmeta[$key]['nofollow'] : '0';
					$qr_newwindow 		= isset($this->quickppr_redirectsmeta[$key]['newwindow']) && $this->quickppr_redirectsmeta[$key]['newwindow'] != '' ? $this->quickppr_redirectsmeta[$key]['newwindow'] : '0';
					$qrtredURL 			= (int) $this->pproverride_rewrite 	== 1 && $this->pproverride_URL != '' ? '<span class="ppr-rrlor">'.$this->pproverride_URL.'</span>' : $redir;
					$qrtactive 			= (int) $this->pproverride_active 	== 1 ? '<span class="ppr-acor">0</span>' : 1;
					$qr_nofollow		= (int) $this->pproverride_nofollow	== 1 ? '<span class="ppr-nfor">1</span>' : $qr_nofollow;
					$qr_newwindow 		= (int) $this->pproverride_newwin 	== 1 ? '<span class="ppr-nwor">1</span>' : $qr_newwindow;
					$qrtrewrit 			= (int) $this->pproverride_rewrite 	== 1 ? '<span class="ppr-rrlor">1</span>': 'N/A';
					$tempReportArray[] = array(
						'_pprredirect_active' => $qrtactive,
						'_pprredirect_rewritelink' => $qrtrewrit,
						'_pprredirect_relnofollow' => $qr_nofollow,
						'_pprredirect_newwindow' => $qr_newwindow,
						'_pprredirect_type' => 'Quick',
						'post_type' => 'N/A',
						'id' => 'N/A',
						'origurl' => $key,
						'_pprredirect_url' => $qrtredURL,
						'_pprredirect_meta_secs' => $this->pprmeta_seconds,
						);
				}
			}
			if(!empty($this->ppr_all_redir_array)){
				foreach( $this->ppr_all_redir_array as $key => $result ){
					$tempa['id']= $key;
					$tempa['post_type'] = get_post_type( $key );
					if(count($result)>0){
						foreach($result as $metakey => $metaval){
							$tempa[$metakey] = $metaval;
						}
					}
					$tempReportArray[] = $tempa;
					unset($tempa);
				}
			}
			if(!empty($tempReportArray)){
				$pclass = 'onrow';
				foreach($tempReportArray as $reportItem){
					$tactive = $reportItem['_pprredirect_active'];
					if($this->pproverride_active =='1'){$tactive = '<span class="ppr-acor">0</span>';}
					$trewrit = $reportItem['_pprredirect_rewritelink'];
					$tnofoll = $reportItem['_pprredirect_relnofollow'];
					$tnewwin = $reportItem['_pprredirect_newwindow'];
					$tredSec = $reportItem['_pprredirect_meta_secs'];
					$tretype = $reportItem['_pprredirect_type'];
					$tredURL = $reportItem['_pprredirect_url'];
					$tpotype = $reportItem['post_type'];
					$tpostid = $reportItem['id'];
					if($tnewwin == '0' || $tnewwin == ''){
						$tnewwin = '0';
					}elseif($tnewwin == 'N/A'){
						$tnewwin = 'N/A';
					}elseif($tnewwin == '_blank'){
						$tnewwin = '1';
					};
					$tnofoll	= (int) $this->pproverride_nofollow == 1 ? '<span class="ppr-nfor">1</span>' : $tnofoll;
					$tnewwin 	= (int) $this->pproverride_newwin == 1 ? '<span class="ppr-nwor">1</span>' : $tnewwin;
					$trewrit	= (int) $this->pproverride_rewrite == 1 ? '<span class="ppr-rrlor">1</span>' : $trewrit;
					$tredURL 	= (int) $this->pproverride_rewrite == 1 && $this->pproverride_URL != '' ? '<span class="ppr-rrlor">' . $this->pproverride_URL . '</span>' : $tredURL;
					$toriurl 	= isset($reportItem['origurl']) ? $reportItem['origurl'] : get_permalink($tpostid);
					$pclass 	= $pclass == 'offrow' ? 'onrow' : 'offrow';
					if($tredURL == 'http://www.example.com' || $tredURL == '<span class="ppr-rrlor">http://www.example.com</span>'){$tredURL='<strong>N/A - redirection will not occur</strong>';}
				?>
				<tr class="<?php echo $pclass;?>">
					<?php if( $tpostid != 'N/A'){ ?> 
					<td align="left"><?php echo $labelsTD[0];?><a href="<?php echo admin_url('post.php?post='.$tpostid.'&action=edit');?>" title="edit"><?php echo $tpostid;?></a></td>
					<?php }else{ ?>
					<td align="left"><?php echo $labelsTD[0];?><?php echo $tpostid;?></td>
					<?php } ?>
					<td align="center"><?php echo $labelsTD[1];?><?php echo $tpotype;?></td>
					<td align="center"><?php echo $labelsTD[2];?><?php echo $tactive;?></td>
					<td align="center"><?php echo $labelsTD[3];?><?php echo $tnofoll;?></td>
					<td align="center"><?php echo $labelsTD[4];?><?php echo $tnewwin;?></td>
					<td align="center"><?php echo $labelsTD[5];?><?php echo $tretype;?></td>
					<td align="center"><?php echo $labelsTD[6];?><?php echo $trewrit;?></td>
					<td align="left"><?php echo $labelsTD[7];?><?php echo $toriurl;?></td>
					<td align="left"><?php echo $labelsTD[8];?><?php echo $tredURL;?></td>
				</tr>
			<?php }
			}
		 ?>
		 	</tbody>
		</table>
	</div>
</div>
<?php 
	} 
	
	function ppr_import_export_page(){
		if(isset($_GET['update'])){
			if($_GET['update']=='4'){$this->updatemsg ='' . __( 'Quick Redirects Imported & Replaced.', 'quick-pagepost-redirect-plugin' ) . '';}
			if($_GET['update']=='5'){$this->updatemsg ='' . __( 'Quick Redirects Imported & Added to Existing Redirects.', 'quick-pagepost-redirect-plugin' ) . '';}
		}
		echo '<div class="wrap">';
		echo '	<h2>' . __( 'Import/Export Redirects', 'quick-pagepost-redirect-plugin' ) . '</h2>';
		if($this->updatemsg != '')
			echo '	<div class="updated settings-error" id="setting-error-settings_updated"><p><strong>' . $this->updatemsg . '</strong></p></div>';
		$this->updatemsg = '';
		?>
		<div class="qppr-content">
			<div class="qppr-sidebar">
				<div class="pprdonate">
					<div>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
							<input name="cmd" value="_s-xclick" type="hidden"/>
							<input name="hosted_button_id" value="8274582" type="hidden"/>
							<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image">
							<img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" border="0" height="1" width="1" />
						</form>
					</div>
					<?php echo __( 'If you enjoy or find any of our plugins useful, please donate a few dollars to help with future development and updates. We thank you in advance.', 'quick-pagepost-redirect-plugin' );?>
				</div>
			</div>
			<div class="qppr-left">
				<table style="border-collapse: collapse" class="form-table">
				<tr valign="top">
					<td><label class="qppr-label"><strong><?php echo __( 'Export Redirects', 'quick-pagepost-redirect-plugin' );?></strong></label>
					<p><?php echo __( 'You should back-up your redirect regularly in case something happens to the database.', 'quick-pagepost-redirect-plugin' );?></p>
						<p><?php echo __( 'Please use the below buttons to make a back-up as either encoded (unreadable) or pipe separated', 'quick-pagepost-redirect-plugin' );?> (<code>|</code>).</p>
						<br /><p><input class="button button-primary qppr-export-quick-redirects" type="button" name="qppr-export-quick-redirects" value="<?php echo __( 'EXPORT all Quick Redirects (Encoded)', 'quick-pagepost-redirect-plugin' );?>" onclick="document.location='<?php echo wp_nonce_url( admin_url('admin.php?page=redirect-options&qppr-file-type=encoded').'&action=export-quick-redirects-file', 'export-redirects-qppr'); ?>';" /></p>
						<p><?php echo __( 'OR', 'quick-pagepost-redirect-plugin' );?></p>
						<p><input class="button button-primary qppr-export-quick-redirects" type="button" name="qppr-export-quick-redirects" value="<?php echo __( 'EXPORT all Quick Redirects (PIPE Separated)', 'quick-pagepost-redirect-plugin' );?>" onclick="document.location='<?php echo wp_nonce_url( admin_url('admin.php?page=redirect-options').'&action=export-quick-redirects-file&qppr-file-type=pipe', 'export-redirects-qppr'); ?>';" /></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" colspan="2"><hr noshade color="#EAEAEA" size="1"></th>
				</tr>
				<tr valign="top">
					<td><label class="qppr-label"><strong><?php echo __( 'Import Redirects', 'quick-pagepost-redirect-plugin' );?></strong></label>
					<p><?php echo __( 'If you want to replace or restore redirects from a file, use the "Restore" option.', 'quick-pagepost-redirect-plugin' );?></p>
					<p><?php echo __( 'To add new redirects in bulk use the "Add To" option - NOTE: to Add To redirects, the file must be pipe dilimited ', 'quick-pagepost-redirect-plugin' );?> (<code>|</code>).</p>
						<br/>
						<input class="button-primary qppr-import-quick-redirects" type="button" id="qppr-import-quick-redirects-button" name="qppr-import-quick-redirects" value="<?php echo __( 'RESTORE Saved Quick Redirects', 'quick-pagepost-redirect-plugin' );?>" />
						<?php echo __( 'OR', 'quick-pagepost-redirect-plugin' );?>
						<input class="button-primary qppr_addto_qr" type="button" id="qppr_addto_qr_button" name="qppr_addto_qr" value="<?php echo __( 'ADD TO Quick Redirects', 'quick-pagepost-redirect-plugin' );?>" />
						<div id="qppr_import_form" class="hide-if-js">
							<form action="<?php echo admin_url('admin.php?page=redirect-import-export'); ?>" method="post" enctype="multipart/form-data">
								<p style="margin:1em 0;">
									<label><?php echo __( 'Select Quick Redirects file to import:', 'quick-pagepost-redirect-plugin' );?></label>
									<input type="file" name="qppr_file" onchange="qppr_check_file(this);" />
								</p>
								<p class="submit">
									<?php wp_nonce_field( 'import-quick-redrects-file' ); ?>
									<input class="button button-primary" type="submit" id="import-quick-redrects-file" name="import-quick-redrects-file" value="IMPORT & REPLACE Current Quick Redirects" />
								</p>
							</form>
						</div>
						<div id="qppr_addto_form" class="hide-if-js">
							<form action="<?php echo admin_url('admin.php?page=redirect-import-export'); ?>" method="post" enctype="multipart/form-data">
								<p style="margin:.5em 0 1em 1em;color:#444;"> <?php echo __( 'The import file should be a text file with one rediect per line, PIPE separated, in this format:', 'quick-pagepost-redirect-plugin' );?><br/>
									<br/>
									<code><?php echo __( 'redirect|destination|newwindow|nofollow', 'quick-pagepost-redirect-plugin' );?></code><br/>
									<br/><?php echo __( 'for Example:', 'quick-pagepost-redirect-plugin' );?>
									<br/><br/>
									<code><?php echo __( '/old-location.htm|http://some.com/new-destination/|0|1', 'quick-pagepost-redirect-plugin' );?></code><br />
									<code><?php echo __( '/dontate/|http://example.com/destination/|1|1', 'quick-pagepost-redirect-plugin' );?></code><br/>
									<br/>
									<strong><?php echo __( 'IMPORTANT:', 'quick-pagepost-redirect-plugin' );?></strong> <?php echo __( 'Make Sure any destination URLs that have a PIPE in the querystring data are URL encoded before adding them!', 'quick-pagepost-redirect-plugin' );?><br/>
									<br/>
									<label><?php echo __( 'Select Quick Redirects file to import:', 'quick-pagepost-redirect-plugin' );?></label>
									<input type="file" name="qppr_file_add" onchange="qppr_check_file(this);" />
								</p>
								<p class="submit">
									<?php wp_nonce_field( 'import_redirects_add_qppr' ); ?>
									<input class="button button-primary" type="submit" id="import_redirects_add_qppr" name="import_redirects_add_qppr" value="<?php echo __( 'ADD TO Current Quick Redirects', 'quick-pagepost-redirect-plugin' );?>" />
								</p>
							</form>
						</div></td>
				</tr>
				<tr valign="top">
					<th scope="row" colspan="2"><hr noshade color="#EAEAEA" size="1"></th>
				</tr>
			</table>
			</div>
			<div class="clear-both"></div>
			</div>
		<?php
		echo '</div>';
	}

	function ppr_settings_page() {
		if( isset( $_GET['update'] ) && $_GET['update'] != '' ){
			if( $_GET['update'] == '3' ){ $this->updatemsg = __( 'All Quick Redirects deleted from database.', 'quick-pagepost-redirect-plugin' );}
			if( $_GET['update'] == '2' ){ $this->updatemsg = __( 'All Individual Redirects deleted from database.', 'quick-pagepost-redirect-plugin' );}
			if( $_GET['update'] == '4' ){ $this->updatemsg = __( 'Quick Redirects Imported & Replaced.', 'quick-pagepost-redirect-plugin' );}
			if( $_GET['update'] == '5' ){ $this->updatemsg = __( 'Quick Redirects Imported & Added to Existing Redirects.', 'quick-pagepost-redirect-plugin' );}
			if( $_GET['update'] == '6' ){ $this->updatemsg = __( 'All Redirects and Settings deleted from database', 'quick-pagepost-redirect-plugin' );}
			if( $_GET['update'] == '0' ){ $this->updatemsg = __( 'There was an problem with your last request. Please reload the page and try again.', 'quick-pagepost-redirect-plugin' );}
		}
	?>
<div class="wrap" style="position:relative;">
	<h2><?php echo __( 'Quick Page Post Redirect Options', 'quick-pagepost-redirect-plugin' );?></h2>
	<?php if($this->updatemsg != ''){?>
		<div class="updated" id="setting-error-settings_updated">
			<p><strong><?php echo $this->updatemsg;?></strong></p>
		</div>
	<?php } ?>
	<?php $this->updatemsg = '';//reset message;?>
	<div class="qppr-content">
		<div class="qppr-sidebar">
			<div class="pprdonate">
				<div>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input name="cmd" value="_s-xclick" type="hidden"/>
						<input name="hosted_button_id" value="8274582" type="hidden"/>
						<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image">
						<img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" border="0" height="1" width="1" />
					</form>
				</div>
				<?php echo __( 'If you enjoy or find any of our plugins useful, please donate a few dollars to help with future development and updates. We thank you in advance.', 'quick-pagepost-redirect-plugin' );?>
			</div>
		</div>
		<div class="qppr-left">
		<form method="post" action="options.php" class="qpprform">
			<?php settings_fields( 'ppr-settings-group' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row" colspan="2" class="qppr-no-padding"><hr noshade color="#EAEAEA" size="1"></th>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2 style="display:inline-block;"><?php echo __( 'Basic Settings', 'quick-pagepost-redirect-plugin' );?></h2></th>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Use with Custom Post Types?', 'quick-pagepost-redirect-plugin' );?></label></th>
					<td><input type="checkbox" name="ppr_use-custom-post-types" value="1"<?php if(get_option('ppr_use-custom-post-types')=='1'){echo ' checked="checked" ';} ?>/></td>
				</tr>
				<tr>
					<th scope="row"><label><span style="color:#FF0000;font-weight:bold;font-size:100%;margin-left:0px;"><?php echo __( 'Hide', 'quick-pagepost-redirect-plugin' );?></span> <?php echo __( 'meta box for following Post Types:', 'quick-pagepost-redirect-plugin' );?></label></th>
					<td><?php
						$ptypes = get_post_types();
						$ptypesok = $this->pprptypes_ok;
						if(!is_array($ptypesok)){$ptypesok = get_option( 'ppr_qpprptypeok' );}
						if(!is_array($ptypesok)){$ptypesok = array();}
						$ptypeHTML = '<div class="qppr-posttypes">';
						foreach($ptypes as $ptype){
							if($ptype != 'nav_menu_item' && $ptype != 'attachment' && $ptype != 'revision'){
								if(in_array($ptype,$ptypesok)){
									$ptypecheck = ' checked="checked"';
								}else{
									$ptypecheck = '';
								}
								$ptypeHTML .= '<div class="qppr-ptype"><input class="qppr-ptypecb" type="checkbox" name="ppr_qpprptypeok[]" value="'.$ptype.'"'.$ptypecheck.' /> <div class="ppr-type-name">'.$ptype.'</div></div>';
							}
						}
						$ptypeHTML .= '</div>';
					echo $ptypeHTML;
					?></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Show Column Headers?', 'quick-pagepost-redirect-plugin' );?></label></th>
					<td><input type="checkbox" id ="ppr_show-columns" name="ppr_show-columns" value="1"<?php if(get_option('ppr_show-columns')=='1'){echo ' checked="checked" ';} ?>/> <span><?php echo __( 'Show Columns on list pages for set up redirects.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>

					<th scope="row"><label><?php echo __( 'Use jQuery?', 'quick-pagepost-redirect-plugin' );?></label></th>
					<td><input type="checkbox" id ="ppr_use-jquery" name="ppr_use-jquery" value="1"<?php if(get_option('ppr_use-jquery')=='1'){echo ' checked="checked" ';} ?>/> <span><?php echo __( 'Increases effectiveness of plugin. If you have a jQuery conflict, try turning this off.', 'quick-pagepost-redirect-plugin' );?></span><br /><span style="margin:0;"><?php echo __( 'Uses jQuery to add the "New Window" and "No Follow" attributes to links.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row" colspan="2" class="qppr-no-padding"><hr noshade color="#EAEAEA" size="1"></th>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2 style="font-size:20px;display:inline-block;"><?php echo __( 'Master Override Options', 'quick-pagepost-redirect-plugin' );?></h2><span><?php echo __( '<strong>NOTE: </strong>The below settings will override all individual settings.', 'quick-pagepost-redirect-plugin' );?></span></th>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Turn OFF all Redirects?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="checkbox" name="ppr_override-active" value="1"<?php if(get_option('ppr_override-active')=='1'){echo ' checked="checked" ';} ?>/> <span><?php echo __( 'Basically the same as having no redirects set up.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Make ALL Redirects have <code>rel="nofollow"</code>?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="checkbox" name="ppr_override-nofollow" value="1"<?php if(get_option('ppr_override-nofollow')=='1'){echo ' checked="checked" ';} ?>/> <span><?php echo __( 'Requires "use jQuery" option to work with Quick Redirects.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Make ALL Redirects open in a New Window?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="checkbox" name="ppr_override-newwindow" value="1"<?php if(get_option('ppr_override-newwindow')=='1'){echo ' checked="checked" ';} ?>/>	<span><?php echo __( 'Requires "use jQuery" option to work with Quick Redirects.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Make ALL Redirects this type:', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><select name="ppr_override-redirect-type">
							<option value="0"><?php echo __( 'Use Individual Settings', 'quick-pagepost-redirect-plugin' );?></option>
							<option value="301" <?php if( get_option('ppr_override-redirect-type')=='301') {echo ' selected="selected" ';} ?>>301 <?php echo __( 'Permanant Redirect', 'quick-pagepost-redirect-plugin' );?></option>
							<option value="302" <?php if( get_option('ppr_override-redirect-type')=='302') {echo ' selected="selected" ';} ?>>302 <?php echo __( 'Temporary Redirect', 'quick-pagepost-redirect-plugin' );?></option>
							<option value="307" <?php if( get_option('ppr_override-redirect-type')=='307') {echo ' selected="selected" ';} ?>>307 <?php echo __( 'Temporary Redirect', 'quick-pagepost-redirect-plugin' );?></option>
							<option value="meta" <?php if(get_option('ppr_override-redirect-type')=='meta'){echo ' selected="selected" ';} ?>><?php echo __( 'Meta Refresh Redirect', 'quick-pagepost-redirect-plugin' );?></option>
						</select>
						<span> <?php echo __( '(This will also override Quick Redirects)', 'quick-pagepost-redirect-plugin' );?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Make ALL redirects Case Sensitive?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="checkbox" name="ppr_override-casesensitive" value="1"<?php if(get_option('ppr_override-casesensitive')=='1'){echo ' checked="checked" ';} ?>/> <span> <?php echo __( 'Makes URLs CaSe SensiTivE - i.e., /somepage/ DOES NOT EQUAL /SoMEpaGe/', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Make ALL Redirects go to this URL:', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="text" size="50" name="ppr_override-URL" value="<?php echo get_option('ppr_override-URL'); ?>"/> <span><?php echo __( 'Use full URL including <code>http://</code>.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Rewrite ALL Redirects URLs to Show in LINK?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td><input type="checkbox" name="ppr_override-rewrite" value="1"<?php if(get_option('ppr_override-rewrite')=='1'){echo ' checked="checked" ';} ?>/> <span><?php echo __( 'Makes link show redirect URL instead of the original URL. Will only work on Quick Redirects if the "Use jQuery" option is set.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><hr noshade color="#EAEAEA" size="1"></th>
				</tr>
				<tr>
					<th scope="row" colspan="2" class="qppr-no-padding"><h2 style="display:inline-block;"><?php echo __( 'Plugin Clean Up', 'quick-pagepost-redirect-plugin' );?></h2><span><?php echo __( '<strong>NOTE: </strong>This will DELETE all redirects - so be careful with this.', 'quick-pagepost-redirect-plugin' );?></span></th>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Delete Redirects?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td>
						<input class="button-secondary qppr-delete-regular" type="button" name="qppr-delete-regular" value="<?php echo __( 'Delete All Individual Redirects', 'quick-pagepost-redirect-plugin' );?>" />
						<input class="button-secondary qppr-delete-quick" type="button" name="qppr-delete-quick" value="<?php echo __( 'Delete all Quick Redirects', 'quick-pagepost-redirect-plugin' );?>" />
						<span style="display: block;margin-top: 5px;"><?php echo __( 'Individual Redirects are redirects set up on individual pages or posts when in the editing screen. The Quick Redirects are set up on the Quick Redirects page.', 'quick-pagepost-redirect-plugin' );?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Delete ALL Redirects & Settings?', 'quick-pagepost-redirect-plugin' );?> </label></th>
					<td>
						<input class="button-secondary qppr-delete-everything" type="button" name="qppr-delete-everything" value="<?php echo __( 'Delete ALL Redirects AND Settings', 'quick-pagepost-redirect-plugin' );?>" />
						<span style="color: #0000ff;display: block;margin-top: 5px;"><?php echo __( 'All Redirects and Settings will be removed from the database. This can NOT be undone!', 'quick-pagepost-redirect-plugin' );?></span>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php echo __( 'Save Changes', 'quick-pagepost-redirect-plugin' );?>" /></p>
		</form>
		</div>
		<div class="clear-both"></div>
	</div>
</div>
<?php } 

	function qppr_options_help_tab(){
		//generate the options page in the wordpress admin
		$screen 	= get_current_screen();
		$screen_id 	= $screen->id;
		if($screen_id == 'toplevel_page_redirect-updates' ){
			$content 	= '
			<div style="padding:10px 0;">		
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left">Example Requests</th>
					<th align="left"></th>
					<th align="left">Example Destinations</th>
				</tr>
				<tr>
					<td><code>/about.htm</code></td>
					<td>&nbsp;&raquo;&nbsp;</td>
					<td><code>'.$this->homelink.'/about/</code></td>
				</tr>
				<tr>
					<td><code>/directory/landing/</code></td>
					<td>&nbsp;&raquo;&nbsp;</td>
					<td><code>/about/</code></td>
				</tr>
				<tr>
					<td><code>'. str_replace("http://", "https://",$this->homelink).'/contact-us/</code></td>
					<td>&nbsp;&raquo;&nbsp;</td>
					<td><code>'.$this->homelink.'/contact-us-new/</code></td>
				</tr>
			</table>

			</div>
			';
			$screen->add_help_tab( array( 
			   'id' => 'qppr_sample_redirects',
			   'title' => __( 'Examples', 'quick-pagepost-redirect-plugin' ), 
			   'content' => $content ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr_add_redirects',
			   'title' => __( 'Troubleshooting', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '
			   <div style="padding:10px 0;">		
				<b style="color:red;">' . __( 'IMPORTANT TROUBLESHOOTING NOTES:', 'quick-pagepost-redirect-plugin' ) . '</b>
				<ol style="margin-top:5px;">
					<li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'At this time the New Window (NW) and No Follow (NF) features will not work for Quick Redirects unless "Use jQuery" is enabled in the options.', 'quick-pagepost-redirect-plugin' ) . '</li>
					<li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'It is recommended that the <b>Request URL</b> be relative to the ROOT directory and contain the <code>/</code> at the beginning.', 'quick-pagepost-redirect-plugin' ) . '</li>
					<li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'If you do use the domain name in the Request URL field, make sure it matches your site\'s domain style and protocol. For example, if your site uses "www" in front of your domain name, be sure to include it. if your site uses <code>https://</code>, use it as the protocol. Our best guess is that your domain and protocol are', 'quick-pagepost-redirect-plugin' ) . ' <code>'.network_site_url('/').'</code></li>
					<!--li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'If you are having issues with the link not redirecting on a SSL site with mixed SSL (meaning links can be either SSL or non SSL), try adding two redirects, one with and one without the SSL protocol.', 'quick-pagepost-redirect-plugin' ) . '</li-->
					<li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'The <b>Destination</b> field can be any valid URL or relative path (from root), for example', 'quick-pagepost-redirect-plugin' ) . ' <code>http://www.mysite.com/destination-page/</code> OR <code>/destination-page/</code></li>
					<li style="color:#214070;margin-left:15px;list-style-type:disc;">' . __( 'In order for NW (open in a new window) or NF (rel="nofollow") options to work with Quick Redirects, you need to have:', 'quick-pagepost-redirect-plugin' ) . '
						<ol>
							<li>' . __( '"Use jQuery?" option selected in the settings page', 'quick-pagepost-redirect-plugin' ) . '</li>
							<li>' . __( 'A link that uses the request url SOMEWHERE in your site page - i.e., in a menu, content, sidebar, etc.', 'quick-pagepost-redirect-plugin' ) . ' </li>
							<li>' . __( 'The open in a new window or nofollow settings will not happen if someone just types the old link in the URL or if they come from a bookmark or link outside your site - in essence, there needs to be a link that they click on in your site so that the jQuery script can add the appropriate <code>target</code> and <code>rel</code> properties to the link to make it work.', 'quick-pagepost-redirect-plugin' ) . '</li>
						</ol>
					</li>
				</ol>
				</div>' ,
			) );
		}elseif( $screen_id == 'quick-redirects_page_redirect-import-export' ){
			$screen->add_help_tab( array( 
			   'id' => 'qppr_export_redirects',
			   'title' => __( 'Export Redirects', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'You can export redirects in two formats - Encoded or Delimited.', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr_import_redirects',
			   'title' => __( 'Import Redirects', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>Help content coming soon.</p></div>' ,
			) );
		}elseif( $screen_id == 'quick-redirects_page_meta_addon' ){
			$screen->add_help_tab( array( 
			   'id' => 'qppr-load-page-content',
			   'title' => __( 'Load Content?', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'Use the <strong>Load Content?</strong> option to allow the page content to load as normal or to only load a blank page or the content provided in the <strong>Page Content</strong> section. ', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'If checked, all of the original content will load, so keep this in mind when setting the <strong>Redirect Seconds</strong> - if set too low, the page will not compeletely load. ', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr-redirect-seconds',
			   'title' => __( 'Redirect Seconds', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'Enter the nuber of seconds to wait before the redirect happens. Enter 0 to have an instant redirect*.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( '*Keep in mind that the redirect seconds will start counting only AFTER the <strong>Redirect Trigger</strong> element is loaded - so 0 may be slightly longer than instant, depending on how much content needs to load before the trigger happens.', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr-redirect-trigger',
			   'title' => __( 'Redirect Trigger', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'The class or id or tag name of the element to load before the redirect starts counting down. If nothing is used, it will default to the body tag as a trigger.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'If you use a class, the class name should have the "." in the name, i.e., <strong>.my-class-name</strong>', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'If you use an id, the id should have the "#" in the name, i.e., <strong>#my-id-name</strong>.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'If you use a tag name, the name should NOT have the "&lt;" or "&gt;" characters in the name, i.e., &lt;body&gt; would just be <strong>body</strong>.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'Do not use a tag name that is common, like "a" or "div" as it will trigger on all events.', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr-redirect-append',
			   'title' => __( 'Append Content To', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'The class, id or tag name that you want the content in the <strong>Page Content</strong> to be loading into.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'If you are loading the content of the page, use an existing class or id for an existing element (i.e., .page-content) so your additional page content (if any) is loaded into that element.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'When no class, id or tag name is used, the <strong>body</strong> tag will be used.', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
			$screen->add_help_tab( array( 
			   'id' => 'qppr-redirect-content',
			   'title' => __( 'Page Content', 'quick-pagepost-redirect-plugin' ), 
			   'content' => '<div style="padding:10px 0;"><p>' . __( 'This is your page content you want to add. If you have a "tracking pixel" script or image tag you want to use, add it here.', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'A good example of use, is adding a tracking script (or Facebook Conversion Pixel) to the <strong>Page Content box</strong> and unchecking the <strong>Load Content?</strong> box. Then set the <strong>Redirect Seconds</strong> to 1 or 2 so the script has a chance to load and set <strong>Append Content</strong> To to "body" and <strong>Redirect Trigger</strong> to "body".', 'quick-pagepost-redirect-plugin' ) . '</p>
			   <p>' . __( 'Additionally, you can add the redirect counter to the page by adding the code sample under the <strong>Page Content</strong> box.', 'quick-pagepost-redirect-plugin' ) . '</p></div>' ,
			) );
		}
	}

	function ppr_options_page(){
?>
<div class="wrap">
	<h2><?php echo __( 'Quick Redirects (301 Redirects)', 'quick-pagepost-redirect-plugin' );?></h2>
	<?php if($this->updatemsg != ''){?>
		<div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo $this->updatemsg;?></strong></p></div>
	<?php } ?>
	<?php $this->updatemsg ='';//reset message;?>
	<?php 
	$isJQueryOn 		= get_option('ppr_use-jquery');
	$isJQueryMsgHidden 	= get_option('qppr_jQuery_hide_message');
	$isJQueryMsgHidden2 = get_option('qppr_jQuery_hide_message2');?>
		<?php if( $isJQueryOn == '' && ( $isJQueryMsgHidden == '' || $isJQueryMsgHidden == '0' ) ){ ?>
			<div class="usejqpprmessage error below-h2" id="usejqpprmessage">
				<?php echo __( 'The <code>Use jQuery?</code> option is turned off in the settings.<br/>In order to use <strong>NW</strong> (open in a new window) or <strong>NF</strong> (add rel="nofollow") options for Quick Redirects, you must have it enabled.', 'quick-pagepost-redirect-plugin' );?><br/>
				<div class="hidepprjqmessage" style=""><a href="javascript:void(0);" id="hidepprjqmessage"><?php echo __( 'hide this message', 'quick-pagepost-redirect-plugin' );?></a></div>
			</div>
		<?php }elseif($isJQueryMsgHidden2 !='1'){ ?>
			<div class="usejqpprmessage info below-h2" id="usejqpprmessage2">
				<?php echo __( 'To use the <strong>NW</strong> (open in a new window) <strong>NF</strong> (nofollow) options, check the appropriate option and update when adding redirects. Then, any link in the page that has the request URL will be updated with these options (as long as you have <code>Use jQuery?</code> enabled in the plugin settings.', 'quick-pagepost-redirect-plugin' );?>
				<div class="hidepprjqmessage" style=""><a href="javascript:void(0);" id="hidepprjqmessage2"><?php echo __( 'hide this message', 'quick-pagepost-redirect-plugin' );?></a></div>
			</div>
		<?php }?>
	<p><?php echo __( 'Quick Redirects are useful when you have links from an old site that now come up 404 Not Found, and you need to have them redirect to a new location on the current site - as long as the old site and the current site have the same domain name. They are also helpful if you have an existing URL that you need to send some place else and you don\'t want to create a Page or Post just to use the individual Page/Post Redirect option.', 'quick-pagepost-redirect-plugin' );?></p>
	<p><?php echo __( 'To add Quick Redirects, put the URL for the redirect in the <strong>Request URL</strong> field, and the URL it should be redirected to in the <strong>Destination URL</strong> field. To delete a redirect, click the trash can at the end of that row. To edit a redirect, click the pencil edit icon.', 'quick-pagepost-redirect-plugin' );?></p>
	<p><?php echo __( 'See \'HELP\' in the upper right corner, for troubleshooting problems and example redirects.', 'quick-pagepost-redirect-plugin' );?></p>
	<form method="post" action="admin.php?page=redirect-updates" id="qppr_quick_save_form">
		<?php wp_nonce_field( 'add_qppr_redirects' ); ?>
		<div class="qppr_quick_redirects_wrapper">
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th align="left" colspan="8"><h3><?php echo __( 'Add New Redirects', 'quick-pagepost-redirect-plugin' );?></h3></th>
			</tr>
			<tr>
				<th align="left" colspan="2"><?php echo __( 'Request URL', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="left">&nbsp;</th>
				<th align="left"><?php echo __( 'Destination URL', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="center"><?php echo __( 'NW', 'quick-pagepost-redirect-plugin' );?>*</th>
				<th align="center"><?php echo __( 'NF', 'quick-pagepost-redirect-plugin' );?>*</th>
				<th align="left"></th>
				<th align="left"></th>
			</tr>
			<tr>
				<td class="table-qppr-req" colspan="2"><input type="text" name="quickppr_redirects[request][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-arr">&nbsp;&raquo;&nbsp;</td>
				<td class="table-qppr-des"><input type="text" name="quickppr_redirects[destination][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-nwn"><input class="pprnewwin" type="checkbox" name="quickppr_redirects[newwindow][0]" value="1" title="<?php echo __( 'open in a New Window', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-nfl"><input class="pprnofoll" type="checkbox" name="quickppr_redirects[nofollow][0]" value="1" title="<?php echo __( 'add No Follow', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-edt"></td>
				<td class="table-qppr-del"></td>
			</tr>
			<tr>
				<td class="table-qppr-req" colspan="2"><input type="text" name="quickppr_redirects[request][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-arr">&nbsp;&raquo;&nbsp;</td>
				<td class="table-qppr-des"><input type="text" name="quickppr_redirects[destination][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-nwn"><input class="pprnewwin" type="checkbox" name="quickppr_redirects[newwindow][1]" value="1" title="<?php echo __( 'open in a New Window', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-nfl"><input class="pprnofoll" type="checkbox" name="quickppr_redirects[nofollow][1]" value="1" title="<?php echo __( 'add No Follow', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-edt"></td>
				<td class="table-qppr-del"></td>
			</tr>
			<tr>
				<td class="table-qppr-req" colspan="2"><input type="text" name="quickppr_redirects[request][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-arr">&nbsp;&raquo;&nbsp;</td>
				<td class="table-qppr-des"><input type="text" name="quickppr_redirects[destination][]" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-nwn"><input class="pprnewwin" type="checkbox" name="quickppr_redirects[newwindow][2]" value="1" title="<?php echo __( 'open in a New Window', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-nfl"><input class="pprnofoll" type="checkbox" name="quickppr_redirects[nofollow][2]" value="1" title="<?php echo __( 'add No Follow', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-edt"></td>
				<td class="table-qppr-del"></td>
			</tr>
			<tr>
				<td style="text-align:right;" colspan="6"><div style="font-size: 11px;"><em>*<?php echo __( 'New Window(NW) and NoFollow(NF) functionality not available unless "Use with jQuery" is set in the options.', 'quick-pagepost-redirect-plugin' );?></em></div></td>
				<td style="text-align:right;" colspan="2"></td>
			</tr>
			<tr>
				<td align="left" colspan="8"><p class="submit"><input type="submit" name="submit_301" class="button button-primary" value="<?php echo __( 'Add New Redirects', 'quick-pagepost-redirect-plugin' );?>" /></p></td>
			</tr>
			<tr>
				<td class="newdiv" colspan="8"><div></div></td>
			</tr>
			<tr>
				<th align="left" colspan="8"><h3 id="qppr-existing-redirects"><?php echo __( 'Existing Redirects', 'quick-pagepost-redirect-plugin' );?></h3></th>
			</tr>
			<tr>
				<th align="left" colspan="2"><?php echo __( 'Request URL', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="left">&nbsp;</th>
				<th align="left"><?php echo __( 'Destination URL', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="center"><?php echo __( 'NW', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="center"><?php echo __( 'NF', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="center"><?php //echo __( 'Edit', 'quick-pagepost-redirect-plugin' );?></th>
				<th align="center"><?php //echo __( 'Delete', 'quick-pagepost-redirect-plugin' );?></th>
			</tr>
			<?php echo $this->expand_redirects(); ?>
			<tr id="qppr-edit-row-holder" class="qppr-editing">
				<td class="table-qppr-req cloned" colspan="2"><input class="input-qppr-req" type="text" name="request" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-arr cloned">&nbsp;&raquo;&nbsp;</td>
				<td class="table-qppr-des cloned"><input class="input-qppr-dest" type="text" name="destination" value="" style="max-width:100%;width:100%;" /></td>
				<td class="table-qppr-nwn cloned"><input class="input-qppr-neww" type="checkbox" name="newwindow" value="1" title="<?php echo __( 'open in a New Window', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-nfl cloned"><input class="input-qppr-nofo" type="checkbox" name="nofollow" value="1" title="<?php echo __( 'add No Follow', 'quick-pagepost-redirect-plugin' );?>" /></td>
				<td class="table-qppr-sav cloned"><span class="qpprfont-save" data-rowid="" title="<?php echo __( 'Save', 'quick-pagepost-redirect-plugin' );?>"></span></td>
				<td class="table-qppr-can cloned"><span class="qpprfont-cancel" data-rowid="" title="<?php echo __( 'Cancel', 'quick-pagepost-redirect-plugin' );?>"></span></td>
			</tr>
			<tr id="qppr-edit-row-saving" class="qppr-saving">
				<td colspan="8" class="qppr-saving-row"><div class="saving"></div></td>
			</tr>
		</table>
		</div>
	</form>
	<table id="qppr-temp-table-holder"><tr><td></td></tr></table>
	</div>
<?php
	} 

	function expand_redirects(){
	//utility function to return the current list of redirects as form fields
		$output = '';
		if (!empty($this->quickppr_redirects)) {
			$ww = 0;
			foreach ($this->quickppr_redirects as $request => $destination) {
				$newWindow 		= isset($this->quickppr_redirectsmeta[$request]['newwindow']) ? (int) $this->quickppr_redirectsmeta[$request]['newwindow'] : 0;
				$noFollow  		= isset($this->quickppr_redirectsmeta[$request]['nofollow']) ? (int) $this->quickppr_redirectsmeta[$request]['nofollow'] : 0;
				$noChecked 		= '';
				$noCheckedAjax 	= '';
				$newChecked 	= '';
				$newCheckedAjax = '';
				if($newWindow == 1){
					$newChecked = ' checked="checked"';
					$newCheckedAjax = 'X';
				}
				if($noFollow == 1){
					$noChecked = ' checked="checked"'; 
					$noCheckedAjax = 'X';
				}
				$output .= '
				<tr id="rowpprdel-'.$ww.'" class="qppr-existing">
					<td class="table-qppr-count"><span class="qppr-count-row">'.($ww + 1).'.</span></td>
					<td class="table-qppr-req"><div class="qppr-request" data-qppr-orig-url="'.esc_attr($request).'">'.esc_attr(urldecode($request)).'</div></td>
					<td class="table-qppr-arr">&nbsp;&raquo;&nbsp;</td>
					<td class="table-qppr-des"><div class="qppr-destination">'.esc_attr(urldecode($destination)).'</div></td>
					<td class="table-qppr-nwn"><div class="qppr-newindow" >'.$newCheckedAjax.'</div></td>
					<td class="table-qppr-nfl"><div class="qppr-nofollow" >'.$noCheckedAjax.'</div></td>
					<td class="table-qppr-edt"><span id="ppredit-'.$ww.'" class="edit-qppr dashicons-edit" data-rowid="rowpprdel-'.$ww.'" title="' . __( 'Edit', 'quick-pagepost-redirect-plugin' ) . '"></span></td>
					<td class="table-qppr-del"><span id="pprdel-'.$ww.'" class="delete-qppr dashicons-trash" data-rowid="rowpprdel-'.$ww.'" title="' . __( 'Delete', 'quick-pagepost-redirect-plugin' ) . '"></span></td>
				</tr>
				';
				$ww++;
			}
		}else{
				$output .= '
				<tr >
					<td colspan="8">' . __( 'No Quick Redirects.', 'quick-pagepost-redirect-plugin' ) . '</td>
				</tr>
				';
		}
		return $output;
	}
	
	function ppr_filter_links ($link = '', $post = array()) {
		global $qppr_setting_links;
		if( $qppr_setting_links)
			return $link;
		if(isset($post->ID)){	
			$id = $post->ID;
		}else{
			$id = $post;
		}
		$newCheck = is_array( $this->ppr_all_redir_array ) ? $this->ppr_all_redir_array : array();
		if( array_key_exists( $id, $newCheck ) ){
			$matchedID = $newCheck[$id];
			if($matchedID['_pprredirect_rewritelink'] == '1' || $this->pproverride_rewrite =='1'){ // if rewrite link is checked or override is set
				if($this->pproverride_URL ==''){
					$newURL = $matchedID['_pprredirect_url'];
				}else{
					$newURL = $this->pproverride_URL;
				} // check override
				if( strpos( $newURL, $this->homelink ) >= 0 || strpos( $newURL, 'www.' ) >= 0 || strpos( $newURL, 'http://' ) >= 0 || strpos( $newURL, 'https://') >= 0 ){
					$link = esc_url( $newURL );
				}else{
					$link = esc_url( $this->homelink.'/'. $newURL );
				}
			}
		}
		return $link;
	}
	
	function ppr_filter_page_links ($link, $post) {
		global $qppr_setting_links;
		if( $qppr_setting_links)
			return $link;
		$id 		= isset( $post->ID ) ? $post->ID : $post;
		$newCheck 	= $this->ppr_all_redir_array;
		if( !is_array( $newCheck ) ){
			$newCheck = array();
		}
		if( array_key_exists( $id, $newCheck ) ){
			$matchedID = $newCheck[$id];
			if($matchedID['_pprredirect_rewritelink'] == '1' || $this->pproverride_rewrite =='1'){ // if rewrite link is checked
				if($this->pproverride_URL ==''){
					$newURL = $matchedID['_pprredirect_url'];
				}else{
					$newURL = $this->pproverride_URL;
				} // check override
				if(strpos($newURL,$this->homelink)>=0 || strpos($newURL,'www.')>=0 || strpos($newURL,'http://')>=0 || strpos($newURL,'https://')>=0){
					$link = esc_url( $newURL );
				}else{
					$link = esc_url( $this->homelink.'/'. $newURL );
				}
			}
		}
		return $link;
	}
	
	function get_main_array(){
		global $wpdb;
		$this->pprptypes_ok	= get_option( 'ppr_qpprptypeok', array() );		
		if( is_array( $this->ppr_all_redir_array ) && ! empty( $this->ppr_all_redir_array ) )
			return $this->ppr_all_redir_array;
		$theArray 	= array();
		$theArrayNW = array();
		$theArrayNF = array();
		$theqsl 	= "SELECT * FROM $wpdb->postmeta a, $wpdb->posts b  WHERE a.`post_id` = b.`ID` AND b.`post_status` != 'trash' AND ( a.`meta_key` = '_pprredirect_active' OR a.`meta_key` = '_pprredirect_rewritelink' OR a.`meta_key` = '_pprredirect_newwindow' OR a.`meta_key` = '_pprredirect_relnofollow' OR a.`meta_key` = '_pprredirect_type' OR a.`meta_key` = '_pprredirect_url') ORDER BY a.`post_id` ASC;";
		$thetemp 	= $wpdb->get_results($theqsl);
		if( count( $thetemp ) > 0 ){
			foreach( $thetemp as $key ){
				$theArray[$key->post_id][$key->meta_key] = $key->meta_value;
			}
			foreach( $thetemp as $key ){
				// defaults
				if(!isset($theArray[$key->post_id]['_pprredirect_rewritelink'])){$theArray[$key->post_id]['_pprredirect_rewritelink']	= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_url'])){$theArray[$key->post_id]['_pprredirect_url']					= '';}
				if(!isset($theArray[$key->post_id]['_pprredirect_type'] )){$theArray[$key->post_id]['_pprredirect_type']				= 302;}
				if(!isset($theArray[$key->post_id]['_pprredirect_relnofollow'])){$theArray[$key->post_id]['_pprredirect_relnofollow']	= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_newwindow'] ))	{$theArray[$key->post_id]['_pprredirect_newwindow']		= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_meta_secs'] ))	{$theArray[$key->post_id]['_pprredirect_meta_secs']		= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_active'] )){$theArray[$key->post_id]['_pprredirect_active']			= 0;}
				if($theArray[$key->post_id]['_pprredirect_newwindow']!= '0' || $this->pproverride_newwin =='1'){
					$theArrayNW[$key->post_id] = get_permalink($key->ID);
				}
				if($theArray[$key->post_id]['_pprredirect_relnofollow']!= '0' || $this->pproverride_nofollow =='1'){
					$theArrayNF[$key->post_id] = get_permalink($key->ID);
				}
			}
		}
		$this->ppr_newwindow = $theArrayNW;
		$this->ppr_nofollow  = $theArrayNF;
		return $theArray;
	}
	
	function get_value($theval='none'){
		return isset($this->$theval) ? $this->$theval : 0;
	}
	
	function ppr_queryhook($vars) {
		$vars[] = 'qppr-file-type';
		return $vars;
	}
	
	function ppr_parse_request_new($wp) {	
		global $wp, $wpdb;
		$this->ppr_all_redir_array	= $this->get_main_array();
		$this->pprptypes_ok	= get_option( 'ppr_qpprptypeok', array() );
		if( current_user_can( 'manage_options' ) ){
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'export-quick-redirects-file' ) {
				$newQPPR_Array = array();
				check_admin_referer( 'export-redirects-qppr' );
				$type = isset( $_GET['qppr-file-type'] ) && sanitize_text_field( $_GET['qppr-file-type'] ) == 'encoded' ? 'encoded' : 'pipe' ; // can be 'encoded' or 'pipe';
				header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); 
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); 
				header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
				header( 'Cache-Control: post-check=0, pre-check=0', false ); 
				header( 'Pragma: no-cache' ); 
				header( "Content-Type: application/force-download" );
				header( "Content-Type: application/octet-stream" );
				header( "Content-Type: application/download" );
				header( "Content-Disposition: attachment; filename=qppr-quick-redirects-export-" . date('U') . ".txt;" );
				$newQPPR_Array['quickppr_redirects'] 		= get_option( 'quickppr_redirects', array() );
				$newQPPR_Array['quickppr_redirects_meta'] 	= get_option( 'quickppr_redirects_meta', array() );
				if( $type == 'encoded' ){
					die( 'QUICKPAGEPOSTREDIRECT' . base64_encode( serialize( $newQPPR_Array ) ) );
				}else{
					if( is_array( $newQPPR_Array ) ){
						$qpprs = $newQPPR_Array['quickppr_redirects'];
						$qpprm = $newQPPR_Array['quickppr_redirects_meta'];
						foreach($qpprs as $key=>$val){
							$nw 	= ( isset( $qpprm[$key]['newwindow'] ) && $qpprm[$key]['newwindow'] == '1' ) ? $qpprm[$key]['newwindow'] : '0' ;
							$nf 	= ( isset( $qpprm[$key]['nofollow'] ) && $qpprm[$key]['nofollow'] == '1' ) ? $qpprm[$key]['nofollow'] : '0' ;
							$temps 	= str_replace( '|', '%7C', $key ) . '|' . str_replace( '|', '%7C', $val ) . '|' . $nw . '|' . $nf;
							if($temps!='|||'){
								$newline[] = $temps;
							}
						}
						$newfile 	= implode( "\r\n", $newline );
					}else{
						$newfile 	= $newtext;
					}
					die( $newfile );				
				}
				exit;
			} elseif( isset( $_POST['import-quick-redrects-file'] ) && isset( $_FILES['qppr_file'] ) ) {
				check_admin_referer( 'import-quick-redrects-file' );
				if ( $_FILES['qppr_file']['error'] > 0 ) {
					wp_die( __( 'An error occured during the file upload. Please fix your server configuration and retry.', 'quick-pagepost-redirect-plugin' ) , __( 'SERVER ERROR - Could Not Load', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
					exit;
				} else {
					$config_file = file_get_contents( $_FILES['qppr_file']['tmp_name'] );
					if ( substr($config_file, 0, strlen('QUICKPAGEPOSTREDIRECT')) !== 'QUICKPAGEPOSTREDIRECT' ) {
						if(strpos($config_file,'|') !== false){
							$delim = '|';
						}elseif(strpos($config_file,',') !== false){
							$delim = ',';
						}elseif(strpos($config_file,"\t") !== false){
							$delim = "\t";
						}else{
							$delim = false;
						}
						if($delim != false){
							$config_file = str_replace("\r\n", "\n", $config_file);  
							$config_file = str_replace("\r", "\n", $config_file);
							$text		 = explode( "\n", $config_file );
							$newfile1 	 = array();
							if( is_array( $text ) && !empty( $text ) ){
								foreach( $text as $nl ){
									if( $nl != '' ){
										$elem 	= explode( $delim, $nl );
										if( isset( $elem[0] ) && isset( $elem[1] ) ){
											$newfile1['quickppr_redirects'][esc_url($elem[0])] = esc_url($elem[1]);
											$nw 	= isset($elem[2]) && $elem[2] == '1' ? '1' : '0';
											$nf 	= isset($elem[3]) && $elem[3] == '1' ? '1' : '0';
											$newfile1['quickppr_redirects_meta'][$elem[0]]['newwindow'] = $nw;
											$newfile1['quickppr_redirects_meta'][$elem[0]]['nofollow'] = $nf;
										}
									}
								}
								if(is_array($newfile1) && !empty( $newfile1 )){
									if( isset( $newfile1['quickppr_redirects'] ) ){
										update_option( 'quickppr_redirects', $newfile1['quickppr_redirects'] );
									}
									if( isset( $newfile1['quickppr_redirects_meta'] ) ){
										update_option( 'quickppr_redirects_meta', $newfile1['quickppr_redirects_meta'] );
									}
								}
							}
							$this->qppr_try_to_clear_cache_plugins();
							wp_redirect( admin_url( 'admin.php?page=redirect-import-export&update=4' ), 302 );
							exit;
						}else{
							wp_die( __( 'This does not look like a Quick Page Post Redirect file - it is possibly damaged or corrupt.', 'quick-pagepost-redirect-plugin' ) , __( 'ERROR - Not a valid File', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
							exit;
						}
					} else {
						$config_file = unserialize(base64_decode(substr($config_file, strlen('QUICKPAGEPOSTREDIRECT'))));
						if ( !is_array( $config_file ) ) {
							wp_die( __( 'This does not look like a Quick Page Post Redirect file - it is possibly damaged or corrupt.', 'quick-pagepost-redirect-plugin' ) , __( 'ERROR - Not a valid File', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
							exit;
						} else {
							$newQPPRRedirects 	= $config_file['quickppr_redirects'];
							$newQPPRMeta 		= $config_file['quickppr_redirects_meta'];
							update_option('quickppr_redirects', $newQPPRRedirects);
							update_option('quickppr_redirects_meta', $newQPPRMeta);
							$this->qppr_try_to_clear_cache_plugins();
							wp_redirect(admin_url('admin.php?page=redirect-import-export&update=4'),302);
						}
					}
				}
			} elseif( isset($_POST['import_redirects_add_qppr']) && isset($_FILES['qppr_file_add']) ) {
				check_admin_referer( 'import_redirects_add_qppr' );
				if ( $_FILES['qppr_file_add']['error'] > 0 ) {
					wp_die(__( 'An error occured during the file upload. It might me that the file is too large or you do not have the premissions to write to the temporary upload directory. Please fix your server configuration and retry.', 'quick-pagepost-redirect-plugin' ) , __( 'SERVER ERROR - Could Not Load', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
					exit;
				} else {
					$config_file = file_get_contents( $_FILES['qppr_file_add']['tmp_name'] );
					if(strpos($config_file,'|') !== false){
						$delim = '|';
					}elseif(strpos($config_file,',') !== false){
						$delim = ',';
					}elseif(strpos($config_file,"\t") !== false){
						$delim = "\t";
					}else{
						$delim = false;
					}
					if ( strpos( $config_file, $delim ) === false ) {
						wp_die( __( 'This does not look like the file is in the correct format - it is possibly damaged or corrupt.<br/>Be sure the redirects are 1 per line and the redirect and destination are seperated by a PIPE (|), COMMA (,) or a TAB.', 'quick-pagepost-redirect-plugin' ) . '<br/>Example:<br/><br/><code>redirect|destination</code>', __( 'ERROR - Not a valid File', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
						exit;
					} else {
						$tempArr	 = array();
						$tempMArr	 = array();
						$config_file = str_replace("\r\n", "\n", $config_file);  
						$config_file = str_replace("\r", "\n", $config_file);
						$QR_Array 	 = explode( "\n", $config_file );
						$newfile1 	 = array();
						if( !empty( $QR_Array ) && is_array( $QR_Array )):
							foreach( $QR_Array as $qrtoadd ):
								if( $qrtoadd != '' && $delim != false && strpos( $qrtoadd, $delim ) !== false ){
									$elem 	= explode( $delim, str_replace( array( "\r", "\n" ), array( '', '' ), $qrtoadd ) );	
									if( isset( $elem[0] ) && isset( $elem[1] ) ){
										$newfile1['quickppr_redirects'][esc_url($elem[0])] = esc_url($elem[1]);
										$nw 	= isset($elem[2]) && $elem[2] == '1' ? '1' : '0';
										$nf 	= isset($elem[3]) && $elem[3] == '1' ? '1' : '0';
										$newfile1['quickppr_redirects_meta'][$elem[0]]['newwindow'] = $nw;
										$newfile1['quickppr_redirects_meta'][$elem[0]]['nofollow'] = $nf;
									}
								}
							endforeach;	
							if(is_array($newfile1) && !empty( $newfile1 )){
								if( isset( $newfile1['quickppr_redirects'] ) ){
									$currQRs 	= get_option( 'quickppr_redirects', array() );
									$resultQRs 	= array_replace($currQRs, $newfile1['quickppr_redirects']);
									update_option( 'quickppr_redirects', $resultQRs );
								}
								if( isset( $newfile1['quickppr_redirects_meta'] ) ){
									$currQRM 	= get_option( 'quickppr_redirects_meta', array() );
									$resultQRMs = array_replace($currQRM, $newfile1['quickppr_redirects_meta']);
									update_option( 'quickppr_redirects_meta', $resultQRMs );
								}
							}
							$this->qppr_try_to_clear_cache_plugins();
							wp_redirect(admin_url('admin.php?page=redirect-import-export&update=5'),302);
							exit;
						else:
							wp_die( __( 'It does not look like there are any valid items to import - check the file and try again.', 'quick-pagepost-redirect-plugin' ) , __( 'ERROR - No Valid items to add.', 'quick-pagepost-redirect-plugin' ), array( 'response' => '200', 'back_link' => '1' ) );
							exit;
						endif;
					}
				}
			}		return;
		}	return;
	}
		
	function qppr_pprhidemessage_ajax(){
		check_ajax_referer( 'qppr_ajax_verify', 'scid', true );
		$msg = isset($_POST['pprhidemessage']) ? (int)$_POST['pprhidemessage'] : 0;
		if($msg == 1){
			update_option('qppr_jQuery_hide_message','1');
			echo '1';
		}elseif($msg == 2){
			update_option('qppr_jQuery_hide_message2','1');
			echo '1';
		}else{
			echo '0';	
		}
		exit;
	}

	function ppr_init_check_version() {
	// checks version of plugin in DB and updates if needed.
		global $wpdb;
		//$this->pprptypes_ok	= get_option( 'ppr_qpprptypeok', array() );		
		if( is_array( $this->ppr_all_redir_array ) && ! empty( $this->ppr_all_redir_array ) )
			$this->ppr_all_redir_array = $this->get_main_array();

		if ( version_compare( $this->thepprversion, $this->ppr_curr_version, '<' ) && version_compare( $this->ppr_curr_version, '5.1.1', '<' )  ){
			$metaMsg 	= get_option( 'ppr_meta-message', 'not-set' );
			$metaMsgNew = get_option( 'qppr_meta_addon_content', 'not-set' );
			if( $metaMsgNew == 'not-set' && $metaMsg != 'not-set' ){
				update_option( 'qppr_meta_addon_content', $metaMsg );
				$this->pprmeta_message = $metaMsg; 
			}
			$metaSec 	= get_option( 'ppr_meta-seconds', 'not-set' );
			$metaSecNew = get_option( 'qppr_meta_addon_sec', 'not-set');
			if( $metaSecNew == 'not-set' && $metaSec != 'not-set' ){
				update_option( 'qppr_meta_addon_sec', $metaSec );
				$this->pprmeta_seconds	= $metaSec;
			}
			if( $this->thepprversion == '5.0.7' ){
				update_option( 'ppr_use-jquery','1'); //default to on
				update_option( 'ppr_show-columns','1'); //default to on
			}elseif( $this->thepprversion != '5.1.0' ){
				if ( get_option( 'ppr_override-casesensitive' , 'not-set' ) == 'not-set' )
					update_option( 'ppr_override-casesensitive', '1' );
				$this->ppruse_jquery 	= '0';
				$this->pproverride_casesensitive = '1';
			}
			update_option( 'ppr_version', $this->ppr_curr_version );
		}elseif( version_compare( $this->thepprversion, $this->ppr_curr_version, '<' ) ){
			update_option( 'ppr_version', $this->ppr_curr_version );
		}
		
		if( $this->thepprmeta != '1' && version_compare( $this->ppr_curr_version, '5.0.7', '<' )){
			update_option( 'ppr_meta_clean', '1' );
			$wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = CONCAT('_',`meta_key`) WHERE `meta_key` = 'pprredirect_active' OR `meta_key` = 'pprredirect_rewritelink' OR `meta_key` = 'pprredirect_newwindow' OR `meta_key` = 'pprredirect_relnofollow' OR `meta_key` = 'pprredirect_type' OR `meta_key` = 'pprredirect_url';");
		}
	}

	function ppr_filter_plugin_actions($links){
		$links[] 	= '<a href="'.$this->adminlink.'admin.php?page=redirect-options"><span class="dashicons dashicons-admin-settings"></span> ' . __( 'Settings', 'quick-pagepost-redirect-plugin' ) . '</a>';
		return $links;
	}
	
	function ppr_filter_plugin_links($links, $file){
		if ( $file == plugin_basename(__FILE__) ){
			$links[] = '<a href="'.$this->adminlink.'admin.php?page=redirect-updates"><span class="dashicons dashicons-external"></span> ' . __( 'Quick Redirects', 'quick-pagepost-redirect-plugin' ) . '</a>';
			$links[] = '<a href="'.$this->adminlink.'admin.php?page=redirect-faqs"><span class="dashicons dashicons-editor-help"></span> ' . __( 'FAQ', 'quick-pagepost-redirect-plugin' ) . '</a>';
			$links[] = '<a target="_blank" href="'.$this->fcmlink.'/donations/"><span class="dashicons dashicons-heart"></span> ' . __( 'Donate', 'quick-pagepost-redirect-plugin' ) . '</a>';
		}
		return $links;
	}
	
	function edit_box_ppr_1() {
	// Prints the inner fields for the custom post/page section 
		global $post;
		$ppr_option1='';
		$ppr_option2='';
		$ppr_option3='';
		$ppr_option4='';
		$ppr_option5='';
		// Use nonce for verification ... ONLY USE ONCE!
		wp_nonce_field( 'pprredirect_noncename', 'pprredirect_noncename', false, true );
		// The actual fields for data entry
		$pprredirecttype = get_post_meta($post->ID, '_pprredirect_type', true) !='' ? get_post_meta($post->ID, '_pprredirect_type', true) : "";
		$pprredirecturl =  get_post_meta($post->ID, '_pprredirect_url', true)!='' ? get_post_meta($post->ID, '_pprredirect_url', true) : "";
		echo '<label for="pprredirect_active" style="padding:2px 0;"><input type="checkbox" name="pprredirect_active" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_active',true),0).' />&nbsp;' . __( 'Make Redirect <strong>Active</strong>.', 'quick-pagepost-redirect-plugin' ) . '<span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'Check to turn on or redirect will not work.', 'quick-pagepost-redirect-plugin' ) . '</span></span></label><br />';
		echo '<label for="pprredirect_newwindow" style="padding:2px 0;"><input type="checkbox" name="pprredirect_newwindow" id="pprredirect_newwindow" value="_blank" '. checked('_blank',get_post_meta($post->ID,'_pprredirect_newwindow',true),0).'>&nbsp;' . __( 'Open in a <strong>new window.</strong>', 'quick-pagepost-redirect-plugin' ) . '<span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'To increase effectivness, select "Use jQuery" in the options.', 'quick-pagepost-redirect-plugin' ) . '</span></span></label><br />';
		echo '<label for="pprredirect_relnofollow" style="padding:2px 0;"><input type="checkbox" name="pprredirect_relnofollow" id="pprredirect_relnofollow" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_relnofollow',true),0).'>&nbsp;' . __( 'Add <strong>rel="nofollow"</strong> to link.', 'quick-pagepost-redirect-plugin' ) . '<span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'To increase effectivness, select "Use jQuery" in the options.', 'quick-pagepost-redirect-plugin' ) . '</span></span></label><br />';
		echo '<label for="pprredirect_rewritelink" style="padding:2px 0;"><input type="checkbox" name="pprredirect_rewritelink" id="pprredirect_rewritelink" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_rewritelink',true),0).'>&nbsp;' . __( '<strong>Show</strong> Redirect URL in link.', 'quick-pagepost-redirect-plugin' ) . ' <span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'To increase effectivness, select "Use jQuery" in the options. This will only change the URL in the link <strong>NOT</strong> the URL in the Address bar.', 'quick-pagepost-redirect-plugin' ) . '</span></span></label><br /><br />';
		//echo '<label for="pprredirect_casesensitive" style="padding:2px 0;"><input type="checkbox" name="pprredirect_casesensitive" id="pprredirect_casesensitive" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_casesensitive',true),0).'>&nbsp;Make the Redirect Case Insensitive.</label><br /><br />';
		echo '<label for="pprredirect_url"><b>' . __( 'Redirect / Destination URL:', 'quick-pagepost-redirect-plugin' ) . '</b></label><br />';
		echo '<input type="text" style="width:75%;margin-top:2px;margin-bottom:2px;" name="pprredirect_url" value="'.$pprredirecturl.'" /><span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help"><br />' . __( '(i.e., <strong>http://example.com</strong> or <strong>/somepage/</strong> or <strong>p=15</strong> or <strong>155</strong>. Use <b>FULL URL</b> <i>including</i> <strong>http://</strong> for all external <i>and</i> meta redirects.)', 'quick-pagepost-redirect-plugin' ) . '</span></span><br /><br />';
		echo '<label for="pprredirect_type"><b>' . __( 'Type of Redirect:', 'quick-pagepost-redirect-plugin' ) . '</b></label><br />';
		
		switch($pprredirecttype):
			case "":
				$ppr_option1=" selected"; //default is 301 (as of 5.1.1)
				break;
			case "301":
				$ppr_option1=" selected";
				break;
			case "302":
				$ppr_option2=" selected";
				break;
			case "307":
				$ppr_option3=" selected";
				break;
			case "meta":
				$ppr_option5=" selected";
				break;
		endswitch;
		
		echo '
		<select style="margin-top:2px;margin-bottom:2px;width:40%;" name="pprredirect_type" id="pprredirect_type">
		<option value="301" '.$ppr_option1.'>301 ' . __( 'Permanent', 'quick-pagepost-redirect-plugin' ) . '</option>
		<option value="302" '.$ppr_option2.'>302 ' . __( 'Temporary', 'quick-pagepost-redirect-plugin' ) . '</option>
		<option value="307" '.$ppr_option3.'>307 ' . __( 'Temporary', 'quick-pagepost-redirect-plugin' ) . '</option>
		<option value="meta" '.$ppr_option5.'>' . __( 'Meta Redirect', 'quick-pagepost-redirect-plugin' ) . '</option>
		</select><span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'Default is 301 (Permanent Redirect).', 'quick-pagepost-redirect-plugin' ) . ' </span></span><br /><br />
		';
		$metasel = ' meta-not-selected';
		if( $ppr_option5 == ' selected' )
			$metasel = ' meta-selected';
			
		echo '<div class="qppr-meta-section-wrapper'.$metasel.'">';
		echo '	<label for="pprredirect_meta_secs" style="padding:2px 0;"><strong>' . __( 'Redirect Seconds (ONLY for meta redirects).', 'quick-pagepost-redirect-plugin' ) . '</strong></label><br /><input type="text" name="pprredirect_meta_secs" id="pprredirect_meta_secs" value="'. (get_post_meta($post->ID,'_pprredirect_meta_secs',true) != '' ? get_post_meta($post->ID,'_pprredirect_meta_secs',true ): '' ).'" size="3"><span class="qppr_meta_help_wrap"><span class="qppr_meta_help_icon dashicons dashicons-editor-help"></span><span class="qppr_meta_help">' . __( 'Leave blank to use options setting. 0 = instant.', 'quick-pagepost-redirect-plugin' ) . ' </span></span><br /><br />';
		echo '</div>';
		echo __( '<strong>NOTE:</strong> For a Page or Post (or Custom Post) Redirect to work, it may need to be published first and then saved again as a Draft. If you do not already have a page/post created you can add a \'Quick\' redirect using the', 'quick-pagepost-redirect-plugin' ) . ' <a href="./admin.php?page=redirect-updates">' . __( 'Quick Redirects', 'quick-pagepost-redirect-plugin' ) . '</a> ' . __( 'method.', 'quick-pagepost-redirect-plugin' );
	}
	
	function isOne_none($val=''){ //true (1) or false =''
		if($val == '_blank'){
			return $val;
		}elseif($val == '1' || $val == 'true' || $val === true ){
			return 1;
		}
		return '';
	}
	
	function ppr_save_metadata($post_id, $post) {
		if($post->post_type == 'revision' || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
			return;
		// verify authorization
		if( isset( $_POST['pprredirect_noncename'] ) ){
			if ( !wp_verify_nonce( $_REQUEST['pprredirect_noncename'], 'pprredirect_noncename' ) )
				return $post_id;
		}
		// check allowed to editing
		if ( !current_user_can('edit_posts', $post_id))
			return $post_id;
			
		if(!empty($my_meta_data))
			unset($my_meta_data);
			
		$my_meta_data = array();
		if( isset( $_POST['pprredirect_active'] ) || isset( $_POST['pprredirect_url'] ) || isset( $_POST['pprredirect_type'] ) || isset( $_POST['pprredirect_newwindow'] ) || isset($_POST['pprredirect_relnofollow']) || isset($_POST['pprredirect_meta_secs'])):
			$protocols 		= apply_filters( 'qppr_allowed_protocols', array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
			// find & save the form data & put it into an array
			$my_meta_data['_pprredirect_active'] 		= isset($_REQUEST['pprredirect_active']) 		? sanitize_meta( '_pprredirect_active', $this->isOne_none(intval( $_REQUEST['pprredirect_active'])), 'post' ) : '';
			$my_meta_data['_pprredirect_newwindow'] 	= isset($_REQUEST['pprredirect_newwindow']) 	? sanitize_meta( '_pprredirect_newwindow', $this->isOne_none( $_REQUEST['pprredirect_newwindow']), 'post' ) 	: '';
			$my_meta_data['_pprredirect_relnofollow'] 	= isset($_REQUEST['pprredirect_relnofollow']) 	? sanitize_meta( '_pprredirect_relnofollow', $this->isOne_none(intval( $_REQUEST['pprredirect_relnofollow'])), 'post' ) 	: '';
			$my_meta_data['_pprredirect_type'] 			= isset($_REQUEST['pprredirect_type']) 			? sanitize_meta( '_pprredirect_type', sanitize_text_field( $_REQUEST['pprredirect_type'] ), 'post' )		: '';
			$my_meta_data['_pprredirect_rewritelink'] 	= isset($_REQUEST['pprredirect_rewritelink']) 	? sanitize_meta( '_pprredirect_rewritelink', $this->isOne_none(intval( $_REQUEST['pprredirect_rewritelink'])), 'post' )	: '';
			$my_meta_data['_pprredirect_url']    		= isset($_REQUEST['pprredirect_url']) 			? esc_url_raw( $_REQUEST['pprredirect_url'], $protocols ) : ''; 
			$my_meta_data['_pprredirect_meta_secs']    	= isset($_REQUEST['pprredirect_meta_secs']) &&  $_REQUEST['pprredirect_meta_secs'] != '' ? (int) $_REQUEST['pprredirect_meta_secs'] : ''; 

			$info = $this->appip_parseURI($my_meta_data['_pprredirect_url']);
			//$my_meta_data['_pprredirect_url'] = esc_url_raw($info['url']);
			$my_meta_data['_pprredirect_url'] = $info['url'];

			if($my_meta_data['_pprredirect_url'] == 'http://' || $my_meta_data['_pprredirect_url'] == 'https://' || $my_meta_data['_pprredirect_url'] == ''){
				$my_meta_data['_pprredirect_url'] 		= ''; //reset to nothing
				$my_meta_data['_pprredirect_type'] 		= NULL; //clear Type if no URL is set.
				$my_meta_data['_pprredirect_active'] 	= NULL; //turn it off if no URL is set
				$my_meta_data['_pprredirect_rewritelink'] = NULL;  //turn it off if no URL is set
				$my_meta_data['_pprredirect_newwindow']	= NULL; //turn it off if no URL is set
				$my_meta_data['_pprredirect_relnofollow'] = NULL; //turn it off if no URL is set
			}
			
			// Add values of $my_meta_data as custom fields
			if(count($my_meta_data)>0){
				foreach ($my_meta_data as $key => $value) { 
					$value = implode(',', (array)$value);
					if($value == '' || $value == NULL || $value == ','){ 
						delete_post_meta($post->ID, $key); 
					}else{
						if(get_post_meta($post->ID, $key, true) != '') {
							update_post_meta($post->ID, $key, $value);
						} else { 
							add_post_meta($post->ID, $key, $value);
						}
					}
				}
			}
			$this->qppr_try_to_clear_cache_plugins();
		endif;
	}
	
	function appip_parseURI($url){
		/*
		[scheme]
		[host]
		[user]
		[pass]
		[path]
		[query]
		[fragment]
		*/
		$strip_protocol = 0;
		$tostrip = '';
		if(substr($url,0,2) == 'p=' || substr($url,0,8) == 'page_id='){ 
			// page or post id
			$url = network_site_url().'/?'.$url;
		}elseif(is_numeric($url)){ 
			// page or post id
			$url = network_site_url().'/?'.$url;
		}elseif($url == "/" ){ 
			// root
			$url = network_site_url().'/';
		}elseif(substr($url,0,1) == '/' ){ 
			// relative to root
			$url =  network_site_url().$url;
			$strip_protocol = 1;
			$tostrip = network_site_url(); 
		}elseif(substr($url,0,7) != 'http://' && substr($url,0,8) != 'https://' ){ 
			//no protocol so add it
			//NOTE: desided not to add it automatically - too iffy.
		}
		$info = @parse_url($url);
		if($strip_protocol == 1 && $tostrip != '' ){
			$info['url'] = str_replace($tostrip, '', $url);
		}else{
			$info['url'] = $url;
		}
		return $info;
	}
	
	function ppr_fix_targetsandrels($pages) {
		$ppr_url 		= array();
		$ppr_newindow 	= array();
		$ppr_nofollow 	= array();
		
		if (empty($ppr_url) && empty($ppr_newindow) && empty($ppr_nofollow)){
			$thefirstppr = array();
			if(!empty($this->ppr_all_redir_array)){
				foreach($this->ppr_all_redir_array as $key => $pprd){
					foreach($pprd as $ppkey => $pprs){
						$thefirstppr[$key][$ppkey] = $pprs;
						$thefirstppr[$key]['post_id'] = $key;

					}
				}
			}
			if(!empty($thefirstppr)){
				foreach($thefirstppr as $ppitems){
					if($ppitems['_pprredirect_active'] == 1 && $this->pproverride_newwin =='1'){ 
						// check override of NEW WINDOW
						$ppr_newindow[] = $ppitems['post_id'];
					}else{
						if($ppitems['_pprredirect_active'] == 1 && $ppitems['_pprredirect_newwindow'] === '_blank'){
							$ppr_newindow[] = $ppitems['post_id'];
						}
					}
					
					if($ppitems['_pprredirect_active']==1 && $this->pproverride_nofollow =='1'){ 
						//check override of NO FOLLOW
						$ppr_nofollow[] = $ppitems['post_id'];
					}else{
						if($ppitems['_pprredirect_active']==1 && $ppitems['_pprredirect_relnofollow'] == 1){
							$ppr_nofollow[] = $ppitems['post_id'];
						}
					}
					
					if($ppitems['_pprredirect_active']==1 && $this->pproverride_rewrite =='1'){ 
						//check override of REWRITE
						if($this->pproverride_URL!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $this->pproverride_URL; //check override of URL
						}elseif($ppitems['_pprredirect_url']!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $ppitems['_pprredirect_url'];
						}
					}else{
						if($ppitems['_pprredirect_active']==1 && $ppitems['_pprredirect_rewritelink'] == '1' && $ppitems['_pprredirect_url']!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $ppitems['_pprredirect_url'];
						}
					}
				}
			}
			if (count($ppr_newindow)<0 && count($ppr_nofollow)<0){
				return $pages;
			}
		}
		
		//$this_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if(count($ppr_nofollow)>=1) {
			foreach($ppr_nofollow as $relid){
			$validexp="@\<li(?:.*?)".$relid."(?:.*?)\>\<a(?:.*?)rel\=\"nofollow\"(?:.*?)\>@i";
			$found = preg_match_all($validexp, $pages, $matches);
				if($found!=0){
					$pages = $pages; //do nothing 'cause it is already a rel=nofollow.
				}else{
					$pages = preg_replace('@<li(.*?)-'.$relid.'(.*?)\>\<a(.*?)\>@i', '<li\1-'.$relid.'\2><a\3 rel="nofollow">', $pages);
				}
			}
		}
		
		if(count($ppr_newindow)>=1) {
			foreach($ppr_newindow as $p){
				$validexp="@\<li(?:.*?)".$p."(?:.*?)\>\<a(?:.*?)target\=(?:.*?)\>@i";
				$found = preg_match_all($validexp, $pages, $matches);
				if($found!=0){
					$pages = $pages; //do nothing 'cause it is already a target=_blank.
				}else{
					$pages = preg_replace('@<li(.*?)-'.$p.'(.*?)\>\<a(.*?)\>@i', '<li\1-'.$p.'\2><a\3 target="_blank">', $pages);
				}
			}
		}
		return $pages;
	}
	
	function redirect_post_type(){
		return;
		//not needed at this time
	}
	
	function getAddress($home = ''){
	// utility function to get the full address of the current request - credit: http://www.phpro.org/examples/Get-Full-URL.html
		if( !isset( $_SERVER['HTTPS'] ) ){
			$_SERVER['HTTPS'] = '';
		}
		$protocol = $_SERVER['HTTPS'] !== '' && strpos( $home, 'http:' ) === false ? 'https' : 'http'; //check for https
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //return the full address
	}
	
	function getQAddress($home = ''){
	// utility function to get the protocol and host of the current request
		if( !isset( $_SERVER['HTTPS'] ) )
			$_SERVER['HTTPS'] = '';
		$protocol = $_SERVER['HTTPS'] !== '' && strpos( $home, 'http:' ) === false ? 'https' : 'http'; //check for https
		return $protocol.'://'.$_SERVER['HTTP_HOST']; 
	}
	
	function ppr_new_nav_menu_fix($ppr){
		$newmenu = array();
		if(!empty($ppr)){
			foreach($ppr as $ppd){
				if(isset($this->ppr_all_redir_array[$ppd->object_id])){
					$theIsActives 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_active'];
					$theNewWindow 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_newwindow'];
					$theNoFollow 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_relnofollow'];
					$theRewrite 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_rewritelink'];
					$theRedURL	 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_url'];
					if($this->pproverride_URL !=''){$theRedURL = $this->pproverride_URL;} // check override
					if($theIsActives == '1' && $theNewWindow === '_blank'){
						$ppd->target = '_blank';
						$ppd->classes[] = 'ppr-new-window';
					}
					if($theIsActives == '1' && $theNoFollow == '1'){
						$ppd->xfn = 'nofollow';
						$ppd->classes[] = 'ppr-nofollow';
					}
					if($theIsActives == '1' && $theRewrite == '1' && $theRedURL != ''){
						$ppd->url = $theRedURL;
						$ppd->classes[] = 'ppr-rewrite';
					}
				}
				$newmenu[] = $ppd;
			}
		}
		return $newmenu;
	}
	
	function redirect(){
		//bypass for testing.
		if(isset($_GET['action']) && $_GET['action'] == 'no-redirect' )
			return;
		// Quick Redirects Redirect.
		// Read the list of redirects and if the current page is found in the list, send the visitor on her way
		if (!empty($this->quickppr_redirects) && !is_admin()) {
			$homeURL 		= get_option( 'home' );
			$getAddress		= $this->getAddress( $homeURL ); // gets just the protocol and full URL of request. for cases when the setting for Site URL has a subfolder but a request may not.
			$getQAddress	= $this->getQAddress( $homeURL ); // gets just the protocol and domain (host) of the request.
			
			//get the query string if there is one so that it can be preserved
				// patch submitted for version 5.0.7 by Romulo De Lazzari <romulodelazzari@gmail.com> - THANKS!
				$finalQS = (filter_input(INPUT_SERVER, 'QUERY_STRING'));
				if ($finalQS === null || $finalQS === false || $finalQS === '') {
					$finalQS = '';
				} else {
					$finalQS = '?' . $finalQS;
				}
				$userrequest = str_replace( $homeURL, '', $getAddress );
				$userrequest = preg_replace('/\?.*/', '', $userrequest);
				// end patch				
			//end QS preservation

			$needle 		= $this->pproverride_casesensitive ? $userrequest : strtolower($userrequest);
			$haystack 		= $this->pproverride_casesensitive ? $this->quickppr_redirects : array_change_key_case($this->quickppr_redirects);
			$getAddrNeedle 	= $this->pproverride_casesensitive ? $getAddress : strtolower($getAddress);
			$getQAddrNeedle = $this->pproverride_casesensitive ? str_replace( $getQAddress, '', $getAddrNeedle ) : strtolower(str_replace( $getQAddress, '', $getAddrNeedle ));
			$finalQS 		= str_replace( '&amp;','&', $finalQS);
			$finalQS		= $this->pproverride_casesensitive ? $finalQS : strtolower( $finalQS ); //added 5.1.4 to fix URL needle being converted to lower, but not Query (as it never matches unless user enters lower)
			$finalQS 		= apply_filters( 'appip_filter_testing_finalQS', $finalQS, $needle, $haystack); // added 5.1.4 to allow filtering of QS data prior to matching.
			$index 			= false;
			
			/* These are the URL matching checks to see if the request should be redirected.
			 * They trickle down to the less likely scenarios last - tries to recover a redirect if the 
			 * user just forgot things like ending slash or used wrong protocol, etc.
			 */

			if( array_key_exists( ($needle . $finalQS), $haystack ) ){ 
				//check if QS data might be part of the redirect URL and not supposed to be added back.
				$index = $needle . $finalQS;
				$finalQS = ''; //remove it
			}elseif( array_key_exists( urldecode($needle . $finalQS), $haystack ) ){ 
				//check if QS data might be part of the encoded redirect URL and not supposed to be added back.
				$index = $needle . $finalQS;
				$finalQS = ''; //remove it
			}elseif(array_key_exists( $needle, $haystack)){
				//standard straight forward check for needle (request URL)
				$index = $needle;
			}elseif(array_key_exists(urldecode($needle), $haystack)){
				//standard straight forward check for URL encoded needle (request URL)
				$index = urldecode($needle);
			}elseif(array_key_exists( $getAddrNeedle, $haystack)){
				//Checks of the needle (request URL) might be using a different protocol than site home URL
				$index = $getAddrNeedle;
			}elseif(array_key_exists( urldecode( $getAddrNeedle ), $haystack)){
				//Checks of an encoded needle (request URL) might be using a different protocol than site home URL
				$index =  urldecode( $getAddrNeedle );
			}elseif( strpos( $needle, 'https' ) !== false ){
				//Checks of the encoded needle (request URL) might be http but the redirect is set up as http
				if(array_key_exists(str_replace('https','http',$needle), $haystack))
					$index = str_replace('https','http',$needle); //unencoded version
				elseif(array_key_exists(str_replace('https','http',urldecode($needle)), $haystack))
					$index = str_replace('https','http',urldecode($needle)); //encoded version
			}elseif(strpos($needle,'/') === false) {
				//Checks of the needle (request URL) might not have beginning and ending / but the redirect is set up with them
				if( array_key_exists( '/' . $needle . '/', $haystack ) )
					$index = '/'.$needle.'/';
			}elseif( array_key_exists( urldecode($getQAddrNeedle), $haystack ) ){
				//Checks if encoded needle (request URL) doesn't contain a sub directory in the URL, but the site Root is set to include it.
				$index = urldecode( $getQAddrNeedle );
			}elseif( array_key_exists( $getQAddrNeedle, $haystack ) ){
				//Checks if needle (request URL) doesn't contain a sub directory in the URL, but the site Root is set to include it.
				$index = $getQAddrNeedle;
			}elseif( array_key_exists( $needle . '/', $haystack ) ){
				//checks if needle (request URL) just is missing the ending / but the redirect is set up with it.
				$index = $needle . '/';
			}
			$index = apply_filters('qppr_filter_quickredirect_index', $index, $finalQS);
			
			if($index != false && $index != ''){
				// Finally, if we have a matched request URL, get ready to redirect.
				$val = isset($haystack[$index]) ? $haystack[$index] : false;				
				if($val) {
					// if global setting to make all redirects go to a specific URL is set, that takes priority.
					$useURL 	 = $this->pproverride_URL != '' ? $this->pproverride_URL : $val;
					$useURL 	.= apply_filters( 'qppr_filter_quickredirect_append_QS_data', $finalQS ); //add QS back or use filter to set to blank.
					$useURL 	 = apply_filters( 'qppr_filter_quickredirect_url', $useURL, $index ); // final URL filter
					
					$qpprRedType = apply_filters( 'qppr_filter_quickredirect_type', 301 ) ; // filter for redirect type (301 is default here).
					$qpprMetaSec = apply_filters( 'qppr_filter_quickredirect_secs', $this->pprmeta_seconds ) ; // filter for redirect seconds if type is changed to meta).
					if( strpos( $useURL, '/' ) !== false && strpos( $useURL, '/' ) === 0 ){
						// $addback refers to adding back the site home link back to the front of the request URL that is relative to the root. 
						// by default it will, but this can be filtered to never add it back (or based on URL).
						$addback 	= (bool) apply_filters( 'qppr_filter_quickredirect_add_home_link_to_destination_url', true, $useURL);
						$useURL = $addback ? $homeURL . $useURL : $useURL;
					}
					// action to allow take over.
					do_action( 'qppr_redirect', $useURL, $qpprRedType );
					
					if( $useURL != '' ){
						// and now the redirect (meta or type set).
						if( $qpprRedType == 'meta' ){
							$this->ppr_metaurl = $useURL;
							$this->ppr_addmetatohead_theme();
						}else{
							header('RedirectType: Quick Page Post Redirect - Quick');
							wp_redirect( $useURL, $qpprRedType );
							exit();
						}
					}
				}	
			}
		}
	}
	
	function ppr_do_redirect( $var1='var1', $var2 = 'var2'){
		//bypass for testing.
		if(isset($_GET['action']) && $_GET['action'] == 'no-redirect' )
			return;
		// Individual Redirects Redirect.
		// Read the list of redirects and if the current page is found in the list, send the visitor on her way
		
		global $post;
		if ( count( $this->ppr_all_redir_array ) > 0 && ( is_single() || is_singular() || is_page() ) ) {
			if( isset( $this->ppr_all_redir_array[$post->ID] ) ){
				$isactive = $this->ppr_all_redir_array[$post->ID]['_pprredirect_active'];
				$redrtype = $this->ppr_all_redir_array[$post->ID]['_pprredirect_type'];
				$redrurl  = $this->ppr_all_redir_array[$post->ID]['_pprredirect_url'];
				$metasecs = $this->ppr_all_redir_array[$post->ID]['_pprredirect_meta_secs'];
				if($isactive == 1 && $redrurl != '' && $redrurl != 'http://www.example.com'){
					if($redrtype === 0){$redrtype = '200';}
					if($redrtype === ''){$redrtype = '302';}
					if( strpos($redrurl, 'http://')=== 0 || strpos($redrurl, 'https://')=== 0){
						$urlsite	= $redrurl;
					}elseif(strpos($redrurl, 'www')=== 0){ //check if they have full url but did not put http://
						$urlsite 	= 'http://'.$redrurl;
					}elseif(is_numeric($redrurl)){ // page/post number
						$urlsite	= $this->homelink.'/?p='.$redrurl;
					}elseif(strpos($redrurl,'/') === 0){ // relative to root	
						$urlsite 	= $this->homelink.$redrurl;
					}else{	// we assume they are using the permalink / page name??
						$urlsite=$this->homelink.'/'.$redrurl;
					}
					// check if override is set for all redirects to go to one URL
					if($this->pproverride_URL !=''){$urlsite=$this->pproverride_URL;} 
					if($this->pproverride_type!='0' && $this->pproverride_type!=''){$redrtype = $this->pproverride_type;} //override check
					if($redrtype == 'meta'){
						$this->ppr_metaurl = $redrurl;
						$post_meta_secs = get_post_meta( $post->ID, '_pprredirect_meta_secs', true);
						$this->ppr_addmetatohead_theme();
						//$this->add_extra_meta_features( $redrurl, $metasecs, 'individual', $post );
					}else{
						header('RedirectType: Quick Page Post Redirect - Individual');
						do_action('qppr_do_redirect',$urlsite,$this->pproverride_type);
						wp_redirect($urlsite,$redrtype);
						exit();
					}
				}
			}
		}
	}

	function ppr_addmetatohead_theme(){
		$themsgmeta = '';
		$themsgmsg 	= '';
		$hook_name 	= 'ppr_meta_head_hook';
		// check URL override
	    if($this->pproverride_URL !=''){
			$urlsite = $this->pproverride_URL;
		} else {
			$urlsite = $this->ppr_metaurl;
		}
	    $this->pproverride_URL = ''; //reset
	    if($this->pprmeta_seconds==''){
			$this->pprmeta_seconds='0';
		}
		$themsgmeta =  '<meta http-equiv="refresh" content="'.$this->pprmeta_seconds.'; URL='.$urlsite.'" />'."\n";
		if($this->pprmeta_message!='' && $this->pprmeta_seconds!='0'){
			$themsgmsg =  '<div style="margin-top:20px;text-align:center;">'.$this->pprmeta_message.'</div>'."\n";
		}
		if( has_action($hook_name)){
			do_action( $hook_name,$urlsite,$this->pprmeta_seconds,$this->pprmeta_message);
			return;
		}elseif( has_filter($hook_name.'_filter')){
			$themsgmeta = apply_filters($hook_name, $themsgmeta,$themsgmsg);
			echo $themsgmeta;
			return;
		}else{
			echo $themsgmeta;
			echo $themsgmsg;
			exit;
		}
	}

	function override_ppr_metahead( $refresh_url = '', $refresh_secs = 0, $messages = ''){
		global $post;
		global $is_IE;
		$messages 	= '';
		$outHTML   	= array();
		$psecs 		= '';
		$ptrigger	= '';
		$pload		= '';
		$pcontent	= '';
		$appMsgTo	= 'body';
		if( is_object( $post ) && !empty( $post )){
			$psecs 		= get_post_meta($post->ID, '_pprredirect_meta_secs', true);	
			$ptrigger	= get_post_meta($post->ID, 'qppr_meta_trigger', true) != '' ? get_post_meta($post->ID, 'qppr_meta_trigger', true) : '';
			$pload		= (bool) get_post_meta($post->ID, 'qppr_meta_load', true) === true ? '1' : '';
			$pcontent	= get_post_meta($post->ID, 'qppr_meta_content', true) != '' ? get_post_meta($post->ID, 'qppr_meta_content', true) : '';
			$appMsgTo	= get_post_meta($post->ID, 'qppr_meta_append', true) != '' ? get_post_meta($post->ID, 'qppr_meta_append', true) : '';
		}
		$secs			= $psecs != '' ? $psecs : get_option( 'qppr_meta_addon_sec', $refresh_secs );
		$class			= $ptrigger != '' ? $ptrigger : get_option( 'qppr_meta_addon_trigger', 'body' );
		$load			= $pload != '' ? true : ( get_option( 'qppr_meta_addon_load', '' ) != '' ? true : false);
		$content		= $pcontent != '' ? $pcontent : get_option( 'qppr_meta_addon_content', $this->pprmeta_message );
		$timer 			= (int) $secs * 100;
		$appendTo		= $appMsgTo != '' ? $appMsgTo : get_option( 'qppr_meta_append_to', 'body' );
		$injectMsg		= $content != '' ? '<div id="ppr_custom_message">'.$content.'</div>' : '';
		$bfamily 		= qppr_get_browser_family();
		if( !$load ) {
			//wp_enqueue_script( 'qppr-meta-redirect-no-load', plugins_url( '/js/qppr_meta_redirect.js', __FILE__ ), array( 'jquery' ), $this->ppr_curr_version, false );
			wp_enqueue_script( 'qppr-meta-redirect-no-load', plugins_url( '/js/qppr_meta_redirect.min.js', __FILE__ ), array( 'jquery' ), $this->ppr_curr_version, false );
			wp_localize_script( 'qppr-meta-redirect-no-load', 'qpprMetaData', array( 'browserFamily' => $bfamily,'appendTo' => $appendTo, 'class' => $class, 'secs' => $secs, 'refreshURL' => $refresh_url , 'injectMsg' => $injectMsg ) );
			echo '<!DOCTYPE html>'."\n";
			echo '<html>'."\n";
			echo '<head>'."\n";
			global $wp_scripts;
			$allowScripts = array('jquery','qppr-meta-redirect-no-load');
			$jqnew = isset( $wp_scripts->queue ) ? $wp_scripts->queue : array() ;
			if( is_array($jqnew) && !empty($jqnew)){
				foreach( $jqnew as $key => $val ){
					if( !in_array( $val, $allowScripts ) ){
						unset($wp_scripts->queue[$key]);
					}
				}
			}
			wp_print_scripts();
			echo '</head>'."\n";
			echo '<body>'."\n";
			echo '</body>'."\n";
			echo '</html>';
			exit;
		}else{
			//wp_enqueue_script( 'qppr-meta-redirect-load', plugins_url( '/js/qppr_meta_redirect.js', __FILE__ ), array( 'jquery' ), $this->ppr_curr_version, false );
			wp_enqueue_script( 'qppr-meta-redirect-load', plugins_url( '/js/qppr_meta_redirect.min.js', __FILE__ ), array( 'jquery' ), $this->ppr_curr_version, false );
			wp_localize_script( 'qppr-meta-redirect-load', 'qpprMetaData', array('browserFamily' => $bfamily, 'appendTo' => $appendTo, 'class' => $class, 'secs' => $secs, 'refreshURL' => $refresh_url , 'injectMsg' => $injectMsg ) );
		}
		return;
	}

	function qppr_meta_addon_page(){
	?>
	<div class="wrap" style="position:relative;">
		<h2><?php echo __( 'Meta Redirect Settings', 'quick-pagepost-redirect-plugin' );?></h2>
		<?php if ( ! empty( $_GET['settings-updated'] ) ) : ?><div id="message" class="updated notice is-dismissible"><p><?php echo __( 'Settings Updated', 'quick-pagepost-redirect-plugin' );?></p></div><?php endif; ?>
		<p><?php echo __( 'This section is for updating options for redirects that use the "meta refresh" funcitonality for redirecting.', 'quick-pagepost-redirect-plugin' );?></p>
		<p><?php echo __( 'Using the setting below, you can add elements or a message to the page that is loaded before tht redirect, or just allow the page to load as normal until the redirect reaches the number of seconds you have set below.', 'quick-pagepost-redirect-plugin' );?></p>
		<form method="post" action="options.php" class="qpprform">
			<?php settings_fields( 'qppr-meta-settings-group' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label id="qppr-meta-options"><?php echo __( 'Load Page Content?', 'quick-pagepost-redirect-plugin' );?></label></th>
					<td><input type="checkbox" name="qppr_meta_addon_load" value="1" <?php echo ( ( get_option( 'qppr_meta_addon_load', '' ) != '' ) ? ' checked="checked"' : '' ); ?> /><span><?php echo __( 'Check if you want the normal page to load before redirect happens (if redirect is 0 seconds, it may not load fully).', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Redirect Seconds', 'quick-pagepost-redirect-plugin' );?>:</label></th>
					<td><input type="text" size="5" name="qppr_meta_addon_sec" value="<?php echo get_option('qppr_meta_addon_sec', '0'); ?>"/><span><code>0</code> = <?php echo __( 'instant', 'quick-pagepost-redirect-plugin' );?>*. <code>10</code> <?php echo __( 'would redirect 10 seconds after the required element is loaded (i.e., body or an element with a specific class). *Intsant will still have a \'slight\' delay, as some content needs to load before the redirect occurs. Settings on individual pages will override this setting.', 'quick-pagepost-redirect-plugin' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Redirect Trigger', 'quick-pagepost-redirect-plugin' );?>:</label></th>
					<td><input type="text" size="25" id="qppr_meta_addon_trigger" name="qppr_meta_addon_trigger" value="<?php echo get_option('qppr_meta_addon_trigger', 'body'); ?>"/><span><?php printf( __( 'The %1$s, %2$s or tag name of the element you want to load before triggering redirect. Use a %3$s in the class name or %4$s for the ID. <strong><em>For example:</em></strong> if you want it to redirect when the body tag loads, you would type %5$s above. To redirect after an element with a class or ID, use %6$s or %7$s.', 'quick-pagepost-redirect-plugin' ), '<code>class</code>', '<code>ID</code>', '<code>.</code>', '<code>#</code>', '<code>body</code>', '<code>.some-class</code>', '<code>#some-id</code>' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Append Content To', 'quick-pagepost-redirect-plugin' );?>:</label></th>
					<td><input type="text" size="25" id="qppr_meta_append_to" name="qppr_meta_append_to" value="<?php echo get_option('qppr_meta_append_to', 'body'); ?>"/><span><?php printf( __( 'The %1$s, %2$s or tag name of the element you want the content to load into when the page loads.', 'quick-pagepost-redirect-plugin' ), '<code>class</code>', '<code>ID</code>' );?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><?php echo __( 'Page Content', 'quick-pagepost-redirect-plugin' );?>:</label></th>
					<td><span><?php printf( __( 'Be sure to include a tag with your class or ID or tag name (entered above) so the redirect triggers - if you do not, the redirect will not happen. If you check the box to "Load Page Content", this data will be inserted into the page right after the %1$s tag. Otherwise, it will be the only content shown.', 'quick-pagepost-redirect-plugin' ), '&lt;body&gt;');?><br /><strong><br /><?php echo __( 'Add your content below', 'quick-pagepost-redirect-plugin' );?></strong>.</span>
						<textarea id="qppr_meta_addon_content" name="qppr_meta_addon_content"><?php echo get_option('qppr_meta_addon_content', ''); ?></textarea>
						<br /><span><?php echo __( 'To use a counter, add the following:', 'quick-pagepost-redirect-plugin' );?>
						<pre>&lt;div id="qppr_meta_counter" data-meta-counter-text="This page will redirect in %1$ seconds."&gt;&lt;/div&gt;</pre>
						<?php echo __( 'The "%1$" will be replaced with the actual seconds.', 'quick-pagepost-redirect-plugin' );?>
						</span>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php echo __( 'Save Changes', 'quick-pagepost-redirect-plugin' );?>" /></p>
		</form>
	</div>	
	<?php
	}
	
	function qppr_meta_plugin_has_addon() {
		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
			return;
		if ( is_admin() && is_plugin_active( 'qppr-meta-redirect-add-on/qppr-meta-redirect-add-on.php' ) ) {
			add_action( 'admin_notices', array( $this, 'qppr_meta_addon_admin_notice' ) );
			deactivate_plugins( 'qppr-meta-redirect-add-on/qppr-meta-redirect-add-on.php' ); 
		}
	}

	function qppr_meta_addon_admin_notice() {
		echo '
		<div class="update-nag">
			' . __( 'You have the Addon Plugin', 'quick-pagepost-redirect-plugin' ) . ' <strong>"QPPR - Meta Redirect Add On"</strong> ' . __( 'activated. This plugin\'s functionality is now built into the parent', 'quick-pagepost-redirect-plugin' ) . ' <strong>"Quick Page/Post Redirect Plugin"</strong> ' . __( 'so you no longer need to have the addon plugin installed.', 'quick-pagepost-redirect-plugin' ) . '
			<br /><br />' . __( 'The plugin will be deactivated now to prevent conflicts. You may delete it if you desire.', 'quick-pagepost-redirect-plugin' ) . '
		</div>';
	}
}
//=======================================
// END Main Redirect Class.
//=======================================
function start_ppr_class(){
	global $newqppr, $redirect_plugin;
	$redirect_plugin = $newqppr = new quick_page_post_reds(); // call our class
}

/**
* qppr_create_individual_redirect - helper function to create Individual Redirect programatically.
* @param array $atts default settings for array.
*		post_id int|string the post id
*		active int 1 or 0
*		url	string redirect URL
*		type string 301, 302, 307 or meta
*		newwindow int 1 or 0
*		nofollow int 1 or 0
*		rewrite int 1 or 0
* @return bool true on success 
* @example:
* *****************
	$atts = array(
		'post_id' 	=> $post->ID,
		'url' 		=> 'http://example.com/',
		'active' 	=> 0, 
		'type' 		=> '301',
		'newwindow'	=> 1,
		'nofollow'	=> 0,
		'rewrite'	=> 0
	);
	qppr_create_individual_redirect( $atts );
* *****************
*/
function qppr_create_individual_redirect( $atts = array() ){
	if( !is_array( $atts ) )
		return false;
	$defaults = array( 
		'post_id' 	=> '0', 
		'active' 	=> 1, 
		'url'		=> '',
		'type' 		=> '301',
		'newwindow'	=> 0,
		'nofollow'	=> 0,
		'rewrite'	=> 0
	);
	extract( shortcode_atts($defaults, $atts) );
	if( $post_id == '0' || $url == '' )
		return false;
	// some validation
	$type 		= !in_array( $type, array( '301', '302', '307', 'meta' ) ) ? '301' : $type;
	$active 	= (int) $active == 1 ? 1 : 0;
	$newwindow 	= (int) $newwindow == 1 ? 1 : 0;
	$nofollow 	= (int) $nofollow == 1 ? 1 : 0;
	$rewrite 	= (int) $rewrite == 1 ? 1 : 0;
	// set required meta 
	add_post_meta( $post_id, '_pprredirect_url', $url );
	add_post_meta( $post_id, '_pprredirect_type', $type );
	add_post_meta( $post_id, '_pprredirect_active', $active );
	//set optional meta
	if( $rewrite == 1 )
		add_post_meta( $post_id, '_pprredirect_rewritelink', 1 );
	if( $newwindow == 1 )
		add_post_meta( $post_id, '_pprredirect_newwindow', '_blank' );
	if( $nofollow == 1 )
		add_post_meta( $post_id, '_pprredirect_relnofollow', 1 );	
	return true;
}
/**
* qppr_delete_individual_redirect - helper function to delete Individual Redirect programatically.
* @param post_id int|string the post id
* @return bool true on success 
* @example:
* *****************
	qppr_delete_individual_redirect( $post_id );
* *****************
*/
function qppr_delete_individual_redirect( $post_id = 0){
	$post_id = (int) $post_id;
	if( $post_id == 0 )
		return false;
	$ptype = get_post_type( $post_id );
	if( $ptype != 'post' )
		$ok = current_user_can( 'edit_pages' );
	else
		$ok = current_user_can( 'edit_posts' );
		
	if( $ok ){ 
		// delete meta fields
		delete_post_meta( $post_id, '_pprredirect_url' );
		delete_post_meta( $post_id, '_pprredirect_type');
		delete_post_meta( $post_id, '_pprredirect_active' );
		delete_post_meta( $post_id, '_pprredirect_rewritelink' );
		delete_post_meta( $post_id, '_pprredirect_newwindow' );
		delete_post_meta( $post_id, '_pprredirect_relnofollow' );	
		return true;
	}else{
		return false;
	}
}

/**
* qppr_create_quick_redirect - helper function to create Quick Redirect programatically.
* @param array $atts default settings for array.
*		request_url string redirect URL
*		destination_url	string redirect URL
*		newwindow int 1 or 0
*		nofollow int 1 or 0
* @return bool true on success 
* @example:
* *****************
	$atts = array(
		'request_url'		=> '/some-url/',
		'destination_url'	=> '/new-url/',
		'newwindow'			=> 1,
		'nofollow'			=> 0,
	);
	qppr_create_quick_redirect( $atts );
* *****************
*/
function qppr_create_quick_redirect( $atts = array() ){
	if( !is_array( $atts ) )
		return false;
	$defaults = array( 
		'request_url'		=> '',
		'destination_url'	=> '',
		'newwindow'			=> 0,
		'nofollow'			=> 0,
	);
	extract( shortcode_atts($defaults, $atts) );
	if( $request_url == '' || $destination_url == '' )
		return false;
	
	global $newqppr, $redirect_plugin;
	$currRedirects 	= get_option( 'quickppr_redirects', array() );
	$currMeta 		= get_option( 'quickppr_redirects_meta', array() );
	$protocols 		= apply_filters( 'qppr_allowed_protocols', array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
	$request_url	= esc_url( str_replace( ' ', '%20', trim( $request_url ) ), null, 'appip' );
	$destination_url= esc_url( str_replace( ' ', '%20', trim( $destination_url ) ), null, 'appip' );
	$newwindow 		= (int) $newwindow == 1 ? 1 : 0;
	$nofollow 		= (int) $nofollow == 1 ? 1 : 0;
	if( strpos( $request_url, '/', 0 ) !== 0 && !$redirect_plugin->qppr_strposa( $request_url, $protocols ) )
		$request_url = '/' . $request_url; // adds root marker to front if not there
	if( ( strpos( $request_url, '.' ) === false && strpos( $request_url, '?' ) === false ) && strpos( $request_url, '/', strlen( $request_url ) -1 ) === false )
		$request_url = $request_url . '/'; // adds end folder marker if not a file end
	if( ( $request_url == '' || $request_url == '/' ) && $destination_url == '')
		return false; //if nothing there do nothing
	elseif ( $request_url != '' && $request_url != '/' && $destination_url == '' )
		$currRedirects[$request_url] = '/';
	else
		$currRedirects[$request_url] = $destination_url;

	$currMeta[$request_url]['newwindow'] = $newwin;
	$currMeta[$request_url]['nofollow']  = $nofoll;
	update_option( 'quickppr_redirects', sanitize_option( 'quickppr_redirects', $currRedirects ) );
	update_option( 'quickppr_redirects_meta', sanitize_option( 'quickppr_redirects_meta', $currMeta ) );
	$redirect_plugin->quickppr_redirectsmeta	= get_option( 'quickppr_redirects_meta', array() );
	$redirect_plugin->quickppr_redirects 		= get_option( 'quickppr_redirects', array() );
	return true;
}
/**
* qppr_delete_quick_redirect - helper function to delete Quick Redirect programatically.
* @param request_url string redirect URL
* @return bool true on success 
* @example:
* *****************
	qppr_delete_quick_redirect( '/some-url/' );
* *****************
*/
function qppr_delete_quick_redirect( $request_url = '' ){
	if( $request_url == '' )
		return false;
	global $newqppr, $redirect_plugin;
	$currRedirects 	= get_option( 'quickppr_redirects', array() );
	$currMeta 		= get_option( 'quickppr_redirects_meta', array() );
	$protocols 		= apply_filters( 'qppr_allowed_protocols', array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'));
	$request_url	= esc_url( str_replace( ' ', '%20', trim( $request_url ) ), null, 'appip' );
	if( !isset( $currRedirects[$request_url] ) )
		return false;
	if( !isset( $currMeta[$request_url] ) ) 
		return false;
	unset( $currRedirects[$request_url], $currMeta[$request_url] );
	update_option( 'quickppr_redirects', sanitize_option( 'quickppr_redirects', $currRedirects ) );
	update_option( 'quickppr_redirects_meta', sanitize_option( 'quickppr_redirects_meta', $currMeta ) );
	$redirect_plugin->quickppr_redirectsmeta	= get_option( 'quickppr_redirects_meta', array() );
	$redirect_plugin->quickppr_redirects 		= get_option( 'quickppr_redirects', array() );
	return true;
}

/**
* qppr_get_browser_family - helper function that uses HTTP_USER_AGENT to determine browser family (for meta redirect).
* @param type string either 'name' or 'class'
* @return string returns browser family name or class (using sanitize_title_with_dashes function).
*		returns 'unknown' if browser family is not known.
* @since: 5.1.3
* @example:
* *****************
	$browserFamilyName = qppr_get_browser_family( 'name' );
* *****************
*/
function qppr_get_browser_family( $type = 'class' ){ //name or class
	global $is_iphone,$is_chrome,$is_safari,$is_NS4,$is_opera,$is_macIE,$is_winIE,$is_gecko,$is_lynx,$is_IE,$is_edge;
	if( $is_IE ){
		if( $is_macIE )
			$name = 'Mac Internet Explorer';
		if( $is_winIE )
			$name = 'Windows Internet Explorer';
		$name = 'Internet Explorer';
	}else if( $is_iphone || $is_safari ){
		if( $is_safari )
			$name = 'Safari';
		$name = 'iPhone Safari';
	}else if( $is_edge ){
		$name = 'Microsoft Edge';
	}else if( $is_chrome ){
		$name = 'Google Chrome';
		if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
			if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') !== false )
				$name = 'Microsoft Edge';
		}
	}else if( $is_NS4 ){
		$name = 'Netscape 4';
	}else if( $is_opera ){
		$name = 'Opera';
	}else if( $is_gecko ){
		$name = 'FireFox';
	}else if( $is_lynx ){
		$name = 'Lynx';
	}else{
		$name = 'Unknown';	
	}
	if($type == 'name')
		return $name;
	return sanitize_title_with_dashes( 'browser-'.$name );
}