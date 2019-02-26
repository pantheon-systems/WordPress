(function ( $ ) {
	window.InlineShortcodeView_vc_tour = window.InlineShortcodeView_vc_tabs.extend( {
		render: function () {
			_.bindAll( this, 'stopSorting' );
			this.$tabs = this.$el.find( '> .wpb_tour' );
			window.InlineShortcodeView_vc_tabs.__super__.render.call( this );
			this.buildNav();
			return this;
		},
		beforeUpdate: function () {
			this.$tabs.find( '.wpb_tour_heading,.wpb_tour_next_prev_nav' ).remove();
			vc.frame_window.vc_iframe.destroyTabs( this.$tabs );
		},
		updated: function () {
			this.$tabs.find( '.wpb_tour_next_prev_nav' ).appendTo( this.$tabs );
			window.InlineShortcodeView_vc_tour.__super__.updated.call( this );
		}
	} );
})( window.jQuery );