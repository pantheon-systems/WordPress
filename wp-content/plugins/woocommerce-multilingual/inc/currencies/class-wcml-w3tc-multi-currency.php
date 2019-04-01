<?php

class WCML_W3TC_Multi_Currency{

    function __construct(){

        add_filter( 'init' , array( $this, 'init' ), 15 );

    }

    function init(){

    	// Only needed for older W3TC versions
    	if( function_exists( 'w3_require_once' ) ){
		    add_action( 'wcml_switch_currency', array( $this, 'flush_page_cache' ) );
	    }

    }

    function flush_page_cache(){
	    w3_require_once( W3TC_LIB_W3_DIR . '/AdminActions/FlushActionsAdmin.php' );
        $flush = new W3_AdminActions_FlushActionsAdmin();
        $flush->flush_pgcache();
    }


}