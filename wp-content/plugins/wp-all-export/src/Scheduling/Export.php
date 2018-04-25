<?php

namespace Wpae\Scheduling;


class Export
{

    public function trigger($export)
    {
        $export->set(array(
            'triggered' => 1,
            'exported' => 0,
            'last_activity' => date('Y-m-d H:i:s')
        ))->update();
    }

    /**
     * @param $export
     * @return array
     */
    public function process($export)
    {
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
    }
}