jQuery( function($) {

    WCML_Troubleshooting = {


        init:  function(){

            $(document).ready( function(){

                //troubleshooting page
                jQuery('#wcml_trbl').on('click',function(){
                    var field = jQuery(this);
                    field.attr('disabled', 'disabled');
                    jQuery('.spinner').css('display','inline-block').css('visibility','visible');
                    if(jQuery('#wcml_sync_update_product_count').is(':checked')){
                        WCML_Troubleshooting.update_product_count();
                    }else{
                        WCML_Troubleshooting.run_next_troubleshooting_action();
                    }
                });

                jQuery('#attr_to_duplicate').on('change',function(){
                    jQuery('.attr_status').html(jQuery(this).find('option:selected').attr('rel'))
                    jQuery('#count_terms').val(jQuery(this).find('option:selected').attr('rel'))
                });

                jQuery('#wcml_product_type_trbl').on('click',function(){
                    var field = jQuery(this);
                    field.attr('disabled', 'disabled');
                    jQuery('.product_type_spinner').css('display','inline-block').css('visibility','visible');

                    WCML_Troubleshooting.fix_product_type_terms();
                });
            });
        },
        update_product_count: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_update_count",
                    wcml_nonce: jQuery('#trbl_update_count_nonce').val()
                },
                dataType: 'json',
                success: function( response ) {
                    jQuery('.var_status').each(function(){
                        jQuery(this).html(response.data.count);
                    })
                    jQuery('#count_prod_variat').val(response.data.count);
                    WCML_Troubleshooting.run_next_troubleshooting_action();
                }
            });
        },

        sync_variations: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_variations",
                    wcml_nonce: jQuery('#trbl_sync_variations_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(jQuery('#count_prod_variat').val() == 0){
                        jQuery('.var_status').each(function(){
                            jQuery(this).html(0);
                        });
                        WCML_Troubleshooting.run_next_troubleshooting_action();
                    }else{
                        var left = jQuery('#count_prod_variat').val()-3;
                        if(left < 0 ){
                            left = 0;
                        }
                        jQuery('.var_status').each(function(){
                            jQuery(this).html(left);
                        });
                        jQuery('#count_prod_variat').val(left);
                        WCML_Troubleshooting.sync_variations();
                    }
                }
            });
        },

        sync_product_gallery: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_gallery_images",
                    wcml_nonce: jQuery('#trbl_gallery_images_nonce').val(),
                    page: jQuery('#sync_galerry_page').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(jQuery('#count_galleries').val() == 0){
                        WCML_Troubleshooting.run_next_troubleshooting_action();
                        jQuery('.gallery_status').html(0);
                    }else{
                        var left = jQuery('#count_galleries').val()-5;
                        if(left < 0 ){
                            left = 0;
                        }else{
                            jQuery('#sync_galerry_page').val(parseInt(jQuery('#sync_galerry_page').val())+1)
                        }
                        jQuery('.gallery_status').html(left);
                        jQuery('#count_galleries').val(left);
                        WCML_Troubleshooting.sync_product_gallery();
                    }
                }
            });
        },

        sync_product_categories: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_categories",
                    wcml_nonce: jQuery('#trbl_sync_categories_nonce').val(),
                    page: jQuery('#sync_category_page').val()
                },
                success: function(response) {
                    if(jQuery('#count_categories').val() == 0){
                        WCML_Troubleshooting.run_next_troubleshooting_action();
                        jQuery('.cat_status').html(0);
                    }else{
                        var left = jQuery('#count_categories').val()-5;
                        if(left < 0 ){
                            left = 0;
                        }else{
                            jQuery('#sync_category_page').val(parseInt(jQuery('#sync_category_page').val())+1)
                        }
                        jQuery('.cat_status').html(left);
                        jQuery('#count_categories').val(left);
                        WCML_Troubleshooting.sync_product_categories();
                    }
                }
            });
        },

        duplicate_terms: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_duplicate_terms",
                    wcml_nonce: jQuery('#trbl_duplicate_terms_nonce').val(),
                    attr: jQuery('#attr_to_duplicate option:selected').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(jQuery('#count_terms').val() == 0){
                        WCML_Troubleshooting.run_next_troubleshooting_action();
                        jQuery('.attr_status').html(0);
                    }else{
                        var left = jQuery('#count_terms').val()-5;
                        if(left < 0 ){
                            left = 0;
                        }
                        jQuery('.attr_status').html(left);
                        jQuery('#count_terms').val(left);

                        WCML_Troubleshooting.duplicate_terms();
                    }
                }
            });
        },

        sync_stock: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_stock",
                    wcml_nonce: jQuery('#trbl_sync_stock_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#count_stock').val(0);
                    WCML_Troubleshooting.run_next_troubleshooting_action();
                    jQuery('.stock_status').html(0);
                }
            });
        },

        fix_product_type_terms: function(){
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_fix_product_type_terms",
                    wcml_nonce: jQuery('#trbl_product_type_terms_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wcml_product_type_trbl').removeAttr('disabled');
                    jQuery('.product_type_spinner').hide();
                    jQuery('.product_type_fix_done').show();
                    setTimeout(function() {
                        jQuery('.product_type_fix_done').fadeOut( 300 );
                    }, 2000);

                }
            });
        },

        run_next_troubleshooting_action: function(){
           if(jQuery('#wcml_sync_product_variations').is(':checked') && parseInt( jQuery('#count_prod_variat').val() ) !== 0 ){
                WCML_Troubleshooting.sync_variations();
           }else if(jQuery('#wcml_sync_gallery_images').is(':checked') && parseInt( jQuery('#count_galleries').val() ) !== 0 ){
               WCML_Troubleshooting.sync_product_gallery();
           }else if(jQuery('#wcml_sync_categories').is(':checked') && parseInt( jQuery('#count_categories').val() ) !== 0 ){
                WCML_Troubleshooting.sync_product_categories();
            }else if(jQuery('#wcml_duplicate_terms').is(':checked') && parseInt( jQuery('#count_terms').val() ) !== 0 ){
                WCML_Troubleshooting.duplicate_terms();
            }else if(jQuery('#wcml_sync_stock').is(':checked') && parseInt( jQuery('#count_stock').val() ) !== 0 ){
                WCML_Troubleshooting.sync_stock();
            }else{
                jQuery('#wcml_trbl').removeAttr('disabled');
                jQuery('.spinner').hide();
                jQuery('#wcml_trbl').next().fadeOut();
            }
        }

    }

    WCML_Troubleshooting.init();

});


