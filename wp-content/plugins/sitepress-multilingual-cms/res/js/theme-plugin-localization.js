/* globals icl_ajx_saved, icl_ajx_error, ajaxurl */

jQuery(function($){

	'use strict';

	var auto_load_theme_mo_file = $( '.automatically_load_mo_file' ),
		do_not_use_st = "2",
		theme_localization_type = $( 'input[name*="theme_localization_type"]:checked' ).val();

	auto_load_theme_mo_file.hide();

	$(document).ready(function () {
		if ( theme_localization_type === do_not_use_st ) {
			hide_theme_plugin_sections();
		}

		var ajax_success_action = function( response, response_text ) {

			if( response.success ) {
				response_text.text( icl_ajx_saved );
			} else {
				response_text.text( icl_ajx_error );
			}

			response_text.show();

			setTimeout(function () {
				response_text.fadeOut('slow');
			}, 2500);
		};

		$( '#wpml-js-theme-plugin-save-option' ).click(function(){

			var alert_scan_new_strings = $( 'input[name*="wpml_st_display_strings_scan_notices"]' ),
				use_theme_plugin_domain = $( 'input[name*="use_theme_plugin_domain"]' ),
				theme_localization_load_textdomain = $( 'input[name*="theme_localization_load_textdomain"]' ),
				all_strings_are_in_english = $( 'input[name*="wpml-st-all-strings-are-in-english"]' ),
				gettext_theme_domain_name = $( 'input[name*="gettext_theme_domain_name"]' ),
				response_text = $( '#wpml-js-theme-plugin-options-response' ),
				spinner = $( '#wpml-js-theme-plugin-options-spinner' );

			theme_localization_type = $( 'input[name*="theme_localization_type"]:checked' ).val();
			spinner.addClass( 'is-active' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wpml_update_localization_options',
					nonce: $( '#wpml-localization-options-nonce' ).val(),
					theme_localization_type: theme_localization_type,
					wpml_st_display_strings_scan_notices: 'checked' === alert_scan_new_strings.attr( 'checked' ) ? alert_scan_new_strings.val() : 0,
					use_theme_plugin_domain: 'checked' === use_theme_plugin_domain.attr( 'checked' ) ? use_theme_plugin_domain.val() : 0,
					theme_localization_load_textdomain: 'checked' === theme_localization_load_textdomain.attr( 'checked' ) ? theme_localization_load_textdomain.val() : 0,
					gettext_theme_domain_name: gettext_theme_domain_name.val(),
                    all_strings_are_english: all_strings_are_in_english.is(":checked") ? 1 : 0

				},
				success: function ( response ) {
					spinner.removeClass( 'is-active' );
					ajax_success_action( response, response_text );

					if (do_not_use_st === theme_localization_type) {
						hide_theme_plugin_sections();
					} else {
						$('.wpml_theme_localization').show();
						$('.wpml_plugin_localization').show();
					}
				}
			});
		});

		function hide_theme_plugin_sections() {
			$('.wpml_theme_localization').hide();
			$('.wpml_plugin_localization').hide();
		}
		
		$( 'input[name*="theme_localization_type"]' ).click(function(){
			auto_load_theme_mo_file.hide();
		});

		$( 'input[name*="theme_localization_type"][value="2"]' ).click(function(){
			auto_load_theme_mo_file.toggle();
		});

		if ( '2' === $( 'input[name*="theme_localization_type"]:checked' ).val() ) {
			auto_load_theme_mo_file.show();
		}
	});
});