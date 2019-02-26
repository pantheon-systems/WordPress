(function ( $ ) {
	window.InlineShortcodeView_vc_section = window.InlineShortcodeViewContainer.extend( {
		controls_selector: '#vc_controls-template-container',
		initialize: function () {
			_.bindAll( this, 'checkSectionWidth' );
			window.InlineShortcodeView_vc_section.__super__.initialize.call( this );
			vc.frame_window.jQuery( vc.frame_window.document ).off( 'vc-full-width-row-single',
				this.checkSectionWidth );
			vc.frame_window.jQuery( vc.frame_window.document ).on( 'vc-full-width-row-single', this.checkSectionWidth );
		},
		checkSectionWidth: function ( e, data ) {
			if ( data.el.hasClass( 'vc_section' ) && data.el.attr( 'data-vc-stretch-content' ) ) {
				data.el.siblings( '.vc_controls' ).find( '.vc_controls-out-tl' ).css( { left: data.offset - 17 } );
			}
		},
		render: function () {
			var $content = this.content();
			if ( $content && $content.hasClass( 'vc_row-has-fill' ) ) {
				$content.removeClass( 'vc_row-has-fill' );
				this.$el.addClass( 'vc_row-has-fill' );
			}

			return window.InlineShortcodeView_vc_section.__super__.render.call( this );
		}
	} );
})( window.jQuery );