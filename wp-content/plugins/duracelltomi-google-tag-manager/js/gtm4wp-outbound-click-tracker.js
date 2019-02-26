jQuery( function() {
	var gtm4wp_localdomain = window.location.hostname.replace( "www.", "" );

	jQuery( "a[href^=http]" )
		.each( function() {
			var gtm4wp_linkhref = jQuery( this ).attr( "href" );

			if ( gtm4wp_linkhref.indexOf( gtm4wp_localdomain ) == -1 ) {
				jQuery( this )
					.on( "click", function() {
						window[ gtm4wp_datalayer_name ].push({
							'event': 'gtm4wp.outboundClick',
							'linkhref': jQuery( this ).attr( "href" )
						});
					})
					.attr( "target", "_blank" );
			}
		});
});