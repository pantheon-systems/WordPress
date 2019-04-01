Object.keys = Object.keys || function(o) { 
    var result = []; 
    for(var name in o) { 
        if (o.hasOwnProperty(name)) 
          result.push(name); 
    } 
    return result; 
};

jQuery(document).ready(function($){

	var animationDelay = 0; 
	var enableAnimation, extraColor, greyscale, enableZoom, enableZoomConnect, markerImg, centerlng, centerlat, zoomLevel, latLng, infoWindows;
	var map = [];
	var infoWindows = [];

	window.mapAPI_Loaded = function() {

		for(var i = 0; i < $('.nectar-google-map').length; i++) {
			 infoWindows[i] = [];
		}

		$('.nectar-google-map').each(function(i){
			
			/*var $mapCopy = $(this).clone();
			var $currentPosition = $(this).next('.map-marker-list');
			$(this).remove();
			$mapCopy.insertBefore($currentPosition);*/


			//map margin if page header
			if( $('#page-header-bg:not("[data-parallax=1]")').length > 0 && $('#contact-map').length > 0 ) { $('#contact-map').css('margin-top', 0);  $('.container-wrap').css('padding-top', 0);} 
			if( $('#page-header-bg[data-parallax=1]').length > 0 ) $('#contact-map').css('margin-top', '-30px');
			
		  zoomLevel = parseFloat($(this).attr('data-zoom-level'));
		  centerlat = parseFloat($(this).attr('data-center-lat'));
			centerlng = parseFloat($(this).attr('data-center-lng'));
			markerImg = $(this).attr('data-marker-img');
			enableZoom = $(this).attr('data-enable-zoom');
			enableZoomConnect = (enableZoom == '1') ? false : true;
			greyscale = $(this).attr('data-greyscale');
			extraColor = $(this).attr('data-extra-color');
			ultraFlat = $(this).attr('data-ultra-flat');
			darkColorScheme = $(this).attr('data-dark-color-scheme');
			var $flatObj = [];
			var $darkColorObj = [];
			enableAnimation = $(this).attr('data-enable-animation');
			
			if( isNaN(zoomLevel) ) { zoomLevel = 12;}
			if( isNaN(centerlat) ) { centerlat = 51.47;}
			if( isNaN(centerlng) ) { centerlng = -0.268199;}
			if( typeof enableAnimation != 'undefined' && enableAnimation == 1 && $(window).width() > 690) { animationDelay = 180; enableAnimation = google.maps.Animation.BOUNCE } else { enableAnimation = null; }
		
		    latLng = new google.maps.LatLng(centerlat,centerlng);
		    
		   //color

		    if(ultraFlat == '1') {
		        $flatObj = [{
				    "featureType": "transit",
				    "elementType": "geometry",
				    "stylers": [
				      { "visibility": "off" }
				    ]
				  },
				  {
				    "elementType": "labels",
				    "stylers": [
				      { "visibility": "off" }
				    ]
				  },
				  {
				    "featureType": "administrative",
				    "stylers": [
				      { "visibility": "off" }
				    ]
				  }];
		    } else {
		    	$flatObj[0] = {};
		    	$flatObj[1] = {};
		    	$flatObj[2] = {};
		    }


		    if(darkColorScheme == '1') {
		    	 $darkColorObj = [{
			        "featureType": "all",
			        "elementType": "labels.text.fill",
			        "stylers": [
			            {
			                "saturation": 36
			            },
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 40
			            }
			        ]
			    },
			    {
			        "featureType": "all",
			        "elementType": "labels.text.stroke",
			        "stylers": [
			            {
			                "visibility": "on"
			            },
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 16
			            }
			        ]
			    },
			    {
			        "featureType": "all",
			        "elementType": "labels.icon",
			        "stylers": [
			            {
			                "visibility": "off"
			            }
			        ]
			    },
			    {
			        "featureType": "administrative",
			        "elementType": "geometry.fill",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 20
			            }
			        ]
			    },
			    {
			        "featureType": "administrative",
			        "elementType": "geometry.stroke",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 17
			            },
			            {
			                "weight": 1.2
			            }
			        ]
			    },
			    {
			        "featureType": "landscape",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 20
			            }
			        ]
			    },
			    {
			        "featureType": "poi",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 21
			            }
			        ]
			    },
			    {
			        "featureType": "road.highway",
			        "elementType": "geometry.fill",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 17
			            }
			        ]
			    },
			    {
			        "featureType": "road.highway",
			        "elementType": "geometry.stroke",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 29
			            },
			            {
			                "weight": 0.2
			            }
			        ]
			    },
			    {
			        "featureType": "road.arterial",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 18
			            }
			        ]
			    },
			    {
			        "featureType": "road.local",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 16
			            }
			        ]
			    },
			    {
			        "featureType": "transit",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 19
			            }
			        ]
			    },
			    {
			        "featureType": "water",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "color": "#000000"
			            },
			            {
			                "lightness": 17
			            }
			        ]
			    }];
		    } else {
		    	$darkColorObj[0] = {};
		    	$darkColorObj[1] = {};
		    	$darkColorObj[2] = {};
		    	$darkColorObj[3] = {};
		    	$darkColorObj[4] = {};
		    	$darkColorObj[5] = {};
		    	$darkColorObj[6] = {};
		    	$darkColorObj[7] = {};
		    	$darkColorObj[8] = {};
		    	$darkColorObj[9] = {};
		    	$darkColorObj[10] = {};
		    	$darkColorObj[11] = {};
		    	$darkColorObj[12] = {};
		    }

		    if(greyscale == '1' && extraColor.length > 0) {
			    styles = [
			    
			    {
					featureType: "poi",
					elementType: "labels",
					stylers: [{
						visibility: "off"
					}]
				}, 
				{ 
					featureType: "road.local", 
					elementType: "labels.icon", 
					stylers: [{ 
						"visibility": "off" 
					}] 
				},
				{ 
					featureType: "road.arterial", 
					elementType: "labels.icon", 
					stylers: [{ 
						"visibility": "off" 
					}] 
				},
				{
					featureType: "road",
					elementType: "geometry.stroke",
					stylers: [{
						visibility: "off"
					}]
				}, 
				{ 
					featureType: "transit", 
					elementType: "geometry.fill", 
					stylers: [
						{ hue: extraColor },
						{ visibility: "on" }, 
						{ lightness: 1 }, 
						{ saturation: 7 }
					]
				},
				{
					elementType: "labels",
					stylers: [{
					saturation: -100,
					}]
				}, 
				{
					featureType: "poi",
					elementType: "geometry.fill",
					stylers: [
						{ hue: extraColor },
						{ visibility: "on" }, 
						{ lightness: 20 }, 
						{ saturation: 7 }
					]
				},
				{
					featureType: "landscape",
					stylers: [
						{ hue: extraColor },
						{ visibility: "on" }, 
						{ lightness: 20 }, 
						{ saturation: 20 }
					]
					
				}, 
				{
					featureType: "road",
					elementType: "geometry.fill",
					stylers: [
						{ hue: extraColor },
						{ visibility: "on" }, 
						{ lightness: 1 }, 
						{ saturation: 7 }
					]
				}, 
				{
					featureType: "water",
					elementType: "geometry",
					stylers: [
						{ hue: extraColor },
						{ visibility: "on" }, 
						{ lightness: 1 }, 
						{ saturation: 7 }
					]
				},
				$darkColorObj[0],
				$darkColorObj[1],
				$darkColorObj[2],
				$darkColorObj[3],
				$darkColorObj[4],
				$darkColorObj[5],
				$darkColorObj[6],
				$darkColorObj[7],
				$darkColorObj[8],
				$darkColorObj[9],
				$darkColorObj[10],
				$darkColorObj[11],
				$darkColorObj[12],
				$flatObj[0],
				$flatObj[1],
				$flatObj[2]
				];
				
			} 
			
			
			
			else if(greyscale == '1'){
				
				styles = [
			    
			    {
					featureType: "poi",
					elementType: "labels",
					stylers: [{
						visibility: "off"
					}]
				}, 
				{ 
					featureType: "road.local", 
					elementType: "labels.icon", 
					stylers: [{ 
						"visibility": "off" 
					}] 
				},
				{ 
					featureType: "road.arterial", 
					elementType: "labels.icon", 
					stylers: [{ 
						"visibility": "off" 
					}] 
				},
				{
					featureType: "road",
					elementType: "geometry.stroke",
					stylers: [{
						visibility: "off"
					}]
				}, 
				{
					elementType: "geometry",
					stylers: [{
						saturation: -100
					}]
				},
				{
					elementType: "labels",
					stylers: [{
					saturation: -100
					}]
				}, 
				{
					featureType: "poi",
					elementType: "geometry.fill",
					stylers: [{
						color: "#ffffff"
					}]
				},
				{
					featureType: "landscape",
					stylers: [{
						color: "#ffffff"
					}]
				}, 
				{
					featureType: "road",
					elementType: "geometry.fill",
					stylers: [ {
						color: "#f1f1f1"
					}]
				}, 
				{
					featureType: "water",
					elementType: "geometry",
					stylers: [{
						color: "#b9e7f4"
					}]
				},
				$darkColorObj[0],
				$darkColorObj[1],
				$darkColorObj[2],
				$darkColorObj[3],
				$darkColorObj[4],
				$darkColorObj[5],
				$darkColorObj[6],
				$darkColorObj[7],
				$darkColorObj[8],
				$darkColorObj[9],
				$darkColorObj[10],
				$darkColorObj[11],
				$darkColorObj[12],
				$flatObj[0],
				$flatObj[1],
				$flatObj[2]
				];
					
				
			}
			
			
			else {
				 styles = [];
			} 
			
			
			var styledMap = new google.maps.StyledMapType(styles,
		    {name: "Styled Map"});
		
		
		    //options
			var mapOptions = {
		      center: latLng,
		      zoom: zoomLevel,
		      mapTypeControlOptions: {
		        mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
		   	  },
		      scrollwheel: false,
		      panControl: false,
			  zoomControl: enableZoom,
			  disableDoubleClickZoom: enableZoomConnect,	  
			  zoomControlOptions: {
		        style: google.maps.ZoomControlStyle.LARGE,
		        position: google.maps.ControlPosition.LEFT_CENTER
		   	  },
			  mapTypeControl: false,
			  scaleControl: false,
			  streetViewControl: false
			  
		    };
			
			map[i] = new google.maps.Map(document.getElementById($(this).attr('id')), mapOptions);
			
			//Associate the styled map with the MapTypeId and set it to display.
		    map[i].mapTypes.set('map_style', styledMap);
		    map[i].setMapTypeId('map_style');
		
			var $count = i;
			
			google.maps.event.addListenerOnce(map[i], 'tilesloaded', function() {
				
				var map_id = $(map[i].getDiv()).attr('id');

				//don't start the animation until the marker image is loaded if there is one
				if(markerImg.length > 0) {
					var markerImgLoad = new Image();
					markerImgLoad.src = markerImg;
					
					$(markerImgLoad).load(function(){
						 setMarkers(map[i], map_id, $count);
					});

				}
				else {
					setMarkers(map[i], map_id, $count);
				}
		    });

	   });

	    
      //watcher to resize gmap inside grow-in animatino col
      var $gMapsAnimatedSelector = $('.col.has-animation[data-animation="grow-in"] .nectar-google-map');
      var gMapsInterval = [];
      var gMapsAnimatedCount = ($gMapsAnimatedSelector.length > 0) ? $gMapsAnimatedSelector.length : 0;
      $gMapsAnimatedSelector.each(function(i){
        
        var $that = $(this);
        
        //watcher
        gMapsInterval[i] = setInterval(function(){

          if($that.parents('.col.has-animation[data-animation="grow-in"]').hasClass('animated-in')) {
            
            for(var k=0; k < map.length; k++ ) {
              google.maps.event.trigger(map[k], 'resize');
            }
             
            //clear watcher
            setTimeout(function(){
              clearInterval(gMapsInterval[i]);
            },1000); 
          
          }
          
        },500);
        
      });
			
			CustomMarker.prototype = new google.maps.OverlayView();
			CustomMarker.prototype.draw = function() {

			  var me = this;
			  var div = this.div_;
			  if (!div) {
			    div = this.div_ = $('' +
            '<div><div class="animated-dot">' +
            '<div class="middle-dot"></div>' +
            '<div class="signal"></div>' +
            '<div class="signal2"></div>' +
            '</div></div>' +
            '')[0];


			    div.style.position = 'absolute';
			    div.style.paddingLeft = '0px';
			    div.style.cursor = 'pointer';

			    var panes = this.getPanes();
			    panes.overlayImage.appendChild(div);

			         

			  }
			  var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
			  if (point) {
			    div.style.left = point.x + 'px';
			    div.style.top = point.y + 'px';
			  }

			    //infowindow
		       google.maps.event.addDomListener(div, "click", function(event) {

		       	    infoWindows[me.mapIndex][me.infoWindowIndex].setPosition(me.latlng_);
		        	infoWindows[me.mapIndex][me.infoWindowIndex].open(me.map);
			        
		        });
						 
			};
			CustomMarker.prototype.remove = function() {
		     // Check if the overlay was on the map and needs to be removed.
		     if (this.div_) {
		       this.div_.parentNode.removeChild(this.div_);
		       this.div_ = null;
		     }
		   };

		   CustomMarker.prototype.getPosition = function() {
		    return this.latlng_;
		   };

	} //api loaded
    


	if(typeof google === 'object' && typeof google.maps === 'object') {


 		$(window).on("pronto.render", function(){
 			mapAPI_Loaded();
 		});

 		//$(window).trigger('resize');
 		//mapAPI_Loaded();
 		//setTimeout(function(){  $(window).trigger('resize'); },200);

 	} else {

 		if(nectarLove.mapApiKey.length > 0) {
 			$.getScript('https://maps.googleapis.com/maps/api/js?sensor=false&key='+nectarLove.mapApiKey+'&callback=mapAPI_Loaded');
 		} else {
 			$.getScript('https://maps.googleapis.com/maps/api/js?sensor=false&callback=mapAPI_Loaded');
 		}
 		

 	}


 

 	function CustomMarker(latlng,  map, PARAM1, PARAM2) {
	  this.latlng_ = latlng;
	  this.infoWindowIndex = PARAM1;
	  this.mapIndex = PARAM2;
	  this.setMap(map);
	}

    function setMarkers(map,map_id,count) {


		  $('.map-marker-list.'+map_id).each(function(){
		      	
		        var enableAnimation = $('#'+map_id).attr('data-enable-animation');
				
		      	$(this).find('.map-marker').each(function(i){
					
					//nectar marker 
					if($('#'+map_id).is('[data-marker-style="nectar"]')) {
						var latlng = new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));
						var overlay = new CustomMarker(latlng, map, i, count);
					}
					
						 
		      		 var marker = new google.maps.Marker({
				      	position: new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng')),
				        map: map,
				        visible: false,
				        mapIndex: count,
						infoWindowIndex : i,
						icon: $('#'+map_id).attr('data-marker-img'),
						optimized: false
				      }); 

					//google default marker
					if(!$('#'+map_id).is('[data-marker-style="nectar"]')) {
						  //animation
						  if(typeof enableAnimation != 'undefined' && enableAnimation == 1 && $(window).width() > 690) {
						     setTimeout(function() {			     	
						  	    marker.setAnimation(google.maps.Animation.BOUNCE);
						  	    marker.setOptions({ visible: true });
						  	    setTimeout(function(){marker.setAnimation(null);},500);
						     },   i * 200);
					      } else {
					      	marker.setOptions({ visible: true });
					      }
					  }

					   //infowindows 
					  if($(this).attr('data-mapinfo') != '' && $(this).attr('data-mapinfo') != '<br />' && $(this).attr('data-mapinfo') != '<br/>') {
					      var infowindow = new google.maps.InfoWindow({
					   	    content: $(this).attr('data-mapinfo'),
					    	maxWidth: 300
						  });
						  
						  infoWindows[count].push(infowindow);
				
					      google.maps.event.addListener(marker, 'click', (function(marker, i) {
					        return function() {
					        	infoWindows[this.mapIndex][this.infoWindowIndex].open(map, this);
					        }
					        
					      })(marker, i));
				      }
				    
		      		 
		      	});
		      
		 });
				          
		     
	}//setMarker
	
});
