<?php

namespace Wpae\App\Service;


class ScheduledExport
{
    /**
     * @param $export
     * @return JsonResponse
     */
    public function trigger($export)
    {
        if ((int)$export->executing) {
            return new JsonResponse(array(
                'status' => 403,
                'message' => sprintf(__('Export #%s is currently in manually process. Request skipped.', 'wp_all_export_plugin'), $export->id)
            ));
        }
        if ($export->processing and !$export->triggered) {
            return new JsonResponse(array(
                'status' => 403,
                'message' => sprintf(__('Export #%s currently in process. Request skipped.', 'wp_all_export_plugin'), $export->id)
            ));
        }
        if (!$export->processing and $export->triggered) {
            return new JsonResponse(array(
                'status' => 403,
                'message' => sprintf(__('Export #%s already triggered. Request skipped.', 'wp_all_export_plugin'), $export->id)
            ));
        }

        $export->set(array(
            'triggered' => 1,
            'exported' => 0,
            'last_activity' => date('Y-m-d H:i:s')
        ))->update();

        return new JsonResponse(array(
            'status' => 200,
            'message' => sprintf(__('#%s Cron job triggered.', 'wp_all_export_plugin'), $export->id)
        ));
    }

    /**
     * @param $export
     * @param $queue_exports
     * @param $logger
     */
    public function process($export, $queue_exports, $logger)
    {
        if ($export->processing == 1 and (time() - strtotime($export->registered_on)) > 120) { // it means processor crashed, so it will reset processing to false, and terminate. Then next run it will work normally.
            $export->set(array(
                'processing' => 0
            ))->update();
        }

        // start execution imports that is in the cron process
        if (!(int)$export->triggered) {
            if (!empty($export->parent_id) or empty($queue_exports)) {
                wp_send_json(array(
                    'status' => 403,
                    'message' => sprintf(__('Export #%s is not triggered. Request skipped.', 'wp_all_export_plugin'), $export->id)
                ));
            }
        } elseif ((int)$export->executing) {
            wp_send_json(array(
                'status' => 403,
                'message' => sprintf(__('Export #%s is currently in manually process. Request skipped.', 'wp_all_export_plugin'), $export->id)
            ));
        } elseif ((int)$export->triggered and !(int)$export->processing) {
            $response = $export->set(array('canceled' => 0))->execute($logger, true);

            if (!(int)$export->triggered and !(int)$export->processing) {

                // trigger update child exports with correct WHERE & JOIN filters
                if (!empty($export->options['cpt']) and class_exists('WooCommerce') and in_array('shop_order', $export->options['cpt']) and empty($export->parent_id)) {
                    $queue_exports = XmlExportWooCommerceOrder::prepare_child_exports($export, true);

                    if (empty($queue_exports)) {
                        delete_option('wp_all_export_queue_' . $export->id);
                    } else {
                        update_option('wp_all_export_queue_' . $export->id, $queue_exports);
                    }
                }
                // remove child export from queue
                if (!empty($export->parent_id)) {
                    $queue_exports = get_option('wp_all_export_queue_' . $export->parent_id);

                    if (!empty($queue_exports)) {
                        foreach ($queue_exports as $key => $queue_export) {
                            if ($queue_export == $export->id) {
                                unset($queue_exports[$key]);
                            }
                        }
                    }

                    if (empty($queue_exports)) {
                        delete_option('wp_all_export_queue_' . $export->parent_id);
                    } else {
                        update_option('wp_all_export_queue_' . $export->parent_id, $queue_exports);
                    }
                }

                wp_send_json(array(
                    'status' => 200,
                    'message' => sprintf(__('Export #%s complete', 'wp_all_export_plugin'), $export->id)
                ));
            } else {
                wp_send_json(array(
                    'status' => 200,
                    'message' => sprintf(__('Records Processed %s.', 'wp_all_export_plugin'), (int)$export->exported)
                ));
            }

        } else {
            wp_send_json(array(
                'status' => 403,
                'message' => sprintf(__('Export #%s already processing. Request skipped.', 'wp_all_export_plugin'), $export->id)
            ));
        }
    }
}