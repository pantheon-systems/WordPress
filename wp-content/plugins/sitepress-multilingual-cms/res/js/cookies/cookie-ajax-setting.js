jQuery(document).ready(function () {

	var cookie_setting      = wpml_cookie_setting,
		ajax_success_action = function( response, response_text ) {

		if( response.success ) {
			response_text.text( icl_ajx_saved );
		} else {
			response_text.text( icl_ajx_error );
		}

		response_text.show();

		setTimeout(function () {
			response_text.fadeOut('slow');
		}, 2500);
	},
		openTooltip = function(triggerNode) {

			var content = triggerNode.data('content');

			jQuery('.js-wpml-cookie-active-tooltip').pointer('close');

			if(triggerNode.length && content) {
				triggerNode.addClass('js-wpml-cookie-active-tooltip');
				triggerNode.pointer({
					pointerClass : 'js-wpml-cookie-tooltip wpml-ls-tooltip',
					content:       content,
					position: {
						edge:  'bottom',
						align: 'left'
					},
					show: function(event, t){
						t.pointer.css('marginLeft', '-54px');
					},
					close: function(event, t){
						t.pointer.css('marginLeft', '0');
					},
					buttons: function( event, t ) {
						var button = jQuery('<a class="close" href="#">&nbsp;</a>');

						return button.bind( 'click.pointer', function(e) {
							e.preventDefault();
							t.element.pointer('close');
						});
					}

				}).pointer('open');
			}
		};

	jQuery( '#' + cookie_setting.button_id ).click(function(){

		var store_frontend_cookie = jQuery( 'input[name*="' + cookie_setting.field_name + '"]:checked' ).val(),
			response_text         = jQuery( '#' + cookie_setting.ajax_response_id ),
			spinner               = jQuery( '#js-store-frontend-cookie-spinner' );

		spinner.addClass( 'is-active' );

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: cookie_setting.ajax_action,
				nonce: jQuery( '#' + cookie_setting.nonce ).val(),
				store_frontend_cookie: store_frontend_cookie
			},
			success: function ( response ) {
				spinner.removeClass( 'is-active' );
				ajax_success_action( response, response_text );
			}
		});
	});

	jQuery( '.js-wpml-cookie-tooltip-open' ).click( function( e ) {
		e.preventDefault();
		openTooltip( jQuery( this ) );
	});

});