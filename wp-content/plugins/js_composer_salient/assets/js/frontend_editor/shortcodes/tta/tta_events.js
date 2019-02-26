(function ( $ ) {
	function ttaMapChildEvents( model ) {
		var childTag = 'vc_tta_section';
		vc.events.on(
			'shortcodes:' + childTag + ':add:parent:' + model.get( 'id' ),
			function ( model ) {
				var activeTabIndex, models, parentModel;
				parentModel = vc.shortcodes.get( model.get( 'parent_id' ) );
				activeTabIndex = parseInt( parentModel.getParam( 'active_section' ) );
				if ( 'undefined' === typeof(activeTabIndex) ) {
					activeTabIndex = 1;
				}
				models = _.pluck( _.sortBy( vc.shortcodes.where( { parent_id: parentModel.get( 'id' ) } ),
					function ( model ) {
						return model.get( 'order' );
					} ), 'id' );
				if ( models.indexOf( model.get( 'id' ) ) === activeTabIndex - 1 ) {
					model.set( 'isActiveSection', true );
				}
				return model;
			}
		);
		vc.events.on(
			'shortcodes:' + childTag + ':clone:parent:' + model.get( 'id' ),
			function ( model ) {
				vc.ttaSectionActivateOnClone && model.set( 'isActiveSection', true );
				vc.ttaSectionActivateOnClone = false;
			}
		);
	}

	vc.events.on( 'shortcodes:vc_tta_accordion:add', ttaMapChildEvents );
	vc.events.on( 'shortcodes:vc_tta_tabs:add', ttaMapChildEvents );
	vc.events.on( 'shortcodes:vc_tta_tour:add', ttaMapChildEvents );
	vc.events.on( 'shortcodes:vc_tta_pageable:add', ttaMapChildEvents );
})( window.jQuery );