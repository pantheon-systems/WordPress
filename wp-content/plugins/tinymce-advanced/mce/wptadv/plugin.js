/*global tinymce:true */

tinymce.PluginManager.add( 'wptadv', function( editor ) {
	var regex = [
			new RegExp('https?://(www\\.)?youtube\\.com/(watch|playlist).*', 'i'),
			new RegExp('https?://youtu.be/.*', 'i'),
			new RegExp('https?://blip.tv/.*', 'i'),
			new RegExp('https?://(www\\.)?vimeo\\.com/.*', 'i'),
			new RegExp('https?://(www\\.)?dailymotion\\.com/.*', 'i'),
			new RegExp('https?://dai.ly/.*', 'i'),
			new RegExp('https?://(www\\.)?flickr\\.com/.*', 'i'),
			new RegExp('https?://flic.kr/.*', 'i'),
			new RegExp('https?://(.+\\.)?smugmug\\.com/.*', 'i'),
			new RegExp('https?://(www\\.)?hulu\\.com/watch/.*', 'i'),
			new RegExp('https?://(www\\.)?viddler\\.com/.*', 'i'),
			new RegExp('https?://qik.com/.*', 'i'),
			new RegExp('https?://revision3.com/.*', 'i'),
			new RegExp('https?://i*.photobucket.com/albums/.*', 'i'),
			new RegExp('https?://gi*.photobucket.com/groups/.*', 'i'),
			new RegExp('https?://(www\\.)?scribd\\.com/.*', 'i'),
			new RegExp('https?://wordpress.tv/.*', 'i'),
			new RegExp('https?://(.+\\.)?polldaddy\\.com/.*', 'i'),
			new RegExp('https?://poll\\.fm/.*', 'i'),
			new RegExp('https?://(www\\.)?funnyordie\\.com/videos/.*', 'i'),
			new RegExp('https?://(www\\.)?twitter\\.com/.+?/status(es)?/.*', 'i'),
			new RegExp('https?://vine\\.co/v/.*', 'i'),
			new RegExp('https?://(www\\.)?soundcloud\\.com/.*', 'i'),
			new RegExp('https?://(www\\.)?slideshare\\.net/.*', 'i'),
			new RegExp('https?://instagr(\\.am|am\\.com)/p/.*', 'i'),
			new RegExp('https?://(www\\.)?rdio\\.com/.*', 'i'),
			new RegExp('https?://rd\\.io/x/.*', 'i'),
			new RegExp('https?://(open|play)\\.spotify\\.com/.*', 'i'),
			new RegExp('https?://(.+\\.)?imgur\\.com/.*', 'i'),
			new RegExp('https?://(www\\.)?meetu(\\.ps|p\\.com)/.*', 'i'),
			new RegExp('https?://(www\\.)?issuu\\.com/.+/docs/.*', 'i'),
			new RegExp('https?://(www\\.)?collegehumor\\.com/video/.*', 'i'),
			new RegExp('https?://(www\\.)?mixcloud\\.com/.*', 'i'),
			new RegExp('https?://(www\\.|embed\\.)?ted\\.com/talks/.*', 'i'),
			new RegExp('https?://(www\\.)(animoto|video214)\\.com/play/.*', 'i'),
			new RegExp('https?://(.+)\.tumblr\.com/post/.*', 'i'),
			new RegExp('https?://(www\.)?kickstarter\.com/projects/.*', 'i'),
			new RegExp('https?://kck\.st/.*', 'i')
		],
		blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre' +
			'|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section' +
			'|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary',
		trim = tinymce.trim;

	function addLineBreaks( html ) {
		html = html.replace( new RegExp( '<(?:' + blocklist + ')(?: [^>]*)?>', 'gi' ), '\n$&' );
		html = html.replace( new RegExp( '</(?:' + blocklist + ')>', 'gi' ), '$&\n' );
		html = html.replace( new RegExp( '<br(?: [^>]*)?>', 'gi' ), '$&\n' );
		html = html.replace( />\n\n</g, '>\n<' );
		html = html.replace( /^<li/gm, '\t<li' );

		return trim( html );
	}

	if ( ! editor.settings.wpautop && editor.settings.tadv_noautop ) {
		editor.on( 'init', function() {
			editor.on( 'SaveContent', function( event ) {
				event.content = event.content.replace( /<p>\s*(https?:\/\/[^<>"\s]+)\s*<\/p>/ig, function( all, match ) {
					for( var i in regex ) {
						if ( regex[i].test( match ) ) {
							return '\n' + match + '\n';
						//	return '<p>[embed]' + match + '[/embed]</p>';
						}
					}
					return all;
				})
				.replace( /caption\](\s|<br[^>]*>|<p>&nbsp;<\/p>)*\[caption/g, 'caption] [caption' )
				.replace( /<(object|audio|video)[\s\S]+?<\/\1>/g, function( match ) {
					return match.replace( /[\r\n]+/g, ' ' );
				})
				.replace( /<pre[^>]*>[\s\S]+?<\/pre>/g, function( match ) {
					match = match.replace( /<br ?\/?>(\r\n|\n)?/g, '\n' );
					return match.replace( /<\/?p( [^>]*)?>(\r\n|\n)?/g, '\n' );
				});
			}); // end SaveContent

			editor.on( 'hide', function() {
				var textarea = editor.getElement();
				textarea.value = addLineBreaks( textarea.value );
			});
		});

		editor.on( 'beforeSetContent', function( event ) {
			var wp = window.wp,
				autop = wp && wp.editor && wp.editor.autop;

			if ( event.load ) {
				event.content = event.content.replace( /(^|[\r\n]+)\s*(https?:\/\/[^<>"\s]+)[ \u00A0\uFEFF]*([\r\n]+|$)/igm, '$1<p>$2</p>$3' );

				if ( autop && event.content && event.content.indexOf( '\n' ) > -1 && ! /<p>/i.test( event.content ) ) {
					event.content = autop( event.content );
				}
			}
		}, true );
	}
});
