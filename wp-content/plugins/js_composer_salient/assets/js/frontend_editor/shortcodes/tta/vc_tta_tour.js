(function ( $ ) {
	window.InlineShortcodeView_vc_tta_tour = window.InlineShortcodeView_vc_tta_tabs.extend( {
		defaultSectionTitle: window.i18nLocale.section,
		buildPagination: function () {
			this.removePagination();
			var params = this.model.get( 'params' );
			if ( ! _.isUndefined( params.pagination_style ) && params.pagination_style.length ) {
				this.$el.find( '.vc_tta-panels-container' ).append( this.getPaginationList() ); // TODO: change this
			}
		}
	} );
})( window.jQuery );