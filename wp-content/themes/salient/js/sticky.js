(function($) {

  var $headerHeight = $('#header-outer').outerHeight() + 34;	
  var $extraHeight = ($('#wpadminbar').length > 0) ? 28 : 0; //admin bar
  var secondaryHeader = ($('#header-outer').attr('data-using-secondary') == '1') ? 33 : 0 ;
  
  $(window).load(function(){
   	 $headerHeight = $('#header-outer').outerHeight() + 34;	
     $extraHeight = ($('#wpadminbar').length > 0) ? 28 : 0; //admin bar
  });
 
  $.fn.extend({
    stickyMojo: function(options) {

      var settings = $.extend({
        'footerID': '',
        'contentID': '',
        'orientation': 'right'
      }, options);

      var sticky = {
        'el': $(this),  
        'stickyLeft': $(settings.contentID).outerWidth() + $(settings.contentID).offset.left,
        'stickyTop2': $(this).offset().top,
        'stickyHeight': $(this).outerHeight(true),
        'contentHeight': $(settings.contentID).outerHeight(true),
        'win': $(window),
        'breakPoint': $(this).outerWidth(true) + $(settings.contentID).outerWidth(true),
        'marg': parseInt($(this).css('margin-top'), 10)
      };

      var errors = checkSettings();
      cacheElements();

      return this.each(function() {
        buildSticky();
      });

      function buildSticky() { 
        if (!errors.length) {
          sticky.el.css('left', sticky.stickyLeft);

          sticky.win.bind({
            'scroll': stick,
            'resize': function() {
              sticky.el.css('left', sticky.stickyLeft);
              sticky.contentHeight = $(settings.contentID).outerHeight(true);
              sticky.stickyHeight =  sticky.el.outerHeight(true);

              stick();
              toLiveOrDie();
            }
          });
          
          setTimeout(function(){  toLiveOrDie();  },600);
          
        } else {
          if (console && console.warn) {
            console.warn(errors);
          } else {
            alert(errors);
          }
        }
      }
	  
	  //destroy sticky sidebar if the sidebar is shorter than the content area
	  function toLiveOrDie(){

	  	if(parseInt($('#sidebar').height()) + 50 >= parseInt($('.post-area').height())) {
      	 	sticky.win.unbind('scroll', stick);
      	 	sticky.el.removeClass('fixed-sidebar');
      	 	sticky.el.css({
      	 		'position':'relative',
      	 		'top' : 0,
      	 		'margin-left' : 0,
      	 		'bottom' : 0
      	 	});
      	 }
      	 else {
      	 	sticky.win.unbind('scroll', stick);
      	 	sticky.win.bind('scroll', stick);
      	 	sticky.el.addClass('fixed-sidebar');
      	 }

         if($('#sidebar').length > 0) {

           if($(window).scrollTop() < 100) { 
              sticky.stickyTop2 = $('#sidebar').offset().top;
              sticky.marg = parseInt($('#sidebar').css('margin-top'), 10);
            }
         }
	  }
	  
      // Caches the footer and content elements into jquery objects
      function cacheElements() {
        settings.footerID = $(settings.footerID);
        settings.contentID = $(settings.contentID);
      }

      //  Calcualtes the limits top and bottom limits for the sidebar
      function calculateLimits() {

        var $bottomControls = ($('.bottom_controls').length > 0) ? $('.bottom_controls').outerHeight(true) : 0;
        var $ascendComments = ($('.comment-wrap.full-width-section').length > 0) ? $('.comment-wrap.full-width-section').outerHeight(true) : 0;
        var $footerHeight = ($('body[data-footer-reveal="1"]').length == 0) ? $('#footer-outer').height() : 0 ;

        return {
          limit: ($('#ajax-content-wrap').height() + $('#ajax-content-wrap').offset().top) - sticky.stickyHeight - $footerHeight -$headerHeight - $extraHeight - secondaryHeader - $bottomControls - $ascendComments,
          windowTop: sticky.win.scrollTop(),
          stickyTop: sticky.stickyTop2 - sticky.marg - $headerHeight - $extraHeight - secondaryHeader
        }


      }

      // Sets sidebar to fixed position
      function setFixedSidebar() {
        sticky.el.css({
          position: 'fixed',
          right: 'auto',
          top: $headerHeight + $extraHeight + secondaryHeader,
          bottom : 'auto'
        });
      }

      // Determines the sidebar orientation and sets margins accordingly
      function checkOrientation() {
        if (settings.orientation === "left") {
          settings.contentID.css('margin-left', sticky.el.outerWidth(true));
        } else {
          sticky.el.css('margin-left', settings.contentID.outerWidth(true));
        }
      }

      // sets sidebar to a static positioned element
      function setStaticSidebar() {
        sticky.el.css({
          'position': 'static',
          'margin-left': '0px',
          'bottom' : 'auto'
        });
        settings.contentID.css('margin-left', '0px');
      }

      // initiated to stop the sidebar from intersecting the footer
      function setLimitedSidebar(diff) {

        sticky.el.css({
          position: 'absolute',
          top: 'auto',
          right: '0',
          bottom  : -38
        });
      }

      //determines whether sidebar should stick and applies appropriate settings to make it stick
      function stick() {
        var tops = calculateLimits();
        var hitBreakPoint = tops.stickyTop < tops.windowTop && (sticky.win.width() >= sticky.breakPoint);
		    $headerHeight = $('#header-outer').outerHeight() + 34;	
		  

        if (hitBreakPoint) {
          setFixedSidebar();
          checkOrientation();
        } else {
          setStaticSidebar();
        }
        if (tops.limit < tops.windowTop) {

          var diff = tops.limit - tops.windowTop;
          setLimitedSidebar(diff);
        }

      }

      // verifies that all settings are correct
      function checkSettings() {
        var errors = [];
        for (var key in settings) {
          if (!settings[key]) {
            errors.push(settings[key]);
          }
        }
        ieVersion() && errors.push("NO IE 7");
        return errors;
      }

      function ieVersion() {
        if(document.querySelector) {
          return false;
        }
        else {
          return true;
        }
      }
    }
  });
})(jQuery);
