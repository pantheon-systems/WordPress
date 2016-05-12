(function($){
	$( document ).ready( function() {
		var $body = $( 'body' );

		$( '.et_dashboard_authorize' ).click( function() {
			var $this_button = $( this ),
				$key_field = $this_button.closest( 'ul' ).find( '.api_option_key' ),
				$spinner = $this_button.closest( 'li' ).find( 'span.spinner' );

			authorize_aweber( $key_field, $spinner );

			return false;
		});

		function authorize_aweber( $key_field, $spinner ) {
			var $container = $key_field.closest( '.et_dashboard_form' );

			$key_field.css( { 'border' : 'none' } );

			if ( $key_field.length && '' == $key_field.val() ) {
				$key_field.css( { 'border' : '1px solid red' } );
			} else {
				$.ajax({
					type: 'POST',
					url: builder_settings.ajaxurl,
					data: {
						action : 'et_builder_authorize_aweber',
						et_builder_nonce : builder_settings.et_builder_nonce,
						et_builder_api_key : $key_field.val()
					},
					beforeSend: function( data ) {
						$spinner.addClass( 'et_dashboard_spinner_visible' );
					},

					success: function( data ){
						$spinner.removeClass( 'et_dashboard_spinner_visible' );

						if ( 'success' == data || '' == data ) {
							$( $container ).find( '.et_dashboard_authorize' ).text( builder_settings.reauthorize_text );
							window.et_dashboard_generate_warning( builder_settings.authorization_successflull, '#', '', '', '', '' );
						} else {
							window.et_dashboard_generate_warning( data, '#', '', '', '', '' );
						}
					}
				});
			}

			return false;
		}

		$( '.et_dashboard_get_lists' ).click( function() {
			var $this_button = $( this ),
				$this_spinner = $this_button.closest( 'li' ).find( 'span.spinner' ),
				service_name = $this_button.hasClass( 'et_pb_aweber' ) ? 'aweber' : 'mailchimp',
				options_fromform = $( '.et_builder #et_dashboard_options' ).serialize();

			$.ajax({
				type: 'POST',
				url: builder_settings.ajaxurl,
				data: {
					action : 'et_builder_refresh_lists',
					et_builder_nonce : builder_settings.et_builder_nonce,
					et_builder_mail_service : service_name,
					et_builder_form_options : options_fromform
				},
				beforeSend: function( data ) {
					$this_spinner.addClass( 'et_dashboard_spinner_visible' );
				},

				success: function( data ) {
					$this_spinner.removeClass( 'et_dashboard_spinner_visible' );
					window.et_dashboard_generate_warning( data, '#', '', '', '', '' );
				}
			});

			return false;
		});

		$( '.et_dashboard_updates_save' ).click( function() {
			var $this_button = $( this ),
				$this_container = $this_button.closest( 'ul' ),
				$this_spinner = $this_container.find( 'span.spinner' ),
				username = $this_container.find( '.updates_option_username' ).val(),
				api_key = $this_container.find( '.updates_option_api_key' ).val();

			$.ajax({
				type: 'POST',
				url: builder_settings.ajaxurl,
				data: {
					action : 'et_builder_save_updates_settings',
					et_builder_nonce : builder_settings.et_builder_nonce,
					et_builder_updates_username : username,
					et_builder_updates_api_key : api_key
				},
				beforeSend: function( data ) {
					$this_spinner.addClass( 'et_dashboard_spinner_visible' );
				},

				success: function( data ) {
					$this_spinner.removeClass( 'et_dashboard_spinner_visible' );
				}
			});

			return false;
		});

		$( '#et_pb_save_plugin' ).click( function() {
			var $loading_animation = $( '#et_pb_loading_animation' ),
				$success_animation = $( '#et_pb_success_animation' ),
				options_fromform;

			tinyMCE.triggerSave();
			options_fromform = $( '.' + dashboardSettings.plugin_class + ' #et_dashboard_options' ).serialize();

			$.ajax({
				type: 'POST',
				url: builder_settings.ajaxurl,
				data: {
					action : 'et_builder_save_settings',
					options : options_fromform,
					options_sub_title : '',
					save_settings_nonce : builder_settings.save_settings
				},
				beforeSend: function ( xhr ) {
					$loading_animation.removeClass( 'et_pb_hide_loading' );
					$success_animation.removeClass( 'et_pb_active_success' );
					$loading_animation.show();
				},
				success: function( data ) {
					$loading_animation.addClass( 'et_pb_hide_loading' );
					$success_animation.addClass( 'et_pb_active_success' ).show();

					setTimeout( function(){
						$success_animation.fadeToggle();
						$loading_animation.fadeToggle();
					}, 1000 );
				}
			});

			return false;
		});

		$body.append( '<div id="et_pb_loading_animation"></div>' );
		$body.append( '<div id="et_pb_success_animation"></div>' );

		$( '#et_pb_loading_animation' ).hide();
		$( '#et_pb_success_animation' ).hide();
	});
})(jQuery)