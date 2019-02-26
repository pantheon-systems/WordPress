(function ( $ ) {
	window.InlineShortcodeView_vc_column_text = window.InlineShortcodeView.extend( {
		initialize: function ( options ) {
			window.InlineShortcodeView_vc_column_text.__super__.initialize.call( this, options );
			_.bindAll( this, 'setupEditor', 'updateContent' );
		},
		render: function () {
			window.InlineShortcodeView_vc_column_text.__super__.render.call( this );
			// Here
			return this;
		},
		setupEditor: function ( ed ) {
			ed.on( 'keyup', this.updateContent )
		},
		updateContent: function () {
			var params = this.model.get( 'params' );
			params.content = tinyMCE.activeEditor.getContent();
			this.model.save( { params: params }, { silent: true } );
		}
	} );
})( window.jQuery );