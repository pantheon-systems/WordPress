/*global jQuery, ajaxurl, wp, icl_ajx_response */
var WPML_TP_API = WPML_TP_API || {};

(function () {
	'use strict';

	WPML_TP_API.refreshLanguagePairs = function () {

		var refreshLanguagePairs = function (event) {
			event.preventDefault();

			var self = this;
			var spinner = jQuery( '.refresh-language-pairs-section .spinner' );
			
			spinner.addClass( 'is-active' );
			
			wp.ajax.send({
				data:     {
					action: 'wpml-tp-refresh-language-pairs',
					nonce:  jQuery(self).data('nonce')
				},
				success:  function (response) {
					var response_text = jQuery( '.refresh-language-pairs-section .wpml_ajax_response' );

					response_text.html( response.msg );
					response_text.css( 'display', 'inline-block' );
					spinner.removeClass( 'is-active' );

					setTimeout(function() {
						response_text.fadeOut( 'slow' );
					}, 3000);
				},
				complete: function () {
					spinner.remove();
				}
			});
		};

		jQuery('.js-refresh-language-pairs').on('click', refreshLanguagePairs);

	};

	jQuery(document).ready(function () {
		WPML_TP_API.refreshLanguagePairs();
	});
}());