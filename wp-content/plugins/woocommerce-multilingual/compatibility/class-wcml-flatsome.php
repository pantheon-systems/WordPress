<?php

class WCML_Flatsome{

    function __construct(){
        add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'add_action_to_multi_currency_ajax' ) );
    }

    function add_action_to_multi_currency_ajax($actions){
        $actions[] = 'ux_quickview';
        return $actions;
    }


}

