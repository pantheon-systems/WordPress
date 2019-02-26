/*jshint devel:true */
/*global jQuery, ajaxurl, get_checked_cbs */
var WPML_String_Translation = WPML_String_Translation || {};

WPML_String_Translation.ChangeLanguage = function () {
	"use strict";
	var privateData = {};

    var init = function () {
        jQuery(document).ready(function () {

            privateData.language_select = jQuery('#icl_st_change_lang_selected');
            privateData.language_select.on('change', applyChanges);

            privateData.spinner = jQuery('.icl-st-change-spinner');
            privateData.spinner.detach().insertAfter(privateData.language_select);
        });
    };

	var applyChanges = function () {
		var checkBoxValue;
		var data;
		var i;
		var checkboxes;
		var strings;

        privateData.spinner.addClass('is-active');

		strings = [];
		checkboxes = get_checked_cbs();
		for (i = 0; i < checkboxes.length; i++) {
			checkBoxValue = jQuery(checkboxes[i]).val();
			strings.push(checkBoxValue);
		}

		data = {
			action:   'wpml_change_string_lang',
			wpnonce:  jQuery('#wpml_change_string_language_nonce').val(),
			strings:  strings,
			language: privateData.language_select.val()
		};

		jQuery.ajax({
			url:      ajaxurl,
			type:     'post',
			data:     data,
			dataType: 'json',
			success:  function (response) {
				if (response.success) {
					window.location.reload(true);
				}
				if (response.error) {
					privateData.spinner.removeClass( 'is-active' );
					alert(response.error);
					privateData.apply_button.prop('disabled', false);
				}
			}
		});
	};

	init();
};

WPML_String_Translation.change_language = new WPML_String_Translation.ChangeLanguage();