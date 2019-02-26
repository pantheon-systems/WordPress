jQuery( function() {
	jQuery( ".wpcf7" )
		.on( 'wpcf7mailsent', function( e ) {
			var gtm4wp_cf7formid = '(not set)';
			if ( e && e.detail && e.detail.contactFormId ) {
				gtm4wp_cf7formid = e.detail.contactFormId;
			} else if ( e && e.originalEvent && e.originalEvent.detail && e.originalEvent.detail.contactFormId ) {
				gtm4wp_cf7formid = e.originalEvent.detail.contactFormId;
			}

			var gtm4wp_cf7forminputs = [];
			if ( e && e.detail && e.detail.inputs ) {
				gtm4wp_cf7forminputs = e.detail.inputs;
			} else if ( e && e.originalEvent && e.originalEvent.detail && e.originalEvent.detail.inputs ) {
				gtm4wp_cf7forminputs = e.originalEvent.detail.inputs;
			}

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.contactForm7Submitted',
				'gtm4wp.cf7formid': gtm4wp_cf7formid,
				'gtm4wp.cf7inputs': gtm4wp_cf7forminputs
			});
		});
});