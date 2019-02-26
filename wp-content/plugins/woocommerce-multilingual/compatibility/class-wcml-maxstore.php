<?php

class WCML_MaxStore{

    function add_hooks(){

	    add_filter( 'wcml_force_reset_cart_fragments', array( $this, 'wcml_force_reset_cart_fragments' ) );

    }

    public function wcml_force_reset_cart_fragments(){

        return 1;

    }

}
