<?php

function pmxe_site_transient_update_plugins($value, $transient = false){
    global $pagenow;
    $plugin_file = 'wp-all-export-pro/wp-all-export-pro.php';
    if ( isset( $value->response[ $plugin_file ] ) && empty($value->response[ $plugin_file ]->download_link) && $pagenow == 'update-core.php') {
        unset($value->response[ $plugin_file ]);
    }
    return $value;
}