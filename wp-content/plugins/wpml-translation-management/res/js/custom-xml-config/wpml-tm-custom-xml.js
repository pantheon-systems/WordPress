/*globals jQuery, ajaxurl */

var WPML_TM = WPML_TM || {};

(function () {
	'use strict';

	WPML_TM.Custom_XML = function (element, codeMirror) {
		this.container = element;
		this.spinner = this.container.find('.spinner');
		this.content = this.container.find('.wpml-tm-custom-xml-content');
		this.messages = this.container.find('.js-wpml-tm-messages');
		this.action = this.content.data('action');
		this.nonceValidate = this.content.data('nonce-validate');
		this.nonceSave = this.content.data('nonce-save');
		this.saveButton = this.container.find('.button-primary');
		this.editor = codeMirror;
	};

	WPML_TM.Custom_XML.prototype = {
		init:             function () {
			this.saveButton.on('click', jQuery.proxy(this.onSave, this));
			if (this.editor) {
				this.editor.init();
				this.editor.onSave = jQuery.proxy(function () {this.saveButton.trigger('click');}, this);
			}
		},
		onSave:           function (event) {
			event.preventDefault();

			if (this.editor) {
				this.editor.prepareSave();
			}

			var content = this.content.val();

			this.messages.empty();
			this.showSpinner();
			this.validate(content);
		},
		validate:         function (content) {
			jQuery.ajax({
										type:    'POST',
										url:     ajaxurl,
										data:    {
											action:  this.action + '-validate',
											nonce:   this.nonceValidate,
											content: content
										},
										context: this,
										success: jQuery.proxy(this.validated, this),
										error:   jQuery.proxy(this.onError, this)
									});
		},
		validated:        function (response) {
			this.addMessage(response);
			if (response.success) {
				this.save(this.content.val());
			} else {
				this.highlightErrors(response);
				this.hideSpinner();
			}
		},
		onError:          function (response) {
			this.addMessage(response);
			this.highlightErrors(response);
			this.hideSpinner();
		},
		highlightErrors:  function (response) {
			if (this.editor) {
				this.editor.highlightErrors(response.data);
			}
		},
		save:             function (content) {
			jQuery.ajax({
										type:     'POST',
										url:      ajaxurl,
										data:     {
											action:  this.action + '-save',
											nonce:   this.nonceSave,
											content: content
										},
										context:  this,
										success:  jQuery.proxy(this.saved, this),
										error:    jQuery.proxy(this.onError, this),
										complete: jQuery.proxy(this.hideSpinner, this)
									});
		},
		saved:            function (response) {
			this.addMessage(response);
		},
		addMessage:           function (response) {
			var message = '';
			var type = '';
			if ('undefined' !== response) {
				message = 'error';
				type = 'error';
				if (response.hasOwnProperty('success') && response.success) {
					type = 'info';
					message = 'success';
				}
				if (response.hasOwnProperty('data') && response.data) {
					if (typeof(response.data) === 'string' || response.data instanceof String) {
						message = response.data;
					} else if (response.data.constructor === Array) {
						message = this.convertArrayToUL(response.data);
					} else if (typeof response.data === 'object') {
						message = this.convertObjectToTable(response.data);
					}
				}
			}
			var messageContainer = jQuery('<p></p>');
			messageContainer.addClass(type);
			messageContainer.html(message);
			this.messages.append(messageContainer);
		},
		showSpinner:          function () {
			this.spinner.addClass('is-active');
		},
		hideSpinner:          function () {
			this.spinner.removeClass('is-active');
		},
		convertObjectToTable: function (data) {
			var html = '<table>';
			for (var property in data) {
				if (data.hasOwnProperty(property)) {
					html += '<tr><th>' + property + '</th><td>' + data[property] + '</td></tr>';
				}
			}
			html += '</table>';
			return html;
		},
		convertArrayToUL: function (data) {
			var html = '<ul>';
			for (var property in data) {
				if (data.hasOwnProperty(property)) {
					html += '<li>';
					if (typeof(data[property]) === 'string' || data[property] instanceof String) {
						html += data[property];
					} else if (data[property].constructor === Array) {
						html += this.convertArrayToUL(data[property]);
					} else if (typeof data[property] === 'object') {
						if (data[property].hasOwnProperty('message') && data[property].message) {
							html += data[property].message;
						} else if (data[property].hasOwnProperty('error') && data[property].error) {
							html += data[property].error;
						} else {
							html += this.convertObjectToTable(data[property]);
						}
					}
					html += '</li>';
				}
			}
			html += '</ul>';
			return html;
		}
	};

	jQuery(document).ready(function () {
		var items = document.getElementsByClassName('js-wpml-tm-custom-xml');

		var i;
		for (i = 0; i < items.length; i++) {
			var codeMirror;
			if (WPML_TM.Custom_XML_Editor) {
				codeMirror = new WPML_TM.Custom_XML_Editor(items[i]);
			}
			var customXML = new WPML_TM.Custom_XML(jQuery(items[i]), codeMirror);
			customXML.init();
		}
	});

}());
