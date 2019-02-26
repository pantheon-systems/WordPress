jQuery(document).ready(function ($) {
	"use strict";

	var dialog = $('.js-wpml-translation-basket-dialog');

	var openDialog = function(result) {
		/** @namespace result.call_to_action */
		/** @namespace result.ts_batch_link */

		var hasAdditionalContent = typeof result.call_to_action !== 'undefined' || typeof result.ts_batch_link !== 'undefined';

		var options = {
			dialogClass: 'wpml-dialog otgs-ui-dialog',
			width: 600,
			title: dialog.data('title'),
			modal: true,
			closeOnEscape: false,
			resizable: false,
			draggable: false,
			open: function () {
				var dialogContent = dialog.find('.js-dialog-content');

				var callToAction = dialogContent.find('.js-call-to-action');

				var batchLink       = dialogContent.find('.js-batch-link');
				var batchLinkAnchor = batchLink.find('a');

				if (callToAction && typeof result.call_to_action !== 'undefined') {
					callToAction.text(result.call_to_action);
					hasAdditionalContent = true;
				}
				if (batchLinkAnchor && typeof result.ts_batch_link !== 'undefined') {
					batchLinkAnchor.attr('href', result.ts_batch_link.href);
					batchLinkAnchor.text(result.ts_batch_link.text);
					batchLink.show();
					hasAdditionalContent = true;
				}
				dialog.show();
				repositionDialog();
			}
		};

		if (hasAdditionalContent) {
			dialog.dialog(options);
		}

	};

	var repositionDialog = function() {
		var winH = $(window).height() - 180;
		$(".otgs-ui-dialog .ui-dialog-content").css({
			"max-height": winH
		});
		$(".otgs-ui-dialog").css({
			"max-width": "95%"
		});
		dialog.dialog("option", "position", {
			my: "center",
			at: "center",
			of: window
		});
	};

	$(window).resize(repositionDialog);

	var form = $('#translation-jobs-translators-form');

	form.on('wpml-tm-basket-submitted', function(event, response) {
		if (response.result) {
			openDialog(response.result);
		}
	});
});
