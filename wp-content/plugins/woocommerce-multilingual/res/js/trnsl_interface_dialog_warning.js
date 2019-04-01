jQuery(document).ready(function ($) {

    $(document).on('click', 'input[name="trnsl_interface"]', function () {

        if ( $(this).val() == 0 ) {
            jQuery(document).ready(function () {
                var dialogBox = jQuery("#wcml-translation-interface-dialog-confirm");
                var buttonsOpts = {};
                buttonsOpts[ dialogBox.find('.cancel-button').val() ] = function () {
                    jQuery(this).dialog("close");
                    jQuery('input[name="trnsl_interface"][value="1"]').attr('checked', 'checked');
                };
                buttonsOpts[ dialogBox.find('.ok-button').val() ] = function () {
                    jQuery(this).dialog("close");
                };

                dialogBox.dialog({
                    resizable: false,
                    draggable: false,
                    height: "auto",
                    width: 600,
                    modal: true,
                    closeOnEscape: false,
                    dialogClass: "otgs-ui-dialog",
                    create: function () {

                    },
                    open: function (event, ui) {
                        jQuery(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
						jQuery('#jquery-ui-style-css').attr('disabled', 'disabled');
                    },
                    close: function (event, ui) {
                        jQuery('#jquery-ui-style-css').removeAttr('disabled');
                    },
                    buttons : buttonsOpts
                });
            });
        }
    });

});

