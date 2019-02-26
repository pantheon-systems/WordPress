(function ( $ ) {
	$( '#vc_vendor_qtranslate_langs_front' ).change( function () {
		vc.closeActivePanel();
		$( '#vc_logo' ).addClass( 'vc_ui-wp-spinner' );
		window.location.href = $( this ).val();
	} );

	vc.ShortcodesBuilder.prototype.getContent = function () {
		var output,
			$postContent = $( '#vc_vendor_qtranslate_postcontent' ),
			lang = $postContent.attr( 'data-lang' ),
			content = $postContent.val();
		vc.shortcodes.sort();
		output = this.modelsToString( vc.shortcodes.where( { parent_id: false } ) );
		return qtrans_integrate( lang, output, content );
	};
	vc.ShortcodesBuilder.prototype.getTitle = function () {
		var $titleContent = $( '#vc_vendor_qtranslate_posttitle' ),
			lang = $titleContent.attr( 'data-lang' ),
			content = $titleContent.val();
		return qtrans_integrate( lang, vc.title, content );
	};
})( window.jQuery );