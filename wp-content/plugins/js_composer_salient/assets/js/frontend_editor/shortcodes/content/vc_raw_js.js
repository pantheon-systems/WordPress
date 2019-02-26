(function ( $ ) {
	window.InlineShortcodeView_vc_raw_js = window.InlineShortcodeView.extend( {
		render: function () {
			window.InlineShortcodeView_vc_raw_js.__super__.render.call( this );
			var script = this.$el.find( '.vc_js_inline_holder' ).val();
			this.$el.find( '.wpb_wrapper' ).html( script );
			return this;
		}
	} );
})( window.jQuery );