jQuery(document).ready(function () {
    var dashboard_dropdown = jQuery('#dropdown_dashboard_currency').clone();
    jQuery('#dropdown_dashboard_currency').remove();
    dashboard_dropdown.insertBefore('.sales-this-month a').show();
    jQuery('#woocommerce_dashboard_status .wc_status_list li').css('display', 'table');
});

jQuery(document).on('change', '#dropdown_dashboard_currency', function () {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'wcml_dashboard_set_currency',
            currency: jQuery('#dropdown_dashboard_currency').val(),
            wcml_nonce: wcml_admin_currency_selector.nonce
        },
        error: function(xhr, status, error){
            alert(xhr.responseJSON.data);
        },
        success: function (response) {
            if ( response.success ) {
                window.location = window.location.href;
            }
        }
    })
});