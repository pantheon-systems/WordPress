var otgs_wp_installer_dismiss_nag = {

	init: function() {
		jQuery( '.installer-dismiss-nag' ).click( otgs_wp_installer_dismiss_nag.dismiss_nag );
	},

	dismiss_nag: function() {
		var thisa = jQuery( this );

		data = { action: 'installer_dismiss_nag', repository: jQuery( this ).data( 'repository' ) };

		jQuery.ajax( {
			url: ajaxurl, type: 'POST', dataType: 'json', data: data, success:
				function( ret ) {
					thisa.closest( '.otgs-is-dismissible' ).remove();
				}
		} );

		return false;
	}
};

jQuery( document ).ready( otgs_wp_installer_dismiss_nag.init );