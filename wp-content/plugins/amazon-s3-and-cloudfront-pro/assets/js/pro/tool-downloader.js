var as3cfDeactivatePluginModal = (function( $, as3cfModal ) {

	var modal = {
		selector: '.as3cf-deactivate-plugin-container',
		event: {}
	};

	/**
	 * Open modal
	 *
	 * @param {object} event
	 */
	modal.open = function( event ) {
		modal.event = event;
		modal.event.preventDefault();

		as3cfModal.open( modal.selector, null, 'deactivate-plugin' );
	};

	/**
	 * Close modal
	 */
	modal.close = function( download ) {
		as3cfModal.setLoadingState( false );
		as3cfModal.close();

		var url = modal.event.target;

		if ( 1 === parseInt( download ) ) {
			url = as3cfpro_downloader.plugin_url;
		}

		window.location = url;
	};

	// Setup click handlers
	$( document ).ready( function() {

		$( 'body' ).on( 'click', '.deactivate-plugin [data-download-tool]', function( e ) {
			var value = $( this ).data( 'download-tool' );

			$( '[data-download-tool]' ).prop( 'disabled', true ).siblings( '.spinner' ).css( 'visibility', 'visible' ).show();

			as3cfModal.setLoadingState( true );

			modal.close( value );
		} );

		$( 'body' ).on( 'click', '#' + as3cfpro_downloader.plugin_slug + ' .deactivate a, [data-slug="' + as3cfpro_downloader.plugin_slug + '"]  .deactivate a', function( event ) {
			as3cfDeactivatePluginModal.open( event );
		} );

	} );

	return modal;

})( jQuery, as3cfModal );
