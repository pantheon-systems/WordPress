<?php

function wp_all_export_get_export_format($options)
{
	return ($options['export_to'] == 'xml') ? 'xml' : ( empty($options['export_to_sheet']) ? 'csv' : $options['export_to_sheet']);	
}