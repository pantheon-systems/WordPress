<?php

namespace Wpae\App\Controller;

use PMXE_Export_Record;
use PMXE_Input;
use WP_Error;
use Wpae\App\Service\TemplateManager;
use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;
use PMXE_Plugin;
use XmlExportEngine;

class ExportController extends BaseController
{
    private $input;

    private $errors;

    private $data = array();

    private $isWizard = true;

    public $baseUrlParamNames = array('page', 'pagenum', 'order', 'order_by', 'type', 's', 'f');

    public function saveAction(Request $request)
    {
        die('Not Supported');
    }

    public function getAction(Request $request)
    {
        if(!$request->get('id')) {
            $sessionData = PMXE_Plugin::$session->get_session_data();
            $exportData = unserialize($sessionData['google_merchants_post_data']);
        } else {
            $id = $_GET['id'];
            $export = new \PMXE_Export_Record();
            if ($export->getById($id)->isEmpty()) { // specified import is not found
                wp_redirect(add_query_arg('page', 'pmxe-admin-manage', admin_url('admin.php'))); die();
            }

            $exportData = $export->options['google_merchants_post_data'];
        }

        if($exportData === 'false' || !$exportData) {
            $exportData = null;
        }

        return new JsonResponse($exportData);
    }
    
}