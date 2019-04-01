(function( $, wp, _ ) {

	/**
	 * Create and display a spinner next to an element
	 *
	 * @param element
	 */
	function createSpinner( element ) {
		var $spinner = $( '<span />', { class: 'spinner' } );
		$spinner.css( { 'display': 'inline-block', 'float': 'none', 'visibility': 'visible' } );
		$spinner.insertAfter( element );

		return $spinner;
	}

	$( document ).ready( function() {

		// Move the filesystem credential form below the last error notice
		var $lastNotice = $( '.as3cf-pro-installer-notice' ).last();
		$( '.as3cfpro-installer-filesystem-creds' ).insertAfter( $lastNotice );

		$( '.as3cfpro-installer-filesystem-creds form' ).on( 'submit', function( e ) {
			var $submitBtn = $( '#upgrade' );
			if ( $submitBtn.hasClass( 'button-disabled' ) ) {
				return;
			}

			$submitBtn.addClass( 'button-disabled' );
			$submitBtn.val( as3cfpro_installer.strings.installing + '...' );

			createSpinner( $submitBtn );
		} );

		$( 'body' ).on( 'click', '.as3cf-pro-installer .install-plugins', function( e ) {
			e.preventDefault();
			if ( $( this ).hasClass( 'button-disabled' ) ) {
				return;
			}

			$( this ).addClass( 'button-disabled' );
			$( this ).text( as3cfpro_installer.strings.installing + '...' );

			var $spinner = createSpinner( this );

			var process = $( this ).data( 'process' );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: 'as3cfpro_install_plugins_' + process,
					nonce: as3cfpro_installer.nonces.install_plugins
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					installerError( errorThrown + ' - ' + textStatus );
				},
				success: function( response ) {
					if ( _.isObject( response ) && ! _.isUndefined( response.success ) ) {

						if ( response.success ) {
							window.location.href = response.data.redirect;
						}

						if ( ! _.isUndefined( response.data.error ) ) {
							installerError( response.data.error );
						}
					}
				}
			} );

			/**
			 * Render an error notice and remove the installer notice
			 *
			 * @param string errorMessage
			 */
			function installerError( errorMessage ) {
				var noticeText = as3cfpro_installer.strings.error_installing + ': ' + errorMessage;
				var noticeHtml = '<div class="error"><p>' + noticeText + '</p></div>';

				$( '.as3cf-pro-installer' ).after( noticeHtml );
				$( '.as3cf-pro-installer' ).remove();

				$spinner.hide();
			}

		} );

	} );

})( jQuery, wp, _ );
