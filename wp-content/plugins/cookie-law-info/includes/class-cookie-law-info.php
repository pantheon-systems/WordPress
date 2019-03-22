<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://cookielawinfo.com/
 * @since      1.6.6
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.6.6
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Cookie_Law_Info {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.6.6
	 * @access   protected
	 * @var      Cookie_Law_Info_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.6.6
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.6.6
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	private static $stored_options=array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.6.6
	 */
	public function __construct() 
	{
		if(defined( 'CLI_VERSION' )) 
		{
			$this->version = CLI_VERSION;
		} 
		else 
		{
			$this->version = '1.7.6';
		}
		$this->plugin_name = 'cookie-law-info';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_thrid_party_hooks();
		//$this->cli_patches();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cookie_Law_Info_Loader. Orchestrates the hooks of the plugin.
	 * - Cookie_Law_Info_i18n. Defines internationalization functionality.
	 * - Cookie_Law_Info_Admin. Defines all hooks for the admin area.
	 * - Cookie_Law_Info_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.6.6
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cookie-law-info-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cookie-law-info-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cookie-law-info-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cookie-law-info-public.php';


		/**
		 * The class responsible for adding compatibility to third party plugins
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'third-party/class-cookie-law-info-third-party.php';

		$this->loader = new Cookie_Law_Info_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cookie_Law_Info_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.6.6
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cookie_Law_Info_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.6.6
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cookie_Law_Info_Admin( $this->get_plugin_name(), $this->get_version(),$this);

		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu',11); /* Adding admin menu */		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_meta_box'); /* Adding custom meta box */
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_metaboxes');/* Saving meta box data */
		$this->loader->add_action( 'manage_edit-cookielawinfo_columns', $plugin_admin, 'manage_edit_columns'); /* Customizing listing column */
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'manage_posts_custom_columns');
		
		$this->loader->add_action('admin_menu',$plugin_admin,'remove_cli_addnew_link');

		// Add plugin settings link:
		add_filter('plugin_action_links_'.plugin_basename(CLI_PLUGIN_FILENAME),array($plugin_admin,'plugin_action_links'));

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );	

		
		/*.   
		* loading admin modules
		*/
		$plugin_admin->admin_modules();
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.6.6
	 * @access   private
	 */
	private function define_public_hooks() 
	{
		$plugin_public = new Cookie_Law_Info_Public( $this->get_plugin_name(), $this->get_version(),$this);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public,'register_custom_post_type');


		$plugin_public->common_modules();

		//below hook's functions needs update
		$this->loader->add_action( 'init',$plugin_public,'other_plugin_compatibility');
  		$this->loader->add_action( 'wp_footer',$plugin_public,'cookielawinfo_inject_cli_script');
  		$this->loader->add_action('wp_head',$plugin_public,'include_user_accepted_cookielawinfo');
  		$this->loader->add_action('wp_footer',$plugin_public,'include_user_accepted_cookielawinfo_in_body');
	}


	/**
	 * Register all of the hooks related to the Third party plugin compatibility
	 * of the plugin.
	 *
	 * @since    1.7.2
	 * @access   public
	 */
	public function define_thrid_party_hooks() 
	{
		$plugin_third_party = new Cookie_Law_Info_Third_Party();
		$plugin_third_party->register_scripts();
	}
	

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.6.6
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.6.6
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.6.6
	 * @return    Cookie_Law_Info_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.6.6
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get current settings.
	 *
	 */
	public static function get_settings()
	{
		$settings = self::get_default_settings();
		//self::$stored_options=self::$stored_options && count(self::$stored_options)>0 ? self::$stored_options : get_option(CLI_SETTINGS_FIELD);
		self::$stored_options=get_option(CLI_SETTINGS_FIELD);
		if(!empty(self::$stored_options)) 
		{
			foreach(self::$stored_options as $key => $option ) 
			{
				$settings[$key] = self::sanitise_settings($key,$option );
			}
		}
		update_option( CLI_SETTINGS_FIELD, $settings );
		return $settings;
	}

	/**
	 * Generate tab head for settings page.
	 * method will translate the string to current language
	 */
	public static function generate_settings_tabhead($title_arr)
	{		
		$out_arr=array();
		foreach($title_arr as $k=>$v)
		{
			if($k=='cookie-law-info-buttons')
			{
				$out_arr[$k]=$v;
				//tab head for modules
				$out_arr=apply_filters('cli_module_settings_tabhead',$out_arr);
			}else
			{
				$out_arr[$k]=$v;
			}
		}		
		foreach($out_arr as $k=>$v)
		{			
			if(is_array($v))
			{
				$v=(isset($v[2]) ? $v[2] : '').__($v[0], 'cookie-law-info').' '.(isset($v[1]) ? $v[1] : '');
			}else
			{
				$v=__($v, 'cookie-law-info');
			}
		?>
			<a class="nav-tab" href="#<?php echo $k;?>"><?php echo $v; ?></a>
		<?php
		}
	}

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="")
	{
	?>
		<div class="cookie-law-info-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
		</div>
	<?php
	}

	/**
	 Returns default settings
	 If you override the settings here, be ultra careful to use escape characters!
	 */
	public static function get_default_settings($key='')
	{
		$settings_v0_9 = array(
			'animate_speed_hide' 			=> '500',
			'animate_speed_show' 			=> '500',
			'background' 					=> '#FFF',
			'background_url' 				=> '',
			'border' 						=> '#b1a6a6c2',
			'border_on'						=> true,
			'button_1_text'					=> 'Accept',
			'button_1_url' 					=> '#',
			'button_1_action' 				=> '#cookie_action_close_header',
			'button_1_link_colour' 			=> '#fff',
			'button_1_new_win' 				=> false,
			'button_1_as_button' 			=> true,
			'button_1_button_colour' 		=> '#000',
			'button_1_button_size' 			=> 'medium',
	            
			'button_2_text' 				=> 'Read More',
			'button_2_url' 					=> get_site_url(),
			'button_2_action' 				=> 'CONSTANT_OPEN_URL',
			'button_2_link_colour' 			=> '#444',
			'button_2_new_win' 				=> true,
			'button_2_as_button'			=> false,
			'button_2_button_colour' 		=> '#333',
			'button_2_button_size' 			=> 'medium',
			'button_2_url_type'				=>'url',
			'button_2_page'					=>get_option('wp_page_for_privacy_policy') ? get_option('wp_page_for_privacy_policy') : 0,
			'button_2_hidebar'					=>false,
	            
	        'button_3_text'					=> 'Reject',
			'button_3_url' 					=> '#',
			'button_3_action' 				=> '#cookie_action_close_header_reject',
			'button_3_link_colour' 			=> '#fff',
			'button_3_new_win' 				=> false,
			'button_3_as_button' 			=> true,
			'button_3_button_colour' 		=> '#000',
			'button_3_button_size' 			=> 'medium',
	            
	        'button_4_text'					=> 'Settings',
			'button_4_url' 					=> '#',
			'button_4_action' 				=> '#cookie_action_settings',
			'button_4_link_colour' 			=> '#fff',
			'button_4_new_win' 				=> false,
			'button_4_as_button' 			=> true,
			'button_4_button_colour' 		=> '#000',
			'button_4_button_size' 			=> 'medium',
	            
			'font_family' 					=> 'inherit', // Pick the family, not the easy name (see helper function below)
			'header_fix'                    => false,
			'is_on' 						=> true,
	        'is_eu_on' 						=> false,
	        'logging_on' 					=> false,
			'notify_animate_hide'			=> true,
			'notify_animate_show'			=> false,
			'notify_div_id' 				=> '#cookie-law-info-bar',
			'notify_position_horizontal'	=> 'right',	// left | right
			'notify_position_vertical'		=> 'bottom', // 'top' = header | 'bottom' = footer
			'notify_message'				=> addslashes ( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.[cookie_button margin="5px"][cookie_reject margin="5px"] [cookie_link margin="5px"]'),
			'scroll_close'                  => false,
			'scroll_close_reload'           => false,
	        'accept_close_reload'           => false,
	        'reject_close_reload'           => false,
			'showagain_background' 			=> '#fff',
			'showagain_border' 				=> '#000',
			'showagain_text'	 			=> addslashes('Privacy & Cookies Policy'),
			'showagain_div_id' 				=> '#cookie-law-info-again',
			'showagain_tab' 				=> true,
			'showagain_x_position' 			=> '100px',
			'text' 							=> '#000',
			'use_colour_picker'				=> true,
			'show_once_yn'					=> false,	// this is a new feature so default = switched off
			'show_once'						=> '10000',	// 8 seconds
			'is_GMT_on'						=> true,
			'as_popup'						=> false,  // version 1.7.1 onwards this option is merged with `cookie_bar_as`
			'popup_overlay'					=> true,  // 
			'bar_heading_text'				=>'',
			'cookie_bar_as'					=>'banner',
			'popup_showagain_position'		=>'bottom-right', //bottom-right | bottom-left | top-right | top-left
			'widget_position'		=>'left', //left | right
		);
		return $key!="" ? $settings_v0_9[$key] : $settings_v0_9;
	}

	/**
	 Returns JSON object containing the settings for the main script
	 REFACTOR / DEBUG: may need to use addslashes( ... ) else breaks JSON
	 */
	public static function get_json_settings() 
	{
	  $settings = self::get_settings();
	  
	  // DEBUG hex:
	  // preg_match('/^#[a-f0-9]{6}|#[a-f0-9]{3}$/i', $hex)
	  // DEBUG json_encode - issues across different versions of PHP!
	  // $str = json_encode( $slim_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
	  
	  // Slim down JSON objects to the bare bones:
	  $slim_settings = array(
	    'animate_speed_hide'      => $settings['animate_speed_hide'],
	    'animate_speed_show'      => $settings['animate_speed_show'],
	    'background'          => $settings['background'],
	    'border'            => $settings['border'],
	    'border_on'           => false, //$settings['border_on'],
	    'button_1_button_colour'    => $settings['button_1_button_colour'],
	    'button_1_button_hover'     => (self::su_hex_shift( $settings['button_1_button_colour'], 'down', 20 )),
	    'button_1_link_colour'      => $settings['button_1_link_colour'],
	    'button_1_as_button'      => $settings['button_1_as_button'],
	    'button_1_new_win'      => $settings['button_1_new_win'],
	    'button_2_button_colour'    => $settings['button_2_button_colour'],
	    'button_2_button_hover'     => (self::su_hex_shift( $settings['button_2_button_colour'], 'down', 20 )),
	    'button_2_link_colour'      => $settings['button_2_link_colour'],
	    'button_2_as_button'      => $settings['button_2_as_button'],
	    'button_2_hidebar'		 =>$settings['button_2_hidebar'],
	    'button_3_button_colour'    => $settings['button_3_button_colour'],
	    'button_3_button_hover'     => (self::su_hex_shift( $settings['button_3_button_colour'], 'down', 20 )),
	    'button_3_link_colour'      => $settings['button_3_link_colour'],
	    'button_3_as_button'      => $settings['button_3_as_button'],
	    'button_3_new_win'      => $settings['button_3_new_win'],
	    'button_4_button_colour'    => $settings['button_4_button_colour'],
	    'button_4_button_hover'     => (self::su_hex_shift( $settings['button_4_button_colour'], 'down', 20 )),
	    'button_4_link_colour'      => $settings['button_4_link_colour'],
	    'button_4_as_button'      => $settings['button_4_as_button'],            
	    'font_family'         => $settings['font_family'],
	    'header_fix'                    => $settings['header_fix'],
	    'notify_animate_hide'     => $settings['notify_animate_hide'],
	    'notify_animate_show'     => $settings['notify_animate_show'],
	    'notify_div_id'         => $settings['notify_div_id'],
	    'notify_position_horizontal'  => $settings['notify_position_horizontal'],
	    'notify_position_vertical'    => $settings['notify_position_vertical'],
	    'scroll_close'                  => $settings['scroll_close'],
	    'scroll_close_reload'           => $settings['scroll_close_reload'],
	    'accept_close_reload'           => $settings['accept_close_reload'],
	    'reject_close_reload'           => $settings['reject_close_reload'],
	    'showagain_tab'         => $settings['showagain_tab'],
	    'showagain_background'      => $settings['showagain_background'],
	    'showagain_border'        => $settings['showagain_border'],
	    'showagain_div_id'        => $settings['showagain_div_id'],
	    'showagain_x_position'      => $settings['showagain_x_position'],
	    'text'              => $settings['text'],
	    'show_once_yn'          => $settings['show_once_yn'],
	    'show_once'           => $settings['show_once'],
	    'logging_on'=>$settings['logging_on'],
	    'as_popup'=>$settings['as_popup'],
	    'popup_overlay'=>$settings['popup_overlay'],
	    'bar_heading_text'=>$settings['bar_heading_text'],
	    'cookie_bar_as'=>$settings['cookie_bar_as'],
		'popup_showagain_position'=>$settings['popup_showagain_position'],
		'widget_position'=>$settings['widget_position'],
	  );
	  $str = json_encode( $slim_settings );
	  /*
	  DEBUG: 
	  if ( $str == null | $str == '') {
	    $str = 'error: json is empty';
	  }
	  */
	  return $str;
	}

	/**
 	Returns sanitised content based on field-specific rules defined here
	 Used for both read AND write operations
	 */
	public static function sanitise_settings($key, $value) 
	{
		$ret = null;		
		switch ($key) {
			// Convert all boolean values from text to bool:
			case 'is_on':
			case 'is_reject_on':
	        case 'is_eu_on':
	        case 'logging_on':    
			case 'border_on':
			case 'notify_animate_show':
			case 'notify_animate_hide':
			case 'showagain_tab':
			case 'use_colour_picker':
			case 'button_1_new_win':
			case 'button_1_as_button':
			case 'button_2_new_win':
			case 'button_2_as_button':
			case 'button_2_hidebar':
	        case 'button_3_new_win':
			case 'button_3_as_button':
	        case 'button_4_new_win':
			case 'button_4_as_button':
			case 'scroll_close':
			case 'scroll_close_reload':
	        case 'accept_close_reload':
	        case 'reject_close_reload':
			case 'show_once_yn':
			case 'header_fix':
			case 'is_GMT_on':
			case 'as_popup':
			case 'popup_overlay':

				if ( $value == 'true' || $value === true ) 
				{
					$ret = true;
				}
				elseif ( $value == 'false' || $value === false ) 
				{
					$ret = false;
				}
				else 
				{
					// Unexpected value returned from radio button, go fix the HTML.
					// Failover = assign null.
					$ret = 'fffffff';
				}
				break;
			// Any hex colour e.g. '#f00', '#FE01ab' '#ff0000' but not 'f00' or 'ff0000':
			case 'background':
			case 'text':
			case 'border':
			case 'showagain_background':
			case 'showagain_border':
			case 'button_1_link_colour':
			case 'button_1_button_colour':
			case 'button_2_link_colour':
			case 'button_2_button_colour':
	        case 'button_3_link_colour':
			case 'button_3_button_colour':   
	        case 'button_4_link_colour':
			case 'button_4_button_colour': 
				if ( preg_match( '/^#[a-f0-9]{6}|#[a-f0-9]{3}$/i', $value ) ) 
				{
					// Was: '/^#([0-9a-fA-F]{1,2}){3}$/i' which allowed e.g. '#00dd' (error)
					$ret =  $value;
				}
				else {
					// Failover = assign '#000' (black)
					$ret =  '#000';
				}
				break;
			// Allow some HTML, but no JavaScript. Note that deliberately NOT stripping out line breaks here, that's done when sending JavaScript parameter elsewhere:
			case 'notify_message':
			case 'bar_heading_text':
				$ret = wp_kses( $value,self::allowed_html(), self::allowed_protocols() );
				break;
			// URLs only:
			case 'button_1_url':
			case 'button_2_url':
	                case 'button_3_url':
	                case 'button_4_url':                    
				$ret = esc_url( $value );
				break;
			// Basic sanitisation for all the rest:
			default:
				$ret = sanitize_text_field( $value );
				break;
		}
	        if(('is_eu_on' === $key || 'logging_on' == $key) && 'fffffff' === $ret) $ret = false;
		return $ret;
	}

	public static function get_non_necessary_cookie_ids()
	{

	    global $wpdb;	    
	    $args = array(
	            'post_type' => CLI_POST_TYPE, 
	            'meta_query' => array(
								    array(
								      'key' => '_cli_cookie_sensitivity',
								      'value' => 'non-necessary'
								    )
								)
	            
	            );
	  $posts = get_posts($args); 
	  
	  if ( !$posts ) {
	    return;
	  }
	        $cookie_slugs = array();    
	        
	        if($posts){    
	            foreach( $posts as $post )
	          {
	                $cookie_slugs[] = get_post_meta( $post->ID, "_cli_cookie_slugid", true);
	          }   
	        }        
	        
	        return $cookie_slugs;
	}

	/**
	 * Color shift a hex value by a specific percentage factor
	 * By http://www.phpkode.com/source/s/shortcodes-ultimate/shortcodes-ultimate/lib/color.php
	 * Adapted by Richard Ashby; amended error handling to use failovers not messages, so app continues
	 *
	 * @param string $supplied_hex Any valid hex value. Short forms e.g. #333 accepted.
	 * @param string $shift_method How to shift the value e.g( +,up,lighter,>)
	 * @param integer $percentage Percentage in range of [0-100] to shift provided hex value by
	 * @return string shifted hex value
	 * @version 1.0 2008-03-28
	 */
	public static function su_hex_shift( $supplied_hex, $shift_method, $percentage = 50 ) {
	  $shifted_hex_value = null;
	  $valid_shift_option = FALSE;
	  $current_set = 1;
	  $RGB_values = array( );
	  $valid_shift_up_args = array( 'up', '+', 'lighter', '>' );
	  $valid_shift_down_args = array( 'down', '-', 'darker', '<' );
	  $shift_method = strtolower( trim( $shift_method ) );

	  // Check Factor
	  if ( !is_numeric( $percentage ) || ($percentage = ( int ) $percentage) < 0 || $percentage > 100 ) {
	    //trigger_error( "Invalid factor", E_USER_ERROR );
	    return $supplied_hex;
	  }

	  // Check shift method
	  foreach ( array( $valid_shift_down_args, $valid_shift_up_args ) as $options ) {
	    foreach ( $options as $method ) {
	      if ( $method == $shift_method ) {
	        $valid_shift_option = !$valid_shift_option;
	        $shift_method = ( $current_set === 1 ) ? '+' : '-';
	        break 2;
	      }
	    }
	    ++$current_set;
	  }

	  if ( !$valid_shift_option ) {
	    //trigger_error( "Invalid shift method", E_USER_ERROR );
	    return $supplied_hex;
	  }

	  // Check Hex string
	  switch ( strlen( $supplied_hex = ( str_replace( '#', '', trim( $supplied_hex ) ) ) ) ) {
	    case 3:
	      if ( preg_match( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', $supplied_hex ) ) {
	        $supplied_hex = preg_replace( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', '\\1\\1\\2\\2\\3\\3', $supplied_hex );
	      } else {
	        //trigger_error( "Invalid hex color value", E_USER_ERROR );
	        return $supplied_hex;
	      }
	      break;
	    case 6:
	      if ( !preg_match( '/^[0-9a-f]{2}[0-9a-f]{2}[0-9a-f]{2}$/i', $supplied_hex ) ) {
	        //trigger_error( "Invalid hex color value", E_USER_ERROR );
	        return $supplied_hex;
	      }
	      break;
	    default:
	      //trigger_error( "Invalid hex color length", E_USER_ERROR );
	      return $supplied_hex;
	  }

	  // Start shifting
	  $RGB_values['R'] = hexdec( $supplied_hex{0} . $supplied_hex{1} );
	  $RGB_values['G'] = hexdec( $supplied_hex{2} . $supplied_hex{3} );
	  $RGB_values['B'] = hexdec( $supplied_hex{4} . $supplied_hex{5} );

	  foreach ( $RGB_values as $c => $v ) {
	    switch ( $shift_method ) {
	      case '-':
	        $amount = round( ((255 - $v) / 100) * $percentage ) + $v;
	        break;
	      case '+':
	        $amount = $v - round( ($v / 100) * $percentage );
	        break;
	      default:
	        // trigger_error( "Oops. Unexpected shift method", E_USER_ERROR );
	        return $supplied_hex;
	    }

	    $shifted_hex_value .= $current_value = (
	      strlen( $decimal_to_hex = dechex( $amount ) ) < 2
	      ) ? '0' . $decimal_to_hex : $decimal_to_hex;
	  }

	  return '#' . $shifted_hex_value;
	}

	/**
	 Returns list of HTML tags allowed in HTML fields for use in declaration of wp_kset field validation.
	 
	 Deliberately allows class and ID declarations to assist with custom CSS styling.
	 To customise further, see the excellent article at: http://ottopress.com/2010/wp-quickie-kses/
	 */
	public static function allowed_html() {
		$allowed_html = array(
			// Allowed:		<a href="" id="" class="" title="" target="">...</a>
			// Not allowed:	<a href="javascript(...);">...</a>
			'a' => array(
				'href' => array(),
				'id' => array(),
				'class' => array(),
				'title' => array(),
				'target' => array(),
				'rel' => array(),
				'style' => array()
			),
			'b' => array(),
			'br' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'div' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'em' => array (
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'i' => array(),
			'img' => array(
				'src' => array(),
				'id' => array(),
				'class' => array(),
				'alt' => array(),
				'style' => array()				
			),
			'p' => array (
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'span' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'strong' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'label' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			)
		);
		$html5_tags=array('article','section','aside','details','figcaption','figure','footer','header','main','mark','nav','summary','time');
		foreach($html5_tags as $html5_tag)
		{
			$allowed_html[$html5_tag]=array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			);
		}
		return $allowed_html;
	}


	/**
	 Returns list of allowed protocols, for use in declaration of wp_kset field validation.
	 N.B. JavaScript is specifically disallowed for security reasons.
	 Don't even trust your own database, as you don't know if another plugin has written to your settings.
	 */
	public static function allowed_protocols() {
		// Additional options: 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'
		return array ('http', 'https');
	}


	/**
	 * Check if GTM is active
	 **/
	public static function cli_is_active_GTM()
	{
		
		if ( in_array( 'duracelltomi-google-tag-manager/duracelltomi-google-tag-manager-for-wordpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		    return true;
		}
	}
	public static function cli_get_client_ip() 
    {

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /*
	*
	* Patch script while updating versions
    */
    public static function cli_patches()
    {
    	$options=self::get_settings();

    	//========bar as widget=========@since 1.7.1
    	if($options['cookie_bar_as']=='banner' && $options['as_popup']==true) //the site in popup mode
		{
			$options['cookie_bar_as']='popup';
			$options['as_popup']=false;
			$options['popup_showagain_position']=$options['notify_position_vertical'].'-'.$options['notify_position_horizontal'];
			update_option( CLI_SETTINGS_FIELD,$options);
		}



    	//========reject button missing issue=========@since 1.6.7
    	$message_bar_text=$options['notify_message'];
    	//user turned on the reject button with his previous settings
    	if(isset($options['is_reject_on']) && $options['is_reject_on']==true)
    	{  
    		if(strpos($message_bar_text,'cookie_reject')===false) //user not manualy inserted the code
    		{
    			$pattern = get_shortcode_regex();
    			if(preg_match_all ('/'. $pattern .'/s',$message_bar_text, $matches))
    			{
    				$shortcode_arr=$matches[0];
    				foreach($shortcode_arr as $shrtcode)
    				{
    					if(strpos($shrtcode,'cookie_button')!==false)
    					{
    						
    						$options['notify_message']=str_replace($shrtcode,$shrtcode.' [cookie_reject]',$message_bar_text);
    						$options['is_reject_on']=false;
    						update_option( CLI_SETTINGS_FIELD, $options );
    						break;
    					}
    				}
    			}
    		}else
    		{
    			$options['is_reject_on']=false;
    			update_option( CLI_SETTINGS_FIELD, $options );
    		}
    	}
    	//---------reject button missing issue------------

    	//bar heading text issue @since 1.6.7
    	$bar_version='1.6.6';
    	$bar_heading_version = get_option('cli_heading_version');
    	if($bar_heading_version!=$bar_version)
    	{
    		if(isset($options['bar_heading_text']) && $options['bar_heading_text']=='This website uses cookies')
    		{
    			$options['bar_heading_text']='';
    			update_option( CLI_SETTINGS_FIELD, $options );
    			update_option('cli_heading_version', $bar_version);
    		}
    	}
    }
}
