/*global Backbone, WpmlTmEditorModel, document, jQuery, _ */

var WPML_TM = WPML_TM || {};

(function () {
	"use strict";

	WPML_TM.editorJobFieldView = Backbone.View.extend({
		tagName: 'div',
		className: 'wpml-form-row',
		events: {
			'click .icl_tm_copy_link': 'copyField',
			'click .js-toggle-diff': 'toggleDiff',
			'change .js-field-translation-complete' : 'setInputStatus'
		},
		toggleDiff: function(e) {
			e.preventDefault();
			var diff = this.$el.find('.diff');
			if(diff.is(':visible')) {
				diff.fadeOut();
			} else {
				diff.fadeIn();
			}
			this.updateUI();
		},
		copyField: function () {
			var self = this;
			self.setTranslation(self.getOriginal());
			return false;
		},
		updateUI: function () {
			var self = this;
			if (self.$el.is(':visible')) {
				try {
					var original = self.getOriginal();
					var translation = self.getTranslation();

					self.$el.find('.icl_tm_copy_link').prop('disabled', translation !== '' || original === '');
					self.translationCompleteCheckbox.prop('disabled', translation === '');
					if ('' === translation) {
						self.translationCompleteCheckbox.prop('checked', false);
						self.translationCompleteCheckbox.trigger('change');
					}
					self.sendMessageToGroupView();
					jQuery(document).trigger('WPML_TM.editor.field_update_ui', self);
				}
				catch(err) {
					// this try - catch block is needed because this is sometimes called before tiny MCE editor is completely initialized
				}
			}
		},
		render: function (field, labels) {
			var self = this;
			self.field = field;
			if (typeof self.field.title === 'undefined' || '' === self.field.title) {
				self.$el.removeClass('wpml-form-row').addClass('wpml-form-row-nolabel');
			}
			self.$el.html(WPML_TM[self.getTemplate()]({
				field: self.field,
				labels: labels
			}));
			self.translationCompleteCheckbox = self.$el.find('.js-field-translation-complete');
			_.defer(_.bind(self.updateUI, self));
			if (WpmlTmEditorModel.hide_empty_fields && field.field_data === '') {
				self.$el.hide().addClass('hidden');
				self.translationCompleteCheckbox.prop('checked', true);
				self.translationCompleteCheckbox.prop('disabled', false);
			}
			if (!WpmlTmEditorModel.requires_translation_complete_for_each_field) {
				self.translationCompleteCheckbox.hide();
				self.translationCompleteCheckbox.parent().hide();
			}
			self.$el.find('.field-diff').find('.diff').hide();

			self.setTranslatedColor( self.getStatusColors() );

			jQuery(document).trigger('WPML_TM.editor.field_view_ready', self);
		},

		setup: function () {
		},

		sendMessageToGroupView: function () {
			var group = this.$el.closest('.wpml-field-group');
			if (group.length) {
				group.find('.js-button-copy-group').trigger('update_button_state');
			}
		},

		getFieldType: function () {
			return this.field.field_type;
		},

		setInputStatus: function() {
			var self = this;
			self.setTranslatedColor( self.getStatusColors() );
			_.delay( function () {
				jQuery( '.js-toggle-translated' ).trigger( 'change' );
			}, 1000 );
		},

		getStatusColors: function () {
			var self = this;
			if (self.translationCompleteCheckbox.is(':checked')) {
				return {
					background: '#e8f7e3',
					borderColor: '#d0e9c6'
				};
			} else {
				return {
					background: '',
					borderColor: ''
				};
			}
		},

		hideTranslated: function ( state ) {
			var self = this;
			if ( self.translationCompleteCheckbox.is( ':checked' ) && state ) {
				self.$el.hide();
			} else {
				if ( !self.$el.hasClass( 'hidden' ) ) {
					self.$el.show();
				}
			}
		}

	});

}());
