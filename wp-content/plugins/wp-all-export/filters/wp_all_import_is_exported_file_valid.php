<?php

function pmxe_wp_all_import_is_exported_file_valid($is_valid, $export_id, $elements_cloud){		

	$exportRecord = new PMXE_Export_Record();
	$exportRecord->getById($export_id);
	if ( ! $exportRecord->isEmpty()){
		$exportOptions = $exportRecord->options;
		$required_fields = array();
		foreach ($exportOptions['ids'] as $ID => $value) {
			if (is_numeric($ID)){ 
				
				$element_name = ( ! empty($exportOptions['cc_name'][$ID]) ) ? preg_replace('/[^a-z0-9_]/i', '', $exportOptions['cc_name'][$ID]) : 'untitled_' . $ID;									

				switch ($exportOptions['cc_type'][$ID]) {
					case 'id':
						$required_fields[] = $element_name;
						break;
					case 'post_type':
						if ($exportOptions['export_type'] == 'advanced')
							$required_fields[] = $element_name;
						break;
					case 'woo':
						if ( ! empty($exportOptions['cpt']) and in_array('product', $exportOptions['cpt']))
						{
							switch ($exportOptions['cc_label'][$ID]) {
								case '_sku':
									$required_fields[] = $element_name;
									break;
							}
						}
						break;
					case 'cats':
						if ( ! empty($exportOptions['cpt']) and in_array('product', $exportOptions['cpt']))
						{
							switch ($exportOptions['cc_label'][$ID]) {
								case 'product_type':
									$required_fields[] = $element_name;
									break;
							}
						}
						break;
				}
			}
		}
		if ( ! empty($required_fields) ){			
			foreach ($required_fields as $field) {				
				if (empty($elements_cloud[$field]) and empty($elements_cloud[strtolower($field)])) {
					$is_valid = false;
					break;
				}
			}			
		}
	}
	
	return $is_valid;

}