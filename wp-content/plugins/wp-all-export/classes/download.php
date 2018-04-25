<?php

class PMXE_Download
{

	static public function zip($file_name)
	{
		$uploads    = wp_upload_dir();
		$bundle_url = $uploads['baseurl'] . str_replace($uploads['basedir'], '', $file_name);
        $bundle_url = str_replace( "\\", "/", $bundle_url );
		wp_redirect($bundle_url);
		die;
	}

	static public function xls($file_name)
	{
		self::sendFile("Content-Type: application/vnd.ms-excel; charset=UTF-8", $file_name);
	}

    static public function xlsx($file_name)
    {
        self::sendFile("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8", $file_name);
    }

	static public function csv($file_name)
	{
       self::sendFile("Content-Type: text/plain; charset=UTF-8", $file_name);
	}

	static public function txt($file_name)
	{
	    self::sendFile("Content-Type: text/plain; charset=UTF-8", $file_name);
	}

	static public function xml($file_name)
	{
        self::sendFile("Content-Type: application/xhtml+xml; charset=UTF-8", $file_name);
	}

	static public function sendFile($header, $file_name)
    {
        // If we ar testing don't sent it as attachment
        if (php_sapi_name() != 'cli-server') {
            header($header);
            header("Content-Disposition: attachment; filename=\"" . basename($file_name) . "\"");
        }
        while (ob_get_level()) {
            ob_end_clean();
        }

        readfile($file_name);
        die;
    }

}