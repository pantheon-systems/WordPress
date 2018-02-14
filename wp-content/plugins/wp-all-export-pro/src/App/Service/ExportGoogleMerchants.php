<?php

namespace Wpae\App\Service;


use PMXE_Plugin;
use Wpae\WordPress\Filters;
use XmlExportEngine;

class ExportGoogleMerchants
{
    const GOOGLE_MERCHANTS_DELIMITER = "\t";

    const GOOGLE_MERCHANTS_EXTENSION = 'txt';
    /**
     * @var GoogleMerchantsDataProcessor
     */
    private $googleMerchantsDataProcessor;
    /**
     * @var Filters
     */
    private $wordPressFilters;

    public function __construct(GoogleMerchantsDataProcessor $googleMerchantsDataProcessor, Filters $filters)
    {
        $this->googleMerchantsDataProcessor = $googleMerchantsDataProcessor;
        $this->wordPressFilters = $filters;

        XmlExportEngine::$exportOptions['cc_type'] = $this->googleMerchantsDataProcessor->exportFieldSlugs;
        XmlExportEngine::$exportOptions['cc_name'] = $this->googleMerchantsDataProcessor->exportFieldSlugs;
        XmlExportEngine::$exportOptions['cc_label'] = $this->googleMerchantsDataProcessor->exportFieldSlugs;
    }

    public function export($is_cron, $file_path, $exported_by_cron = 0)
    {
        ob_start();

        $stream = fopen("php://output", 'w');

        $headers = array();
        $articles = array();

        //TODO: Check if this can be moved from here
        $pmxePlugin = PMXE_Plugin::getInstance();
        $pmxePlugin->adminInit();

        $articles = $this->exportRequestedData($articles);
        $headers = $this->prepareCsvHeaders($headers, $stream, $exported_by_cron);

        foreach ($articles as $article) {
            $line = array();
            foreach ($headers as $header) {
                $line[$header] = (isset($article[$header])) ? $article[$header] : '';
            }
            fputcsv($stream, $line, self::GOOGLE_MERCHANTS_DELIMITER);
            $this->wordPressFilters->applyFilters('wp_all_export_after_csv_line', array($stream, XmlExportEngine::$exportID));
        }

        $response = $this->saveCsvToFile($file_path, $is_cron, $exported_by_cron);

        return $response;
    }

    /**
     * @param $headers
     * @param $stream
     * @return mixed
     */
    private function prepareCsvHeaders($headers, $stream, $exported = 0)
    {
        if (XmlExportEngine::$exportOptions['cc_name']) {
            foreach (XmlExportEngine::$exportOptions['cc_name'] as $ID => $value) {

                if (empty(XmlExportEngine::$exportOptions['cc_name'][$ID]) or empty(XmlExportEngine::$exportOptions['cc_type'][$ID]) or !is_numeric($ID)) continue;

                $this->prepare_csv_headers($headers, $ID);
            }
        }
        $headers = $this->wordPressFilters->applyFilters('wp_all_export_csv_headers', array($headers, XmlExportEngine::$exportID));

        if (!$exported) {
            fputcsv($stream, array_map(array('XmlCsvExport', '_get_valid_header_name'), $headers), self::GOOGLE_MERCHANTS_DELIMITER);
        }

        return $headers;
    }

    private function prepare_csv_headers( &$headers, $ID)
    {
        $element_name = (!empty(XmlExportEngine::$exportOptions['cc_name'][$ID])) ? XmlExportEngine::$exportOptions['cc_name'][$ID] : 'untitled_' . $ID;

        if (strpos(XmlExportEngine::$exportOptions['cc_label'][$ID], "item_data__") !== false) {
            XmlExportEngine::$woo_order_export->get_element_header($headers, XmlExportEngine::$exportOptions, $ID);
            return;
        }

        if ($element_name == 'ID') $element_name = 'id';

        if (!in_array($element_name, $headers)) {
            $headers[] = $element_name;
        } else {
            $is_added = false;
            $i = 0;
            do {
                $new_element_name = $element_name . '_' . md5($i);

                if (!in_array($new_element_name, $headers)) {
                    $headers[] = $new_element_name;
                    $is_added = true;
                }

                $i++;
            } while (!$is_added);
        }
    }

    /**
     * @param $articles
     * @return array
     * @internal param $acfs
     * @internal param $woo
     * @internal param $woo_order
     */
    private function exportRequestedData($articles)
    {
        while (XmlExportEngine::$exportQuery->have_posts()) {
            XmlExportEngine::$exportQuery->the_post();
            $record = get_post(get_the_ID());
            $articles[] = $this->googleMerchantsDataProcessor->processData($record);
            $articles = $this->wordPressFilters->applyFilters('wp_all_export_csv_rows', array($articles, XmlExportEngine::$exportOptions, XmlExportEngine::$exportID));
            do_action('pmxe_exported_post', $record->ID, XmlExportEngine::$exportRecord);
        }

        wp_reset_postdata();

        return $articles;
    }

    /**
     * @return bool
     */
    private function saveCsvToFile($file_path, $is_cron, $exported_by_cron)
    {
        if ($is_cron) {
            if ( ! $exported_by_cron ) {
                // The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.
                if (XmlExportEngine::$exportOptions['include_bom']) {
                    file_put_contents($file_path, chr(0xEF).chr(0xBB).chr(0xBF).ob_get_clean());
                }
                else {
                    file_put_contents($file_path, ob_get_clean());
                }
            }
            else {
                file_put_contents($file_path, ob_get_clean(), FILE_APPEND);
            }

            return $file_path;

        }
        else
        {
            if ( empty(PMXE_Plugin::$session->file) ){

                // generate export file name
                $export_file = wp_all_export_generate_export_file( XmlExportEngine::$exportID );

                // The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.
                if (XmlExportEngine::$exportOptions['include_bom']) {
                    file_put_contents($export_file, chr(0xEF).chr(0xBB).chr(0xBF).ob_get_clean());
                }
                else {
                    file_put_contents($export_file, ob_get_clean());
                }

                PMXE_Plugin::$session->set('file', $export_file);
                PMXE_Plugin::$session->save_data();

            }
            else {
                file_put_contents(PMXE_Plugin::$session->file, ob_get_clean(), FILE_APPEND);
            }

            return true;
        }

    }

    public function merge_headers( $file, &$headers )
    {
        $in  = fopen($file, 'r');

        $clear_old_headers = fgetcsv($in, 0, XmlExportEngine::$exportOptions['delimiter']);

        fclose($in);

        $old_headers = array();

        foreach ($clear_old_headers as $i => $header) {
            $header = str_replace("'", "", str_replace('"', "", str_replace(chr(0xEF).chr(0xBB).chr(0xBF), "", $header)));

            if ( ! in_array($header, $old_headers)) {
                $old_headers[] = $header;
            }
            else {
                $is_added = false;
                $i = 0;
                do {
                    $new_element_name = $header . '_' . md5($i);

                    if ( ! in_array($new_element_name, $old_headers) ) {
                        $old_headers[] = $new_element_name;
                        $is_added = true;
                    }

                    $i++;
                }
                while (!$is_added);
            }
        }
    }
}