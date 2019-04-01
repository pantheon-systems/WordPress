var gtm4wp_vimeo_percentage_tracking = 10;
var gtm4wp_vimeo_percentage_tracking_marks = {};

jQuery(function() {
	jQuery( '[id^="vimeoplayer_"]' ).each(function() {
		var vimeoapi = $f( this ),
				jqframe  = jQuery( this ),
				videourl = jqframe
					.attr( "src" )
					.split( "?" )
					.shift(),
				videoid = videourl.split( "/" ).pop();

		jqframe.attr( "data-player_id", videoid );
		jqframe.attr( "data-player_url", videourl );

		vimeoapi.addEvent( 'ready', function( player_id ) {
			vimeoapi.api( 'getDuration', function( value, player_id ) {

				jqframe.attr( "data-player_duration", value );

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerReady',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "title" ),
						'url': videourl,
						'duration': value
					},
					'mediaCurrentTime': 0
				});
			}); // end of api call getDuration

			vimeoapi.addEvent( 'playProgress', function( value, player_id ) {
			  gtm4wp_onVimeoPercentageChange( value );
			});

			vimeoapi.addEvent( 'play', function( player_id ) {
				gtm4wp_onVimeoPlayerStateChange( 'play' );
			});

			vimeoapi.addEvent( 'pause', function( player_id ) {
				gtm4wp_onVimeoPlayerStateChange( 'pause' );
			});

			vimeoapi.addEvent( 'finish', function( player_id ) {
				gtm4wp_onVimeoPlayerStateChange( 'finish' );
			});

			vimeoapi.addEvent( 'seek', function( value, player_id ) {
				gtm4wp_onVimeoPlayerStateChange( 'seek' );
			});

			var gtm4wp_onVimeoPlayerStateChange = function( player_state ) {
				vimeoapi.api( 'getCurrentTime', function( value, player_id ) {
					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.mediaPlayerStateChange',
						'mediaType': 'vimeo',
						'mediaData': {
							'id': videoid,
							'author': '',
							'title': jqframe.attr( "title" ),
							'url': jqframe.attr( "data-player_url" ),
							'duration': parseInt( jqframe.attr( "data-player_duration" ) )
						},
						'mediaPlayerState': player_state,
						'mediaCurrentTime': value
					});
				});
			};

			var gtm4wp_onVimeoPercentageChange = function( data ) {
				var videoDuration   = parseInt( jqframe.attr( "data-player_duration" ) );
				var videoPercentage = Math.floor( data.seconds / videoDuration * 100 );

				if ( typeof gtm4wp_vimeo_percentage_tracking_marks[ videoid ] == "undefined" ) {
					gtm4wp_vimeo_percentage_tracking_marks[ videoid ] = [];
				}

				for( var i=0; i<100; i+=gtm4wp_vimeo_percentage_tracking ) {
					if ( ( videoPercentage > i ) && ( gtm4wp_vimeo_percentage_tracking_marks[ videoid ].indexOf( i ) == -1 ) ) {
						gtm4wp_vimeo_percentage_tracking_marks[ videoid ].push( i );

						window[ gtm4wp_datalayer_name ].push({
							'event': 'gtm4wp.mediaPlaybackPercentage',
							'mediaType': 'vimeo',
							'mediaData': {
								'id': videoid,
								'author': '',
								'title': jqframe.attr( "title" ),
								'url': jqframe.attr( "data-player_url" ),
								'duration': videoDuration
							},
							'mediaCurrentTime': data.seconds,
							'mediaPercentage': i
						});
					}
				}
			};

		});
	});
});