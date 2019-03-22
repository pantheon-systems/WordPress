<?php
/*
 * working behind the seen
 */

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');

class NM_PersonalizedProduct_Admin extends NM_PersonalizedProduct {

	var $menu_pages, $plugin_scripts_admin, $plugin_settings;

	function __construct() {
		
		// setting plugin meta saved in config.php
		$this->plugin_meta = ppom_get_plugin_meta();
		
		// getting saved settings
		$this->plugin_settings = get_option ( $this -> plugin_meta['shortname'] . '_settings' );
		
		// populating $inputs with NM_Inputs object
		// $this -> inputs = self::get_all_inputs ();
		
		/*
		 * [1] TODO: change this for plugin admin pages
		*/
		$this->menu_pages = array ( array (
				'page_title' => __('PPOM', "ppom"),
				'menu_title' => __('PPOM', "ppom"),
				'cap' => 'manage_options',
				'slug' => "ppom",
				'callback' => 'product_meta',
				'parent_slug' => 'woocommerce' 
			),
		);
		
		
		add_action ( 'admin_menu', array (
				$this,
				'add_menu_pages' 
		) );
		
		
		// Get PRO version
		// add_action('ppom_after_ppom_field_admin', 'ppom_admin_pro_version_noticez', 10);
		add_action('ppom_after_ppom_field_admin', 'ppom_admin_rate_and_get', 10);
		
		// Getting products list
		add_action('wp_ajax_ppom_get_products', array($this, 'get_products'));
		add_action('wp_ajax_ppom_attach_ppoms', array($this, 'ppom_attach_ppoms'));
		
		// Adding setting tab in WooCommerce
    	add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    	
    	// Display settings
    	add_action( 'woocommerce_settings_tabs_ppom_settings', array($this, 'settings_tab') );
    	// Save settings
    	add_action( 'woocommerce_update_options_ppom_settings', array($this, 'save_settings') );
    	
    	add_action( 'admin_head', array($this, 'ppom_tabs_custom_style') );
	}
	

	
	/*
	 * creating menu page for this plugin
	*/
	function add_menu_pages() {
		
		if( !$this->menu_pages ) return '';
		
		foreach ( $this->menu_pages as $page ) {
			
			if ($page ['parent_slug'] == '') {
				
				$menu = add_options_page ( __ ( 'PPOM Fields', "ppom" ), __ ( 'PPOM Fields', "ppom" ), $page ['cap'], $page ['slug'], array (
						$this,
						$page ['callback'] 
				), $this->plugin_meta ['logo'], $this->plugin_meta ['menu_position'] );
			} else {
				
				$menu = add_submenu_page ( $page ['parent_slug'], __ ( $page ['page_title'], "ppom" ), __ ( 'PPOM Fields', "ppom" ), $page ['cap'], $page ['slug'], array (
						$this,
						$page ['callback'] 
				) );
			}
			
			
		}
	}
	

