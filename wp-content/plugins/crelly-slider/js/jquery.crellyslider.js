/**
 * Plugin Name: Crelly Slider
 * Plugin URI: https://wordpress.org/plugins/crelly-slider/
 * Description: A free responsive slider that supports layers. Add texts, images, videos and beautify them with transitions and animations.
 * Version: 1.3.4
 * Author: Fabio Rinaldi
 * Author URI: https://github.com/fabiorino
 * License: MIT
 */

/*************/
/** GLOBALS **/
/*************/

// Using these two variables we can check if we still need to load the APIs for YouTube and Vimeo
var crellyslider_youtube_api_ready = false;
var crellyslider_vimeo_api_ready = false;

(function($) {

	/************************/
	/** EXTERNAL RESOURCES **/
	/************************/

	// Custom build of jQuery mobile. I need it for swipeleft and swiperight
	(function(e,t,n){typeof define=="function"&&define.amd?define(["jquery"],function(r){return n(r,e,t),r.mobile}):n(e.jQuery,e,t)})(this,document,function(e,t,n,r){(function(e,t,n,r){function T(e){while(e&&typeof e.originalEvent!="undefined")e=e.originalEvent;return e}function N(t,n){var i=t.type,s,o,a,l,c,h,p,d,v;t=e.Event(t),t.type=n,s=t.originalEvent,o=e.event.props,i.search(/^(mouse|click)/)>-1&&(o=f);if(s)for(p=o.length,l;p;)l=o[--p],t[l]=s[l];i.search(/mouse(down|up)|click/)>-1&&!t.which&&(t.which=1);if(i.search(/^touch/)!==-1){a=T(s),i=a.touches,c=a.changedTouches,h=i&&i.length?i[0]:c&&c.length?c[0]:r;if(h)for(d=0,v=u.length;d<v;d++)l=u[d],t[l]=h[l]}return t}function C(t){var n={},r,s;while(t){r=e.data(t,i);for(s in r)r[s]&&(n[s]=n.hasVirtualBinding=!0);t=t.parentNode}return n}function k(t,n){var r;while(t){r=e.data(t,i);if(r&&(!n||r[n]))return t;t=t.parentNode}return null}function L(){g=!1}function A(){g=!0}function O(){E=0,v.length=0,m=!1,A()}function M(){L()}function _(){D(),c=setTimeout(function(){c=0,O()},e.vmouse.resetTimerDuration)}function D(){c&&(clearTimeout(c),c=0)}function P(t,n,r){var i;if(r&&r[t]||!r&&k(n.target,t))i=N(n,t),e(n.target).trigger(i);return i}function H(t){var n=e.data(t.target,s),r;!m&&(!E||E!==n)&&(r=P("v"+t.type,t),r&&(r.isDefaultPrevented()&&t.preventDefault(),r.isPropagationStopped()&&t.stopPropagation(),r.isImmediatePropagationStopped()&&t.stopImmediatePropagation()))}function B(t){var n=T(t).touches,r,i,o;n&&n.length===1&&(r=t.target,i=C(r),i.hasVirtualBinding&&(E=w++,e.data(r,s,E),D(),M(),d=!1,o=T(t).touches[0],h=o.pageX,p=o.pageY,P("vmouseover",t,i),P("vmousedown",t,i)))}function j(e){if(g)return;d||P("vmousecancel",e,C(e.target)),d=!0,_()}function F(t){if(g)return;var n=T(t).touches[0],r=d,i=e.vmouse.moveDistanceThreshold,s=C(t.target);d=d||Math.abs(n.pageX-h)>i||Math.abs(n.pageY-p)>i,d&&!r&&P("vmousecancel",t,s),P("vmousemove",t,s),_()}function I(e){if(g)return;A();var t=C(e.target),n,r;P("vmouseup",e,t),d||(n=P("vclick",e,t),n&&n.isDefaultPrevented()&&(r=T(e).changedTouches[0],v.push({touchID:E,x:r.clientX,y:r.clientY}),m=!0)),P("vmouseout",e,t),d=!1,_()}function q(t){var n=e.data(t,i),r;if(n)for(r in n)if(n[r])return!0;return!1}function R(){}function U(t){var n=t.substr(1);return{setup:function(){q(this)||e.data(this,i,{});var r=e.data(this,i);r[t]=!0,l[t]=(l[t]||0)+1,l[t]===1&&b.bind(n,H),e(this).bind(n,R),y&&(l.touchstart=(l.touchstart||0)+1,l.touchstart===1&&b.bind("touchstart",B).bind("touchend",I).bind("touchmove",F).bind("scroll",j))},teardown:function(){--l[t],l[t]||b.unbind(n,H),y&&(--l.touchstart,l.touchstart||b.unbind("touchstart",B).unbind("touchmove",F).unbind("touchend",I).unbind("scroll",j));var r=e(this),s=e.data(this,i);s&&(s[t]=!1),r.unbind(n,R),q(this)||r.removeData(i)}}}var i="virtualMouseBindings",s="virtualTouchID",o="vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),u="clientX clientY pageX pageY screenX screenY".split(" "),a=e.event.mouseHooks?e.event.mouseHooks.props:[],f=e.event.props.concat(a),l={},c=0,h=0,p=0,d=!1,v=[],m=!1,g=!1,y="addEventListener"in n,b=e(n),w=1,E=0,S,x;e.vmouse={moveDistanceThreshold:10,clickDistanceThreshold:10,resetTimerDuration:1500};for(x=0;x<o.length;x++)e.event.special[o[x]]=U(o[x]);y&&n.addEventListener("click",function(t){var n=v.length,r=t.target,i,o,u,a,f,l;if(n){i=t.clientX,o=t.clientY,S=e.vmouse.clickDistanceThreshold,u=r;while(u){for(a=0;a<n;a++){f=v[a],l=0;if(u===r&&Math.abs(f.x-i)<S&&Math.abs(f.y-o)<S||e.data(u,s)===f.touchID){t.preventDefault(),t.stopPropagation();return}}u=u.parentNode}}},!0)})(e,t,n),function(e){e.mobile={}}(e),function(e,t){var r={touch:"ontouchend"in n};e.mobile.support=e.mobile.support||{},e.extend(e.support,r),e.extend(e.mobile.support,r)}(e),function(e,t,r){function l(t,n,i,s){var o=i.type;i.type=n,s?e.event.trigger(i,r,t):e.event.dispatch.call(t,i),i.type=o}var i=e(n),s=e.mobile.support.touch,o="touchmove scroll",u=s?"touchstart":"mousedown",a=s?"touchend":"mouseup",f=s?"touchmove":"mousemove";e.each("touchstart touchmove touchend tap taphold swipe swipeleft swiperight scrollstart scrollstop".split(" "),function(t,n){e.fn[n]=function(e){return e?this.bind(n,e):this.trigger(n)},e.attrFn&&(e.attrFn[n]=!0)}),e.event.special.scrollstart={enabled:!0,setup:function(){function s(e,n){r=n,l(t,r?"scrollstart":"scrollstop",e)}var t=this,n=e(t),r,i;n.bind(o,function(t){if(!e.event.special.scrollstart.enabled)return;r||s(t,!0),clearTimeout(i),i=setTimeout(function(){s(t,!1)},50)})},teardown:function(){e(this).unbind(o)}},e.event.special.tap={tapholdThreshold:750,emitTapOnTaphold:!0,setup:function(){var t=this,n=e(t),r=!1;n.bind("vmousedown",function(s){function a(){clearTimeout(u)}function f(){a(),n.unbind("vclick",c).unbind("vmouseup",a),i.unbind("vmousecancel",f)}function c(e){f(),!r&&o===e.target?l(t,"tap",e):r&&e.preventDefault()}r=!1;if(s.which&&s.which!==1)return!1;var o=s.target,u;n.bind("vmouseup",a).bind("vclick",c),i.bind("vmousecancel",f),u=setTimeout(function(){e.event.special.tap.emitTapOnTaphold||(r=!0),l(t,"taphold",e.Event("taphold",{target:o}))},e.event.special.tap.tapholdThreshold)})},teardown:function(){e(this).unbind("vmousedown").unbind("vclick").unbind("vmouseup"),i.unbind("vmousecancel")}},e.event.special.swipe={scrollSupressionThreshold:30,durationThreshold:1e3,horizontalDistanceThreshold:30,verticalDistanceThreshold:30,getLocation:function(e){var n=t.pageXOffset,r=t.pageYOffset,i=e.clientX,s=e.clientY;if(e.pageY===0&&Math.floor(s)>Math.floor(e.pageY)||e.pageX===0&&Math.floor(i)>Math.floor(e.pageX))i-=n,s-=r;else if(s<e.pageY-r||i<e.pageX-n)i=e.pageX-n,s=e.pageY-r;return{x:i,y:s}},start:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y],origin:e(t.target)}},stop:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y]}},handleSwipe:function(t,n,r,i){if(n.time-t.time<e.event.special.swipe.durationThreshold&&Math.abs(t.coords[0]-n.coords[0])>e.event.special.swipe.horizontalDistanceThreshold&&Math.abs(t.coords[1]-n.coords[1])<e.event.special.swipe.verticalDistanceThreshold){var s=t.coords[0]>n.coords[0]?"swipeleft":"swiperight";return l(r,"swipe",e.Event("swipe",{target:i,swipestart:t,swipestop:n}),!0),l(r,s,e.Event(s,{target:i,swipestart:t,swipestop:n}),!0),!0}return!1},eventInProgress:!1,setup:function(){var t,n=this,r=e(n),s={};t=e.data(this,"mobile-events"),t||(t={length:0},e.data(this,"mobile-events",t)),t.length++,t.swipe=s,s.start=function(t){if(e.event.special.swipe.eventInProgress)return;e.event.special.swipe.eventInProgress=!0;var r,o=e.event.special.swipe.start(t),u=t.target,l=!1;s.move=function(t){if(!o||t.isDefaultPrevented())return;r=e.event.special.swipe.stop(t),l||(l=e.event.special.swipe.handleSwipe(o,r,n,u),l&&(e.event.special.swipe.eventInProgress=!1)),Math.abs(o.coords[0]-r.coords[0])>e.event.special.swipe.scrollSupressionThreshold&&t.preventDefault()},s.stop=function(){l=!0,e.event.special.swipe.eventInProgress=!1,i.off(f,s.move),s.move=null},i.on(f,s.move).one(a,s.stop)},r.on(u,s.start)},teardown:function(){var t,n;t=e.data(this,"mobile-events"),t&&(n=t.swipe,delete t.swipe,t.length--,t.length===0&&e.removeData(this,"mobile-events")),n&&(n.start&&e(this).off(u,n.start),n.move&&i.off(f,n.move),n.stop&&i.off(a,n.stop))}},e.each({scrollstop:"scrollstart",taphold:"tap",swipeleft:"swipe.left",swiperight:"swipe.right"},function(t,n){e.event.special[t]={setup:function(){e(this).bind(n,e.noop)},teardown:function(){e(this).unbind(n)}}})}(e,this)});

	// YouTube API:
	function loadYoutubeAPI() {
		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		crellyslider_youtube_api_ready = true;
	}

	// Vimeo API
	function loadVimeoAPI() {
		var Froogaloop=function(){function e(a){return new e.fn.init(a)}function g(a,c,b){if(!b.contentWindow.postMessage)return!1;a=JSON.stringify({method:a,value:c});b.contentWindow.postMessage(a,h)}function l(a){var c,b;try{c=JSON.parse(a.data),b=c.event||c.method}catch(e){}"ready"!=b||k||(k=!0);if(!/^https?:\/\/player.vimeo.com/.test(a.origin))return!1;"*"===h&&(h=a.origin);a=c.value;var m=c.data,f=""===f?null:c.player_id;c=f?d[f][b]:d[b];b=[];if(!c)return!1;void 0!==a&&b.push(a);m&&b.push(m);f&&b.push(f);
		return 0<b.length?c.apply(null,b):c.call()}function n(a,c,b){b?(d[b]||(d[b]={}),d[b][a]=c):d[a]=c}var d={},k=!1,h="*";e.fn=e.prototype={element:null,init:function(a){"string"===typeof a&&(a=document.getElementById(a));this.element=a;return this},api:function(a,c){if(!this.element||!a)return!1;var b=this.element,d=""!==b.id?b.id:null,e=c&&c.constructor&&c.call&&c.apply?null:c,f=c&&c.constructor&&c.call&&c.apply?c:null;f&&n(a,f,d);g(a,e,b);return this},addEvent:function(a,c){if(!this.element)return!1;
		var b=this.element,d=""!==b.id?b.id:null;n(a,c,d);"ready"!=a?g("addEventListener",a,b):"ready"==a&&k&&c.call(null,d);return this},removeEvent:function(a){if(!this.element)return!1;var c=this.element,b=""!==c.id?c.id:null;a:{if(b&&d[b]){if(!d[b][a]){b=!1;break a}d[b][a]=null}else{if(!d[a]){b=!1;break a}d[a]=null}b=!0}"ready"!=a&&b&&g("removeEventListener",a,c)}};e.fn.init.prototype=e.fn;window.addEventListener?window.addEventListener("message",l,!1):window.attachEvent("onmessage",l);return window.Froogaloop=
		window.$f=e}();

		crellyslider_vimeo_api_ready = true;
	}

	/*******************/
	/** CRELLY SLIDER **/
	/*******************/

	$.CrellySlider = function(target, settings) {

		/**********************/
		/** USEFUL VARIABLES **/
		/**********************/

		// HTML classes of the slider
		var SLIDER 	 = $(target);
		var CRELLY 	 = 'div.crellyslider';
		var SLIDES 	 = 'ul.cs-slides';
		var SLIDE  	 = 'li.cs-slide';
		var ELEMENTS = '> *';

		var total_slides;
		var current_slide = 0;

		var paused = false;
		var can_pause = false; // Also used as "can change slide"
		var executed_slide = false; // Will be true as soon as the current slide is executed
		var first_play = true;

		// Slide timer: only current slide. Elements timers: all the elements. This prevents conflicts during changes and pauses
		var current_slide_time_timer = new Timer(function() {}, 0);
		var elements_times_timers = new Array();
		var elements_delays_timers = new Array();

		// The arrays "link" every DOM iframe element to its player element that can interact with APIs
		var youtube_videos = {};
		var vimeo_videos = {};

		var scale = 1;
		var window_width_before_setResponsive = 0; // This variable is useful ONLY to prevent that window.resize fires on vertical resizing or on a right window width

		/********************/
		/** INITIALIZATION **/
		/********************/

		// EVERYTHING BEGINS HERE

		// Load necessary APIs
		if(! crellyslider_youtube_api_ready && thereAreVideos('youtube')) {
			loadYoutubeAPI();
		}
		if(! crellyslider_vimeo_api_ready && thereAreVideos('vimeo')) {
			loadVimeoAPI();
		}

		// Before initializing Crelly Slider, we have to wait for the YouTube API. I use the setInterval method to prevent compatibility issues with other plugins and to be sure that, if there is more than a slider loaded on the page, everything works
		if(crellyslider_youtube_api_ready && (typeof(YT) == 'undefined' || typeof(YT.Player) == 'undefined')) {
			var temp = setInterval(function() {
				if(typeof(YT) != 'undefined' && typeof(YT.Player) != 'undefined') {
					clearInterval(temp);
					init();
				}
			}, 100);
		}
		else {
			init();
		}

		// Returns an array like this: {youtube = true, vimeo = false} if there are YouTube videos but not Vimeo videos
		// This function can be called before init()
		function thereAreVideos(platform) {
			if(platform == 'youtube') {
				return SLIDER.find('.cs-yt-iframe').length > 0 ? true : false;
			}
			if(platform == 'vimeo') {
				return SLIDER.find('.cs-vimeo-iframe').length > 0 ? true : false;
			}

			return -1;
		}

		// The slider constructor: runs automatically only the first time, sets the basic needs of the slider and the preloader then runs Crelly Slider
		function init() {
			// Add wrappers and classes
			SLIDER.wrapInner('<div class="crellyslider" />');
			SLIDER.find(CRELLY + ' > ul').addClass('cs-slides');
			SLIDER.find(CRELLY + ' ' + SLIDES + ' > li').addClass('cs-slide');

			// Set total_slides
			total_slides = getSlides().length;

			// If the slider is empty, stop
			if(total_slides == 0) {
				return false;
			}

			// If there is only a slide, clone it
			if(total_slides == 1) {
				var clone = getSlide(0);
				var prepend = SLIDER.find(CRELLY).find(SLIDES);
				clone.clone().prependTo(prepend);
				total_slides++;
			}

			orderSlides();

			// Show controls (previous and next arrows)
			if(settings.showControls) {
				SLIDER.find(CRELLY).append('<div class="cs-controls"><span class="cs-next"></span><span class="cs-previous"></span></div>');
			}

			// Show navigation
			if(settings.showNavigation) {
				var nav = '<div class="cs-navigation">';
				for(var i = 0; i < total_slides; i++) {
					nav += '<span class="cs-slide-link"></span>';
				}
				nav += '</div>';
				SLIDER.find(CRELLY).append(nav);
			}

			// Show progress bar
			if(settings.showProgressBar) {
				SLIDER.find(CRELLY).append('<div class="cs-progress-bar"></div>');
			}
			else {
				SLIDER.find(CRELLY).append('<div class="cs-progress-bar cs-progress-bar-hidden"></div>');
			}

			// Display slider
			SLIDER.css('display', 'block');

			// Set layout for the first time
			if(settings.responsive) {
				setScale();
			}
			setLayout();

			// Set slides links
			getSlides().find('.cs-background-link')
			.html(' ')
			.data({
				'left' : 0,
				'top' : 0,
				'in' : 'none',
				'out' : 'none',
				'easeIn' : 0,
				'easeOut' : 0,
				'delay' : 0,
				'time' : 'all',
			});

			setPreloader();

			initVideos().done(function() {
				// Timeout needed to prevent compatibility issues
				var loading = setInterval(function() {
					if(document.readyState == 'complete' && SLIDER.find(CRELLY).find('.cs-preloader').length > 0) { // If window.load and preloader is loaded
						clearInterval(loading);
						loadedWindow();
					}
				}, 100);
			});
		}

		// Orders the slides by rearranging them in the DOM
		function orderSlides() {
			// If randomOrder is disabled and the initial slide is the first, the slides are already ordered
			if(! settings.randomOrder && settings.startFromSlide == 0) {
				return;
			}

			var slides_order = new Array();
			var ordered_slides = new Array();

			// Set the first slide according to the settings
			if(settings.startFromSlide == -1) {
				var index = Math.floor((Math.random() * total_slides));
				slides_order[0] = index;
				ordered_slides[0] = getSlide(index);
			}
			else {
				slides_order[0] = settings.startFromSlide;
				ordered_slides[0] = getSlide(settings.startFromSlide);
			}

			// Set all the other slides
			for(var i = 1; i < total_slides; i++) {
				var index;

				if(settings.randomOrder) { // Get a random slide index that was never generated before
					do {
						index = Math.floor((Math.random() * total_slides));
					} while(slides_order.indexOf(index) != -1);
				}
				else { // Get the next index
					if(i + slides_order[0] < total_slides) {
						index = i + slides_order[0];
					}
					else {
						index = i + slides_order[0] - total_slides;
					}
				}

				slides_order[i] = index;
				ordered_slides[i] = getSlide(index);
			}

			// Delete all the slides
			SLIDER.find(CRELLY).find(SLIDES).empty();

			// Put the slides that are now ordered
			for(var i = 0; i < total_slides; i++) {
				SLIDER.find(CRELLY).find(SLIDES).append(ordered_slides[i]);
			}
		}

		// Inits Youtube and Vimeo videos
		function initVideos() {
			var def = new $.Deferred();
			var total_iframes = getSlides().find('.cs-yt-iframe, .cs-vimeo-iframe').length;
			var loaded_iframes = 0;

			if(total_iframes == 0) {
				return def.resolve().promise();
			}

			// When iframes are loaded...
			getSlides().find('.cs-yt-iframe, .cs-vimeo-iframe').each(function() {
				var iframe = $(this);

				iframe.one('load', function() {
					loaded_iframes++;
					if(loaded_iframes == total_iframes) {
						// ...init videos
						initYoutubeVideos().done(function() {
							initVimeoVideos().done(function() {
								def.resolve();
							});
						});
					}
				})
			});

			return def.promise();
		}

		// Generates an unique id for each youtube iframe, then links them to a new YouTube player
		function initYoutubeVideos() {
			var def = new $.Deferred();
			var slides = getSlides();
			var total_yt_videos = slides.find(ELEMENTS + '.cs-yt-iframe').length;
			var loaded_videos = 0;
			var temp;

			if(total_yt_videos == 0) {
				return def.resolve().promise();
			}

			slides.each(function() {
				var slide = $(this);
				var elements = slide.find(ELEMENTS + '.cs-yt-iframe');

				elements.each(function() {
					var element = $(this);

					element.uniqueId();
					element.attr('id', 'cs-yt-iframe-' + element.attr('id'));

					var player = new YT.Player(element.attr('id'), {
						events: {
							'onReady' : function() {
								loaded_videos++;
								if(loaded_videos == total_yt_videos) {
									def.resolve();
								}
							},

							'onStateChange' : function(e) {
								if(e.data === YT.PlayerState.ENDED && getItemData(element, 'loop')) {
									player.playVideo();
								}

								if(can_pause) {
									if(e.data === YT.PlayerState.PAUSED) {
										youtube_videos[element.attr('id')].manually_paused = true;
									}
									if(e.data === YT.PlayerState.PLAYING) {
										youtube_videos[element.attr('id')].manually_paused = false;
									}
								}						
							},
						},
					});

					temp = {
						player : player,
						played_once : false,
						manually_paused : false,
					};

					youtube_videos[element.attr('id')] = temp;
				});
			});

			return def.promise();
		}

		// Generates an unique id for each Vimeo iframe, then links them to a new Vimeo player
		function initVimeoVideos() {
			var def = new $.Deferred();
			var slides = getSlides();
			var total_vimeo_videos = slides.find(ELEMENTS + '.cs-vimeo-iframe').length;
			var loaded_videos = 0;
			var temp;

			if(total_vimeo_videos == 0) {
				return def.resolve().promise();
			}

			slides.each(function() {
				var slide = $(this);
				var elements = slide.find(ELEMENTS + '.cs-vimeo-iframe');

				elements.each(function() {
					var element = $(this);

					element.uniqueId();
					element.attr('id', 'cs-vimeo-iframe-' + element.attr('id'));
					element.attr('src', element.attr('src') + '&player_id=' + element.attr('id'));

					var player = $f(element[0]);

					player.addEvent('ready', function() {
						player.addEvent('finish', function() {
							vimeo_videos[element.attr('id')].ended = true;
						});

						player.addEvent('play', function() {
							vimeo_videos[element.attr('id')].played_once = true;
							vimeo_videos[element.attr('id')].ended = false;

							if(can_pause) {
								vimeo_videos[element.attr('id')].manually_paused = false;
							}
						});

						player.addEvent('pause', function() {
							if(can_pause) {
								vimeo_videos[element.attr('id')].manually_paused = true;
							}
						});

						if(getItemData(element, 'loop')) {
							player.api('setLoop', true);
						}

						loaded_videos++;
						if(loaded_videos == total_vimeo_videos) {
							def.resolve();
						}
					});

					temp = {
						player : player,
						played_once : false,
						ended : false,
						manually_paused : false,
					};

					vimeo_videos[element.attr('id')] = temp;
				});
			});

			return def.promise();
		}

		// Does operations after window.load is complete. Need to do it as a function for back-end compatibility
		function loadedWindow() {
			// Set layout for the second time
			if(settings.responsive) {
				setScale();
			}
			setLayout();

			window_width_before_setResponsive = $(window).width();

			initProperties();

			addListeners();

			unsetPreloader();

			settings.beforeStart();

			// Positions and responsive dimensions then run
			if(settings.responsive) {
				setResponsive();
			}
			else {
				play();
			}
		}

		// Stores original slides, elements and elements contents values then hides all the slides
		function initProperties() {
			getSlides().each(function() {
				var slide = $(this);

				slide.find(ELEMENTS).each(function() {
					var element = $(this);

					element.find('*').each(function() {
						var element_content = $(this);						
						setElementData(element_content);
					});

					setElementData(element);
				});

				slide.css('display', 'none');
				slide.data('opacity', parseFloat(slide.css('opacity')));
			});
		}

		// Initializes the element with original values
		function setElementData(element) {
			element.data('width', parseFloat(element.width()));
			element.data('height', parseFloat(element.height()));
			element.data('letter-spacing', parseFloat(element.css('letter-spacing')));
			element.data('font-size', parseFloat(element.css('font-size')));

			if(element.css('line-height').slice(-2).toLowerCase() == 'px') {
				// if pixel values are given, use those
				element.data('line-height', parseFloat(element.css('line-height')));
			}
			else if(element.css('line-height') == 'normal') {
				// if the browser returns 'normal' then use a default factor of 1.15 * font-size
				// see: http://meyerweb.com/eric/thoughts/2008/05/06/line-height-abnormal/
				element.data('line-height', getItemData(element, 'font-size') * 1.15);
			}
			else {
				// otherwise assume that the returned value is a factor and multiply it with the font-size
				element.data('line-height', parseFloat(element.css('line-height')) * getItemData(element, 'font-size'));
			}

			element.data('padding-top', parseFloat(element.css('padding-top')));
			element.data('padding-right', parseFloat(element.css('padding-right')));
			element.data('padding-bottom', parseFloat(element.css('padding-bottom')));
			element.data('padding-left', parseFloat(element.css('padding-left')));
			element.data('opacity', parseFloat(element.css('opacity')));
		}

		// Sets all listeners for the user interaction
		function addListeners() {
			// Make responsive. Run if resizing horizontally and the slider is not at the right dimension
			if(settings.responsive) {
				$(window).resize(function() {
					if(window_width_before_setResponsive != $(window).width() && ((settings.layout == 'full-width' && getWidth() != $(SLIDER).width()) || ($(SLIDER).width() < getWidth() || (($(SLIDER).width() > getWidth()) && getWidth() < settings.startWidth)))) {
						setResponsive();
					}
				});
			}

			// Compatibility with Popup Maker (https://wordpress.org/plugins/popup-maker/)
			/*$(document).on('pumAfterOpen', '.pum', function() {
				if($(this).find(CRELLY).length > 0) {
					setResponsive();
				}
			});*/

			// Previous control click
			SLIDER.find(CRELLY).find('.cs-controls > .cs-previous').click(function() {
				changeSlide(getPreviousSlide());
			});

			// Next Control click
			SLIDER.find(CRELLY).find('.cs-controls > .cs-next').click(function() {
				changeSlide(getNextSlide());
			});

			// Swipe and drag
			if(settings.enableSwipe) {
				SLIDER.find(CRELLY).on('swipeleft', function() {
					resume();
					changeSlide(getNextSlide());
				});

				SLIDER.find(CRELLY).on('swiperight', function() {
					resume();
					changeSlide(getPreviousSlide());
				});
			}

			// Navigation link click
			SLIDER.find(CRELLY).find('.cs-navigation > .cs-slide-link').click(function() {
				changeSlide($(this).index());
			});

			// Pause on hover
			if(settings.pauseOnHover) {
				SLIDER.find(CRELLY).find(SLIDES).hover(function() {
					pause();
				});

				SLIDER.find(CRELLY).find(SLIDES).mouseleave(function() {
					resume();
				});
			}
		}

		// Hides the unnecessary divs and sets the blurred preloader and the gif spinner
		function setPreloader() {
			// Setup
			SLIDER.find(CRELLY).find(SLIDES).css('visibility', 'hidden');
			SLIDER.find(CRELLY).find('.cs-progress-bar').css('display', 'none');
			SLIDER.find(CRELLY).find('.cs-navigation').css('display', 'none');
			SLIDER.find(CRELLY).find('.cs-controls').css('display', 'none');

			// Get the URL of the background image of the first slide
			var img_url = getSlide(0).css('background-image');
			img_url = img_url.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');

			if(! img_url.match(/\.(jpeg|jpg|gif|png|bmp|tiff|tif)$/)) { // If there isn't a background image
				addPreloaderHTML();
			}
			else {
				// When the background image of the first slide is loaded
				$('<img>')
				.load(function() {
					addPreloaderHTML();
				})
				.attr('src', img_url)
				.each(function() {
					if(this.complete) {
						$(this).load();
					}
				});
			}

			function addPreloaderHTML() {
				// Add preloader
				SLIDER.find(CRELLY).append('<div class="cs-preloader"><div class="cs-bg"></div><div class="cs-loader"><div class="cs-spinner"></div></div></div>');

				// Set background. Background is set to both the preloader div and the bg div to fix the CSS blur effect
				SLIDER.find(CRELLY).find('.cs-preloader').css({
					'background-color' : getSlide(current_slide).css('background-color'),
					'background-image' : getSlide(current_slide).css('background-image'),
					'background-position' : getSlide(current_slide).css('background-position'),
					'background-repeat' : getSlide(current_slide).css('background-repeat'),
					'background-size' : getSlide(current_slide).css('background-size'),
				});
				SLIDER.find(CRELLY).find('.cs-preloader > .cs-bg').css({
					'background-color' : getSlide(current_slide).css('background-color'),
					'background-image' : getSlide(current_slide).css('background-image'),
					'background-position' : getSlide(current_slide).css('background-position'),
					'background-repeat' : getSlide(current_slide).css('background-repeat'),
					'background-size' : getSlide(current_slide).css('background-size'),
				});
			}
		}

		// Shows the necessary divs and fades out the preloader
		function unsetPreloader() {
			// Setup
			SLIDER.find(CRELLY).find(SLIDES).css('visibility', 'visible');
			SLIDER.find(CRELLY).find('.cs-progress-bar').css('display', 'block');
			SLIDER.find(CRELLY).find('.cs-navigation').css('display', 'block');
			SLIDER.find(CRELLY).find('.cs-controls').css('display', 'block');

			// Display the first slide to avoid the slide in animation
			slideIn(getSlide(0));
			getSlide(0).finish();

			// Fade out
			SLIDER.find(CRELLY).find('.cs-preloader').animate({
				'opacity' : 0,
			}, 300, function() {
				SLIDER.find(CRELLY).find('.cs-preloader').remove();
			});
		}

		/*******************************/
		/** LAYOUT AND RESPONSIVENESS **/
		/*******************************/

		// Sets slider and slides. Width and height are scaled
		function setLayout() {
			var layout = settings.layout;
			var width, height;

			switch(layout) {
				case 'fixed':
					width  = settings.startWidth;
					height = settings.startHeight;
					SLIDER.find(CRELLY).css({
						'width'  : getScaled(width),
						'height' : getScaled(height),
					});
					getSlides().css({
						'width'  : getScaled(width),
						'height' : getScaled(height),
					});
					break;

				case 'full-width':
					width  = SLIDER.width();
					height = settings.startHeight;
					SLIDER.find(CRELLY).css({
						'width'  : width,
						'height' : getScaled(height),
					});
					getSlides().css({
						'width'  : width,
						'height' : getScaled(height),
					});
					break;
				default:
					return false;
					break;
			}
		}

		// Returns the element top end left gaps (when the slider is full-width is very useful)
		function getLayoutGaps(element) {
			var top_gap = (getHeight() - settings.startHeight) / 2;
			var left_gap = (getWidth() - settings.startWidth) / 2;

			var new_top = 0;
			var new_left = 0;

			if(top_gap > 0) {
				new_top = top_gap;
			}
			if(left_gap > 0) {
				new_left = left_gap;
			}

			return {
				top: new_top,
				left: new_left,
			};
		}

		// Scales every element to make it responsive. It automatically restarts the current slide
		function setResponsive() {
			settings.beforeSetResponsive();

			var slides = getSlides();

			stop(true);

			slides.each(function() {
				var slide = $(this);
				var elements = slide.find(ELEMENTS);

				slide.finish();
				slideIn(slide);
				slide.finish();

				elements.each(function() {
					var element = $(this);

					element.finish();
					elementIn(element);
					element.finish();

					if(isVideo(element)) {
						pauseVideo(element);
					}
				});
			});

			setScale();

			setLayout();

			slides.each(function() {
				var slide = $(this);
				var elements = slide.find(ELEMENTS);

				elements.each(function() {
					var element = $(this);

					element.find('*').each(function() {
						var element_content = $(this);
						scaleElement(element_content);
					});

					scaleElement(element);

					element.finish();
					elementOut(element);
					element.finish();

					if(isVideo(element)) {
						pauseVideo(element);
					}
				});

				slide.finish();
				slideOut(slide);
				slide.finish();
			});

			window_width_before_setResponsive = $(window).width();

			play();
		}

		// Scales a text or an image and their contents
		function scaleElement(element) {
			// Standard element
			element.css({
				'top' 			 : getScaled(getItemData(element, 'top') + getLayoutGaps(element).top),
				'left' 			 : getScaled(getItemData(element, 'left') + getLayoutGaps(element).left),
				'padding-top'	 : getScaled(getItemData(element, 'padding-top')),
				'padding-right'	 : getScaled(getItemData(element, 'padding-right')),
				'padding-bottom' : getScaled(getItemData(element, 'padding-bottom')),
				'padding-left'	 : getScaled(getItemData(element, 'padding-left')),
			});

			// Element contains text
			if(element.is('input') || element.is('button') || element.text().trim().length) {
				element.css({
					'line-height'	 : getScaled(getItemData(element, 'line-height')) + 'px',
					'letter-spacing' : getScaled(getItemData(element, 'letter-spacing')),
					'font-size'		 : getScaled(getItemData(element, 'font-size')),
				});
			}

			// Element doesn't contain text (like images or iframes)
			else {
				element.css({
					'width'  : getScaled(getItemData(element, 'width')),
					'height' : getScaled(getItemData(element, 'height')),
				});
			}
		}

		// Using the start dimensions, sets how the slider and it's elements should be scaled
		function setScale() {
			var slider_width = SLIDER.width();
			var start_width = settings.startWidth;

			if(slider_width >= start_width || ! settings.responsive) {
				scale = 1;
			}
			else {
				scale = slider_width / start_width;
			}
		}

		// Using the current scale variable, returns the value that receives correctly scaled. Remember to always use getScaled() to get positions & dimensions of the elements
		function getScaled(value) {
			return value * scale;
		}

		/*********************/
		/** SLIDER COMMANDS **/
		/*********************/

		// Runs Crelly from the current slide
		function play() {
			if(settings.automaticSlide) {
				loopSlides();
			}
			else {
				executeSlide(current_slide);
			}

			first_play = false;
		}

		// Stops all the slides and the elements and resets the progress bar
		function stop(finish_queues) {
			for(var i = 0; i < elements_times_timers.length; i++) {
				elements_times_timers[i].clear();
			}

			for(var i = 0; i < elements_delays_timers.length; i++) {
				elements_delays_timers[i].clear();
			}

			current_slide_time_timer.clear();

			getSlides().each(function() {
				var temp_slide = $(this);
				if(finish_queues) {
					temp_slide.finish();
				}
				else {
					temp_slide.stop(true, true);
				}
				temp_slide.find(ELEMENTS).each(function() {
					var temp_element = $(this);
					if(finish_queues) {
						temp_element.finish();
					}
					else {
						temp_element.stop(true, true);
					}
				});
			});

			resetProgressBar();
		}

		// Stops the progress bar and the slide time timer
		function pause() {
			if(! paused && can_pause) {
				settings.beforePause();

				var progress_bar = SLIDER.find(CRELLY).find('.cs-progress-bar');
				progress_bar.stop(true);
				current_slide_time_timer.pause();

				paused = true;
			}
		}

		// Animates until the end the progress bar and resumes the current slide time timer
		function resume() {
			if(paused && can_pause) {
				settings.beforeResume();

				var progress_bar = SLIDER.find(CRELLY).find('.cs-progress-bar');
				var slide_time = getItemData(getSlide(current_slide), 'time');
				var remained_delay = current_slide_time_timer.getRemaining();

				progress_bar.animate({
					'width' : '100%',
				}, remained_delay);

				current_slide_time_timer.resume();

				paused = false;
			}
		}

		/****************************************/
		/** SLIDER OR SLIDES DATAS / UTILITIES **/
		/****************************************/

		// Returns the Crelly Slider container width
		function getWidth() {
			return SLIDER.find(CRELLY).width();
		}

		// Returns the Crelly Slider container height
		function getHeight() {
			return SLIDER.find(CRELLY).height();
		}

		// Returns the index of the next slide
		function getNextSlide() {
			if(current_slide + 1  == total_slides) {
				return 0;
			}
			return current_slide + 1;
		}

		// Returns the index of the previous slide
		function getPreviousSlide() {
			if(current_slide - 1 < 0) {
				return total_slides - 1;
			}
			return current_slide - 1;
		}

		// Returns a "data" of an item (slide or element). If is an integer || float, returns the parseInt() || parseFloat() of it. If the slide or the element has no data returns the default value
		function getItemData(item, data) {
			var is_slide;

			if(item.parent('ul').hasClass('cs-slides')) {
				is_slide = true;
			}
			else {
				is_slide = false;
			}

			switch(data) {
				case 'ease-in' :
					if(is_slide) {
						return isNaN(parseInt(item.data(data))) ? settings.slidesEaseIn : parseInt(item.data(data));
					}
					else {
						return isNaN(parseInt(item.data(data))) ? settings.elementsEaseIn : parseInt(item.data(data));
					}
					break;

				case 'ease-out' :
					if(is_slide) {
						return isNaN(parseInt(item.data(data))) ? settings.slidesEaseOut : parseInt(item.data(data));
					}
					else {
						return isNaN(parseInt(item.data(data))) ? settings.elementsEaseOut : parseInt(item.data(data));
					}
					break;

				case 'delay' :
					return isNaN(parseInt(item.data(data))) ? settings.elementsDelay : parseInt(item.data(data));

					break;

				case 'time' :
					if(is_slide) {
						return isNaN(parseInt(item.data(data))) ? settings.slidesTime : parseInt(item.data(data));
					}
					else {
						if(item.data(data) == 'all') {
							return 'all';
						}
						else {
							return isNaN(parseInt(item.data(data))) ? settings.itemsTime : parseInt(item.data(data));
						}
					}
					break;

				case 'ignore-ease-out' :
					if(parseInt(item.data(data)) == 1) {
						return true;
					}
					else if(parseInt(item.data(data)) == 0) {
						return false;
					}
					return settings.ignoreElementsEaseOut;
					break;

				case 'autoplay' :
					if(parseInt(item.data(data)) == 1) {
						return true;
					}
					else if(parseInt(item.data(data)) == 0) {
						return false;
					}
					return settings.videoAutoplay;
					break;

				case 'loop' :
					if(parseInt(item.data(data)) == 1) {
						return true;
					}
					else if(parseInt(item.data(data)) == 0) {
						return false;
					}
					return settings.videoLoop;
					break;

				case 'top' :
				case 'left' :
				case 'width' :
				case 'height' :
				case 'padding-top' :
				case 'padding-right' :
				case 'padding-bottom' :
				case 'padding-left' :
				case 'line-height' :
				case 'letter-spacing' :
				case 'font-size' :
					return isNaN(parseFloat(item.data(data))) ? 0 : parseFloat(item.data(data));
					break;

				case 'in' :
				case 'out' :
				case 'opacity' :
					return item.data(data);
					break;

				default :
					return false;
					break;
			}
		}

		// Returns the slides DOM elements
		function getSlides() {
			return SLIDER.find(CRELLY).find(SLIDES).find(SLIDE);
		}

		// Returns the slide DOM element
		function getSlide(slide_index) {
			return getSlides().eq(slide_index);
		}

		// Timeout with useful methods
		function Timer(callback, delay) {
			var id;
			var start;
			var remaining = delay;

			this.pause = function() {
				clearTimeout(id);
				remaining -= new Date() - start;
			};

			this.resume = function() {
				start = new Date();
				clearTimeout(id);
				id = window.setTimeout(function() {
					callback();
				}, remaining);
			};

			this.clear = function () {
				clearTimeout(id);
			};

			// For now, works only after this.pause(). No need to calculate in other moments
			this.getRemaining = function() {
				return remaining;
			};

			this.resume();
		}

		// Returns true if the user is using a mobile browser
		function isMobile() {
			return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
		}

		/*****************/
		/** SLIDER CORE **/
		/*****************/

		// Loops trough the slides
		function loopSlides() {
			executeSlide(current_slide).done(function() {
				if(! paused) {
					current_slide = getNextSlide();

					loopSlides();
				}
			});
		}

		// Resets the progress bar and draws the progress bar of the current slide
		function drawProgressBar() {
			var progress_bar = SLIDER.find(CRELLY).find('.cs-progress-bar');

			resetProgressBar();

			progress_bar.animate({
				'width' : '100%',
			}, getItemData(getSlide(current_slide), 'time'));
		}

		// Resets the progress bar animation and CSS
		function resetProgressBar() {
			var progress_bar = SLIDER.find(CRELLY).find('.cs-progress-bar');

			progress_bar.stop();
			progress_bar.css('width', 0);
		}

		// Sets the right HTML classes of the navigation links
		function setNavigationLink() {
			var nav = SLIDER.find(CRELLY).find('.cs-navigation');
			var links = nav.find('> .cs-slide-link');

			links.each(function() {
				var link = $(this);

				if(link.index() == current_slide) {
					link.addClass('cs-active');
				}
				else {
					link.removeClass('cs-active');
				}
			});
		}

		// Finishes the current slide (animations out of elements and slide) and then plays the new slide
		function changeSlide(slide_index) {
			if(slide_index == current_slide) {
				return;
			}

			if(can_pause || executed_slide) {
				stop(false);

				finishSlide(current_slide, false, true).done(function() {
					current_slide = slide_index;
					play();
				});
			}
		}

		// Executes a slide completely. If the auto loop is disabled won't animate out the slide and the elements with time == "all"
		function executeSlide(slide_index) {
			settings.beforeSlideStart();

			var def = new $.Deferred();

			executed_slide = false;

			// If something is still animating, reset
			for(var i = 0; i < elements_times_timers.length; i++) {
				elements_times_timers[i].clear();
			}
			for(var i = 0; i < elements_delays_timers.length; i++) {
				elements_delays_timers[i].clear();
			}
			current_slide_time_timer.clear();
			getSlide(slide_index).finish();
			slideOut(slide_index);
			getSlide(slide_index).finish();
			var elements = getSlide(slide_index).find(ELEMENTS);
			elements.each(function() {
				var element = $(this);
				element.finish();
				elementOut(element);
				element.finish();
			});


			setNavigationLink();

			runSlide(slide_index);

			if(settings.automaticSlide) {
				finishSlide(slide_index, true, true).done(function() {
					executed_slide = true;
					def.resolve();
				});
			}
			else {
				finishSlide(slide_index, true, false).done(function() {
					executed_slide = true;
					def.resolve();
				});
			}

			return def.promise();
		}

		// Executes the in animation of the slide and it's elements
		function runSlide(slide_index) {
			var slide = getSlide(slide_index);
			var elements = slide.find(ELEMENTS);

			var elements_in_completed = 0;
			var slide_in_completed = false;

			var def = new $.Deferred();

			can_pause = false;

			// Do slide in animation
			slideIn(slide_index).done(function() {
				drawProgressBar();

				can_pause = true;

				slide_in_completed = true;
				if(slide_in_completed && elements_in_completed == elements.length) {
					def.resolve();
				}
			});

			// Do elements in animation
			elements.each(function() {
				var element = $(this);
				var element_delay = getItemData(element, 'delay');

				elements_delays_timers.push(new Timer(function() {
					elementIn(element).done(function() {
						if(isVideo(element)) {
							playVideo(element);
						}

						elements_in_completed++;
						if(slide_in_completed && elements_in_completed == elements.length) {
							def.resolve();
						}
					});
				}, element_delay));
			});

			return def.promise();
		}

		// Does all times, elements out animations and slide out animation
		// execute_time, if true, will do the slide and the elements timers. If false, the timers will be = 0 so the plugin will execute the code of the callback function immediately.
		// animate_all_out, if false, will execute the elements with time != all out animations but not the slide and the elements with time == all out animations. If true, executes all the out animations
		function finishSlide(slide_index, execute_time, animate_all_out) {
			var slide = getSlide(slide_index);
			var elements = slide.find(ELEMENTS);
			var data_time = execute_time ? getItemData(slide, 'time') + getItemData(slide, 'ease-in') : 0;

			var elements_out_completed = 0;
			var slide_time_completed = false;

			var def = new $.Deferred();

			// Elements with time != "all"
			elements.each(function() {
				var element = $(this);
				var time = getItemData(element, 'time');

				if(time != 'all') {
					var final_element_time = execute_time ? time : 0;

					if(getItemData(element, 'ignore-ease-out')) {
						elements_out_completed++;

						if(elements.length == elements_out_completed && slide_time_completed && animate_all_out) {
							pauseVideos(slide_index);
							slideOut(slide_index);
							def.resolve();
						}
					}

					elements_times_timers.push(new Timer(function() {
						elementOut(element).done(function() {
							if(! getItemData(element, 'ignore-ease-out')) {
								elements_out_completed++;

								if(elements.length == elements_out_completed && slide_time_completed && animate_all_out) {
									pauseVideos(slide_index);
									slideOut(slide_index);
									def.resolve();
								}
							}
						});
					}, final_element_time));
				}
			});

			// Execute slide time
			current_slide_time_timer = new Timer(function() {
				can_pause = false;

				resetProgressBar();

				slide_time_completed = true;

				if(elements.length == elements_out_completed && slide_time_completed && animate_all_out) {
					pauseVideos(slide_index);
					slideOut(slide_index);
					def.resolve();
				}

				if(! animate_all_out) {
					def.resolve();
				}
				else {
					// Elements with time == "all"
					elements.each(function() {
						var element = $(this);
						var time = getItemData(element, 'time');

						if(time == 'all') {
							if(getItemData(element, 'ignore-ease-out')) {
								elements_out_completed++;

								if(elements.length == elements_out_completed && slide_time_completed && animate_all_out) {
									pauseVideos(slide_index);
									slideOut(slide_index);
									def.resolve();
								}
							}

							elementOut(element).done(function() {
								if(! getItemData(element, 'ignore-ease-out')) {
									elements_out_completed++;

									if(elements.length == elements_out_completed && slide_time_completed && animate_all_out) {
										pauseVideos(slide_index);
										slideOut(slide_index);
										def.resolve();
									}
								}
							});
						}
					});
				}
			}, data_time);

			return def.promise();
		}

		// VIDEOS FUNCTIONS

		// Returns true if the element is a YouTube or a Vimeo iframe
		function isVideo(element) {
			return isYoutubeVideo(element) || isVimeoVideo(element);
		}

		// Checks what's the source of the video, then plays it
		function playVideo(element) {
			if(isYoutubeVideo(element)) {
				playYoutubeVideo(element);
			}
			else {
				playVimeoVideo(element);
			}
		}

		// Pauses all the YouTube and Vimeo videos
		function pauseVideos(slide_index) {
			pauseYoutubeVideos(slide_index);
			pauseVimeoVideos(slide_index);
		}

		// Checks what's the source of the video, then pauses it
		function pauseVideo(element) {
			if(isYoutubeVideo(element)) {
				pauseYoutubeVideo(element);
			}
			else {
				pauseVimeoVideo(element);
			}
		}

		// Checks if the element is a YouTube video
		function isYoutubeVideo(element) {
			return element.hasClass('cs-yt-iframe');
		}

		// Returns the player associated to the element
		function getYoutubePlayer(element) {
			return youtube_videos[element.attr('id')].player;
		}

		/*
		Returns:
		-1 – unstarted
		0 – ended
		1 – playing
		2 – paused
		3 – buffering
		5 – video cued
		*/
		function getYoutubePlayerState(element) {
			return getYoutubePlayer(element).getPlayerState();
		}

		// Checks if the video can be played and plays it
		function playYoutubeVideo(element) {
			// If autplay and first slide loop. Disabled on mobile for compatibility reasons (details on the Youtube's website)
			if(getItemData(element, 'autoplay') && ! youtube_videos[element.attr('id')].played_once && ! isMobile()) {
				getYoutubePlayer(element).playVideo();
			}

			// If was paused, but not manually
			if(getYoutubePlayerState(element) == 2 && !youtube_videos[element.attr('id')].manually_paused) {
				getYoutubePlayer(element).playVideo();
			}

			youtube_videos[element.attr('id')].played_once = true;
		}

		// Pause all the videos in a slide
		function pauseYoutubeVideos(slide_index) {
			getSlide(slide_index).each(function() {
				var slide = $(this);

				slide.find(ELEMENTS + '.cs-yt-iframe').each(function() {
					pauseYoutubeVideo($(this));
				});
			});
		}

		// Checks if the video can be paused and pauses it
		function pauseYoutubeVideo(element) {
			if(getYoutubePlayerState(element) == 1) {
				getYoutubePlayer(element).pauseVideo();
			}
		}

		// Checks if the element is a Vimeo video
		function isVimeoVideo(element) {
			return element.hasClass('cs-vimeo-iframe');
		}

		// Returns the player associated to the element
		function getVimeoPlayer(element) {
			return vimeo_videos[element.attr('id')].player;
		}

		// Plays the video
		function playVimeoVideo(element) {
			// If autplay and first slide loop. Disabled on mobile for compatibility reasons (details on the Vimeo's website)
			if(getItemData(element, 'autoplay') && ! vimeo_videos[element.attr('id')].played_once && ! isMobile()) {
				getVimeoPlayer(element).api('play');
			}

			// If was paused
			if(getVimeoPlayer(element).api('paused') && ! vimeo_videos[element.attr('id')].ended && vimeo_videos[element.attr('id')].played_once && !vimeo_videos[element.attr('id')].manually_paused) {
				getVimeoPlayer(element).api('play');
			}
		}

		// Pause all the videos in a slide
		function pauseVimeoVideos(slide_index) {
			getSlide(slide_index).each(function() {
				var slide = $(this);

				slide.find(ELEMENTS + '.cs-vimeo-iframe').each(function() {
					pauseVimeoVideo($(this));
				});
			});
		}

		// Pauses the video
		function pauseVimeoVideo(element) {
			getVimeoPlayer(element).api('pause');
		}

		/****************/
		/** ANIMATIONS **/
		/****************/

		// WARNING: slideIn and elementIn must reset every CSS propriety to the correct value before starting

		// Does slide in animation
		function slideIn(slide_index) {
			var slide = getSlide(slide_index);
			var data_in = getItemData(slide, 'in');
			var data_ease_in = getItemData(slide, 'ease-in');

			var def = new $.Deferred();

			if(slide.css('display') == 'block') {
				return def.resolve().promise();
			}

			// If first play, don't execute the animation
			if(first_play) {
				slide.css({
					'display' : 'block',
					'top'	  : 0,
					'left'	  : 0,
					'opacity' : getItemData(slide, 'opacity'),
				});
				return def.resolve().promise();
			}

			switch(data_in) {
				case 'fade' :
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : 0,
						'opacity' : 0,
					});
					slide.animate({
						'opacity' : getItemData(slide, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeLeft' :
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : getWidth(),
						'opacity' : 0,
					});
					slide.animate({
						'opacity' : getItemData(slide, 'opacity'),
						'left'	  : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeRight' :
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : -getWidth(),
						'opacity' : 0,
					});
					slide.animate({
						'opacity' : getItemData(slide, 'opacity'),
						'left'	  : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideLeft' :
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : getWidth(),
						'opacity' : getItemData(slide, 'opacity'),
					});
					slide.animate({
						'left' : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideRight' :
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : -getWidth(),
						'opacity' : getItemData(slide, 'opacity'),
					});
					slide.animate({
						'left' : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideUp' :
					slide.css({
						'display' : 'block',
						'top'	  : getHeight(),
						'left'	  : 0,
						'opacity' : getItemData(slide, 'opacity'),
					});
					slide.animate({
						'top' : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideDown' :
					slide.css({
						'display' : 'block',
						'top'	  : -getHeight(),
						'left'	  : 0,
						'opacity' : getItemData(slide, 'opacity'),
					});
					slide.animate({
						'top' : 0,
					}, data_ease_in, function() { def.resolve(); });
					break;

				default:
					slide.css({
						'display' : 'block',
						'top'	  : 0,
						'left'	  : 0,
						'opacity' : getItemData(slide, 'opacity'),
					});
					def.resolve();
					break;
			}

			return def.promise();
		}

		// Does slide out animation
		function slideOut(slide_index) {
			var slide = getSlide(slide_index);
			var data_out = getItemData(slide, 'out');
			var data_ease_out = getItemData(slide, 'ease-out');

			var def = new $.Deferred();

			if(slide.css('display') == 'none') {
				return def.resolve().promise();
			}

			switch(data_out) {
				case 'fade' :
					slide.animate({
						'opacity' : 0,
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'opacity' : getItemData(slide, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeLeft' :
					slide.animate({
						'opacity' : 0,
						'left'	  : -getWidth(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'opacity' : getItemData(slide, 'opacity'),
							'left' 	  : 0,
						});
						def.resolve();
					});
					break;

				case 'fadeRight' :
					slide.animate({
						'opacity' : 0,
						'left'	  : getWidth(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'opacity' : getItemData(slide, 'opacity'),
							'left' 	  : 0,
						});
						def.resolve();
					});
					break;

				case 'slideLeft' :
					slide.animate({
						'left' : -getWidth(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'left' : 0,
						});
						def.resolve();
					});
					break;

				case 'slideRight' :
					slide.animate({
						'left' : getWidth(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'left' : 0,
						});
						def.resolve();
					});
					break;

				case 'slideUp' :
					slide.animate({
						'top' : -getHeight(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'top' : 0,
						});
						def.resolve();
					});
					break;

				case 'slideDown' :
					slide.animate({
						'top' : getHeight(),
					}, data_ease_out,
					function() {
						slide.css({
							'display' : 'none',
							'top' : 0,
						});
						def.resolve();
					});
					break;

				default :
					slide.css({
						'display' : 'none',
					});
					def.resolve();
					break;
			}

			return def.promise();
		}

		// Does element in animation
		function elementIn(element) {
			var element_width = element.outerWidth();
			var element_height = element.outerHeight();
			var data_in = getItemData(element, 'in');
			var data_ease_in = getItemData(element, 'ease-in');
			var data_top = getItemData(element, 'top');
			var data_left = getItemData(element, 'left');

			var def = new $.Deferred();

			if(element.css('display') == 'block') {
				return def.resolve().promise();
			}

			switch(data_in) {
				case 'slideDown' :
					element.css({
						'display' : 'block',
						'top'	  : -element_height,
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideUp' :
					element.css({
						'display' : 'block',
						'top'  	  : getHeight(),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideLeft' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getWidth(),
						'opacity' : getItemData(element, 'opacity'),
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'slideRight' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : -element_width,
						'opacity' : getItemData(element, 'opacity'),
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fade' :
					element.css({
						'display' : 'block',
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : 0,
					}).animate({
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeDown' :
					element.css({
						'display' : 'block',
						'top'	  : -element_height,
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : 0,
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeUp' :
					element.css({
						'display' : 'block',
						'top'  	  : getHeight(),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : 0,
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeLeft' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getWidth(),
						'opacity' : 0,
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeRight' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : -element_width,
						'opacity' : 0,
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeSmallDown' :
					element.css({
						'display' : 'block',
						'top'	  : getScaled(data_top + getLayoutGaps(element).top -30),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : 0,
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeSmallUp' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top + 30),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : 0,
					}).animate({
						'top'	  : getScaled(data_top + getLayoutGaps(element).top),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeSmallLeft' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left + 30),
						'opacity' : 0,
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				case 'fadeSmallRight' :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left - 30),
						'opacity' : 0,
					}).animate({
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					}, data_ease_in, function() { def.resolve(); });
					break;

				default :
					element.css({
						'display' : 'block',
						'top'  	  : getScaled(data_top + getLayoutGaps(element).top),
						'left'	  : getScaled(data_left + getLayoutGaps(element).left),
						'opacity' : getItemData(element, 'opacity'),
					});
					def.resolve();
					break;
			}

			return def.promise();
		}

		// Does element out animation
		function elementOut(element) {
			var element_width = element.outerWidth();
			var element_height = element.outerHeight();
			var data_out = getItemData(element, 'out');
			var data_ease_out = getItemData(element, 'ease-out');

			var def = new $.Deferred();

			if(element.css('display') == 'none') {
				return def.resolve().promise();
			}

			switch(data_out) {
				case 'slideDown' :
					element.animate({
						'top' : getHeight(),
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
						});
						def.resolve();
					});
					break;

				case 'slideUp' :
					element.animate({
						'top' : - element_height,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
						});
						def.resolve();
					});
					break;

				case 'slideLeft' :
					element.animate({
						'left' : - element_width,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
						});
						def.resolve();
					});
					break;

				case 'slideRight' :
					element.animate({
						'left' : getWidth(),
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
						});
						def.resolve();
					});
					break;

				case 'fade' :
					element.animate({
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeDown' :
					element.animate({
						'top' : getHeight(),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeUp' :
					element.animate({
						'top' : - element_height,
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeLeft' :
					element.animate({
						'left' : - element_width,
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeRight' :
					element.animate({
						'left' : getWidth(),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeSmallDown' :
					element.animate({
						'top' : getScaled(getItemData(element, 'top') + getLayoutGaps(element).top + 30),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeSmallUp' :
					element.animate({
						'top' : getScaled(getItemData(element, 'top') + getLayoutGaps(element).top - 30),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeSmallLeft' :
					element.animate({
						'left' : getScaled(getItemData(element, 'left') + getLayoutGaps(element).left - 30),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				case 'fadeSmallRight' :
					element.animate({
						'left' : getScaled(getItemData(element, 'left') + getLayoutGaps(element).left + 30),
						'opacity' : 0,
					}, data_ease_out,
					function() {
						element.css({
							'display' : 'none',
							'opacity' : getItemData(element, 'opacity'),
						});
						def.resolve();
					});
					break;

				default :
					element.css({
						'display' : 'none',
					});
					def.resolve();
					break;
			}

			return def.promise();
		}

		/**********************/
		/** PUBLIC FUNCTIONS **/
		/**********************/

		this.resume = function() {
			resume();
		}

		this.pause = function() {
			pause();
		}

		this.nextSlide = function() {
			changeSlide(getNextSlide());
		}

		this.previousSlide = function() {
			changeSlide(getPreviousSlide());
		}

		this.changeSlide = function(slide_index) {
			changeSlide(slide_index);
		}

		this.getCurrentSlide = function() {
			return current_slide;
		}

		this.getTotalSlides = function() {
			return total_slides;
		}

	};

	/**************************/
	/** CRELLY SLIDER PLUGIN **/
	/**************************/

	$.fn.crellySlider = function(options) {
      var settings = $.extend({
				layout									: 'fixed',
				responsive							: true,
				startWidth							: 1140,
				startHeight							: 500,

				pauseOnHover						: true,
				automaticSlide					: true,
				randomOrder							: true,
				startFromSlide					: 0, // -1 means random, >= 0 means the exact index
				showControls 						: true,
				showNavigation					: true,
				showProgressBar					: true,
				enableSwipe							: true,

				slidesTime							: 3000,
				elementsDelay						: 0,
				elementsTime						: 'all',
				slidesEaseIn						: 300,
				elementsEaseIn					: 300,
				slidesEaseOut						: 300,
				elementsEaseOut					: 300,
				ignoreElementsEaseOut 	: false,

				videoAutoplay						: false,
				videoLoop								: false,

				beforeStart							: function() {},
				beforeSetResponsive			: function() {},
				beforeSlideStart				: function() {},
				beforePause							: function() {},
				beforeResume						: function() {},
      }, options);

      return this.each(function() {
				if(undefined == $(this).data('crellySlider')) {
					var plugin = new $.CrellySlider(this, settings);
					$(this).data('crellySlider', plugin);
				}
      });
    };

})(jQuery);
