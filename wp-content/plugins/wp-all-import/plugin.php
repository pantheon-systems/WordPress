<?php
/*
Plugin Name: WP All Import
Plugin URI: http://www.wpallimport.com/upgrade-to-pro?utm_source=wordpress.org&utm_medium=plugins-page&utm_campaign=free+plugin
Description: The most powerful solution for importing XML and CSV files to WordPress. Create Posts and Pages with content from any XML or CSV file. A paid upgrade to WP All Import Pro is available for support and additional features.
Version: 3.4.6
Author: Soflyy
*/

/**
 * Plugin root dir with forward slashes as directory separator regardless of actuall DIRECTORY_SEPARATOR value
 * @var string
 */
define('WP_ALL_IMPORT_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));
/**
 * Plugin root url for referencing static content
 * @var string
 */
define('WP_ALL_IMPORT_ROOT_URL', rtrim(plugin_dir_url(__FILE__), '/'));
/**
 * Plugin prefix for making names unique (be aware that this variable is used in conjuction with naming convention,
 * i.e. in order to change it one must not only modify this constant but also rename all constants, classes and functions which
 * names composed using this prefix)
 * @var string
 */
define('WP_ALL_IMPORT_PREFIX', 'pmxi_');

define('PMXI_VERSION', '3.4.6');

define('PMXI_EDITION', 'free');

/**
 * Plugin root uploads folder name
 * @var string
 */
define('WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY', 'wpallimport');
/**
 * Plugin logs folder name
 * @var string
 */
define('WP_ALL_IMPORT_LOGS_DIRECTORY', WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'logs');
/**
 * Plugin files folder name
 * @var string
 */
define('WP_ALL_IMPORT_FILES_DIRECTORY', WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'files');
/**
 * Plugin uploads folder name
 * @var string
 */
define('WP_ALL_IMPORT_UPLOADS_DIRECTORY', WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'uploads');
/**
 * Plugin history folder name
 * @var string
 */
define('WP_ALL_IMPORT_HISTORY_DIRECTORY', WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'history');
/**
 * Plugin temp folder name
 * @var string
 */
define('WP_ALL_IMPORT_TEMP_DIRECTORY', WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'temp');	

/**
 * Main plugin file, Introduces MVC pattern
 *
 * @singletone
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */
final class PMXI_Plugin {
	/**
	 * Singletone instance
	 * @var PMXI_Plugin
	 */
	protected static $instance;

	/**
	 * Plugin options
	 * @var array
	 */
	protected $options = array();

	/**
	 * Plugin root dir
	 * @var string
	 */
	const ROOT_DIR = WP_ALL_IMPORT_ROOT_DIR;
	/**
	 * Plugin root URL
	 * @var string
	 */
	const ROOT_URL = WP_ALL_IMPORT_ROOT_URL;
	/**
	 * Prefix used for names of shortcodes, action handlers, filter functions etc.
	 * @var string
	 */
	const PREFIX = WP_ALL_IMPORT_PREFIX;		
	/**
	 * Plugin file path
	 * @var string
	 */
	const FILE = __FILE__;
	/**
	 * Max allowed file size (bytes) to import in default mode
	 * @var int
	 */
	const LARGE_SIZE = 0; // all files will importing in large import mode	

	public static $session = null;		

	public static $is_csv = false;

	public static $csv_path = false;	

	public static $capabilities = 'manage_options';

	/**
	 * WP All Import logs folder
	 * @var string
	 */
	const LOGS_DIRECTORY =  WP_ALL_IMPORT_LOGS_DIRECTORY;
	/**
	 * WP All Import files folder
	 * @var string
	 */
	const FILES_DIRECTORY =  WP_ALL_IMPORT_FILES_DIRECTORY;
	/**
	 * WP All Import temp folder
	 * @var string
	 */
	const TEMP_DIRECTORY =  WP_ALL_IMPORT_TEMP_DIRECTORY;
	/**
	 * WP All Import uploads folder
	 * @var string
	 */
	const UPLOADS_DIRECTORY =  WP_ALL_IMPORT_UPLOADS_DIRECTORY;

	/**
	 * WP All Import history folder
	 * @var string
	 */
	const HISTORY_DIRECTORY =  WP_ALL_IMPORT_HISTORY_DIRECTORY;
	 
	/**
	 * Return singletone instance
	 * @return PMXI_Plugin
	 */
	static public function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	static public function getEddName(){
		return 'WP All Import';
	}

	/**
	 * Common logic for requestin plugin info fields
	 */
	public function __call($method, $args) {
		if (preg_match('%^get(.+)%i', $method, $mtch)) {
			$info = get_plugin_data(self::FILE);
			if (isset($info[$mtch[1]])) {
				return $info[$mtch[1]];
			}
		}
		throw new Exception("Requested method " . get_class($this) . "::$method doesn't exist.");
	}

	/**
	 * Get path to plagin dir relative to wordpress root
	 * @param bool[optional] $noForwardSlash Whether path should be returned withot forwarding slash
	 * @return string
	 */
	public function getRelativePath($noForwardSlash = false) {
		$wp_root = str_replace('\\', '/', ABSPATH);
		return ($noForwardSlash ? '' : '/') . str_replace($wp_root, '', self::ROOT_DIR);
	}

	/**
	 * Check whether plugin is activated as network one
	 * @return bool
	 */
	public function isNetwork() {
		if ( !is_multisite() )
		return false;

		$plugins = get_site_option('active_sitewide_plugins');
		if (isset($plugins[plugin_basename(self::FILE)]))
			return true;

		return false;
	}

	/**
	 * Check whether permalinks is enabled
	 * @return bool
	 */
	public function isPermalinks() {
		global $wp_rewrite;

		return $wp_rewrite->using_permalinks();
	}

	/**
	 * Return prefix for plugin database tables
	 * @return string
	 */
	public function getTablePrefix() {
		global $wpdb;
		
		//return ($this->isNetwork() ? $wpdb->base_prefix : $wpdb->prefix) . self::PREFIX;
		return $wpdb->prefix . self::PREFIX;
	}

	/**
	 * Return prefix for wordpress database tables
	 * @return string
	 */
	public function getWPPrefix() {
		global $wpdb;
		return ($this->isNetwork()) ? $wpdb->base_prefix : $wpdb->prefix;
	}

	/**
	 * Class constructor containing dispatching logic
	 * @param string $rootDir Plugin root dir
	 * @param string $pluginFilePath Plugin main file
	 */
	protected function __construct() {						

		// register autoloading method
		spl_autoload_register(array($this, 'autoload'));

		// register helpers
		if (is_dir(self::ROOT_DIR . '/helpers')) foreach (PMXI_Helper::safe_glob(self::ROOT_DIR . '/helpers/*.php', PMXI_Helper::GLOB_RECURSE | PMXI_Helper::GLOB_PATH) as $filePath) {
			require_once $filePath;
		}						
		
		// init plugin options
		$option_name = get_class($this) . '_Options';
		$options_default = PMXI_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();

		$current_options = get_option($option_name, array());
		if (empty($current_options)) $current_options = array();

		$this->options = array_intersect_key($current_options, $options_default) + $options_default;
		$this->options = array_intersect_key($options_default, array_flip(array('info_api_url'))) + $this->options; // make sure hidden options apply upon plugin reactivation
		if ('' == $this->options['cron_job_key']) $this->options['cron_job_key'] = wp_all_import_url_title(wp_all_import_rand_char(12));

		update_option($option_name, $this->options);
		$this->options = get_option(get_class($this) . '_Options');

		register_activation_hook(self::FILE, array($this, 'activation'));

		// register action handlers
		if (is_dir(self::ROOT_DIR . '/actions')) if (is_dir(self::ROOT_DIR . '/actions')) foreach (PMXI_Helper::safe_glob(self::ROOT_DIR . '/actions/*.php', PMXI_Helper::GLOB_RECURSE | PMXI_Helper::GLOB_PATH) as $filePath) {
			require_once $filePath;
			$function = $actionName = basename($filePath, '.php');
			if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
				$actionName = $m[1];
				$priority = intval($m[2]);
			} else {
				$priority = 10;
			}
			add_action($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
		}		

		// register filter handlers
		if (is_dir(self::ROOT_DIR . '/filters')) foreach (PMXI_Helper::safe_glob(self::ROOT_DIR . '/filters/*.php', PMXI_Helper::GLOB_RECURSE | PMXI_Helper::GLOB_PATH) as $filePath) {
			require_once $filePath;
			$function = $actionName = basename($filePath, '.php');
			if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
				$actionName = $m[1];
				$priority = intval($m[2]);
			} else {
				$priority = 10;
			}
			add_filter($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
		}

		// register shortcodes handlers
		if (is_dir(self::ROOT_DIR . '/shortcodes')) foreach (PMXI_Helper::safe_glob(self::ROOT_DIR . '/shortcodes/*.php', PMXI_Helper::GLOB_RECURSE | PMXI_Helper::GLOB_PATH) as $filePath) {
			$tag = strtolower(str_replace('/', '_', preg_replace('%^' . preg_quote(self::ROOT_DIR . '/shortcodes/', '%') . '|\.php$%', '', $filePath)));
			add_shortcode($tag, array($this, 'shortcodeDispatcher'));
		}			

		// register admin page pre-dispatcher
		add_action('admin_init', array($this, 'adminInit'));
		add_action('admin_init', array($this, 'fix_options'));
		add_action('init', array($this, 'init'));
		
	}	

	public function init(){
		$this->load_plugin_textdomain();
	}

	public function plugin_row_meta($links, $file)
	{
		if ( $file == plugin_basename( __FILE__ ) ) {
			$row_meta = array(
				'pro'    => '<a href="http://www.wpallimport.com" target="_blank" title="' . esc_attr( __( 'WP All Import Pro Version', 'wp_all_import_plugin' ) ) . '">' . __( 'Pro Version', 'wp_all_import_plugin' ) . '</a>',				
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	/**
	 * convert imports options
	 * compatibility with version 3.2.3
	 */
	public function fix_options(){

		global $wpdb;
		
		$imports = new PMXI_Import_List();
		$post    = new PMXI_Post_Record();

		$templates = new PMXI_Template_List();
		$template  = new PMXI_Template_Record();

		$is_migrated = get_option('pmxi_is_migrated');

		$uploads = wp_upload_dir();
		
		if ( empty($is_migrated) or version_compare($is_migrated, PMXI_VERSION) < 0 ){ //PMXI_VERSION

			$commit_migration = true;

			if ( empty($is_migrated) ){ // plugin version less than 4.0.0

				wp_all_import_rmdir($uploads['basedir'] . '/wpallimport_history');
				wp_all_import_rmdir($uploads['basedir'] . '/wpallimport_logs');

				foreach ($imports->setColumns($imports->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $imp){
					
					$imp->getById($imp->id);				
					
					if ( ! $imp->isEmpty() and ! empty($imp->template)){

						$options = array_merge($imp->options, $imp->template);

						$this->ver_4_transition_fix($options);
						
						$imp->set(array(
							'options' => $options
						))->update();
						
						if ($imp->type == 'file'){									
							$imp->set(array(
								'path' => $uploads['basedir'] . DIRECTORY_SEPARATOR . self::FILES_DIRECTORY . DIRECTORY_SEPARATOR . basename($imp->path)
							))->update();
						}
					}
				}					

				foreach ($templates->setColumns($templates->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $tpl){
					
					$tpl->getById($tpl->id);				
					
					if ( ! $tpl->isEmpty() and ! empty($tpl->title) ) {
						
						$opt = ( empty($tpl->options) ) ? array() : $tpl->options;

						$options = array_merge($opt, array(
							'title' => $tpl->title,
							'content' => $tpl->content,
							'is_keep_linebreaks' => $tpl->is_keep_linebreaks,
							'is_leave_html' => $tpl->is_leave_html,
							'fix_characters' => $tpl->fix_characters
						));

						$this->ver_4_transition_fix($options);

						$tpl->set(array(
							'options' => $options
						))->update();

					}

				}					

				$commit_migration = $this->fix_db_schema(); // feature to version 4.0.0
				
			}
			else {

				$commit_migration = $this->fix_db_schema();
				
				foreach ($imports->setColumns($imports->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $imp){
				
					$imp->getById($imp->id);				
					
					if ( ! $imp->isEmpty() ){

						$options = $imp->options;

						$this->ver_4x_transition_fix($options, $is_migrated);
						
						$imp->set(array(
							'options' => $options
						))->update();																
					}
				}					

				foreach ($templates->setColumns($templates->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $tpl){
					
					$tpl->getById($tpl->id);				
					
					if ( ! $tpl->isEmpty() ) {
						
						$options = ( empty($tpl->options) ) ? array() : $tpl->options;							

						$this->ver_4x_transition_fix($options, $is_migrated);

						$tpl->set(array(
							'options' => $options
						))->update();

					}

				}
			}
			if ($commit_migration) update_option('pmxi_is_migrated', PMXI_VERSION);
		}			
	}

	public function ver_4_transition_fix( &$options ){
			
		$options['wizard_type'] = ($options['duplicate_matching'] == 'auto') ? 'new' : 'matching';

		if ($options['download_images']){
			$options['download_images'] = 'yes';
			$options['download_featured_image'] = $options['featured_image'];
			$options['featured_image'] = '';
			$options['download_featured_delim'] = $options['featured_delim'];
			$options['featured_delim'] = '';
		}

		if ($options['set_image_meta_data']){
			$options['set_image_meta_title'] = 1;
			$options['set_image_meta_caption'] = 1;
			$options['set_image_meta_alt'] = 1;
			$options['set_image_meta_description'] = 1;
		}

		if ("" == $options['custom_type']) $options['custom_type'] = $options['type'];

		$exclude_taxonomies = (class_exists('PMWI_Plugin')) ? array('post_format', 'product_type') : array('post_format');	
		$post_taxonomies = array_diff_key(get_taxonomies_by_object_type(array($options['custom_type']), 'object'), array_flip($exclude_taxonomies));

		$options['tax_logic'] = array();
		$options['tax_assing'] = array();
		$options['tax_multiple_xpath'] = array();
		$options['tax_multiple_delim'] = array();
		$options['tax_hierarchical_logic_entire'] = array();
		$options['tax_hierarchical_logic_manual'] = array();

		if ( ! empty($post_taxonomies)):
			foreach ($post_taxonomies as $ctx):					

				$options['tax_logic'][$ctx->name] = ($ctx->hierarchical) ? 'hierarchical' : 'multiple';
				
				if ($ctx->name == 'category'){
					$options['post_taxonomies']['category'] = $options['categories'];
				}
				elseif ($ctx->name == 'post_tag' ){
					$options['tax_assing']['post_tag'] = 1;						
					$options['tax_multiple_xpath']['post_tag'] = $options['tags'];
					$options['tax_multiple_delim']['post_tag'] = $options['tags_delim'];
					}
				
				if ( ! empty($options['post_taxonomies'][$ctx->name])){

					$taxonomies_hierarchy = json_decode($options['post_taxonomies'][$ctx->name], true);									
					$options['tax_assing'][$ctx->name] = (!empty($taxonomies_hierarchy[0]['assign'])) ? 1 : 0;										
					
					if ($options['tax_logic'][$ctx->name] == 'multiple') {
						$options['tax_multiple_xpath'][$ctx->name] = (!empty($taxonomies_hierarchy[0]['xpath'])) ? $taxonomies_hierarchy[0]['xpath'] : '';	
						$options['tax_multiple_delim'][$ctx->name] = (!empty($taxonomies_hierarchy[0]['delim'])) ? $taxonomies_hierarchy[0]['delim'] : '';	
					}
					else{							
						$options['tax_hierarchical_logic_manual'][$ctx->name] = 1;							
					}
				}											

			endforeach;				
		endif;						
	}

	public function ver_4x_transition_fix(&$options, $version){
		if ( version_compare($version, '4.0.5') < 0  ){				
			if ( ! empty($options['tax_hierarchical_logic']) and is_array($options['tax_hierarchical_logic']) ){
				foreach ($options['tax_hierarchical_logic'] as $tx => $type) {
					switch ($type){
						case 'entire':
							$options['tax_hierarchical_logic_entire'][$tx] = 1;	
							break;
						case 'manual':
							$options['tax_hierarchical_logic_manual'][$tx] = 1;
							break;
						default:

							break;
					}
				}
				unset($options['tax_hierarchical_logic']);
			}
		}

	}

	/**
	 * pre-dispatching logic for admin page controllers
	 */
	public function adminInit() {

		// create history folder
		$uploads = wp_upload_dir();				

		$wpallimportDirs = array( WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY, self::LOGS_DIRECTORY, self::FILES_DIRECTORY, self::TEMP_DIRECTORY, self::UPLOADS_DIRECTORY, self::HISTORY_DIRECTORY);			

		foreach ($wpallimportDirs as $destination) {

			$dir = $uploads['basedir'] . DIRECTORY_SEPARATOR . $destination;
			
			if ( !is_dir($dir)) wp_mkdir_p($dir);			

			if ( ! @file_exists($dir . DIRECTORY_SEPARATOR . 'index.php') ) @touch( $dir . DIRECTORY_SEPARATOR . 'index.php' );						
			
		}
		
		self::$session = new PMXI_Handler();				

		$input = new PMXI_Input();
		$page = strtolower($input->getpost('page', ''));						

		if (preg_match('%^' . preg_quote(str_replace('_', '-', self::PREFIX), '%') . '([\w-]+)$%', $page)) {
			//$this->adminDispatcher($page, strtolower($input->getpost('action', 'index')));

			$action = strtolower($input->getpost('action', 'index'));

			// capitalize prefix and first letters of class name parts	
			$controllerName = preg_replace_callback('%(^' . preg_quote(self::PREFIX, '%') . '|_).%', array($this, "replace_callback"),str_replace('-', '_', $page));
			$actionName = str_replace('-', '_', $action);
			if (method_exists($controllerName, $actionName)) {

				@ini_set("max_input_time", PMXI_Plugin::getInstance()->getOption('max_input_time'));
				@ini_set("max_execution_time", PMXI_Plugin::getInstance()->getOption('max_execution_time'));

				if ( ! get_current_user_id() or ! current_user_can( self::$capabilities )) {
				    // This nonce is not valid.
				    die( 'Security check' ); 

				} else {

					$this->_admin_current_screen = (object)array(
						'id' => $controllerName,
						'base' => $controllerName,
						'action' => $actionName,
						'is_ajax' => strpos($_SERVER["HTTP_ACCEPT"], 'json') !== false,
						'is_network' => is_network_admin(),
						'is_user' => is_user_admin(),
					);
					add_filter('current_screen', array($this, 'getAdminCurrentScreen'));
					add_filter('admin_body_class', create_function('', 'return "' . 'wpallimport-plugin";'));

					$controller = new $controllerName();
					if ( ! $controller instanceof PMXI_Controller_Admin) {
						throw new Exception("Administration page `$page` matches to a wrong controller type.");
					}

					if ($this->_admin_current_screen->is_ajax) { // ajax request						
						$controller->$action();
						do_action('pmxi_action_after');
						die(); // stop processing since we want to output only what controller is randered, nothing in addition
					} elseif ( ! $controller->isInline) {																																		
						@ob_start();
						$controller->$action();
						self::$buffer = @ob_get_clean();													
					} else {
						self::$buffer_callback = array($controller, $action);
					}

				}
				
			} else { // redirect to dashboard if requested page and/or action don't exist
				wp_redirect(admin_url()); die();
			}

		}			

	}

	/**
	 * Dispatch shorttag: create corresponding controller instance and call its index method
	 * @param array $args Shortcode tag attributes
	 * @param string $content Shortcode tag content
	 * @param string $tag Shortcode tag name which is being dispatched
	 * @return string
	 */
	public function shortcodeDispatcher($args, $content, $tag) {

		$controllerName = self::PREFIX . preg_replace_callback('%(^|_).%', array($this, "replace_callback"), $tag);// capitalize first letters of class name parts and add prefix
		$controller = new $controllerName();
		if ( ! $controller instanceof PMXI_Controller) {
			throw new Exception("Shortcode `$tag` matches to a wrong controller type.");
		}
		ob_start();
		$controller->index($args, $content);
		return ob_get_clean();
	}

	static $buffer = NULL;
	static $buffer_callback = NULL;

	/**
	 * Dispatch admin page: call corresponding controller based on get parameter `page`
	 * The method is called twice: 1st time as handler `parse_header` action and then as admin menu item handler
	 * @param string[optional] $page When $page set to empty string ealier buffered content is outputted, otherwise controller is called based on $page value
	 */
	public function adminDispatcher($page = '', $action = 'index') {			

		if ('' === $page) {				
			if ( ! is_null(self::$buffer)) {
				echo '<div class="wrap">';
				echo self::$buffer;
				do_action('pmxi_action_after');
				echo '</div>';
			} elseif ( ! is_null(self::$buffer_callback)) {
				echo '<div class="wrap">';
				call_user_func(self::$buffer_callback);
				do_action('pmxi_action_after');
				echo '</div>';
			} else {
				throw new Exception('There is no previousely buffered content to display.');
			}
		} 
		
	}

	public function replace_callback($matches){
		return strtoupper($matches[0]);
	}

	protected $_admin_current_screen = NULL;
	public function getAdminCurrentScreen()
	{
		return $this->_admin_current_screen;
	}

	/**
	 * Autoloader
	 * It's assumed class name consists of prefix folloed by its name which in turn corresponds to location of source file
	 * if `_` symbols replaced by directory path separator. File name consists of prefix folloed by last part in class name (i.e.
	 * symbols after last `_` in class name)
	 * When class has prefix it's source is looked in `models`, `controllers`, `shortcodes` folders, otherwise it looked in `core` or `library` folder
	 *
	 * @param string $className
	 * @return bool
	 */
	public function autoload($className) {
		$is_prefix = false;
		$filePath = str_replace('_', '/', preg_replace('%^' . preg_quote(self::PREFIX, '%') . '%', '', strtolower($className), 1, $is_prefix)) . '.php';
		if ( ! $is_prefix) { // also check file with original letter case
			$filePathAlt = $className . '.php';
		}
		foreach ($is_prefix ? array('models', 'controllers', 'shortcodes', 'classes') : array('libraries') as $subdir) {
			$path = self::ROOT_DIR . '/' . $subdir . '/' . $filePath;
			if (is_file($path)) {
				require $path;
				return TRUE;
			}
			if ( ! $is_prefix) {
				$pathAlt = self::ROOT_DIR . '/' . $subdir . '/' . $filePathAlt;
				if (is_file($pathAlt)) {
					require $pathAlt;
					return TRUE;
				}
			}
		}			

		return FALSE;
	}

	/**
	 * Get plugin option
	 * @param string[optional] $option Parameter to return, all array of options is returned if not set
	 * @return mixed
	 */
	public function getOption($option = NULL) {
		$options = apply_filters('wp_all_import_config_options', $this->options);
		if (is_null($option)) {
			return $options;
		} else if (isset($options[$option])) {
			return $options[$option];
		} else {
			throw new Exception("Specified option is not defined for the plugin");
		}
	}
	/**
	 * Update plugin option value
	 * @param string $option Parameter name or array of name => value pairs
	 * @param mixed[optional] $value New value for the option, if not set than 1st parameter is supposed to be array of name => value pairs
	 * @return array
	 */
	public function updateOption($option, $value = NULL) {
		is_null($value) or $option = array($option => $value);
		if (array_diff_key($option, $this->options)) {
			throw new Exception("Specified option is not defined for the plugin");
		}
		$this->options = $option + $this->options;
		update_option(get_class($this) . '_Options', $this->options);

		return $this->options;
	}

	/**
	 * Plugin activation logic
	 */
	public function activation() {
		// uncaught exception doesn't prevent plugin from being activated, therefore replace it with fatal error so it does
		set_exception_handler(create_function('$e', 'trigger_error($e->getMessage(), E_USER_ERROR);'));

		// create plugin options
		$option_name = get_class($this) . '_Options';
		$options_default = PMXI_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();
		$wpai_options = get_option($option_name, false);
		if ( ! $wpai_options ) update_option($option_name, $options_default);

		// create/update required database tables
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		require self::ROOT_DIR . '/schema.php';
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
	        // check if it is a network activation - if so, run the activation function for each blog id	        
	        if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	            $old_blog = $wpdb->blogid;
	            // Get all blog ids
	            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	            foreach ($blogids as $blog_id) {
	                switch_to_blog($blog_id);
	                require self::ROOT_DIR . '/schema.php';
	                dbDelta($plugin_queries);		                

					// sync data between plugin tables and wordpress (mostly for the case when plugin is reactivated)
					
					$post = new PMXI_Post_Record();
					$wpdb->query('DELETE FROM ' . $post->getTable() . ' WHERE post_id NOT IN (SELECT ID FROM ' . $wpdb->posts . ')');
	            }
	            switch_to_blog($old_blog);
	            return;	         
	        }	         
	    }

		dbDelta($plugin_queries);			

		// sync data between plugin tables and wordpress (mostly for the case when plugin is reactivated)
		
		$post = new PMXI_Post_Record();
		$wpdb->query('DELETE FROM ' . $post->getTable() . ' WHERE post_id NOT IN (SELECT ID FROM ' . $wpdb->posts . ')');

	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp_all_import_plugin' );							
		
		load_plugin_textdomain( 'wp_all_import_plugin', false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages" );
	}		

	public function fix_db_schema(){

		$uploads = wp_upload_dir();		

		if ( ! is_dir($uploads['basedir'] . DIRECTORY_SEPARATOR . self::LOGS_DIRECTORY) or ! is_writable($uploads['basedir'] . DIRECTORY_SEPARATOR . self::LOGS_DIRECTORY)) {
			die(sprintf(__('Uploads folder %s must be writable', 'wp_all_import_plugin'), $uploads['basedir'] . DIRECTORY_SEPARATOR . self::LOGS_DIRECTORY));
		}

		if ( ! is_dir($uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY) or ! is_writable($uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY)) {
			die(sprintf(__('Uploads folder %s must be writable', 'wp_all_import_plugin'), $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY));
		}

		// create/update required database tables
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		require self::ROOT_DIR . '/schema.php';
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
	        // check if it is a network activation - if so, run the activation function for each blog id
	        if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	            $old_blog = $wpdb->blogid;
	            // Get all blog ids
	            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	            foreach ($blogids as $blog_id) {
	                switch_to_blog($blog_id);
	                require self::ROOT_DIR . '/schema.php';
	                dbDelta($plugin_queries);

					// sync data between plugin tables and wordpress (mostly for the case when plugin is reactivated)

					$post = new PMXI_Post_Record();
					$wpdb->query('DELETE FROM ' . $post->getTable() . ' WHERE post_id NOT IN (SELECT ID FROM ' . $wpdb->posts . ')');
	            }
	            switch_to_blog($old_blog);
	            return;
	        }
	    }

		dbDelta($plugin_queries);
		
		// do not execute ALTER TABLE queries if sql user doesn't have ALTER privileges
		$grands = $wpdb->get_results("SELECT * FROM information_schema.user_privileges WHERE grantee LIKE \"'" . DB_USER . "'%\" AND PRIVILEGE_TYPE = 'ALTER' AND IS_GRANTABLE = 'YES';");
		
		$table = $table = $this->getTablePrefix() . 'files';
		
		$tablefields = $wpdb->get_results("DESCRIBE {$table};");
		// For every field in the table
		foreach ($tablefields as $tablefield) {
			if ('contents' == $tablefield->Field) {
				$list = new PMXI_File_List();
				for ($i = 1; $list->getBy(NULL, 'id', $i, 1)->count(); $i++) {
					foreach ($list->convertRecords() as $file) {
						$file->save(); // resave file for file to be stored in uploads folder
					}
				}

				if (!empty($grands)) $wpdb->query("ALTER TABLE {$table} DROP " . $tablefield->Field);

				break;
			}
		}

		$table = $this->getTablePrefix() . 'imports';
		
		$tablefields = $wpdb->get_results("DESCRIBE {$table};");
		$fields_to_alter = array(
			'parent_import_id',
			'iteration',
			'deleted',
			'executing',
			'canceled',
			'canceled_on',
			'failed',
			'failed_on',
			'settings_update_on',
			'last_activity'
		);					

		// Check if field exists
		foreach ($tablefields as $tablefield) {
			if (in_array($tablefield->Field, $fields_to_alter)){
				$fields_to_alter = array_diff($fields_to_alter, array($tablefield->Field));
			} 
		}
		
		if ( ! empty($fields_to_alter) ){								

			if (empty($grands)) return false;																		
			
			foreach ($fields_to_alter as $field) {
				switch ($field) {
					case 'parent_import_id':
						$wpdb->query("ALTER TABLE {$table} ADD `parent_import_id` BIGINT(20) NOT NULL DEFAULT 0;");		
						break;
					case 'iteration':
						$wpdb->query("ALTER TABLE {$table} ADD `iteration` BIGINT(20) NOT NULL DEFAULT 0;");	
						break;
					case 'deleted':
						$wpdb->query("ALTER TABLE {$table} ADD `deleted` BIGINT(20) NOT NULL DEFAULT 0;");						
						break;
					case 'executing':
						$wpdb->query("ALTER TABLE {$table} ADD `executing` BOOL NOT NULL DEFAULT 0;");						
						break;
					case 'canceled':
						$wpdb->query("ALTER TABLE {$table} ADD `canceled` BOOL NOT NULL DEFAULT 0;");						
						break;
					case 'canceled_on':
						$wpdb->query("ALTER TABLE {$table} ADD `canceled_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';");						
						break;
					case 'failed':
						$wpdb->query("ALTER TABLE {$table} ADD `failed` BOOL NOT NULL DEFAULT 0;");		
						break;
					case 'failed_on':
						$wpdb->query("ALTER TABLE {$table} ADD `failed_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';");			
						break;
					case 'settings_update_on':
						$wpdb->query("ALTER TABLE {$table} ADD `settings_update_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';");						
						break;
					case 'last_activity':
						$wpdb->query("ALTER TABLE {$table} ADD `last_activity` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';");		
						break;

					default:
						# code...
						break;
				}
			}				
		}							

		$table = $this->getTablePrefix() . 'posts';
		$tablefields = $wpdb->get_results("DESCRIBE {$table};");
		$iteration = false;
		$specified = false;

		// Check if field exists
		foreach ($tablefields as $tablefield) {			
			if ('iteration' == $tablefield->Field) $iteration = true;			
			if ('specified' == $tablefield->Field) $specified = true;	
		}

		if (!$iteration){ 
			
			if (empty($grands)) {					
				?>
				<div class="error"><p>
					<?php printf(
							__('<b>%s Plugin</b>: Current sql user %s doesn\'t have ALTER privileges', 'pmwi_plugin'),
							self::getInstance()->getName(), DB_USER
					) ?>
				</p></div>
				<?php
				return false;
			}
			
			$wpdb->query("ALTER TABLE {$table} ADD `iteration` BIGINT(20) NOT NULL DEFAULT 0;");
			
		}

		if (!$specified and !empty($grands))
		{
			$wpdb->query("ALTER TABLE {$table} ADD `specified` BOOL NOT NULL DEFAULT 0;");
		}

		if ( ! empty($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate))
			$charset_collate .= " COLLATE $wpdb->collate";
			
		$table_prefix = $this->getTablePrefix();

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$table_prefix}history (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			import_id BIGINT(20) UNSIGNED NOT NULL,
			type ENUM('manual','processing','trigger','continue','') NOT NULL DEFAULT '',	
			time_run TEXT,	
			date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',		
			summary TEXT,
			PRIMARY KEY  (id)
		) $charset_collate;");

		return true;
	}	

	/**
	 * Method returns default import options, main utility of the method is to avoid warnings when new
	 * option is introduced but already registered imports don't have it
	 */
	public static function get_default_import_options() {
		return array(
			'type' => 'post',
			'is_override_post_type' => 0,
			'post_type_xpath' => '',
			'deligate' => '',
			'wizard_type' => 'new',
			'custom_type' => '',
			'featured_delim' => ',',
			'atch_delim' => ',',
			'is_search_existing_attach' => 0,
			'post_taxonomies' => array(),
			'parent' => 0,
			'is_multiple_page_parent' => 'yes',
			'single_page_parent' => '',
			'order' => 0,
			'status' => 'publish',
			'page_template' => 'default',
			'is_multiple_page_template' => 'yes',
			'single_page_template' => '',
			'page_taxonomies' => array(),
			'date_type' => 'specific',
			'date' => 'now',
			'date_start' => 'now',
			'date_end' => 'now',
			'custom_name' => array(),
			'custom_value' => array(),
			'custom_format' => array(),
			'custom_mapping' => array(),
			'serialized_values' => array(),
			'custom_mapping_rules' => array(),
			'comment_status' => 'open',
			'comment_status_xpath' => '',
			'ping_status' => 'open',
			'ping_status_xpath' => '',
			'create_draft' => 'no',
			'author' => '',
			'post_excerpt' => '',
			'post_slug' => '',
			'attachments' => '',
			'is_import_specified' => 0,
			'import_specified' => '',
			'is_delete_source' => 0,
			'is_cloak' => 0,
			'unique_key' => '',
			'tmp_unique_key' => '',
			'feed_type' => 'auto',
			'search_existing_images' => 1,

			'create_new_records' => 1,
			'is_delete_missing' => 0,
			'set_missing_to_draft' => 0,
			'is_update_missing_cf' => 0,
			'update_missing_cf_name' => '',
			'update_missing_cf_value' => '',

			'is_keep_former_posts' => 'no',
			'is_update_status' => 1,
			'is_update_content' => 1,
			'is_update_title' => 1,
			'is_update_slug' => 1,
			'is_update_excerpt' => 1,
			'is_update_categories' => 1,
			'is_update_author' => 1,
			'is_update_comment_status' => 1,
			'is_update_post_type' => 1,
			'update_categories_logic' => 'full_update',
			'taxonomies_list' => array(),
			'taxonomies_only_list' => array(),
			'taxonomies_except_list' => array(),
			'is_update_attachments' => 1,
			'is_update_images' => 1,
			'update_images_logic' => 'full_update',
			'is_update_dates' => 1,
			'is_update_menu_order' => 1,
			'is_update_parent' => 1,
			'is_keep_attachments' => 0,
			'is_keep_imgs' => 0,
			'do_not_remove_images' => 1,

			'is_update_custom_fields' => 1,
			'update_custom_fields_logic' => 'full_update',
			'custom_fields_list' => array(),
			'custom_fields_only_list' => array(),
			'custom_fields_except_list' => array(),

			'duplicate_matching' => 'auto',
			'duplicate_indicator' => 'title',
			'custom_duplicate_name' => '',
			'custom_duplicate_value' => '',
			'is_update_previous' => 0,
			'is_scheduled' => '',
			'scheduled_period' => '',
			'friendly_name' => '',
			'records_per_request' => 20,
			'auto_rename_images' => 0,
			'auto_rename_images_suffix' => '',
			'images_name' => 'filename',
			'post_format' => 'standard',
			'post_format_xpath' => '',
			'encoding' => 'UTF-8',
			'delimiter' => '',
			'image_meta_title' => '',
			'image_meta_title_delim' => ',',
			'image_meta_caption' => '',
			'image_meta_caption_delim' => ',',
			'image_meta_alt' => '',
			'image_meta_alt_delim' => ',',
			'image_meta_description' => '',
			'image_meta_description_delim' => ',',
			'image_meta_description_delim_logic' => 'separate',
			'status_xpath' => '',
			'download_images' => 'yes',
			'converted_options' => 0,
			'update_all_data' => 'yes',
			'is_fast_mode' => 0,
			'chuncking' => 1,
			'import_processing' => 'ajax',
			'save_template_as' => 0,

			'title' => '',
			'content' => '',
			'name' => '',
			'is_keep_linebreaks' => 1,
			'is_leave_html' => 0,
			'fix_characters' => 0,
			'pid_xpath' => '',

			'featured_image' => '',
			'download_featured_image' => '',
			'download_featured_delim' => ',',
			'gallery_featured_image' => '',
			'gallery_featured_delim' => ',',
			'is_featured' => 1,
			'set_image_meta_title' => 0,
			'set_image_meta_caption' => 0,
			'set_image_meta_alt' => 0,
			'set_image_meta_description' => 0,
			'auto_set_extension' => 0,
			'new_extension' => '',
			'tax_logic' => array(),
			'tax_assing' => array(),
			'term_assing' => array(),
			'multiple_term_assing' => array(),
			'tax_hierarchical_assing' => array(),
			'tax_hierarchical_last_level_assign' => array(),
			'tax_single_xpath' => array(),
			'tax_multiple_xpath' => array(),
			'tax_hierarchical_xpath' => array(),
			'tax_multiple_delim' => array(),
			'tax_hierarchical_delim' => array(),
			'tax_manualhierarchy_delim' => array(),
			'tax_hierarchical_logic_entire' => array(),
			'tax_hierarchical_logic_manual' => array(),
			'tax_enable_mapping' => array(),
			'tax_is_full_search_single' => array(),
			'tax_is_full_search_multiple' => array(),
			'tax_assign_to_one_term_single' => array(),
			'tax_assign_to_one_term_multiple' => array(),
			'tax_mapping' => array(),
			'tax_logic_mapping' => array(),
			'is_tax_hierarchical_group_delim' => array(),
			'tax_hierarchical_group_delim' => array(),
			'nested_files' => array(),
			'xml_reader_engine' => 0
		);
	}

	/*
	 * Convert csv to xml
	 */
	public static function csv_to_xml($csv_url){

		include_once(self::ROOT_DIR.'/libraries/XmlImportCsvParse.php');

		$csv = new PMXI_CsvParser($csv_url);

		$wp_uploads = wp_upload_dir();
		$tmpname = wp_unique_filename($wp_uploads['path'], str_replace("csv", "xml", basename($csv_url)));
		$xml_file = $wp_uploads['path']  .'/'. $tmpname;
		file_put_contents($xml_file, $csv->toXML());
		return $xml_file;

	}

	public static function is_ajax(){
		return strpos($_SERVER["HTTP_ACCEPT"], 'json') !== false;
	}

}

PMXI_Plugin::getInstance();	
	