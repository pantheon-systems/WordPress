<?php

if ( ! function_exists('wp_all_import_ctx_mapping')){
	function wp_all_import_ctx_mapping( $ctx, $mapping_rules, $tx_name ){		
		if ( ! empty( $mapping_rules) and $ctx['is_mapping']){			
			foreach ($mapping_rules as $rule) {
				foreach ($rule as $key => $value) {					
					if ( trim($ctx['name']) == trim($key) || str_replace("&amp;", "&", trim($ctx['name'])) == str_replace("&amp;", "&", trim($key)) ){ 
						$ctx['name'] = trim($value);
						break;
					}
				}							
			}			
		}				
		return apply_filters('pmxi_single_category', $ctx, $tx_name);
	}
}