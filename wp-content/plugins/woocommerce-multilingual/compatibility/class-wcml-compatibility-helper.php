<?php

class WCML_Compatibility_Helper{

    function get_product_type($product_id){

        if ( $terms = wp_get_object_terms( $product_id, 'product_type' ) ) {
            $product_type = sanitize_title( current( $terms )->name );
        } else {
            $product_type = apply_filters( 'default_product_type', 'simple' );
        }

        return $product_type;

    }

}

?>