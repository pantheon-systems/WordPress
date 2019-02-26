function gtm4wp_track_downloads(track_extensions) {
	var gtm4wp_extensions_to_track = track_extensions.split(",");

	for ( var i = 0; i < gtm4wp_extensions_to_track.length; i++ ) {
		jQuery( "a[href$=\\." + gtm4wp_extensions_to_track[i].toLowerCase() + "], a[href$=\\." + gtm4wp_extensions_to_track[i].toUpperCase() + "]" )
			.on( "click", function() {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.downloadClick',
					'linkhref': jQuery( this ).attr( "href" )
				});
			})
			.attr( "target", "_blank" );
	} // end for i
}