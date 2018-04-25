<?php
if( ! defined("SS_VERSION") ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class SS_Admin {

	private $code_version = 1;
	
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		
		add_filter( "plugin_action_links_wp-social-sharing/index.php", array( $this, 'add_settings_link' ) );

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wp-social-sharing' ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );
		}
	}
	
	static function wss_plugin_activation_action(){
		$defaults = array(
				'twitter_username' => "",
				'auto_add_post_types' => array( 'post' ),
				'social_options'=>array('facebook','twitter','googleplus'),
				'social_icon_position'=>'after',
				'load_static'=>array('load_css','load_js'),
				'facebook_text'=>"Share on Facebook",
				'twitter_text'=>"Share on Twitter",
				'googleplus_text'=>"Share on Google+",
				'linkedin_text'=>"Share on Linkedin",
				'pinterest_text'=>"Share on Pinterest",
				'pinterest_image'=>"",
				'xing_text'=>"Share on Xing",
				'reddit_text'	=>	'Share on Reddit',
				'show_icons'=>'1',
				'before_button_text'=>'',
				'text_position'=>'left'
		);
		update_option( 'wp_social_sharing', $defaults );
		update_option( 'wss_wp_social_sharing','f,t,g,l,p,x,r');
		update_option( 'wss_pluign_version ',SS_VERSION);
	}
	
	public function load_css() {
		wp_enqueue_style ( 'wp-social-sharing', SS_PLUGIN_URL . 'static/admin-styles.css' );
		wp_enqueue_media();
		wp_enqueue_script( 'wp-social-sharing', SS_PLUGIN_URL . 'static/socialshareadmin.js', array(), SS_VERSION, true );
	}

	public function register_settings() {
		register_setting( 'wp_social_sharing', 'wp_social_sharing', array($this, 'sanitize_settings') );
	}

	public function sanitize_settings( $settings ) {
		$settings['before_button_text'] 	= 	trim( strip_tags( $settings['before_button_text'] ) );
		$settings['twitter_username'] 		= 	trim( strip_tags( $settings['twitter_username'] ) );
		$settings['facebook_text'] 			=	trim( strip_tags( $settings['facebook_text'] ) );
		$settings['twitter_text'] 			= 	trim( strip_tags( $settings['twitter_text'] ) );
		$settings['googleplus_text'] 		=	trim( strip_tags( $settings['googleplus_text'] ) );
		$settings['linkedin_text'] 			= 	trim( strip_tags( $settings['linkedin_text'] ) );
		$settings['pinterest_text'] 		= 	trim( strip_tags( $settings['pinterest_text'] ) );
		$settings['pinterest_image'] 		= 	trim( strip_tags( $settings['pinterest_image'] ) );
		$settings['xing_text'] 				= 	trim( strip_tags( $settings['xing_text'] ) );
		$settings['reddit_text']			=	trim( strip_tags( $settings['reddit_text'] ));	
		$settings['auto_add_post_types'] 	= 	( isset( $settings['auto_add_post_types'] ) ) ? $settings['auto_add_post_types'] : array();
		$settings['show_sharebutton'] 		= 	( isset( $settings['show_sharebutton'] ) ) ? $settings['show_sharebutton'] : array();
		return $settings;
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=wp-social-sharing">'. __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function add_menu_item() {
		add_options_page( 'WP Social Sharing', 'WP Social Sharing', 'manage_options', 'wp-social-sharing', array( $this, 'show_settings_page' ) );
	}

	public function show_settings_page() {
		$opts = ss_get_options();
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		include SS_PLUGIN_DIR . 'includes/settings-page.php';
	}
}
