(function ( $ ) {
	window.InlineShortcodeView_vc_column = window.InlineShortcodeViewContainerWithParent.extend( {
		controls_selector: '#vc_controls-template-vc_column',
		resizeDomainName: 'columnSize',
		_x: 0,
		css_width: 12,
		prepend: false,
		initialize: function ( params ) {
			window.InlineShortcodeView_vc_column.__super__.initialize.call( this, params );
			_.bindAll( this, 'startChangeSize', 'stopChangeSize', 'resize' );
		},
		render: function () {
			var width;
			window.InlineShortcodeView_vc_column.__super__.render.call( this );
			this.prepend = false;
			// Here goes width logic
			$( '<div class="vc_resize-bar"></div>' )
				.appendTo( this.$el )
				.mousedown( this.startChangeSize );
			this.setColumnClasses();
			this.customCssClassReplace();
			return this;
		},
		destroy: function ( e ) {
			var parent_id = this.model.get( 'parent_id' );
			window.InlineShortcodeView_vc_column.__super__.destroy.call( this, e );
			if ( ! vc.shortcodes.where( { parent_id: parent_id } ).length ) {
				vc.shortcodes.get( parent_id ).destroy();
			}
		},
		customCssClassReplace: function () {
			var css_classes, css_regex, class_match;

			css_classes = this.$el.find( '.wpb_column' ).attr( 'class' );
			css_regex = /.*(vc_custom_\d+).*/;
			class_match = css_classes && css_classes.match ? css_classes.match( css_regex ) : false;
			if ( class_match && class_match[ 1 ] ) {
				this.$el.addClass( class_match[ 1 ] );
				this.$el.find( '.wpb_column' ).attr( 'class', css_classes.replace( class_match[ 1 ], '' ).trim() );
			}
		},
		setColumnClasses: function () {
			var offset, width, $content;
			offset = this.getParam( 'offset' ) || '';
			width = this.getParam( 'width' ) || '1/1';
			$content = this.$el.find( '> .wpb_column' );
			this.css_class_width = this.convertSize( width );
			if ( this.css_class_width !== width ) {
				this.css_class_width = this.css_class_width.replace( /[^\d]/g, '' );
			}
			$content.removeClass( 'vc_col-sm-' + this.css_class_width );
			if ( ! offset.match( /vc_col\-sm\-\d+/ ) ) {
				this.$el.addClass( 'vc_col-sm-' + this.css_class_width );
			}
			if ( vc.responsive_disabled ) {
				offset = offset.replace( /vc_col\-(lg|md|xs)[^\s]*/g, '' );
			}
			if ( ! _.isEmpty( offset ) ) {
				$content.removeClass( offset );
				this.$el.addClass( offset );
			}
		},
		startChangeSize: function ( e ) {
			var width = this.getParam( width ) || 12;
			this._grid_step = this.parent_view.$el.width() / width;
			vc.frame_window.jQuery( 'body' ).addClass( 'vc_column-dragging' ).disableSelection();
			this._x = parseInt( e.pageX );
			vc.$page.bind( 'mousemove.' + this.resizeDomainName, this.resize );
			$( vc.frame_window.document ).mouseup( this.stopChangeSize );
		},
		stopChangeSize: function () {
			this._x = 0;
			vc.frame_window.jQuery( 'body' ).removeClass( 'vc_column-dragging' ).enableSelection();
			vc.$page.unbind( 'mousemove.' + this.resizeDomainName );
		},
		resize: function ( e ) {
			var width, old_width, diff, params = this.model.get( 'params' );
			diff = e.pageX - this._x;
			if ( Math.abs( diff ) < this._grid_step ) {
				return;
			}
			this._x = parseInt( e.pageX );
			old_width = '' + this.css_class_width;
			if ( 0 < diff ) {
				this.css_class_width += 1;
			} else if ( 0 > diff ) {
				this.css_class_width -= 1;
			}
			if ( 12 < this.css_class_width ) {
				this.css_class_width = 12;
			}
			if ( 1 > this.css_class_width ) {
				this.css_class_width = 1;
			}
			params.width = vc.getColumnSize( this.css_class_width );
			this.model.save( { params: params }, { silent: true } );
			this.$el.removeClass( 'vc_col-sm-' + old_width ).addClass( 'vc_col-sm-' + this.css_class_width );
		},
		convertSize: function ( width ) {
			var prefix, numbers, range, num, dev;
			prefix = 'vc_col-sm-';
			numbers = width ? width.split( '/' ) : [
				1,
				1
			];
			range = _.range( 1, 13 );
			num = ! _.isUndefined( numbers[ 0 ] ) && 0 <= _.indexOf( range,
				parseInt( numbers[ 0 ], 10 ) ) ? parseInt( numbers[ 0 ], 10 ) : false;
			dev = ! _.isUndefined( numbers[ 1 ] ) && 0 <= _.indexOf( range,
				parseInt( numbers[ 1 ], 10 ) ) ? parseInt( numbers[ 1 ], 10 ) : false;
			// Custom fix for 5 columns grid
			if ( '5' === numbers[ 1 ] ) {
				return width;
			}
			if ( false !== num && false !== dev ) {
				return prefix + (12 * num / dev);
			}
			return prefix + '12';
		},
		allowAddControl: function () {
			return vc_user_access().shortcodeAll( 'vc_column' );
		}
	} );
})( window.jQuery );