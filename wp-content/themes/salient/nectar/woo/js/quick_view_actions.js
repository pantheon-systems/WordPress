jQuery(document).ready(function($){
  
  
  $('body').on('click','ul.products li.product a.nectar_quick_view',function(e){
    
    e.preventDefault();
    
    var $quickViewBox = $('.nectar-quick-view-box');
    var $product_id = $(this).data('product-id');
    
    //exit if no ID passed
    if(typeof $product_id === 'undefined') { return; }
    
    quickView($(this).parents('li.product'), 'open');
    
    //empty old product info
    $quickViewBox.find('.inner-content').empty();
    

    //get product info
    $.ajax({
      type: 'POST',
      url: nectarLove.ajaxurl, 
      data: {
        'action': 'nectar_woo_get_product',
        'product_id':  $product_id
    }, 
    success: function(response) {

      $quickViewBox.find('.inner-content').html(response);
      
      //store variation starting attr
      $vari_startingImage = ($quickViewBox.find('.nectar-product-slider div.carousel-cell:first img').length > 0) ? $quickViewBox.find('.nectar-product-slider div.carousel-cell:first img').attr('src') : '';

      
      //select2
      if($('body[data-fancy-form-rcs="1"]').length > 0) {
        select2Init();
        
        //z index fix
        $select2_css = '.select2-container { z-index: 99999; }';
        var head = document.head || document.getElementsByTagName('head')[0];
  			var style = document.createElement('style');
        
  			style.type = 'text/css';
  			if (style.styleSheet){
  			  style.styleSheet.cssText = $select2_css;
  			} else {
  			  style.appendChild(document.createTextNode($select2_css));
  			}
  			$(style).attr('id','quickview-select-2-zindex');
  			head.appendChild(style);
        
      }
      
      //slide BG Cover
      if($('.nectar-quick-view-box[data-image-sizing="cropped"]').length > 0) {
        flickitySlideCover();
      }
      
      //variations
      if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
  			$( '.variations_form' ).each( function() {
  				$( this ).wc_variation_form();
  			});
  		}
      
      //quantity
      quantityButtons();
      
      $('.nectar-quick-view-box').addClass('fully-open');
      
      $('.nectar-quick-view-box-backdrop').addClass('visible');
      $('.nectar-quick-view-box').addClass('animate-width').transition({
           'left': productQV_Left+'px',
           'width': productQV_Width+'px',
        }, 550, 'cubic-bezier(.55,0,.1,1)' ,function(){
          
        //init flickity
        $pageDots = true;
        if($('.nectar-quick-view-box .nectar-product-slider .carousel-cell').length == 1) {
          $pageDots = false;
        }
        $carousel = $('.nectar-quick-view-box .nectar-product-slider').flickity({
          contain: true,
          lazyLoad: false,
          imagesLoaded: true,
          percentPosition: true,
          prevNextButtons: false,
          pageDots: $pageDots,
          resize: true,
          setGallerySize: true,
          wrapAround: true,
          accessibility: false
        });
        
        //show quick view content
        $('.nectar-quick-view-box .preview_image').hide();
        $('.nectar-quick-view-box').addClass('add-content');
        $('.nectar-quick-view-box').addClass('fixedPos');
        
      });
        
      
    } // success
    
    
  }); //ajax
  
  
}); //quick view click



$('body').on('click','.nectar-quick-view-box-backdrop, .nectar-quick-view-box .close',function(e){
  e.preventDefault();
  if( $('.nectar-quick-view-box.fully-open').length > 0 ) {
     quickView($('.product.open-nectar-quick-view'),'close');
  }
});

var $startingImage, $carousel, productQV_Width, productQV_Left, newHeight;

function quickView(el,state) {
    
    var viewportWidth = window.innerWidth;
    var viewportHeight = window.innerHeight;
    
    if(el.find('.background-color-expand').length == 0 || state == 'close') {
			var topSelected = el.offset().top - $(window).scrollTop(),
			leftSelected = el.offset().left,
			widthSelected = el.find('img').width(),
			heightSelected = el.find('img').height();
    } else {
      var topSelected = el.offset().top - $(window).scrollTop() - 20,
			leftSelected = el.offset().left - 20,
			widthSelected = el.width() + 40,
			heightSelected = el.height() + 40;
    }
    
    var aspectRatio = parseInt(el.find('.product-wrap img').height()) / parseInt(el.find('.product-wrap img').width());
    
    var endingWidth = 425;
    
    if(aspectRatio < 1.1) { endingWidth = 550; } 
    if(aspectRatio > 1.5) { endingWidth = 350; } 
    
    newHeight = Math.floor(aspectRatio*endingWidth);
    var endingTop = (viewportHeight - newHeight)/2;
		var endingLeft = (viewportWidth - endingWidth)/2;
		
		productQV_Width = endingWidth + 475;
		productQV_Left = (viewportWidth - productQV_Width)/2;
        
        
    if(state == 'open') {   

      //copy image
      var productImg = el.find('.product-wrap img:first').clone();
      $('.nectar-quick-view-box .preview_image').show().html(productImg);
       
      
      if(el.find('.background-color-expand').length > 0) {
        $('.nectar-quick-view-box .preview_image').css({
          "top": '20px',
          "left": '20px',
          "width": el.find('.product-wrap img').width(),
          "height": el.find('.product-wrap img').height()
        });
        
        //set BG color on minimal
        $('.nectar-quick-view-box .inner-wrap').css('background-color',el.find('.background-color-expand').css('background-color'));
        
        
      } else {
        $('.nectar-quick-view-box').addClass('box-shadow-trans');
      }
      
      
      $('.nectar-quick-view-box').css({
          "position": 'fixed',
          "transform": '',
          'opacity': '1',
          "top": topSelected,
          "left": leftSelected,
          "width": widthSelected,
          "height": heightSelected
      });
      

      //hide item and show the quick view 
      setTimeout(function(){
        el.addClass('no-trans').addClass('open-nectar-quick-view');
        el.trigger('mouseleave');
        $('.nectar-quick-view-box').addClass('visible');
        $('.nectar-quick-view-box-backdrop').css({'visibility': 'visible', 'z-index': '10000', 'pointer-events': 'all'});
      },75);
      
      
      
      //show loading
      setTimeout(function(){
        $('.nectar-quick-view-box').addClass('loading-vis');
      },575);
        
    
     setTimeout(function(){  
       
          if(el.find('.background-color-expand').length > 0) {

            $('.nectar-quick-view-box .preview_image').transition({
              "top": '-2px',
              "left": '-2px',
              "height": "calc(100% + 4px)"
            }, 800, 'cubic-bezier(.55,0,.1,1)');
          }
          
          //animate size
          $('.nectar-quick-view-box').transition({
            'top': endingTop+ 'px',
            'left': endingLeft+'px',
            'width': (endingWidth - 1) + 'px',
            'height': newHeight + 'px'
          }, 750, 'cubic-bezier(.55,0,.1,1)');
            
        
      },125);  

    
  } else {
    
     //close
      $('.nectar-quick-view-box').removeClass('fully-open');
      el.removeClass('no-trans');
      $('.nectar-quick-view-box-backdrop').removeClass('visible');
      
      if($('head #quickview-select-2-zindex').length > 0) {
        $('head #quickview-select-2-zindex').remove();
      }
    
      $('.nectar-quick-view-box').transition({
        'scale': '0.85',
        'opacity': '0'
      }, 300, 'cubic-bezier(.2,.75,.5,1)', function(){
        el.removeClass('open-nectar-quick-view');
        $startingImage = ($('.nectar-product-slider .flickity-slider .carousel-cell:first-child > img').length > 0) ? $('.nectar-product-slider .flickity-slider .carousel-cell:first-child > img').attr('src') : '';
        $('.nectar-quick-view-box-backdrop').css({'visibility': 'hidden',  'z-index': '-1', 'pointer-events': 'none'});
        $('.nectar-quick-view-box').removeClass('visible').removeClass('add-content').removeClass('loading-vis').removeClass('animate-width').removeClass('fixedPos');
      });
      
  }
  
      
} //quickview function


function resizePos() {
  $('.nectar-quick-view-box.fixedPos').css({
    'left': ($(window).width() - $('.nectar-quick-view-box').width())/2,
    'top': ($(window).height() - $('.nectar-quick-view-box').height())/2
  });
}

$(window).on('resize',resizePos);


function flickitySlideCover() {
  
  $('.nectar-quick-view-box div.images img').css({
    'height': (parseInt($('.nectar-quick-view-box').height()) + 4) + 'px'
  });
  
  //set imgs as BGs  
  $('.nectar-quick-view-box .carousel-cell').each(function(){
    
    var $storedImgSrc = $(this).find('img').css('visibility','hidden').attr('src');
    

    if(typeof newHeight != 'undefined') {
      $(this).find('img').css({
        'height': newHeight + 4
      });
    }
    
    $(this).css({
      'background-image': 'url(' + $storedImgSrc + ')',
      'background-size': 'cover',
      'background-position': 'center'
    });
    
  });
  
}


//variation support
var $vari_startingImage = '';

$('body').on('blur','.nectar-quick-view-box select[name*="attribute_"]', function(){

  var $that = $(this);
  var attr_data = $('.variations_form').data('product_variations');
  var $parent_quick_view = $(this).parents('.nectar-quick-view-box');
  
  if($that.val().length > 0) {

    //give woo time to update img
    setTimeout(function(){

      $(attr_data).each(function(i, el){
        
        if(el.image && el.image.src) {
          

          if(el.image.src == $parent_quick_view.find('.flickity-slider div.carousel-cell:first a > img').attr('src')){
            
             if(el.image.url){
               
                $parent_quick_view.find('.flickity-slider div.carousel-cell:first').css('background-image','url(' + el.image.src + ')');
                $carousel.flickity( 'select', 0, true, false );

              } // if found img url
              
          } // if the sources match

        } // if img source exists

      });	//loop through attrs	

    },30);
    
} else {
  
  //reset to original
  $parent_quick_view.find('.flickity-slider div.carousel-cell:first').css('background-image','url(' + $vari_startingImage + ')');

}
 
}); //blur variation





$('body').on('change','.nectar-quick-view-box select[name*="attribute_"]', function(){


     //keep classes from default hidden btn and full width btn the same
     if($('.nectar-quick-view-box .product .product > .single_add_to_cart_button_wrap .single_add_to_cart_button').length > 0) {
       setTimeout(function(){
          var addToCartClasses = $('.nectar-quick-view-box .summary-content .single_add_to_cart_button').attr('class');
          $('.nectar-quick-view-box .product .product > .single_add_to_cart_button_wrap .single_add_to_cart_button').attr('class',addToCartClasses);
       },290);
       
     }

}); //blur variation 2



function select2Init() {
  $('.nectar-quick-view-box select' ).each( function() {

    $( this ).select2({
      minimumResultsForSearch: 7,
      width: '100%'
    });

  });
  
}
 



// Quantity buttons
function quantityButtons() {
    
    if($('.nectar-quick-view-box .plus').length == 0) {
      
      $('.nectar-quick-view-box div.quantity:not(.buttons_added), .nectar-quick-view-box td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
      
    }
    
    //also move add to cart button
    setTimeout(function(){
        var addToCartBtnText = $('.nectar-quick-view-box .summary-content .single_add_to_cart_button').text();
        var addToCartBtnClasses = ($('.nectar-quick-view-box .summary-content .single_add_to_cart_button[class]').length > 0) ? $('.nectar-quick-view-box .summary-content .single_add_to_cart_button').attr('class') : '';
        var productViewFullBtn = $('.nectar-quick-view-box .nectar-full-product-link').clone();
        
        $('.nectar-quick-view-box .product .product').append('<div class="single_add_to_cart_button_wrap" />');
        
        $('.nectar-quick-view-box .product .product .single_add_to_cart_button_wrap').append('<a class="single_add_to_cart_button button"><span>'+ addToCartBtnText +'</span></a>').append(productViewFullBtn);
        
        //bind click to original button
        $('.nectar-quick-view-box .product .product .single_add_to_cart_button_wrap > .single_add_to_cart_button').attr('class',addToCartBtnClasses).on('click',function(e){
          e.preventDefault(e);
          $('.nectar-quick-view-box .summary-content .single_add_to_cart_button').trigger('click');
        });
        
    },150);
}





});