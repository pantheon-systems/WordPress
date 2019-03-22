<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cookielawinfo.com/
 * @since      1.6.6
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/admin
 * @author     WebToffee <info@webtoffee.com>
 */
class Cookie_Law_Info_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.6.6
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.6.6
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $plugin_obj;

	/*
	 * admin module list, Module folder and main file must be same as that of module name
	 * Please check the `admin_modules` method for more details
	 */
	private $modules=array(
		'cli-policy-generator'
	);

	public static $existing_modules=array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.6.6
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version,$plugin_obj ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_obj = $plugin_obj;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.6.6
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cookie_Law_Info_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cookie_Law_Info_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cookie-law-info-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.6.6
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cookie_Law_Info_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cookie_Law_Info_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cookie-law-info-admin.js', array( 'jquery' ,'wp-color-picker'), $this->version, false );

	}

	/**
	 Registers admin modules	 
	 */
	public function admin_modules()
	{
		foreach ($this->modules as $module) //loop through module list and include its file
		{
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";
			if(file_exists($module_file))
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			} 
		}
	}

	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}

	/**
	 Registers menu options
	 Hooked into admin_menu
	 */
	public function admin_menu() {
		global $submenu;
		add_submenu_page(
			'edit.php?post_type='.CLI_POST_TYPE,
			__('Cookie Law Settings','cookie-law-info'),
			__('Cookie Law Settings','cookie-law-info'),
			'manage_options',
			'cookie-law-info',
			array($this,'admin_settings_page')
		);
		add_submenu_page(
			'edit.php?post_type='.CLI_POST_TYPE,
			__('Non-necessary Cookie','cookie-law-info'),
			__('Non-necessary Cookie','cookie-law-info'),
			'manage_options',
			'cookie-law-info-thirdparty',
			array($this,'admin_non_necessary_cookie_page')
		);
		//rearrange settings menu
		if(isset($submenu) && !empty($submenu) && is_array($submenu))
		{
			$out=array();
			$back_up_settings_menu=array();
			if(isset($submenu['edit.php?post_type='.CLI_POST_TYPE]) && is_array($submenu['edit.php?post_type='.CLI_POST_TYPE]))
			{
				foreach ($submenu['edit.php?post_type='.CLI_POST_TYPE] as $key => $value) 
				{
					if($value[2]=='cookie-law-info')
					{
						$back_up_settings_menu=$value;
					}else
					{
						$out[$key]=$value;
					}
				}
				array_unshift($out,$back_up_settings_menu);
				$submenu['edit.php?post_type='.CLI_POST_TYPE]=$out;
			}
		}
	}

	public function plugin_action_links( $links ) 
	{
	   $links[] = '<a href="'. get_admin_url(null,'edit.php?post_type='.CLI_POST_TYPE.'&page=cookie-law-info') .'">'.__('Settings','cookie-law-info').'</a>';
	   $links[] = '<a href="https://www.webtoffee.com/product/gdpr-cookie-consent/" target="_blank">'.__('Support','cookie-law-info').'</a>';
	   $links[] = '<a href="https://www.webtoffee.com/product/gdpr-cookie-consent/" target="_blank">'.__('Premium Upgrade','cookie-law-info').'</a>';
	   return $links;
	}


	public function admin_non_necessary_cookie_page()
	{
	    wp_enqueue_style($this->plugin_name);
	    wp_enqueue_script($this->plugin_name);
	    $options = array('thirdparty_on_field',
	        'thirdparty_head_section',
	        'thirdparty_body_section',
			//'thirdparty_footer_section',
	    );
	    // Get options:
	    $stored_options = get_option('cookielawinfo_thirdparty_settings', array(
	        'thirdparty_on_field' => false,
	        'thirdparty_head_section' => '',
	        'thirdparty_body_section' => '',
			//'thirdparty_footer_section' => '',
	    ));

	    // Check if form has been set:
	    if (
	    	isset($_POST['update_thirdparty_settings_form']) || //normal php submit
	    	isset($_POST['cli_non-necessary_ajax_update'])
		) 
	    {
	        // Check nonce:
	        check_admin_referer('cookielawinfo-update-thirdparty');
	        foreach ($options as $key) 
	        {
	            if (isset($_POST[$key])) 
	            {
	                // Store sanitised values only:
	                $stored_options[$key]=wp_unslash($_POST[$key]);
	            }
	        }
	        update_option('cookielawinfo_thirdparty_settings', $stored_options);
	        echo '<div class="updated"><p><strong>';
	        echo __('Settings Updated.','cookie-law-info');
	        echo '</strong></p></div>';
	        if(!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
	        {	            
	        	exit();
	        }
	    }

	    $stored_options = get_option('cookielawinfo_thirdparty_settings', array(
	        'thirdparty_on_field' => false,
	        'thirdparty_head_section' => '',
	        'thirdparty_body_section' => '',
			//'thirdparty_footer_section' => '',
	    ));
	    require_once plugin_dir_path( __FILE__ ).'views/admin_non_necessary_cookie.php';
	}


	/*
	* admin settings page
	*/
	public function admin_settings_page()
	{
		wp_enqueue_style($this->plugin_name);
		wp_enqueue_script($this->plugin_name);
		// Lock out non-admins:
		if (!current_user_can('manage_options')) 
		{
		    wp_die(__('You do not have sufficient permission to perform this operation', 'cookie-law-info'));
		}
		// Get options:
    	$the_options =Cookie_Law_Info::get_settings();
    	// Check if form has been set:
	    if(isset($_POST['update_admin_settings_form']) || //normal php submit
	    (isset($_POST['cli_settings_ajax_update']) && $_POST['cli_settings_ajax_update']=='update_admin_settings_form'))  //ajax submit
	    {
	        // Check nonce:
	        check_admin_referer('cookielawinfo-update-' . CLI_SETTINGS_FIELD);

	        //module settings saving hook
	        do_action('cli_module_save_settings');

	        foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key . '_field'])) 
	            {
	                // Store sanitised values only:
	                $the_options[$key] = Cookie_Law_Info::sanitise_settings($key, $_POST[$key . '_field']);
	            }
	        }
	        update_option(CLI_SETTINGS_FIELD, $the_options);
	        echo '<div class="updated"><p><strong>' . __('Settings Updated.', 'cookie-law-info') . '</strong></p></div>';
	    } 
	    elseif (isset($_POST['delete_all_settings']) || //normal php submit
	    (isset($_POST['cli_settings_ajax_update']) && $_POST['cli_settings_ajax_update']=='delete_all_settings'))  //ajax submit 
	    {
	        // Check nonce:
	        check_admin_referer('cookielawinfo-update-' . CLI_SETTINGS_FIELD);
	        $this->delete_settings();
	        //$the_options = Cookie_Law_Info::get_settings();
	        //exit();
	    } 
	    elseif (isset($_POST['revert_to_previous_settings']))  //disabled on new update
	    {
	        if (!$this->copy_old_settings_to_new()) 
	        {
	            echo '<h3>' . __('ERROR MIGRATING SETTINGS (ERROR: 2)', 'cookie-law-info') . '</h3>';
	        }
	        $the_options = Cookie_Law_Info::get_settings();;
	    }
	    if(!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
        {	            
        	exit();
        }
		require_once plugin_dir_path( __FILE__ ).'partials/cookie-law-info-admin_settings.php';
	}

	/**
	 Add custom meta boxes to Cookie Audit custom post type.
	 	- Cookie Type (e.g. session, permanent)
	 	- Cookie Duration (e.g. 2 hours, days, years, etc)
	 */
	public function add_meta_box() {
	    
	    add_meta_box("_cli_cookie_slugid", "Cookie ID", array($this,"metabox_cookie_slugid"), "cookielawinfo", "side", "default");
		add_meta_box("_cli_cookie_type", "Cookie Type", array($this,"metabox_cookie_type"), "cookielawinfo", "side", "default");
		add_meta_box("_cli_cookie_duration", "Cookie Duration", array($this,"metabox_cookie_duration"), "cookielawinfo", "side", "default");
	    add_meta_box("_cli_cookie_sensitivity", "Cookie Sensitivity", array($this,"metabox_cookie_sensitivity"), "cookielawinfo", "side", "default");
	}

	/** Display the custom meta box for cookie_slugid */
	public function metabox_cookie_slugid() 
	{
		global $post;
		$custom = get_post_custom( $post->ID );
		$cookie_slugid = ( isset ( $custom["_cli_cookie_slugid"][0] ) ) ? $custom["_cli_cookie_slugid"][0] : '';
		?>
		<label>Cookie ID:</label>
		<input name="_cli_cookie_slugid" value="<?php echo sanitize_text_field( $cookie_slugid ); ?>" style="width:95%;" />
		<?php
	}

	/** Display the custom meta box for cookie_type */
	public function metabox_cookie_type() 
	{
		global $post;
		$custom = get_post_custom( $post->ID );
		$cookie_type = ( isset ( $custom["_cli_cookie_type"][0] ) ) ? $custom["_cli_cookie_type"][0] : '';
		?>
		<label>Cookie Type: (persistent, session, third party )</label>
		<input name="_cli_cookie_type" value="<?php echo sanitize_text_field( $cookie_type ); ?>" style="width:95%;" />
		<?php
	}

	/** Display the custom meta box for cookie_duration */
	public function metabox_cookie_duration() {
		global $post;
		$custom = get_post_custom( $post->ID );
		$cookie_duration = ( isset ( $custom["_cli_cookie_duration"][0] ) ) ? $custom["_cli_cookie_duration"][0] : '';
		?>
		<label>Cookie Duration:</label>
		<input name="_cli_cookie_duration" value="<?php echo sanitize_text_field( $cookie_duration ); ?>" style="width:95%;" />
		<?php
	}

	/** Display the custom meta box for cookie_sensitivity */
	public function metabox_cookie_sensitivity() 
	{
		global $post;
		$custom = get_post_custom( $post->ID );
		$cookie_sensitivity = ( isset ( $custom["_cli_cookie_sensitivity"][0] ) ) ? $custom["_cli_cookie_sensitivity"][0] : '';
		?>
		<label>Cookie Sensitivity: ( necessary , non-necessary )</label>
		<input name="_cli_cookie_sensitivity" value="<?php echo sanitize_text_field( $cookie_sensitivity ); ?>" style="width:95%;" />
		<?php
	}

	/** Saves all form data from custom post meta boxes, including saitisation of input */
	public function save_custom_metaboxes() 
	{
		global $post;	
		if ( isset ( $_POST["_cli_cookie_type"] ) ) {
			update_post_meta( $post->ID, "_cli_cookie_type", sanitize_text_field( $_POST["_cli_cookie_type"] ) );
	        }
	        if ( isset ( $_POST["_cli_cookie_type"] ) ) {
			update_post_meta( $post->ID, "_cli_cookie_duration", sanitize_text_field( $_POST["_cli_cookie_duration"] ) );
		}
	        if ( isset ( $_POST["_cli_cookie_sensitivity"] ) ) {
			update_post_meta( $post->ID, "_cli_cookie_sensitivity", sanitize_text_field( $_POST["_cli_cookie_sensitivity"] ) );
		}
	        if ( isset ( $_POST["_cli_cookie_slugid"] ) ) {
			update_post_meta( $post->ID, "_cli_cookie_slugid", sanitize_text_field( $_POST["_cli_cookie_slugid"] ) );
		}
	}

	/** Apply column names to the custom post type table */
	public function manage_edit_columns( $columns ) 
	{
		$columns = array(
			"cb" 			=> "<input type=\"checkbox\" />",
			"title"			=> "Cookie Name",
			"type"			=> "Type",
			"duration"		=> "Duration",
	        "sensitivity"	=> "Sensitivity",
	        "slugid"		=> "ID",
			"description"   => "Description"
		);
		return $columns;
	}

	/** Add column data to custom post type table columns */
	public function manage_posts_custom_columns( $column, $post_id=0 ) 
	{
		global $post;
		
		switch ( $column ) {
		case "description":
	            
	                $content_post = get_post($post_id);
	                if($content_post){
	                echo $content_post->post_content;
	                }else{
	                    echo '---';
	                }
			break;
		case "type":
			$custom = get_post_custom();
			if ( isset ( $custom["_cli_cookie_type"][0] ) ) {
				echo $custom["_cli_cookie_type"][0];
			}
			break;      
		case "duration":
			$custom = get_post_custom();
			if ( isset ( $custom["_cli_cookie_duration"][0] ) ) {
				echo $custom["_cli_cookie_duration"][0];
			}
			break;
	        case "sensitivity":
			$custom = get_post_custom();
			if ( isset ( $custom["_cli_cookie_sensitivity"][0] ) ) {
				echo $custom["_cli_cookie_sensitivity"][0];
			}
			break;
	        case "slugid":
			$custom = get_post_custom();
			if ( isset ( $custom["_cli_cookie_slugid"][0] ) ) {
				echo $custom["_cli_cookie_slugid"][0];
			}
			break;
		}	        
	}

	function remove_cli_addnew_link() 
	{
	    global $submenu;
	    if(isset($submenu) && !empty($submenu) && is_array($submenu))
		{
	    	unset($submenu['edit.php?post_type='.CLI_POST_TYPE][10]);
		}
	}
	

	/** Updates latest version number of plugin */
	public function update_to_latest_version_number() {
		update_option( CLI_MIGRATED_VERSION, CLI_LATEST_VERSION_NUMBER );
	}
	/**
	 Delete the values in all fields
	 WARNING - this has a predictable result i.e. will delete saved settings! Once deleted,
	 the get_admin_options() function will not find saved settings so will return default values
	 */
	public function delete_settings() 
	{
		if(defined( 'CLI_ADMIN_OPTIONS_NAME' )) 
		{
			delete_option( CLI_ADMIN_OPTIONS_NAME );
		}
		if ( defined ( 'CLI_SETTINGS_FIELD' ) ) 
		{
			delete_option( CLI_SETTINGS_FIELD );
		}
	}

	public function copy_old_settings_to_new() {
		$new_settings = Cookie_Law_Info::get_settings();
		$old_settings = get_option( CLI_ADMIN_OPTIONS_NAME );
		
		if ( empty( $old_settings ) ) {
			// Something went wrong:
			return false;
		}
		else {
			// Copy over settings:
			$new_settings['background'] 			= $old_settings['colour_bg'];
			$new_settings['border'] 				= $old_settings['colour_border'];
			$new_settings['button_1_action']		= 'CONSTANT_OPEN_URL';
			$new_settings['button_1_text'] 			= $old_settings['link_text'];
			$new_settings['button_1_url'] 			= $old_settings['link_url'];
			$new_settings['button_1_link_colour'] 	= $old_settings['colour_link'];
			$new_settings['button_1_new_win'] 		= $old_settings['link_opens_new_window'];
			$new_settings['button_1_as_button']		= $old_settings['show_as_button'];
			$new_settings['button_1_button_colour']	= $old_settings['colour_button_bg'];
			$new_settings['notify_message'] 		= $old_settings['message_text'];
			$new_settings['text'] 					= $old_settings['colour_text'];
			
			// Save new values:
			update_option( CLI_SETTINGS_FIELD, $new_settings );
		}
		return true;
	}
	/** Migrates settings from version 0.8.3 to version 0.9 */
	public function migrate_to_new_version() {
		
		if ( $this->has_migrated() ) {
			return false;
		}
		
		if ( !$this->copy_old_settings_to_new() ) {
			return false;
		}
		
		// Register that have completed:
		$this->update_to_latest_version_number();
		return true;
	}

	/** Returns true if user is on latest version of plugin */
	public function has_migrated() {
		// Test for previous version. If doesn't exist then safe to say are fresh install:
		$old_settings = get_option( CLI_ADMIN_OPTIONS_NAME );
		if ( empty( $old_settings ) ) {
			return true;
		}
		// Test for latest version number
		$version = get_option( CLI_MIGRATED_VERSION );
		if ( empty ( $version ) ) {
			// No version stored; not yet migrated:
			return false;
		}
		if ( $version == CLI_LATEST_VERSION_NUMBER ) {
			// Are on latest version
			return true;
		}
		echo 'VERSION: ' . $version . '<br /> V2: ' . CLI_LATEST_VERSION_NUMBER;
		// If you got this far then you're on an inbetween version
		return false;
	}

	/**
	 Prints a combobox based on options and selected=match value
	 
	 Parameters:
	 	$options = array of options (suggest using helper functions)
	 	$selected = which of those options should be selected (allows just one; is case sensitive)
	 
	 Outputs (based on array ( $key => $value ):
	 	<option value=$value>$key</option>
	 	<option value=$value selected="selected">$key</option>
	 */
	public function print_combobox_options( $options, $selected ) 
	{
		foreach ( $options as $key => $value ) {
			echo '<option value="' . $value . '"';
			if ( $value == $selected ) {
				echo ' selected="selected"';
			}
			echo '>' . $key . '</option>';
		}
	}

	/**
	 Returns list of available jQuery actions
	 Used by buttons/links in header
	 */
	public function get_js_actions() {
		$js_actions = array(
			'Close Header' => '#cookie_action_close_header',
			'Open URL' => 'CONSTANT_OPEN_URL'	// Don't change this value, is used by jQuery
		);
		return $js_actions;
	}

	/**
	 Returns button sizes (dependent upon CSS implemented - careful if editing)
	 Used when printing admin form (for combo boxes)
	 */
	public function get_button_sizes() {
		$sizes = Array(
			'Extra Large'	=> 'super',
			'Large'			=> 'large',
			'Medium'		=> 'medium',
			'Small'			=> 'small'
		);
		return $sizes;
	}

	/**
	 Function returns list of supported fonts
	 Used when printing admin form (for combo box)
	 */
	public function get_fonts() {
		$fonts = Array(
			'Default theme font'	=> 'inherit',
			'Sans Serif' 			=> 'Helvetica, Arial, sans-serif',
			'Serif' 				=> 'Georgia, Times New Roman, Times, serif',
			'Arial'					=> 'Arial, Helvetica, sans-serif',
			'Arial Black' 			=> 'Arial Black,Gadget,sans-serif',
			'Georgia' 				=> 'Georgia, serif',
			'Helvetica' 			=> 'Helvetica, sans-serif',
			'Lucida' 				=> 'Lucida Sans Unicode, Lucida Grande, sans-serif',
			'Tahoma' 				=> 'Tahoma, Geneva, sans-serif',
			'Times New Roman' 		=> 'Times New Roman, Times, serif',
			'Trebuchet' 			=> 'Trebuchet MS, sans-serif',
			'Verdana' 				=> 'Verdana, Geneva'
		);
		return $fonts;
	}

}
