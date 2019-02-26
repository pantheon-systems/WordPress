/*
 * jQuery Orbit Plugin 1.4.0
 * www.ZURB.com/playground
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/

(function($) {
  'use strict';
  $.fn.findFirstImage = function () {
    return this.first()
            .find('img')
            .andSelf().filter('img')
            .first();
  };

  var ORBIT = {

    defaults: {
      animation: 'horizontal-push',     // fade, horizontal-slide, vertical-slide, horizontal-push, vertical-push
      animationSpeed: 450,        // how fast animtions are
      timer: true,            // true or false to have the timer
      advanceSpeed: 4000,         // if timer is enabled, time between transitions
      pauseOnHover: false,        // if you hover pauses the slider
      startClockOnMouseOut: false,    // if clock should start on MouseOut
      startClockOnMouseOutAfter: 1000,  // how long after MouseOut should the timer start again
      directionalNav: true,         // manual advancing directional navs
      directionalNavRightText: 'Right', // text of right directional element for accessibility
      directionalNavLeftText: 'Left', // text of left directional element for accessibility
      captions: true,           // do you want captions?
      captionAnimation: 'fade',       // fade, slideOpen, none
      captionAnimationSpeed: 600,     // if so how quickly should they animate in
      resetTimerOnClick: false,      // true resets the timer instead of pausing slideshow progress on manual navigation
      bullets: false,           // true or false to activate the bullet navigation
      bulletThumbs: false,        // thumbnails for the bullets
      bulletThumbLocation: '',      // location from this file where thumbs will be
      afterSlideChange: $.noop,   // empty function
      afterLoadComplete: $.noop, //callback to execute after everything has been loaded
      fluid: true,
      centerBullets: true    // center bullet nav with js, turn this off if you want to position the bullet nav manually
    },

    activeSlide: 0,
    numberSlides: 0,
    orbitWidth: null,
    orbitHeight: null,
    locked: null,
    timerRunning: null,
    degrees: 0,
    wrapperHTML: '<div class="orbit-wrapper" />',
    timerHTML: '<div class="timer"><span class="mask"><span class="rotator"></span></span><span class="pause"></span></div>',
    captionHTML: '<div class="orbit-caption"></div>',
    directionalNavHTML: '<div class="slider-nav"><span class="right"></span><span class="left"></span></div>',
    bulletHTML: '<ul class="orbit-bullets"></ul>',

    init: function (element, options) {
      var $imageSlides,
          imagesLoadedCount = 0,
          self = this;

      // Bind functions to correct context
      this.clickTimer = $.proxy(this.clickTimer, this);
      this.addBullet = $.proxy(this.addBullet, this);
      this.resetAndUnlock = $.proxy(this.resetAndUnlock, this);
      this.stopClock = $.proxy(this.stopClock, this);
      this.startTimerAfterMouseLeave = $.proxy(this.startTimerAfterMouseLeave, this);
      this.clearClockMouseLeaveTimer = $.proxy(this.clearClockMouseLeaveTimer, this);
      this.rotateTimer = $.proxy(this.rotateTimer, this);

      this.options = $.extend({}, this.defaults, options);
      if (this.options.timer === 'false') this.options.timer = false;
      if (this.options.captions === 'false') this.options.captions = false;
      if (this.options.directionalNav === 'false') this.options.directionalNav = false;

      this.$element = $(element);
      this.$wrapper = this.$element.wrap(this.wrapperHTML).parent();
      this.$slides = this.$element.children('img, a, div');

      this.$element.bind('orbit.next', function () {
        self.shift('next');
      });

      this.$element.bind('orbit.prev', function () {
        self.shift('prev');
      });

      this.$element.bind('orbit.goto', function (event, index) {
        self.shift(index);
      });

      this.$element.bind('orbit.start', function (event, index) {
        self.startClock();
      });

      this.$element.bind('orbit.stop', function (event, index) {
        self.stopClock();
      });

      $imageSlides = this.$slides.filter('img');

      if ($imageSlides.length === 0) {
        this.loaded();
      } else {
        $imageSlides.bind('imageready', function () {
          imagesLoadedCount += 1;
          if (imagesLoadedCount === $imageSlides.length) {
            self.loaded();
          }
        });
      }
    },

    loaded: function () {
      this.$element
        .addClass('orbit')
        .css({width: '1px', height: '1px'});

      this.$slides.addClass('orbit-slide');

      this.setDimentionsFromLargestSlide();
      this.updateOptionsIfOnlyOneSlide();
      this.setupFirstSlide();

      if (this.options.timer) {
        this.setupTimer();
        this.startClock();
      }

      if (this.options.captions) {
        this.setupCaptions();
      }

      if (this.options.directionalNav) {
        this.setupDirectionalNav();
      }

      if (this.options.bullets) {
        this.setupBulletNav();
        this.setActiveBullet();
      }

      this.options.afterLoadComplete.call(this);
    },

    currentSlide: function () {
      return this.$slides.eq(this.activeSlide);
    },

    setDimentionsFromLargestSlide: function () {
      //Collect all slides and set slider size of largest image
      var self = this,
          $fluidPlaceholder;

      self.$element.add(self.$wrapper).width(this.$slides.first().width());
      self.$element.add(self.$wrapper).height(this.$slides.first().height());
      self.orbitWidth = this.$slides.first().width();
      self.orbitHeight = this.$slides.first().height();
      $fluidPlaceholder = this.$slides.first().findFirstImage().clone();


      this.$slides.each(function () {
        var slide = $(this),
            slideWidth = slide.width(),
            slideHeight = slide.height();

        if (slideWidth > self.$element.width()) {
          self.$element.add(self.$wrapper).width(slideWidth);
          self.orbitWidth = self.$element.width();
        }
        if (slideHeight > self.$element.height()) {
          self.$element.add(self.$wrapper).height(slideHeight);
          self.orbitHeight = self.$element.height();
          $fluidPlaceholder = $(this).findFirstImage().clone();
        }
        self.numberSlides += 1;
      });

      if (this.options.fluid) {
        if (typeof this.options.fluid === "string") {
          $fluidPlaceholder = $('<img src="http://placehold.it/' + this.options.fluid + '" />')
        }

        self.$element.prepend($fluidPlaceholder);
        $fluidPlaceholder.addClass('fluid-placeholder');
        self.$element.add(self.$wrapper).css({width: 'inherit'});
        self.$element.add(self.$wrapper).css({height: 'inherit'});

        $(window).bind('resize', function () {
          self.orbitWidth = self.$element.width();
          self.orbitHeight = self.$element.height();
        });
      }
    },

    //Animation locking functions
    lock: function () {
      this.locked = true;
    },

    unlock: function () {
      this.locked = false;
    },

    updateOptionsIfOnlyOneSlide: function () {
      if(this.$slides.length === 1) {
        this.options.directionalNav = false;
        this.options.timer = false;
        this.options.bullets = false;
      }
    },

    setupFirstSlide: function () {
      //Set initial front photo z-index and fades it in
      var self = this;
      
      //webkit video fix
      this.$slides.find('.video').hide();
      if( this.$slides.first().find('.video').length > 0) { this.$slides.first().find('.video').show(); this.$slides.first().find('.mejs-mediaelement').css('visibility','hidden'); };
       
      if( $('#featured').attr('data-caption-animation') == '0' ){
  		$('#featured').find('.post-title').children().css({'opacity':1, 'margin-top': 0});
  	  }
      
      
      //reset opacity on all slides
	  this.$slides.css({"opacity" : 0});
	  this.$slides.first().css({"opacity" : 1});
	  
      this.$slides.first()
        .css({"z-index" : 3})
        .fadeIn(function() {
        	 
            if( $('#featured').attr('data-caption-animation') == '1' ){
            	
               $('#featured').find('.post-title').children().css({'opacity':0, 'margin-top': 20});
	           self.$slides.first().find('.post-title').children().each(function(i){
					$(this).stop().delay(i*320).animate({
						'opacity' : 1,
						'margin-top' : 0
					},600,'easeOutSine');
				}); 
           	}
           	
          //brings in all other slides IF css declares a display: none
          self.$slides.css({"display":"block"})
      });
    },

    startClock: function () {
      var self = this;

      if(!this.options.timer) {
        return false;
      }

      if (this.$timer.is(':hidden')) {
        this.clock = setInterval(function () {
          self.$element.trigger('orbit.next');
        }, this.options.advanceSpeed);
      } else {
        this.timerRunning = true;
        this.$pause.removeClass('active')
        this.clock = setInterval(this.rotateTimer, this.options.advanceSpeed / 180);
      }
    },

    rotateTimer: function (reset) {
      var degreeCSS = "rotate(" + this.degrees + "deg)"
      this.degrees += 2;
      this.$rotator.css({
        "-webkit-transform": degreeCSS,
        "-moz-transform": degreeCSS,
        "-o-transform": degreeCSS
      });
      if(this.degrees > 180) {
        this.$rotator.addClass('move');
        this.$mask.addClass('move');
      }
      if(this.degrees > 360 || reset) {
        this.$rotator.removeClass('move');
        this.$mask.removeClass('move');
        this.degrees = 0;
        this.$element.trigger('orbit.next');
      }
    },

    stopClock: function () {
      if (!this.options.timer) {
        return false;
      } else {
        this.timerRunning = false;
        clearInterval(this.clock);
        this.$pause.addClass('active');
      }
    },

    setupTimer: function () {
      this.$timer = $(this.timerHTML);
      this.$wrapper.append(this.$timer);

      this.$rotator = this.$timer.find('.rotator');
      this.$mask = this.$timer.find('.mask');
      this.$pause = this.$timer.find('.pause');

      this.$timer.click(this.clickTimer);

      if (this.options.startClockOnMouseOut) {
        this.$wrapper.mouseleave(this.startTimerAfterMouseLeave);
        this.$wrapper.mouseenter(this.clearClockMouseLeaveTimer);
      }

      if (this.options.pauseOnHover) {
        this.$wrapper.mouseenter(this.stopClock);
      }
    },

    startTimerAfterMouseLeave: function () {
      var self = this;

      this.outTimer = setTimeout(function() {
        if(!self.timerRunning){
          self.startClock();
        }
      }, this.options.startClockOnMouseOutAfter)
    },

    clearClockMouseLeaveTimer: function () {
      clearTimeout(this.outTimer);
    },

    clickTimer: function () {
      if(!this.timerRunning) {
          this.startClock();
      } else {
          this.stopClock();
      }
    },

    setupCaptions: function () {
      this.$caption = $(this.captionHTML);
      this.$wrapper.append(this.$caption);
      this.setCaption();
    },

    setCaption: function () {
      var captionLocation = this.currentSlide().attr('data-caption'),
          captionHTML;

      if (!this.options.captions) {
        return false;
      }

      //Set HTML for the caption if it exists
      if (captionLocation) {
        captionHTML = $(captionLocation).html(); //get HTML from the matching HTML entity
        this.$caption
          .attr('id', captionLocation) // Add ID caption TODO why is the id being set?
          .html(captionHTML); // Change HTML in Caption
          //Animations for Caption entrances
        switch (this.options.captionAnimation) {
          case 'none':
            this.$caption.show();
            break;
          case 'fade':
            this.$caption.fadeIn(this.options.captionAnimationSpeed);
            break;
          case 'slideOpen':
            this.$caption.slideDown(this.options.captionAnimationSpeed);
            break;
        }
      } else {
        //Animations for Caption exits
        switch (this.options.captionAnimation) {
          case 'none':
            this.$caption.hide();
            break;
          case 'fade':
            this.$caption.fadeOut(this.options.captionAnimationSpeed);
            break;
          case 'slideOpen':
            this.$caption.slideUp(this.options.captionAnimationSpeed);
            break;
        }
      }
    },

    setupDirectionalNav: function () {
      var self = this,
          $directionalNav = $(this.directionalNavHTML);

      $directionalNav.find('.right').html(this.options.directionalNavRightText);
      $directionalNav.find('.left').html(this.options.directionalNavLeftText);

      this.$wrapper.append($directionalNav);

      this.$wrapper.find('.slider-nav > span.left').click(function () {
        self.stopClock();
        if (self.options.resetTimerOnClick) {
          self.rotateTimer(true);
          self.startClock();
        }
        $(".mejs-pause").trigger('click');
        self.$element.trigger('orbit.prev');
      });

      this.$wrapper.find('.slider-nav > span.right').click(function () {
        self.stopClock();
        if (self.options.resetTimerOnClick) {
          self.rotateTimer(true);
          self.startClock();
        }
        $(".mejs-pause").trigger('click');
        self.$element.trigger('orbit.next');
      });
      
      this.$wrapper.find('.jp-play, .more-info a, #featured article .post-title > a, .video, .mejs-button, button, .mejs-controls, .mejs-playpause-button').on('click',function () {
      	self.stopClock(); 
      });
    
      this.$wrapper.find('button, .mejs-controls, .mejs-playpause-button').mousedown(function(){
     		self.stopClock(); 
     		$(this).parents('.video').find('.mejs-mediaelement').css('visibility','visible');
      });
      
    },

    setupBulletNav: function () {
      this.$bullets = $(this.bulletHTML);
      this.$wrapper.append(this.$bullets);
      this.$slides.each(this.addBullet);
      this.$element.addClass('with-bullets');
      if (this.options.centerBullets) this.$bullets.css('margin-left', -this.$bullets.width() / 2);
    },

    addBullet: function (index, slide) {
      var position = index + 1,
          $li = $('<li>' + (position) + '</li>'),
          thumbName,
          self = this;

      if (this.options.bulletThumbs) {
        thumbName = $(slide).attr('data-thumb');
        if (thumbName) {
          $li
            .addClass('has-thumb')
            .css({background: "url(" + this.options.bulletThumbLocation + thumbName + ") no-repeat"});;
        }
      }
      this.$bullets.append($li);
      $li.data('index', index);
      $li.click(function () {
        self.stopClock();
        if (self.options.resetTimerOnClick) {
          self.rotateTimer(true);
          self.startClock();
        }
        self.$element.trigger('orbit.goto', [$li.data('index')])
      });
    },
 
    setActiveBullet: function () {
      if(!this.options.bullets) { return false; } else {
        this.$bullets.find('li')
          .removeClass('active')
          .eq(this.activeSlide)
          .addClass('active');
      }
    },

    resetAndUnlock: function () {
      //reset caption animation
      if( $('#featured').attr('data-caption-animation') == '1' ){
      	this.$slides.find('.post-title').children().stop(true,true).css({'opacity':0, 'margin-top':20});
	  }
	  
      this.$slides.eq(this.prevActiveSlide).css({"z-index" : 1});
      this.options.afterSlideChange.call(this, this.$slides.eq(this.prevActiveSlide), this.$slides.eq(this.activeSlide));
      this.unlock();
    },

    shift: function (direction) { 
     
      if($(this.$slides).filter(':animated').length == 0 && $(this.$slides).find('h2').filter(':animated').length == 0 && $(this.$slides).find('a').filter(':animated').length == 0 && $(this.$slides).find('div').filter(':animated').length == 0 && $(this.$slides).find('span').filter(':animated').length == 0) {
	
      var slideDirection = direction;

      //remember previous activeSlide
      this.prevActiveSlide = this.activeSlide;

      //exit function if bullet clicked is same as the current image
      if (this.prevActiveSlide == slideDirection) { return false; }

      if (this.$slides.length == "1") { return false; }
      if (!this.locked) {
        this.lock();
        //deduce the proper activeImage
        if (direction == "next") {
          this.activeSlide++;
          if (this.activeSlide == this.numberSlides) {
              this.activeSlide = 0;
          }
        } else if (direction == "prev") {
          this.activeSlide--
          if (this.activeSlide < 0) {
            this.activeSlide = this.numberSlides - 1;
          }
        } else {
          this.activeSlide = direction;
          if (this.prevActiveSlide < this.activeSlide) {
            slideDirection = "next";
          } else if (this.prevActiveSlide > this.activeSlide) {
            slideDirection = "prev"
          }
        }

        //set to correct bullet
        this.setActiveBullet();

        //set previous slide z-index to one below what new activeSlide will be
        this.$slides
          .eq(this.prevActiveSlide)
          .css({"z-index" : 2});

        //fade
        if (this.options.animation == "fade" ) {
         
          var $that = this;
          var $currentSlide = this.$slides.eq(this.activeSlide);
          var timeout = 0;
          var count = 0;
          var infoLength = this.$slides.eq(this.prevActiveSlide).find('.post-title').children().length;
          var currentPos = this.$slides.eq(this.prevActiveSlide).find('article').css('top');
          this.$slides
            .eq(this.prevActiveSlide).find('.post-title').children().each(function(i){
            	
            	count++; 
            	infoLength--;
            	
            	//if aniamtion is on
            	if( $('#featured').attr('data-caption-animation') == '1' ){
            		
	            	//fadeOut in reverse
	          		 $(this).stop().delay(infoLength*150).animate({
						'opacity' : 0
					},300,'easeOutSine');
					
					timeout = count*300 - count*150;
					
				}

            });
            
            //switch slides when the caption is faded out
            var currentTimeout = setTimeout(function(){
            	
            	//webkit video fix..
				if( $('#featured').attr('data-caption-animation') == '1' && $(window).width() > 1000){
            		$that.$slides.find('.mejs-mediaelement').css('visibility','hidden');
            		$that.$slides.find('.video').hide();
            		$that.$slides.find('.mejs-mediaelement').parents('.video').show();
            	}

            	
            	if( $('#featured').attr('data-caption-animation') == '1' ){
	          		$that.$slides.find('.video .jp-jplayer > img').show();	
	          	}
	          	
	          	if($currentSlide.find('.video').length > 0) { $currentSlide.find('.video').show(); $currentSlide.find('.mejs-poster').show(); };
	          	
				
				$that.$slides
	            .eq($that.activeSlide)
	            .find('article').css({"top" : currentPos});
	            
            	$that.$slides
	            .eq($that.activeSlide)
	            .css({"opacity" : 0, "z-index" : 3})
	            .animate({"opacity" : 1}, $that.options.animationSpeed, function(){
				
				
				
					//reset opacity on all slides
					$that.$slides.css({"opacity" : 0});
					$that.$slides.eq($that.activeSlide).css({"opacity" : 1});
					
					//stop any previously playing video
					if($that.$slides.eq($that.prevActiveSlide).find('.jp-jplayer-video').length>0){
						$that.$slides.find('video').hide();
						$that.$slides.eq($that.prevActiveSlide).find('.jp-jplayer-video').jPlayer("stop");
					}
					
	            	//reset caption animation
	            	if( $('#featured').attr('data-caption-animation') == '1' ){
	    	  			$that.$slides.find('.post-title').children().css({'opacity':0, 'margin-top':20});
	    	  		}
	    	  		
					
					if( $('#featured').attr('data-caption-animation') == '0' ){
	            		$that.$slides.find('video').hide();
	            		$that.$slides.find('.video').hide();
	            		$that.$slides.find('.video .jp-jplayer > img').show();	
	            		if($currentSlide.find('.video').length > 0) { $currentSlide.find('.video').show(); $currentSlide.find('.mejs-poster').show(); $currentSlide.find('.mejs-mediaelement').css('visibility','hidden');};
	            	}
	            	
	    	  		$that.resetAndUnlock();
	    	  		
	    	  		$(window).trigger('resize'); 
	    	  		
	    	  		if( $('#featured').attr('data-caption-animation') == '1' ){
	    	  			$currentSlide.find('.post-title').children().each(function(i){
							$(this).stop().delay(i*270).animate({
								'opacity' : 1,
								'margin-top' : 0
							},500,'easeOutSine');
						});
	    	  		}
					
	
	            });
            	
            }, timeout)
          
        }

        //horizontal-slide
        if (this.options.animation == "horizontal-slide") {
          if (slideDirection == "next") {
            this.$slides
              .eq(this.activeSlide)
              .css({"left": this.orbitWidth, "z-index" : 3})
              .animate({"left" : 0}, this.options.animationSpeed, this.resetAndUnlock);
          }
          if (slideDirection == "prev") {
            this.$slides
              .eq(this.activeSlide)
              .css({"left": -this.orbitWidth, "z-index" : 3})
              .animate({"left" : 0}, this.options.animationSpeed, this.resetAndUnlock);
          }
        }

        //vertical-slide
        if (this.options.animation == "vertical-slide") {
          if (slideDirection == "prev") {
            this.$slides
              .eq(this.activeSlide)
              .css({"top": this.orbitHeight, "z-index" : 3})
              .animate({"top" : 0}, this.options.animationSpeed, this.resetAndUnlock);
          }
          if (slideDirection == "next") {
            this.$slides
              .eq(this.activeSlide)
              .css({"top": -this.orbitHeight, "z-index" : 3})
              .animate({"top" : 0}, this.options.animationSpeed, this.resetAndUnlock);
          }
        }

        //horizontal-push
        if (this.options.animation == "horizontal-push") {
          if (slideDirection == "next") {
            this.$slides
              .eq(this.activeSlide)
              .css({"left": this.orbitWidth, "z-index" : 3})
              .animate({"left" : 0}, this.options.animationSpeed, this.resetAndUnlock);
            this.$slides
              .eq(this.prevActiveSlide)
              .animate({"left" : -this.orbitWidth}, this.options.animationSpeed);
          }
          if (slideDirection == "prev") {
            this.$slides
              .eq(this.activeSlide)
              .css({"left": -this.orbitWidth, "z-index" : 3})
              .animate({"left" : 0}, this.options.animationSpeed, this.resetAndUnlock);
            this.$slides
              .eq(this.prevActiveSlide)
              .animate({"left" : this.orbitWidth}, this.options.animationSpeed);
          }
        }

        //vertical-push
        if (this.options.animation == "vertical-push") {
          if (slideDirection == "next") {
            this.$slides
              .eq(this.activeSlide)
              .css({top: -this.orbitHeight, "z-index" : 3})
              .animate({top : 0}, this.options.animationSpeed, this.resetAndUnlock);
            this.$slides
              .eq(this.prevActiveSlide)
              .animate({top : this.orbitHeight}, this.options.animationSpeed);
          }
          if (slideDirection == "prev") {
            this.$slides
              .eq(this.activeSlide)
              .css({top: this.orbitHeight, "z-index" : 3})
              .animate({top : 0}, this.options.animationSpeed, this.resetAndUnlock);
            this.$slides
              .eq(this.prevActiveSlide)
              .animate({top : -this.orbitHeight}, this.options.animationSpeed);
          }
        }

        this.setCaption();
        
         }
      }
    }
  };

  $.fn.orbit = function (options) {
    return this.each(function () {
      var orbit = $.extend({}, ORBIT);
      orbit.init(this, options);
    });
  };

})(jQuery);

/*!
 * jQuery imageready Plugin
 * http://www.zurb.com/playground/
 *
 * Copyright 2011, ZURB
 * Released under the MIT License
 */
