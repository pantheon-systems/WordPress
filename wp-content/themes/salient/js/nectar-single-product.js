(function ($) {

    'use strict';

    var dw, dh, rw, rh, lx, ly;

    var defaults = {

        // The text to display within the notice box while loading the zoom image.
        loadingNotice: 'Loading image',

        // The text to display within the notice box if an error occurs loading the zoom image.
        errorNotice: 'The image could not be loaded',

        // The time (in milliseconds) to display the error notice.
        errorDuration: 2500,

        // Prevent clicks on the zoom image link.
        preventClicks: true,

        // Callback function to execute when the flyout is displayed.
        onShow: undefined,

        // Callback function to execute when the flyout is removed.
        onHide: undefined

    };

    /**
     * EasyZoom
     * @constructor
     * @param {Object} target
     * @param {Object} options
     */
    function EasyZoom(target, options) {
        this.$target = $(target);
        this.opts = $.extend({}, defaults, options);

        if (this.isOpen === undefined) {
            this._init();
        }

        return this;
    }

    /**
     * Init
     * @private
     */
    EasyZoom.prototype._init = function() {
        var self = this;

        this.$link   = this.$target.find('a');
        this.$image  = this.$target.find('img');

        this.$flyout = $('<div class="easyzoom-flyout" />');
        this.$notice = $('<div class="easyzoom-notice" />');

        this.$target
            .on('mouseenter.easyzoom touchstart.easyzoom', function(e) {
                self.isMouseOver = true;

                if (!e.originalEvent.touches || e.originalEvent.touches.length === 1) {
                    e.preventDefault();
                    self.show(e, true);
                }
            })
            .on('mousemove.easyzoom touchmove.easyzoom', function(e) {
                if (self.isOpen) {
                    e.preventDefault();
                    self._move(e);
                }
            })
            .on('mouseleave.easyzoom touchend.easyzoom', function() {
                self.isMouseOver = false;

                if (self.isOpen) {
                    self.hide();
                }
            });

        if (this.opts.preventClicks) {
            this.$target.on('click.easyzoom', 'a', function(e) {
                e.preventDefault();
            });
        }
    };

    /**
     * Show
     * @param {MouseEvent|TouchEvent} e
     * @param {Boolean} testMouseOver
     */
    EasyZoom.prototype.show = function(e, testMouseOver) {
        var w1, h1, w2, h2;
        var self = this;

        if (! this.isReady) {
            this._load(this.$link.attr('href'), function() {
                if (self.isMouseOver || !testMouseOver) {
                    self.show(e);
                }
            });

            return;
        }

        this.$target.append(this.$flyout);

        w1 = this.$target.width();
        h1 = this.$target.height();

        w2 = this.$flyout.width();
        h2 = this.$flyout.height();

        dw = this.$zoom.width() - w2;
        dh = this.$zoom.height() - h2;

        rw = dw / w1;
        rh = dh / h1;

        this.isOpen = true;

        if (this.opts.onShow) {
            this.opts.onShow.call(this);
        }

        if (e) {
            this._move(e);
        }
    };

    /**
     * Load
     * @private
     * @param {String} href
     * @param {Function} callback
     */
    EasyZoom.prototype._load = function(href, callback) {
        var zoom = new Image();

        this.$target.addClass('is-loading').append(this.$notice.text(this.opts.loadingNotice));

        this.$zoom = $(zoom);

        zoom.onerror = $.proxy(function() {
            var self = this;

            this.$notice.text(this.opts.errorNotice);
            this.$target.removeClass('is-loading').addClass('is-error');

            this.detachNotice = setTimeout(function() {
                self.$notice.detach();
                self.detachNotice = null;
            }, this.opts.errorDuration);
        }, this);

        zoom.onload = $.proxy(function() {

            // IE may fire a load event even on error so check the image has dimensions
            if (!zoom.width) {
                return;
            }

            this.isReady = true;

            this.$notice.detach();
            this.$flyout.html(this.$zoom);
            this.$target.removeClass('is-loading').addClass('is-ready');

            callback();
        }, this);

        zoom.style.position = 'absolute';
        zoom.src = href;
    };

    /**
     * Move
     * @private
     * @param {Event} e
     */
    EasyZoom.prototype._move = function(e) {

        if (e.type.indexOf('touch') === 0) {
            var touchlist = e.touches || e.originalEvent.touches;
            lx = touchlist[0].pageX;
            ly = touchlist[0].pageY;
        }
        else {
            lx = e.pageX || lx;
            ly = e.pageY || ly;
        }

        var offset  = this.$target.offset();
        var pt = ly - offset.top;
        var pl = lx - offset.left;
        var xt = Math.ceil(pt * rh);
        var xl = Math.ceil(pl * rw);

        // Close if outside
        if (xl < 0 || xt < 0 || xl > dw || xt > dh) {
            this.hide();
        }
        else {
            this.$zoom.css({
                top:  '' + (xt * -1) + 'px',
                left: '' + (xl * -1) + 'px'
            });
        }

    };

    /**
     * Hide
     */
    EasyZoom.prototype.hide = function() {
        if (this.isOpen) {
            this.$flyout.detach();
            this.isOpen = false;

            if (this.opts.onHide) {
                this.opts.onHide.call(this);
            }
        }
    };

    /**
     * Swap
     * @param {String} standardSrc
     * @param {String} zoomHref
     * @param {String|Array} srcsetStringOrArray (Optional)
     */
    EasyZoom.prototype.swap = function(standardSrc, zoomHref, srcsetStringOrArray) {
        this.hide();
        this.isReady = false;

        if (this.detachNotice) {
            clearTimeout(this.detachNotice);
        }

        if (this.$notice.parent().length) {
            this.$notice.detach();
        }

        if ($.isArray(srcsetStringOrArray)) {
            srcsetStringOrArray = srcsetStringOrArray.join();
        }

        this.$target.removeClass('is-loading is-ready is-error');
        this.$image.attr({
            src: standardSrc,
            srcset: srcsetStringOrArray
        });
        this.$link.attr('href', zoomHref);
    };

    /**
     * Teardown
     */
    EasyZoom.prototype.teardown = function() {
        this.hide();

        this.$target.removeClass('is-loading is-ready is-error').off('.easyzoom');

        if (this.detachNotice) {
            clearTimeout(this.detachNotice);
        }

        delete this.$link;
        delete this.$zoom;
        delete this.$image;
        delete this.$notice;
        delete this.$flyout;

        delete this.isOpen;
        delete this.isReady;
    };

    // jQuery plugin wrapper
    $.fn.easyZoom = function(options) {
        return this.each(function() {
            var api = $.data(this, 'easyZoom');

            if (!api) {
                $.data(this, 'easyZoom', new EasyZoom(this, options));
            }
            else if (api.isOpen === undefined) {
                api._init();
            }
        });
    };

    // AMD and CommonJS module compatibility
    if (typeof define === 'function' && define.amd){
        define(function() {
            return EasyZoom;
        });
    }
    else if (typeof module !== 'undefined' && module.exports) {
        module.exports = EasyZoom;
    }

})(jQuery);




