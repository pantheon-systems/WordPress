function gtm4wp_set_cookie( cookiename, cookievalue, expiredays ) {
	var d = new Date();
	d.setTime(d.getTime() + (expiredays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();

	document.cookie = cookiename + "=" + cookievalue + ";" + expires + ";path=/";
}

function gtm4wp_get_cookie( cookiename ) {
	var decoded_cookie_list = decodeURIComponent(document.cookie).split(';');
	var onecookie = '';

	for( var i=0; i<decoded_cookie_list.length; i++ ) {
		onecookie = decoded_cookie_list[i].trim();
		if ( 0 == onecookie.indexOf( cookiename ) ) {
			return onecookie.substring( cookiename.length+1, onecookie.length );
		}
	}

	return "";
}

var gtm4wp_user_logged_in = gtm4wp_get_cookie( 'gtm4wp_user_logged_in' );
if ( gtm4wp_user_logged_in === "1" ) {
	window[ gtm4wp_datalayer_name ].push({
		'event': 'gtm4wp.userLoggedIn',
	});

	gtm4wp_set_cookie( 'gtm4wp_user_logged_in', '', -1 );
}

var gtm4wp_new_user_registered = gtm4wp_get_cookie( 'gtm4wp_user_registered' );
if ( gtm4wp_new_user_registered === "1" ) {
	window[ gtm4wp_datalayer_name ].push({
		'event': 'gtm4wp.userRegistered',
	});

	gtm4wp_set_cookie( 'gtm4wp_user_registered', '', -1 );
}
