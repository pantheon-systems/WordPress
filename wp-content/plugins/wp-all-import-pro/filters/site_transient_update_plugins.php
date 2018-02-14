<?php

function pmxi_site_transient_update_plugins($value, $transient){
    global $pagenow;
    $plugin_file = 'wp-all-import-pro/wp-all-import-pro.php';
    if ( isset( $value->response[ $plugin_file ] ) && empty($value->response[ $plugin_file ]->download_link) && $pagenow == 'update-core.php') {
        unset($value->response[ $plugin_file ]);
    }
    return $value;
}