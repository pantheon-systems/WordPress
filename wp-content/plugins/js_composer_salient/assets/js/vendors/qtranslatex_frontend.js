(function ( $ ) {
	$( '#vc_vendor_qtranslatex_langs_front' ).change( function () {
		vc.closeActivePanel();
		$( '#vc_logo' ).addClass( 'vc_ui-wp-spinner' );
		window.location.href = $( this ).val();
	} );
	
	var nativeGetContent = vc.ShortcodesBuilder.prototype.getContent;
	vc.ShortcodesBuilder.prototype.getContent = function () {
		var content = nativeGetContent();
		jQuery( '#content' ).val( content );
		return content;
	};

})( window.jQuery );