jQuery(document).ready(function ($) {
    var discard = false;

    window.onbeforeunload = function (e) {
        if (discard) {
            return $('#wcml_warn_message').val();
        }
    }

    $('.wcml-section input[type="submit"]').click(function () {
        discard = false;
    });

    $('.wcml_search').click(function () {
        window.location = $('.wcml_products_admin_url').val() + '&cat=' + $('.wcml_product_category').val() + '&trst=' + $('.wcml_translation_status').val() + '&st=' + $('.wcml_product_status').val() + '&slang=' + $('.wcml_translation_status_lang').val();
    });

    $('.wcml_search_by_title').click(function () {
        window.location = $('.wcml_products_admin_url').val() + '&s=' + $('.wcml_product_name').val();
    });

    $('.wcml_reset_search').click(function () {
        window.location = $('.wcml_products_admin_url').val();
    });

    var wcml_product_rows_data = new Array();
    var wcml_get_product_fields_string = function (row) {
        var string = '';
        row.find('input[type=text], textarea').each(function () {
            string += $(this).val();
        });

        return string;
    }


    $('#wcml_custom_exchange_rates').submit(function () {

        var thisf = $(this);

        thisf.find(':submit').parent().prepend(icl_ajxloaderimg + '&nbsp;')
        thisf.find(':submit').prop('disabled', true);

        $.ajax({

            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: thisf.serialize(),
            success: function () {
                thisf.find(':submit').prev().remove();
                thisf.find(':submit').prop('disabled', false);
            }

        })

        return false;
    })

    function wcml_remove_custom_rates(post_id) {

        var thisa = $(this);

        $.ajax({

            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {action: 'wcml_remove_custom_rates', 'post_id': post_id},
            success: function () {
                thisa.parent().parent().parent().fadeOut(function () {
                    $(this).remove()
                });
            }

        })

        return false;

    }

    $(document).on('click', '.wcml_save_base', function (e) {
        e.preventDefault();

        var elem = $(this);
        var dialog_saving_data = $(this).closest('.wcml-dialog-container');
        var link = '#wcml-edit-base-slug-' + elem.attr('data-base') + '-' + elem.attr('data-language') + '-link';
        var dialog_container = '#wcml-edit-base-slug-' + elem.attr('data-base') + '-' + elem.attr('data-language');
        $.ajax({
            type: "post",
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: "wcml_update_base_translation",
                base: elem.attr('data-base'),
                base_value: dialog_saving_data.find('#base-original').val(),
                base_translation: dialog_saving_data.find('#base-translation').val(),
                language: elem.attr('data-language'),
                wcml_nonce: $('#wcml_update_base_nonce').val()
            },
            success: function (response) {
                $(dialog_container).remove();
                $(link).find('i').remove();
                $(link).append('<i class="otgs-ico-edit" >');
                $(link).parent().prepend(response);
            }
        })
    });

    $(document).on('click', '.hide-rate-block', function () {

        var wrap = $(this).closest('.wcml-wrap');

        $(this).attr('disabled', 'disabled');
        var ajaxLoader = $('<span class="spinner" style="visibility: visible;">');
        var setting = jQuery(this).data('setting');
        $(this).parent().prepend(ajaxLoader);
        $(this).remove();

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'wcml_update_setting_ajx',
                setting: setting,
                value: 1,
                nonce: $('#wcml_settings_nonce').val()
            },
            success: function (response) {
                wrap.hide();
            }
        });
        return false;
    });


});