	/*
	 * CALLBACKS
	*/
	function product_meta() {
		echo '<div class="ppom-admin-wrap woocommerce">';
		
		$action  = (isset($_REQUEST ['action']) ? $_REQUEST ['action'] : '');
		$do_meta = (isset($_REQUEST ['do_meta']) ? $_REQUEST ['do_meta'] : '');

		if ($action != 'new' && $do_meta != 'edit' && $do_meta != 'clone') {
			echo '<h1 class="ppom-heading-style">' . __ ( 'N-Media WooCommerce Personalized Product Option Manager', "ppom" ) . '</h1>';
			echo '<p>' . __ ( 'Create different meta groups for different products.', "ppom" ) . '</p>';
		}
		
		if ((isset ( $_REQUEST ['productmeta_id'] ) && $_REQUEST ['do_meta'] == 'edit') || $action == 'new') {
			ppom_load_template ( 'admin/ppom-fields.php' );
		} elseif ( isset($_REQUEST ['do_meta']) && $_REQUEST ['do_meta'] == 'clone') {
			$this -> clone_product_meta($_REQUEST ['productmeta_id']);
		}else{
			$url_add = add_query_arg(array('action' => 'new'));
			$video_url = 'https://najeebmedia.com/ppom/#howtovideo';
			$ppom_settings_url = admin_url( "admin.php?page=wc-settings&tab=ppom_settings" );
			
			echo '<div class="ppom-product-meta-block text-center ppom-meta-card-block">';
				echo '<h2>' . __ ( 'How it works?', "ppom" ) . '</h2>';
				printf(__('<p><a href="%s" target="_blank">Watch a Quick Video</a>', "ppom"), $video_url);
				printf(__(' - <a href="%s">PPOM Settings</a></p>', "ppom"), $ppom_settings_url);
				echo '<a class="btn btn-success" href="'.esc_url($url_add).'"><span class="dashicons dashicons-plus"></span> '. __ ( 'Add PPOM Meta Group', "ppom" ) . '</a>';
			echo '</div>';
			echo '<br>';

			do_action('ppom_pdf_setting_action');
			do_action('ppom_enquiryform_setting_action');
			
			echo '<div class="ppom-nm-plugins-block">';
				echo '<a class="btn btn-yellow ppom-import-export-btn" href=""><span class="dashicons dashicons-download"></span> ' . __ ( 'Import Meta', "ppom" ) . '</a>';
				echo '<a class="ppom-nm-plugins" href="#"  data-modal-id="ppom-nm-plugins-modal">PPOM More Addons</a>';
				ppom_load_template ( 'admin/ppom-nm-plugins.php' );
			echo '</div>';
			
		}
		
		// existing meta group tables show only ppom main page
		if ( $action != 'new' && $do_meta != 'edit' ) {
			ppom_load_template ( 'admin/existing-meta.php' );
		}
		
		echo '</div>';
	}
	
	
	/*
	 * Get Products
	*/
	function get_products() {
		
		global $wpdb;
		
		$all_product_data = $wpdb->get_results("SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");
		
		$ppom_id = $_GET['ppom_id'];
		
		ob_start();
		$vars = array('product_list'=>$all_product_data,'ppom_id'=>$ppom_id);
		ppom_load_template('admin/products-list.php', $vars);
		
		$list_html = ob_get_clean();
        
        echo $list_html;
        
        die(0);
	}
	

	/*
	 * PPOM Attachments
	*/
	function ppom_attach_ppoms() {
		
		// wp_send_json($_POST);
		$response = array();
		
		$ppom_id = $_POST['ppom_id'];
		
		$ppom_udpated = 0;
		// Reset
		if( isset($_POST['ppom_removed']) ) {
			
			foreach($_POST['ppom_removed'] as $product_id) {
			
				delete_post_meta($product_id, '_product_meta_id');
				$ppom_udpated++;
			}
		}
		
		if( isset($_POST['ppom_attached']) ) {
			
			foreach($_POST['ppom_attached'] as $product_id) {
			
				update_post_meta($product_id, '_product_meta_id', $ppom_id);
				$ppom_udpated++;
			}
		}
		
		$response = array("message"=>"PPOM updated for {$ppom_udpated} Products", 'status'=>'success');
		
		wp_send_json( $response );
	}


	/*
	 * Plugin Validation
	*/
	function validate_plugin(){
		
		echo '<div class="wrap">';
		echo '<h2>' . __ ( 'Provide API key below:', "ppom" ) . '</h2>';
		echo '<p>' . __ ( 'If you don\'t know your API key, please login into your: <a target="_blank" href="http://wordpresspoets.com/member-area">Member area</a>', "ppom" ) . '</p>';
		
		echo '<form onsubmit="return validate_api_wooproduct(this)">';
			echo '<p><label id="plugin_api_key">'.__('Entery API key', "ppom").':</label><br /><input type="text" name="plugin_api_key" id="plugin_api_key" /></p>';
			wp_nonce_field();
			echo '<p><input type="submit" class="button-primary button" name="plugin_api_key" /></p>';
			echo '<p id="nm-sending-api"></p>';
		echo '</form>';
		
		echo '</div>';
	}
	
	public static function add_settings_tab( $settings_tabs ) {
    	
        $settings_tabs['ppom_settings'] = __( 'PPOM Settings', 'ppom' );
        return $settings_tabs;
    }
    
    function settings_tab() {
    	
    	woocommerce_admin_fields( ppom_array_settings() );
        
    }
    
    function save_settings() {
    	
    	woocommerce_update_options( ppom_array_settings() );
    }

    function ppom_tabs_custom_style() {
		?>
		<style>
			#woocommerce-product-data .ppom_extra_options_panel label{ margin: 0 !important; }
			
			/* PPOM Meta in column */
			th.column-ppom_meta{ width: 10%!important;}
		</style><?php
	}
    
    
}