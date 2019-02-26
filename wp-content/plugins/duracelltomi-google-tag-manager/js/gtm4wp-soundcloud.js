var gtm4wp_soundclound_percentage_tracking = 10;
var gtm4wp_soundclound_percentage_tracking_marks = {};

jQuery(function() {
//	jQuery( '[id^="soundcloudplayer_"]' ).each(function() {
	jQuery( 'iframe[src*="soundcloud.com"]' ).each(function() {
	  var iframe  = this,
				widget  = SC.Widget( this ),
				jqframe = jQuery( iframe ),
				sound   = {};

		widget.bind( SC.Widget.Events.READY, function() {
			widget.getCurrentSound(function( soundData ) {

				jqframe.attr( "data-player_id", soundData.id );
				jqframe.attr( "data-player_author", soundData.user.username );
				jqframe.attr( "data-player_title", soundData.title );
				jqframe.attr( "data-player_url", soundData.permalink_url );
				jqframe.attr( "data-player_duration", soundData.duration );

				sound = soundData;

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerReady',
					'mediaType': 'soundcloud',
					'mediaData': {
						'id':     soundData.id,
						'author': soundData.user.username,
						'title':  soundData.title,
						'url':    soundData.permalink_url,
						'duration': soundData.duration
					},
					'mediaCurrentTime': 0
				});
			}); // end of api call getDuration

			widget.bind( SC.Widget.Events.PLAY_PROGRESS, function( eventData ) {
			  gtm4wp_onSoundCloudPercentageChange( eventData );
			});

			widget.bind( SC.Widget.Events.PLAY, function( eventData ) {
				gtm4wp_onSoundCloudPlayerStateChange( eventData, 'play' );
			});

			widget.bind( SC.Widget.Events.PAUSE, function( eventData ) {
				gtm4wp_onSoundCloudPlayerStateChange( eventData, 'pause' );
			});

			widget.bind( SC.Widget.Events.FINISH, function( eventData ) {
				gtm4wp_onSoundCloudPlayerStateChange( eventData, 'ended' );
			});

			widget.bind( SC.Widget.Events.SEEK, function( eventData ) {
				gtm4wp_onSoundCloudPlayerStateChange( eventData, 'seeked' );
			});

			widget.bind( SC.Widget.Events.CLICK_DOWNLOAD, function() {
				gtm4wp_onSoundCloudPlayerEvent( 'click-download' );
			});

			widget.bind( SC.Widget.Events.CLICK_BUY, function() {
				gtm4wp_onSoundCloudPlayerEvent( 'click-buy' );
			});

			widget.bind( SC.Widget.Events.OPEN_SHARE_PANEL, function() {
				gtm4wp_onSoundCloudPlayerEvent( 'open-share-panel' );
			});

			widget.bind( SC.Widget.Events.ERROR, function() {
				gtm4wp_onSoundCloudPlayerEvent( 'error' );
			});
		});

		var gtm4wp_onSoundCloudPlayerStateChange = function( eventData, playerState ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.mediaPlayerStateChange',
				'mediaType': 'soundcloud',
				'mediaData': {
					'id':     sound.id,
					'author': sound.user.username,
					'title':  sound.title,
					'url':    sound.permalink_url,
					'duration': sound.duration
				},
				'mediaCurrentTime': eventData.currentPosition,
				'mediaPlayerState': playerState
			});
		};

		var gtm4wp_onSoundCloudPercentageChange = function( eventData ) {
			var mediaPercentage  = Math.floor( eventData.currentPosition / sound.duration * 100 );

			if ( typeof gtm4wp_soundclound_percentage_tracking_marks[ sound.id ] == "undefined" ) {
				gtm4wp_soundclound_percentage_tracking_marks[ sound.id ] = [];
			}

			for( var i=0; i<100; i+=gtm4wp_soundclound_percentage_tracking ) {
				if ( ( mediaPercentage > i ) && ( gtm4wp_soundclound_percentage_tracking_marks[ sound.id ].indexOf( i ) == -1 ) ) {
					gtm4wp_soundclound_percentage_tracking_marks[ sound.id ].push( i );

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.mediaPlaybackPercentage',
						'mediaType': 'soundcloud',
						'mediaData': {
							'id':     sound.id,
							'author': sound.user.username,
							'title':  sound.title,
							'url':    sound.permalink_url,
							'duration': sound.duration
						},
						'mediaCurrentTime': eventData.currentPosition,
						'mediaPercentage': i
					});
				}
			}
		};

		var gtm4wp_onSoundCloudPlayerEvent = function( eventName ) {
			widget.getPosition(function( currentPosition ) {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.mediaPlayerEvent',
					'mediaType': 'soundcloud',
					'mediaData': {
						'id':     sound.id,
						'author': sound.user.username,
						'title':  sound.title,
						'url':    sound.permalink_url,
						'duration': soundData.duration
					},
					'mediaCurrentTime': currentPosition,
					'mediaPlayerEvent': eventName
				});
			});
		};

	});
});