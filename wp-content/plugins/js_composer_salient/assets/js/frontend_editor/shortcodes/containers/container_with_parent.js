(function ( $ ) {
	window.InlineShortcodeViewContainerWithParent = window.InlineShortcodeViewContainer.extend( {
		controls_selector: '#vc_controls-template-container-with-parent',
		events: {
			'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
			'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
			'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
			'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
			'click > .vc_controls .vc_control-btn-append': 'appendElement',
			'click > .vc_controls .vc_parent .vc_control-btn-delete': 'destroyParent',
			'click > .vc_controls .vc_parent .vc_control-btn-edit': 'editParent',
			'click > .vc_controls .vc_parent .vc_control-btn-clone': 'cloneParent',
			'click > .vc_controls .vc_parent .vc_control-btn-prepend': 'addSibling',
			'click > .vc_controls .vc_parent .vc_control-btn-layout': 'changeLayout',
			'click > .vc_empty-element': 'appendElement',
			'click > .vc_controls .vc_control-btn-switcher': 'switchControls',
			'mouseenter': 'resetActive',
			'mouseleave': 'holdActive'
		},
		destroyParent: function ( e ) {
			e && e.preventDefault();
			this.parent_view.destroy( e );
		},
		cloneParent: function ( e ) {
			e && e.preventDefault();
			this.parent_view.clone( e );
		},
		editParent: function ( e ) {
			e && e.preventDefault();
			this.parent_view.edit( e );
		},
		addSibling: function ( e ) {
			e && e.preventDefault();
			this.parent_view.addElement( e );
		},
		changeLayout: function ( e ) {
			e && e.preventDefault();
			this.parent_view.changeLayout( e );
		},
		switchControls: function ( e ) {
			e && e.preventDefault();
			vc.unsetHoldActive();
			var $control = $( e.currentTarget ),
				$parent = $control.parent(),
				$current;
			// $parentAdvanced = $parent.find( '.vc_advanced' );
			//$parentAdvanced.width( 30 * $parentAdvanced.find( '.vc_control-btn' ).length );
			$parent.addClass( 'vc_active' );

			$current = $parent.siblings( '.vc_active' );
			//$current.find( '.vc_advanced' ).width( 0 );
			$current.removeClass( 'vc_active' );
			! $current.hasClass( 'vc_element' ) && window.setTimeout( this.holdActive, 500 );
		}
	} );
})( window.jQuery );