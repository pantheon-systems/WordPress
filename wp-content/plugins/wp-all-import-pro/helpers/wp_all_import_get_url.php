<?php
/**
 * Reading large files from remote server
 * @ $filePath - file URL
 * return local path of copied file
 */

if (!function_exists('wp_all_import_get_url')) {

    function wp_all_import_get_url($filePath, $targetDir = FALSE, $contentType = FALSE, $contentEncoding = FALSE, $detect = FALSE) {

        $type = $contentType;

        $uploads = wp_upload_dir();

        $targetDir = (!$targetDir) ? wp_all_import_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY) : $targetDir;

        $tmpname = wp_unique_filename($targetDir, ($type and strlen(basename($filePath)) < 30) ? basename($filePath) : time());

        $localPath = $targetDir . '/' . urldecode(sanitize_file_name($tmpname)) . ((!$type) ? '.tmp' : '');

        $is_valid = FALSE;

        $is_curl_download_only = apply_filters('wp_all_import_curl_download_only', false, $filePath);

        if ( ! $is_curl_download_only ){

            $file = ($contentEncoding == 'gzip') ? @fopen($filePath) : @fopen($filePath, "rb");

            if (is_resource($file)) {

                $fp = @fopen($localPath, 'w');
                $first_chunk = TRUE;
                while (!@feof($file)) {
                    $chunk = @fread($file, 1024);
                    if (!$type and $first_chunk and (strpos($chunk, "<?") !== FALSE or strpos($chunk, "<rss") !== FALSE) or strpos($chunk, "xmlns") !== FALSE) {
                        $type = 'xml';
                    }
                    elseif (!$type and $first_chunk) {
                        $type = 'csv';
                    } // if it's a 1st chunk, then chunk <? symbols to detect XML file
                    $first_chunk = FALSE;
                    @fwrite($fp, $chunk);
                }
                @fclose($file);
                @fclose($fp);

                $chunk = new PMXI_Chunk($localPath);

                if (!empty($chunk->options['element'])) {
                    $defaultXpath = "/" . $chunk->options['element'];
                    $is_valid = TRUE;
                }

                if ($is_valid) {

                    while ($xml = $chunk->read()) {

                        if (!empty($xml)) {

                            //PMXI_Import_Record::preprocessXml($xml);
                            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;

                            $dom = new DOMDocument('1.0', 'UTF-8');
                            $old = libxml_use_internal_errors(TRUE);
                            $dom->loadXML($xml);
                            libxml_use_internal_errors($old);
                            $xpath = new DOMXPath($dom);
                            if (($elements = $xpath->query($defaultXpath)) and $elements->length) {
                                break;
                            }
                        }
                    }

                    if (empty($xml)) {
                        $is_valid = FALSE;
                    }
                }
                unset($chunk);
            }
        }

        if (!$is_valid) {

            $request = get_file_curl($filePath, $localPath);

            if ( ! is_wp_error($request) ) {

                if ( ! $type ) {
                    if ($contentEncoding == 'gzip') {
                        $file = @fopen($localPath);
                    }
                    else {
                        $file = @fopen($localPath, "rb");
                    }
                    while (!@feof($file)) {
                        $chunk = @fread($file, 1024);
                        if (strpos($chunk, "<?") !== FALSE or strpos($chunk, "<rss") !== FALSE or strpos($chunk, "xmlns") !== FALSE) {
                            $type = 'xml';
                        }
                        else {
                            $type = 'csv';
                        } // if it's a 1st chunk, then chunk <? symbols to detect XML file
                        break;
                    }
                    @fclose($file);
                }
            }
            else {
                return $request;
            }

        }

        if (!preg_match('%\W(' . $type . ')$%i', basename($localPath))) {
            if (@rename($localPath, $localPath . '.' . $type)) {
                $localPath = $localPath . '.' . $type;
            }
        }

        return ($detect) ? array(
            'type' => $type,
            'localPath' => $localPath
        ) : $localPath;
    }
}	