var $j = jQuery.noConflict();

$j(document).ready(function(e) {

	// Switcher buttons
	(function () {
		// Cache selector to all items
		var $items 				= $j( '.oceanwp-modules .modules-inner' ).find( '.column-wrap' ),
			fadeoutClass 		= 'is-fadeout',
			fadeinClass 		= 'is-fadein',
			animationDuration 	= 200;

		// Hide all items.
		var fadeOut = function () {
			var dfd = jQuery.Deferred();

			$items.addClass( fadeoutClass );

			setTimeout( function() {
				$items.removeClass( fadeoutClass ).hide();

				dfd.resolve();
			}, animationDuration );

			return dfd.promise();
		};

		var fadeIn = function ( type, dfd ) {
			var filter = type ? '[data-type*="' + type + '"]' : 'div';

			if ( 'all' === type ) {
				filter = 'div';
			}

			$items.filter( filter ).show().addClass( 'is-fadein' );

			setTimeout( function() {
				$items.removeClass( fadeinClass );

				dfd.resolve();
			}, animationDuration );
		};

		var animate = function ( type ) {
			var dfd = jQuery.Deferred();

			var promise = fadeOut();

			promise.done( function () {
				fadeIn( type, dfd );
			} );

			return dfd;
		};

		$j( '.oceanwp-modules .btn-switcher li a' ).on( 'click', function( event ) {
			event.preventDefault();

			// Remove 'active' class from the previous nav list items.
			$j( this ).parent().siblings().removeClass( 'active' );

			// Add the 'active' class to this nav list item.
			$j( this ).parent().addClass( 'active' );

			var type = this.hash.slice(1);

			// show/hide the right items, based on type selected
			var promise = animate( type );

			promise.done();
		} );

		$j( document ).on( 'click', '#owp-switch-all', function() {
			if ( $j( this ).is( ':checked' ) ) {
			    $j( this ).closest( '.oceanwp-modules' ).find( 'input.owp-checkbox' ).prop( 'checked', true );
			} else {
			    $j( this ).closest( '.oceanwp-modules' ).find( 'input.owp-checkbox' ).prop( 'checked', false );
			}
		} );
	}());

} );
