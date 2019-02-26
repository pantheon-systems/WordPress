(function ( $ ) {
	window.InlineShortcodeView_vc_pie = window.InlineShortcodeView.extend( {
		render: function () {
			_.bindAll( this, 'parentChanged' );
			window.InlineShortcodeView_vc_pie.__super__.render.call( this );
			this.unbindResize();
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_iframe.vc_pieChart();
			} );
			return this;
		},
		unbindResize: function () {
			vc.frame_window.jQuery( vc.frame_window ).unbind( 'resize.vcPieChartEditable' );
		},
		parentChanged: function () {
			this.$el.find( '.vc_pie_chart' ).removeClass( 'vc_ready' );
			vc.frame_window.vc_pieChart();
		},
		rowsColumnsConverted: function () {
			window.setTimeout( this.parentChanged, 200 );
			this.parentChanged();
		}
	} );
})( window.jQuery );