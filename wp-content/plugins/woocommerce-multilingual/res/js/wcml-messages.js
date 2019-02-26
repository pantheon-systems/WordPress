jQuery(document).ready(function($){

    $(document).on('click', '.wcml_ignore_link', function(e){
        e.preventDefault();

        var elem = $(this);
        var setting = elem.attr('data-setting');
        var ajaxLoader = $('<span class="spinner" style="visibility: visible;margin: 0">');

        $(this).parent().append(ajaxLoader);
        $(this).hide();

        $.ajax({
            type : "post",
            url : ajaxurl,
            dataType: 'json',
            data : {
                action: "wcml_ignore_warning",
                setting: setting,
                wcml_nonce: $('#wcml_ignore_warning_nonce').val()
            },
            success: function(response) {
                elem.closest('.error').remove();
            }
        })

        return false;

    });

});

