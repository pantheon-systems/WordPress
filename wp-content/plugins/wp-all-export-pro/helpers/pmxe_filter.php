<?php
function pmxe_filter($value, $custom_func){
	if ( ! empty($custom_func) and "" != $custom_func and function_exists($custom_func)){
		return call_user_func($custom_func, $value);		
	}
	return $value;
}