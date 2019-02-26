jQuery( function() {
	jQuery( "a[href^=mailto]" )
		.on( "click", function() {
			var gtm4wp_linkparts = jQuery( this ).attr( "href" ).split( ":" );

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.emailClick',
				'linkhref': gtm4wp_linkparts
			});
		})
		.attr( "target", "_blank" );
});