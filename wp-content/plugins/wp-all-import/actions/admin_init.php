<?php
	
function pmxi_admin_init(){
    
	wp_enqueue_script('wp-all-import-script', WP_ALL_IMPORT_ROOT_URL . '/static/js/wp-all-import.js', array('jquery'), PMXI_VERSION);	

    @ini_set('mysql.connect_timeout', 300);
    @ini_set('default_socket_timeout', 300);    

 //    if (isset($_GET['addon_notice_ignore']) && '1' == $_GET['addon_notice_ignore'] && isset($_GET['addon_slug']) ) {
	// 	update_option($_GET['addon_slug'] . '_notice_ignore', 'true');
	// }
	
}