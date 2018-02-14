<?php

// Let's bootstrap
function wpae_api() {

    if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
        exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
    }

    if ( ! current_user_can( \PMXE_Plugin::$capabilities ) ){
        exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
    }

    $container = new \Wpae\Di\WpaeDi(array());

    $request = new \Wpae\Http\Request(file_get_contents('php://input'));

    $q = $_GET['q'];
    $routeParts = explode('/', $q);
    $controller = 'Wpae\\App\\Controller\\'.ucwords($routeParts[0]).'Controller';
    $action = ucwords($routeParts[1]).'Action';

    $controller = new $controller($container);
    $response = $controller->$action($request);

    if(!$response instanceof \Wpae\Http\Response) {
        throw new Exception('The controller must return an HttpResponse instance.');
    }

    $response->render();
}

add_action( 'wp_ajax_wpae_api', 'wpae_api' );