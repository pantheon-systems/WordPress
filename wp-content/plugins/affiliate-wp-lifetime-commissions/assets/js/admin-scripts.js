jQuery(document).ready(function ($) {

	$( document.body ).on( 'click', '#affwp_lc_add_email', function(e) {

		e.preventDefault();

		var customer_email = $( '#affwp_lc_email' ).val(),
			affiliate_id = $( '#affwp_lc_affiliate_id' ).val(),
			add_email_button = $( '#affwp_lc_add_email' ),
			email_field = $( '#affwp_lc_email' );

		add_email_button.prop( 'disabled', true );

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				customer_email: customer_email,
				affiliate_id: affiliate_id,
				action: 'affwp_lc_add_email'
			},
			dataType: "json",
			success: function( response ) {

				console.log( response );

				if ( response.success ) {

					$( '<div class="notice notice-success"><p>' + response.data.message + '</p></div>' ).insertAfter( add_email_button ).fadeOut(2000);

					email_field.val('');

					add_email_button.prop('disabled', false);

					$( 'ul.affwp-lc-linked-customers' ).append( response.data.customer_html );

				} else {

					add_email_button.prop('disabled', false);

					$( '<div class="notice notice-error"><p>' + response.data.message + '</p></div>' ).insertAfter( add_email_button ).fadeOut(2000);

				}

			}
		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		});

	});

});
    
    
    