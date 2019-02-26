/*jshint devel:true */
/*global ajaxurl, _ */

var WPML_TM = WPML_TM || {};

WPML_TM.setTranslationRole = function ( $, content, nonce, role, validStateCallback ) {
	"use strict";

	var self = this;

	var init = function () {
		if ( content ) {
			self.newUserInputs = content.find( '.js-first-name, .js-last-name, .js-email, .js-user-name' );
			self.wpRoleSelect = content.find( '.js-role' );
			self.wpRoleSelect.val( 'editor' );

			self.newUserInputs.on( 'input', inputChange );
		}

	};

	var setTranslationRole = function ( data, success, error ) {
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			data: _.extend( data, {
				action: 'wpml_add_translation_' + role,
				nonce: nonce
			} ),
			success: function ( response ) {
				if ( response.success ) {
					success( response.data );
				} else {
					error( response.data );
				}
			}
		} );

	};

	var inputChange = function( e ) {
		validStateCallback( self.isValid() );
	};

	self.setExisting = function ( userId, success, error, extraData ) {
		setTranslationRole(
			_.extend( extraData, { user_id : userId } ),
			success,
			error
		);
	};

	self.addNew = function ( success, error, extraData ) {
		setTranslationRole( _.extend( extraData, {
				first: $( self.newUserInputs[ 0 ] ).val(),
				last: $( self.newUserInputs[ 1 ] ).val(),
				email: $( self.newUserInputs[ 2 ] ).val(),
				user: $( self.newUserInputs[ 3 ] ).val(),
				role: self.wpRoleSelect.val()
			} ),
			success,
			error
		);
	};

	self.isValid = function () {
		return self.getUserName() && self.isEmailValid( self.getEmail() );
	};

	self.isEmailValid = function ( email ) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test( email );
	};

	self.resetInputs = function() {
		self.newUserInputs.val( '' );
	};

	self.getUserName = function() {
		return $( self.newUserInputs[ 3 ] ).val();
	};

	self.getEmail = function() {
		return $( self.newUserInputs[ 2 ] ).val();
	};

	init();

};

