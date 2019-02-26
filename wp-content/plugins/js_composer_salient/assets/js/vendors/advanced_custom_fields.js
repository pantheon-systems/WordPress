jQuery( document ).on( 'acf/setup_fields', function ( e, el ) {
	// Redeclare active editor.
	setTimeout( function () {
		if ( 'tinymce' === getUserSetting( 'editor' ) ) {
			jQuery( '#content-tmce' ).trigger( 'click' );
		} else {
			jQuery( '#content-html' ).trigger( 'click' );
		}
	}, 10 );
} );