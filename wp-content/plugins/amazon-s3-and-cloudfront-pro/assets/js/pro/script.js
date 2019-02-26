(function( $, _, as3cfModal ) {

	var adminUrl = ajaxurl.replace( '/admin-ajax.php', '' );
	var spinnerUrl = adminUrl + '/images/spinner';

	var savedSettings = null;
	var $body = $( 'body' );
	var $main = $( '.as3cf-main' );
	var initSupportTab;

	if ( window.devicePixelRatio >= 2 ) {
		spinnerUrl += '-2x';
	}

	spinnerUrl += '.gif';

	as3cfpro.spinnerUrl = spinnerUrl;

	/**
	 * Licence Key API object
	 * @constructor
	 */
	var LicenceApi = function() {
		this.$key = $main.find( '.as3cf-licence-input' );
		this.$spinner = $main.find( '[data-as3cf-licence-spinner]' );
		this.$feedback = $main.find( '[data-as3cf-licence-feedback]' );
	};

	/**
	 * Set the license key using the values in the settings fields.
	 */
	LicenceApi.prototype.activate = function() {
		var licenceKey = $.trim( this.$key.val() );

		if ( '' === licenceKey ) {
			this.$feedback.addClass( 'notice-error' );
			this.$feedback.html( '<p>' + as3cfpro.strings.enter_licence_key + '</p>' ).show();
			return;
		}

		return this.sendRequest( 'activate', {
			licence_key: licenceKey
		} )
			.done( function( response ) {
				if ( response.success && response.data ) {
					this.$key.val( response.data.masked_licence );
				}
			}.bind( this ) )
			.fail( function() {
				this.$feedback.html( '<p>' + as3cfpro.strings.register_licence_problem + '</p>' ).show();
			}.bind( this ) )
		;
	};

	/**
	 * Remove the license key from the database and clear the fields.
	 */
	LicenceApi.prototype.remove = function() {
		return this.sendRequest( 'remove' )
			.done( function( response ) {
				if ( response.success ) {
					this.$key.val( '' );
				}
			}.bind( this ) )
		;
	};

	/**
	 * Check the licence key
	 */
	LicenceApi.prototype.check = function() {
		return this.sendRequest( 'check' )
			.fail( function( jqXHR ) {

				// Ignore incomplete requests, likely due to navigating away from the page
				if ( jqXHR.readyState < 4 ) {
					return;
				}

				alert( as3cfpro.strings.licence_check_problem );
			} )
		;
	};

	/**
	 * Send the request to the server to update the licence key.
	 *
	 * @param {string} action The action to perform with the keys
	 * @param {undefined|Object} params Extra parameters to send with the request
	 *
	 * @returns {jqXHR}
	 */
	LicenceApi.prototype.sendRequest = function( action, params ) {
		var data = {
			action: 'as3cfpro_' + action + '_licence',
			_ajax_nonce: as3cfpro.nonces[ action + '_licence' ]
		};

		if ( _.isObject( params ) ) {
			data = _.extend( data, params );
		}

		this.$spinner.addClass( 'is-active' ).show();

		return $.post( ajaxurl, data )
			.done( function( response ) {
				this.$feedback
					.toggleClass( 'notice-success', response.success )
					.toggleClass( 'notice-error', ! response.success );

				if ( response.data && response.data.message ) {
					this.$feedback.html( '<p>' + response.data.message + '</p>' ).show();
				}

				if ( response.success ) {
					as3cf.reloadUpdated();
				}
			}.bind( this ) )
			.always( function() {
				this.$spinner.removeClass( 'is-active' ).hide();
			}.bind( this ) )
		;
	};

	/**
	 * Check the licence and return licence info from deliciousbrains.com
	 *
	 * @param licence
	 */
	function checkLicence( licence ) {
		var $support = $main.find( '.support-content' );
		var api = new LicenceApi();

		$( '.as3cf-pro-licence-notice' ).remove();

		api.check( {
			licence: licence
		} )
			.done( function( data ) {
				if ( ! _.isEmpty( data.dbrains_api_down ) ) {
					$support.html( data.dbrains_api_down + data.message );
				} else if ( _.isArray( data.htmlErrors ) && data.htmlErrors.length ) {
					$support.html( data.htmlErrors.join( '' ) );
				} else {
					$support.html( data.message );
				}

				if ( ! _.isEmpty( data.pro_error ) && 0 === $( '.as3cf-pro-licence-notice' ).length ) {
					$( 'h2.nav-tab-wrapper' ).after( data.pro_error );
				}
			} )
		;
	}

	/* Check the licence on the first load of the Support tab */
	initSupportTab = _.once( checkLicence );

	/**
	 * Convert form inputs to single level object
	 *
	 * @param {object} form
	 *
	 * @returns {object}
	 */
	function formInputsToObject( form ) {
		var formInputs = $( form ).serializeArray();
		var inputsObject = {};

		$.each( formInputs, function( index, input ) {
			inputsObject[ input.name ] = input.value;
		} );

		return inputsObject;
	}

	/**
	 * Edit the hash of the check licence URL so we reload to the correct tab
	 *
	 * @param hash
	 */
	function editcheckLicenseURL( hash ) {
		if ( 'support' !== hash && $( '.as3cf-pro-check-again' ).length ) {
			var checkLicenseURL = $( '.as3cf-pro-check-again' ).attr( 'href' );

			if ( as3cf.tabs.defaultTab === hash ) {
				hash = '';
			}

			if ( '' !== hash ) {
				hash = '#' + hash;
			}

			var index = checkLicenseURL.indexOf( '#' );
			if ( 0 === index ) {
				index = checkLicenseURL.length;
			}

			checkLicenseURL = checkLicenseURL.substr( 0, index ) + hash;

			$( '.as3cf-pro-check-again' ).attr( 'href', checkLicenseURL );
		}
	}

	/**
	 * Show the correct sidebar tools for the tab
	 *
	 * @param {string} tab
	 */
	function toggleSidebarTools( tab ) {
		tab = ( '' === tab ) ? as3cf.tabs.defaultTab : tab;

		$( '.as3cf-sidebar.pro .block' ).not( '.' + tab ).hide();
		$( '.as3cf-sidebar.pro .block.' + tab + '[data-render="1"]' ).show();
	}

	/**
	 * Get the hash of the URL
	 *
	 * @returns {string}
	 */
	function getURLHash() {
		var hash = '';
		if ( window.location.hash ) {
			hash = window.location.hash.substring( 1 );
		}

		hash = as3cf.tabs.sanitizeHash( hash );

		return hash;
	}

	/**
	 * Render the sidebar tools
	 */
	function renderSidebarTools() {
		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			cache: false,
			data: {
				action: 'as3cfpro_render_sidebar_tools',
				nonce: as3cfpro.nonces.render_sidebar_tools
			},
			success: function( response ) {
				if ( true === response.success && 'undefined' !== typeof response.data ) {
					$( '.as3cf-sidebar.pro' ).empty();
					$( '.as3cf-sidebar.pro' ).prepend( response.data );
					var hash = getURLHash();
					toggleSidebarTools( hash );
				}
			}
		} );
	}

	$main.on( 'click', '[data-as3cf-licence-action]', function( event ) {
		var action = $( this ).data( 'as3cfLicenceAction' );
		var api = new LicenceApi();

		event.preventDefault();

		if ( 'function' === typeof api[action] ) {
			api[action]();
		}
	} );

	$( document ).on( 'as3cf.tabRendered', function( event, hash ) {
		if ( 'support' === hash && '1' === as3cfpro.strings.has_licence ) {
			initSupportTab();
		} else if ( 'licence' === hash ) {
			$( '.as3cf-licence-input' ).focus();
		}

		editcheckLicenseURL( hash );
		toggleSidebarTools( hash );
	} );

	$( document ).ready( function() {
		var hash = getURLHash();
		editcheckLicenseURL( hash );
		toggleSidebarTools( hash );

		var $settingsForm = $( '#tab-' + as3cf.tabs.defaultTab + ' .as3cf-main-settings form' );

		savedSettings = formInputsToObject( $settingsForm );

		$body.on( 'click', '.reactivate-licence', function( e ) {
			e.preventDefault();

			var $processing = $( '<div/>', { id: 'processing-licence' } ).html( as3cfpro.strings.attempting_to_activate_licence );
			$processing.append( '<img src="' + as3cfpro.spinnerUrl + '" alt="" class="check-licence-ajax-spinner general-spinner" />' );
			$( '.as3cf-invalid-licence' ).hide().after( $processing );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: 'as3cfpro_reactivate_licence',
					nonce: as3cfpro.nonces.reactivate_licence
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					$processing.remove();
					$( '.as3cf-invalid-licence' ).show().html( as3cfpro.strings.activate_licence_problem );
					$( '.as3cf-invalid-licence' ).append( '<br /><br />' + as3cfpro.strings.status + ': ' + jqXHR.status + ' ' + jqXHR.statusText + '<br /><br />' + as3cfpro.strings.response + '<br />' + jqXHR.responseText );
				},
				success: function( data ) {
					$processing.remove();

					if ( 'undefined' !== typeof data.as3cfpro_error && 1 === data.as3cfpro_error ) {
						$( '.as3cf-invalid-licence' ).html( data.body ).show();
						return;
					}

					if ( 'undefined' !== typeof data.dbrains_api_down && 1 === data.dbrains_api_down ) {
						$( '.as3cf-invalid-licence' ).html( as3cfpro.strings.temporarily_activated_licence );
						$( '.as3cf-invalid-licence' ).append( data.body ).show();
						return;
					}

					$( '.as3cf-invalid-licence' ).empty().html( as3cfpro.strings.licence_reactivated );
					$( '.as3cf-invalid-licence' ).addClass( 'success notification-message success-notice' ).show();
					location.reload();
				}
			} );

		} );

		// Show support tab when 'support request' link clicked within compatibility notices
		$body.on( 'click', '.support-tab-link', function( e ) {
			as3cf.tabs.toggle( 'support' );
		} );

	} );
})( jQuery, _, as3cfModal );
