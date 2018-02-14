<?php
if ( ! function_exists('str_getcsv')):
/**
 * str_getcsv function for PHP less than 5.3
 * @see http://php.net/manual/en/function.str-getcsv.php
 * NOTE: function doesn't support escape paramter (in correspondance to fgetcsv not supporting it prior 5.3)
 */
function str_getcsv($input, $delimiter=',', $enclosure='"') {
	if ("" == $delimiter) $delimiter = ',';
	$temp = fopen("php://memory", "rw");
	fwrite($temp, $input);
	fseek($temp, 0);
	$r = fgetcsv($temp, strlen($input), $delimiter, $enclosure);
	fclose($temp);
	return $r;
}

endif;