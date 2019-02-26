jQuery(document).ready( function($) {

	// datepicker
	if( $('.affwp-datepicker').length ) {
		$('.affwp-datepicker').datepicker({dateFormat: 'mm/dd/yy'});
	}

	// Remove whitespace from the campaign name.
	$( '#affwp-campaign' ).on( 'focusout', function( event ) {
		$( this ).val( $( this ).val().replace( /\s/g, '' ) );
	} );

	$( '#affwp-generate-ref-url' ).submit( function() {

		var url                 = $( this ).find( '#affwp-url' ).val(),
		    campaign            = $( this ).find( '#affwp-campaign' ).val(),
		    refVar              = $( this ).find( 'input[type="hidden"].affwp-referral-var' ).val(),
		    affId               = $( this ).find( 'input[type="hidden"].affwp-affiliate-id' ).val(),
		    prettyAffiliateUrls = affwp_vars.pretty_affiliate_urls,
		    add                 = '';

		// Strip any whitespace from the beginning or end of the URL.
		url = url.trim();

		// URL has fragment
		if ( url.indexOf( '#' ) > 0 ) {
			var fragment = url.split('#');
		}

		// if fragment, remove it, we'll append it later
		if ( fragment ) {
			url = fragment[0];
		}

		if ( prettyAffiliateUrls ) {
			// pretty affiliate URLs

			if ( url.indexOf( '?' ) < 0 ) {
				// no query strings

				// add trailing slash if missing
				if ( ! url.match( /\/$/ ) ) {
				    url += '/';
				}

				if( campaign.length ) {

					campaign = '?campaign=' + campaign;

				}

			} else {
				// has query strings

				// split query string at first occurrence of ?
				var pieces = url.split('?');

				// set URL back to first piece
				url = pieces[0];

				// add trailing slash if missing
				if ( ! url.match( /\/$/ ) ) {
				    url += '/';
				}

				// add any query strings to the end
				add = '/?' + pieces[1];

				if( campaign.length ) {

					campaign = '&campaign=' + campaign;

				}

			}

			// build URL
			url = url + refVar + '/' + affId + '/' + add + campaign;

		} else {

			// non-pretty URLs

			if( campaign.length ) {

				campaign = '&campaign=' + campaign;

			}


			if ( url.indexOf( '?' ) < 0 ) {

				// add trailing slash if missing
				if ( ! url.match( /\/$/ ) ) {
				    url += '/';
				}

			} else {

				// split query string at first occurrence of ?
				var pieces = url.split('?');

				// set url back to first piece
				url = pieces[0];

				// add trailing slash if missing
				if ( ! url.match( /\/$/ ) ) {
				    url += '/';
				}

				// add any query strings to the end
				add = '&' + pieces[1];

			}

			// build URL
			url = url + '?' + refVar + '=' + affId + add + campaign;

		}

		// if there's a fragment, add it to the end of the URL
		if ( fragment) {
			url += '#' + fragment[1];
		}

		// clean URL to remove any instances of multiple slashes
		url = url.replace(/([^:])(\/\/+)/g, '$1/');

		// encode any spaces in the URL
		url = url.replace(/ /g, '%20');

		if( affwp_is_valid_url( url ) ) {

			$( '.affwp-wrap.affwp-base-url-wrap' ).find( '.affwp-errors' ).remove();
			$( this ).find( '.affwp-referral-url-wrap' ).slideDown();
			$( this ).find( '#affwp-referral-url' ).val( url ).focus();

		} else {

			$( '.affwp-wrap.affwp-base-url-wrap' ).find( '.affwp-errors' ).remove();
			$( '.affwp-wrap.affwp-base-url-wrap' ).append( '<div class="affwp-errors"><p class="affwp-error">' + affwp_vars.invalid_url + '</p></div>' );

		}

		return false;
	});

});

function affwp_is_valid_url(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}