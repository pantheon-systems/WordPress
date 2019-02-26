<?php

class PMXI_Error{

    public $recordNumber;

    public function __construct($recordNumber = false) {
        $this->recordNumber = $recordNumber;
    }

    public function handle(){

        $error = $this->getLastError();
        $trace = $this->trace();
        if($error && strpos($error['file'], 'functions.php') !== false){
            $wp_uploads = $this->getUploadsDir();
            $functions = 'in '.$wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php:'.$error['line'];
            $error['message'] = str_replace($functions, '', $error['message']);
            $error['message'] = str_replace("\\n",'',$error['message']);
            $errorParts = explode('Stack trace', $error['message']);
            $error['message'] = $errorParts[0];
            $error['message'] .=' on line '.$error['line'];
            $error['message'] = str_replace("\n",'',$error['message']);
            $error['message'] = str_replace("Uncaught Error:", '', $error['message']);
            $error['message'] = 'PHP Error: ' . $error['message'];
            $error['message'] = str_replace('  ', ' ', $error['message']);
            echo "[[ERROR]]";
            if($error['message'] == '') {
                $error['message'] = __('An unknown error occured', 'wp_all_import_plugin');
            }
            $this->terminate(json_encode(array('error' => '<span class="error">'.$error['message'].' of the Functions Editor'.'</span>', 'line' => $error['line'], 'title' => __('PHP Error','wp_all_import_plugin'))));
        } else if(strpos($error['file'], 'XMLWriter.php') !== false ) {
            if(strpos($error['message'],'syntax error, unexpected') !== false) {
                echo "[[ERROR]]";
                $this->terminate(json_encode(array('error'=>__('You probably forgot to close a quote', 'wp_all_import_plugin'),'title' => __('PHP Error','wp_all_import_plugin'))));
            }
        }
    }

    /**
     * @return array
     */
    protected function getLastError()
    {
        return error_get_last();
    }

    /**
     * @return mixed
     */
    protected function getUploadsDir()
    {
        return wp_upload_dir();
    }

    /**
     * Hack to be able to test the class in isolation
     *
     * @param $message
     */
    protected function terminate($message)
    {
        exit($message);
    }

    protected function trace(){
        $e = new Exception();
        return $e->getTraceAsString();
//        return debug_backtrace();
    }

    public function import_data_handler($errno, $errstr, $errfile, $errline) {
        error_log('Found import exception: ' . $errstr . ' ' . $errno . ' ' . $errfile . ' ' . $errline . ' for record #' . $this->recordNumber);
//        trigger_error('TEST');
//        throw new XmlImportException($errstr, $errno, 0, $errfile, $errline);
    }

    public function parse_data_handler($errno, $errstr, $errfile, $errline) {
        error_log('Found parse exception: ' . $errstr . ' ' . $errno . ' ' . $errfile . ' ' . $errline);
        throw new XmlImportException($errstr, $errno);
    }
}