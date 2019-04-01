(function( $ ) {
	'use strict';

	$('.ewc-filter-charity, .ewc-filter-range').live('change', function(){
		var filter = $(this).val();
		document.location.href = 'admin.php?page=ewc_list_table' + filter;
	});

})( jQuery );
