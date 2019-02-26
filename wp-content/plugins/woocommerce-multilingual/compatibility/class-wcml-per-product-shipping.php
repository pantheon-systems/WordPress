<?php

class WCML_Per_Product_Shipping{

    function __construct(){
        
        if(!is_admin()){
            
            add_filter('woocommerce_per_product_shipping_get_matching_rule_product_id', array( $this, 'original_product_id' ) );
            
        }
        
        
    }
    
    function original_product_id( $product_id ){
        global $sitepress;
        
        $trid = $sitepress->get_element_trid($product_id, 'post_product');
        $translations = $sitepress->get_element_translations($trid, 'post_product');
        foreach($translations as $language_code =>$translation){
            if($translation->original){
                $product_id = $translation->element_id;    
            }
        }
        
        return $product_id;
    }

}
