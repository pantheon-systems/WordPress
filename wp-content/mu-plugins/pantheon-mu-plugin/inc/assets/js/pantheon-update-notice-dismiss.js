/**
 * Persist dismissal of the Pantheon update notice.
 *
 * WordPress's native `is-dismissible` handler only hides the notice in the DOM;
 * it makes no server call, so the notice returns on the next page load. This
 * listens for the same dismiss click and records it server-side (per user, keyed
 * to the current WordPress version) so the notice stays dismissed until a newer
 * version is available.
 */
/* global pantheonUpdateNotice */
( function () {
	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.notice-dismiss' );
		if ( ! button ) {
			return;
		}

		if ( ! button.closest( '#pantheon-update-notice' ) ) {
			return;
		}

		var data = new FormData();
		data.append( 'action', pantheonUpdateNotice.action );
		data.append( 'nonce', pantheonUpdateNotice.nonce );

		fetch( pantheonUpdateNotice.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} );
	} );
}() );
