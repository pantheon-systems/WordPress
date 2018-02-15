<?php
/*
Plugin Name: WP All Export
Plugin URI: http://www.wpallimport.com/export/
Description: Export any post type to a CSV or XML file. Edit the exported data, and then re-import it later using WP All Import.
Version: 1.1.5
Author: Soflyy
*/

require_once(__DIR__.'/classes/CdataStrategyFactory.php');

if( ! defined( 'PMXE_SESSION_COOKIE' ) )
	define( 'PMXE_SESSION_COOKIE', '_pmxe_session' );

/**
 * Plugin root dir with forward slashes as directory separator regardless of actuall DIRECTORY_SEPARATOR value
 * @var string
 */
define('PMXE_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));
/**
 * Plugin root url for referencing static content
 * @var string
 */
define('PMXE_ROOT_URL', rtrim(plugin_dir_url(__FILE__), '/'));

if ( class_exists('PMXE_Plugin') and PMXE_EDITION == "paid"){

	function pmxe_notice(){
		
		?>
		<div class="error">
			<p>
			<?php printf(__('Please de-activate and remove the free version of the WP All Export before activating the paid version.', 'wp_all_export_plugin')); ?>			
			</p>
		</div>
		<?php				

		deactivate_plugins( str_replace('\\', '/', dirname(__FILE__)) . '/wp-all-export.php');

	}

	add_action('admin_notices', 'pmxe_notice');	

}
else {

	/**
	 * Plugin prefix for making names unique (be aware that this variable is used in conjuction with naming convention,
	 * i.e. in order to change it one must not only modify this constant but also rename all constants, classes and functions which
	 * names composed using this prefix)
	 * @var string
	 */
	define('PMXE_PREFIX', 'pmxe_');

	define('PMXE_VERSION', '1.1.5');

	define('PMXE_EDITION', 'free');

	/**
	 * Plugin root uploads folder name
	 * @var string
	 */
	define('WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY', 'wpallexport');	
	/**
	 * Plugin uploads folder name
	 * @var string
	 */
	define('WP_ALL_EXPORT_UPLOADS_DIRECTORY', WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'exports');

	/**
	 * Plugin temp folder name
	 * @var string
	 */
	define('WP_ALL_EXPORT_TEMP_DIRECTORY', WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'temp');	

	/**
	 * Plugin temp folder name
	 * @var string
	 */
	define('WP_ALL_EXPORT_CRON_DIRECTORY', WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'exports');	

	/**
	 * Main plugin file, Introduces MVC pattern
	 *
	 * @singletone
	 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
	 */
	final class PMXE_Plugin {
		/**
		 * Singletone instance
		 * @var PMXE_Plugin
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
		const ROOT_DIR = PMXE_ROOT_DIR;
		/**
		 * Plugin root URL
		 * @var string
		 */
		const ROOT_URL = PMXE_ROOT_URL;
		/**
		 * Prefix used for names of shortcodes, action handlers, filter functions etc.
		 * @var string
		 */
		const PREFIX = PMXE_PREFIX;
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

		/**
		 * WP All Import temp folder
		 * @var string
		 */
		const TEMP_DIRECTORY =  WP_ALL_EXPORT_TEMP_DIRECTORY;
		/**
		 * WP All Import uploads folder
		 * @var string
		 */
		const UPLOADS_DIRECTORY =  WP_ALL_EXPORT_UPLOADS_DIRECTORY;
		/**
		 * WP All Import uploads folder
		 * @var string
		 */
		const CRON_DIRECTORY =  WP_ALL_EXPORT_CRON_DIRECTORY;

		public static $session = null;		

		public static $capabilities = 'manage_options';
		
		/**
		 * Return singletone instance
		 * @return PMXE_Plugin
		 */
		static public function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		static public function getEddName(){
			return 'WP All Export';
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

			require_once (self::ROOT_DIR . '/classes/installer.php');
			
			$installer = new PMXE_Installer();
			$installer->checkActivationConditions();
			
			// uncaught exception doesn't prevent plugin from being activated, therefore replace it with fatal error so it does
			//set_exception_handler(create_function('$e', 'trigger_error($e->getMessage(), E_USER_ERROR);'));

			// register autoloading method
			spl_autoload_register(array($this, 'autoload'));

			// register helpers
			if (is_dir(self::ROOT_DIR . '/helpers')) foreach (PMXE_Helper::safe_glob(self::ROOT_DIR . '/helpers/*.php', PMXE_Helper::GLOB_RECURSE | PMXE_Helper::GLOB_PATH) as $filePath) {
				require_once $filePath;
			}			

			// init plugin options
			$option_name = get_class($this) . '_Options';
			$options_default = PMXE_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();
			$this->options = array_intersect_key(get_option($option_name, array()), $options_default) + $options_default;
			$this->options = array_intersect_key($options_default, array_flip(array('info_api_url'))) + $this->options; // make sure hidden options apply upon plugin reactivation								
			if ('' == $this->options['cron_job_key']) $this->options['cron_job_key'] = wp_all_export_url_title(wp_all_export_rand_char(12));

			update_option($option_name, $this->options);
			$this->options = get_option(get_class($this) . '_Options');
			register_activation_hook(self::FILE, array($this, 'activation'));

			// register action handlers
			if (is_dir(self::ROOT_DIR . '/actions')) if (is_dir(self::ROOT_DIR . '/actions')) foreach (PMXE_Helper::safe_glob(self::ROOT_DIR . '/actions/*.php', PMXE_Helper::GLOB_RECURSE | PMXE_Helper::GLOB_PATH) as $filePath) {
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
			if (is_dir(self::ROOT_DIR . '/filters')) foreach (PMXE_Helper::safe_glob(self::ROOT_DIR . '/filters/*.php', PMXE_Helper::GLOB_RECURSE | PMXE_Helper::GLOB_PATH) as $filePath) {
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
			if (is_dir(self::ROOT_DIR . '/shortcodes')) foreach (PMXE_Helper::safe_glob(self::ROOT_DIR . '/shortcodes/*.php', PMXE_Helper::GLOB_RECURSE | PMXE_Helper::GLOB_PATH) as $filePath) {
				$tag = strtolower(str_replace('/', '_', preg_replace('%^' . preg_quote(self::ROOT_DIR . '/shortcodes/', '%') . '|\.php$%', '', $filePath)));
				add_shortcode($tag, array($this, 'shortcodeDispatcher'));
			}
			
			// register admin page pre-dispatcher
			add_action('admin_init', array($this, 'adminInit'));
			add_action('admin_init', array($this, 'fix_db_schema'));
			add_action('init', array($this, 'init'));
		}	

		public function init(){
			$this->load_plugin_textdomain();
		}

		/**
		 * pre-dispatching logic for admin page controllers
		 */
		public function adminInit() {

			// create history folder
			$uploads = wp_upload_dir();

			$wpallimportDirs = array( WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY, self::TEMP_DIRECTORY, self::UPLOADS_DIRECTORY, self::CRON_DIRECTORY);			

			foreach ($wpallimportDirs as $destination) {

				$dir = $uploads['basedir'] . DIRECTORY_SEPARATOR . $destination;
				
				if ( !is_dir($dir)) wp_mkdir_p($dir);			

				if ( ! @file_exists($dir . DIRECTORY_SEPARATOR . 'index.php') ) @touch( $dir . DIRECTORY_SEPARATOR . 'index.php' );						
				
			}

			if ( ! is_dir($uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY) or ! is_writable($uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY)) {
				die(sprintf(__('Uploads folder %s must be writable', 'wp_all_export_plugin'), $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY));
			}

			if ( ! is_dir($uploads['basedir'] . DIRECTORY_SEPARATOR . self::UPLOADS_DIRECTORY) or ! is_writable($uploads['basedir'] . DIRECTORY_SEPARATOR . self::UPLOADS_DIRECTORY)) {
				die(sprintf(__('Uploads folder %s must be writable', 'wp_all_export_plugin'), $uploads['basedir'] . DIRECTORY_SEPARATOR . self::UPLOADS_DIRECTORY));
			}			

			self::$session = new PMXE_Handler();						

			$input = new PMXE_Input();
			$page = strtolower($input->getpost('page', ''));						

			if (preg_match('%^' . preg_quote(str_replace('_', '-', self::PREFIX), '%') . '([\w-]+)$%', $page)) {
				//$this->adminDispatcher($page, strtolower($input->getpost('action', 'index')));

				$action = strtolower($input->getpost('action', 'index'));

				// capitalize prefix and first letters of class name parts	
				$controllerName = preg_replace_callback('%(^' . preg_quote(self::PREFIX, '%') . '|_).%', array($this, "replace_callback"),str_replace('-', '_', $page));
				$actionName = str_replace('-', '_', $action);
				if (method_exists($controllerName, $actionName)) {

					if ( ! get_current_user_id() or ! current_user_can(self::$capabilities)) {
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
						add_filter('admin_body_class', create_function('', 'return "' . 'wpallexport-plugin";'));

						$controller = new $controllerName();
						if ( ! $controller instanceof PMXE_Controller_Admin) {
							throw new Exception("Administration page `$page` matches to a wrong controller type.");
						}

						if ($this->_admin_current_screen->is_ajax) { // ajax request						
							$controller->$action();
							do_action('wpallexport_action_after');
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
		 * @throws Exception
		 */
		public function shortcodeDispatcher($args, $content, $tag) {

			$controllerName = self::PREFIX . preg_replace_callback('%(^|_).%', array($this, "replace_callback"), $tag);// capitalize first letters of class name parts and add prefix
			$controller = new $controllerName();
			if ( ! $controller instanceof PMXE_Controller) {
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
		 * @param string $page
		 * @param string $action
		 * @throws Exception
		 * @internal param $string [optional] $page When $page set to empty string ealier buffered content is outputted, otherwise controller is called based on $page value
		 */
		public function adminDispatcher($page = '', $action = 'index') {
			if ('' === $page) {				
				if ( ! is_null(self::$buffer)) {
					echo '<div class="wrap">';
					echo self::$buffer;
					do_action('wpallexport_action_after');
					echo '</div>';
				} elseif ( ! is_null(self::$buffer_callback)) {
					echo '<div class="wrap">';
					call_user_func(self::$buffer_callback);
					do_action('wpallexport_action_after');
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
					if(strpos($className, '_') !== false) {
						$pathAlt = str_replace('_',DIRECTORY_SEPARATOR, $pathAlt);
					}
					if (is_file($pathAlt)) {
						require $pathAlt;
						return TRUE;
					}
				}
			}

			if(strpos($className, '\\') !== false){
				// project-specific namespace prefix
				$prefix = 'Wpae\\';

				// base directory for the namespace prefix
				$base_dir = self::ROOT_DIR . '/src/';

				// does the class use the namespace prefix?
				$len = strlen($prefix);
				if (strncmp($prefix, $className, $len) !== 0) {
					// no, move to the next registered autoloader
					return;
				}

				// get the relative class name
				$relative_class = substr($className, $len);

				// replace the namespace prefix with the base directory, replace namespace
				// separators with directory separators in the relative class name, append
				// with .php
				$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

				// if the file exists, require it
				if (file_exists($file)) {
					require $file;
				}
			}
			
			return FALSE;
		}

		/**
		 * Get plugin option
		 * @param string [optional] $option Parameter to return, all array of options is returned if not set
		 * @return mixed
		 * @throws Exception
		 */
		public function getOption($option = NULL) {
			$options = apply_filters('wp_all_export_config_options', $this->options);			
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
		 * @param null $value
		 * @return array
		 * @throws Exception
		 * @internal param $mixed [optional] $value New value for the option, if not set than 1st parameter is supposed to be array of name => value pairs
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
			$options_default = PMXE_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();
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
		            }
		            switch_to_blog($old_blog);
		            return;	         
		        }	         
		    }

			dbDelta($plugin_queries);		

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
			
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp_all_export_plugin' );							
			
			load_plugin_textdomain( 'wp_all_export_plugin', false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages" );
		}	

		public function fix_db_schema(){

			global $wpdb;
			$installed_ver = get_option( "wp_all_export_db_version" );

			if ( $installed_ver == PMXE_VERSION ) return true;

			if ( ! empty($wpdb->charset))
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate))
				$charset_collate .= " COLLATE $wpdb->collate";

			$table_prefix = $this->getTablePrefix();

			$wpdb->query("CREATE TABLE IF NOT EXISTS {$table_prefix}templates (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(200) NOT NULL DEFAULT '',
				options LONGTEXT,
				PRIMARY KEY  (id)
			) $charset_collate;");

			$wpdb->query("CREATE TABLE IF NOT EXISTS {$table_prefix}posts (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				post_id BIGINT(20) UNSIGNED NOT NULL,
				export_id BIGINT(20) UNSIGNED NOT NULL,	
				iteration BIGINT(20) NOT NULL DEFAULT 0,
				PRIMARY KEY  (id)	
			) $charset_collate;");

			$googleCatsTableExists = $wpdb->query("SHOW TABLES LIKE '{$table_prefix}google_cats'");
			if(!$googleCatsTableExists) {
				require_once self::ROOT_DIR . '/schema.php';
				$wpdb->query($googleCatsQueryCreate);
				$wpdb->query($googleCatsQueryData);
			}

			$table = $this->getTablePrefix() . 'exports';
			$tablefields = $wpdb->get_results("DESCRIBE {$table};");
			$iteration = false;
			$parent_id = false;
			$export_post_type = false;			

			// Check if field exists
			foreach ($tablefields as $tablefield) {
				if ('iteration' == $tablefield->Field) $iteration = true;
				if ('parent_id' == $tablefield->Field) $parent_id = true;
				if ('export_post_type' == $tablefield->Field) $export_post_type = true;
			}

			if ( ! $iteration ){				
				$wpdb->query("ALTER TABLE {$table} ADD `iteration` BIGINT(20) NOT NULL DEFAULT 0;");
			}
			if ( ! $parent_id ){				
				$wpdb->query("ALTER TABLE {$table} ADD `parent_id` BIGINT(20) NOT NULL DEFAULT 0;");
			}
			if ( ! $export_post_type ){				
				$wpdb->query("ALTER TABLE {$table} ADD `export_post_type` VARCHAR(64) NOT NULL DEFAULT '';");
			}

			update_option( "wp_all_export_db_version", PMXE_VERSION );
		}

		/**
		 * Determine is current export was created before current version
		 */
		public static function isExistingExport( $checkVersion = false ){

			$input  = new PMXE_Input();
			$export_id = $input->get('id', 0);

			if (empty($export_id)) $export_id = $input->get('export_id', 0);

			// ID not found means this is new export
			if (empty($export_id)) return false;

			if ( ! $checkVersion ) $checkVersion = PMXE_VERSION;

			$export = new PMXE_Export_Record();
			$export->getById($export_id);
			if ( ! $export->isEmpty() && (empty($export->options['created_at_version']) || version_compare($export->options['created_at_version'], $checkVersion) < 0 )){
				return true;
			}

			return false;
		}

		/**
		 * Determine is current export is first time running
		 */
		public static function isNewExport(){

			$input  = new PMXE_Input();
			$export_id = $input->get('id', 0);

			if (empty($export_id)) $export_id = $input->get('export_id', 0);

			if (empty($export_id)) $export_id = XmlExportEngine::$exportID;

			// ID not found means this is new export
			if (empty($export_id)) return true;

			$export = new PMXE_Export_Record();
			$export->getById($export_id);
			if ( ! $export->isEmpty() && ! $export->iteration ){
				return true;
			}

			return false;
		}

		/**
		 * Method returns default import options, main utility of the method is to avoid warnings when new
		 * option is introduced but already registered imports don't have it
		 */
		public static function get_default_import_options() {
			return array(
				'cpt' => array(),	
				'whereclause' => '',
				'joinclause' => '',
				'filter_rules_hierarhy' => '',
				'product_matching_mode' => 'parent',
				'order_item_per_row' => 1,
				'order_item_fill_empty_columns' => 1,
				'filepath' => '',				
				'current_filepath' => '',
				'bundlepath' => '',
				'export_type' => 'specific',
				'wp_query' => '',	
				'wp_query_selector' => 'wp_query',
				'is_user_export' => false,
				'is_comment_export' => false,
				'export_to' => 'csv',	
				'export_to_sheet' => 'csv',
				'delimiter' => ',',
				'encoding' => 'UTF-8',
				'is_generate_templates' => 1,				
				'is_generate_import' => 1,				
				'import_id' => 0,									
				'template_name' => '',				
				'is_scheduled' => 0,
				'scheduled_period' => '',				
				'scheduled_email' => '',
				'cc_label' => array(),
				'cc_type' => array(),
				'cc_value' => array(),
				'cc_name' => array(),
				'cc_php' => array(),
				'cc_code' => array(),
				'cc_sql' => array(),				
				'cc_options' => array(),
				'cc_settings' => array(),
				'friendly_name' => '',
				'fields' => array('default', 'other', 'cf', 'cats'),
				'ids' => array(),
				'rules' => array(),
				'records_per_iteration' => 50,
				'include_bom' => 0,
				'include_functions' => 1,
				'split_large_exports' => 0,
				'split_large_exports_count' => 10000,
				'split_files_list' => array(),
				'main_xml_tag' => 'data',
				'record_xml_tag' => 'post',
				'save_template_as' => 0,
				'name' => '',
				'export_only_new_stuff' => 0,
				'export_only_modified_stuff' => 0,
				'creata_a_new_export_file' => 0,
				'attachment_list' => array(),
				'order_include_poducts' => 0,
				'order_include_all_poducts' => 0,
				'order_include_coupons' => 0,
				'order_include_all_coupons' => 0,
				'order_include_customers' => 0,
				'order_include_all_customers' => 0,
				'migration' => '',
				'xml_template_type' => 'simple',
				'custom_xml_template' => '',
				'custom_xml_template_header' => '',
				'custom_xml_template_loop' => '',
				'custom_xml_template_footer' => '',				
				'custom_xml_template_options' => array(),
        		'custom_xml_cdata_logic' => 'auto',
				'show_cdata_in_preview' => 0,
				'taxonomy_to_export' => '',
				'created_at_version' => '',
        		'export_variations' => XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION,
				'export_variations_title' => XmlExportEngine::VARIATION_USE_PARENT_TITLE,
				'show_cdata_in_preview' => 0,
				'include_header_row' => 1,
				'wpml_lang' => 'all'
			);
		}		

		public static function is_ajax(){
			return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false ;
		}

	}

	PMXE_Plugin::getInstance();	

	// Include the api front controller
	include_once('wpae_api.php');
	
}
