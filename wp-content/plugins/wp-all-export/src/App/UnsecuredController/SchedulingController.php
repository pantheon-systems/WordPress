<?php

namespace Wpae\App\UnsecuredController;


use Wpae\Controller\BaseController;
use Wpae\Http\Request;
use Wpae\Scheduling\Export;
use Wpae\Http\JsonResponse;

class SchedulingController extends BaseController
{
    /** Scheduling API Version */
    const VERSION = 1;

    /** @var Export */
    private $scheduledExportService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->scheduledExportService = new Export();
    }

    public function triggerAction(Request $request)
    {
        if (!$this->isRequestValid()) {
            return new JsonResponse(array('message' => 'Export hash is invalid'), 401);
        }

        $exportId = intval($request->get('export_id'));

        $export = new \PMXE_Export_Record();
        $export->getById($exportId);

        if ($export->isEmpty()) {
            return new JsonResponse(array('message' => 'Export not found'), 404);
        }

        if ((int)$export->executing) {
            return new JsonResponse(array("message" => "Export #" . $export->id . " is currently in manually process. Request skipped."), 409);
        }
        if ($export->processing and !$export->triggered) {
            return new JsonResponse(array("message" => "Export #" . $export->id . " currently in process. Request skipped."), 409);

        }
        if (!$export->processing and $export->triggered) {
            return new JsonResponse(array("message" => "Export #" . $export->id . " already triggered. Request skipped."), 409);
        }

        if (!$export->processing and !$export->triggered) {
            $this->scheduledExportService->trigger($export);

            return new JsonResponse(array('message' => "#" . $export->id . " Cron job triggered."));
        }

        return new JsonResponse(array("message" => "Can't process"), 500);
    }

    public function processAction(Request $request)
    {
        if (!$this->isRequestValid()) {
            return new JsonResponse(array('message' => 'Export hash is invalid'), 401);
        }

        $exportId = intval($request->get('export_id'));

        $export = new \PMXE_Export_Record();
        $export->getById($exportId);

        if ($export->isEmpty()) {
            return new JsonResponse(array('message' => 'Export not found'), 404);
        }

        $logger = create_function('$m', 'echo "<p>$m</p>\\n";');

        if ($export->processing == 1 and (time() - strtotime($export->registered_on)) > 120) {
            // it means processor crashed, so it will reset processing to false, and terminate. Then next run it will work normally.
            $export->set(array(
                'processing' => 0
            ))->update();
        }

        // start execution imports that is in the cron process
        if (!(int)$export->triggered) {
            if (!empty($export->parent_id) or empty($queue_exports)) {
                return new JsonResponse(array("message" => 'Export #' . $exportId . ' is not triggered. Request skipped.'), 400);
            }
        } elseif ((int)$export->executing) {
            return new JsonResponse(array('message' => 'Export #' . $exportId . ' is currently in manually process. Request skipped.'), 409);
        } elseif ((int)$export->triggered and !(int)$export->processing) {

            $export->set(array('canceled' => 0))->execute($logger, true);

            if (!(int)$export->triggered and !(int)$export->processing) {
                $this->scheduledExportService->process($export);
                return new JsonResponse(array('Export #' . $exportId . ' complete'), 201);
            } else {
                return new JsonResponse(array('message' => 'Records Processed ' . (int)$export->exported . '.'));
            }

        } else {
            return new JsonResponse(array('message' => 'Export #' . $exportId . ' already processing. Request skipped.'), 409);
        }

        return new JsonResponse(array("message" => "Can't process"), 500);
    }

    public function versionAction()
    {
        return new JsonResponse(array('version' => self::VERSION));
    }

    /**
     * @return bool
     */
    private function isRequestValid()
    {
        $cron_job_key = \PMXE_Plugin::getInstance()->getOption('cron_job_key');
        return
            !empty($cron_job_key) and
            !empty($_GET['export_id']) and
            !empty($_GET['export_key']) and
            $_GET['export_key'] == $cron_job_key;
    }

}