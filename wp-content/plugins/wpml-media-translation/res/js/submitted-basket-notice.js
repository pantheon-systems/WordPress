/* globals wpml_media_basket_notice_data */
var WPML_Media_Submitted_Basket_Notice = WPML_Media_Submitted_Basket_Notice || {};

jQuery(function ($) {
	"use strict";

	var form = jQuery('#translation-jobs-translators-form');
	form.on('wpml-tm-basket-submitted', function(){

		var dialogBox = $('#submitted-basket-notice-dialog');
		dialogBox.dialog({
			modal:true,
			closeOnEscape: false,
			dialogClass: "no-close otgs-ui-dialog",
			resizable: false,
			draggable: false,
			width: 600,
			open: function() {
				repositionDialog();
				wpmlTMBasket.dialogs.push( 'media' );
				wpmlTMBasket.redirect = false;
			},
			buttons: [
				{
					text: wpml_media_basket_notice_data.button_label,
					class: 'button-primary',
					click: function() {
						dialogBox.dialog('close');
					}
				}
			],
			close: function() {
				wpmlTMBasket.dialogs.splice( wpmlTMBasket.dialogs.indexOf( 'media' ), 1 );

				if(0 === wpmlTMBasket.dialogs.length) {
					location.href = dialogBox.data('redirect-url');
				}
			}
		});

		$(window).resize(repositionDialog);

		function repositionDialog() {
			var winH = $(window).height() - 180;
			$(".otgs-ui-dialog .ui-dialog-content").css({
				"max-height": winH
			});
			$(".otgs-ui-dialog").css({
				"max-width": "95%"
			});
			dialogBox.dialog("option", "position", {
				my: "center",
				at: "center",
				of: window
			});
		}
	});
});
