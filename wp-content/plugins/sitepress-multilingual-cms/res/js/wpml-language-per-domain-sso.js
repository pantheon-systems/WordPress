jQuery(document).ready(function ($) {
	'use strict';

	$('.wpml_iframe').load(function() {
		var userStatus = 'wpml_is_user_signed_out';

		if ( wpml_sso.is_user_logged_in ) {
			userStatus = 'wpml_is_user_signed_in';
		}

		this.contentWindow.postMessage(JSON.stringify({userStatus: userStatus, userId: wpml_sso.current_user_id}), "*");
	});
});
