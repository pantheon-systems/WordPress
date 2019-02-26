jQuery(document).ready(function($){

    maybe_show_wcml_shedule_link();

    $(document).on('click','.woocommerce_variations h3', function( e ){
        maybe_show_wcml_shedule_link();
    });

    $(document).on('change','.wcml_custom_prices_input', function(){
        if($(this).val() == 1){
            $(this).closest('.wcml_custom_prices_block').find('.wcml_automaticaly_prices_block').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block').show();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_hide').show();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_show').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_auto_block_hide').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_auto_block_show').show();
            $(this).parent().find('.block_actions').hide();
        }else{
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_hide').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_show').hide();
            $(this).parent().find('.block_actions').show();
        }
    });

    $(document).on('click','.wcml_custom_prices_auto_block_show', function( e ){
        e.preventDefault();
        if($(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_input:checked').val() == 0){
            if(!$(this).closest('.wcml_custom_prices_block').find('.wcml_automaticaly_prices_block').is(':visible')){
                $(this).hide();
                $(this).closest('.wcml_custom_prices_block').find('.wcml_automaticaly_prices_block').show();
                $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_auto_block_hide').show();
            }
        }
    });

    $(document).on('click','.wcml_custom_prices_auto_block_hide', function( e ){
        e.preventDefault();
        if($(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_input:checked').val() == 0){
            if($(this).closest('.wcml_custom_prices_block').find('.wcml_automaticaly_prices_block').is(':visible')){
                $(this).hide();
                $(this).closest('.wcml_custom_prices_block').find('.wcml_automaticaly_prices_block').hide();
                $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_auto_block_show').show();
            }
        }
    });

    $(document).on('click','.wcml_custom_prices_manually_block_hide', function( e ){
        e.preventDefault();

        if($(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_input:checked').val() == 1){
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_hide').hide();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_show').show();
        }
    });

    $(document).on('click','.wcml_custom_prices_manually_block_show', function( e ){
        e.preventDefault();
        if($(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_input:checked').val() == 1){
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block').show();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_hide').show();
            $(this).closest('.wcml_custom_prices_block').find('.wcml_custom_prices_manually_block_show').hide();
        }
    });

    $(document).on('change','#_regular_price', function(){
        var val = $(this).val();
        $(this).closest('div').find('input[name="_readonly_regular_price"]').each(function(){
             $(this).val(val*$(this).attr('rel'));
        });
    });

    $(document).on('change','#_sale_price', function(){
        var val = $(this).val();
        $(this).closest('div').find('input[name="_readonly_sale_price"]').each(function(){
            $(this).val(val*$(this).attr('rel'));
        });
    });

    $(document).on('change','input[name^="variable_regular_price"]', function(){
        var val = $(this).val();
        $(this).closest('table').find('input[name="_readonly_regular_price"]').each(function(){
            $(this).val(val*$(this).attr('rel'));
        });
    });

    $(document).on('change','input[name^="variable_sale_price"]', function(){
        var val = $(this).val();
        $(this).closest('table').find('input[name="_readonly_sale_price"]').each(function(){
            $(this).val(val*$(this).attr('rel'));
        });
    });

    $(document).on('change','input.wcml_input_price', function(){
        if($(this).val() > 0){
            $(this).closest('.currency_blck').find('.wcml_no_price_message').hide();
        }
    });

    $(document).on('change','.wcml_schedule_input', function(){
        if($(this).val() == 1){
            datepick();
            $(this).closest('div').find('.wcml_schedule_dates').show();
            $(this).closest('div').find('.wcml_schedule_manually_block_show').hide();
            $(this).closest('div').find('.wcml_schedule_manually_block_hide').show();
            $(this).parent().find('.block_actions').show();
        }else{
            $(this).closest('div').find('.wcml_schedule_dates').hide();
            $(this).closest('div').find('.wcml_schedule_manually_block_show').show();
            $(this).closest('div').find('.wcml_schedule_manually_block_hide').hide();
            $(this).parent().find('.block_actions').hide();
        }
    });

    $(document).on('click','.wcml_schedule_manually_block_hide', function( e ){
        e.preventDefault();
        if($(this).closest('div').find('.wcml_schedule_input:checked').val() == 1){
            $(this).closest('div').find('.wcml_schedule_dates').hide();
            $(this).closest('div').find('.wcml_schedule_manually_block_show').show();
            $(this).closest('div').find('.wcml_schedule_manually_block_hide').hide();
        }
    });

    $(document).on('click','.wcml_schedule_manually_block_show', function( e ){
        e.preventDefault();
        if($(this).closest('div').find('.wcml_schedule_input:checked').val() == 1){
            datepick();
            $(this).closest('div').find('.wcml_schedule_dates').show();
            $(this).closest('div').find('.wcml_schedule_manually_block_show').hide();
            $(this).closest('div').find('.wcml_schedule_manually_block_hide').show();
        }
    });


    $(document).on('keyup','.wcml_sale_price', function(){
        if( parseInt($(this).val()) > parseInt($(this).closest('div').find('.wcml_regular_price').val()) ){
            if( $(this).closest('p').find('.wcml_price_error').size() == 0 )
                $(this).after($('.wcml_price_error').clone().show());
        }else{
            $(this).closest('p').find('.wcml_price_error').remove();
        }
    });

    $(document).on('change','.wcml_sale_price', function(){
        if( parseInt($(this).val()) > parseInt($(this).closest('div').find('.wcml_regular_price').val()) ){
            $(this).val($(this).closest('div').find('.wcml_regular_price').val());
            $(this).closest('p').find('.wcml_price_error').remove();
        }
    });

    function maybe_show_wcml_shedule_link(){
        $('.wcml_schedule_input').each(function(){
            if($(this).is(':checked') && $(this).val() == 1){
                $(this).parent().find('.block_actions').show();
            }
        });
    }

    function datepick(){
    var date_img = '';
    if(typeof woocommerce_admin_meta_boxes != 'undefined'){
        date_img = woocommerce_admin_meta_boxes.calendar_image;
    }else{
        date_img = woocommerce_writepanel_params.calendar_image;
    }

    $( ".wcml_schedule_dates input" ).datepicker({
        defaultDate: "",
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showButtonPanel: true,
        showOn: "button",
        buttonImage: date_img,
        buttonImageOnly: true,
        onSelect: function( selectedDate ) {

            var instance = $( this ).data( "datepicker" ),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings );

            if($(this).is('.custom_sale_price_dates_from')){
                $(this).closest('div').find('.custom_sale_price_dates_to').datepicker( "option", "minDate", date );
            }else{
                $(this).closest('div').find('.custom_sale_price_dates_from').datepicker( "option", "maxDate", date );
            }

        }
    });
    }       


});

