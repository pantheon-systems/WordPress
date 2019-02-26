(function ( $ ) {
	window.InlineShortcodeView_vc_toggle = window.InlineShortcodeView.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_toggle.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_iframe.vc_toggle( model_id );
			} );
			return this;
		}
	} );
})( window.jQuery );