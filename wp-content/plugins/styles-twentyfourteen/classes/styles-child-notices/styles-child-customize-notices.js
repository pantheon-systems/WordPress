jQuery( document ).ready( function ( $ ) {

	styles_child_notices();
	/**
	 * Prompt users if a notice is sent by styles-admin.php
	 */
	function styles_child_notices() {
		if ( wp_styles_child_notices.length == 0 ) {
			return;
		}

		var $notices = $( '<div id="styles_child_notices"></div>' )
		             .addClass( 'accordion-section-content' )
		             .show();

		jQuery.each( wp_styles_child_notices, function( index, value ){
			$notices.append( value );
		});

		$( '#customize-info' ).prepend( $notices );
	}

});