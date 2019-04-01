/*global wc_setup_params */
/*global wc_setup_currencies */
/*global wc_base_state */
jQuery( function( $ ) {
	function blockWizardUI() {
		$('.wc-setup-content').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	}

	$( '.button-next' ).on( 'click', function() {
		var form = $( this ).parents( 'form' ).get( 0 );

		if ( ( 'function' !== typeof form.checkValidity ) || form.checkValidity() ) {
			blockWizardUI();
		}

		return true;
	} );

	$( '#store_country' ).on( 'change', function() {
		// Prevent if we don't have the metabox data
		if ( wc_setup_params.states === null ){
			return;
		}

		var $this         = $( this ),
			country       = $this.val(),
			$state_select = $( '#store_state' );

		if ( ! $.isEmptyObject( wc_setup_params.states[ country ] ) ) {
			var states = wc_setup_params.states[ country ];

			$state_select.empty();

			$.each( states, function( index ) {
				$state_select.append( $( '<option value="' + index + '">' + states[ index ] + '</option>' ) );
			} );

			$( '.store-state-container' ).show();
			$state_select.selectWoo().val( wc_base_state ).change().prop( 'required', true );
		} else {
			$( '.store-state-container' ).hide();
			$state_select.empty().val( '' ).change().prop( 'required', false );
		}

		$( '#currency_code' ).val( wc_setup_currencies[ country ] ).change();
	} );

	$( '#store_country' ).change();

	$( '.wc-wizard-services' ).on( 'change', '.wc-wizard-service-enable input', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.wc-wizard-service-toggle' ).removeClass( 'disabled' );
			$( this ).closest( '.wc-wizard-service-item' ).addClass( 'checked' );
			$( this ).closest( '.wc-wizard-service-item' )
				.find( '.wc-wizard-service-settings' ).removeClass( 'hide' );
		} else {
			$( this ).closest( '.wc-wizard-service-toggle' ).addClass( 'disabled' );
			$( this ).closest( '.wc-wizard-service-item' ).removeClass( 'checked' );
			$( this ).closest( '.wc-wizard-service-item' )
				.find( '.wc-wizard-service-settings' ).addClass( 'hide' );
		}
	} );

	$( '.wc-wizard-services' ).on( 'click', '.wc-wizard-service-enable', function( e ) {
		var eventTarget = $( e.target );

		if ( eventTarget.is( 'input' ) ) {
			e.stopPropagation();
			return;
		}

		var $checkbox = $( this ).find( 'input[type="checkbox"]' );

		$checkbox.prop( 'checked', ! $checkbox.prop( 'checked' ) ).change();
	} );

	$( '.wc-wizard-services-list-toggle' ).on( 'click', function() {
		$( this ).closest( '.wc-wizard-services-list-toggle' ).toggleClass( 'closed' );
		$( this ).closest( '.wc-wizard-services' ).find( '.wc-wizard-service-item' )
			.slideToggle()
			.css( 'display', 'flex' );
	} );

	$( '.wc-wizard-services' ).on( 'change', '.wc-wizard-shipping-method-select .method', function( e ) {
		var zone = $( this ).closest( '.wc-wizard-service-description' );
		var selectedMethod = e.target.value;

		var description = zone.find( '.shipping-method-descriptions' );
		description.find( '.shipping-method-description' ).addClass( 'hide' );
		description.find( '.' + selectedMethod ).removeClass( 'hide' );

		var $checkbox = zone.parent().find( 'input[type="checkbox"]' );
		var settings = zone.find( '.shipping-method-settings' );
		settings
			.find( '.shipping-method-setting' )
			.addClass( 'hide' )
			.find( '.shipping-method-required-field' )
			.prop( 'required', false );
		settings
			.find( '.' + selectedMethod )
			.removeClass( 'hide' )
			.find( '.shipping-method-required-field' )
			.prop( 'required', $checkbox.prop( 'checked' ) );
	} ).find( '.wc-wizard-shipping-method-select .method' ).change();

	$( '.wc-wizard-services' ).on( 'change', '.wc-wizard-shipping-method-enable', function() {
		var checked = $( this ).is( ':checked' );
		var selectedMethod = $( '.wc-wizard-shipping-method-select .method' ).val();

		$( this )
			.closest( '.wc-wizard-service-item' )
			.find( '.' + selectedMethod )
			.find( '.shipping-method-required-field' )
			.prop( 'required', checked );
	} );

	function submitActivateForm() {
		$( 'form.activate-jetpack' ).submit();
	}

	function waitForJetpackInstall() {
		wp.ajax.post( 'setup_wizard_check_jetpack' )
			.then( function( result ) {
				// If we receive success, or an unexpected result
				// let the form submit.
				if (
					! result ||
					! result.is_active ||
					'yes' === result.is_active
				) {
					return submitActivateForm();
				}

				// Wait until checking the status again
				setTimeout( waitForJetpackInstall, 3000 );
			} )
			.fail( function() {
				// Submit the form as normal if the request fails
				submitActivateForm();
			} );
	}

	// Wait for a pending Jetpack install to finish before triggering a "save"
	// on the activate step, which launches the Jetpack connection flow.
	$( '.activate-jetpack' ).on( 'click', '.button-primary', function( e ) {
		blockWizardUI();

		if ( 'no' === wc_setup_params.pending_jetpack_install ) {
			return true;
		}

		e.preventDefault();
		waitForJetpackInstall();
	} );

	$( '.wc-wizard-services' ).on( 'change', 'input#stripe_create_account, input#ppec_paypal_reroute_requests', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.wc-wizard-service-settings' )
				.find( 'input.payment-email-input' )
				.attr( 'type', 'email' )
				.prop( 'disabled', false )
				.prop( 'required', true );
		} else {
			$( this ).closest( '.wc-wizard-service-settings' )
				.find( 'input.payment-email-input' )
				.attr( 'type', null )
				.prop( 'disabled', true )
				.prop( 'required', false );
		}
	} ).find( 'input#stripe_create_account, input#ppec_paypal_reroute_requests' ).change();

	function addPlugins( bySlug, $el, hover ) {
		var plugins = $el.data( 'plugins' );
		for ( var i in Array.isArray( plugins ) ? plugins : [] ) {
			var slug = plugins[ i ].slug;
			bySlug[ slug ] = bySlug[ slug ] ||
				$( '<span class="plugin-install-info-list-item">' )
					.append( '<a href="https://wordpress.org/plugins/' + slug + '/" target="_blank">' + plugins[ i ].name + '</a>' );

			bySlug[ slug ].find( 'a' )
				.on( 'mouseenter mouseleave', ( function( $hover, event ) {
					$hover.toggleClass( 'plugin-install-source', 'mouseenter' === event.type );
				} ).bind( null, hover ? $el.closest( hover ) : $el ) );
		}
	}

	function updatePluginInfo() {
		var pluginLinkBySlug = {};
		var extraPlugins = [];

		$( '.wc-wizard-service-enable input:checked' ).each( function() {
			addPlugins( pluginLinkBySlug, $( this ), '.wc-wizard-service-item' );

			var $container = $( this ).closest( '.wc-wizard-service-item' );
			$container.find( 'input.payment-checkbox-input:checked' ).each( function() {
				extraPlugins.push( $( this ).attr( 'id' ) );
				addPlugins( pluginLinkBySlug, $( this ), '.wc-wizard-service-settings' );
			} );
			$container.find( '.wc-wizard-shipping-method-select .method' ).each( function() {
				var $this = $( this );
				if ( 'live_rates' === $this.val()  ) {
					addPlugins( pluginLinkBySlug, $this, '.wc-wizard-service-item' );
				}
			} );
		} );

		$( '.recommended-item input:checked' ).each( function() {
			addPlugins( pluginLinkBySlug, $( this ), '.recommended-item' );
		} );

		var $list = $( 'span.plugin-install-info-list' ).empty();

		for ( var slug in pluginLinkBySlug ) {
			$list.append( pluginLinkBySlug[ slug ] );
		}

		if (
			extraPlugins &&
			wc_setup_params.current_step &&
			wc_setup_params.i18n.extra_plugins[ wc_setup_params.current_step ] &&
			wc_setup_params.i18n.extra_plugins[ wc_setup_params.current_step ][ extraPlugins.join( ',' ) ]
		) {
			$list.append(
				wc_setup_params.i18n.extra_plugins[ wc_setup_params.current_step ][ extraPlugins.join( ',' ) ]
			);
		}

		$( 'span.plugin-install-info' ).toggle( $list.children().length > 0 );
	}

	updatePluginInfo();
	$( '.wc-setup-content' ).on( 'change', '[data-plugins]', updatePluginInfo );

	$( document.body ).on( 'init_tooltips', function() {
		$( '.help_tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'defaultPosition': 'top'
		} );
	} ).trigger( 'init_tooltips' );
} );
