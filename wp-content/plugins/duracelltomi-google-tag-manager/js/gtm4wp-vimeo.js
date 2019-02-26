var gtm4wp_vimeo_percentage_tracking = 10;
var gtm4wp_vimeo_percentage_tracking_marks = {};

jQuery(function() {
	jQuery( 'iframe[src*="vimeo.com"]' ).each(function() {
		var vimeoapi = new Vimeo.Player( this ),
				jqframe  = jQuery( this ),
				videourl = jqframe
					.attr( "src" )
					.split( "?" )
					.shift(),
				videoid = videourl.split( "/" ).pop();

		jqframe.attr( "data-player_id", videoid );
		jqframe.attr( "data-player_url", videourl );

		vimeoapi.getVideoTitle().then( function( title ) {
			jqframe.attr( "data-player_title", title );

			vimeoapi.getDuration().then( function( duration ) {

				jqframe.attr( "data-player_duration", duration );

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerReady',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "data-player_title" ),
						'url': videourl,
						'duration': duration
					},
					'mediaCurrentTime': 0
				});

			}).catch( function( error ) {

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerEvent',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "data-player_title" ),
						'url': videourl,
						'duration': 0
					},
					'mediaCurrentTime': 0,
					'mediaPlayerEvent': 'error',
					'mediaPlayerEventParam': error
				});

			}); // end of api call getDuration

		}).catch( function( error ) {

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.mediaPlayerEvent',
				'mediaType': 'vimeo',
				'mediaData': {
					'id': videoid,
					'author': '',
					'title': "Unknown title",
					'url': videourl,
					'duration': 0
				},
				'mediaCurrentTime': 0,
				'mediaPlayerEvent': 'error',
				'mediaPlayerEventParam': error
			});

		}); // end of api call getVideoTitle

		vimeoapi.on( 'play', function( data ) {
			gtm4wp_onVimeoPlayerStateChange( 'play', data );
		});

		vimeoapi.on( 'pause', function( data ) {
			gtm4wp_onVimeoPlayerStateChange( 'pause', data );
		});

		vimeoapi.on( 'ended', function( data ) {
			gtm4wp_onVimeoPlayerStateChange( 'ended', data );
		});

		vimeoapi.on( 'seeked', function( data ) {
			gtm4wp_onVimeoPlayerStateChange( 'seeked', data );
		});

		vimeoapi.on( 'texttrackchange', function( data ) {

			vimeoapi.getCurrentTime().then( function( seconds ) {

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerEvent',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "data-player_title" ),
						'url': jqframe.attr( "data-player_url" ),
						'duration': jqframe.attr( "data-player_duration" )
					},
					'mediaPlayerEvent': 'texttrackchange',
					'mediaPlayerEventParam': data,
					'mediaCurrentTime': seconds

				}).catch( function( error ) {

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.mediaPlayerEvent',
						'mediaType': 'vimeo',
						'mediaData': {
							'id': videoid,
							'author': '',
							'title': "Unknown title",
							'url': videourl,
							'duration': jqframe.attr( "data-player_duration" )
						},
						'mediaCurrentTime': 0,
						'mediaPlayerEvent': 'error',
						'mediaPlayerEventParam': error
					});

				}); // end call api getCurrentTime()

			});

		});

		vimeoapi.on( 'volumechange', function( data ) {

			vimeoapi.getCurrentTime().then( function( seconds ) {

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerEvent',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "data-player_title" ),
						'url': jqframe.attr( "data-player_url" ),
						'duration': jqframe.attr( "data-player_duration" )
					},
					'mediaPlayerEvent': 'volumechange',
					'mediaPlayerEventParam': data.volume,
					'mediaCurrentTime': seconds

				}).catch( function( error ) {

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.mediaPlayerEvent',
						'mediaType': 'vimeo',
						'mediaData': {
							'id': videoid,
							'author': '',
							'title': "Unknown title",
							'url': videourl,
							'duration': jqframe.attr( "data-player_duration" )
						},
						'mediaCurrentTime': 0,
						'mediaPlayerEvent': 'error',
						'mediaPlayerEventParam': error
					});

				}); // end call api getCurrentTime()

			});

		});

		vimeoapi.on( 'error', function( data ) {

			vimeoapi.getCurrentTime().then( function( seconds ) {

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerEvent',
					'mediaType': 'vimeo',
					'mediaData': {
						'id': videoid,
						'author': '',
						'title': jqframe.attr( "data-player_title" ),
						'url': jqframe.attr( "data-player_url" ),
						'duration': jqframe.attr( "data-player_duration" )
					},
					'mediaPlayerEvent': 'error',
					'mediaPlayerEventParam': data,
					'mediaCurrentTime': seconds

				}).catch( function( error ) {

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.mediaPlayerEvent',
						'mediaType': 'vimeo',
						'mediaData': {
							'id': videoid,
							'author': '',
							'title': "Unknown title",
							'url': videourl,
							'duration': jqframe.attr( "data-player_duration" )
						},
						'mediaCurrentTime': 0,
						'mediaPlayerEvent': 'error',
						'mediaPlayerEventParam': error
					});

				}); // end call api getCurrentTime()

			});
		});

		vimeoapi.on( 'timeupdate', function( data ) {
			gtm4wp_onVimeoPercentageChange( data );
		});

		var gtm4wp_onVimeoPlayerStateChange = function( player_state, data ) {

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.mediaPlayerStateChange',
				'mediaType': 'vimeo',
				'mediaData': {
					'id': videoid,
					'author': '',
					'title': jqframe.attr( "data-player_title" ),
					'url': jqframe.attr( "data-player_url" ),
					'duration': data.duration
				},
				'mediaPlayerState': player_state,
				'mediaCurrentTime': data.seconds
			});

		};

		var gtm4wp_onVimeoPercentageChange = function( data ) {

			var videoDuration   = data.duration;
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
							'title': jqframe.attr( "data-player_title" ),
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