(function ($) {

  var options = {};

  $.event.special.imageready = {

    setup: function (data, namespaces, eventHandle) {
      options = data || options;
    },

    add: function (handleObj) {
      var $this = $(this),
          src;

      if ( this.nodeType === 1 && this.tagName.toLowerCase() === 'img' && this.src !== '' ) {
        if (options.forceLoad) {
          src = $this.attr('src');
          $this.attr('src', '');
          bindToLoad(this, handleObj.handler);
          $this.attr('src', src);
        } else if ( this.complete || this.readyState === 4 ) {
          handleObj.handler.apply(this, arguments);
        } else {
          bindToLoad(this, handleObj.handler);
        }
      }
    },

    teardown: function (namespaces) {
      $(this).unbind('.imageready');
    }
  };

  function bindToLoad(element, callback) {
    var $this = $(element);

    $this.bind('load.imageready', function () {
       callback.apply(element, arguments);
       $this.unbind('load.imageready');
     });
  }


























/***************** Home Slider ******************/

  var sliderAdvanceSpeed = parseInt($('#featured').attr('data-advance-speed'));
  var sliderAnimationSpeed = parseInt($('#featured').attr('data-animation-speed'));
  var sliderAutoplay = parseInt($('#featured').attr('data-autoplay'));
  
  if( isNaN(sliderAdvanceSpeed) ) { sliderAdvanceSpeed = 5500;}
  if( isNaN(sliderAnimationSpeed) ) { sliderAnimationSpeed = 800;}
  
  var $yPos;
  

  var img_urls=[];
  $('[style*="background"]').each(function() {
      var style = $(this).attr('style');
      var pattern = /background.*?url\('(.*?)'\)/g
      var match = pattern.exec(style);
      if (match) {        
          img_urls.push(match[1]);
      }
  });
  
  var imgArray = [];
  
  for(i=0;i<img_urls.length;i++){
    imgArray[i] = new Image();
    imgArray[i].src = img_urls[i];
  }
   
  
  //home slider height
  var sliderHeight = parseInt($('#featured').attr('data-slider-height'));
  if( isNaN(sliderHeight) ) { sliderHeight = 650 } else { sliderHeight = sliderHeight -12 }; 
  
  ////min height if video
  if( $('#featured .video').length > 0 && sliderHeight < 500) sliderHeight = 500;
  
  function customSliderHeight(){
    if(!$('body').hasClass('mobile')){
      $('#featured').attr('style', 'height: '+sliderHeight+'px !important');
      $('#featured article').css('height',sliderHeight+headerPadding2-23+'px')
    }
    else {
      $('#featured').attr('style', 'height: '+sliderHeight+'px');
    }

    //transparent header fix
    if($('#header-outer[data-transparent-header="true"]').length > 0) $('.orbit-wrapper').addClass('transparent-header');
  }
  
  customSliderHeight();
  
  

  
  //take into account header height when calculating the controls and info positioning 
  var logoHeight = parseInt($('#header-outer').attr('data-logo-height'));
  var headerPadding = parseInt($('#header-outer').attr('data-padding'));
  var headerPadding2 = parseInt($('#header-outer').attr('data-padding'));
  var extraDef = 10;
  var headerResize = ($('body').hasClass('pp-video-function')) ? '1' : $('#header-outer').attr('data-header-resize');
  var headerResizeOffExtra = 0;
  var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
  var usingLogoImage = true;
    var mediaElement = ($('.wp-video-shortcode').length > 0) ? 36 : 0;
    var secondaryHeader = ($('#header-outer').attr('data-using-secondary') == '1') ? 32 : 0 ;
    
  if( isNaN(logoHeight) ) { usingLogoImage = false; logoHeight = 30;}
  if( isNaN(headerPadding) ) { headerPadding = 28; headerPadding2 = 28;}
  if( headerResize.length == 0 ) { extraDef = 0; headerResizeOffExtra = headerPadding2; }
    if( $('header#top #logo img').length == 0 ) { logoHeight = 30; }
    
  var $captionPos = (((sliderHeight-70)/2 - $('div.slider-nav span.left span.white').height()/2) + headerPadding2 - headerResizeOffExtra) - 75;
  var $controlsPos = (((sliderHeight-70)/2 - $('div.slider-nav span.left span.white').height()/2) + logoHeight + headerPadding*2 + extraHeight + secondaryHeader) -10;
  
  var $scrollTop = 0;
  var $videoHeight; 
  
  
  function homeSliderInit(){
    $('#featured').orbit({
           animation: 'fade',
           advanceSpeed: sliderAdvanceSpeed,
           animationSpeed: sliderAnimationSpeed, 
           timer: sliderAutoplay
       });
       
       customSliderHeight();
     sliderAfterSetup();
     
      //test for slider arrows
     if(!$('body').hasClass('mobile')){
      $('.orbit-wrapper #featured article').css('top', ((- $scrollTop / 5)+logoHeight+headerPadding2+headerResizeOffExtra+extraHeight-extraDef+secondaryHeader)  + 'px');
      $('.orbit-wrapper div.slider-nav span.right, .orbit-wrapper div.slider-nav span.left').html('<span class="white"></span><span class="shadow"></span>');
     } else {
      $('.orbit-wrapper div.slider-nav span.right').html('<i class="icon-angle-right"></i>');
      $('.orbit-wrapper div.slider-nav span.left').html('<i class="icon-angle-left"></i>');
     }
  }
  
  //home slider init
  function homeSliderInit2(){
    if($('#featured').length > 0 && $().orbit) {

      $('#featured article .post-title h2 span').show();


      //home slider bg color
      var sliderBackgroundColor = $('#featured').attr('data-bg-color');
      if( sliderBackgroundColor.length == 0 ) sliderBackgroundColor = '#000000'; 
      
      $('#featured article').css('background-color',sliderBackgroundColor);
    
  
       
       var $firstBg = $('#featured').find('.slide:first-child > article').attr('style');

         var pattern = /url\(["']?([^'")]+)['"]?\)/;
         var match = pattern.exec($firstBg);
   
         if (match && match[1].indexOf('.') !== -1) {       
            var slideImg = new Image();
        slideImg.onload = function(){ 
          homeSliderInit();
        }
        slideImg.src = match[1];
       } else {
        homeSliderInit();
       }
   
        ////add hover effect to slider nav
        if($('.slider-nav > span').find('.white').length == 0) {
          $('.slider-nav > span').append('<span class="white"></span><span class="shadow"></span>');  
        }

        ////swipe for home slider
        if($('body').hasClass('mobile')){
          $('#featured h2, #featured .video').swipe({
            swipeRight : function(e) {
            $('.slider-nav .left').trigger('click');
            e.stopImmediatePropagation();
            return false;
           },
           swipeLeft : function(e) {
            $('.slider-nav .right').trigger('click');
            e.stopImmediatePropagation();
            return false;
           }    
          })
        }
     
    
    }
  }
  homeSliderInit2();
  
  
  
  //inital load
  function sliderAfterSetup(){
    //webkit video fix
    $('#featured .mejs-container').css('width',$('#featured .video').width());
    $('#featured .mejs-container').css('height',$('#featured .video').width()/1.7777);
    //$(window).trigger('resize');
    
    $('body:not(.mobile) .orbit-wrapper #featured .orbit-slide:not(".has-video") article .container').css('top', $captionPos +"px");
    $('body:not(.mobile) .orbit-wrapper #featured .orbit-slide.has-video article .container').css('top', $videoHeight +"px");
    $('body:not(.mobile) .orbit-wrapper .slider-nav > span').css('top', $controlsPos +"px");  
    $('body:not(.mobile) .orbit-wrapper #featured .slide article').css({'top': ((- $scrollTop / 5)+logoHeight+headerPadding2+headerResizeOffExtra+extraHeight-extraDef+secondaryHeader)  + 'px' });
    
    //height fix for when resize on scroll if off
    if(!$('body').hasClass('mobile') && headerResize.length == 0){
      $('#featured article').css('height',sliderHeight-32+'px')
    }
    
    $(window).trigger('resize');
  }
  
  
  function videoSlidePos(){
    $('#featured > div').has('.video').each(function(){
      if( $(window).width() > 1300 ) {
        $('#featured .orbit-slide.has-video .video, #featured .orbit-slide.has-video h2').css('top','0');
        $('#featured .orbit-slide.has-video .post-title > a').css('top','10px');

        $videoHeight = ((sliderHeight-28)/2) - ((410-mediaElement)/2) + headerPadding2 - headerResizeOffExtra;
      }
      
      else if( $(window).width() > 1000 && $(window).width() < 1081 ){
        $('#featured .orbit-slide.has-video .video, #featured .orbit-slide.has-video h2').css('top','0');
        $('#featured .orbit-slide.has-video .post-title > a').css('top','10px');
        
        $videoHeight = ((sliderHeight-28)/2) - ((290-mediaElement)/2) + headerPadding2 - headerResizeOffExtra;
      }
      
      else {
        $videoHeight = ((sliderHeight-28)/2) - ((336-mediaElement)/2) +headerPadding2 - headerResizeOffExtra;
      }
  
    });
  }
  
  videoSlidePos();
  
  //dynamic controls and info positioning
  function controlsAndInfoPos(){
    $scrollTop = $(window).scrollTop();
    
    $('body:not(.mobile) .orbit-wrapper #featured .orbit-slide:not(".has-video") article .container').css({ 
      'opacity' : 1-($scrollTop/(sliderHeight-130)),
      'top' : ($scrollTop*-0.2) + $captionPos +"px"
    });
    
    //video slides
    $('body:not(.mobile) .orbit-wrapper #featured .orbit-slide.has-video article .container').css({ 
      'opacity' : 1-($scrollTop/(sliderHeight-130)),
      'top' : ($scrollTop*-0.2) + $videoHeight +"px"
    });
    
    if($('#boxed').length == 0){
      $('body:not(.mobile) .orbit-wrapper .slider-nav > span').css({ 
        'opacity' : 1-($scrollTop/(sliderHeight-130)),
        'top' : ($scrollTop*-0.4) + $controlsPos +"px"
      });
    }
    
  }
  
  controlsInit();
  function controlsInit(){

    if($('#boxed').length > 0) {
      if(1-$scrollTop/(sliderHeight-$controlsPos-20) >= 0){
        $(window).off('scroll',hideControls);
        $(window).on('scroll',showControls);
      } else {
        $(window).off('scroll',showControls);
        $(window).on('scroll',hideControls);
      }
    } else {
      $(window).off('scroll',showControls);
      $(window).off('scroll',hideControls);
    }
  
  }
  
  function showControls(){

    if(1-$scrollTop/(sliderHeight-$controlsPos-20) >= 0){
      $('body:not(.mobile) .orbit-wrapper .slider-nav > span.left').stop(true,true).animate({ 'left' : '0px'},450,'easeOutCubic');
      if($('body').attr('data-smooth-scrolling')=='1'){
        $('body:not(.mobile) .orbit-wrapper .slider-nav > span.right').stop(true,true).animate({ 'right' : '15px'},450,'easeOutCubic');
      } else {
        $('body:not(.mobile) .orbit-wrapper .slider-nav > span.right').stop(true,true).animate({ 'right' : '0px'},450,'easeOutCubic');
      }
      $(window).off('scroll',showControls);
      $(window).on('scroll',hideControls);
    }
  }
  
  function hideControls(){
    
    if(1-$scrollTop/(sliderHeight-$controlsPos-20) < 0){
      $('body:not(.mobile) .orbit-wrapper .slider-nav > span.left').stop(true,true).animate({ 'left' : '-80px'},450,'easeOutCubic');
      $('body:not(.mobile) .orbit-wrapper .slider-nav > span.right').stop(true,true).animate({ 'right' : '-80px'},450,'easeOutCubic');
      $(window).off('scroll',hideControls);
      $(window).on('scroll',showControls);
    }
    
  }
  

  function homeSliderParallaxScroll(){
        
      //hide video to not mess up parallax section
      $('#featured .mejs-mediaelement, #featured .iframe-embed').each(function(){
      
        if( $(this).parents('.container').css('opacity') <= 0){
          $(this).css('visibility','hidden').hide();
        } else {
          $(this).css('visibility','visible').show();
        }
      });
      
      if(!$('body').hasClass('mobile')){
        
        controlsAndInfoPos();
      $('body:not(.mobile) .orbit-wrapper #featured .slide:not(:transparent) article').css({'top': ((- $scrollTop / 5)+logoHeight+headerPadding2+headerResizeOffExtra+extraHeight-extraDef+secondaryHeader)  + 'px' }); 
    
    }
  }

  function homeSliderMobile() {

    if(!$('body').hasClass('mobile')){
      $('.orbit-wrapper #featured article').css('top', ((- $scrollTop / 5)+logoHeight+headerPadding2+headerResizeOffExtra+extraHeight-extraDef+secondaryHeader)  + 'px');
      $('.orbit-wrapper div.slider-nav span.right, .orbit-wrapper div.slider-nav span.left').html('<span class="white"></span><span class="shadow"></span>');
    } else {
      $('.orbit-wrapper div.slider-nav span.right').html('<i class="icon-angle-right"></i>');
      $('.orbit-wrapper div.slider-nav span.left').html('<i class="icon-angle-left"></i>');
    }

    videoSlidePos();
    controlsAndInfoPos();
    customSliderHeight();
    
    //height fix for when resize on scroll if off
    if(!$('body').hasClass('mobile') && headerResize.length == 0){
      $('#featured article').css('height',sliderHeight-32+'px')
    }
    
  }


  if( $('#featured').length > 0){
    
    $(window).off('scroll.hsps');
    $(window).on('scroll.hsps',homeSliderParallaxScroll);
        
    //disable parallax for mobile
    $(window).off('resize.hsps');
    $(window).on('resize.hsps',homeSliderMobile);
    
  }

    
    //webkit self-hosted video fix
    $('.jp-video-container .jp-play, jp-video-container .jp-seek-bar').click(function(){
      $(this).parents('.jp-video-container').prev('.jp-jplayer').find('video').show().css('display','block');
      $(this).parents('.jp-video-container').prev('.jp-jplayer').find('.jp-jplayer > img').hide();
    });
    
    //mobile video more info
    $('#featured .span_12 a.more-info').click(function(){
      if( !$(this).find('.btv').is(":visible")){
        $(this).parent().parent().find('h2, > a').css('opacity',1);
        $(this).parent().parent().find('.video').stop().animate({'top':'-400px'},800,'easeOutCubic');
        $(this).parent().parent().find('h2').stop().animate({'top':'-400px'},800,'easeOutCubic');
        $(this).parent().parent().find('> a').stop().animate({'top':'-380px'},800,'easeOutCubic');
        $(this).find('.btv').show();
        $(this).find('.mi').hide();
      }
      else {
        $(this).parent().parent().find('.video').stop().animate({'top':'0px'},800,'easeOutCubic');
        $(this).parent().parent().find('h2').stop().animate({'top':'0px'},800,'easeOutCubic');
        $(this).parent().parent().find('> a').stop().animate({'top':'0px'},800,'easeOutCubic');
        $(this).find('.mi').show();
        $(this).find('.btv').hide();
      }
      
      return false;
    });


    

}(jQuery));