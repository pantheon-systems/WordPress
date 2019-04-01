/*jshint devel:true */
/*global jQuery, ajaxurl, _, head, WPML_headjs */

var WPML_TM = WPML_TM || {};

WPML_TM.translationManagers = function ( $ ) {
	"use strict";

	var self = this;

	var init = function () {

		$( document ).ready( function () {

			self.main = $( '.js-translation-managers' );

			self.list = $( '.js-translation-managers-list' );
			self.noTranslationManagersMessage = $( '.js-no-translation-managers' );
			self.nonce = self.main.data( 'nonce' );
			self.selectedUser = null;
			self.addExistingTranslationManagerButton = $( '.js-add-existing-translation-manager' );
			self.addNewTranslationManagerButton = $( '.js-add-new-translation-manager' );
			self.error = $( '.js-error-message' );
			self.newOrExistingRadiosWrap = $('.js-translation-managers-radios');
			self.newOrExistingRadios = $( '[name="user-manager"]' );
			self.newSection = self.main.find( '.js-new-user-section' );
			self.existingSection = self.main.find( '.js-existing-user-section' );

			self.addExistingTranslationManagerButton.on( 'click', self.addExistingTranslationManager );
			self.addNewTranslationManagerButton.on( 'click', self.addNewTranslationManager );

			$( '.js-remove-translation-manager' ).on( 'click', self.removeTranslationManager );

			self.newOrExistingRadios.on( 'change', self.newOrExistingMode );

			WPML_TM.translationRolesSelect2( $( '.js-translation-manager-select' ), self.nonce, 'manager', self.selectChange );

			self.addTranslationManager = new WPML_TM.setTranslationRole(
				$,
				self.newSection,
				self.nonce,
				'manager',
				self.enableNewUserButton
			);

			self.showOrHideList();
			self.newOrExistingMode();

		} );
	};

	self.selectChange = function ( e ) {
		self.selectedUser = $( this ).val();
		if ( '' === self.selectedUser ) {
			self.addExistingTranslationManagerButton.attr( 'disabled', 'disabled' );
		} else {
			self.addExistingTranslationManagerButton.removeAttr( 'disabled' );
		}
	};

	self.removeTranslationManager = function ( e ) {
		var row = $( this ).closest( 'li' ),
			userID = row.data( 'user_id' );


		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			data: {
				action: 'wpml_remove_translation_manager',
				nonce: self.nonce,
				user_id: userID
			},
			success: function ( response ) {
				if ( response.success ) {
					row.fadeOut( 300, function () {
						$( this ).remove();
						self.showOrHideList();
						self.refreshPageIfYouRemoveYourself( userID );
					} );
				} else {
					self.showError( response.data );
				}
			}
		} );
	};

	self.addExistingTranslationManager = function ( e ) {
		self.addExistingTranslationManagerButton.attr( 'disabled', 'disabled' );
		self.addTranslationManager.setExisting( self.selectedUser, self.translationManagerAdded, self.translationManagerError, { sendEmail: true } );
	};

	self.addNewTranslationManager = function ( e ) {
		self.enableNewUserButton( false );
		self.addTranslationManager.addNew( self.translationManagerAdded, self.translationManagerError, { sendEmail : true } );
	};

	self.translationManagerAdded = function( response ) {
		self.list.prepend( response );
		self.showOrHideList();
		$( '.js-remove-translation-manager' ).off( 'click' ).on( 'click', self.removeTranslationManager );
		self.refreshPageIfYouAddedYourself();
	};

	self.translationManagerError = function( response ) {
		self.showError( response );
	};

	self.addNewFormHide = function() {
		self.newSection.hide();
		self.existingSection.hide();
		self.newOrExistingRadios.prop('checked', false);
	};

	self.showOrHideList = function () {
		if ( self.list.find( 'li' ).length ) {
			self.newOrExistingRadiosWrap.find('label').removeClass('button-primary').addClass('button-secondary');
			self.addNewFormHide();
			self.list.show();
			self.noTranslationManagersMessage.hide();
		} else {
			self.newOrExistingRadiosWrap.find('label').removeClass('button-secondary').addClass('button-primary');
			self.addNewFormHide();
			self.list.hide();
			self.noTranslationManagersMessage.show();
		}
	};

	self.showError = function( message ) {
		self.error.text( message );
		self.error.show();
		setTimeout( function() {
			self.error.fadeOut();
		}, 5000 );
	};

	self.newOrExistingMode = function ( e ) {
		_.each( self.newOrExistingRadios, function( radio ) {
			radio = $( radio );
			if ( radio.is( ':checked') && radio.val() === 'existing' ) {
				self.newSection.hide();
				self.existingSection.show();
				$('[for="new-user-manager"]', self.newOrExistingRadiosWrap ).addClass('button-secondary').removeClass('button-primary');
				$('[for="existing-user-manager"]', self.newOrExistingRadiosWrap ).addClass('button-primary').removeClass('button-secondary');
			}
			if ( radio.is( ':checked') && radio.val() === 'new' ) {
				self.newSection.show();
				self.existingSection.hide();
				$('[for="existing-user-manager"]', self.newOrExistingRadiosWrap ).addClass('button-secondary').removeClass('button-primary');
				$('[for="new-user-manager"]', self.newOrExistingRadiosWrap ).addClass('button-primary').removeClass('button-secondary');
			}
		});
	};

	self.enableNewUserButton = function( state ) {
		if ( state ) {
			self.addNewTranslationManagerButton.removeAttr( 'disabled' );
		} else {
			self.addNewTranslationManagerButton.attr( 'disabled', 'disabled' );
		}
	};

	self.refreshPageIfYouAddedYourself = function() {
		var currentUser = self.main.data( 'current-user-id' );
		var lastUserAdded = self.list.find( 'li' ).data( 'user_id' );
		if ( currentUser === lastUserAdded ) {
			location.reload();
		}
	};

	self.refreshPageIfYouRemoveYourself = function( removedUser) {
		var currentUser = self.main.data( 'current-user-id' );
		if ( currentUser === removedUser ) {
			location.reload();
		}
	};

	init();

};

WPML_TM.translationManagersInstance = new WPML_TM.translationManagers( jQuery );

