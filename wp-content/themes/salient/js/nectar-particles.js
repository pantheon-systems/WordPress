(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame'] 
                                   || window[vendors[x]+'CancelRequestAnimationFrame'];
    }
 
    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); }, 
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
 
    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());



jQuery(document).ready(function($){

var $shapes = [];
var $selector = ($('.nectar-box-roll').length > 0) ? '.nectar-box-roll .shape': '.nectar-particles .shape';

function initSetup() {

  if($selector.length == 0) return false;

  $($selector).each(function(i){
  	$shapes[i] = {
  		 shape: $(this).attr('data-src'),
  	   	 colorMapping: ($(this).attr('data-color-mapping').length > 0) ? $(this).attr('data-color-mapping') : 'original',
  	   	 color: ($(this).attr('data-color').length > 0) ? $(this).attr('data-color') : '#fefefe',
  	  	 backgroundColor: ($(this).attr('data-bg-color').length > 0) ? $(this).attr('data-bg-color') : 'transparent',
  	   	 colorAlpha: ($(this).attr('data-alpha').length > 0) ? $(this).attr('data-alpha') : 'original', 
  	   	 density: ($(this).attr('data-density').length > 0) ? parseInt($(this).attr('data-density')) : 13, 
  	   	 densityOriginal: ($(this).attr('data-density').length > 0) ? parseInt($(this).attr('data-density')) : 13, 
  	   	 maxParticleSize: ($(this).attr('data-max-size').length > 0) ? parseInt($(this).attr('data-max-size')) : 3, 
  	   	 maxParticleSizeOriginal : ($(this).attr('data-max-size').length > 0) ? parseInt($(this).attr('data-max-size')) : 3
  	};
  	$(this).remove();
  });
}
initSetup();

var Nodes = {

  // Settings
  canvasID: null,
  
  drawDistance: 28,
  maxLineThickness: 4,
  reactionSensitivity: 3,
  lineThickness: 1,

  points: [],
  mouse: { x: window.innerWidth*2, y: window.innerHeight*2, down: false },

  animation: null,
  randomMovement: false,
  impulsX : Math.random()*600-300,
  impulsY: -Math.random()*300,
  imgsToDraw: $shapes,
  timeoutHolder: null,
  totalImgCount: 0,
  loaded: false,
  loadedCount: 0,
  canvas: null,
  context: null,

  imageInput: null,
  bgImage: [],
  onMobile: false,
  explodeChance: true,
  currentShapeIndex: 0,
  currentSequenceIndex: 0,
  prevShapeIndex: 0,
  sequenceActive: false,
  decMultiplier: 0.02,
  bgCanvas: null,
  bgContext: null,
  bgContextPixelData: null,
  disableExplosion: $('#page-header-bg .nectar-particles').attr('data-disable-explosion'),
  rotateTimer: parseInt($('#page-header-bg .nectar-particles').attr('data-rotation-timing')),
  regularAnimation: true, //determines when fps should be lowered during boxroll for better performance
  textPosition: $('#page-header-bg').attr('data-alignment-v'),
  textPositionH: $('#page-header-bg').attr('data-alignment'),
  fps: 43, //only used when box roll is animating (starts to dec from here)
  fpsDec: 0.13,
  now: 0,
  then: Date.now(),
  elapsed: 0,
  init: function(canvasID) {

    // Set up the visual canvas 
    this.canvas = $(canvasID)[0];
    this.context = canvas.getContext( '2d' );
    this.context.globalCompositeOperation = "lighter";
    this.canvas.width = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerWidth : $(canvasID).parents('#page-header-bg').outerWidth(true);
    this.canvas.height = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerHeight : $(canvasID).parents('#page-header-bg').outerHeight(true);
    this.canvas.style.display = 'block'

    this.canvasID = canvasID;

    //set initial bg color
    Nodes.canvasBgColor();

    //set mobile state
    if(this.canvas.width <= 690) this.onMobile = true;

    /*this.canvas.addEventListener('mousemove', this.mouseMove, false);
    this.canvas.addEventListener('mouseout',  this.mouseOut,  false);
    var overlaidContent = $('.overlaid-content')[0];
    overlaidContent.addEventListener('mousemove', this.mouseMove, false);
    overlaidContent.addEventListener('mouseout', this.mouseOut, false);
    */

    //default autorotate
    if($('#page-header-bg .nectar-particles').attr('data-rotation-timing').length < 1) Nodes.rotateTimer = 5500;

    if($(canvasID).parents('.nectar-box-roll').length > 0) {
    	 $('body').on('mousemove',function(e){
    	 	if(Nodes.regularAnimation == true) {
		    	Nodes.mouse.x = e.clientX;
		    	Nodes.mouse.y = e.clientY;

		    } 
	    });
	    //$('body').on('mouseout',function(){
	    //	Nodes.mouse.x = 1000;
	  	//	Nodes.mouse.y = -1000;
	  	//	Nodes.mouse.down = false;
	    //});
    } else {
    	 $(canvasID).parents('.nectar-particles').on('mousemove',function(e){
	    	Nodes.mouse.x = e.clientX - $(this).offset().left;
	    	Nodes.mouse.y = e.clientY - $(this).offset().top + $(window).scrollTop();
	    });
	    $(canvasID).parents('.nectar-particles').on('mouseout',function(){
	    	Nodes.mouse.x = 1000;
	  		Nodes.mouse.y = -1000;
	  		Nodes.mouse.down = false;
	    });
    }
    

    //for non fullscreen mobile
    if($('#page-header-bg:not(.fullscreen-header)').length > 0 && $(window).width() < 1000) {
       $(window).load(function(){
        setTimeout(function(){

          Nodes.canvas.width = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerWidth : $(canvasID).parents('#page-header-bg').outerWidth(true);
          Nodes.canvas.height = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerHeight : $(canvasID).parents('#page-header-bg').outerHeight(true);
          Nodes.onWindowResize();    
          
          },50);

       });
    }


    window.onresize = function(event) {
      if(typeof(event.isTrigger) !== 'undefined') return false;

      Nodes.canvas.width = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerWidth : $(canvasID).parents('#page-header-bg').outerWidth(true);
      Nodes.canvas.height = ($(canvasID).parents('.nectar-box-roll').length > 0 ) ? window.innerHeight : $(canvasID).parents('#page-header-bg').outerHeight(true);
      Nodes.onWindowResize();    
    }

    //count shapes
    var j = 0;
    for( var i=0; i < Nodes.imgsToDraw.length; i++) {

    	//check for sequenced
    	if(typeof Nodes.imgsToDraw[i].shape === 'object'){

    		for(j=0; j < Nodes.imgsToDraw[i].shape.length; j++) {
    			this.totalImgCount++;
    		}
    	} else {
    		this.totalImgCount++;
    	}

    }

    //start loading the shapes
    var j = 0;
    for( var i=0; i < Nodes.imgsToDraw.length; i++) {

    	//check for sequenced
    	if(typeof Nodes.imgsToDraw[i].shape === 'object'){

    		for(j=0; j < Nodes.imgsToDraw[i].shape.length; j++) {
    			this.loadData(Nodes.imgsToDraw[i].shape[j],i,j,true);	
    		}
    	} else {
    		this.loadData(Nodes.imgsToDraw[i].shape,i,null,false);
    	}

    }
  },

  preparePoints: function(index,index2,resize) {

    // Clear the current points
	if(!jQuery.isArray(this.bgImage[index])) {
		this.points[index] = [];
  	} else {
  		if(typeof this.points[index] !== 'object') this.points[index] = {};
  		this.points[index][index2] = [];	
  	}
    
    var width, height, i, j;

    var colors = this.bgContextPixelData.data;

    for( i = 0; i < this.canvas.height; i += this.imgsToDraw[index].density ) {

      for ( j = 0; j < this.canvas.width; j += this.imgsToDraw[index].density ) {

        var pixelPosition = ( j + i * this.bgContextPixelData.width ) * 4;
        
        // Dont use whiteish pixels
        if ( colors[pixelPosition] > 200 && (colors[pixelPosition + 1]) > 200 && (colors[pixelPosition + 2]) > 200 || colors[pixelPosition + 3] === 0 ) {
          continue;
        }
     
        //first shape while loading
        if(index == 0) {
        	var rndNumX = (Math.random() > 0.5) ? Math.random()*window.innerWidth : Math.random()*-window.innerWidth;
	        var rndNumY = (Math.random() > 0.5) ? Math.random()*window.innerHeight : Math.random()*-window.innerHeight;
	        var xPos = Math.random()*(window.innerWidth*2) +rndNumX;
	       	var yPos = Math.random()*(window.innerHeight*2) +rndNumY;
	        
	    }

	    //all others 
	    else {
	  
	    	//sequenced
	    	/*if(!jQuery.isArray(this.points[index]) && typeof this.points[index] === 'object') { 
	    		
	    		//inside sequenced
	    		if(index2 == 0) {
	    			var prevIndex = (index == this.points[index].length) ? 0 : index-1;
	    		
	    			var xPos = this.points[prevIndex][Math.floor(Math.random()*this.points[prevIndex].length)].originalX;
	       			var yPos = this.points[prevIndex][Math.floor(Math.random()*this.points[prevIndex].length)].originalY;
	    		} else {
	    			var prevIndex = (index2 == this.points[index].length) ? 0 : index2-1;

	    			var xPos = this.points[index][prevIndex][Math.floor(Math.random()*this.points[index][prevIndex].length)].originalX;
	       			var yPos = this.points[index][prevIndex][Math.floor(Math.random()*this.points[index][prevIndex].length)].originalY;
	    		}
	    		

	    	} */
	
	    	//regular
	    	//else {

	    		//going back into sequenced
	    		var prevIndex = (index == this.points.length) ? 0 : index-1;
	    		if(!jQuery.isArray(this.points[prevIndex]) && typeof this.points[prevIndex] === 'object') {
	    			var prevIndex2 = (index2 == this.points[prevIndex].length) ? 0 : index2-1; 
	    			var mathRnd = Math.random();
	    			var xPos = this.points[prevIndex][prevIndex2][Math.floor(mathRnd *this.points[prevIndex][prevIndex2].length)].originalX;
	       			var yPos = this.points[prevIndex][prevIndex2][Math.floor(mathRnd *this.points[prevIndex][prevIndex2].length)].originalY;
	    		} else {
	    			var mathRnd = Math.random();
		    		var xPos = this.points[prevIndex][Math.floor(mathRnd *this.points[prevIndex].length)].originalX;
		       		var yPos = this.points[prevIndex][Math.floor(mathRnd *this.points[prevIndex].length)].originalY;
		       	}
	    	//}

	    }

	    //when user resizes screen
	    if(resize == true && Nodes.randomMovement == false && $(Nodes.canvasID).attr('data-loaded') == 'true') {
	    	xPos = j+this.ran(-7,7);
	    	yPos = i+this.ran(-7,7);
	    } else if(resize == true && Nodes.randomMovement == true && $(Nodes.canvasID).attr('data-loaded') == 'true') {
	    	xPos = Math.random()*(window.innerWidth);
	        yPos = Math.random()*(window.innerHeight);
	    }


	    //alpha mapping
	    switch(this.imgsToDraw[index].colorAlpha){
	    	case 'original':
	    		 alpha = 1;
	    		break;
	    	case 'random':
	    		var alpha = Math.random() + 0.3;
	    		//if(alpha < 0.2) alpha = 0.2;
	    		if(alpha > 1) alpha = 1;
	    		break;
	    }

	    //color mapping
	    switch(this.imgsToDraw[index].colorMapping){
	    	case 'original':
	    		var r = colors[pixelPosition];
	    		var g = colors[pixelPosition+1];
	    		var b = colors[pixelPosition+2]
	    		var color = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha +')';
	    		break;

	    	case 'solid':
	    		hex = this.imgsToDraw[index].color.replace('#','');
	    		var r = parseInt(hex.substring(0,2), 16);
	    		var g = parseInt(hex.substring(2,4), 16);
	    		var b = parseInt(hex.substring(4,6), 16);
	    		var color = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha +')';
	    		break;

	    	case 'random':
	    		var r = Math.floor(Math.random()*255);
	    		var g = Math.floor(Math.random()*255);
	    		var b = Math.floor(Math.random()*255);
	    		var color = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha +')';
	    		break;
	    }

      
        var flashChance = (Math.random() < 0.5) ? true : false;
        var rndNum = Math.random();
       
        if(!jQuery.isArray(this.bgImage[index])) {

	        this.points[index].push({ 
	        	x: xPos, 
	        	y: yPos, 
	        	originalX: j, 
	        	originalY: i, 
	        	toX: Math.random()*(window.innerWidth), 
	        	toY: Math.random()*(window.innerHeight), 
	        	r: r,
	        	g: g,
	        	b: b,
	        	a: alpha,
	        	hiddenDuringTrans: false,
	        	originalAlpha: alpha,
	        	color: color, 
	        	baseRadius: Math.ceil(rndNum*this.imgsToDraw[index].maxParticleSize), 
	        	baseRadiusOriginal: Math.ceil(rndNum*this.imgsToDraw[index].maxParticleSize),
	        	randomPosX: Math.random()*6,
	        	randomPosY: Math.random()*6,
	        	shrinking: false, 
	        	shrinkDelay: Math.random()*100,
	        	flashSize: flashChance, 
	        	used: false, //used already when seeking points in explosion
	        	duplicate: false, // duplicate point in explosion to fade in during shape trans
	        	randomNum: rndNum 
	        });

	        if(this.points[index].baseRadius < 1) { this.points[index].baseRadius = 1; this.points[index].baseRadiusOriginal = 1; }
	    } else {
	    
	  		this.points[index][index2].push({ 
	        	x: j, 
	        	y: i, 
	        	originalX: j, 
	        	originalY: i, 
	        	seqX: j,
	        	seqY: i,
	        	sequenceUsed: false,
	        	toX: Math.random()*(window.innerWidth), 
	        	toY: Math.random()*(window.innerHeight), 
	        	color: color, 
	        	baseRadius: Math.ceil(rndNum*3), 
	        	baseRadiusOriginal: Math.ceil(rndNum*3),
	        	shrinking: false, 
	        	shrinkDelay: Math.random()*100,
	        	flashSize: flashChance, 
	        	randomNum: rndNum 
	        });
	  	}

      }
    }

    //hide particles for trans
    for(var u=0; u<this.points[index].length;u++) {

    	var randomNum = Nodes.ran(0,this.points[index].length);
    	if(window.innerWidth < 690) {
    		var divider = (this.points[index].length > 200) ? 8 : 5;
    		if(this.points[index].length > 150 && randomNum > Math.floor(this.points[index].length/divider)) {
		    	this.points[index][u].hiddenDuringTrans = true;
		    }
    	} else {

    		if(this.points[index].length > 800) {
				var divider = 6;
    		} else if (this.points[index].length <= 800 && this.points[index].length > 600) {
    			var divider = 4.5;
    		} else if (this.points[index].length <= 600 && this.points[index].length > 400) {
    			var divider = 3.5;
    		}
    		else if (this.points[index].length <= 400) {
    		 	var divider = 1.5;
    		} 

    		if(this.points[index].length > 350 && randomNum > Math.floor(this.points[index].length/divider)) {
		    	this.points[index][u].hiddenDuringTrans = true;
		    }
    	}
    	
    }


    //initiate 
    if(index == Nodes.imgsToDraw.length -1) {

    	Nodes.draw();

		//start the rotate timer
    	if(resize == false) Nodes.particlesRotate(false);
	   

 	}



  },


  updatePoints: function() {

    var i, currentPoint, theta, distance, dx, dy;

    this.impulsX = this.impulsX - this.impulsX / 30;
    this.impulsY = this.impulsY - this.impulsY / 30;

    	//handle new points
    	/*var shapePointArr = [];
    	var highestPointIndex = 0;

    	for(var i=0; i<this.points.length;i++){
    		shapePointArr[i] = this.points[index].length
    	}
    	
    	var largest = Math.max.apply(Math, shapePointArr); */
    

    //reset to first shape at end
    //if(Math.floor(this.currentShapeIndex) == this.points.length) this.currentShapeIndex = 0;	

    //handle indexing for sequencing and static shapes
    /*if(!jQuery.isArray(Nodes.points[Nodes.currentShapeIndex]) && typeof Nodes.points[Nodes.currentShapeIndex] === 'object') {  
    	var shapeIndex = this.points[Math.floor(this.currentShapeIndex)][0];
    } else { */
    	var shapeIndex = this.points[Math.floor(this.currentShapeIndex)];
    /*}*/
   if(this.onMobile == true) {
	    if(Nodes.decMultiplier < 0.23) Nodes.decMultiplier += 0.0015; 
	 }
	 else { 
	 	if(Nodes.decMultiplier < 0.125) Nodes.decMultiplier += 0.0004; 
	 }
	
	    

	//proc
    for (i = 0; i < shapeIndex.length; i++ ){

      currentPoint = shapeIndex[i];

      theta = Math.atan2( currentPoint.y - this.mouse.y, currentPoint.x - this.mouse.x);


    
           	 
        distance = this.reactionSensitivity * 60 / Math.sqrt((this.mouse.x - currentPoint.x) * (this.mouse.x - currentPoint.x) +
         (this.mouse.y - currentPoint.y) * (this.mouse.y - currentPoint.y));  
         if(distance > 50) distance = 0;
   


        if (! shapeIndex[i].time)  {

            shapeIndex[i].time  = this.ran(70, 200);
            shapeIndex[i].deg   = this.ran(-120, 180);
            shapeIndex[i].vel   = this.ran(0.08, 0.14);  
           // this.points[Math.floor(this.currentShapeIndex)][i].curve = this.ran(0, 1);
            //this.points[Math.floor(this.currentShapeIndex)][i].fade  = this.ran(0, 1);
        }                

        // Calc movement

        var velocity = (Nodes.randomMovement == false) ? shapeIndex[i].vel : shapeIndex[i].vel;

        dx = velocity * Math.cos(shapeIndex[i].deg * Math.PI/180);
        dy = velocity * Math.sin(shapeIndex[i].deg * Math.PI/180);

        if(Nodes.loaded != false) {
          // Calc new position
       	 currentPoint.x += dx;
       	 currentPoint.y += dy;
       	}

        if (shapeIndex[i].curve > 0) { shapeIndex[i].deg = shapeIndex[i].deg + 2; }
           else { shapeIndex[i].deg = shapeIndex[i].deg - 2; }

        shapeIndex[i].time = shapeIndex[i].time - 1;


        //before loaded
        if(Nodes.loaded == false) {


            if (shapeIndex[i].vel < 0.4) {}
            else shapeIndex[i].vel = shapeIndex[i].vel - .00;

        	currentPoint.x += Math.cos(theta) * distance;
    		currentPoint.y += Math.sin(theta) * distance;
	   //after loaded
	   } else {

	   		//next shape
			if(Nodes.randomMovement == false){

				if(Nodes.sequenceActive == false) {
					//static shape

					////resetting attrs

					//////reset opacity for hidden particles after explosion
					//if(currentPoint.duplicate == true) {
					//	currentPoint.alpha = (currentPoint.alpha < currentPoint.originalAlpha) ? currentPoint.alpha + 0.04 : currentPoint.originalAlpha;
	              	//    currentPoint.color = 'rgba(' + currentPoint.r + ',' + currentPoint.g + ',' + currentPoint.b + ',' + currentPoint.alpha+')';
	              	//    if(currentPoint.alpha == currentPoint.originalAlpha) currentPoint.duplicate = false;
	              	//}

					currentPoint.baseRadius = Math.ceil(currentPoint.randomNum * Nodes.imgsToDraw[Math.floor(this.currentShapeIndex)].maxParticleSize);
					//if(currentPoint.baseRadius < 1) currentPoint.baseRadius = 1; //min size
					currentPoint.baseRadiusOriginal = currentPoint.baseRadius;
					//if(currentPoint.hiddenDuringTrans == true) currentPoint.color = 'rgba(' + currentPoint.r + ',' + currentPoint.g + ',' + currentPoint.b + ',' + currentPoint.originalAlpha +')';

					 if (shapeIndex[i].vel < 0.4) shapeIndex[i].time = 0;
		            else shapeIndex[i].vel = shapeIndex[i].vel - .008;

		    		currentPoint.x += Math.cos(theta) * distance + (shapeIndex[i].originalX - currentPoint.x) * Nodes.decMultiplier;
		    		currentPoint.y += Math.sin(theta) * distance + (shapeIndex[i].originalY - currentPoint.y) * Nodes.decMultiplier;
	    		} else {
	    			//sequence

	    			 //if (shapeIndex[i].vel < 0.7) shapeIndex[i].time = 0;
		           // else shapeIndex[i].vel = shapeIndex[i].vel - .002;

	    			if(typeof this.points[Math.floor(this.currentShapeIndex)][Nodes.currentSequenceIndex][i] !== 'undefined'){
	    				currentPoint.x += Math.cos(theta) * distance + (this.points[Math.floor(this.currentShapeIndex)][0][i].seqX - currentPoint.x) * .08;
		    			currentPoint.y += Math.sin(theta) * distance + (this.points[Math.floor(this.currentShapeIndex)][0][i].seqY - currentPoint.y) * .08;
		    		}
	    		}
			}

			//random movement 
			else {

			
				if(i == 0 && this.reactionSensitivity < 8) this.reactionSensitivity = 8;
	
				/*if(shapeIndex[i].time == 0) {
					currentPoint.randomPosX = 1-2*Math.random() *sizeMovement*2;
					currentPoint.randomPosY = 1-2*Math.random() *sizeMovement*2;
				}*/
				//if(shapeIndex[i].time == 0) console.log('e')
				//if (this.points[Math.floor(this.currentShapeIndex)][i].vel < 0.4) this.points[Math.floor(this.currentShapeIndex)][i].time = 0;
	           // else this.points[Math.floor(this.currentShapeIndex)][i].vel = this.points[Math.floor(this.currentShapeIndex)][i].vel - .008;
		    	//currentPoint.x += Math.cos(theta) * distance + (currentPoint.originalX - currentPoint.x) * .11;
	    		//currentPoint.y += Math.sin(theta) * distance + (currentPoint.originalY - currentPoint.y) * .11;


		
				//hide particles accoirding to density
				

           
	    		var sizeMovement = shapeIndex[i].randomNum*currentPoint.baseRadius/4;
	    		if(sizeMovement < 0.25) sizeMovement = 0.25;


	    		if (! shapeIndex[i].time2)  {
		            shapeIndex[i].time2 = this.ran(300, 900);
		        }    

		        shapeIndex[i].time2 = shapeIndex[i].time2 - 1;

				/*if(shapeIndex[i].time2 == 0) {

					var rndX = (Math.random() >= 0.5) ? 1-2*Math.random()*-1.3:  1-2*Math.random()*1.3;
	  				var rndY = (Math.random() >= 0.5) ? 1-2*Math.random()*-1.3 :  1-2*Math.random()*1.3;
					currentPoint.randomPosX = rndX;
					currentPoint.randomPosY = rndY;
					shapeIndex[i].time2 = this.ran(300, 900);
				}*/

				
			

	    		currentPoint.x += Math.cos(theta) * distance + ((shapeIndex[i].toX - currentPoint.x) * .027);
	    		currentPoint.y += Math.sin(theta) * distance + ((shapeIndex[i].toY - currentPoint.y) * .027);
	    		
	    		

	    		  // check for bounds
	              if(currentPoint.x < -(this.canvas.width*0.1)) {
	                currentPoint.x = this.canvas.width*1.1;
	                currentPoint.toX = this.canvas.width*1.1 - (this.ran(20,40)*4)
	              }
	              if(currentPoint.x > this.canvas.width*1.1) {
	                currentPoint.x = -(this.canvas.width*0.1);
	                currentPoint.toX = -(this.canvas.width*0.1) + (this.ran(20,40)*4);
	              }

	              if(currentPoint.y < -(this.canvas.height*0.1)) {
	                currentPoint.y = this.canvas.height*1.1;
	                currentPoint.toY = this.canvas.height*1.1 - (this.ran(20,40)*4)
	              }
	              if(currentPoint.y > this.canvas.height*1.1) {
	                currentPoint.y = -(this.canvas.height*0.1);
	                currentPoint.toY = -(this.canvas.height*0.1) + (this.ran(20,40)*4)
	              }

	               currentPoint.toX += Math.floor(this.impulsX  * sizeMovement*30/30) + (this.impulsX/7*currentPoint.randomPosX);
              	   currentPoint.toY += Math.floor(this.impulsY * sizeMovement*30/30) + (this.impulsY/7*currentPoint.randomPosY);
              	     
              	   //sparkle
              	  if(currentPoint.shrinkDelay >= 0 ) currentPoint.shrinkDelay = currentPoint.shrinkDelay - 0.5;
     
              	   if(currentPoint.flashSize == true && currentPoint.shrinkDelay <= 0) { 
              	  	 
              	   	 ////start large
              	   	 if(currentPoint.baseRadius == currentPoint.baseRadiusOriginal && currentPoint.shrinking == false) {
              	   	 	currentPoint.baseRadius = Nodes.imgsToDraw[Math.floor(this.currentShapeIndex)].maxParticleSize+4;
              	   	 	currentPoint.alpha = 1;
              	   	 	currentPoint.color = 'rgba(' + currentPoint.a + ',' + currentPoint.g + ',' + currentPoint.b + ',' +'1)';
              	   	 	currentPoint.shrinking = true;
              	   	 }

              	   	 ////dec
              	   	 currentPoint.baseRadius = (currentPoint.baseRadius - 0.3 > 1) ? currentPoint.baseRadius - 0.3 : 1;
              	   	 currentPoint.alpha = (currentPoint.alpha >= currentPoint.originalAlpha && currentPoint.originalAlpha != 1) ? currentPoint.alpha - 0.01 : currentPoint.originalAlpha;
              	   	 currentPoint.color = 'rgba(' + currentPoint.r + ',' + currentPoint.g + ',' + currentPoint.b + ',' + currentPoint.alpha+')';
              	   	 
              	   	 ////end size
              	   	 if(currentPoint.baseRadius <= currentPoint.baseRadiusOriginal && currentPoint.shrinking == true) {
              	   	 	currentPoint.baseRadius = currentPoint.baseRadiusOriginal;
              	   	 	currentPoint.flashSize = false
              	   	 	currentPoint.shrinking = false;
              	   	 	currentPoint.shrinkDelay = Math.random()*100;
              	   	 	currentPoint.color = 'rgba(' + currentPoint.r + ',' + currentPoint.g + ',' + currentPoint.b + ',' + currentPoint.originalAlpha+')';

              	   	 	////set new random one
              	   	 	shapeIndex[Math.floor(Math.random()*shapeIndex.length)].flashSize = true;
              	   	 }

              	   	

              	   }
              	
              	

			}

	    }

    }


   

  },


  drawPoints: function() {

    var i, currentPoint;
    //if(!jQuery.isArray(Nodes.points[Nodes.currentShapeIndex]) && typeof Nodes.points[Nodes.currentShapeIndex] === 'object') {  
    //	var shapeIndex = this.points[Math.floor(this.currentShapeIndex)][0];
    //} else {
    var shapeIndex = this.points[Math.floor(this.currentShapeIndex)];
    //}

    for ( i = 0; i < shapeIndex.length; i++ ) {

      currentPoint = shapeIndex[i];

      var randomNum = shapeIndex[i].randomNum;
      if (randomNum < 0.1) randomNum = 0.3;

      //skip drawing some particles during trans
      if(currentPoint.hiddenDuringTrans == true && Nodes.randomMovement == true) continue;

      // Draw the particle
      this.context.beginPath();
      this.context.arc(currentPoint.x, currentPoint.y, currentPoint.baseRadius, 0 , Math.PI*2, true);
      this.context.fillStyle = currentPoint.color;
      this.context.fill();
      this.context.closePath();
      
    }
  },

  draw: function() {
  
	  	//if($('.no-scroll').length > 0) {

	  		//box roll logic that will need to be bound
	  		//$(Nodes.canvasID).parents('.nectar-box-roll').find('.canvas-bg.topBoxOut').length == 0 && $(Nodes.canvasID).parents('.nectar-box-roll').find('.canvas-bg.topBoxIn').length == 0 
	  		
	  		var $that = this;

	  		if(Nodes.regularAnimation == true || Nodes.randomMovement == true) {
         
	  			  Nodes.animation = requestAnimationFrame( Nodes.draw );

            //throttle fps to 60
            Nodes.now = Date.now();
            Nodes.elapsed = Nodes.now - Nodes.then;
            if (Nodes.elapsed > 16.666) {
                Nodes.then = Nodes.now - (Nodes.elapsed % 16.666);
                //stop drawing when the slider is out of view 
                if($('#page-header-bg.out-of-sight').length == 0) {
                    Nodes.clear();
                    Nodes.updatePoints();
                    Nodes.drawPoints();
                }
             } 

	  		} 
	  		else {
	  			Nodes.fpsDec +=0.23;
	  			Nodes.fps = (Nodes.fps >= 0) ?  Nodes.fps - Nodes.fpsDec : 0;
	  			Nodes.decMultiplier = 0.14;
			    setTimeout(function() {
			        Nodes.animation = requestAnimationFrame( function(){ if(Nodes.fps > 0) Nodes.draw() } );
			        // Drawing code goes here
			    }, 1000 / Nodes.fps);

          //stop drawing when the slider is out of view 
          if($('#page-header-bg.out-of-sight').length == 0) {
              Nodes.clear();
              Nodes.updatePoints();
              Nodes.drawPoints();
          }
				
	  		} 
		  
		//}
  },

  clear: function() {
  	this.context.clearRect(0, 0, canvas.width, canvas.height);
  },

  ran: function(min, max)  {  
     return Math.floor(Math.random() * (max - min + 1)) + min;  
  },

  // The filereader has loaded the image... add it to image object to be drawn
  loadData: function( data,index,index2,sequence ) {



  	if(sequence == true) {
  		if(typeof this.bgImage[index] !== 'object') this.bgImage[index] = [];
  		this.bgImage[index][index2] = new Image;
    	this.bgImage[index][index2].src = data;
  	} else {
  		this.bgImage[index] = new Image;
    	this.bgImage[index].src = data;
  	}
  	
  	if(!jQuery.isArray(this.bgImage[index])) {
	    this.bgImage[index].onload = function(){
	    	Nodes.callDrawImageToBackground(index,index2);
	    }


	} else {
	    this.bgImage[index][index2].onload = function(){
	    	Nodes.callDrawImageToBackground(index,index2);
	    }
	}

	

  },

  particlesRotate: function(skipInitialDelay){
  		 
  		 initTimeOut = (skipInitialDelay == true) ? 0 : 800;

   		 setTimeout(function(){ 

   		 	 //interval between shapes - extra fo first load to balance initial scatter animation
   		 	 var timeoutInterval = (Nodes.loaded == false) ? Nodes.rotateTimer + 1000 : Nodes.rotateTimer;

   		 	Nodes.loaded = true; 
   		 	setTimeout(function(){ $(Nodes.canvasID).attr('data-loaded','true'); }, 1000);

   		 	if(Nodes.imgsToDraw.length > 1) Nodes.timeoutHolder = setTimeout( function(){ Nodes.particleRotateLogic(false) }, timeoutInterval);

			  Nodes.canvasBgColor();
		  	initTextEffect(Nodes.canvasID);
			

   		 }, initTimeOut);

       //fadeout loading animation
      if(skipInitialDelay != true) {
        $('#ajax-loading-screen').stop().transition({'opacity':0},1000,function(){ $(this).css({'display':'none'}); }); 
        $('#ajax-loading-screen .loading-icon').transition({'opacity':0},1000) 
      }

  },

  
  particleRotateLogic: function(seek) {

			//clear current timeout incase seeked
			clearTimeout(Nodes.timeoutHolder);

   		 	//don't switch during boxroll
   		 	if($('.canvas-bg.topBoxOut').length > 0) {
   		 		 //setTimeout( rotate, timeoutInterval);
   		 		return false;
   		 	}

   		 	//chance for random movement or next shape
   		 	var explodeChance = (Nodes.disableExplosion == 'on') ? 0 : 0.4;
   		 	if(Math.random() > explodeChance || seek !== false){

   		 		//update shape index
   		 		if(seek !== false) {
   		 			Nodes.prevShapeIndex = Nodes.currentShapeIndex;
   		 			Nodes.currentShapeIndex = seek; 
   		 		} else {
   		 			Nodes.currentShapeIndex = (Nodes.currentShapeIndex+1 == Nodes.imgsToDraw.length) ? 0 : Nodes.currentShapeIndex+1; 
   		 		}

   		 		//slow particles during trans
   		 		if(Nodes.randomMovement == false) Nodes.decMultiplier = 0.06;
   		 		else { Nodes.decMultiplier = 0.06; }

   		 		//sequenced shape
   		 		/*if(!jQuery.isArray(Nodes.points[Nodes.currentShapeIndex]) && typeof Nodes.points[Nodes.currentShapeIndex] === 'object') { 
   		 			clearInterval(rotation);
   		 			Nodes.sequenceActive = true;
   		 			Nodes.particleSequenceRotate();
   		 		}*/

   		 		var prevIndex = (Nodes.currentShapeIndex == Nodes.points.length) ? 0 : Math.floor(Nodes.currentShapeIndex-1);
				if(Math.floor(Nodes.currentShapeIndex) - 1 == -1) prevIndex = Nodes.points.length - 1;
				var prevPrevIndex = (prevIndex-1 == -1) ? Nodes.points.length - 1 : prevIndex-1;
			
   		 		//set next shape x/y pos to match the previos one after rnd movement
   		 		for (i = 0; i < Nodes.points[Nodes.currentShapeIndex].length; i++ ) {
   		 			var mathRnd = Math.random();
   		 			if(seek !== false) {
   		 				var xPos = Nodes.points[Nodes.prevShapeIndex][Math.floor(mathRnd*Nodes.points[Nodes.prevShapeIndex].length)].x;
			   			var yPos = Nodes.points[Nodes.prevShapeIndex][Math.floor(mathRnd*Nodes.points[Nodes.prevShapeIndex].length)].y;
   		 			} else {
   		 				if(Nodes.randomMovement == true) {
	   		 				//for(j = 0; j < Nodes.points[prevIndex].length; j++){
	   		 					var mathRnd = Math.random();
	   		 					//if(Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].hiddenDuringTrans == false && Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].used == false) {
	   		 						//Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].used = true;
	   		 						var xPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].x;
				   					var yPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].y;
				   					//var color = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].color;
				   					//Nodes.points[Nodes.currentShapeIndex][i].color = color;
				   				//	break;
				   				//} 
				   				//hide duplicate points now and fade them in mid way as they're going to the next shape
				   				/*else if(Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].hiddenDuringTrans == false && Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].used == true) {
				   					var currentPoint = Nodes.points[Nodes.currentShapeIndex][i];
				   					var xPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].x;
				   					var yPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].y;
				   					currentPoint.duplicate = true;
				   					currentPoint.alpha = 0;
              	   	 				currentPoint.color = 'rgba(' + currentPoint.a + ',' + currentPoint.g + ',' + currentPoint.b + ',' +'0)';

              	   	 				break;
				   				}
	   		 				}*/
	   		 			} else {
	   		 				var xPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].x;
				   			var yPos = Nodes.points[prevIndex][Math.floor(mathRnd*Nodes.points[prevIndex].length)].y;
	   		 			}

	   		 		
	   		 			//reset used attr on end of loop
	   		 			//if(i == Nodes.points[Nodes.currentShapeIndex].length - 1) {
	   		 			//	for(j = 0; j < Nodes.points[prevIndex].length; j++) {
	   		 			//		Nodes.points[prevIndex][j].used = false;
	   		 			//	}
	   		 			//}

	   		 			
   		 				
   		 			}
					
			   		Nodes.points[Nodes.currentShapeIndex][i].x = xPos;
			   		Nodes.points[Nodes.currentShapeIndex][i].y = yPos;

			   	} 

			   	var $paginationTimeout = (Nodes.randomMovement == true) ? 300 : 300;
   		 		Nodes.randomMovement = false;
   		 		$(Nodes.canvasID).attr('data-randomMovement','false');

	   		 	 //reset points to prev shap after animation is complete
				for (i = 0; i < Nodes.points[prevIndex].length; i++ ) {
					var mathRnd = Math.random();
					var xPos = Nodes.points[prevPrevIndex][Math.floor(mathRnd*Nodes.points[prevPrevIndex].length)].originalX;
			   		var yPos = Nodes.points[prevPrevIndex][Math.floor(mathRnd*Nodes.points[prevPrevIndex].length)].originalY;
			   		Nodes.points[prevIndex][i].x = xPos;
			   		Nodes.points[prevIndex][i].y = yPos;

			   		Nodes.points[prevIndex][i].toX = Math.random()*(window.innerWidth); 
    				Nodes.points[prevIndex][i].toY = Math.random()*(window.innerHeight); 

    				//reset flash chance
     				var flashChance = (Math.random() < 0.5) ? true: false;
       				Nodes.points[prevIndex][i].flashSize = flashChance;
			   	} 

         if(this.reactionSensitivity > 4) this.reactionSensitivity = (window.innerWidth > 690) ? 4 : 1; 

			   //handle captions
			   if(seek !== false) {
			  	   var currentCaptionIndex = seek+1;
			       var nextCaptionIndex = seek+1;	
			   } else {
			   	   var currentCaptionIndex = (Nodes.currentShapeIndex == 0) ? Nodes.imgsToDraw.length : Nodes.currentShapeIndex;
			       var nextCaptionIndex = (Nodes.currentShapeIndex == Nodes.points.length) ? 0 : Math.floor(Nodes.currentShapeIndex+1);	
			   }

			   Nodes.shapeTextDisplay(currentCaptionIndex, nextCaptionIndex, seek);

			   //update pagination
			   var $selector = ($('.nectar-box-roll').length > 0) ? '.nectar-box-roll': '.nectar-particles';
			   if($(Nodes.canvasID).parents($selector).find('.pagination-navigation').length > 0 && seek == false) {
			       setTimeout( function(){ $(Nodes.canvasID).parents($selector).find('.pagination-dot').eq(Nodes.currentShapeIndex).click(); },$paginationTimeout);
			   }

			   var timeoutInterval = Nodes.rotateTimer;
			   Nodes.timeoutHolder = setTimeout( function(){ Nodes.particleRotateLogic(false) }, timeoutInterval);

   		 	} else {
   		 		var timeoutInterval = 2800;
   		 		Nodes.timeoutHolder = setTimeout( function(){ Nodes.particleRotateLogic(false) }, timeoutInterval);

   		 		Nodes.randomMovement = true;
   		 		$(Nodes.canvasID).attr('data-randomMovement','true');

   		 		Nodes.impulsX = Math.random()*600-300;
       			Nodes.impulsY = -Math.random()*300;
       			for (i = 0; i < Nodes.points[Nodes.currentShapeIndex].length; i++ ) {
       				var currentPoint = Nodes.points[Nodes.currentShapeIndex][i];
					currentPoint.randomPosX = Math.random()*6;
					currentPoint.randomPosY = Math.random()*6;
				}

   		 	}

   		 	Nodes.canvasBgColor();
	   		 
	   		 

  },

  canvasBgColor: function(){

  		jQuery(Nodes.canvasID).parents('.nectar-particles').find('.canvas-bg').css({
	 		'background-color': Nodes.imgsToDraw[Nodes.currentShapeIndex].backgroundColor
	 	});
  },

  resetShapeTextTimeout: null,
  shapeTextDisplay: function(index, index2, seek){

  		clearTimeout(Nodes.resetShapeTextTimeout);

  		var $rotate = 0;
  		var $selector = ($('.nectar-box-roll').length > 0) ? '.nectar-box-roll': '.nectar-particles';
  		jQuery(Nodes.canvasID).parents($selector).find('.inner-wrap').css('z-index',10);

  		if(seek !== false) {
	  		jQuery(Nodes.canvasID).parents($selector).find('.inner-wrap:not(.shape-'+index+')').each(function(i){
	  			$(this).find('> *').each(function(i){
	  				$(this).stop(true,true).delay(i*150).transition({'opacity':'0'}, 250, 'ease');
	  			});
	  		});
	  		Nodes.resetShapeTextTimeout = setTimeout(function(){ jQuery(Nodes.canvasID).parents($selector).find('.inner-wrap:not(.shape-'+index+') > *').delay(50).transition({ 'rotateX': $rotate, 'y': '30px'}, 0); }, jQuery(Nodes.canvasID).parents($selector).find('.inner-wrap:not(.shape-'+index+')').length * 200 );
	  			
  		} else {
  			jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index+' > *').each(function(i){
	  			$(this).stop(true,true).delay(i*150).transition({'opacity':'0'}, 250, 'ease');
	  		});

	  		Nodes.resetShapeTextTimeout = setTimeout(function(){ jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index+' > *').transition({'rotateX': $rotate, 'y': '30px'}, 0); }, jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index+' > *').length * 200 );
	  		
  		}

  		jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index2).css('z-index',100);
  		jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index2+' > *').each(function(i){
  			$(this).stop(true,true).delay(jQuery(Nodes.canvasID).parents($selector).find('.shape-'+index+' > *').length * 150 + (i*175)).transition({'opacity':'1', 'y': 0, 'rotateX': '0'}, 700, 'ease');
  		});

  },



  particleSequenceRotate: function() {
  	setInterval(function(){


  		 var seqXPos, seqYPos, currentPoint, otherPoint, otherPointCache, distance, nextIndex, closestMatchX, closestMatchY, def;
	   
  		
	    for( var i=0; i < Nodes.points.length; i++) {

	    	//check for sequenced
	    	if( !jQuery.isArray(Nodes.points[i]) && typeof Nodes.points[i] === 'object'){

	    		for(j=0; j < 1; j++) {

	    			

	    			//nextIndex = ( j+1 > Object.keys(Nodes.points[i]).length -1 ) ?  0 : j+1;

	    			for(var k=0; k < Nodes.points[i][0].length; k++) {

	    				currentPoint = Nodes.points[i][0][k];
	    				
	    				def = 1000;
	    				closestMatchX = 0;
	    				closestMatchY = 0;

						for(var u=0; u < Nodes.points[i][Nodes.currentSequenceIndex].length; u++) {
							
							 otherPoint = Nodes.points[i][Nodes.currentSequenceIndex][u];
								
							if ( otherPoint.sequenceUsed != true) {
					  			
					  			distance = Math.sqrt((otherPoint.originalX - currentPoint.x) * (otherPoint.originalX - currentPoint.x) + (otherPoint.originalY - currentPoint.y) * (otherPoint.originalY - currentPoint.y));
								
						        if(distance <= def && def > 10 ) {
							        def = distance;
							 		
							        currentPoint.seqX = otherPoint.originalX;
							        currentPoint.seqY = otherPoint.originalY;
						
							        otherPointCache = otherPoint;
							    }
								
								
				
								if(u ==  Nodes.points[i][Nodes.currentSequenceIndex].length -1) {
	        				
	        					 	otherPointCache.sequenceUsed = true;
	        					 }
        					 
        					}
						}

						//clear sequence used
						if(k == Nodes.points[i][0].length -1) {
							for(var u=0; u < Nodes.points[i][Nodes.currentSequenceIndex].length; u++) {
								Nodes.points[i][Nodes.currentSequenceIndex][u].sequenceUsed = false;
							}
						}
						
	    			}
	    			
	    		}

	    	} 
	    }


  		//update shape index
	   	Nodes.currentSequenceIndex = (Nodes.currentSequenceIndex+1 == Object.keys(Nodes.points[Nodes.currentShapeIndex]).length) ? 0 : Nodes.currentSequenceIndex+1; 
  	},80);
  },
  callDrawImageToBackground: function(index,index2){

  		 Nodes.loadedCount += 1;
  		
	      //wait until all are loaded
	      if(Nodes.loadedCount == Nodes.totalImgCount) {


	      	for(var i=0;i<Nodes.imgsToDraw.length;i++) {

	      		if(!jQuery.isArray(this.bgImage[i])) {
	      		
		      		Nodes.drawImageToBackground(i,null,false,true);
		      	} else {
		      		for(j=0; j < Nodes.imgsToDraw[i].shape.length; j++) {
	      	
	      				Nodes.drawImageToBackground(i,j,false,true);
	      			}
		      	}
	      	}

	      } 
  },
  // Image is loaded... draw to bg canvas
  drawImageToBackground: function (index,index2,resize,sequence) {

  	shapeIndex = (index2 == null) ? this.bgImage[index] : this.bgImage[index][index2];

    this.bgCanvas = document.createElement( 'canvas' );
    this.bgCanvas.width = this.canvas.width;
    this.bgCanvas.height = this.canvas.height;

    var newWidth, newHeight;
    var userResized = resize;
    var $selector = ($('.nectar-box-roll').length > 0) ? '.nectar-box-roll': '.nectar-particles';
    var heightDiff = (Nodes.textPosition == 'bottom' && Nodes.textPositionH == 'center') ? $(Nodes.canvasID).parents($selector).find('.inner-wrap').height()/1.3 + 50 : 0;
    if(this.bgCanvas.height < 650) heightDiff = heightDiff/2;

    // If the image is too big for the screen... scale it down.
    if ( shapeIndex.width > this.bgCanvas.width - 50 - heightDiff || shapeIndex.height > this.bgCanvas.height - 50 - heightDiff) {

      var maxRatio = Math.max( shapeIndex.width / (this.bgCanvas.width - 50 ) , shapeIndex.height / (this.bgCanvas.height - 100 - heightDiff) );

      newWidth = shapeIndex.width / maxRatio;
      newHeight = shapeIndex.height / maxRatio;


      //change density based on ratio
      if(this.bgCanvas.width < 1600) {

	      if(maxRatio > 3 && maxRatio <= 4) {
	      		this.imgsToDraw[index].density = this.imgsToDraw[index].densityOriginal-3;
	      		if(this.imgsToDraw[index].maxParticleSize >= 3) this.imgsToDraw[index].maxParticleSize = this.imgsToDraw[index].maxParticleSizeOriginal-1;

	      }
	      else if(maxRatio > 4) {
	      		if(this.bgCanvas.width > 800)this.imgsToDraw[index].density = this.imgsToDraw[index].densityOriginal-4;
	      		else this.imgsToDraw[index].density = this.imgsToDraw[index].densityOriginal-5;

	      		if(this.imgsToDraw[index].maxParticleSize > 2) this.imgsToDraw[index].maxParticleSize = 2;
	      } else if (maxRatio <= 3){
	      		this.imgsToDraw[index].density = this.imgsToDraw[index].densityOriginal;
	      		this.imgsToDraw[index].maxParticleSize = this.imgsToDraw[index].maxParticleSizeOriginal;
	      		
	      }
	  } else {
	  		this.imgsToDraw[index].density = this.imgsToDraw[index].densityOriginal;
	      	this.imgsToDraw[index].maxParticleSize = this.imgsToDraw[index].maxParticleSizeOriginal;
	  }


    } else {
      newWidth = shapeIndex.width;
      newHeight = shapeIndex.height;
    }

    // Draw to background canvas
    var headerHeight = ($('#header-outer[data-transparent-header="true"]').length > 0 && $('body.mobile').length == 0 || $('#header-outer[data-permanent-transparent="1"]').length > 0) ? 0 : $('#header-outer').height();

    this.bgContext = this.bgCanvas.getContext( '2d' );
    this.bgContext.drawImage( shapeIndex, (this.canvas.width - newWidth) / 2, (((this.canvas.height+headerHeight/2) - newHeight - heightDiff*1) / 2) , newWidth, newHeight);
    this.bgContextPixelData = this.bgContext.getImageData( 0, 0, this.bgCanvas.width, this.bgCanvas.height );

    this.preparePoints(index,index2,userResized);

  

  },
  


  // Resize and redraw the canvas.
  onWindowResize: function() {
    cancelAnimationFrame( this.animation );
    if(Nodes.loadedCount == Nodes.imgsToDraw.length) {
      	for(var i=0;i<Nodes.imgsToDraw.length;i++) {
      		this.drawImageToBackground(i,null,true,false);
      	}
     }

     //set mobile state
     this.onMobile = (this.canvas.width <= 690) ? true : false;
  }

}


