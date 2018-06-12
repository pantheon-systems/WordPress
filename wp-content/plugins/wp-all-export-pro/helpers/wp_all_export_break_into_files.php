<?php

function wp_all_export_break_into_files($boundaryTag, $startAt, $maxItems, $rawdata, $fixedFooter, $fileNameTemplate) {

	$arr = explode("\n", $rawdata);
	$items = 0;
	$files = $startAt;
	$length = count($arr);
	$header = "";
	$footer = "";
	$chunk = "";
	$arrFiles = array();
	$boundaryIsFound = false;
	$fileWritten = false;

	// get footer data
	$footerBreak= "</" . trim($boundaryTag). ">";

	for ($i = $length-1; $i>= 0; $i--){
		$line = $arr[$i];
		if (strpos($line, $footerBreak) == false) {
			$footer = $line . "\r\n" . $footer;
		}
		else
			break;
	}

	// process main data
	for ($i = 0;$i < $length; $i++){
		$line  = $arr[$i];

		if (strpos($line, "<". trim($boundaryTag) . ">") !== false || strpos($line, "<" . trim($boundaryTag) . " ") !== false) {
			$items ++;
			$boundaryIsFound = true;
		}

		if (!$boundaryIsFound)
			$header .= $line . "\r\n";

		if ($items >= $maxItems) {
			$items = 0;
			$files++;

			$filename = str_replace('{FILE_COUNT_PLACEHOLDER}', $files, $fileNameTemplate);
			$f = fopen($filename, "w");
			fwrite($f,$header);
			fwrite($f, $chunk);
			if ($fixedFooter == null || $fixedFooter == '')
				fwrite($f, $footer);
			else
				fwrite($f, $fixedFooter);
			fclose($f);
			$arrFiles[] = $filename;
			$chunk = $line . "\r\n";
            $fileWritten = true;
		}
		else {
            $fileWritten = false;
			if ($boundaryIsFound)
				$chunk .= $line . "\r\n";
		}
	}

	if (!$fileWritten) {
		$files++;
		$filename = str_replace('{FILE_COUNT_PLACEHOLDER}', $files, $fileNameTemplate);

		$f = fopen($filename, "w");
		fwrite($f,$header);
		fwrite($f, $chunk);
		fclose($f);
		$arrFiles[] = $filename;
	}

	return $arrFiles;
} 