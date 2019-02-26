/* =========================================================
 * templates-preview.js v1.0.0
 * =========================================================
 * Copyright 2015 WPBakery
 *
 * WPBakery Page Builder template preview
 * ========================================================= */
/* global vc */
(function ( $ ) {
	'use strict';
	if ( window.vc && vc.visualComposerView ) {
		// unset Draggable
		vc.visualComposerView.prototype.setDraggable = function () {
		};
		// unset Sortable
		vc.visualComposerView.prototype.setSortable = function () {
		};
		// unset Sortable
		vc.visualComposerView.prototype.setSorting = function () {
		};
		// unset save
		vc.visualComposerView.prototype.save = function () {
		};
		// unset controls checks for scroll
		vc.visualComposerView.prototype.navOnScroll = function () {
		};

		vc.visualComposerView.prototype.addElement = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};

		vc.visualComposerView.prototype.addTextBlock = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};

		vc.shortcode_view.prototype.events = {};
		vc.shortcode_view.prototype.editElement = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};
		vc.shortcode_view.prototype.clone = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};
		vc.shortcode_view.prototype.addElement = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};
		vc.shortcode_view.prototype.deleteShortcode = function ( e ) {
			e && e.preventDefault && e.preventDefault();
		};
		vc.shortcode_view.prototype.setEmpty = function () {
		};
		vc.visualComposerView.prototype.events = {};
		//vc.shortcode_view.prototype.designHelpersSelector = '[data-js-handler-design-helper]';

		// update backend getView
		vc.visualComposerView.prototype.getView = function ( model ) {
			var view;
			if ( _.isObject( vc.map[ model.get( 'shortcode' ) ] ) && _.isString( vc.map[ model.get( 'shortcode' ) ].js_view ) && vc.map[ model.get( 'shortcode' ) ].js_view.length && ! _.isUndefined( window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ] ) ) {
				try {
					var viewConstructor = window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ];
					viewConstructor.prototype.events = {};
					viewConstructor.prototype.setSortable = function () {
					};
					viewConstructor.prototype.setSorting = function () {
					};
					viewConstructor.prototype.setDropable = function () {
					};
					viewConstructor.prototype.editElement = function ( e ) {
						e && e.preventDefault && e.preventDefault();
					};
					viewConstructor.prototype.clone = function ( e ) {
						e && e.preventDefault && e.preventDefault();
					};
					viewConstructor.prototype.addElement = function ( e ) {
						e && e.preventDefault && e.preventDefault();
					};
					viewConstructor.prototype.deleteShortcode = function ( e ) {
						e && e.preventDefault && e.preventDefault();
					};
					viewConstructor.prototype.setEmpty = function () {
					};
					viewConstructor.prototype.events = {};
					//	viewConstructor.prototype.designHelpersSelector = '[data-js-handler-design-helper]';
					view = new viewConstructor( { model: model } );
				} catch ( e ) {
					window.console && window.console.error && window.console.error( e );
				}
			} else {
				vc.shortcode_view.prototype.events = {};
				view = new vc.shortcode_view( { model: model } );
			}
			model.set( { view: view } );
			return view;
		};

	}

	// unset sortable,draggable,droppable - removed due to issues of return types
	/*jQuery.fn.sortable = function () {
	 }
	 jQuery.fn.draggable = function () {
	 }
	 jQuery.fn.droppable = function () {
	 }*/
	if ( window.VcGitemView ) {
		window.VcGitemView.prototype.setDropable = function () {
		};
		window.VcGitemView.prototype.setDraggable = function () {
		};
		window.VcGitemView.prototype.setDraggableC = function () {
		};

	}

	if ( window.vc && window.vc.events ) {
		window.vc.events.on( 'shortcodeView:ready', function ( view ) {
			if ( window.VcGitemView ) {
				// and do more complex for grid builder
				/*var goodShortcodes = [
				 'vc_gitem',
				 'vc_gitem_animated_block',
				 'vc_gitem_zone_a',
				 'vc_gitem_row',
				 'vc_gitem_col',
				 'vc_gitem_zone_c',
				 'vc_gitem_zone_b'
				 ];
				 if ( view.$control_buttons && _.indexOf( goodShortcodes, view.model.get( 'shortcode' ) ) !== - 1 ) {
				 //	view.$controls_buttons.remove(); // do this for normal case BE
				 }
				 view.$el.find( '.vc_control.column_edit' ).remove();
				 view.$el.find( '.vc_control.column_add' ).remove();
				 view.$el.find( '.vc_control.column_delete' ).remove();
				 view.$el.find( '.vc_control.column_clone' ).remove();
				 view.$el.find( '.vc_control.column_move' ).remove();
				 view.$el.find( '.vc_color-helper' ).css( 'right', '0' );*/
				view.$el.find( '.vc_control-btn.vc_element-name.vc_element-move .vc_btn-content' ).attr( 'style',
					'cursor:pointer !important;' +
					'padding-left: 10px !important;' );
				view.$el.find( '.vc_control-btn.vc_element-name.vc_element-move .vc_btn-content .vc-c-icon-dragndrop' ).hide();
				//view.$el.find( '.vc_controls.vc_controls-visible.bottom-controls' ).remove();
				if ( 'vc_gitem' === view.model.get( 'shortcode' ) ) {
					view.$el.find( '.vc_gitem-add-c-col:not(.vc_zone-added)' ).remove()
				}
			} else {
				//view.$control_buttons && view.$controls_buttons.remove(); // do this for normal case BE
				//view.$el.find( '.vc_controls' ).remove(); // do this for normal case BE
			}
			if ( view.$el ) {
				// remove TTA section append
				view.$el.find( '.vc_tta-section-append' ).remove();
				// remove old TTA tour append
				view.$el.find( '.add_tab_block' ).remove();
				view.$el.find( '.tab_controls' ).remove();
				// remove single image "add-image" link
				view.$el.find( '.column_edit_trigger' ).remove();
			}
		} );
	}

	vc.visualComposerView.prototype.initializeAccessPolicy = function () {
		this.accessPolicy = {
			be_editor: true,
			fe_editor: false,
			classic_editor: false
		};
	};
	vc.events.on( 'app.addAll', function () {
		if ( parent && parent.vc ) {
			parent.vc.templates_panel_view.setTemplatePreviewSize();
		}
	} );
	$(window ).resize(function(){
		parent.vc.templates_panel_view.setTemplatePreviewSize();
	});
})( window.jQuery );