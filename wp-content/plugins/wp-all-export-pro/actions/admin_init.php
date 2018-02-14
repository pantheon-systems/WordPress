<?php
	
function pmxe_admin_init(){
	wp_enqueue_script('pmxe-script', PMXE_ROOT_URL . '/static/js/pmxe.js', array('jquery'), PMXE_VERSION);     
}