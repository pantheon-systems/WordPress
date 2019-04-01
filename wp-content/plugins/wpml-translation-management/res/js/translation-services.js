/*jshint browser:true, devel:true */
/*globals jQuery, ajaxurl*/
var WPMLTranslationServicesDialog = function () {
	"use strict";

	var self = this;

	self.preventEventDefault = function (event) {
		if ('undefined' !== event && 'undefined' !== typeof(event.preventDefault)) {
			event.preventDefault();
		} else {
			event.returnValue = false;
		}
	};

	self.enterKey = 13;
	self.ajaxSpinner = jQuery('<span class="spinner"></span>');
	self.activeServiceWrapper = jQuery( '.js-wpml-active-service-wrapper' );

	self.init = function () {
		var flushWebsiteDetailsCacheLink;
		var header;
		var tip;

		header = self.activeServiceWrapper.find( '.active-service-header' ).val();
		tip = self.activeServiceWrapper.find( '.active-service-tip' ).val();

		self.serviceDialog = jQuery('<div id="service_dialog"><h4>' + header + '</h4><div class="custom_fields_wrapper"></div><i>' + tip + '</i><br /><br /><div class="tp_response_message icl_ajx_response"></div>');
		self.ajaxSpinner.addClass('is-active');

		flushWebsiteDetailsCacheLink = jQuery('.js-flush-website-details-cache');


        jQuery('#wpml-tp-services').delegate('.js-activate-service-id', 'click', function (event) {
			self.preventEventDefault(event);

            var button = jQuery(this);
            var serviceId = jQuery(this).data('id');
			self.toggleService(serviceId, button, 1);

			return false;
		});

        jQuery('body').delegate('.js-deactivate-service', 'click', function (event) {
			var serviceId;
			var button;
			self.preventEventDefault(event);

			button = jQuery(this);
			serviceId = jQuery(this).data('id');
			self.toggleService(serviceId, button, 0);

			return false;
		});

		self.activeServiceWrapper.on('click', '.js-invalidate-service', function (event) {
			var serviceId;
			var button;
			self.preventEventDefault(event);

			button = jQuery(this);
			serviceId = jQuery(this).data('id');
			self.translationServiceAuthentication(serviceId, button, 1);

			return false;
		});

		flushWebsiteDetailsCacheLink.on('click', function (event) {
			var anchor = jQuery(this);
			self.preventEventDefault(event);

			self.flushWebsiteDetailsCache(anchor);

			return false;
		});

		self.activeServiceWrapper.on('click', '.js-authenticate-service', function (event) {
			var customFields;
			var serviceId;
			self.preventEventDefault(event);

			serviceId = jQuery(this).data('id');
			customFields = jQuery(this).data('custom-fields');

			self.serviceAuthenticationDialog(customFields, serviceId);

			return false;
		});

		self.refreshTSInfo();
	};

	self.refreshTSInfo = function() {
		var activeServiceBlock = jQuery('.js-wpml-active-service-wrapper');

		if ( ! activeServiceBlock.length || ! activeServiceBlock.find('.js-needs-info-refresh').val() ) {
			return;
		}

		var activeTsButtons = activeServiceBlock.find('input, button').prop('disabled', true);
		var nonce = activeServiceBlock.find('.js-ts-refresh-nonce').val();
		var refreshMsg = activeServiceBlock.find('.js-ts-refreshing-message').fadeIn();

		jQuery.ajax({
			type:     'POST',
			url:      ajaxurl,
			data: {
				'action': 'refresh_ts_info',
				'nonce':  nonce
			},
			dataType: 'json',
			success: function(response) {
				if (response.success && response.data) {
					var content = jQuery.parseHTML(response.data.active_service_block);
					activeServiceBlock.fadeOut(400, function() {
						activeServiceBlock.html(content).fadeIn(content);
					});
				} else {
					refreshMsg.fadeOut(400, function() {
						refreshMsg.html("<p>" + response.data.message + "</p>").addClass('notice notice-error inline').fadeIn(content);
						activeTsButtons.prop('disabled', false);
					});
				}
			}
		});
	};

	self.toggleService = function (serviceId, button, enableService, successCallback) {
		var ajaxData;
		var enable = enableService;
		var nonce = jQuery( '.translation_service_toggle' ).val();
		if ('undefined' === typeof enableService) {
			enable = 0;
		}

		self.disableButton(button);

		ajaxData = {
			'action':     'translation_service_toggle',
			'nonce':      nonce,
			'service_id': serviceId,
			'enable':     enable ? 1 : 0
		};

		jQuery.ajax({
			type:     "POST",
			url:      ajaxurl,
			data:     ajaxData,
			dataType: 'json',
			success:  function (response) {
				self.enableButton(button)
				if ( typeof successCallback === 'function' ) {
					successCallback( response.data );
				} else {
					var data = response.data;

					if ( data.reload ) {
						location.reload( true );
					}
				}
			},
			error:    function (jqXHR, status, error) {
				var parsedResponse = jqXHR.statusText || status || error;
				alert(parsedResponse);
			}
		});
	};

	self.disableButton = function (button) {
		if (button) {
			button.attr( 'disabled', 'disabled' );
			button.after( self.ajaxSpinner );
		}
	}

	self.enableButton = function (button) {
		if (button) {
			button.removeAttr( 'disabled' );
			button.next().fadeOut();
		}
	}

	self.serviceAuthenticationDialog = function (customFields, serviceId) {
		self.serviceDialog.dialog({
			dialogClass: 'wpml-dialog otgs-ui-dialog',
			width:       'auto',
			title:       self.activeServiceWrapper.find( '.active-service-title' ).val(),
			modal:       true,
			open:        function () {

				var customFieldsWrapper = self.serviceDialog.find('.custom_fields_wrapper');
				self.buildCustomFieldsUI( customFields, customFieldsWrapper );

				jQuery(':input', this).keyup(function (event) {
					if (self.enterKey === event.keyCode) {
						jQuery(this).closest('.ui-dialog').find('.ui-dialog-buttonpane').find('button.js-submit:first').click();
					}
				});

			},
			buttons:     [
				{
					text:    "Cancel",
					click:   function () {
						jQuery(this).dialog("close");
					},
					'class': 'button-secondary alignleft'
				}, {
					text:    "Submit",
					click:   function () {
						self.hideButtons();
						self.translationServiceAuthentication(serviceId, false, 0);
					},
					'class': 'button-primary js-submit'
				}
			]
		});
	};

	self.buildCustomFieldsUI = function (customFields, customFieldsWrapper) {
		var firstInput = false;

		customFieldsWrapper.empty();

		jQuery.each(customFields, function (i, item) {
			var itemLabel, itemInput;
			var itemId;
			var customFieldsListItem = jQuery('<div class="wpml-form-row"></div>');
			customFieldsListItem.appendTo(customFieldsWrapper);

			itemId = 'custom_field_' + item.name;

			if (item.type.trim().toLowerCase() !== 'hidden') {
				itemLabel = jQuery('<label for="' + itemId + '">' + item.label + ':</label>');
				itemLabel.appendTo(customFieldsListItem);
			}
			itemInput = jQuery('<input type="' + item.type + '" id="' + itemId + '" class="custom_fields" name="' + item.name + '" />');

			itemInput.appendTo(customFieldsListItem);
			if (!firstInput) {
				itemInput.focus();
			}
		});

	};

	self.getSerializedCustomFields = function() {
		var customFieldsDataStringify;
		var customFieldsData;
		var customFieldsInput;

		customFieldsInput = jQuery('.custom_fields');
		customFieldsData = {};
		jQuery.each(customFieldsInput, function (i, item) {
			customFieldsData[jQuery(item).attr('name')] = jQuery(item).val();
		});
		return JSON.stringify(customFieldsData, null, ' ');
	};

	self.hideButtons = function () {
		self.ajaxSpinner.appendTo(self.serviceDialog);
		self.serviceDialog.parent().find('.ui-dialog-buttonpane').fadeOut();
	};

	self.showButtons = function () {
		self.serviceDialog.find(self.ajaxSpinner).remove();
		self.serviceDialog.parent().find('.ui-dialog-buttonpane').fadeIn();
	};

	self.translationServiceAuthentication = function (serviceId, button, invalidateService, successCallback) {
		var invalidate;
		var nonce = jQuery( '.translation_service_authentication' ).val();

		invalidate = invalidateService;
		if ('undefined' === typeof invalidateService) {
			invalidate = 0;
		}

		if (isNaN(serviceId)) {
			alert('service_id isNAN');
		} else if (isNaN(invalidate)) {
			alert('invalidate isNAN');
		}

		self.disableButton(button);

		jQuery.ajax({
			type:     "POST",
			url:      ajaxurl,
			data:     {
				'action':        invalidate ? 'translation_service_invalidation' : 'translation_service_authentication',
				'nonce':         nonce,
				'service_id':    serviceId,
				'invalidate':    invalidate,
				'custom_fields': self.getSerializedCustomFields()
			},
			dataType: 'json',
			success: function (response) {
				self.enableButton(button);
				if ( typeof successCallback === 'function' ) {
					successCallback( response.data );
				} else {
					var response_message = jQuery( '.tp_response_message' );
					response = response.data;
					if ( 0 === response.errors ) {
						if ( response.reload ) {
							location.reload( true );
						}
					}

					response_message.html( response.message );
					response_message.show();

					setInterval( function () {
						response_message.fadeOut();
					}, 5000 );
				}
			},
			error: function (jqXHR, status, error) {
				var parsedResponse = jqXHR.statusText || status || error;
				alert(parsedResponse);
			},
			complete: function() {
				self.showButtons();
			}
		});
	};

	self.flushWebsiteDetailsCache = function (anchor) {
		var nonce = anchor.data('nonce');

		self.ajaxSpinner.appendTo(anchor);
		self.ajaxSpinner.addClass('is-active');

		if (nonce) {
			jQuery.ajax({
										type:     "POST",
										url:      ajaxurl,
										data:     {
											'action': 'wpml-flush-website-details-cache',
											'nonce':  nonce
										},
										dataType: 'json',
										success:  function (response) {
											self.ajaxSpinner.removeClass('is-active');
											if (response.success) {
												/** @namespace response.redirectTo */
												location.reload(response.data.redirectTo);
											}
										}
									});
		}
	};
};

jQuery(document).ready(function () {
	"use strict";

	var wpmlTranslationServicesDialog = new WPMLTranslationServicesDialog();
	var current_url = location.href;
	var search_section = jQuery( '.ts-admin-section-search' );

	wpmlTranslationServicesDialog.init();

	search_section.find('.search' ).click(function(){
		var param = {
			s: search_section.find('.search-string' ).val()
		};

		window.location.href = current_url + '&' + jQuery.param( param );
	});

	search_section.find( '.search-string' ).keypress(function (e) {
		if ( e.which === 13 ) {
			search_section.find( '.search' ).click();
			return false;
		}
	});

	jQuery( '.ts-admin-section-inactive-services #current-page-selector-top' ).keypress(function (e) {
		if ( e.which === 13 ) {
			var param = {
				paged: jQuery( this ).val()
			};

			window.location.href = current_url + '&' + jQuery.param( param );
		}
	});
});

