<?php
	
function pmxi_admin_init(){
    
	wp_enqueue_script('wp-all-import-script', WP_ALL_IMPORT_ROOT_URL . '/static/js/wp-all-import.js', array('jquery'), PMXI_VERSION);
    wp_enqueue_style('wp-all-import-updater', WP_ALL_IMPORT_ROOT_URL . '/static/css/plugin-update-styles.css', array(), PMXI_VERSION);

    @ini_set('mysql.connect_timeout', 300);
    @ini_set('default_socket_timeout', 300);
	
}