(function($){
	$(function(){
		//caches a jQuery object containing the header element
		var bumperredcon = $(".noBumper");
		//the data-spy functionality for bootstrap stopped working, prob conflict with caching. Disable dataspy, rely only on this JS to add/remove the .affix class.
		var bumperwhitepart = $(".noaffix");
		$(window).scroll(function() {
			var scroll = $(window).scrollTop();
	
			if (scroll >= 100) {
				bumperredcon.removeClass('noBumper').addClass("navBumper");
				bumperwhitepart.removeClass('noaffix').addClass("affix");
			} else {
				bumperredcon.removeClass("navBumper").addClass('noBumper');
				bumperwhitepart.removeClass("affix").addClass('noaffix');
			}
		});	
	});
})(jQuery);
