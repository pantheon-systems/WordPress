<?php
/**
 * Introduce special type for controllers which render pages inside admin area
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
abstract class PMXI_Controller_Admin extends PMXI_Controller {
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
		
		$p_url = parse_url( site_url() );

		$url = $p_url['scheme'] . '://' . $p_url['host'];

		if (!empty($_POST['is_settings_submitted'])) { // save settings form			
			$input = new PMXI_Input();
			$post = $input->post(array(
				'port' => ''
			));	
			PMXI_Plugin::getInstance()->updateOption($post);
		}
		
		$port = PMXI_Plugin::getInstance()->getOption('port');	

		if ( ! empty($port) and is_numeric($port) ){
            $url .= ':' . $port;
        }
        else{
            $url = ( ! empty($p_url['port']) && ! in_array( $p_url['port'], array( 80, 443 ) ) ) ? $url . ':' . $p_url['port'] : $url;
        }

		if ($remove) {
			$this->baseUrl = $url . remove_query_arg($remove);
		} else {
			$this->baseUrl = $url . $_SERVER['REQUEST_URI'];
		}
		
		parent::__construct();
		
		// add special filter for url fields
		$this->input->addFilter(create_function('$str', 'return "http://" == $str || "ftp://" == $str ? "" : $str;'));
		
		// enqueue required sripts and styles
		global $wp_styles;
		if ( ! is_a($wp_styles, 'WP_Styles'))
			$wp_styles = new WP_Styles();
		
		wp_enqueue_style('jquery-ui', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/css/redmond/jquery-ui.css');
		wp_enqueue_style('jquery-tipsy', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/css/smoothness/jquery.tipsy.css');
		wp_enqueue_style('pmxi-admin-style', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin.css', array(), PMXI_VERSION);
		wp_enqueue_style('pmxi-admin-style-ie', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin-ie.css');		
		wp_enqueue_style('jquery-select2', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/css/select2/select2.css');
		wp_enqueue_style('jquery-select2', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/css/select2/select2-bootstrap.css');
		add_editor_style( WP_ALL_IMPORT_ROOT_URL . '/static/css/custom-editor-style.css' );
		wp_enqueue_style('jquery-codemirror', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/codemirror.css', array(), PMXI_VERSION);

		$wp_styles->add_data('pmxi-admin-style-ie', 'conditional', 'lte IE 7');
		wp_enqueue_style('wp-pointer');			
		
		if ( version_compare(get_bloginfo('version'), '3.8-RC1') >= 0 ){
			wp_enqueue_style('pmxi-admin-style-wp-3.8', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin-wp-3.8.css');
		}
		if ( version_compare(get_bloginfo('version'), '4.0-beta3') >= 0 ){
			wp_enqueue_style('pmxi-admin-style-wp-4.0', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin-wp-4.0.css');
		}
		if ( version_compare(get_bloginfo('version'), '4.4') >= 0 ){
			wp_enqueue_style('pmxi-admin-style-wp-4.4', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin-wp-4.4.css');
		}

		$scheme_color = get_user_option('admin_color') and is_file(PMXI_Plugin::ROOT_DIR . '/static/css/admin-colors-' . $scheme_color . '.css') or $scheme_color = 'fresh';
		if (is_file(PMXI_Plugin::ROOT_DIR . '/static/css/admin-colors-' . $scheme_color . '.css')) {
			wp_enqueue_style('pmxi-admin-style-color', WP_ALL_IMPORT_ROOT_URL . '/static/css/admin-colors-' . $scheme_color . '.css');
		}
	
		wp_enqueue_script('jquery-ui-datepicker', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/ui.datepicker.js', 'jquery-ui-core');
		//wp_enqueue_script('wp-all-import-autocomplete', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/ui.autocomplete.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position'));
		wp_enqueue_script('jquery-tipsy', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/jquery.tipsy.js', 'jquery');
		wp_enqueue_script('jquery-nestable', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/jquery.mjs.nestedSortable.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-tabs', 'jquery-ui-progressbar'));
		wp_enqueue_script('jquery-moment', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/moment.js', 'jquery', PMXI_VERSION);		
		wp_enqueue_script('jquery-select2', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/select2.min.js', 'jquery');
		wp_enqueue_script('jquery-ddslick', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/jquery.ddslick.min.js', 'jquery');
		wp_enqueue_script('jquery-contextmenu', WP_ALL_IMPORT_ROOT_URL . '/static/js/jquery/jquery.ui-contextmenu.min.js', array('jquery', 'jquery-ui-menu'));
		wp_enqueue_script('jquery-codemirror', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/codemirror.js', array(), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-matchbrackets', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/matchbrackets.js', array('jquery-codemirror'), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-htmlmixed', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/htmlmixed.js', array('jquery-codemirror-matchbrackets'), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-xml', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/xml.js', array('jquery-codemirror-htmlmixed'), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-javascript', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/javascript.js', array('jquery-codemirror-xml'), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-clike', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/clike.js', array('jquery-codemirror-javascript'), PMXI_VERSION);
		wp_enqueue_script('jquery-codemirror-php', WP_ALL_IMPORT_ROOT_URL . '/static/codemirror/php.js', array('jquery-codemirror-clike'), PMXI_VERSION);

		wp_enqueue_script('wp-pointer');

		/* load plupload scripts */
		wp_deregister_script('swfupload-all');
		wp_deregister_script('swfupload-handlers');
		wp_enqueue_script('swfupload-handlers', site_url() . "/wp-includes/js/swfupload/handlers.js", array('jquery'), '2201-20100523');

		wp_enqueue_script('jquery-plupload', WP_ALL_IMPORT_ROOT_URL . '/static/js/plupload/wplupload.js', array('plupload', 'jquery'));

		wp_enqueue_script('pmxi-admin-script', WP_ALL_IMPORT_ROOT_URL . '/static/js/admin.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-autocomplete'), PMXI_VERSION);
							
	}	
	
	/**
	 * @see Controller::render()
	 */
	protected function render($viewPath = NULL)
	{
		// assume template file name depending on calling function
		if (is_null($viewPath)) {
			$trace = debug_backtrace();
			$viewPath = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXI_Plugin::PREFIX, '%') . '%', '', strtolower($trace[1]['class']))) . '/' . $trace[1]['function'];
		}
		
		// render contextual help automatically
		$viewHelpPath = $viewPath;
		// append file extension if not specified
		if ( ! preg_match('%\.php$%', $viewHelpPath)) {
			$viewHelpPath .= '.php';
		}
		$viewHelpPath = preg_replace('%\.php$%', '-help.php', $viewHelpPath);
		$fileHelpPath = PMXI_Plugin::ROOT_DIR . '/views/' . $viewHelpPath;
				
		if (is_file($fileHelpPath)) { // there is help file defined
			ob_start();
			include $fileHelpPath;
			add_contextual_help(PMXI_Plugin::getInstance()->getAdminCurrentScreen()->id, ob_get_clean());
		}
		
		parent::render($viewPath);
	}
	
}