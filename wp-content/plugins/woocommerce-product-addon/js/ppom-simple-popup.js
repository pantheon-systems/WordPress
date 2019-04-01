/*
 * jQuery Simple Popup Window Plugin 1.0
*/

'use strict';

(function($) {
	 
	// Plugin name and prefix 
	var pluginName = 'megapopup';
	var prefix     = 'ppom-popup';

	$(document).on('click', '[data-model-id]', function(e){
		e.preventDefault();
		var popup_id = $(this).attr('data-model-id');
		console.log($(this).data());
		$('#'+popup_id).megapopup($(this).data());
	});

	// Init Plugin
    $.fn[pluginName] = function(options) {
        
        
        var defaults = {  
		    backgroundclickevent: true,
		    popupcloseclass: prefix+'-close-js',
		    bodycontroller: prefix+'-open'
    	}; 
    	
        //Extend popup options
        var options = $.extend({}, defaults, options); 
	
        return this.each(function() {
        
 			// Global Variables
        	var modal = $(this),
				modalBG = $('.'+prefix+'-bg-controler');

			// Popup background show
			if(modalBG.length == 0) {
				modalBG = $('<div class="'+prefix+'-bg-controler" />').appendTo('body');
			}		    

			// open popup
			modal.bind(prefix+':open', function () {

				$('body').addClass(options.bodycontroller);
				modal.css({'display':'block',});
				modalBG.fadeIn();
				modal.animate({
					"top": '0px',
					"opacity" : 1
				}, 0);					

			}); 	

			// close popup
			modal.bind(prefix+':close', function () {

				$('body').removeClass(options.bodycontroller);
				modalBG.fadeOut();
				modal.animate({
					"top": '0px',
					"opacity" : 0
				}, 0, function() {
					modal.css({'display':'none'});
				});			
			});     
   	
        	//Open Modal Immediately
    		modal.trigger(prefix+':open');
			
			// close popup listner
			var closeButton = $('.' + options.popupcloseclass).bind('click.modalEvent', function (e) {
			  modal.trigger(prefix+':close');
			  e.preventDefault();
			});
			
			// disable backgroundclickevent close
			if(options.backgroundclickevent) {
				modalBG.css({"cursor":"pointer"})
				modalBG.bind('click.modalEvent', function () {
				  modal.trigger(prefix+':close')
				});
			}
			
        });
    }

})(jQuery);