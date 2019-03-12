
jQuery( function($){

    WCML_Currency_Switcher_Settings = {

        _currency_languages_saving : 0,

        init:  function(){

            $(document).ready( function(){

                $(document).on('change','#currency_switcher_style', WCML_Currency_Switcher_Settings.update_currency_switcher_style);
                $(document).on('click','.currency_switcher_save', WCML_Currency_Switcher_Settings.save_currency_switcher_settings);
                $(document).on('click','.delete_currency_switcher', WCML_Currency_Switcher_Settings.delete_currency_switcher);

                $(document).on('change','.js-wcml-cs-colorpicker-preset', WCML_Currency_Switcher_Settings.set_currency_switcher_color_pre_set );

                $(document).on('keyup','input[name="wcml_curr_template"]', WCML_Currency_Switcher_Settings.setup_currency_switcher_template_keyup);
                $(document).on('change','input[name="wcml_curr_template"]', WCML_Currency_Switcher_Settings.setup_currency_switcher_template_change);

                WCML_Currency_Switcher_Settings.open_dialog_from_hash();

            } );

        },

       initColorPicker : function() {
           $('.wcml-ui-dialog .js-wcml-cs-panel-colors').find('.js-wcml-cs-colorpicker').wpColorPicker({
                change: function(e){
                    var dialog =  $( this ).closest( '.wcml-ui-dialog' );
                    WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog );
                },
                clear: function(e){
                    var dialog =  $( this ).closest( '.wcml-ui-dialog' );
                    WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog );
                }
            });
        },

        save_currency_switcher_settings: function(){

            var dialog =  $( this ).closest( '.wcml-ui-dialog' );
            var ajaxLoader = $('<span class="spinner" style="visibility: visible;"></span>');
            var widget_name = dialog.find('#wcml-cs-widget option:selected').text();
            var switcher_id = dialog.find('#wcml_currencies_switcher_id').val();
            var widget_id = dialog.find('#wcml-cs-widget').val();
            var widget_title = dialog.find('input[name="wcml_cs_widget_title"]').val();
            var switcher_style = dialog.find('#currency_switcher_style').val();

            ajaxLoader.show();
            $(this).parent().append(ajaxLoader);
            dialog.find(':submit,:button').prop('disabled', true);

            var template = dialog.find('input[name="wcml_curr_template"]').val();
            if(!template){
                template = dialog.find('#currency_switcher_default').val();
            }

            var color_scheme = {};
            dialog.find('input.js-wcml-cs-colorpicker').each( function(){
                color_scheme[ $(this).attr('name') ] = $(this).val();
            });

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    action: 'wcml_currencies_switcher_save_settings',
                    wcml_nonce: dialog.find('#wcml_currencies_switcher_save_settings_nonce').val(),
                    switcher_id: switcher_id,
                    widget_id: widget_id,
                    widget_title: widget_title,
                    switcher_style: switcher_style,
                    template: template,
                    color_scheme: color_scheme
                },
                success: function(e) {
                    dialog.find('.ui-dialog-titlebar-close').trigger('click');

                    if( typeof widget_id == 'undefined' ){
                        widget_id = switcher_id;
                    }

                    $('#wcml_currency_switcher_options_form_new_widget #wcml-cs-widget option').each( function(){
                        if( $(this).val() == widget_id ){
                            $(this).remove();
                        }
                    });

                    if( $('#wcml_currency_switcher_options_form_new_widget #wcml-cs-widget option').length == 0 ){
                        $('.wcml_add_cs_sidebar').fadeOut();
                    }

                    if( $('#currency-switcher-widget .wcml-cs-list').find('thead tr').is(':hidden') ){
                        $('#currency-switcher-widget .wcml-cs-list').find('thead tr').fadeIn();
                    }

                    if( $('.wcml-currency-preview.' + widget_id ).length == 0 ){

                        var widget_row = $('.wcml-cs-empty-row').clone();
                        widget_row.removeClass('wcml-cs-empty-row');
                        widget_row.find('.wcml-currency-preview').addClass(widget_id);
                        widget_row.find('.wcml-cs-widget-name').html( widget_name );
                        widget_row.find('.edit_currency_switcher').attr('data-switcher', widget_id );
                        widget_row.find('.edit_currency_switcher').attr('data-dialog', 'wcml_currency_switcher_options_' + widget_id );
                        widget_row.find('.edit_currency_switcher').attr('data-content', 'wcml_currency_switcher_options_' + widget_id );
                        widget_row.find('.delete_currency_switcher').attr('data-switcher', widget_id );
                        widget_row.show();

                        $('.wcml-cs-list').find('tr.wcml-cs-empty-row').before( widget_row );
                        if( $('.wcml-cs-list').is(':hidden') ){
                            $('.wcml-cs-list').fadeIn();
                        }
                    }
                    $('#wcml_currency_switcher_options_' + widget_id).remove();
                    dialog.find('.wcml-dialog-container').attr('id','wcml-dialog-wcml_currency_switcher_options_'+ widget_id );
                    dialog.find(':submit,:button').prop('disabled', false);
                    dialog.find('#wcml_currencies_switcher_id').val( widget_id );
                    ajaxLoader.remove();

                    WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog, true );
                }
            });

            return false;
        },

        delete_currency_switcher: function(e){

            e.preventDefault();

            var switcher_id = $(this).data( 'switcher' );
            var switcher_row = $(this).closest('tr');
            var ajaxLoader = $('<span class="spinner" style="visibility: visible;">');
            $(this).parent().html( ajaxLoader );

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                    data: {
                    action: 'wcml_delete_currency_switcher',
                    wcml_nonce: $('#wcml_delete_currency_switcher_nonce').val(),
                    switcher_id: switcher_id
                },
                success: function(e){
                    var sidebar_name = switcher_row.find('.wcml-cs-widget-name').html();
                    $('#wcml_currency_switcher_options_form_new_widget #wcml-cs-widget').append( '<option value="'+switcher_id+'">'+sidebar_name+'</option>');

                    switcher_row.remove();

                    if( $('#currency-switcher-widget .wcml-cs-list').find('tbody tr').length == 1 ){
                        $('#currency-switcher-widget .wcml-cs-list').find('thead tr').fadeOut();
                    }
                    if( $('.wcml_add_cs_sidebar').is(':hidden') ){
                        $('.wcml_add_cs_sidebar').fadeIn();
                    }
                }
            });
        },

        currency_switcher_preview: _.debounce( function ( dialog, update_settings ){

            var template = dialog.find('input[name="wcml_curr_template"]').val();
            if(!template){
                template = dialog.find('#currency_switcher_default').val();
            }

            var ajaxLoader = $('<span class="spinner" style="visibility: visible;">');
            dialog.find('#wcml_curr_sel_preview_wrap').append(ajaxLoader);

            var color_scheme = {};
            dialog.find('input.js-wcml-cs-colorpicker').each( function(){
                color_scheme[ $(this).attr('name') ] = $(this).val();
            });

            var switcher_id = dialog.find('#wcml_currencies_switcher_id').val();
            var switcher_style = dialog.find('#currency_switcher_style').val();

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'wcml_currencies_switcher_preview',
                    wcml_nonce: dialog.find('#wcml_currencies_switcher_preview_nonce').val(),
                    switcher_id: switcher_id,
                    switcher_style: switcher_style,
                    template: template,
                    color_scheme: color_scheme
                },
                success: function(resp){
                    if( resp.success ) {
                        resp = resp.data;
                        if( $( '#'+resp.inline_styles_id).length == 0 ){
                            $('head').append( '<style type="text/css" id="'+resp.inline_styles_id+'">'+ resp.inline_css+'</style>' );
                        }else{
                            $( '#'+resp.inline_styles_id).html( resp.inline_css );
                        }
                        ajaxLoader.remove();
                        if( update_settings ){
                            if( switcher_id == 'new_widget'){
                                switcher_id = dialog.find('#wcml-cs-widget').val();
                            }
                            $('.wcml-currency-preview.'+switcher_id).html(resp.preview);
                        }else{
                            dialog.find('.wcml-currency-preview').html(resp.preview);
                        }

                        if( switcher_style == 'wcml-dropdown-click' ){
                            WCMLCurrecnySwitcherDropdownClick.init();
                        }
                    }

                }
            });
        }, 500),

        set_currency_switcher_color_pre_set: function (){

            var color_sheme = $(this).val();
            var dialog =  $( this ).closest( '.wcml-ui-dialog' );

            if( settings.pre_selected_colors[color_sheme] != 'undefined' ){
                var selected_scheme = settings.pre_selected_colors[color_sheme];
                var color;
                for ( color in selected_scheme ) {
                    $('.wcml-ui-dialog input[name="'+color+'"]').val( selected_scheme[ color ] );
                    $('.wcml-ui-dialog input[name="'+color+'"]').closest('.wp-picker-container').find('.wp-color-result').css( 'background-color', selected_scheme[color] );
                }
            }

            WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog );
        },

        update_currency_switcher_style: function(e){
            var dialog =  $( this ).closest( '.wcml-ui-dialog' );
            WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog );
        },

        setup_currency_switcher_template_keyup: function(e){
            var dialog =  $( this ).closest( '.wcml-ui-dialog' );
            discard = true;
            $(this).closest('.wcml-section').find('.button-wrap input').css("border-color","#1e8cbe");
            WCML_Currency_Switcher_Settings.currency_switcher_preview( dialog );
        },

        setup_currency_switcher_template_change: function(e){
            if(!$(this).val()){
                $(this).val($('#currency_switcher_default').val())
            }
        },

        open_dialog_from_hash: function(){
            var hashParts = window.location.hash.substring(1).split('/'),
                type = hashParts[0] || '',
                slug = hashParts[1] || '';

            if ( type == 'currency-switcher' ) {
                $('.edit_currency_switcher[data-switcher="'+slug+'"]').trigger('click');
                parent.location.hash = '';
            }
        }

    }

    WCML_Currency_Switcher_Settings.init();

} );