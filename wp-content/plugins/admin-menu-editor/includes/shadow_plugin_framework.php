<?php

/**
 * @author W-Shadow
 * @copyright 2008-2012
 */
 
//Load JSON functions for PHP < 5.2
if ( !(function_exists('json_encode') && function_exists('json_decode')) && !class_exists('Services_JSON') ){
	$class_json_path = ABSPATH . WPINC . '/class-json.php';
	if ( file_exists($class_json_path) ){
		require $class_json_path;
	}
}

class MenuEd_ShadowPluginFramework {
	public static $framework_version = '0.4.1';
	
	public $is_mu_plugin = null; //True if installed in the mu-plugins directory, false otherwise
	
	protected $options = array();
	public $option_name = ''; //should be set or overridden by the plugin
	protected $defaults = array(); //should be set or overridden by the plugin
	protected $sitewide_options = false; //WPMU only : save the setting in a site-wide option
	protected $serialize_with_json = false; //Use the JSON format for option storage 
	
	public $plugin_file = ''; //Filename of the plugin.
	public $plugin_basename = ''; //Basename of the plugin, as returned by plugin_basename().
	public $plugin_dir_url = ''; //The URL of the plugin's folder
	
	protected $magic_hooks = false; //Automagically set up hooks for all methods named "hook_[hookname]" .
	protected $magic_hook_priority = 10; //Priority for magically set hooks.
	
	protected $settings_link = ''; //If set, this will be automatically added after "Deactivate"/"Edit". 
	
  /**
   * Class constructor. Populates some internal fields, then calls the plugin's own 
   * initializer (if any).
   *
   * @param string $plugin_file Plugin's filename. Usually you can just use __FILE__.
   * @param string $option_name
   */
	function __construct( $plugin_file = '', $option_name = null ){
		if ($plugin_file == ''){
			//Try to guess the name of the file that included this file.
			//Not implemented yet.
		}
		$this->option_name = $option_name;
		
		if ( is_null($this->is_mu_plugin) )
			$this->is_mu_plugin = $this->is_in_wpmu_plugin_dir($plugin_file);
		
		$this->plugin_file = $plugin_file;
		$this->plugin_basename = plugin_basename($this->plugin_file);
		
		$this->plugin_dir_url = rtrim(plugin_dir_url($this->plugin_file), '/');

		/************************************
				Add the default hooks
		************************************/
		add_action('activate_'.$this->plugin_basename, array($this,'activate'));
		add_action('deactivate_'.$this->plugin_basename, array($this,'deactivate'));
		
		$this->init();        //Call the plugin's init() function
		$this->init_finish(); //Complete initialization by loading settings, etc
	}
	
	/**
	 * Init the plugin. Should be overridden in a sub-class.
	 * Called by the class constructor.
	 * 
	 * @return void
	 */
	function init(){
		//Do nothing.
	}
	
	/**
	 * Initialize settings and set up magic hooks. 
	 * 
	 * @return void
	 */
	function init_finish(){
		/************************************
				Load settings
		************************************/
		//The provided $option_name overrides the default only if it is set to something useful
		if ( $this->option_name == '' )  {
			//Generate a unique name 
			$this->option_name = 'plugin_'.md5($this->plugin_basename);
		}
		
		//Do we need to load the plugin's settings?
		if ($this->option_name != null){
			$this->load_options();
		}
		
		//Add a "Settings" action link
		if ($this->settings_link)
			add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
		
		if ($this->magic_hooks)
			$this->set_magic_hooks();
	}
	
  /**
   * Load the plugin's configuration.
   * Loads the specified option into $this->options, substituting defaults where necessary.
   *
   * @param string $option_name Optional. The slug of the option to load. If not set, the value of $this->option_name will be used instead.
   * @return boolean TRUE if options were loaded okay and FALSE otherwise. 
   */
	function load_options($option_name = null){
		if ( empty($option_name) ){
			$option_name = $this->option_name;
		}
		
		if ( $this->sitewide_options ) {
			$this->options = get_site_option($option_name);
		} else {
			$this->options = get_option($option_name);
		}
		
		if ( $this->serialize_with_json || is_string($this->options) ){
			$this->options = $this->json_decode($this->options, true);
		}
		
		if(!is_array($this->options)){
			$this->options = $this->defaults;
			return false;
		} else {
			$this->options = array_merge($this->defaults, $this->options);
			return true;
		}
	}
	
  /**
   * ShadowPluginFramework::save_options()
   * Saves the $options array to the database.
   *
   * @return bool
   */
	function save_options(){
		if ($this->option_name) {
			$stored_options = $this->options;
			if ( $this->serialize_with_json ){
				$stored_options = $this->json_encode($stored_options);
			}
			
			if ( $this->sitewide_options && is_multisite() ) {
				return self::atomic_update_site_option($this->option_name, $stored_options);
			} else {
				return update_option($this->option_name, $stored_options);
			}
		}
		return false;
	}

	/**
	 * Like update_site_option, but simulates record locking by using the MySQL GET_LOCK() function.
	 *
	 * The goal is to reduce the risk of triggering a race condition in update_site_option.
	 * It would be better to use real transactions, but many (most?) WordPress sites use storage engines
	 * that don't support transactions, like MyISAM.
	 *
	 * @param string $option_name
	 * @param mixed $data
	 * @return bool
	 */
	public static function atomic_update_site_option($option_name, $data) {
		global $wpdb; /** @var wpdb $wpdb */
		$lock = 'ame.' . (is_multisite() ? $wpdb->sitemeta : $wpdb->options ) . '.' . $option_name;

		//Lock. Note that we're being really optimistic and not checking the return value.
		$wpdb->query($wpdb->prepare("SELECT GET_LOCK(%s, %d)", $lock, 5));
		//Update.
		$updated = update_site_option($option_name, $data);
		//Unlock.
		$wpdb->query($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $lock));