if($shapes.length > 0) Nodes.init('#canvas');

var $selector = ($('.nectar-box-roll').length > 0) ? '.nectar-box-roll': '.nectar-particles';
function initTextEffect(canvasID){
	if($(canvasID).parents('#page-header-bg').hasClass('topBoxIn')) return false;
	$timeOut = ($(canvasID).parents('#page-header-bg[data-text-effect="rotate_in"]').length > 0) ? 800 : 0;
	setTimeout(function(){
		$(canvasID).parents($selector).find('.inner-wrap.shape-1').css('z-index',100);
		$(canvasID).parents($selector).find('.inner-wrap.shape-1 .top-heading').transition({'opacity':1, 'y': 0},0);

		$(canvasID).parents($selector).find('.span_6').find('.wraped').each(function(i){
			$(this).find('span').delay(i*370).transition({ rotateX: '0', 'opacity' : 1, y: 0},400,'easeOutQuad');
		});

		setTimeout(function(){

			$(canvasID).parents($selector).find('.span_6').find('.inner-wrap.shape-1 > *:not(.top-heading)').each(function(i){
				$(this).delay(i*370).transition({ rotateX: '0', 'opacity' : 1, y: 0 },650,'easeOutQuad');
			});

			setTimeout(function(){
				$('.scroll-down-wrap').removeClass('hidden');

				if( Nodes.imgsToDraw.length > 1) {
					$('.pagination-dots .pagination-dot').each(function(i){
						$(this).delay(i*75).transition({ y: 0, 'opacity': 1}, 400);
					});
					$('.pagination-navigation .pagination-current').each(function(i){
						$(this).delay(i*75).transition({ y: 0, 'opacity': 1, scale: 1.15}, 400);
					});

					//init pag
					setTimeout(function(){
						initGooPagination();
					},$(canvasID).parents($selector).find('.pagination-dot').length*75 +370);
				}

			}, $(canvasID).parents($selector).find('.inner-wrap.shape-1 > *:not(.top-heading)').length-1 * 400 + 370);

		}, ($(canvasID).parents($selector).find('.span_6').find('.wraped').length * 370));

	},$timeOut);

}


