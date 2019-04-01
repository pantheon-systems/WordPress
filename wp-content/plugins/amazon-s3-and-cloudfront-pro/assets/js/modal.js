var as3cfModal = (function( $ ) {

	var modal = {
		prefix: 'as3cf',
		loading: false,
		dismissible: true
	};

	var modals = {};

	/**
	 * Target to key
	 *
	 * @param {string} target
	 *
	 * @return {string}
	 */
	function targetToKey( target ) {
		return target.replace( /[^a-z]/g, '' );
	}

	/**
	 * Check if modal exists in DOM or in Memory.
	 *
	 * @param {string} target
	 *
	 * @return {boolean}
	 */
	modal.exists = function( target ) {
		var key = targetToKey( target );

		if ( undefined !== modals[ key ] ) {
			return true;
		}

		if ( $( target ).length ) {
			return true;
		}

		return false;
	};

	/**
	 * Open modal
	 *
	 * @param {string}   target
	 * @param {function} callback
	 * @param {string}   customClass
	 */
	modal.open = function( target, callback, customClass ) {
		var key = targetToKey( target );

		// Overlay
		$( 'body' ).append( '<div id="as3cf-overlay"></div>' );
		var $overlay = $( '#as3cf-overlay' );

		// Modal container
		if ( modal.dismissible ) {
			$overlay.append( '<div id="as3cf-modal"><span class="close-as3cf-modal">×</span></div>' );
		} else {
			$overlay.append( '<div id="as3cf-modal"></div>' );
		}

		var $modal = $( '#as3cf-modal' );

		if ( undefined === modals[ key ] ) {
			var content = $( target );
			modals[ key ] = content.clone( true ).css( 'display', 'block' );
			content.remove();
		}
		$modal.data( 'as3cf-modal-target', target ).append( modals[ key ] );

		if ( undefined !== customClass ) {
			$modal.addClass( customClass );
		}

		if ( 'function' === typeof callback ) {
			callback( target );
		}

		// Handle modals taller than window height,
		// overflow & padding-right remove duplicate scrollbars.
		$( 'body' ).addClass( 'as3cf-modal-open' );

		$overlay.fadeIn( 150 );
		$modal.fadeIn( 150 );

		$( 'body' ).trigger( 'as3cf-modal-open', [ target ] );
	};

	/**
	 * Close modal
	 *
	 * @param {function} callback
	 */
	modal.close = function( callback ) {
		if ( modal.loading || ! modal.dismissible ) {
			return;
		}

		var target = $( '#as3cf-modal' ).data( 'as3cf-modal-target' );

		$( '#as3cf-overlay' ).fadeOut( 150, function() {
			$( 'body' ).removeClass( 'as3cf-modal-open' );

			$( this ).remove();

			if ( 'function' === typeof callback ) {
				callback( target );
			}
		} );

		$( 'body' ).trigger( 'as3cf-modal-close', [ target ] );
	};

	/**
	 * Set loading state
	 *
	 * @param {boolean} state
	 */
	modal.setLoadingState = function( state ) {
		modal.loading = state;
	};

	/**
	 * Set dismissible state.
	 *
	 * @param {boolean} state
	 */
	modal.setDismissibleState = function( state ) {
		modal.dismissible = state;
	};

	// Setup click handlers
	$( document ).ready( function() {

		$( 'body' ).on( 'click', '[data-as3cf-modal]', function( e ) {
			e.preventDefault();
			modal.open( $( this ).data( 'as3cf-modal' ) + '.' + modal.prefix );
		} );

		$( 'body' ).on( 'click', '#as3cf-overlay, .close-as3cf-modal', function( e ) {
			if ( 'A' === e.target.tagName ) {
				return;
			}

			e.preventDefault();

			// Don't allow children to bubble up click event
			if ( e.target !== this ) {
				return false;
			}

			modal.close();
		} );

	} );

	return modal;

})( jQuery );
