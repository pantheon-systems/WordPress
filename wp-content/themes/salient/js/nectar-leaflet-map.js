jQuery(document).ready(function($){
  
  var maps = [];
  
   $(window).on( 'vc_reload', function() {
     if(maps.length > 0) {
   		for(var i=0; i<maps.length; i++) {
          maps[i].remove();
      }
    }
     maps = [];
     nectarLeafletInit();
   });
   
  function nectarLeafletInit() {
    
    $('.nectar-leaflet-map').each(function(i){
      
        var mapID = $(this).attr('id');
        var zoomLevel = parseFloat($(this).attr('data-zoom-level'));
  		  var centerlat = parseFloat($(this).attr('data-center-lat'));
  			var centerlng = parseFloat($(this).attr('data-center-lng'));
  			var markerImg = $(this).attr('data-marker-img');
        var markerStyle = $(this).attr('data-marker-style');
        var closePopupOnClickBool = ($(this).is('[data-infowindow-start-open]') && $(this).attr('data-infowindow-start-open') == '1') ? false : true;
        
        maps[i] = L.map(mapID, {
          scrollWheelZoom: false, 
          center: [centerlat, centerlng], 
          zoom: zoomLevel, 
          closePopupOnClick: closePopupOnClickBool 
        });
      
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(maps[i]);
        
        //store index
        var j = i;
        
        $('.map-marker-list.'+mapID).each(function(){
  		      	
  	        	$(this).find('.map-marker').each(function(i){
  				        
                  var iconObj = {};
                  if(markerStyle == 'nectar') {
                    
                    var customIcon = L.divIcon({
                          html: '<div><div class="animated-dot">' +
                          '<div class="middle-dot"></div>' +
                          '<div class="signal"></div>' +
                          '<div class="signal2"></div>' +
                          '</div></div>'
                      });
                      
                      iconObj = { icon: customIcon };
                      
                  }
                  else if($('#'+mapID).attr('data-marker-img').length > 0) {
                    
                    //get image size
                    imgHeight = ($(this).is('[data-marker-image-height]') && $(this).attr('data-marker-image-height').length > 0) ? parseInt($(this).attr('data-marker-image-height')) : 50;
                    imgWidth = ($(this).is('[data-marker-image-width]') && $(this).attr('data-marker-image-width').length > 0) ? parseInt($(this).attr('data-marker-image-width')) : 50;

                    var customIcon = L.icon({
                          iconUrl: $('#'+mapID).attr('data-marker-img'),
                          iconSize: [imgWidth, imgHeight]
                      });
                      
                      iconObj = { icon: customIcon };
                  } else {
                    //regular
                    var customIcon = L.divIcon({
                        html: '<div class="nectar-leaflet-pin"></div>',
                        iconSize: [34, 34],
                        popupAnchor: [-3, -13],
                        iconAnchor: [20, 20],
                    });
                      
                    iconObj = { icon: customIcon };
                  }
                    
                  var markerLat = ($(this).is('[data-lat]') && $(this).attr('data-lat').length > 0) ? parseFloat($(this).attr('data-lat')) : 0;
                  var markerLng = ($(this).is('[data-lng]') && $(this).attr('data-lng').length > 0) ? parseFloat($(this).attr('data-lng')) : 0;
                  
                  //marker
                  
                  ////with infowindow
                  if($(this).attr('data-mapinfo') != '' && $(this).attr('data-mapinfo') != '<br />' && $(this).attr('data-mapinfo') != '<br/>') {
                    
                    if(!closePopupOnClickBool) {
                      //start open
                      L.marker([markerLat, markerLng], iconObj).addTo(maps[j])
                          .bindPopup($(this).attr('data-mapinfo'), {autoClose: false}).openPopup();
                      
                      ////needed to avoid wrong center point (marker autopanning)    
                      maps[j].setView([centerlat, centerlng], zoomLevel);
                      
                    } else {
                      //start closed
                      L.marker([markerLat, markerLng], iconObj).addTo(maps[j])
                          .bindPopup($(this).attr('data-mapinfo'));
                    }
                    
                  }
                  ////without infowindow
                  else {
                    L.marker([markerLat, markerLng], iconObj).addTo(maps[j]);
                  }

              });
              
        });
        
      
        
    });
  }
  
  nectarLeafletInit();
  
    
});