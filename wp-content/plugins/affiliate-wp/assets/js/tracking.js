jQuery(document).ready( function($) {

	var ref_cookie = $.cookie( 'affwp_ref' );
	var visit_cookie = $.cookie( 'affwp_ref_visit_id' );
	var campaign_cookie = $.cookie( 'affwp_campaign' );
	var credit_last = AFFWP.referral_credit_last;

	/*
	 * Debug data
	 */

	 if ( 1 === AFFWP.debug ) {

		/**
		 * Helpful utilities and data for debugging the JavaScript environment.
		 *
		 * @type {prototype Object} AFFWP.debug_utility  An AFFWP.debug_utility object.
		 *
		 * @since 2.0
		 * @since 2.0.1  Return false instead of null.
		 * @var   item   The cookie item name.
		 */
		function affwp_get_cookie_item( item ) {
			var re    = new RegExp(item + "=([^;]+)");
			var value = re.exec(document.cookie);
			return (value != null) ? unescape(value[1]) : false;
		}

		// Short-circuiting, and saving a parse operation

		/**
		 * Checks whether a given value is an integer, with support for floating-point.
		 * For simple positive integer checks,
		 * use jQuery isNumeric instead (such as within tracking the ref below).
		 *
		 * @since  2.0
		 *
		 * @param  {int}     value  The value to check.
		 * @return {boolean}        True if value is an integer, otherwise false.
		 */
		function affwp_debug_is_int( value ) {
			var i;

			if ( isNaN( value ) ) {
				return false;
			}

			i = parseFloat( value );

			return ( i | 0 ) === i;
		}

		/**
		 * Assert two values are equal.
		 *
		 * @since  2.0
		 *
		 * @param  {mixed string|int} a  String or integer.
		 * @param  {mixed string|int} b  String or integer.
		 * @return void
		 */
		function affwp_debug_tests_assert_equals( a, b ) {
			if ( 1 !== AFFWP.debug ) {
				return false;
			}

			console.assert( a === b, 'AffiliateWP: Assertion failed, values not equal.' );
		}

		/**
		 * Assert two values are not equal.
		 *
		 * @since  2.0
		 *
		 * @param  {mixed string|int} a String or integer.
		 * @param  {mixed string|int} b String or integer.
		 *
		 * @return void
		 */
		function affwp_debug_tests_assert_not_equals( a, b ) {
			if ( 1 !== AFFWP.debug ) {
				return false;
			}

			console.assert( a !== b, 'AffiliateWP: Assertion failed, values are equal.' );
		}

		/**
		 * Asserts value a is greater than value b.
		 *
		 * @since  2.0
		 *
		 * @param  {int} a Integer.
		 * @param  {int} b Integer.
		 *
		 * @return void
		 */
		function affwp_debug_tests_assert_greater_than( a, b ) {
			if ( 1 !== AFFWP.debug || ! affwp_debug_is_int( a ) || ! affwp_debug_is_int( b ) ) {
				return false;
			}

			console.assert( a > b, 'AffiliateWP: Assertion failed, primary value is less than secondary value.' );
		}

		/**
		 * Asserts value a is less than value b.
		 *
		 * @since  2.0
		 *
		 * @param  {int} a Integer.
		 * @param  {int} b Integer.
		 *
		 * @return void
		 */
		function affwp_debug_tests_assert_less_than( a, b ) {
			if ( 1 !== AFFWP.debug || ! affwp_debug_is_int( a ) || ! affwp_debug_is_int( b ) ) {
				return false;
			}

			console.assert( a < b, 'AffiliateWP: Assertion failed, primary value is greater than secondary value.' );
		}

		/**
		 * Various data pertaining to AffiliateWP (if available).
		 *
		 * @since 2.0
		 * @return {array} An array of debug variables.
		 */
		function affwp_debug_inline_vars() {
			var vars = {
				ajax_url        : affwp_scripts.ajaxurl.length ? JSON.stringify( affwp_scripts.ajaxurl ) : 'unavailable',
				ref             : JSON.stringify( AFFWP.referral_var ? AFFWP.referral_var : affwp_get_cookie_item( 'affwp_ref' ) ),
				visit_cookie    : affwp_get_cookie_item( 'affwp_ref_visit_id' ) ? JSON.stringify( affwp_get_cookie_item( 'affwp_ref_visit_id' ) ) : 'unavailable',
				credit_last     : AFFWP.referral_credit_last ? JSON.stringify( AFFWP.referral_credit_last ) : 'unavailable',
				campaign        : JSON.stringify( affwp_get_query_vars()['campaign'] ? affwp_get_query_vars()['campaign'] : affwp_get_cookie_item( 'affwp_campaign' ) ),
				currency        : affwp_debug_vars.currency.length ? JSON.stringify( affwp_debug_vars.currency ) : 'unavailable',
				version         : affwp_debug_vars.version.length ? JSON.stringify( affwp_debug_vars.version ) : 'unavailable'
			}

			return vars;
		}

		/**
		 * Get all active integrations.
		 *
		 * @since  2.0
		 *
		 * @return {array} All active AffiliateWP integrations on the site.
		 */
		function affwp_debug_get_integrations() {

			if ( typeof affwp_debug_vars !== 'undefined' ) {
				return affwp_debug_vars.integrations;
			} else {
				return false;
			}

		}

		/**
		 * Returns the current time via the performance timing API.
		 *
		 * @since  2.0
		 * @return {int} The current browser client time.
		 */
		function affwp_debug_print_time() {
			return performance.now();
		}

		/**
		 * Halts all JavaScript at the given callable of AFFWP.debug_utility.halt().
		 *
		 * - Useful in implementing step-debuggers, and breakpoints.
		 *
		 * @since  2.0
		 *
		 * @param  {string} errorMessage Error message. Optional.
		 *
		 * @return void
		 */
		function affwp_debug_halt( errorMessage ) {
			if ( errorMessage ) {
				console.affwp( errorMessage );
				console.log( '\n' );
			}

			console.affwp( 'Halting at ' + affwp_debug_print_time() );

			debugger;
		}

		/**
		 * Outputs any available debug data
		 *
		 * @since  2.0
		 *
		 * @param  {string} heading    Optional title heading. Renders before the tabular data output.
		 * @param  {array}  debugData  Available debug data.
		 *
		 * @return void
		 */
		function affwp_debug_output() {

			console.affwp( 'The following data is provided from AffiliateWP debug mode. To disable this information, please deactivate debug mode in AffiliateWP.' );

			console.affwp( 'Available debug data: ' + '\n' + JSON.stringify(
				Object(
						affwp_debug_inline_vars()
					)
				)
			);

			console.affwp( 'Integrations' + '\n' + JSON.stringify(
				Object(
						affwp_debug_get_integrations()
					)
				)
			);
		}

		/**
		 * Defines styles for AffiliateWP-related console output.
		 *
		 * @since 2.0
		 * @see   console.affwp
		 */
		var affwpConsoleStyles = [
			'background: transparent'
			, 'border-bottom: 2px solid #E34F43'
			, 'color: black'
			, 'display: block'
			, 'line-height: 18px'
			, 'text-align: left'
			, 'margin: 4px'
			, 'font-weight: bold'
		].join( ';' );

		/**
		 * An extension of the console.log prototype.
		 *
		 * Usage:
		 * - Callable with `console.affwp( "The error or message" )`
		 * - Disambiguates the source of the error or message.
		 *
		 * @since  2.0
		 *
		 * @return void
		 */
		console.affwp = function(){
			Array.prototype.unshift.call(
			arguments, '%c' + ' * AffiliateWP: ', affwpConsoleStyles + ' *' );
			console.log.apply( console, arguments );
		};

		/**
		 * Print debug info to the console.
		 */
		affwp_debug_output();
	}

	/*
	 * End debug data
	 */

	if( '1' != credit_last && ref_cookie ) {
		return;
	}

	var ref = affwp_get_query_vars()[AFFWP.referral_var];
	var campaign = affwp_get_query_vars()['campaign'];

	if( typeof ref == 'undefined' || $.isFunction( ref ) ) {

		// See if we are using a pretty affiliate URL
		var path = window.location.pathname.split( '/' );
		$.each( path, function( key, value ) {
			if( AFFWP.referral_var == value ) {
				ref = path[ key + 1 ];
			}
		});

	}

	if( $.isFunction( ref ) ) {
		return;
	}

	if( typeof ref != 'undefined' && ! $.isNumeric( ref ) ) {

		// If a username was provided instead of an affiliate ID number, we need to retrieve the ID
		$.ajax({
			type: "POST",
			data: {
				action: 'affwp_get_affiliate_id',
				affiliate: ref
			},
			url: affwp_scripts.ajaxurl,
			success: function (response) {
				if( '1' == response.data.success ) {

					if( '1' == credit_last && ref_cookie && ref_cookie != response.data.affiliate_id ) {
						$.removeCookie( 'affwp_ref' );
					}

					if( ( '1' == credit_last && ref_cookie && ref_cookie != response.data.affiliate_id ) || ! ref_cookie ) {
						affwp_track_visit( response.data.affiliate_id, campaign );
					}

				}
			}

		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		});

	} else {

		// If a referral var is present and a referral cookie is not already set
		if( ref && ! ref_cookie ) {
			affwp_track_visit( ref, campaign );
		} else if( '1' == credit_last && ref && ref_cookie && ref !== ref_cookie ) {
			$.removeCookie( 'affwp_ref' );
			affwp_track_visit( ref, campaign );
		}

	}

	/**
	 * Tracks an affiliate visit.
	 *
	 * @since  1.0
	 *
	 * @param  {int}    affiliate_id The affiliate ID.
	 * @param  {string} url_campaign The campaign, if provided.
	 *
	 * @return void
	 */
	function affwp_track_visit( affiliate_id, url_campaign ) {

		// Set the cookie and expire it after 24 hours
		affwp_set_cookie( 'affwp_ref', affiliate_id );

		// Fire an ajax request to log the hit
		$.ajax({
			type: "POST",
			data: {
				action: 'affwp_track_visit',
				affiliate: affiliate_id,
				campaign: url_campaign,
				url: document.URL,
				referrer: document.referrer
			},
			url: affwp_scripts.ajaxurl,
			success: function (response) {
				affwp_set_cookie( 'affwp_ref_visit_id', response );
				affwp_set_cookie( 'affwp_campaign', url_campaign );
			}

		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		});

	}

	/**
	 * Set a cookie, with optional domain if set. Note that providing *any* domain will
	 * set the cookie domain with a leading dot, indicating it should be sent to sub-domains.
	 *
	 * example: host.tld
	 *
	 * - $.cookie( 'some_cookie', ...) = cookie domain: host.tld
	 * - $.cookie ('some_cookie', ... domain: 'host.tld' ) = .host.tld
	 *
	 * @since 2.1.10
	 *
	 * @param {string} name cookie name, e.g. affwp_ref
	 * @param {string} value cookie value
	 */
	function affwp_set_cookie( name, value ) {

		if ( 'cookie_domain' in AFFWP ) {
			$.cookie( name, value, { expires: AFFWP.expiration, path: '/', domain: AFFWP.cookie_domain } );
		} else {
			$.cookie( name, value, { expires: AFFWP.expiration, path: '/' } );
		}
	}

	/**
	 * Gets url query variables from the current URL.
	 *
	 * @since  1.0
	 *
	 * @return {array} vars The url query variables in the current site url, if present.
	 */
	function affwp_get_query_vars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);

			var key = typeof hash[1] == 'undefined' ? 0 : 1;

			// Remove fragment identifiers
			var n = hash[key].indexOf('#');
			hash[key] = hash[key].substring(0, n != -1 ? n : hash[key].length);
			vars[hash[0]] = hash[key];
		}
		return vars;
	}

});
