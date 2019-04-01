(function ( $ ) {
	window.InlineShortcodeView_vc_gallery = window.InlineShortcodeView.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_gallery.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_iframe.vc_gallery( model_id );
			} );
			return this;
		},
		parentChanged: function () {
			window.InlineShortcodeView_vc_gallery.__super__.parentChanged.call( this );
			vc.frame_window.vc_iframe.vc_gallery( this.model.get( 'id' ) );
		}
	} );
})( window.jQuery );