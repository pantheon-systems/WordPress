<?php
/**
 * Common logic for all shortcodes plugin implements
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
abstract class PMXI_Controller {
	/**
	 * Input class instance to retrieve parameters submitted during page request
	 * @var PMXI_Input
	 */
	protected $input;
	/**
	 * Error messages
	 * @var WP_Error
	 */
	protected $errors;
	/**
	 * Warning messages
	 * @var WP_Error
	 */
	protected $warnings;
	/**
	 * Associative array of data which will be automatically available as variables when template is rendered
	 * @var array
	 */
	public $data = array();
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->input = new PMXI_Input();
		$this->input->addFilter('trim');
		
		$this->errors = new WP_Error();
		$this->warnings = new WP_Error();
		
		$this->init();
	}
	
	/**
	 * Method to put controller initialization logic to
	 */
	protected function init() {}
	
	/**
	 * Checks wether protocol is HTTPS and redirects user to secure connection if not
	 */
	protected function force_ssl() {
		if (force_ssl_admin() && ! is_ssl()) {
			if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI'])); die();
			} else {
				wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); die();
			}
		}		
	}
	
	/**
	 * Method returning resolved template content
	 * 
	 * @param string[optional] $viewPath Template path to render
	 */
	protected function render($viewPath = null) {

		if ( ! get_current_user_id() or ! current_user_can( PMXI_Plugin::$capabilities )) {
		    // This nonce is not valid.
		    die( 'Security check' ); 
		} else {
			// assume template file name depending on calling function
			if (is_null($viewPath)) {
				$trace = debug_backtrace();
				$viewPath = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXI_Plugin::PREFIX, '%') . '%', '', strtolower($trace[1]['class']))) . '/' . $trace[1]['function'];
			}
			// append file extension if not specified
			if ( ! preg_match('%\.php$%', $viewPath)) {
				$viewPath .= '.php';
			}
			$filePath = PMXI_Plugin::ROOT_DIR . '/views/' . $viewPath;
			if (is_file($filePath)) {
				extract($this->data);
				include $filePath;
			} else {
				throw new Exception("Requested template file $filePath is not found.");
			}
		}
	}
	
	/**
	 * Display list of errors
	 * 
	 * @param string|array|WP_Error[optional] $msgs
	 */
	protected function error($msgs = NULL) {
		if (is_null($msgs)) {
			$msgs = $this->errors;
		}
		if (is_wp_error($msgs)) {
			unset($msgs->errors['root-element-validation']);
			unset($msgs->errors['upload-validation']);
			$msgs = $msgs->get_error_messages();
		}
		if ( ! is_array($msgs)) {
			$msgs = array($msgs);
		}
		$this->data['errors'] = $msgs;
		
		$viewPathRel = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXI_Plugin::PREFIX, '%') . '%', '', strtolower(get_class($this)))) . '/error.php';
		if (is_file(PMXI_Plugin::ROOT_DIR . '/views/' . $viewPathRel)) { // if calling controller class has specific error view
			$this->render($viewPathRel);
		} else { // render default error view
			$this->render('controller/error.php');
		}
	}

	/**
	 * Display list of warnings
	 * 
	 * @param string|array|WP_Error[optional] $msgs
	 */
	protected function warning($msgs = NULL) {
		if (is_null($msgs)) {
			$msgs = $this->warnings;
		}
		if (is_wp_error($msgs)) {			
			unset($msgs->errors['root-element-validation']);
			$msgs = $msgs->get_error_messages();
		}
		if ( ! is_array($msgs)) {
			$msgs = array($msgs);
		}
		$this->data['warnings'] = $msgs;
		
		$viewPathRel = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXI_Plugin::PREFIX, '%') . '%', '', strtolower(get_class($this)))) . '/warning.php';
		if (is_file(PMXI_Plugin::ROOT_DIR . '/views/' . $viewPathRel)) { // if calling controller class has specific error view
			$this->render($viewPathRel);
		} else { // render default error view
			$this->render('controller/warning.php');
		}
	}
	
}