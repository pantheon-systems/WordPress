

(function($) {
$(function() {
	if( !window.countryDDLs )
		return;
		
	for( var i=0; i<window.countryDDLs.length; i++ ) {
		$('#'+window.countryDDLs[i]).change(function() {
			var v = $(this).val();
			
			if( !v )
				return;
			
			var r = $('option[value="'+v+'"]', this).attr('data-redirect');
			
			if( !r )
				return;
			
			document.location.href = r;
		});
	}
});
})(jQuery);
