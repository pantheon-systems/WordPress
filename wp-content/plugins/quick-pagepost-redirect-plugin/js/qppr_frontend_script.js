;(function($){
	$(document).ready(function(){
		var newWindowArr = qpprFrontData.linkData;
		var root 		 = qpprFrontData.siteURL;
		var qroot 		 = qpprFrontData.siteURLq;
		$( 'a[href]' ).each( function( ){
			var thisMatch = $( this );
			var href = typeof $( this ).attr( 'href' ) !== 'undefined' ? $( this ).attr( 'href' ) : '';
			var linkFound = qppr_in_array( href, newWindowArr );
			if( linkFound !== false ){
				var rel  = 'undefined' !== typeof $( this ).attr( 'rel' ) ? $( this ).attr( 'rel' ) : '';
				var tar  = 'undefined' !== typeof $( this ).attr( 'target' ) ? $( this ).attr( 'target' ) : '';
				var doNW = false;
				var doNF = false;
				var rURL = '';
				var hChk = href;
				
				if( linkFound == '1' ){
					doNW = newWindowArr[ href ][0];
					doNF = newWindowArr[ href ][1];
					rURL = newWindowArr[ href ][2];
				}else if( linkFound == '2' ){
					doNW = newWindowArr[ href.replace(root,'') ][0];
					doNF = newWindowArr[ href.replace(root,'') ][1];
					rURL = newWindowArr[ href.replace(root,'') ][2];
					hChk = href.replace(root,'');
				}else if( linkFound == '3' ){
					doNW = newWindowArr[ href.replace(qroot,'') ][0];
					doNF = newWindowArr[ href.replace(qroot,'') ][1];
					rURL = newWindowArr[ href.replace(qroot,'') ][2];
					hChk = href.replace(qroot,'');
				}
				if( doNW ){
					if( this.target === '' )
						this.target = "_blank";
				}
				if( doNF ){
					if( rel !== '' && rel !== 'nofollow' )
						$( this ).attr( 'rel', rel + ' nofollow');
					else
						$( this ).attr( 'rel', 'nofollow' );
				}
				if( rURL != '' ){
					$( this ).attr( 'href', rURL );
					var newTxt = thisMatch.html();
					newTxt = newTxt.replace(hChk,rURL)
					thisMatch.html( newTxt );
				}
			}
		});
		function qppr_in_array( needle, haystack ) {
			if( 'undefined' !== typeof haystack[needle] ){
				return '1';
			}else if( 'undefined' !== typeof haystack[needle.replace(root,'')]){
				return '2';
			}else if( 'undefined' !== typeof haystack[needle.replace(qroot,'')]){
				return '3';
			}
			return false;
		}
	});
})(jQuery);