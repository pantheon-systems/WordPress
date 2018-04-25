<?php
/*
Plugin Name: Duplicate Page
Plugin URI: https://wordpress.org/plugins/duplicate-page/
Description: Duplicate Posts, Pages and Custom Posts using single click.
Author: mndpsingh287
Version: 2.6
Author URI: https://profiles.wordpress.org/mndpsingh287/
License: GPLv2
Text Domain: duplicate-page
*/
if (!defined("DUPLICATE_PAGE_PLUGIN_DIRNAME")) define("DUPLICATE_PAGE_PLUGIN_DIRNAME", plugin_basename(dirname(__FILE__)));
if(!class_exists('duplicate_page')):
	class duplicate_page 
	{
		protected $SERVER = 'http://ikon.digital/plugindata/api.php';
		/*
		* AutoLoad Hooks
		*/	
		public function __construct(){
			register_activation_hook(__FILE__, array(&$this, 'duplicate_page_install'));
			add_action('admin_menu', array(&$this, 'duplicate_page_options_page'));
			add_filter( 'plugin_action_links', array(&$this, 'duplicate_page_plugin_action_links'), 10, 2 );
			add_action( 'admin_action_dt_duplicate_post_as_draft', array(&$this,'dt_duplicate_post_as_draft') ); 
			add_filter( 'post_row_actions', array(&$this,'dt_duplicate_post_link'), 10, 2);
			add_filter( 'page_row_actions', array(&$this,'dt_duplicate_post_link'), 10, 2);
			add_action( 'post_submitbox_misc_actions', array(&$this,'duplicate_page_custom_button'));
			add_action( 'wp_before_admin_bar_render', array(&$this, 'duplicate_page_admin_bar_link'));
			add_action('init', array(&$this, 'duplicate_page_load_text_domain'));
			 /*
			 Lokhal Verify Email 
			 */
			 add_action( 'wp_ajax_mk_duplicatepage_verify_email', array(&$this, 'mk_duplicatepage_verify_email_callback'));
			 add_action( 'wp_ajax_verify_duplicatepage_email', array(&$this, 'verify_duplicatepage_email_callback') );
			 add_action( 'admin_head', array(&$this, 'duplicate_page_admin_head'));

		}
		  		/* Verify Email*/
		public function mk_duplicatepage_verify_email_callback() {
			$current_user = wp_get_current_user();
			$nonce = $_REQUEST['vle_nonce'];
            if ( wp_verify_nonce( $nonce, 'verify-duplicatepage-email' ) ) {			
				$action = sanitize_text_field($_POST['todo']);
				$lokhal_email = sanitize_text_field($_POST['lokhal_email']);
				$lokhal_fname = sanitize_text_field($_POST['lokhal_fname']);
				$lokhal_lname = sanitize_text_field($_POST['lokhal_lname']);
				// case - 1 - close
				if($action == 'cancel') {
				   set_transient( 'duplicatepage_cancel_lk_popup_'.$current_user->ID, 'duplicatepage_cancel_lk_popup_'.$current_user->ID, 60 * 60 * 24 * 30 );			
			 	   update_option( 'duplicatepage_email_verified_'.$current_user->ID, 'yes' );
				} else if($action == 'verify') {
				  $engagement = '75';	
				  update_option( 'duplicatepage_email_address_'.$current_user->ID, $lokhal_email );
				  update_option( 'verify_duplicatepage_fname_'.$current_user->ID, $lokhal_fname );
				  update_option( 'verify_duplicatepage_lname_'.$current_user->ID, $lokhal_lname );
				  update_option( 'duplicatepage_email_verified_'.$current_user->ID, 'yes' );
				  /* Send Email Code */
				  $subject = "Email Verification";				  
				  $message = "
					<html>
					<head>
					<title>Email Verification</title>
					</head>
					<body>
					<p>Thanks for signing up! Just click the link below to verify your email and we’ll keep you up-to-date with the latest and greatest brewing in our dev labs!</p>	
					<p><a href='".admin_url('admin-ajax.php?action=verify_duplicatepage_email&token='.md5($lokhal_email))."'>Click Here to Verify
</a></p>				
					</body>
					</html>
					";				
				  // Always set content-type when sending HTML email
				  $headers = "MIME-Version: 1.0" . "\r\n";
				  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				  $headers .= "From: noreply@ikon.digital" . "\r\n";
                  $mail = mail($lokhal_email,$subject,$message,$headers);
				  $data = $this->verify_on_server($lokhal_email, $lokhal_fname,  $lokhal_lname, $engagement, 'verify','0');
				  if($mail) {
				  echo '1';
				  } else {
				  echo '2';  
				  }
				  	
				}
			}
			else {
				echo 'Nonce';
			}
			die;
		}		
		/*
		* Verify Email
		*/
		public function verify_duplicatepage_email_callback() {
			$email = sanitize_text_field($_GET['token']);
			$current_user = wp_get_current_user();
			$lokhal_email_address = md5(get_option('duplicatepage_email_address_'.$current_user->ID));
			if($email == $lokhal_email_address) {
			   $this->verify_on_server(get_option('duplicatepage_email_address_'.$current_user->ID), get_option('verify_duplicatepage_fname_'.$current_user->ID), get_option('verify_duplicatepage_lname_'.$current_user->ID), '100', 'verified','1');
			   update_option( 'duplicatepage_email_verified_'.$current_user->ID, 'yes' );	
			   echo '<p>Email Verified Successfully. Redirecting please wait.</p>';
			   echo '<script>';
			   echo 'setTimeout(function(){window.location.href="https://filemanager.webdesi9.com?utm_redirect=wp" }, 2000);';
			   echo '</script>';
			   
			}
			die;
		}
	    /*
		Send Data To Server
		*/
		public function verify_on_server($email, $fname, $lname, $engagement, $todo, $verified) {
			global $wpdb, $wp_version;	
		      $id = get_option( 'page_on_front' );
			    $info = array(
				         'email' => $email,
						 'first_name' => $fname,
						 'last_name' => $lname,
						 'engagement' => $engagement,
						 'SITE_URL' => site_url(),
				         'PHP_version' => phpversion(),
						 'upload_max_filesize' => ini_get('upload_max_filesize'),
						 'post_max_size' => ini_get('post_max_size'),
						 'memory_limit' => ini_get('memory_limit'),
						 'max_execution_time' => ini_get('max_execution_time'),
						 'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
						 'wp_version' => $wp_version,						 
						 'plugin' => 'duplicate page',					 
						 'nonce' => 'um235gt9duqwghndewi87s34dhg',
						 'todo' => $todo,
						 'verified' => $verified
						 
				);
				$str = http_build_query($info);
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $this->SERVER);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // save to returning 1
				curl_setopt($curl, CURLOPT_POSTFIELDS, $str);
				$result = curl_exec ($curl); 
				$data = json_decode($result,true);
				return $data;
		}
		public function duplicate_page_admin_head() {
			    $getPage = isset($_GET['page']) ? $_GET['page'] : '';
				$allowedPages = array(
									  'duplicate_page_settings',
									  );
					if(!empty($getPage) && in_array($getPage, $allowedPages)):
					 include('head.php');
					endif;
		}
		/*
		* Localization - 19-dec-2016
		*/
		public function duplicate_page_load_text_domain(){
			load_plugin_textdomain('duplicate-page', false, DUPLICATE_PAGE_PLUGIN_DIRNAME . "/languages");
		}
		/*
		* Activation Hook
		*/
		public function duplicate_page_install(){
					$defaultsettings = array(
											 'duplicate_post_status' => 'draft',
											 'duplicate_post_redirect' => 'to_list',
											 'duplicate_post_suffix' => ''
											 );
					$opt = get_option('duplicate_page_options');
					if(!$opt['duplicate_post_status']) {
						update_option('duplicate_page_options', $defaultsettings);
					}           	
		}
		/*
		Action Links
		*/
		public function duplicate_page_plugin_action_links($links, $file){
			if ( $file == plugin_basename( __FILE__ ) ) {
				$duplicate_page_links = '<a href="'.get_admin_url().'options-general.php?page=duplicate_page_settings">'.__('Settings', 'duplicate-page').'</a>';
				$duplicate_page_donate = '<a href="http://www.webdesi9.com/donate/?plugin=duplicate-page" title="Donate Now" target="_blank" style="font-weight:bold">'.__('Donate', 'duplicate-page').'</a>';
				array_unshift( $links, $duplicate_page_donate );
				array_unshift( $links, $duplicate_page_links );
			}
		
			return $links;
		}
		/*
		* Admin Menu 
		*/
		public function duplicate_page_options_page(){	
		 add_options_page( __( 'Duplicate Page', 'duplicate-page' ), __( 'Duplicate Page', 'duplicate-page' ), 'manage_options', 'duplicate_page_settings',array(&$this, 'duplicate_page_settings'));
		}
		/*
		* Duplicate Page Admin Settings
		*/
		public function duplicate_page_settings(){
			if(current_user_can( 'manage_options' )){
			   include('admin-settings.php');
			}
		}
		/*
		* Main function
		*/
		public function dt_duplicate_post_as_draft(){
		global $wpdb;
		$opt = get_option('duplicate_page_options');
		$suffix = isset($opt['duplicate_post_suffix']) && !empty($opt['duplicate_post_suffix']) ? ' -- '.$opt['duplicate_post_suffix'] : '';
		$post_status = !empty($opt['duplicate_post_status']) ? $opt['duplicate_post_status'] : 'draft';	
		$redirectit = !empty($opt['duplicate_post_redirect']) ? $opt['duplicate_post_redirect'] : 'to_list';	 
					 if (! ( isset( $_GET['post']) || isset( $_POST['post']) || ( isset($_REQUEST['action']) && 'dt_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
					 wp_die('No post to duplicate has been supplied!');
				 } 
				 $returnpage = '';
				 /*
				 * get the original post id
				 */
				  $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
				 /*
				 * and all the original post data then
				 */
				 $post = get_post( $post_id ); 
				 /*
				 * if you don't want current user to be the new post author,
				 * then change next couple of lines to this: $new_post_author = $post->post_author;
				 */
				 $current_user = wp_get_current_user();
				 $new_post_author = $current_user->ID; 
				 /*
				 * if post data exists, create the post duplicate
				 */
				 if (isset( $post ) && $post != null) { 
				 /*
				 * new post data array
				 */
				 $args = array(
					 'comment_status' => $post->comment_status,
					 'ping_status' => $post->ping_status,
					 'post_author' => $new_post_author,
					 'post_content' => $post->post_content,
					 'post_excerpt' => $post->post_excerpt,
					 'post_name' => $post->post_name,
					 'post_parent' => $post->post_parent,
					 'post_password' => $post->post_password,
					 'post_status' => $post_status,
					 'post_title' => $post->post_title.$suffix,
					 'post_type' => $post->post_type,
					 'to_ping' => $post->to_ping,
					 'menu_order' => $post->menu_order
				 ); 
				 /*
				 * insert the post by wp_insert_post() function
				 */
				 $new_post_id = wp_insert_post( $args ); 
				 /*
				 * get all current post terms ad set them to the new post draft
				 */
				 $taxonomies = get_object_taxonomies($post->post_type);
				 if(!empty($taxonomies) && is_array($taxonomies)):
				 foreach ($taxonomies as $taxonomy) {
					 $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
					 wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				 } 
				 endif;
				 /*
				 * duplicate all post meta
				 */
				 $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
				 if (count($post_meta_infos)!=0) {
				 $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				 foreach ($post_meta_infos as $meta_info) {
					 $meta_key = $meta_info->meta_key;
					 $meta_value = addslashes($meta_info->meta_value);
					 $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
					 }
					 $sql_query.= implode(" UNION ALL ", $sql_query_sel);
					 $wpdb->query($sql_query);
					 } 
					 /*
					 * finally, redirecting to your choice
					 */
					if($post->post_type != 'post'):
					   $returnpage = '?post_type='.$post->post_type;
					endif;
					if(!empty($redirectit) && $redirectit == 'to_list'): 
						wp_redirect( admin_url( 'edit.php'.$returnpage ) );
					elseif(!empty($redirectit) && $redirectit == 'to_page'): 	
						wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
					else:
						wp_redirect( admin_url( 'edit.php'.$returnpage ) );
					endif;	
					 exit;
					 } else {
						wp_die('Error! Post creation failed, could not find original post: ' . $post_id);
					 }
		}
		/*
		 * Add the duplicate link to action list for post_row_actions
		 */
		public function dt_duplicate_post_link( $actions, $post ) {
			$opt = get_option('duplicate_page_options');
			$post_status = !empty($opt['duplicate_post_status']) ? $opt['duplicate_post_status'] : 'draft';	
			 if (current_user_can('edit_posts')) {
			 $actions['duplicate'] = '<a href="admin.php?action=dt_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this as '.$post_status.'" rel="permalink">'.__( "Duplicate This", "duplicate-page" ).'</a>';
			 }
			 return $actions;
		} 
		/*
		 * Add the duplicate link to edit screen
		 */
		public function duplicate_page_custom_button(){
			global $post;
			$opt = get_option('duplicate_page_options');
			$post_status = !empty($opt['duplicate_post_status']) ? $opt['duplicate_post_status'] : 'draft';	
				$html  = '<div id="major-publishing-actions">';
				$html .= '<div id="export-action">';
				$html .= '<a href="admin.php?action=dt_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this as '.$post_status.'" rel="permalink">'.__( "Duplicate This", "duplicate-page" ).'</a>';
				$html .= '</div>';
				$html .= '</div>';
				echo $html;
		}
		/*
		* Admin Bar Duplicate This Link
		*/
		public function duplicate_page_admin_bar_link(){
			global $wp_admin_bar, $post;
			$opt = get_option('duplicate_page_options');
			$post_status = !empty($opt['duplicate_post_status']) ? $opt['duplicate_post_status'] : 'draft';	
			$current_object = get_queried_object();
			if ( empty($current_object) )
			return;
			if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& ( $post_type_object->show_ui || $current_object->post_type  == 'attachment') )
			{
				$wp_admin_bar->add_menu( array(
				'parent' => 'edit',
				'id' => 'duplicate_this',
				'title' => __("Duplicate this as ".$post_status."", 'duplicate-page'),
				'href' => admin_url().'admin.php?action=dt_duplicate_post_as_draft&amp;post='. $post->ID
				) );
			}
		}
		public function duplicate_page_adsense() {
			    $API = "http://www.webdesi9.com/adsense/";
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $API);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, "ad_token=DUP_3951m8635u6542n3819i69130s9372h5602");
				$result = curl_exec ($curl); 
				$data = json_decode($result, true);
				curl_close ($curl);
				if(!empty($data) && $data['status'] == 1 && !empty($data['image'])) {
					return '<a href="'.$data['link'].'" target="_blank" title="Click here"><img src="'.$data['image'].'" width="100%"></a>';
				}
		}
		/*
		 * Redirect function
		*/
		static function dp_redirect($url){
			echo '<script>window.location.href="'.$url.'"</script>';
		}
		  /*
		 Loading Custom Assets
		*/
	   public function load_custom_assets() {
		   echo '<script src="'.plugins_url('js/duplicatepage.js', __FILE__).'"></script>';
		   echo "<link rel='stylesheet' href='".plugins_url('css/duplicatepage.css', __FILE__)."' type='text/css' media='all' />
		   ";
	   }
	}
new duplicate_page;
endif;
?>