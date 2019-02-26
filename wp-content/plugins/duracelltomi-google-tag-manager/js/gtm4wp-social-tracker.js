jQuery( function() {
	if ( typeof FB != "undefined" ) {
		FB.Event.subscribe( 'edge.create', function( href, widget ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.socialAction',
				'network': 'facebook',
				'socialAction': 'like',
				'opt_target': href,
				'opt_pagePath': window.location.href
			});
		});

		FB.Event.subscribe( 'edge.remove', function( href, widget ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.socialAction',
				'network': 'facebook',
				'socialAction': 'unlike',
				'opt_target': href,
				'opt_pagePath': window.location.href
			});
		});

		FB.Event.subscribe( 'comment.create', function( href, commentID ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.socialAction',
				'network': 'facebook',
				'socialAction': 'comment',
				'opt_target': href,
				'opt_pagePath': window.location.href
			});
		});

		FB.Event.subscribe( 'comment.remove', function( href, commentID ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.socialAction',
				'network': 'facebook',
				'socialAction': 'uncomment',
				'opt_target': href,
				'opt_pagePath': window.location.href
			});
		});

		FB.Event.subscribe( 'message.send', function( response ) {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.socialAction',
				'network': 'facebook',
				'socialAction': 'send',
				'opt_target': response,
				'opt_pagePath': window.location.href
			});
		});
	} // end of Facebook social events

	if ( typeof window.twttr == "undefined" ) {
		window.twttr = (function ( d, s, id ) {
			var t, js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
			js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
      return window.twttr || (t = {
        _e: [],
        ready: function(f) {
          t._e.push(f);
        }
      });
    } (document, "script", "twitter-wjs"));
	} // end of loading Twitter JS

	if ( typeof window.twttr != "undefined" ) {
		window.twttr.ready(function ( twttr ) {
			twttr.events.bind( 'tweet', function ( intent_event ) {
				if ( intent_event ) {
					var label = intent_event.data.tweet_id;
					
          if (typeof label != 'undefined' && label) {
            if(label == 'label'){
              label = window.location.href;
            }
          }else{
            label = window.location.href;
          }

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.socialAction',
						'network': 'twitter',
						'socialAction': 'tweet',
						'opt_target': label,
						'opt_pagePath': window.location.href
					});
				}
			});

			window.twttr.events.bind( 'follow', function ( intent_event ) {
				if ( intent_event ) {
					var label = intent_event.data.user_id + " (" + intent_event.data.screen_name + ")";

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.socialAction',
						'network': 'twitter',
						'socialAction': 'follow',
						'opt_target': label,
						'opt_pagePath': window.location.href
					});
				}
			});
		});
	}
});