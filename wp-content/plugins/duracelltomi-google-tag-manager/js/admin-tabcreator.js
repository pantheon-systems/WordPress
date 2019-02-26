jQuery( function() {
	var admintabs = [];
	var adminsubtabsdata = window.adminsubtabs || {};
	var adminsubtabs = [];

	jQuery( '#wpbody form h2' ).each(function( i ) {
		admintabs.push( '<a class="nav-tab" href="#">' + jQuery(this).text() + '</a>' );

		jQuery(this)
			.remove();

		if ( adminsubtabsdata[ i ] ) {
			var _subtabs = [];
			var _startrow = 0;
         
			for (var j in adminsubtabsdata[ i ] ) {
				_subtabs.push( '<a href="#" data-formtableid="' + i + '" data-startrow="' + _startrow + '" data-endrow="' + (_startrow + adminsubtabsdata[ i ][ j ].numitems) + '">' + adminsubtabsdata[ i ][ j ].tabtext + '</a>' );
				_startrow += adminsubtabsdata[ i ][ j ].numitems;
			}
      
			if ( _subtabs.length > 0 ) {
				adminsubtabs.push( '<ul class="adminsubtabs" id="adminsubtabs' + i + '"><li>' + _subtabs.join('</li><li>') + '</li></ul>' );
			}
		}
	});

	jQuery("#wpbody form[action='options.php']")
		.prepend( adminsubtabs.join('') )
		.prepend( '<h2 class="nav-tab-wrapper">' + admintabs.join('') + '</h2>' );

	jQuery( '.adminsubtabs li a' )
		.on( 'click', function() {
			var jqthis = jQuery(this);

			jqthis
				.parent()
				.parent()
				.find( 'a' )
				.removeClass( 'subtab-active' );

			jqthis
				.addClass( 'subtab-active' );

			jQuery( '.form-table:eq(' + jqthis.data( 'formtableid' ) + ') tr' )
				.hide()
				.slice( jqthis.data('startrow'), jqthis.data('endrow') )
				.show();

			return false;
		});

	jQuery( '.nav-tab-wrapper a' )
		.on( "click", function() {
			jQuery( '.nav-tab-wrapper a.nav-tab-active' )
				.removeClass( "nav-tab-active" );

			jQuery( '#wpbody form .tabinfo,#wpbody form .form-table,.adminsubtabs' )
				.hide();

			var tabindex = jQuery(this)
				.addClass( 'nav-tab-active' )
				.index();

			jQuery( '#wpbody form .tabinfo:eq(' + tabindex + '),#wpbody form .form-table:eq(' + tabindex + ')' )
				.show();

			jQuery( '#wpbody #adminsubtabs' + tabindex + ':not(.subtab-activated)' )
				.find( 'a:first' )
				.trigger( 'click' );

			jQuery( '#wpbody #adminsubtabs' + tabindex )
				.addClass( 'subtab-activated' )
				.show();

			return false;
		});

	jQuery( '.nav-tab-wrapper a:first' )
		.trigger( 'click' );
});