;(function($){
	$(document).ready( function($) {
		qppr_open_pointer(0);
		function qppr_open_pointer(i) {
			pointer = qpprPointer.pointers[i];
			options = $.extend( pointer.options, {
				close: function() {
					$.post( ajaxurl, {
						pointer: pointer.pointer_id,
						action: 'dismiss-wp-pointer'
					});
				}
			});
			$(pointer.target).pointer( options ).pointer('open');
		}
	});
})(jQuery);