/*pagination*/
	var $dots=$($selector+" .pagination-dot")
			,$current=$($selector+" .pagination-current")
			,$items=$($selector+" .pagination-item")

			,spacing=parseFloat($dots.css("height"))+(parseFloat($dots.css("marginBottom"))*2)
			,halfSpacing=spacing/2
			,startPos

			,itemsSpacing=450
			,lastItem=0
			,lastItemR=0
			,lastTime=Date.now()
		;

	function initGooPagination(){
		startPos=0;
	
		$current.data("pos",{y:startPos});
		
		$dots.click(function(event){


			var $cur=$(this);
			

			var dest=($cur.index())*spacing;


			//switch shape
			if(event.originalEvent !== undefined && !$cur.hasClass('active')) Nodes.particleRotateLogic($(this).index());
			$($selector+' .pagination-dot').removeClass('active');
			$cur.addClass('active');

		});
		$dots.eq(0).click();

		$items.click(function(){
			$dots.eq($(this).index()).click();

		});
		var dragging=false;
		var startDrag={x:0,y:0};
	}

	



//box roll
var perspect = 'not-rolled';
var animating = 'false';
function boxRoll(e,d) {
	
	if($('#slide-out-widget-area.open').length > 0) return false;

	if(perspect == 'not-rolled' && animating == 'false' && d == -1 && $('.nectar-box-roll canvas[data-loaded="true"]').length > 0) {
		perspect = 'rolled';
		animating = 'true';
		//stop mouse movement
		Nodes.mouse.x = 2000;
	  	Nodes.mouse.y = -2000;

	  	//slow fps
	  	Nodes.regularAnimation = false;
	  	
		setTimeout(function(){ 
					animating ='false'; 
		},1650);
		
		clearTimeout(Nodes.timeoutHolder);

	}

	else if(perspect == 'rolled' && animating == 'false' && d == 1 && $(window).scrollTop() < 100) {

	
		perspect = 'not-rolled';
		animating = 'true';
		
		setTimeout(function(){ 
			
			animating ='false'; 
			Nodes.regularAnimation = true;
		},1600);

		if(Nodes.randomMovement == false) {
			setTimeout(function(){ 
				//start animation again
			    Nodes.draw();
				Nodes.fps = 43;
				Nodes.fpsDec = 0.13;
				Nodes.decMultiplier = 0.06;
			},1630);

		}

		if(Nodes.randomMovement == true) {
			 Nodes.draw();

			 setTimeout(function(){
			 	Nodes.impulsX = Math.random()*800-400;
       	   	    Nodes.impulsY = -Math.random()*400;
       	   	   
			 },400);
			 setTimeout(function(){  Nodes.particleRotateLogic(false); },3400);
			 
		}
		//Nodes.randomMovement = false;
		if(Nodes.randomMovement == false) Nodes.particlesRotate(true); 
	
	}
	
}

