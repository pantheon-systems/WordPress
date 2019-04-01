
var WCML_Dialog = WCML_Dialog || {};

jQuery( function($){

    var dialog_div;

    WCML_Dialog.dialog = function(dialog_id, data){
        var self = this;

        if(typeof dialog_id == 'undefined'){
            dialog_id = 'generic';
        }

        self.overflow_y = $('body').css('overflow-y');
        $('body').css('overflow-y','hidden');

        dialog_div = $('#wcml-dialog-' + dialog_id);

        var title = $('#' + dialog_id).attr('title');
        if( typeof title == 'undefined'){
            if( data.title != 'undefined'){
                title = data.title;
            }else{
                title = '';
            }
        }

        if( typeof data.class === 'undefined'){
            data.class = '';
        }

        if( typeof data.draggable === 'undefined'){
            data.draggable = false;
        }

        if(!dialog_div.length){
            $(document.body).append($('<div class="wcml-dialog-container" title="' + title + '" id="wcml-dialog-' + dialog_id + '" />'));

            dialog_div = $('#wcml-dialog-' + dialog_id);
            var window_h   = $( window ).height();

            var dialog_parameters = {
                title: '',
                autoOpen:false,
                show:true,
                dialogClass:'wcml-ui-dialog otgs-ui-dialog ' + data.class,
                position: { my: 'center', at: 'center', of: window },
                modal:true,
                width: "90%",
                height: "auto",
                resizable:false,
                draggable: data.draggable,
                beforeOpen: function (event) {
                },
                beforeClose: function (event) {
                },
                close: function (event) {
                    $('#jquery-ui-style-css').removeAttr('disabled');
                    $('body').css('overflow-y', self.overflow_y);
                },
                create: function (event) {

                },
                focus: function (event) {
                },
                open: function (event) {
                    $('body').css('overflow', 'hidden');
					$('#jquery-ui-style-css').attr('disabled', 'disabled');

                    if( data.class === 'wcml-cs-dialog' ){
                        WCML_Dialog._attachDialogScrollEvent();
                    }
                },
                refresh:function(event){
                }
            };

            if(typeof data.height != 'undefined' && data.class != 'wcml-cs-dialog'){
                dialog_parameters.height = Math.min(window_h * 0.7, data.height);
            }

            if(typeof data.width != 'undefined' && data.class != 'wcml-cs-dialog'){
                dialog_parameters.width = data.width;
            }

            if(WCML_Dialog.using_wpdialog){ // pre WP 3.5
                dialog_div.wpdialog(dialog_parameters);
            }else{
                dialog_div.dialog(
                    dialog_parameters
                ).on('dialogopen', function () {
                    WCML_Dialog._repositionDialog();
                });
            }
        }

		var resizeWindowEvent = _.debounce(function() {
			WCML_Dialog._repositionDialog();
			if( data.class === 'wcml-cs-dialog' ) {
				WCML_Dialog._attachDialogScrollEvent();
			}
		}, 200);

		if(WCML_Dialog.using_wpdialog) { // pre WP 3.5
            dialog_div.wpdialog('open');
        }else{
            dialog_div.dialog('open');
        }

        //load data
        if(data.action) {

            var spinner = $('<div class="spinner"></div>');
            spinner.css({display: 'inline-block', visibility: 'visible', float: 'none'});
            dialog_div.html(spinner);

            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (response) {
                    dialog_div.html(response.html);
                }
            });
        }

        // load static html
        if( data.content && $('#' + data.content).length ) {
            dialog_div.html($('#' + data.content).html());
        }

        if( typeof WCML_Tooltip != 'undefined' ){
            WCML_Tooltip.init();
        }

        $(window).resize(resizeWindowEvent);

        return false;
    }

    WCML_Dialog._repositionDialog = function () {
        var winH = $(window).height() - 180;
        dialog_div.css("max-height", winH);

        setTimeout(function() {
                dialog_div.dialog("option", "position", {
                my: "center",
                at: "center",
                of: window
            });
        }, 50);

    }

    WCML_Dialog._attachDialogScrollEvent = function() {
        var preview = dialog_div.find('.wcml-currency-preview-wrapper'),
            has_two_columns = dialog_div.width() > 900,
            has_minimal_height = (preview.height() + 200) < dialog_div.height();

        has_minimal_height = has_minimal_height || (has_two_columns && preview.height() < dialog_div.height());

        if (has_minimal_height) {
            dialog_div.on('scroll.preview', function(){
                dialog_div.find('.wcml-currency-preview-wrapper').css({
                    position: 'relative',
                    top: dialog_div.scrollTop()
                });
            });
        } else {
            dialog_div
                .off('scroll.preview')
                .find('.wcml-currency-preview-wrapper').css({
                    position: 'relative',
                    top: 0
                });
        }
    }

    WCML_Dialog._register_open_handler = function(){

        // dialog open handler
        $(document).on( 'click','.js-wcml-dialog-trigger', function(e){

            e.preventDefault();
            var dialog_id = false;

            if($(this).data('dialog')){
                dialog_id = $(this).data('dialog');
            }else if($(this).data('action')) {
                dialog_id = $(this).data('action');
            }

            if(dialog_id){
                if($(this).data('action')){
                    $(this).data('action', $(this).data('action').replace(/-/g, '_'));
                }
                WCML_Dialog.dialog(dialog_id, $(this).data());
            }
        });

        // dialog open handler for currency switcher
        $(document).on( 'click','.js-wcml-cs-dialog-trigger', function(e){

            e.preventDefault();
            var dialog_id = false;

            if( $(this).data('dialog') ){
                dialog_id = $(this).data('dialog');
            }

            var data = $(this).data();
            data.class = 'wcml-cs-dialog';
            data.draggable = true;

            if( dialog_id ){
                 WCML_Dialog.dialog( dialog_id, data );

                WCML_Currency_Switcher_Settings.initColorPicker();
                WCML_Currency_Switcher_Settings.currency_switcher_preview( $('#wcml-dialog-'+dialog_id) );
            }
        });
    }

    WCML_Dialog._register_close_handler = function(){

        // dialog close handler
        $(document).on( 'click', '.wcml-dialog-close-button', function(e){
            e.preventDefault();

            if( typeof tinyMCE != 'undefined' ){
                tinyMCE.triggerSave();
            }

            $('.wcml-dialog').find('.mce_editor textarea').each(function(){
                var editor_id = $(this).attr('id');
                var editor_area = $(this);
                if (editor_id in tinyMCE.editors) {
                    var tinymce_editor = tinyMCE.get(editor_id);
                    if (!tinymce_editor.isHidden()) {
                        editor_area.val(tinymce_editor.getContent());
                    }
                }
            });

            var dialog_div = $(this).closest('.wcml-dialog-container');

            var elem = $(this);
            elem.attr('disabled', 'disabled');

            var data = $(this).data();
            if(data.action){ // sync ajax

                var spinner = $('<div class="spinner"></div>');
                spinner.css({visibility: 'visible', float: 'right'}).prependTo( $('.wcml-dialog-footer .alignright') );

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action : data.action,
                        fields : elem.closest('.wcml-dialog-container').find('form').serialize(),
                        icl_nonce : data.nonce
                    },
                    async: false,
                    success: function (response) {
                        if( data.stay ){
                            spinner.remove();
                            elem.removeAttr('disabled');
                        }
                    }
                });
            }

            if(!data.stay){
                elem.trigger('before_close_dialog');
                if(WCML_Dialog.using_wpdialog){ // pre WP 3.5
                    dialog_div.wpdialog('close');
                }else{
                    dialog_div.dialog('close');
                }
            }
        });
    }

    WCML_Dialog.init = function(){

        $(document).ready(function() {
            if (typeof $.wp != 'undefined') {
                WCML_Dialog.using_wpdialog = typeof $.wp.wpdialog != 'undefined';
            } else {
                WCML_Dialog.using_wpdialog = false;
            }

            WCML_Dialog._register_open_handler();
            WCML_Dialog._register_close_handler();
        });
    }

    WCML_Dialog.init();
});






