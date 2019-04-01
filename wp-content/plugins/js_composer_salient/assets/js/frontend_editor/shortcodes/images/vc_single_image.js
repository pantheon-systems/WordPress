(function ( $ ) {
	window.InlineShortcodeView_vc_single_image = window.InlineShortcodeView.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_single_image.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				if ( 'undefined' !== typeof(this.vc_image_zoom) ) {
					this.vc_image_zoom( model_id );
				}

			} );
			return this;
		},
		parentChanged: function () {
			var modelId = this.model.get( 'id' );
			window.InlineShortcodeView_vc_single_image.__super__.parentChanged.call( this );
			if ( 'undefined' !== typeof(vc.frame_window.vc_image_zoom) ) {
				_.defer( function () {
					vc.frame_window.vc_image_zoom( modelId );
				} );
			}
			return this;
		}
	} );
})( window.jQuery );