if($shapes.length > 0) {

  $('body').mousewheel(function(event, delta) {
  	if($('#slide-out-widget-area.open.fullscreen').length > 0) return false;
  	boxRoll(event,delta);
  });

  $('body').on('click','.nectar-box-roll .section-down-arrow',function(){
  	boxRoll(null,-1);
  	$(this).addClass('hovered');
  	return false;
  });

  //touch 
  if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {
  	$('body').swipe({
      swipeStatus: function(event, phase, direction, distance, duration, fingers) {
        if($('#slide-out-widget-area.open').length > 0) return false;
        if(direction == 'up') {
          boxRoll(null,-1);
          if($('#ajax-content-wrap.no-scroll').length == 0) $('body').swipe("option", "allowPageScroll", 'vertical');
        } else if(direction == "down" && $(window).scrollTop() == 0) {
          boxRoll(null,1);
          $('body').swipe("option", "allowPageScroll", 'auto');
        }
      }
    });

  }

}
/*
if($('body[data-ajax-transitions="true"]').length > 0 && $('#ajax-loading-screen[data-method="ajax"]').length > 0 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) && $(window).width() > 690 ) {
    $(window).on("pronto.request",initSetup);
} */

function updateRowRightPadding(d){
    $('.wpb_row.full-width-section').each(function(){
      if($(this).hasClass('extraPadding') && d == 1) {
        $(this).css('padding-right',parseInt($(this).css('padding-right')) - parseInt($('body').attr('data-scrollbar-width')) + 'px' ).removeClass('extraPadding');
      } else {
        $(this).css('padding-right',parseInt($('body').attr('data-scrollbar-width')) + parseInt($(this).css('padding-right')) + 'px' ).addClass('extraPadding');
      } 
    });
}


});