jQuery(document).ready(function($){
		


  //init easyZoom	
  if(!$('body').hasClass('mobile') && !$().zoom ){
    var $easyzoom = $(".easyzoom").easyZoom({
      preventClicks: true,
      loadingNotice: ' ',
      errorNotice: ' '
    });
    
    if($('.easyzoom').length > 0) {
      var easyzoom_api = $easyzoom.data('easyZoom');
      
      $("table.variations").on('change', 'select', function() {
        easyzoom_api.teardown();
        easyzoom_api._init();
      });
    }
  }
  
  
  
  /*helper function to scroll the page in an animated manner*/
  function nectar_scrollToY(scrollTargetY, speed, easing) {
  
  		var scrollY = window.scrollY || document.documentElement.scrollTop,
  				scrollTargetY = scrollTargetY || 0,
  				speed = speed || 2000,
  				easing = easing || 'easeOutSine',
  				currentTime = 0;
  
  		var time = Math.max(.1, Math.min(Math.abs(scrollY - scrollTargetY) / speed, .8));
  
  
  		var easingEquations = {
  						easeInOutQuint: function (pos) {
  								if ((pos /= 0.5) < 1) {
  										return 0.5 * Math.pow(pos, 5);
  								}
  								return 0.5 * (Math.pow((pos - 2), 5) + 2);
  						}
  				};
  
  
  		function tick() {
  				currentTime += 1 / 60;
  
  				var p = currentTime / time;
  				var t = easingEquations[easing](p);
  
  				if (p < 1) {
  						requestAnimationFrame(tick);
  
  						window.scrollTo(0, scrollY + ((scrollTargetY - scrollY) * t));
  				} else {
  						window.scrollTo(0, scrollTargetY);
  				}
  		}
  
  		tick();
  }  

	
  
function initZoomForTarget( zoomTarget ) {


  var galleryWidth = $('.woocommerce-product-gallery').width(),
    zoomEnabled  = false;

  $( zoomTarget ).each( function( index, target ) {
    var image = zoomTarget.find( 'img' );
    if ( image.data( 'large_image_width' ) > galleryWidth ) {
      zoomEnabled = true;
      return false;
    }
  } );

  // But only zoom if the img is larger than its container.
  if ( zoomEnabled ) {
    var zoom_options = $.extend( {
      touch: false
    }, wc_single_product_params.zoom_options );

    if ( 'ontouchstart' in document.documentElement ) {
      if($('[data-gallery-style="left_thumb_sticky"]').length > 0 && $(window).width() > 1000) {
        
      } else {
        zoom_options.on = 'click';
      }
    }

    zoomTarget.trigger( 'zoom.destroy' );
    zoomTarget.zoom( zoom_options );
  }
};
  

var $mainProdSlider = false;
var $thumbProdSlider = false;

function nectarWooProdSliderInit() {
  
		//init flickity		
		$mainProdSlider = $('.product div.images .flickity.product-slider .slider').flickity({
      draggable: true,
      imagesLoaded: true,
      prevNextButtons: false,
      pageDots: false,
      resize: true,
      adaptiveHeight: true,
      
      on: {
        change: function( index ) {
          if($().zoom) {
            initZoomForTarget(this.$element.find('.flickity-slider > .slide:eq('+index+')'));
          }
          this.$element.find('.flickity-slider > .slide').removeClass('flex-active-slide');
          this.$element.find('.flickity-slider > .slide:eq('+index+')').addClass('flex-active-slide');
        }
      }
      
    });
    
    $thumbProdSlider = $('.product div .flickity.product-thumbs .slider').flickity({
      asNavFor: '.images .flickity.product-slider .slider',
      contain: true,
      resize: true,
      groupCells: true,
      adaptiveHeight: true,
      imagesLoaded: true,
      prevNextButtons: true,
      cellAlign: 'left',
      pageDots: false
    });
    
  }
  
  
  function nectarWooProdSliderDestroy() {
    $mainProdSlider.flickity('destroy');
    $thumbProdSlider.flickity('destroy');
  } 
  
  
  function leftAlignedRelationsInit() {
    //left aligned - thumbnail relation
    $('[data-gallery-style="left_thumb_sticky"] .flickity.product-slider .slide').each(function(i){
      i = i + 1;
      $(this).find('.woocommerce-product-gallery__image').attr('id','nectar_woo_gallery_slide_'+ i);
      
      //zoom
      initZoomForTarget($(this));
    });
    
    $('[data-gallery-style="left_thumb_sticky"] .flickity.product-thumbs .thumb').each(function(i){
      i = i + 1;
      $(this).find('.thumb-inner').wrapInner('<a class="skip-hash" href="#nectar_woo_gallery_slide_'+ i +'" />');
      $(this).find('.thumb-inner a').on('click',function(e){ e.preventDefault(); });
    });
    

    //first thumb active
    if($('.product[data-gallery-style="left_thumb_sticky"] .product-thumbs .thumb-inner > a.active').length == 0) {
      $('.product[data-gallery-style="left_thumb_sticky"] .product-thumbs .thumb:first-child .thumb-inner > a').addClass('active');
    }

  }
  
  function leftAlignedRelationsDestroy() {
    //left aligned - thumbnail relation
    $('[data-gallery-style="left_thumb_sticky"] .flickity.product-slider .slide').each(function(i){
      $(this).find('.woocommerce-product-gallery__image').attr('id','');
    });
    
    $('[data-gallery-style="left_thumb_sticky"] .flickity.product-thumbs .thumb').each(function(i){
      $(this).find('.thumb-inner > a > img').unwrap();
    });
    
  }
  
  var leftAlignedScrollTop = $(window).scrollTop();
  var headerSpace = $('#header-space').height();
  
  function leftAlignedActive() {
     
     leftAlignedScrollTop = $(window).scrollTop();
     
     var closestToTop = $('.product[data-gallery-style="left_thumb_sticky"] .images .slide:first-child');
     $('.product[data-gallery-style="left_thumb_sticky"] .images .slide').each(function(){
       if($(this).offset().top - leftAlignedScrollTop < headerSpace + 250) {
         closestToTop = $(this);
       }
     });
     

     var id = closestToTop.find('.woocommerce-product-gallery__image').attr('id');

     if($('.product-thumbs .thumb-inner').find('a[href="#'+ id +'"]').length > 0) {
       $('.product-thumbs .thumb-inner a').removeClass('active');
       $('.product-thumbs .thumb-inner').find('a[href="#'+ id +'"]').addClass('active');
     }

    
  }
  
  

  function leftAlignedZoomIcon() {
      if($('body.using-mobile-browser').length == 0) {
     
           if($('.product[data-gallery-style="left_thumb_sticky"] .images .slide').length > 1) {
             $(window).on('scroll',function(){
               requestAnimationFrame(leftAlignedActive);
             });
           }
           
           var zoomMouseTimeout;
           
           $('body').on('mouseover','.product[data-gallery-style="left_thumb_sticky"] .images .slide',function(){
             
             var imgWidth = ($(this).find('.woocommerce-product-gallery__image img[data-large_image_width]').length > 0) ? parseInt($(this).find('.woocommerce-product-gallery__image img').data('large_image_width')) : 0;
             
             //only if image is larger than gallery
             if(imgWidth > $('.single-product .images .product-slider').width()) {
                  
                 $(this).removeClass('nectar-no-larger-img');      
                 
                 if(!$(this).hasClass('zoom-img-active')) {
                  
                 } else {
                   clearTimeout(zoomMouseTimeout);
                 }
             } else {
               $(this).addClass('nectar-no-larger-img');
             }
             
           });
           
           $('body').on('mouseleave','.product[data-gallery-style="left_thumb_sticky"] .images .slide',function(){

             if($(this).hasClass('zoom-img-active')) {
               clearTimeout(zoomMouseTimeout);
             }
             
             var $that = $(this);
             
             zoomMouseTimeout = setTimeout(function(){
                $that.removeClass('zoom-img-active');
             },300);
             
           });
           
           $('body').on('mouseup','.product[data-gallery-style="left_thumb_sticky"] .images .slide',function(){
             
             clearTimeout(zoomMouseTimeout);
             
             if($(this).hasClass('zoom-img-active')) {
               $(this).removeClass('zoom-img-active');
             } else {
               $('.product[data-gallery-style="left_thumb_sticky"] .images .slide').removeClass('zoom-img-active');
               $(this).addClass('zoom-img-active');
             }
             
           });
      }
  }
  


  
  function nectarWooProdSliderLiveOrDie() {
    
    if($(window).width() < 1000 && $mainProdSlider == false && $thumbProdSlider == false) {
      
      //carousel
      nectarWooProdSliderInit();  
      leftAlignedRelationsDestroy();
      
    } else if($(window).width() > 1000 && $mainProdSlider && $thumbProdSlider) {
      
      //sticky scroll
      leftAlignedRelationsInit();
      nectarWooProdSliderDestroy();
      
      $mainProdSlider = false;
      $thumbProdSlider = false;
      
    }
    
  }
  
  if($('[data-gallery-style="left_thumb_sticky"]').length > 0) {
    nectarWooProdSliderLiveOrDie();
    leftAlignedRelationsInit();
    $(window).on('resize',nectarWooProdSliderLiveOrDie);
    leftAlignedZoomIcon();
  } else if($('[data-gallery-style="ios_slider"]').length > 0) {
    nectarWooProdSliderInit();
  }
  
  
  
    if($('.slider').length > 0) { 
    	
	    $startingImage = ($('.slide div a:first > img').length > 0) ? $('.slide div a:first > img').attr('src') : '';
	    $startingImageLink = ($('.slide div a:first').length > 0) ? $('.slide div a:first').attr('href') : '';
	    $startingImageThumb = ($('.slider > .thumb:first .thumb-inner img').length > 0) ? $('.slider > .thumb:first .thumb-inner img').attr('src') : $startingImage;

	     $('select[name*="attribute_"]').blur(function(){

        var $that = $(this);
		    var attr_data = $('.variations_form').data('product_variations');

		    if($that.val().length > 0) {

			    //give woo time to update img
		     	setTimeout(function(){

		     		$(attr_data).each(function(i, el){

		     			if(el.image && el.image.src) {
		     			
		     				if(el.image.src == $('.slide div a:first > img').attr('src')){     
  
					    		 if(el.image.url){
  					    		 	$('.slide div a:first').attr('href',el.image.url);
  					    		 	$('.slide div a:first > img').attr('src',el.image.src);
                      
                      if(el.image.gallery_thumbnail_src) {
  					    		 	    $('.product-thumbs .flickity-slider > .thumb:first-child img, .product-thumbs .slider > .thumb:first-child img').attr('src',el.image.gallery_thumbnail_src).removeAttr('srcset');
                          
                          //left aligned
                          if($('[data-gallery-style="left_thumb_sticky"]').length > 0 && $('body.mobile').length == 0) {
                            nectar_scrollToY(0, 700, 'easeInOutQuint');
                          } 
                          //non left aligned
                          else {
                            $thumbProdSlider.flickity( 'selectCell', 0 );
                            $mainProdSlider.flickity( 'selectCell', 0 );
                            $mainProdSlider.flickity( 'resize' );
                          }
                          
                          //update zoom
                          if($().zoom) {
                              if( $('[data-gallery-style="left_thumb_sticky"]').length > 0 && $('body.mobile').length == 0) {
                                initZoomForTarget($('.product-slider .slider > .slide:first-child'));
                              }
                              else if( $('[data-gallery-style="ios_slider"]').length > 0 ) {
                                 initZoomForTarget($('.product-slider .flickity-slider > .slide:first-child'));
                              }
                          }
                          
                      }

					    		  } // if found img url
                    
				    		} // if the sources match

		     			} else {
		     				
		     				//pre 3.0
		     				if(el.image_src == $('.slide div a:first > img').attr('src')){
				    			
					    		 if(el.image_link){
					    		 	$('.slide div a:first').attr('href',el.image_link);
					    		 	$('.slide div a:first > img').attr('src',el.image_src);
					    		 	$('.slider > .thumb:first .thumb-inner img').attr('src',el.image_src).removeAttr('srcset');

					    		 
					    		  }
				    		}

		     			}

				    	
					});		

		     	},30);
          
		 	} else {
        
		 		 $('.slide div a:first').attr('href',$startingImageLink);
    		 	$('.slide div a:first > img').attr('src',$startingImage);
    		 	$('.product-thumbs .flickity-slider > .thumb:first-child img, .product-thumbs .slider > .thumb:first-child img').attr('src',$startingImageThumb).removeAttr('srcset');
          
          //resize
          if($('[data-gallery-style="left_thumb_sticky"]').length > 0 && $('body.mobile').length == 0) {
          
          } else {
            $mainProdSlider.flickity( 'resize' );
          }
          
          //update zoom
          if($().zoom) {
              if( $('[data-gallery-style="left_thumb_sticky"]').length > 0 && $('body.mobile').length == 0) {
                  initZoomForTarget($('.product-slider .slider > .slide:first-child'));
              }
              else if( $('[data-gallery-style="ios_slider"]').length > 0 ) {
                initZoomForTarget($('.product-slider .flickity-slider > .slide:first-child'));
              }
          }
          
		 	}
			 
		 });
		  
		 
	}
  
  
  
  
  function productStickySS() {
		$('.product[data-gallery-style="left_thumb_sticky"] .flickity.product-thumbs, .product[data-gallery-style="left_thumb_sticky"][data-tab-pos="in_sidebar"] .single-product-summary, .product[data-gallery-style="left_thumb_sticky"][data-tab-pos="fullwidth"] .summary.entry-summary').each(function(){

			//padding from top of screen
			$ssExtraTopSpace = 50;
      $secondaryHeaderHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0;
      
			if($('#header-outer[data-remove-fixed="0"]').length > 0 && $('body[data-hhun="1"]').length == 0 && $('#header-outer[data-format="left-header"]').length == 0) { 
			 	$ssExtraTopSpace += $('#header-outer').outerHeight();	
				
				//resize effect
				if($('#header-outer[data-shrink-num][data-header-resize="1"]').length > 0 ) {
					 var shrinkNum = 6;		
					 var headerPadding2 = parseInt($('#header-outer').attr('data-padding')) - parseInt($('#header-outer').attr('data-padding'))/1.8;
					 shrinkNum = $('#header-outer').attr('data-shrink-num');
					 $ssExtraTopSpace -= shrinkNum;
					 $ssExtraTopSpace -= headerPadding2;
				}
				
				//condense
				if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					
						var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
						var $secondaryHeader = $('#header-secondary-outer');
						
						$ssExtraTopSpace = 50;
						$ssExtraTopSpace += $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt($secondaryHeaderHeight);
				}
				

			}

			if($('#wpadminbar').length > 0) {
				$ssExtraTopSpace += $('#wpadminbar').outerHeight();
			}

 			if($('#header-outer').attr('data-using-secondary') == '1') {
				$ssExtraTopSpace += $('#header-secondary-outer').outerHeight();
			}
			
		
			$(this).theiaStickySidebar({
		      additionalMarginTop: $ssExtraTopSpace,
					updateSidebarHeight: false
		    });

		});
    
	}

	if( $().theiaStickySidebar && $('.product[data-gallery-style="left_thumb_sticky"] .product-slider .slide').length > 0 ) {
     
     //wrap for sticky
     var prodImg = $('.single-product .product[data-gallery-style="left_thumb_sticky"] > .single-product-main-image').detach();
     if($('.product[data-gallery-style="left_thumb_sticky"][data-tab-pos="in_sidebar"]').length > 0) {
       var prodDesc = $('.single-product .product[data-gallery-style="left_thumb_sticky"] > .single-product-summary').detach();
     } else {
       var prodDesc = $('.single-product .product[data-gallery-style="left_thumb_sticky"] > .summary.entry-summary').detach();
     }
     
     $('.single-product .product[data-gallery-style="left_thumb_sticky"]').prepend('<div class="nectar-sticky-prod-wrap" />');
     $('.single-product .product[data-gallery-style="left_thumb_sticky"] .nectar-sticky-prod-wrap').append(prodImg).append(prodDesc);
     
     //init sticky
	   productStickySS();
	}
  
  
});