( function() {

    /**
	 * Adds the select control view.
	 */
	butterbean.views.register_control( 'select', {

        ready : function() {

            jQuery( this.$el ).find( '.butterbean-select' ).owpSelect2( {
                minimumResultsForSearch: 10,
                dropdownCssClass: 'oceanwp-select2',
                width: '100%'
            } );
        }

    } );

    /**
	 * Adds the multiple select control view.
	 */
	butterbean.views.register_control( 'multiple-select', {

		ready : function() {

			jQuery( this.$el ).find( '.butterbean-multiple-select' ).owpSelect2( {
				dropdownCssClass: 'oceanwp-select2',
				width: '100%'
			} );
		}
	} );

    /**
	 * Adds the typo control view.
	 */
	butterbean.views.register_control( 'typography', {

        ready : function() {

            jQuery( this.$el ).find( '.butterbean-select' ).owpSelect2( {
                minimumResultsForSearch: 10,
                dropdownCssClass: 'oceanwp-select2',
                width: '100%'
            } );
        }

    } );

    /**
	 * Adds the rgba color control view.
	 */
	butterbean.views.register_control( 'rgba-color', {

		ready : function() {

			var options = this.model.attributes.options;

			jQuery( this.$el ).find( '.butterbean-color-picker' ).wpColorPicker( options );
		}
	} );

	/**
	 * Adds the range control view.
	 */
	butterbean.views.register_control( 'range', {

		ready: function() {

			// Update the text value
			jQuery( 'input[type=range]' ).on( 'mousedown', function() {

				range 			= jQuery( this );
				range_input 	= range.parent().children( '.oceanwp-range-input' );
				value 			= range.attr( 'value' );

				range_input.val( value );

				range.mousemove( function() {
					value = range.attr( 'value' );
					range_input.val( value );
				} );

			} );

			var oceanwp_range_input_number_timeout;

			function oceanwp_autocorrect_range_input_number( input_number, timeout ) {

				var range_input 	= input_number,
					range 			= range_input.parent().find( 'input[type="range"]' ),
					value 			= parseFloat( range_input.val() ),
					reset 			= parseFloat( range.attr( 'data-reset_value' ) ),
					step 			= parseFloat( range_input.attr( 'step' ) ),
					min 			= parseFloat( range_input.attr( 'min') ),
					max 			= parseFloat( range_input.attr( 'max') );

				clearTimeout( oceanwp_range_input_number_timeout );

				oceanwp_range_input_number_timeout = setTimeout( function() {

					if ( isNaN( value ) ) {
						range_input.val( reset );
						range.val( reset ).trigger( 'change' );
						return;
					}

					if ( step >= 1 && value % 1 !== 0 ) {
						value = Math.round( value );
						range_input.val( value );
						range.val( value );
					}

					if ( value > max ) {
						range_input.val( max );
						range.val( max ).trigger( 'change' );
					}

					if ( value < min ) {
						range_input.val( min );
						range.val( min ).trigger( 'change' );
					}

				}, timeout );

				range.val( value ).trigger( 'change' );

			}

			// Change the text value
			jQuery( 'input.oceanwp-range-input' ).on( 'change keyup', function() {

				oceanwp_autocorrect_range_input_number( jQuery( this ), 1000);

			} ).on( 'focusout', function() {

				oceanwp_autocorrect_range_input_number( jQuery( this ), 0);

			} );

			// Handle the reset button
			jQuery( '.oceanwp-reset-slider' ).on('click', function() {

				this_input 		= jQuery( this ).parent().find( 'input' );
				input_default 	= this_input.data( 'reset_value' );

				this_input.val( input_default );
				this_input.change();

			} );
		}
	} );

	/**
	 * Adds the media control view.
	 */
	butterbean.views.register_control( 'media', {

		// Adds custom events.
		events : {
			'click .oceanwp-add-media' 	  : 'showmodal',
		},

		// Executed when the show modal button is clicked.
		showmodal : function() {

			// If we already have a media modal, open it.
			if ( ! _.isUndefined( this.media_modal ) ) {

				this.media_modal.open();
				return;
			}

			// Create a new media modal.
			this.media_modal = wp.media( {
				frame    : 'select',
				multiple : false,
				editing  : true,
			} );

			// Runs when an media is selected in the media modal.
			this.media_modal.on( 'select', function() {

				// Gets the JSON data for the first selection.
				var media = this.media_modal.state().get( 'selection' ).first().toJSON();

				// Updates the model for the view.
				this.model.set( {
					value : media.url
				} );
			}, this );

			// Opens the media modal.
			this.media_modal.open();
		},
	} );

	/**
	 * Adds the editor control view.
	 */
	butterbean.views.register_control( 'editor', {

		ready : function() {

			if ( typeof tinyMCE !== "undefined" ) {
                tinyMCE.execCommand( 'mceAddEditor', true, this.model.get( 'field_name' ) );
            }
		}
	} );
    
}() );