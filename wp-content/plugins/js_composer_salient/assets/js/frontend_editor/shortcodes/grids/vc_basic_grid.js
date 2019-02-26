(function ( $ ) {
	window.InlineShortcodeView_vc_basic_grid = vc.shortcode_view.extend( {
		render: function ( e ) {
			window.InlineShortcodeView_vc_basic_grid.__super__.render.call( this, e );
			this.initGridJs( true );
			return this;
		},
		parentChanged: function () {
			window.InlineShortcodeView_vc_basic_grid.__super__.parentChanged.call( this );
			this.initGridJs();
		},
		initGridJs: function ( useAddActivity ) {
			var model = this.model;
			if ( true === model.get( 'grid_activity' ) ) {
				return false;
			}
			model.set( 'grid_activity', true );
			if ( true === useAddActivity ) {

				vc.frame_window.vc_iframe.addActivity( function () {
					this.vc_iframe.gridInit( model.get( 'id' ) );
					model.set( 'grid_activity', false );
				} );
			} else {
				vc.frame_window.vc_iframe.gridInit( model.get( 'id' ) );
				model.set( 'grid_activity', false );
			}
		}
	} );
})( window.jQuery );