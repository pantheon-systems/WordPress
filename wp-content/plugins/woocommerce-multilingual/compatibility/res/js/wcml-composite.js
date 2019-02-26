jQuery( document ).ready( function( $ ){
    //lock fields
    if( typeof lock_settings != 'undefined'  && typeof lock_settings.lock_fields != 'undefined' && lock_settings.lock_fields == 1 ) {

        $('#bto_config_group_inner .remove_row,.add_bto_group,.save_composition').each(function(){
            $(this).attr('disabled','disabled');
            $(this).after($('.wcml_lock_img').clone().removeClass('wcml_lock_img').show());
        });

        $('#bto_product_data li,#bto_config_group_inner .subsubsub li a').bind({
            click: function(e) {
                return false;
            }
        });


        //components fields
        jQuery('input.group_quantity_min,input.group_quantity_max,input.group_discount,.bto_query_type_selector .wc-product-search').each(function(){
            jQuery(this).attr('readonly','readonly');
            jQuery(this).after(jQuery('.wcml_lock_img').clone().removeClass('wcml_lock_img').show());
        });

        jQuery('select.bto_query_type,.component_options_style select,.group_optional input').each(function(){
            jQuery(this).attr('disabled','disabled');
            jQuery(this).after(jQuery('.wcml_lock_img').clone().removeClass('wcml_lock_img').show());
        });
    }

});



