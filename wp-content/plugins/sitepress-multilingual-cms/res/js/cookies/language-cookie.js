jQuery( 'document' ).ready(function(){
	jQuery.each( wpml_cookies, function( cookieName, cookieData ) {
		jQuery.cookie(cookieName, cookieData.value, {
			'expires': cookieData.expires,
			'path': cookieData.path
		});
	});
});