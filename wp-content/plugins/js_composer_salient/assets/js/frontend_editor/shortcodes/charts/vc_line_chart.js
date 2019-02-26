(function ( $ ) {
	window.InlineShortcodeView_vc_line_chart = window.InlineShortcodeView.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_line_chart.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_line_charts( model_id );
			} );
			return this;
		},
		parentChanged: function () {
			var modelId = this.model.get( 'id' );
			window.InlineShortcodeView_vc_line_chart.__super__.parentChanged.call( this );
			_.defer( function () {
				vc.frame_window.vc_line_charts( modelId );
			} );
			return this;
		},
		remove: function () {
			var id = this.$el.find( '.vc_line-chart' ).data( 'vcChartId' );
			window.InlineShortcodeView_vc_line_chart.__super__.remove.call( this );
			if ( id && undefined !== vc.frame_window.Chart.instances[ id ] ) {
				delete vc.frame_window.Chart.instances[ id ];
			}
		}
	} );
})( window.jQuery );