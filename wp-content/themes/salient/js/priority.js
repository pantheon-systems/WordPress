jQuery(function($) {

	 //add specific class if on device for better tablet tracking
	 var using_mobile_browser = false;
	 if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) { using_mobile_browser = true; }
	 
	 var nectarPageHeader;
	 
	 function fullscreenHeightCalc() {
		  var pageHeaderOffset = nectarPageHeader.offset().top;
	 		nectarPageHeader.css('height', ( parseInt(window.innerHeight) - parseInt(pageHeaderOffset)) +'px');
	 }
	 
	 if(using_mobile_browser && $('#page-header-bg.fullscreen-header').length > 0) {
		 
		 nectarPageHeader = $('#page-header-bg');
		 
		 fullscreenHeightCalc();
		 
		 var $windowDOMWidth = window.innerWidth, $windowDOMHeight = window.innerHeight;
		 
		 $(window).resize(function(){
			 	if( ($(window).width() != $windowDOMWidth && $(window).height != $windowDOMHeight)){
				 fullscreenHeightCalc();
				 //store the current window dimensions
				 $windowDOMWidth = window.innerWidth;
				 $windowDOMHeight = window.innerHeight;
			 }
		 });
		 
	 }
	 
	 function portfolioFullScreenSliderCalcs() {
 
 		var $bodyBorderSize = ($('.body-border-top').length > 0 && $(window).width() > 1000) ? $('.body-border-top').height(): 0;
 
 		$('.nectar_fullscreen_zoom_recent_projects').each(function(){
 			if($(this).parents('.first-section').length > 0) {
 				$(this).css('height',$(window).height() - $(this).offset().top - $bodyBorderSize);
 			} else {
 				$(this).css('height',$(window).height());
 			}
 		});
 
 	}
	
	if(using_mobile_browser && $('.nectar_fullscreen_zoom_recent_projects').length > 0) { portfolioFullScreenSliderCalcs(); }
	
	
		function centeredNavBottomBarReposition() {
			
			var $headerOuter = $('#header-outer');
			var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
			var $headerSpan3 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3');
			var $secondaryHeader = $('#header-secondary-outer');
			

			var $logoLinkClone = $headerSpan3.find('#logo').clone();
			if($logoLinkClone.is('[data-supplied-ml="true"]')) {
				$logoLinkClone.find('img:not(.mobile-only-logo)').remove();
			}
			//trans
			$logoLinkClone.find('img.starting-logo').remove();
			

			function centeredNavBottomBarSecondary() {
				if($('body.mobile').length > 0) {
					$('#header-outer').css('margin-top','');
				} else {
	
					// custom mobile breakpoint
					if($('#header-outer .span_9').css('display') == 'none') {
						 $('#header-outer').css('margin-top','');
					} else if($('#header-outer .span_9').css('display') != 'none' && parseInt($('#header-outer').css('top')) > 0) {
						 $('#header-outer').css('top','');
					}
					
					
				}
				
			}
			
			if($secondaryHeader.length > 0) {
				
				if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					setTimeout(function(){
						centeredNavBottomBarSecondary();
					},50);
				}
			
				$secondaryHeader.addClass('centered-menu-bottom-bar');


			} 
			
			if($('#header-outer[data-condense="true"]').length > 0) {
				$headerSpan9.prepend($logoLinkClone);
			} 
			

			
		}
	
		if($('#header-outer[data-format="centered-menu-bottom-bar"]').length > 0) {
			centeredNavBottomBarReposition();
		}
	
	
		//add loaded class for zoom out page header
		$('#page-header-bg[data-animate-in-effect="zoom-out"]').addClass('loaded');
	
		function sliderFontOverrides() { 
				
				var $overrideCSS = '';
	
				$('.nectar-slider-wrap').each(function(i){
	
						if($(this).find('.swiper-container[data-tho]').length > 0) {
					 
								$tho = $(this).find('.swiper-container').attr('data-tho');
								$tco = $(this).find('.swiper-container').attr('data-tco');
								$pho = $(this).find('.swiper-container').attr('data-pho');
								$pco = $(this).find('.swiper-container').attr('data-pco');
	
								//tablet
								if($tho != 'auto' || $tco != 'auto') {
										$overrideCSS += '@media only screen and (max-width: 1000px) and (min-width: 690px) {';
										if($tho != 'auto')
												$overrideCSS += '#'+$(this).attr('id')+ '.nectar-slider-wrap[data-full-width="false"] .swiper-slide .content h2, #boxed .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content h2, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="true"] .swiper-slide .content h2, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="boxed-full-width"] .swiper-slide .content h2, body .full-width-content .vc_span12 .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content h2 { font-size:' + $tho + 'px!important; line-height:' + (parseInt($tho) + 10) + 'px!important;  }';
										if($pho != 'auto')
												$overrideCSS += '#'+$(this).attr('id')+ '.nectar-slider-wrap[data-full-width="false"] .swiper-slide .content p, #boxed .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content p, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="true"] .swiper-slide .content p, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="boxed-full-width"] .swiper-slide .content p, body .full-width-content .vc_span12 .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content p { font-size:' + $tco + 'px!important; line-height:' + (parseInt($tco) + 10) + 'px!important;  }';
										$overrideCSS += '}';
							 }
			 
	
								//phone
								 if($pho != 'auto' || $pco != 'auto') {
	
									 $overrideCSS += '@media only screen and (max-width: 690px) {';
										if($pho != 'auto')
												$overrideCSS += '#'+$(this).attr('id')+ '.nectar-slider-wrap[data-full-width="false"] .swiper-slide .content h2, #boxed .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content h2, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="true"] .swiper-slide .content h2, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="boxed-full-width"] .swiper-slide .content h2, body .full-width-content .vc_span12 .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content h2 { font-size:' + $pho + 'px!important; line-height:' + (parseInt($pho) + 10) + 'px!important;  }';
										if($pho != 'auto')
												$overrideCSS += '#'+$(this).attr('id')+ '.nectar-slider-wrap[data-full-width="false"] .swiper-slide .content p, #boxed .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content p,  body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="true"] .swiper-slide .content p, body .nectar-slider-wrap#'+$(this).attr('id')+ '[data-full-width="boxed-full-width"] .swiper-slide .content p, body .full-width-content .vc_span12 .nectar-slider-wrap#'+$(this).attr('id')+ ' .swiper-slide .content p { font-size:' + $pco + 'px!important; line-height:' + (parseInt($pco) + 10) + 'px!important;  }';
										$overrideCSS += '}';
								}
	
						}
				});
	
				if($overrideCSS.length > 1) {
						var head = document.head || document.getElementsByTagName('head')[0];
						var style = document.createElement('style');
	
						style.type = 'text/css';
						if (style.styleSheet){
							style.styleSheet.cssText = $overrideCSS;
						} else {
							style.appendChild(document.createTextNode($overrideCSS));
						}
	
						head.appendChild(style);
						
						$('.nectar-slider-wrap .content').css('visibility','visible');
				}
		
	
		}
		sliderFontOverrides();
	
	
	
		function centeredLogoMargins() {
			if($('#header-outer[data-format="centered-logo-between-menu"]').length > 0 && $(window).width() > 1000) {
				$midnightSelector = ($('#header-outer .midnightHeader').length > 0) ? '> .midnightHeader:first-child' : '';
				var $navItemLength = $('#header-outer[data-format="centered-logo-between-menu"] '+$midnightSelector+' nav > .sf-menu > li').length;
				if($('#header-outer #social-in-menu').length > 0) { $navItemLength--; }
	
				$centerLogoWidth = ($('#header-outer .row .col.span_3 #logo img:visible').length == 0) ? parseInt($('#header-outer .row .col.span_3').width()) : parseInt($('#header-outer .row .col.span_3 img:visible').width());
	
				$extraMenuSpace = ($('#header-outer[data-lhe="animated_underline"]').length > 0) ? parseInt($('#header-outer header#top nav > ul > li:first-child > a').css('margin-right')) : parseInt($('#header-outer header#top nav > ul > li:first-child > a').css('padding-right'));
				
				if($extraMenuSpace > 30) {
					$extraMenuSpace += 45;
				} else if($extraMenuSpace > 20) {
					$extraMenuSpace += 40;
				} else {
					$extraMenuSpace += 30;
				}
	
				$('#header-outer[data-format="centered-logo-between-menu"] nav > .sf-menu > li:nth-child('+Math.floor($navItemLength/2)+')').css({'margin-right': ($centerLogoWidth+$extraMenuSpace) + 'px'}).addClass('menu-item-with-margin');
				$leftMenuWidth = 0;
				$rightMenuWidth = 0;
				$('#header-outer[data-format="centered-logo-between-menu"] '+$midnightSelector+' nav > .sf-menu > li:not(#social-in-menu)').each(function(i){
					if(i+1 <= Math.floor($navItemLength/2)) {
						$leftMenuWidth += $(this).width();
					} else {
						$rightMenuWidth += $(this).width();
					}
	
				});
	
				var $menuDiff = Math.abs($rightMenuWidth - $leftMenuWidth);
	
				if($leftMenuWidth > $rightMenuWidth) 
					$('#header-outer .row > .col.span_9').css('padding-right',$menuDiff);
				else 
					$('#header-outer .row > .col.span_9').css('padding-left',$menuDiff);
	
				$('#header-outer[data-format="centered-logo-between-menu"] nav').css('visibility','visible');
			}
		}
	
		var usingLogoImage = ($('#header-outer[data-using-logo="1"]').length > 0) ? true : false;
	
	
		//logo centered between menu
		if(!usingLogoImage) {
			centeredLogoMargins();
		}
		else if(usingLogoImage && $('#header-outer[data-format="centered-logo-between-menu"]').length > 0 && $('header#top #logo img:first[src]').length > 0) {
			
			//fadein img on load
			var tempLogoImg = new Image();
			tempLogoImg.src = $('header#top #logo img:first').attr('src');
	
				tempLogoImg.onload = function() {
					 centeredLogoMargins();
				};
			
		}
		
		
		
		
	function nectarFullWidthSections() {


	var $windowInnerWidth = window.innerWidth;
	var $scrollBar = ($('#ascrail2000').length > 0 && $windowInnerWidth > 1000) ? -13 : 0;
	var $bodyBorderWidth = ($('.body-border-right').length > 0 && $windowInnerWidth > 1000) ? parseInt($('.body-border-right').width())*2 : 0;
  var $justOutOfSight;
	
	if($('#boxed').length == 1){
		$justOutOfSight = ((parseInt($('.container-wrap').width()) - parseInt($('.main-content').width())) / 2) + 4;
	} else {
		
		//if the ext responsive mode is on - add the extra padding into the calcs
		var $extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && $windowInnerWidth >= 1000) ? 180 : 0;
		var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && $windowInnerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
		if($(window).width() - $leftHeaderSize - $bodyBorderWidth  <= parseInt($('.main-content').css('max-width'))) { 
			var $windowWidth = parseInt($('.main-content').css('max-width'));

			//no need for the scrollbar calcs with ext responsive on desktop views
			if($extResponsivePadding == 180) $windowWidth = $windowWidth - $scrollBar;

		} else { 
			var $windowWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth;
		}

		
		var $contentWidth = parseInt($('.main-content').css('max-width'));

		//single post fullwidth
		if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 ) {
			$contentWidth = $('.post-area').width();
			$extResponsivePadding = 0;
		}
		
		$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
	}


	$('.carousel-wrap[data-full-width="true"], .portfolio-items[data-col-num="elastic"]:not(.fullwidth-constrained), .full-width-content').each(function(){

	var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && $windowInnerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
	var $bodyBorderWidth = ($('.body-border-right').length > 0 && $windowInnerWidth > 1000) ? parseInt($('.body-border-right').width())*2 : 0;

	//single post fullwidth
	if($('#boxed').length == 1){

		var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());

		if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 && $(this).parents('.post-area').length > 0) {
			$contentWidth = $('.post-area').width();
			$extResponsivePadding = 0;
			$windowWidth = $(window).width() - $bodyBorderWidth;
			$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
		} else {
			if($(this).parents('.page-submenu').length > 0)
				$justOutOfSight = ((parseInt($('.container-wrap').width()) - $mainContentWidth) / 2);
			else 
				$justOutOfSight = ((parseInt($('.container-wrap').width()) - $mainContentWidth) / 2) + 4;
		}
	} else {
		if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 && $(this).parents('.post-area').length > 0) {
			$contentWidth = $('.post-area').width();
			$extResponsivePadding = 0;
			$windowWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth;
		} else {

			var $mainContentMaxWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').css('max-width')) : parseInt($(this).parents('.container').css('max-width'));

			//when using gutter on portfolio don't add extra space for scroll bar
				if($('#boxed').length == 0 && $(this).hasClass('portfolio-items') && $(this).is('[data-gutter*="px"]') && $(this).attr('data-gutter').length > 0 && $(this).attr('data-gutter') != 'none') {
					$scrollBar = ($('#ascrail2000').length > 0 && $windowInnerWidth > 1000) ? -13 : 0;
				}

			if($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentMaxWidth) { 
				$windowWidth = $mainContentMaxWidth;
				//no need for the scrollbar calcs with ext responsive on desktop views
				if($extResponsivePadding == 180) $windowWidth = $windowWidth - $scrollBar;
			}
			$contentWidth = $mainContentMaxWidth;
			$extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && window.innerWidth >= 1000) ? 180 : 0;
			if($leftHeaderSize > 0) $extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && window.innerWidth >= 1000) ? 120 : 0;
		}

		$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
	}

	var $extraSpace = 0;
	if( $(this).hasClass('carousel-wrap')) $extraSpace = 1;
	if( $(this).hasClass('portfolio-items')) $extraSpace = 5;

		var $carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : $(window).width() - $leftHeaderSize - $bodyBorderWidth +$extraSpace  + $scrollBar ;

		//when using gutter on portfolio don't add extra space
		if($('#boxed').length == 0 && $(this).hasClass('portfolio-items') && $(this).is('[data-gutter*="px"]') && $(this).attr('data-gutter').length > 0 && $(this).attr('data-gutter') != 'none') {
			if($(window).width() > 1000)
				$carouselWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth + $scrollBar + 3
			else 
				$carouselWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth + $scrollBar 
		}

		if($(this).parent().hasClass('default-style')) { 

			var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());
			
			if($('#boxed').length != 0) {
				$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : $(window).width() - $leftHeaderSize + $extraSpace + $scrollBar ;
		}
		else {
			$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.025) + $extraSpace + $scrollBar ;
			$windowWidth = ($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentWidth) ? $mainContentWidth : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.025);
			$justOutOfSight = Math.ceil( (($windowWidth + $scrollBar - $mainContentWidth) / 2) )
		}
	}

	else if($(this).parent().hasClass('spaced')) { 

		var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());

		if($('#boxed').length != 0) {
				$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) - ($(window).width()*.02) : $(window).width() + $extraSpace + $scrollBar ;
		} else {
			$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : ($(window).width()- $leftHeaderSize - $bodyBorderWidth)  - Math.ceil(($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.02) + $extraSpace + $scrollBar ;
			var $windowWidth2 = ($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentWidth) ? $mainContentWidth : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.02);
			$justOutOfSight = Math.ceil( (($windowWidth2 + $scrollBar - $mainContentWidth) / 2) +2)
		}
	}
		
		if(!$(this).parents('.span_9').length > 0 && !$(this).parent().hasClass('span_3') && $(this).parent().attr('id') != 'sidebar-inner' && $(this).parent().attr('id') != 'portfolio-extra' 
		&& !$(this).find('.carousel-wrap[data-full-width="true"]').length > 0
		&& !$(this).find('.nectar-carousel-flickity-fixed-content').length > 0
		&& !$(this).find('.portfolio-items:not(".carousel")[data-col-num="elastic"]').length > 0){

			//escape if inside woocoommerce page and not using applicable layout
			if($('.single-product').length > 0 && $(this).parents('#tab-description').length > 0 && $(this).parents('.full-width-tabs').length == 0) {
				$(this).css({
				'visibility': 'visible'
			});	
			} else {
				if($(this).hasClass('portfolio-items')) {
					$(this).css({
						'transform': 'translateX(-'+ $justOutOfSight + 'px)',
						'margin-left': 0,
						'left': 0,
						'width': $carouselWidth,
						'visibility': 'visible'
					});	
				} else {
					$(this).css({
						'left': 0,
						'margin-left': - $justOutOfSight,
						'width': $carouselWidth,
						'visibility': 'visible'
					});	
				}
			
		}
	}  else if($(this).parent().attr('id') == 'portfolio-extra' && $('#full_width_portfolio').length != 0) {
		$(this).css({
			'left': 0,
			'margin-left': - $justOutOfSight,
			'width': $carouselWidth,
			'visibility': 'visible'
		});	
	}

	else {
		$(this).css({
			'margin-left': 0,
			'width': 'auto',
			'left': '0',
			'visibility': 'visible'
		});	
	}
		
	});


}

	if($('#nectar_fullscreen_rows').length == 0) {
		nectarFullWidthSections();
	}


});
