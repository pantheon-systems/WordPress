<?php
/**
 * Common logic for all shortcodes plugin implements
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
abstract class PMXE_Controller {
	/**
	 * Input class instance to retrieve parameters submitted during page request
	 * @var PMXE_Input
	 */
	protected $input;
	/**
	 * Error messages
	 * @var WP_Error
	 */
	protected $errors;
	/**
	 * Associative array of data which will be automatically available as variables when template is rendered
	 * @var array
	 */
	public $data = array();
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->input = new PMXE_Input();
		$this->input->addFilter('trim');			

		$this->errors = new WP_Error();
		
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
	 * @param string [optional] $viewPath Template path to render
	 * @throws Exception
	 */
	protected function render($viewPath = null) {
		
		if ( ! get_current_user_id() or ! current_user_can( PMXE_Plugin::$capabilities )) {
		    // This nonce is not valid.
		    die( 'Security check' ); 

		} else {
			
			// assume template file name depending on calling function
			if (is_null($viewPath)) {
				$trace = debug_backtrace();
				$viewPath = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXE_Plugin::PREFIX, '%') . '%', '', strtolower($trace[1]['class']))) . '/' . $trace[1]['function'];
			}
			// append file extension if not specified
			if ( ! preg_match('%\.php$%', $viewPath)) {
				$viewPath .= '.php';
			}
			$filePath = PMXE_Plugin::ROOT_DIR . '/views/' . $viewPath;
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
		if (is_wp_error($msgs)) 
		{			
			unset($msgs->errors['count-validation']);
						
			$msgs = $msgs->get_error_messages();
		}		
		if ( ! is_array($msgs)) {
			$msgs = array($msgs);
		}
		$this->data['errors'] = $msgs;
		
		$viewPathRel = str_replace('_', '/', preg_replace('%^' . preg_quote(PMXE_Plugin::PREFIX, '%') . '%', '', strtolower(get_class($this)))) . '/error.php';
		if (is_file(PMXE_Plugin::ROOT_DIR . '/views/' . $viewPathRel)) { // if calling controller class has specific error view
			$this->render($viewPathRel);
		} else { // render default error view
			$this->render('controller/error.php');
		}
	}
	
	public function download(){


		$nonce = (!empty($_REQUEST['_wpnonce'])) ? $_REQUEST['_wpnonce'] : '';
		if ( ! wp_verify_nonce( $nonce, '_wpnonce-download_feed' ) && !isset($_GET['google_feed']) ) {
		    die( __('Security check', 'wp_all_export_plugin') );
		} else {

			$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

			$id = $this->input->get('id');

			$export = new PMXE_Export_Record();

			$filepath = '';
			
			if ( ! $export->getById($id)->isEmpty())
			{
				if($export->options['export_to'] != XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS && isset($_GET['google_feed'])) {
					die('Unauthorized');
				}
				if ( ! $is_secure_import)
				{
					$filepath = get_attached_file($export->attch_id);					
				}
				else
				{
					$filepath = wp_all_export_get_absolute_path($export->options['filepath']);
				}				
				if ( @file_exists($filepath) )
				{
					switch ($export['options']['export_to']) 
					{
						case XmlExportEngine::EXPORT_TYPE_XML:

							if($export['options']['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS) {
								PMXE_download::txt($filepath);
							} else {
								PMXE_download::xml($filepath);
							}

							break;
						case XmlExportEngine::EXPORT_TYPE_CSV:
							if (empty($export->options['export_to_sheet']) or $export->options['export_to_sheet'] == 'csv')
							{
								PMXE_download::csv($filepath);		
							}							
							else 
							{
                                switch ($export->options['export_to_sheet']){
                                    case 'xls':
                                        PMXE_download::xls($filepath);
                                        break;
                                    case 'xlsx':
                                        PMXE_download::xlsx($filepath);
                                        break;
                                }
							}							
							break;

						default:
							
							break;
					}
				}					
			}	
		}
	}
}