(function($){
	$(document).ready( function(){
		var $reset_builder_areas = $( '#et_pb_toggle_builder, #et_pb_main_container' );

		$reset_builder_areas.click( function( event ) {
			disable_flatsome_builder();
		});

		function disable_flatsome_builder() {
			var is_flatsome_builder_enabled = $( '#enable-uxbuilder' ).length && $( '#enable-uxbuilder' ).hasClass( 'nav-tab-active' );
			if ( is_flatsome_builder_enabled ) {
				var ms = 365*24*60*60*1000,
					date = new Date(),
					expires;
				date.setTime( date.getTime() + ms );
				expires = "; expires=" + date.toUTCString();
				document.cookie = "uxbuilder=disabled" + expires + "; path=/";

				$( '#uxbuilder-enable-disable a' ).removeClass( 'nav-tab-active' );
				$( 'html' ).attr( 'data-uxbuilder', 'disabled' );
				$( '#disable-uxbuilder' ).addClass( 'nav-tab-active' );
				$( window ).resize();
			}
		}
	});
})(jQuery)