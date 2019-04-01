(function( $ ) {
	var $notice = $( '.wpses-notice' );

	$notice.on( 'click', '.notice-dismiss', function( e ) {
		console.log( 'clicked' );
		var data = {
			action: 'wp-ses-dismiss-notice',
			_nonce: wpses.dismiss_notice_nonce
		};

		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: data,
			error: function( jqXHR, textStatus, errorThrown ) {
				alert( wpses.dismiss_notice_error + errorThrown );
			}
		} );
	} );
})( jQuery );