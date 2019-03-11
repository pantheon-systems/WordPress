jQuery(document).ready(function ($) {

    jQuery(document).on( 'click', '.wcml_removed_cart_items_clear', function(e){
        e.preventDefault();

        jQuery.ajax({
            type : 'post',
            url : woocommerce_params.ajax_url,
            data : {
                action: 'wcml_cart_clear_removed_items',
                wcml_nonce: jQuery('#wcml_clear_removed_items_nonce').val()
            },
            success: function(response) {
                window.location = window.location.href;
            }
        });
    });

});

