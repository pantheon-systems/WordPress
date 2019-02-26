/*jshint browser:true, devel:true */
/*global jQuery, wp */
(function($){
	"use strict";

	$(document).ready(function(){

		var section      = $('#wpml-translation-feedback-options'),
			enableToggle = section.find('#wpml-tf-enable-translation-feedback'),
			displayMode  = section.find('input[name="display_mode"]'),
			saveTriggers = section.find('.js-wpml-tf-trigger-save');

		var saveSettings = function(node) {
			var spinner  = node.closest('.js-wpml-tf-settings-block').find('.spinner'),
				message  = node.closest('.js-wpml-tf-settings-block').find('.js-wpml-tf-request-status').empty(),
				settings = getSerializedSettings();

			spinner.addClass('is-active');

			wp.ajax.send({
				data: {
					action:   section.data('action'),
					nonce:    section.data('nonce'),
					settings: settings
				},
				success:  function (data) {
					spinner.removeClass('is-active');
					message.html(data).fadeIn().delay(3000).fadeOut();
				},
				error: function (data) {
					spinner.removeClass('is-active');
					message.html(data).fadeIn().delay(3000).fadeOut();
				}
			});
		};

		var getSerializedSettings = function() {
			var form     = section.find('form'),
				disabled = form.find(':input:disabled').prop('disabled', false),
				settings = form.find('input, select, textarea').serialize();

			disabled.prop('disabled', true);

			return settings;
		};

		enableToggle.on('change', function() {
			section.find('.js-wpml-tf-full-options').slideToggle();
		});

		displayMode.on('change', function() {
			var	customDisplayMode = section.find('#wpml_tf_display_mode_custom');

			if (customDisplayMode.is(':checked')) {
				customDisplayMode.closest('li').find('select').prop('disabled', false);
			} else {
				customDisplayMode.closest('li').find('select').prop('disabled', true);
			}
		});

		saveTriggers.on('change', function() {
			saveSettings($(this));
		});

	});
})(jQuery);