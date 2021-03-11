/* global Color */
( function() {

	// Variable for some backgrounds
	var alphaImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==';

	/**
	 * Overwrite Color to enable support for rbga colors.
	 */
	Color.fn.toString = function() {
		var hex,
		    i;

		if ( this._alpha < 1 ) {
			return this.toCSS( 'rgba', this._alpha ).replace( /\s+/g, '' );
		}

		hex = parseInt( this._color, 10 ).toString( 16 );

		if ( this.error ) {
			return '';
		}

		if ( hex.length < 6 ) {
			for ( i = 6 - hex.length - 1; i >= 0; i-- ) {
				hex = '0' + hex;
			}
		}

		return '#' + hex;
	};

	/**
	 * Overwrite iris
	 */
	jQuery.widget( 'a8c.iris', jQuery.a8c.iris, {
		_create: function() {
			this._super();

			// Global option for check is mode rbga is enabled
			this.options.alpha = this.element.data( 'alpha' ) || false;

			// Is not input disabled
			if ( ! this.element.is( ':input' ) ) {
				this.options.alpha = false;
			}

			if ( typeof this.options.alpha !== 'undefined' && this.options.alpha ) {
				var self       = this,
				    _html      = '<div class="iris-strip iris-slider iris-alpha-slider"><div class="iris-slider-offset iris-slider-offset-alpha"></div></div>',
				    aContainer = jQuery( _html ).appendTo( self.picker.find( '.iris-picker-inner' ) ),
				    aSlider    = aContainer.find( '.iris-slider-offset-alpha' ),
				    controls   = {
						aContainer: aContainer,
						aSlider: aSlider
				    };

				jQuery( self.picker ).parents( '.wp-picker-container' ).addClass( 'wp-picker-alpha-container' );

				self.options.customWidth = 100;
				if ( 'undefined' !== typeof self.element.data( 'custom-width' ) ) {
					self.options.customWidth = parseInt( self.element.data( 'custom-width' ), 10 ) || 0;
				}

				// Set default width for input reset
				self.options.defaultWidth = self.element.width();

				// Update width for input
				if ( self._color._alpha < 1 || self._color.toString().indexOf( 'rgb' ) !== 1 ) {
					self.element.width( parseInt( self.options.defaultWidth + self.options.customWidth, 10 ) );
				}

				// Push new controls
				jQuery.each( controls, function( k, v ) {
					self.controls[ k ] = v;
				});

				// Change size strip and add margin for sliders
				self.controls.square.css({ 'margin-right': '0' });
				var emptyWidth   = ( self.picker.width() - self.controls.square.width() - 20 ),
				    stripsMargin = emptyWidth / 6,
				    stripsWidth  = ( emptyWidth / 2 ) - stripsMargin;

				jQuery.each( [ 'aContainer', 'strip' ], function( k, v ) {
					self.controls[ v ].width( stripsWidth ).css({ 'margin-left': stripsMargin + 'px' });
				});

				// Add new slider
				self._initControls();

				// For updated widget
				self._change();
			}
		},

		_initControls: function() {
			this._super();

			if ( this.options.alpha ) {
				var self     = this,
				    controls = self.controls;

				controls.aSlider.slider({
					orientation: 'vertical',
					min: 0,
					max: 100,
					step: 1,
					value: parseInt( self._color._alpha * 100, 10 ),
					slide: function( event, ui ) {

						// Update alpha value
						self._color._alpha = parseFloat( ui.value / 100 );
						self._change.apply( self, arguments );
					}
				});
			}
		},

		_change: function() {
			this._super();
			var self = this,
			    reset;

			if ( this.options.alpha ) {
				var controls     = self.controls,
				    alpha        = parseInt( self._color._alpha * 100, 10 ),
				    color        = self._color.toRgb(),
				    defaultWidth = self.options.defaultWidth,
				    customWidth  = self.options.customWidth,
				    target       = self.picker.closest( '.wp-picker-container' ).find( '.wp-color-result' ),
				    gradient     = [
						'rgb(' + color.r + ',' + color.g + ',' + color.b + ') 0%',
						'rgba(' + color.r + ',' + color.g + ',' + color.b + ', 0) 100%'
				    ];

				// Generate background slider alpha, only for CSS3 old browser fuck!! :)
				controls.aContainer.css({ 'background': 'linear-gradient(to bottom, ' + gradient.join( ', ' ) + '), url(' + alphaImage + ')' });

				if ( target.hasClass( 'wp-picker-open' ) ) {
					// Update alpha value
					controls.aSlider.slider( 'value', alpha );

					/**
					 * Disabled change opacity in default slider Saturation ( only is alpha enabled )
					 * and change input width for view all value
					 */
					if ( self._color._alpha < 1 ) {
						var style = controls.strip.attr( 'style' ).replace( /rgba\(([0-9]+,)(\s+)?([0-9]+,)(\s+)?([0-9]+)(,(\s+)?[0-9\.]+)\)/g, 'rgb($1$3$5)' );

						controls.strip.attr( 'style', style );

						self.element.width( parseInt( defaultWidth + customWidth, 10 ) );
					} else {
						self.element.width( defaultWidth );
					}
				}
			}

			reset = self.element.data( 'reset-alpha' ) || false;
			if ( reset ) {
				self.picker.find( '.iris-palette-container' ).on( 'click.palette', '.iris-palette', function() {
					self._color._alpha = 1;
					self.active = 'external';
					self._change();
				});
			}
		},

		_addInputListeners: function( input ) {
			var self            = this,
			    debounceTimeout = 100,
			    callback;

			callback = function( event ) {
				var color = new Color( input.val() ),
				    val   = input.val();

				input.removeClass( 'iris-error' );

				// We gave a bad color.
				if ( color.error ) {

					// Don't error on an empty input.
					if ( '' !== val ) {
						input.addClass( 'iris-error' );
					}
				} else if ( color.toString() !== self._color.toString() ) {

					// Let's not do this on keyup for hex shortcodes.
					if ( ! ( event.type === 'keyup' && val.match( /^[0-9a-fA-F]{3}$/ ) ) ) {
						self._setOption( 'color', color.toString() );
					}
				}
			};

			input.on( 'change', callback ).on( 'keyup', self._debounce( callback, debounceTimeout ) );

			// If we initialized hidden, show on first focus. The rest is up to you.
			if ( self.options.hide ) {
				input.one( 'focus', function() {
					self.show();
				});
			}
		},

		_addPalettes: function() {
			var container = jQuery( '<div class="iris-palette-container" />' ),
			    palette   = jQuery( '<a class="iris-palette" tabindex="0" />' ),
			    colors    = ['#000000', '#ffffff', '#f44336', '#E91E63', '#03A9F4', '#00BCD4', '#8BC34A', '#FFEB3B', '#FFC107', '#FF9800', '#607D8B'];

			// Do we have an existing container? Empty and reuse it.
			if ( this.picker.find( '.iris-palette-container' ).length ) {
				container = this.picker.find( '.iris-palette-container' ).detach().html( '' );
			}

			jQuery.each( colors, function( index, val ) {
				palette.clone().data( 'color', val )
					.css( 'backgroundColor', val ).appendTo( container )
					.height( 10 ).width( 10 );
			});

			this.picker.append( container );
		}
	} );
}( jQuery ) );

jQuery( document ).ready( function( $ ) {

	// Fix Safari issue on input click
	$( '.butterbean-color-picker, .wp-picker-clear, .iris-picker' ).on( 'click', function( e ) {
		e.preventDefault();
	} );
	
});