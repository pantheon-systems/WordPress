;(function($){
	$(document).ready(function(){
		$( qpprMetaData.appendTo ).append( qpprMetaData.injectMsg );
		var ctval 		= qpprMetaData.secs,
			metaText 	= '',
			bFamily		= qpprMetaData.browserFamily,
			rSecs		= qpprMetaData.secs,
			rURL		= qpprMetaData.refreshURL;
		function timerFunc(){
			if($('#qppr_meta_counter').length >= 1){
				metaText = $('#qppr_meta_counter').data('meta-counter-text')
				if( typeof metaText == 'undefined' ) 
					metaText = 'Page will redirect in %1$ seconds' ;
				if( ctval < 1){
					clearTimeout(timerFunc);
				   	$('#qppr_meta_counter').text( metaText.replace( '%1$',ctval) );
					ctval--;
				}else if(ctval >= 1){
				   	$('#qppr_meta_counter').text( metaText.replace( '%1$',ctval) );
					ctval--;
				   	setTimeout(timerFunc, 1000);
				}
			}
		}
		$.timerFuncNew = function(){ timerFunc(); }
		if( !$("meta[http-equiv=refresh]").is('*') ){
			var redirectTrigger = $( qpprMetaData.class ).length > 0 ? qpprMetaData.class : 'body';
			if( $(redirectTrigger ).length > 0 ){
				var tagtype = $( redirectTrigger ).prop('tagName').toLowerCase();
				switch( bFamily ) {
					case 'safari':
					case 'google-chrome':
						if( tagtype == 'img' || tagtype == 'script' || tagtype == 'frame' || tagtype == 'iframe'){
							$( redirectTrigger ).load(function() {
								$.timerFuncNew();
								$('head').append('<meta http-equiv="refresh" content="'+rSecs+';url='+rURL+'" />');
							});
						}else{
							$( window ).load(function() {
								$.timerFuncNew();
								$('head').append('<meta http-equiv="refresh" content="'+rSecs+';url='+rURL+'" />');
							});
						}
						break;
					default:
						if( tagtype == 'img' || tagtype == 'script' || tagtype == 'frame' || tagtype == 'iframe'){
							$( redirectTrigger ).load(function() {
								$.timerFuncNew();
								$('head').append('<meta http-equiv="refresh" content="'+rSecs+';url='+rURL+'" />');
								window.setTimeout(function() {window.location.href = rURL;}, (rSecs * 1000));
							});
						}else{
							$( window ).load(function() {
								$.timerFuncNew();
								$('head').append('<meta http-equiv="refresh" content="'+rSecs+';url='+rURL+'" />');
								window.setTimeout(function() {window.location.href = rURL;}, (rSecs * 1000));
							});
						}
						break;
				}
			}
		}
	});
})(jQuery);