
jQuery( function($){

    WCML_Multi_Currency = {

        _currency_languages_saving : 0,

        init:  function(){

            $(document).ready( function(){

                WCML_Multi_Currency.setup_multi_currency_toggle();

                $(document).on('change','.currency_code select', WCML_Multi_Currency.select_currency);

                $(document).on('click','.delete_currency', WCML_Multi_Currency.delete_currency);

                $(document).on('click', '.wcml_currency_options .currency_options_save', WCML_Multi_Currency.save_currency);

                $(document).on('click','.js-display-tooltip', WCML_Multi_Currency.tooltip);

                $(document).on('click', '.currency_languages a.otgs-ico-no', WCML_Multi_Currency.enable_currency_for_language);
                $(document).on('click', '.currency_languages a.otgs-ico-yes', WCML_Multi_Currency.disable_currency_for_language);

                $(document).on('change', '.default_currency select', WCML_Multi_Currency.change_default_currency);

                WCML_Multi_Currency.setup_currencies_sorting();

                $(document).on('change','.currency_option_position', WCML_Multi_Currency.price_preview);
                $(document).on('change','.currency_option_thousand_sep', WCML_Multi_Currency.price_preview);
                $(document).on('change','.currency_option_decimal_sep', WCML_Multi_Currency.price_preview);
                $(document).on('change','.currency_option_decimals', WCML_Multi_Currency.price_preview);
                $(document).on('change','.currency_code select', WCML_Multi_Currency.price_preview);

                $(document).on('keypress', '.currency_option_decimals', function (event) {
                    // 8 for backspace, 0 for null values, 48-57 for 0-9 numbers
                    if (event.which != 8 && event.which != 0 && event.which < 48 || event.which > 57) {
                        event.preventDefault();
                    }
                });

                $(document).on('keyup','.wcml-exchange-rate', WCML_Multi_Currency.exchange_rate_check);

                if($('#wcml_mc_options').length){
                    WCML_Multi_Currency.wcml_mc_form_submitted = false;
                    WCML_Multi_Currency.read_form_fields_status();

                    window.onbeforeunload = function(e) {
                        if(
                            ( !WCML_Multi_Currency.wcml_mc_form_submitted && WCML_Multi_Currency.form_fields_changed() ) ||
                            WCML_Multi_Currency.is_update_currency_lang_in_progress()
                        ){
                            return $('#wcml_warn_message').val();
                        }
                    }

                    $('#wcml_mc_options').on('submit', function(){
                        WCML_Multi_Currency.wcml_mc_form_submitted = true;
                    })
                }

            } );

        },

        setup_multi_currency_toggle: function(){

            $('#multi_currency_independent').change(function(){

                if($(this).attr('checked') == 'checked'){
                    $('#currency-switcher, #currency-switcher-widget, #currency-switcher-product, #multi-currency-per-language-details, #online-exchange-rates').fadeIn();
                }else{
                    $('#currency-switcher, #currency-switcher-widget, #currency-switcher-product, #multi-currency-per-language-details, #online-exchange-rates').fadeOut();
                }

            })


        },

        select_currency: function(){
            var parent = $(this).closest('.wcml_currency_options');
            var close_button = parent.find('.wcml-dialog-close-button');
            close_button.attr('data-currency', $(this).val());
            close_button.attr('data-symbol', $(this).find('option:selected').attr('data-symbol'));
            parent.find('.this-currency').html( $(this).val() );

        },

        delete_currency: function(e){

            e.preventDefault();
            var is_return = false;
            var currency        = $(this).data('currency');
            var currency_name   = $(this).data('currency_name');
            var currency_symbol = $(this).data('currency_symbol');

            $( '.currency_lang_table .wcml-row-currency-lang:first .currency_languages').each( function(){
                if( !WCML_Multi_Currency.check_currency_language( $(this).find('li').data('lang'), currency ) ){
                    is_return = true;
                    return false;
                }
            });

            if( is_return ){
                return;
            }

            $('#currency_row_' + currency + ' .currency_action_update').hide();
            var ajaxLoader = $('<span class="spinner" style="visibility: visible;margin:0;">');
            $(this).hide();
            $(this).parent().append(ajaxLoader).show();

            $.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "wcml_delete_currency",
                    wcml_nonce: $('#del_currency_nonce').val(),
                    code: currency
                },
                success: function(response) {
                    $('#currency_row_' + currency).remove();
                    $('#currency_row_langs_' + currency).remove();
                    $('#currency_row_del_' + currency).remove();

                    $('#wcml_currencies_order .wcml_currencies_order_'+ currency).remove();

                    $('#wcml_currency_options_code_').prepend('<option data-symbol="' + currency_symbol + '" value="' + currency + '">' + currency_name + '</option>');
                    $('#wcml_currency_options_code_').val(currency).trigger('change');

                    //remove from default currency list
                    $('#currency-lang-table').find('tr.default_currency select').each( function(){
                        $(this).find("option[value='"+currency+"']").remove();
                    });
                    $('.wcml-ui-dialog').each(function(){
                        WCML_Currency_Switcher_Settings.currency_switcher_preview( $(this) );
                    });


                    if( $('.wcml-row-currency').length == 1 ){
                        $('#online-exchange-rates-no-currencies').next().hide();
                        $('#online-exchange-rates-no-currencies').show();
                    }
                },
                done: function() {
                    ajaxLoader.remove();
                }
            });

            return false;

        },

        save_currency: function(){

            var parent = $(this).closest('.wcml-dialog-container');
            var chk_autosub = WCML_Multi_Currency.check_on_numeric(parent,'.abstract_amount');

            if( chk_autosub ){
                return false;
            }

            $('.wcml-currency-options-dialog :submit, .wcml-currency-options-dialog :button').prop('disabled', true);
            var currency = parent.find('[name="currency_options[code]"]').val();

            var ajaxLoader = $('<span class="spinner" style="visibility:visible;position:absolute;margin-left:10px;"></span>');

            ajaxLoader.show();
            $(this).parent().prepend(ajaxLoader);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: parent.find('[name^="currency_options"]').serialize() + '&action=wcml_save_currency&wcml_nonce=' + jQuery('#wcml_save_currency_nonce').val(),
                success: function(response){
                    parent.find('.wcml-dialog-close-button').trigger('click');

                    $('.wcml-ui-dialog').each(function(){
                        WCML_Currency_Switcher_Settings.currency_switcher_preview( $(this) );
                    });

                    if( $('#currency_row_' + currency).length == 0 ) {

                        var tr = $('#currency-table tr.wcml-row-currency:last').clone();
                        tr.attr('id', 'currency_row_' + currency);

                        var edit_link = tr.find('.wcml-col-edit a');
                        edit_link.attr('data-content', 'wcml_currency_options_' + currency);
                        edit_link.attr('data-currency', currency);
                        edit_link.data('dialog', 'wcml_currency_options_' + currency);
                        edit_link.removeClass('hidden');

                        $('#currency-table').find('tr.default_currency').before( tr );

                        var tr = $('.empty-currency-language-row').clone();
                        tr.attr('id', 'currency_row_langs_' + currency);
                        $('#currency-lang-table').find('tr.default_currency').before( tr );

                        tr.removeClass('hidden empty-currency-language-row');
                        tr.find('.on a').each( function(){
                            $(this).attr('data-currency', currency);
                            $(this).attr('title', $(this).attr('title').replace('%code%', response.currency_name));
                            $(this).attr('data-title-alt', $(this).attr('data-title-alt').replace('%code%', response.currency_name));
                        });

                        //add to default currency list
                        $('#currency-lang-table').find('tr.default_currency select').each( function(){
                            $(this).append('<option value="'+currency+'">'+currency+'</option>');
                        });

                        //add to orders list
                        $('#wcml_currencies_order').append('<li class="wcml_currencies_order_'+currency+' ui-sortable-handle" cur="'+currency+'">'+response.currency_name_formatted+'</li>');

                        var tr = $('#currency-delete-table tr.wcml-row-currency-del:last').clone();
                        tr.attr('id', 'currency_row_del_' + currency);

                        var del_link = tr.find('.delete_currency');
                        del_link.removeClass('hidden');
                        del_link.attr('data-currency', currency);
                        del_link.attr('data-currency_name', response.currency_name);
                        del_link.attr('data-currency_symbol', response.currency_symbol);
                        $('#currency-delete-table').find('tr.default_currency').before( tr );

                    }

                    $('#currency_row_' + currency + ' .wcml-col-currency').html(response.currency_name_formatted);
                    $('#currency_row_' + currency + ' .wcml-col-rate').html(response.currency_meta_info);

                    $('#wcml_currency_options_' + currency).remove();
                    $('#wcml_mc_options').before(response.currency_options);

                    $('#wcml_currency_options_code_ option[value="'+currency+'"]').remove();

                    if( $('#online-exchange-rates-no-currencies').is(':visible') ){
                        $('#online-exchange-rates-no-currencies').hide();
                        $('#online-exchange-rates-no-currencies').next().show();
                    }
                }

            })

            return false;
        },

        check_on_numeric: function(parent, elem){

            var messageContainer = $('<span class="wcml-error">');

            if(!WCML_Multi_Currency.is_number(parent.find(elem).val())){
                if(parent.find(elem).parent().find('.wcml-error').size() == 0){
                    parent.find(elem).parent().append( messageContainer );
                    messageContainer.text( parent.find(elem).data('message') );
                }
                return true;
            }else{
                if(parent.find(elem).parent().find('.wcml-error').size() > 0){
                    parent.find(elem).parent().find('.wcml-error').remove();
                }
                return false;
            }

        },

        tooltip: function(){
            var $thiz = $(this);

            // hide this pointer if other pointer is opened.
            $('.wp-pointer').fadeOut(100);

            $(this).pointer({
                content: '<h3>'+$thiz.data('header')+'</h3><p>'+$thiz.data('content')+'</p>',
                position: {
                    edge: 'left',
                    align: 'center',
                    offset: '15 0'
                }
            }).pointer('open');
        },

        enable_currency_for_language: function(e){

            if( WCML_Multi_Currency.is_update_currency_lang_in_progress() ) return false;

            e.preventDefault();
            $(this).addClass('spinner').removeClass('otgs-ico-no').css('visibility', 'visible');

            var index = $(this).closest('tr')[0].rowIndex;
            $('.default_currency select[rel="'+$(this).data('language')+'"]').append('<option value="'+$(this).data('currency')+'">'+$(this).data('currency')+'</option>');
            WCML_Multi_Currency.update_currency_lang($(this),1,0);

            var title_alt = $(this).data( 'title-alt' );
            $(this).data( 'title-alt', $(this).attr('title') );
            $(this).attr('title', title_alt);

        },

        disable_currency_for_language: function(e){

            if( WCML_Multi_Currency.is_update_currency_lang_in_progress() ) return false;

            e.preventDefault();

            $(this).addClass('spinner').removeClass('otgs-ico-yes').css('visibility', 'visible');

            var lang = $(this).data('language');

            if( !WCML_Multi_Currency.check_currency_language( lang ) ){
                $(this).removeClass('spinner').addClass('otgs-ico-yes');
                return;
            }

            var index = $(this).closest('tr')[0].rowIndex;

            if($('.currency_languages select[rel="'+$(this).data('language')+'"]').val() == $(this).data('currency')){
                WCML_Multi_Currency.update_currency_lang($(this),0,1);
            }else{
                WCML_Multi_Currency.update_currency_lang($(this),0,0);
            }
            $('.default_currency select[rel="'+$(this).data('language')+'"] option[value="'+$(this).data('currency')+'"]').remove();

            var title_alt = $(this).data( 'title-alt' );
            $(this).data( 'title-alt', $(this).attr('title') );
            $(this).attr('title', title_alt);

        },

        check_currency_language: function( lang, currency ){

            var elem = $( '#currency-lang-table a.otgs-ico-yes[data-language="'+lang+'"]' );

            if( currency ){
                elem = $( '#currency-lang-table a.otgs-ico-yes[data-language="'+lang+'"]' ).not( $( '[data-currency="'+currency+'"]' ) );
            }

            if( elem.length == 0 ){
                alert( $( '#wcml_warn_disable_language_massage' ).val() );
                return false;
            }

            return true;

        },

        is_update_currency_lang_in_progress: function(){
            var is =
                ( typeof WCML_Multi_Currency._update_currency_lang_sync_flag != 'undefined' )
                && WCML_Multi_Currency._update_currency_lang_sync_flag == 1;

            return is;
        },

        set_update_currency_lang_in_progress: function( val ){
            WCML_Multi_Currency._update_currency_lang_sync_flag = val;
        },

        update_currency_lang: function(elem, value, upd_def){

            WCML_Multi_Currency._currency_languages_saving++;
            $('#wcml_mc_options :submit').attr('disabled','disabled');

            $('input[name="wcml_mc_options"]').attr('disabled','disabled');

            var lang = elem.data('language');
            var code = elem.data('currency');
            discard = true;

            WCML_Multi_Currency.set_update_currency_lang_in_progress( 1 );

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'wcml_update_currency_lang',
                    value: value,
                    lang: lang,
                    code: code,
                    wcml_nonce: $('#update_currency_lang_nonce').val()
                },
                success: function(){
                    if(upd_def){
                        WCML_Multi_Currency.update_default_currency(lang,0);
                    }
                },
                complete: function() {
                    $('input[name="wcml_mc_options"]').removeAttr('disabled');
                    discard = false;

                    elem.removeClass('spinner').css('visibility', 'visible');
                    if(value){
                        elem.addClass('otgs-ico-yes');
                    }else{
                        elem.addClass('otgs-ico-no');
                    }

                    WCML_Multi_Currency._currency_languages_saving--;
                    if(WCML_Multi_Currency._currency_languages_saving == 0){
                        $('#wcml_mc_options :submit').removeAttr('disabled');
                    }
                    WCML_Multi_Currency.set_update_currency_lang_in_progress( 0 );
                }
            });

        },

        change_default_currency: function(){
            WCML_Multi_Currency.update_default_currency($(this).attr('rel'), $(this).val(), $(this) );
        },

        update_default_currency: function(lang, code, select){
            $('#wcml_mc_options_submit').attr('disabled', 'disabled');
            if( select ){
                var ajaxLoader = $('<span class="spinner" style="visibility: visible;float:none;position: absolute">');
                select.parent().append(ajaxLoader);
            }

            discard = true;
            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'wcml_update_default_currency',
                    lang: lang,
                    code: code,
                    wcml_nonce: $('#wcml_update_default_currency_nonce').val()
                },
                complete: function(){
                    discard = false;
                    $('#wcml_mc_options_submit').removeAttr('disabled');
                    if( select ) {
                        select.parent().find('.spinner').remove();
                    }
                }
            });
        },

        is_number: function(n){
            return !isNaN(parseFloat(n)) && isFinite(n);
        },

        setup_currencies_sorting: function(){

            $('#wcml_currencies_order').sortable({
                update: function(){
                    var currencies_order = [];
                    $('#wcml_currencies_order').find('li').each(function(){
                        currencies_order.push($(this).attr('cur'));
                    });
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'wcml_currencies_order',
                            wcml_nonce: $('#wcml_currencies_order_order_nonce').val(),
                            order: currencies_order.join(';')
                        },
                        success: function(resp){
                            if ( resp.success ) {
                                fadeInAjxResp('.wcml_currencies_order_ajx_resp', resp.data.message);
                                $('.wcml-ui-dialog').each(function(){
                                    WCML_Currency_Switcher_Settings.currency_switcher_preview( $(this) );
                                });
                            }
                        }
                    });
                }
            });

        },

        price_preview: function(){

            var parent = $(this).closest('.wcml_currency_options');

            var position = parent.find('.currency_option_position').val();
            var thousand_sep = parent.find('.currency_option_thousand_sep').val();
            var thousand_sep = parent.find('.currency_option_thousand_sep').val();
            var decimal_sep  = parent.find('.currency_option_decimal_sep').val();
            var symbol       = $(this).closest('.wcml_currency_options').find('.wcml-dialog-close-button').attr('data-symbol');
            var decimals     = '56789'.substr(0, parent.find('.currency_option_decimals').val());
            if(decimals == ''){
                decimal_sep = '';
            }

            var format   = '';

            switch(position){
                case 'left':
                    format = '{symbol}1{thousand_sep}234{decimal_sep}{decimals}';
                    break;
                case 'right':
                    format = '1{thousand_sep}234{decimal_sep}{decimals}{symbol}';
                    break;
                case 'left_space':
                    format = '{symbol}&nbsp;1{thousand_sep}234{decimal_sep}{decimals}';
                    break;
                case 'right_space':
                    format = '1{thousand_sep}234{decimal_sep}{decimals}&nbsp;{symbol}';
                    break;

            }

            var preview = format.replace(/\{symbol\}/, symbol).replace(/\{thousand_sep\}/, thousand_sep).replace(
                /\{decimal_sep\}/, decimal_sep).replace(/\{decimals\}/, decimals);

            parent.find('.wcml-co-preview-value').html( preview );

            return false;

        },

        read_form_fields_status: function(){
            this.mc_form_status = $('#wcml_mc_options').serialize();
        },

        form_fields_changed: function(){
            return this.mc_form_status != $('#wcml_mc_options').serialize();
        },

        exchange_rate_check: function( e ){

            if (typeof KeyEvent == "undefined") {
                var KeyEvent = {
                    DOM_SUBTRACT: 109,
                    DOM_DASH: 189,
                    DOM_E: 69
                };
            }

            if(
                $( this ).val() <= 0 ||
                !WCML_Multi_Currency.is_number( $( this ).val() ) ||
                e.keyCode == KeyEvent.DOM_SUBTRACT ||
                e.keyCode == KeyEvent.DOM_DASH ||
                e.keyCode == KeyEvent.DOM_E
            ){
                $('.wcml-co-set-rate .wcml-error').fadeIn();
                $('.currency_options_save').attr( 'disabled', 'disabled' );
            }else{
                $('.wcml-co-set-rate .wcml-error').fadeOut();
                $('.currency_options_save').removeAttr('disabled');
            }
        }
    }


    WCML_Multi_Currency.init();


} );