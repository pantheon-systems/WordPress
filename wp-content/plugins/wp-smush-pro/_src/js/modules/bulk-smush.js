jQuery( function ( $ ) {
	'use strict';

	// Remove dismissable notices.
	$( '.sui-wrap' ).on( 'click', '.sui-notice-dismiss', function ( e ) {
		e.preventDefault();

		$( this ).parent().stop().slideUp( 'slow' );
	} );

	$( window ).on( 'load', function () {
		// If quick setup box is found, show.
		if ( $( '#smush-quick-setup-dialog' ).length > 0 ) {
			// Show the modal.
			window.SUI.dialogs['smush-quick-setup-dialog'].show();
		}
	} );

} );