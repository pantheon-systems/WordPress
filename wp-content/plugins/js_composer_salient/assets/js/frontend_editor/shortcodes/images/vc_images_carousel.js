(function ( $ ) {
	window.InlineShortcodeView_vc_images_carousel = window.InlineShortcodeView.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_images_carousel.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_iframe.vc_imageCarousel( model_id );
			} );
			return this;
		}
	} );
})( window.jQuery );