		return $updated;

	}
	
	
  /**
   * Backwards compatible json_decode.
   *
   * @param string $data
   * @param bool $assoc Decode objects as associative arrays.
   * @return mixed
   */
    function json_decode($data, $assoc=false){
    	if ( function_exists('json_decode') ){
    		return json_decode($data, $assoc);
    	}
    	if ( class_exists('Services_JSON') ){
    		$flag = $assoc?SERVICES_JSON_LOOSE_TYPE:0;
	        $json = new Services_JSON($flag);
	        return( $json->decode($data) );
    	} else {
    		trigger_error('No JSON parser available', E_USER_ERROR);
		    return null;
    	}    
    }

  /**
   * Backwards compatible json_encode.
   *
   * @param mixed $data
   * @return string
   */
    function json_encode($data) {
    	if ( function_exists('json_encode') ){
    		return json_encode($data);
    	}
    	if ( class_exists('Services_JSON') ){
    		$json = new Services_JSON();
        	return( $json->encodeUnsafe($data) );
    	} else {
    		trigger_error('No JSON parser available', E_USER_ERROR);
		    return '';
   		}        
    }    

	
  /**
   * ShadowPluginFramework::set_magic_hooks()
   * Automagically sets up hooks for all methods named "hook_[tag]". Uses the Reflection API.
   *
   * @return void
   */
	function set_magic_hooks(){
		$class = new ReflectionClass(get_class($this));
		$methods = $class->getMethods();
		
		foreach ($methods as $method){ /** @var ReflectionMethod $method */
			//Check if the method name starts with "hook_"
			if (strpos($method->name, 'hook_') === 0){
				//Get the hook's tag from the method name 
				$hook = substr($method->name, 5);
				//Add the hook. Uses add_filter because add_action is simply a wrapper of the same.
				add_filter($hook, array($this, $method->name),
					$this->get_magic_hook_priority(), $method->getNumberOfParameters());
			}
		}
		
		unset($class);
	}

	public function get_magic_hook_priority() {
		return $this->magic_hook_priority;
	}
	

  /**
   * ShadowPluginFramework::activate()
   * Stub function for the activation hook.
   *
   * @return void
   */
	function activate(){

	}
	
  /**
   * ShadowPluginFramework::deactivate()
   * Stub function for the deactivation hook. Does nothing. 
   *
   * @return void
   */
	function deactivate(){
		
	}
	
  /**
   * ShadowPluginFramework::plugin_action_links()
   * Adds a "Settings" link to the plugin's action links. Default handler for the 'plugin_action_links' hook. 
   *
   * @param array $links
   * @param string $file
   * @return array
   */
	function plugin_action_links($links, $file) {
        if (($file == $this->plugin_basename) && is_array($links)) {
	        $links[] = "<a href='" . $this->settings_link . "'>" . __('Settings') . "</a>";
        }
        return $links;
    }
    
  /**
   * ShadowPluginFramework::uninstall()
   * Default uninstaller. Removes the plugins configuration record (if available). 
   *
   * @return void
   */
    function uninstall(){
		if ($this->option_name)
			delete_option($this->option_name);
	}
	
  /**
   * Checks if the specified file is inside the mu-plugins directory.
   *
   * @param string $filename The filename to check. Leave blank to use the current plugin's filename. 
   * @return bool
   */
	function is_in_wpmu_plugin_dir( $filename = '' ){
		if ( !defined('WPMU_PLUGIN_DIR') ) return false;
		
		if ( empty($filename) ){
			$filename = $this->plugin_file;
		}
		
		return (strpos( realpath($filename), realpath(WPMU_PLUGIN_DIR) ) !== false);
	}
	
	/**
	 * Check if the plugin is active for the entire network.  
	 * Will return true when the plugin is installed in /mu-plugins/ (WPMU, pre-3.0)
	 * or has been activated via "Network Activate" (WP 3.0+).
	 * 
	 * Blame the ridiculous blog/site/network confusion perpetrated by 
	 * the WP API for the silly name.
	 * 
	 * @return bool
	 */
	function is_super_plugin(){
		if ( is_null($this->is_mu_plugin) ){
			$this->is_mu_plugin = $this->is_in_wpmu_plugin_dir($this->plugin_file);
		}
		
		if ( $this->is_mu_plugin ){
			return true;
		} else {
			return $this->is_plugin_active_for_network($this->plugin_basename);
		}
	}
	
	/**
	 * Check whether the plugin is active for the entire network.
	 * 
	 * Silly WP doesn't load the file that contains this native function until *after* 
	 * all plugins are loaded, so until then we use a copy-pasted version of the same.
	 * 
	 * @param string $plugin
	 * @return bool
	 */
	function is_plugin_active_for_network( $plugin ) {
		if ( function_exists('is_plugin_active_for_network') ){
			return is_plugin_active_for_network($plugin);
		}
		
		if ( !is_multisite() )
			return false;
	
		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;
	
		return false;
	}

	/**
	 * Check whether the plugin is active.
	 *
	 * @see self::is_plugin_active_for_network
	 *
	 * @param string $plugin
	 * @return bool
	 */
	function is_plugin_active($plugin) {
		if ( function_exists('is_plugin_active') ) {
			return is_plugin_active($plugin);
		}
		return in_array( $plugin, (array) get_option('active_plugins', array()) ) || $this->is_plugin_active_for_network($plugin);
	}
	
}