<?php

function pmxe_wp_loaded() {				
	
	@ini_set("max_input_time", PMXE_Plugin::getInstance()->getOption('max_input_time'));
	@ini_set("max_execution_time", PMXE_Plugin::getInstance()->getOption('max_execution_time'));
}