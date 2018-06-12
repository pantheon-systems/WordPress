<?php
/**
 * Introduce special type for controllers which render pages inside admin area
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
abstract class PMXE_Controller_Admin extends PMXE_Controller {
	/**
	 * Admin page base url (request url without all get parameters but `page`)
	 * @var string
	 */
	public $baseUrl;
	/**
	 * Parameters which is left when baseUrl is detected
	 * @var array
	 */
	public $baseUrlParamNames = array('page', 'pagenum', 'order', 'order_by', 'type', 's', 'f');
	/**
	 * Whether controller is rendered inside wordpress page
	 * @var bool
	 */
	public $isInline = false;
	/**
	 * Constructor
	 */
	public function __construct() {		

		$remove = array_diff(array_keys($_GET), $this->baseUrlParamNames);
		if ($remove) {
			$this->baseUrl = remove_query_arg($remove);
		} else {
			$this->baseUrl = $_SERVER['REQUEST_URI'];
		}
		parent::__construct();
		
		// add special filter for url fields
		$this->input->addFilter(create_function('$str', 'return "http://" == $str || "ftp://" == $str ? "" : $str;'));
		
		// enqueue required sripts and styles
		global $wp_styles;
		if ( ! is_a($wp_styles, 'WP_Styles'))
			$wp_styles = new WP_Styles();
		
		wp_enqueue_style('jquery-ui', PMXE_ROOT_URL . '/static/js/jquery/css/redmond/jquery-ui.css', array('media-views'));
		wp_enqueue_style('jquery-tipsy', PMXE_ROOT_URL . '/static/js/jquery/css/smoothness/jquery.tipsy.css', array('media-views'));
		wp_enqueue_style('pmxe-admin-style', PMXE_ROOT_URL . '/static/css/admin.css',array('media-views'), PMXE_VERSION);
		wp_enqueue_style('pmxe-admin-style-ie', PMXE_ROOT_URL . '/static/css/admin-ie.css', array('media-views'));
		wp_enqueue_style('jquery-select2', PMXE_ROOT_URL . '/static/js/jquery/css/select2/select2.css', array('media-views'));
		wp_enqueue_style('jquery-select2', PMXE_ROOT_URL . '/static/js/jquery/css/select2/select2-bootstrap.css', array('media-views'));
		wp_enqueue_style('jquery-chosen', PMXE_ROOT_URL . '/static/js/jquery/css/chosen/chosen.css', array('media-views'));
		wp_enqueue_style('jquery-codemirror', PMXE_ROOT_URL . '/static/codemirror/codemirror.css', array('media-views'), PMXE_VERSION);
		wp_enqueue_style('jquery-timepicker', PMXE_ROOT_URL . '/static/js/jquery/css/timepicker/jquery.timepicker.css', array('media-views'), PMXE_VERSION);
		wp_enqueue_style('pmxe-angular-scss', PMXE_ROOT_URL . '/dist/styles.css', array('media-views'), PMXE_VERSION);

		$wp_styles->add_data('pmxe-admin-style-ie', 'conditional', 'lte IE 7');
		wp_enqueue_style('wp-pointer');		
		
		if ( version_compare(get_bloginfo('version'), '3.8-RC1') >= 0 ){			
			wp_enqueue_style('pmxe-admin-style-wp-3.8', PMXE_ROOT_URL . '/static/css/admin-wp-3.8.css', array('media-views'));
		}

		if ( version_compare(get_bloginfo('version'), '4.4') >= 0 ){			
			wp_enqueue_style('pmxe-admin-style-wp-4.4', PMXE_ROOT_URL . '/static/css/admin-wp-4.4.css', array('media-views'));
		}

		$scheme_color = get_user_option('admin_color') and is_file(PMXE_Plugin::ROOT_DIR . '/static/css/admin-colors-' . $scheme_color . '.css') or $scheme_color = 'fresh';
		if (is_file(PMXE_Plugin::ROOT_DIR . '/static/css/admin-colors-' . $scheme_color . '.css')) {
			wp_enqueue_style('pmxe-admin-style-color', PMXE_ROOT_URL . '/static/css/admin-colors-' . $scheme_color . '.css', array('media-views'));
		}
	
		wp_enqueue_script('jquery-ui-datepicker', PMXE_ROOT_URL . '/static/js/jquery/ui.datepicker.js', 'jquery-ui-core');
		wp_enqueue_script('jquery-tipsy', PMXE_ROOT_URL . '/static/js/jquery/jquery.tipsy.js', 'jquery');
		wp_enqueue_script('jquery-pmxe-nestable', PMXE_ROOT_URL . '/static/js/jquery/jquery.mjs.pmxe_nestedSortable.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-tabs', 'jquery-ui-progressbar'));
		wp_enqueue_script('jquery-select2', PMXE_ROOT_URL . '/static/js/jquery/select2.min.js', 'jquery');
		wp_enqueue_script('jquery-ddslick', PMXE_ROOT_URL . '/static/js/jquery/jquery.ddslick.min.js', 'jquery');
		wp_enqueue_script('jquery-chosen', PMXE_ROOT_URL . '/static/js/jquery/chosen.jquery.js', 'jquery');
		wp_enqueue_script('jquery-codemirror', PMXE_ROOT_URL . '/static/codemirror/codemirror.js', array(), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-matchbrackets', PMXE_ROOT_URL . '/static/codemirror/matchbrackets.js', array('jquery-codemirror'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-htmlmixed', PMXE_ROOT_URL . '/static/codemirror/htmlmixed.js', array('jquery-codemirror-matchbrackets'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-xml', PMXE_ROOT_URL . '/static/codemirror/xml.js', array('jquery-codemirror-htmlmixed'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-javascript', PMXE_ROOT_URL . '/static/codemirror/javascript.js', array('jquery-codemirror-xml'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-clike', PMXE_ROOT_URL . '/static/codemirror/clike.js', array('jquery-codemirror-javascript'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-php', PMXE_ROOT_URL . '/static/codemirror/php.js', array('jquery-codemirror-clike'), PMXE_VERSION);
		wp_enqueue_script('jquery-codemirror-autorefresh', PMXE_ROOT_URL . '/static/codemirror/autorefresh.js', array('jquery-codemirror'), PMXE_VERSION);		
		wp_enqueue_script('jquery-timepicker', PMXE_ROOT_URL . '/static/js/jquery/jquery.timepicker.js', array('jquery'), PMXE_VERSION);

		wp_enqueue_script('wp-pointer');

		/* load plupload scripts */		
		wp_enqueue_script('pmxe-admin-script', PMXE_ROOT_URL . '/static/js/admin.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-autocomplete' ), PMXE_VERSION);
		wp_enqueue_script('pmxe-admin-validate-braces', PMXE_ROOT_URL . '/static/js/validate-braces.js', array('pmxe-admin-script' ), PMXE_VERSION);

		if(getenv('WPAE_DEV')) {
			wp_enqueue_script('pmxe-angular-app', PMXE_ROOT_URL . '/dist/app.js', array('jquery'), PMXE_VERSION);
		} else {
			wp_enqueue_script('pmxe-angular-app', PMXE_ROOT_URL . '/dist/app.min.js', array('jquery'), PMXE_VERSION);
		}
	}
	
	/**
	 * @see Controller::render()
	 */
	protected function render($viewPath = NULL)
	{
		// assume template file name depending on calling function
		if (is_null($viewPath)) {
			$trace = debug_backtrace();			
			$viewPath = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXE_Plugin::PREFIX, '%') . '%', '', strtolower($trace[1]['class']))) . '/' . $trace[1]['function'];
		}
		
		// render contextual help automatically
		$viewHelpPath = $viewPath;
		// append file extension if not specified
		if ( ! preg_match('%\.php$%', $viewHelpPath)) {
			$viewHelpPath .= '.php';
		}
		$viewHelpPath = preg_replace('%\.php$%', '-help.php', $viewHelpPath);
		$fileHelpPath = PMXE_Plugin::ROOT_DIR . '/views/' . $viewHelpPath;
				
		if (is_file($fileHelpPath)) { // there is help file defined
			ob_start();
			include $fileHelpPath;
			add_contextual_help(PMXE_Plugin::getInstance()->getAdminCurrentScreen()->id, ob_get_clean());
		}
		
		parent::render($viewPath);
	}
	
}