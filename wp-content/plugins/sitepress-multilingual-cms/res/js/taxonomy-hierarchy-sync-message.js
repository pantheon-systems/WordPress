/*globals jQuery, document */

var WPML_Core = WPML_Core || {};
WPML_Core.taxonomy_sync_complete_reload = function () {
	"use strict";
	location.reload();
}

WPML_Core.taxonomy_sync_complete = function ( event, xhr, settings ) {
	"use strict";

	if ( settings.data && typeof settings.data === 'string' ) {
		if ( settings.data.search( 'action=wpml_tt_sync_hierarchy_save' ) !== -1 ) {
			WPML_Core.taxonomy_sync_complete_reload();
		}
	}
}

jQuery( document ).ready( function () {
	"use strict";

	jQuery( document ).ajaxComplete( WPML_Core.taxonomy_sync_complete );
} );