const { __ } = wp.i18n;
/**
 * Now let's get started.
 */
jQuery( document ).ready( function($) {

	var mediaUploader;

	// Handle adding a new avatar.
	$( 'tr.user-profile-picture' ).on( 'click', '#fair-avatar-upload', function(e) {
		e.preventDefault();

		// If the media frame already exists, reopen it.
		if ( mediaUploader ) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media({
			title: __( 'Choose Profile Picture', 'fair' ),
			button: {
				text: __( 'Use as Profile Picture', 'fair' )
			},
			library: {
				type: ['image']
			},
			multiple: false
		});

		mediaUploader.on( 'select', function() {
			var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();

			if ( attachment.type !== 'image' ) {
				return false;
			}

			var alttext = ! attachment.alt ? fairAvatars.defaultAlt : attachment.alt;

			// Add the value, swap the image, and show the button.
			$( '#fair-avatar-id' ).val( attachment.id );
			$( '.user-profile-picture td img.avatar' ).attr( { src: attachment.url, alt: alttext } );
			$( '#fair-avatar-remove' ).removeClass( 'button-hidden' );

			wp.a11y.speak( __( 'Profile Picture Assigned', 'fair' ) );
		});

		mediaUploader.open();
	});

	// Handle removing an existing avatar.
	$( 'tr.user-profile-picture' ).on( 'click', '#fair-avatar-remove', function(e) {
		e.preventDefault();

		// Remove the value, swap the image, hide the button, and set the focus.
		$( '#fair-avatar-id' ).val('');
		$( '.user-profile-picture td img.avatar' ).attr( { src: fairAvatars.defaultImg, alt: fairAvatars.defaultAlt } );
		$( this ).addClass( 'button-hidden' );
		$( '#fair-avatar-upload' ).trigger( 'focus' );

		wp.a11y.speak( __( 'Profile Picture Removed', 'fair' ) );
	});
});
