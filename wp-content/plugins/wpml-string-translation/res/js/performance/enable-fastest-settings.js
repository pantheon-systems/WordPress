/*globals jQuery, ajaxurl */
jQuery(function($) {
	"use strict";

	function bindEvents() {
		$('[data-id="wpml_st_faster_settings"] .button-primary').click(function (event) {

			var enable_button = $(this);
			var notice = enable_button.closest('.otgs-notice');
			var success = notice.find('.js-done');
			var error = notice.find('.js-error');

			event.preventDefault();
			enable_button.attr( 'disabled', true );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wpml_update_localization_options',
					theme_localization_type: 3,
					all_strings_are_english: 1,
					nonce: $( '#wpml-localization-options-nonce' ).val()
				},
				success: function( response ) {
					error.hide();
					success.hide();
					notice.removeClass('notice-error error notice-info info');
					enable_button.attr( 'disabled', false );

					var has_block_editor = wpml_get_block_editor();
					var components_notice = notice.closest( '.components-notice' );
					if (response.success) {
						if ( has_block_editor ) {
							components_notice.removeClass('is-info is-error is-success');
							components_notice.addClass('is-success');
						} else {
							notice.addClass('notice-success success');
						}

						enable_button.hide();
						success.show();

						setTimeout(function () {
							if (has_block_editor) {
								components_notice.fadeOut('slow');
							} else {
								notice.fadeOut('slow');
							}
						}, 2500);
					} else {
						if ( has_block_editor ) {
							components_notice.removeClass('is-info is-error is-success');
							components_notice.addClass('is-error');
						} else {
							notice.addClass( 'notice-error' );
							notice.addClass( 'error' );
						}
						error.show();

						if (null !== response.data) {
							error.find('strong').text(response.data);
						}
					}
				}
			});
		});
	}

	$('document').ready(function () {
		bindEvents();
	});

	$(document).on('otgs-notices-added', function (event) {
		bindEvents();
	});
});