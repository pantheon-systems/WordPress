/*jshint devel:true */
/*global ajaxurl, jQuery */

var WPML_TM = WPML_TM || {};

WPML_TM.translationRolesSelect2 = function ( elementSelector, nonce, role, onChange ) {
	"use strict";

	var formatResult = function ( user ) {
		/** @namespace user.full_name */
		var result = user.full_name;

		if (!result) {
			result += user.display_name;
		}

		result += ' - ' + user.user_login;

		if (user.user_email) {
			result += ' (' + user.user_email + ')';
		}

		return result;
	};

	/**
	 * Fix select2 on ui dialogs
	 * https://github.com/select2/select2/issues/1246#issuecomment-71710835
	 */

	var otgs_access_fix_select2_in_dialog = function () {
		//Enable select2 dropdown fix
		if ( jQuery.ui && jQuery.ui.dialog && jQuery.ui.dialog.prototype._allowInteraction ) {
			var ui_dialog_interaction = jQuery.ui.dialog.prototype._allowInteraction;
			jQuery.ui.dialog.prototype._allowInteraction = function ( e ) {
				if ( jQuery( e.target ).closest( '.select2-dropdown' ).length ) return true;
				return ui_dialog_interaction.apply( this, arguments );
			};
		}
	};

	elementSelector.select2( {
		width: '250px',
		minimumInputLength: 2,
		ajax: {
			type: 'POST',
			url: ajaxurl,
			datatype: 'json',
			delay: 250,
			data: function ( params ) {
				return {
					action: 'wpml_search_translation_' + role,
					nonce: nonce,
					search: params
				};
			},
			results: function ( data ) {
				return {
					results: data.data
				};
			}
		},
		formatResult: formatResult,
		formatSelection: formatResult,
		escapeMarkup: function ( m ) {
			return m;
		},
		id: function ( user ) {
			return user.ID;
		}
	} ).on( 'change', onChange );

	otgs_access_fix_select2_in_dialog();

};


