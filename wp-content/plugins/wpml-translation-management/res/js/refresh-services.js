/*globals jQuery, ajaxurl*/

jQuery(document).ready(function () {
	"use strict";

	jQuery( '#wpml-tm-refresh-services' ).click(function(){
		var button = jQuery(this);

		button.prop( 'disabled', true );

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				'nonce': jQuery(this).data('nonce'),
				'action': 'wpml_tm_refresh_services'
			},
			dataType: 'json',
			success:  function ( res ) {
				button.prop( 'disabled', true );
				jQuery( '.wpml-tm-refresh-services-msg' ).html( res.data.message );
			}
		});
	})
});
