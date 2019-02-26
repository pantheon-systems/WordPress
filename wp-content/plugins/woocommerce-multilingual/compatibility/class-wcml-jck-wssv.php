<?php

class WCML_JCK_WSSV{

    private $transient_name = 'jck_wssv_term_counts';

    public function  __construct(){

        add_filter( 'pre_transient_' . $this->transient_name, array( $this, 'get_language_specific_transient' ) );
        add_filter( 'set_transient_' . $this->transient_name, array( $this, 'set_language_specific_transient' ), 10, 2 );
    }

    public function get_language_specific_transient(){
        return get_transient( $this->transient_name . '_' . ICL_LANGUAGE_CODE );
    }

    public function set_language_specific_transient( $value, $expiration ){

        delete_transient( $this->transient_name );
        set_transient( $this->transient_name . '_' . ICL_LANGUAGE_CODE, $value, $expiration );

    }

}