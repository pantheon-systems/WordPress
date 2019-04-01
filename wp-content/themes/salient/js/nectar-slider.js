/*
 * Swiper 2.2 - Mobile Touch Slider
 * http://www.idangero.us/sliders/swiper/
 *
 * Copyright 2012-2013, Vladimir Kharlampidi
 * The iDangero.us
 * http://www.idangero.us/
 *
 * Licensed under GPL & MIT
 *
 * Updated on: September 15, 2013
*/

if(!jQuery().swiper) {

var Swiper = function (selector, params) {
    /*=========================
      A little bit dirty but required part for IE8 and old FF support
      ===========================*/
    if (document.body.__defineGetter__) {
        if (HTMLElement) {
            var element = HTMLElement.prototype;
            if (element.__defineGetter__) {
                element.__defineGetter__("outerHTML", function () { return new XMLSerializer().serializeToString(this); } );
            }
        }
    }

    if (!window.getComputedStyle) {
        window.getComputedStyle = function (el, pseudo) {
            this.el = el;
            this.getPropertyValue = function (prop) {
                var re = /(\-([a-z]){1})/g;
                if (prop === 'float') prop = 'styleFloat';
                if (re.test(prop)) {
                    prop = prop.replace(re, function () {
                        return arguments[2].toUpperCase();
                    });
                }
                return el.currentStyle[prop] ? el.currentStyle[prop] : null;
            }
            return this;
        }
    }
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(obj, start) {
            for (var i = (start || 0), j = this.length; i < j; i++) {
                if (this[i] === obj) { return i; }
            }
            return -1;
        }
    }
    if (!document.querySelectorAll) {
        if (!window.jQuery) return;
    }
    function $$(selector, context) {
        if (document.querySelectorAll)
            return (context || document).querySelectorAll(selector);
        else
            return jQuery(selector, context);
    }

    /*=========================
      Check for correct selector
      ===========================*/
    if(typeof selector === 'undefined') return;

    if(!(selector.nodeType)){
        if ($$(selector).length === 0) return;
    }

     /*=========================
      _this
      ===========================*/
    var _this = this;

     /*=========================
      Default Flags and vars
      ===========================*/
    _this.touches = {
        start:0,
        startX:0,
        startY:0,
        current:0,
        currentX:0,
        currentY:0,
        diff:0,
        abs:0
    };
    _this.positions = {
        start:0,
        abs:0,
        diff:0,
        current:0
    };
    _this.times = {
        start:0,
        end:0
    };

    _this.id = (new Date()).getTime();
    _this.container = (selector.nodeType) ? selector : $$(selector)[0];
    _this.isTouched = false;
    _this.isMoved = false;
    _this.activeIndex = 0;
    _this.activeLoaderIndex = 0;
    _this.activeLoopIndex = 0;
    _this.previousIndex = null;
    _this.velocity = 0;
    _this.snapGrid = [];
    _this.slidesGrid = [];
    _this.imagesToLoad = [];
    _this.imagesLoaded = 0;
    _this.wrapperLeft=0;
    _this.wrapperRight=0;
    _this.wrapperTop=0;
    _this.wrapperBottom=0;
    var wrapper, slideSize, wrapperSize, direction, isScrolling, containerSize;

    /*=========================
      Default Parameters
      ===========================*/
    var defaults = {
        mode : 'horizontal', // or 'vertical'
        touchRatio : 1,
        speed : 300,
        freeMode : false,
        freeModeFluid : false,
        momentumRatio: 1,
        momentumBounce: true,
        momentumBounceRatio: 1,
        slidesPerView : 1,
        slidesPerGroup : 1,
        simulateTouch : true,
        followFinger : true,
        shortSwipes : true,
        moveStartThreshold:false,
        autoplay: false,
        onlyExternal : false,
        createPagination : true,
        pagination : false,
        paginationElement: 'span',
        paginationClickable: false,
        paginationAsRange: true,
        resistance : true, // or false or 100%
        scrollContainer : false,
        preventLinks : true,
        noSwiping : false, // or class
        noSwipingClass : 'swiper-no-swiping', //:)
        initialSlide: 0,
        keyboardControl: false,
        mousewheelControl : false,
        mousewheelDebounce: 600,
        useCSS3Transforms : true,
        //Loop mode
        loop:false,
        loopAdditionalSlides:0,
        //Auto Height
        calculateHeight: false,
        //Images Preloader
        updateOnImagesReady : true,
        //Form elements
        releaseFormElements : true,
        //Watch for active slide, useful when use effects on different slide states
        watchActiveIndex: false,
        //Slides Visibility Fit
        visibilityFullFit : false,
        //Slides Offset
        offsetPxBefore : 0,
        offsetPxAfter : 0,
        offsetSlidesBefore : 0,
        offsetSlidesAfter : 0,
        centeredSlides: false,
        //Queue callbacks
        queueStartCallbacks : false,
        queueEndCallbacks : false,
        //Auto Resize
        autoResize : true,
        resizeReInit : false,
        //DOMAnimation
        DOMAnimation : true,
        //Slides Loader
        loader: {
            slides:[], //array with slides
            slidesHTMLType:'inner', // or 'outer'
            surroundGroups: 1, //keep preloaded slides groups around view
            logic: 'reload', //or 'change'
            loadAllSlides: false
        },
        //Namespace
        slideElement : 'div',
        slideClass : 'swiper-slide',
        slideActiveClass : 'swiper-slide-active',
        slideVisibleClass : 'swiper-slide-visible',
        wrapperClass : 'swiper-wrapper',
        paginationElementClass: 'swiper-pagination-switch',
        /*nectar addition*/
        paginationActiveClass : 'swiper-active-switch ar-vis',
        /*nectar addition end*/
        paginationVisibleClass : 'swiper-visible-switch'
    }
    params = params || {};
    for (var prop in defaults) {
        if (prop in params && typeof params[prop]==='object') {
            for (var subProp in defaults[prop]) {
                if (! (subProp in params[prop])) {
                    params[prop][subProp] = defaults[prop][subProp];
                }
            }
        }
        else if (! (prop in params)) {
            params[prop] = defaults[prop]
        }
    }
    _this.params = params;
    if (params.scrollContainer) {
        params.freeMode = true;
        params.freeModeFluid = true;
    }
    if (params.loop) {
        params.resistance = '100%';
    }
    var isH = params.mode==='horizontal';

    /*=========================
      Define Touch Events
      ===========================*/

    _this.touchEvents = {
        touchStart : _this.support.touch || !params.simulateTouch  ? 'touchstart' : (_this.browser.ie10 ? 'MSPointerDown' : 'mousedown'),
        touchMove : _this.support.touch || !params.simulateTouch ? 'touchmove' : (_this.browser.ie10 ? 'MSPointerMove' : 'mousemove'),
        touchEnd : _this.support.touch || !params.simulateTouch ? 'touchend' : (_this.browser.ie10 ? 'MSPointerUp' : 'mouseup')
    };

    /*=========================
      Wrapper
      ===========================*/
    for (var i = _this.container.childNodes.length - 1; i >= 0; i--) {
        if (_this.container.childNodes[i].className) {
            var _wrapperClasses = _this.container.childNodes[i].className.split(/\s+/);
            for (var j = 0; j < _wrapperClasses.length; j++) {
                if (_wrapperClasses[j] === params.wrapperClass) {
                    wrapper = _this.container.childNodes[i];
                }
            }
        }
    }

    _this.wrapper = wrapper;
    /*=========================
      Slide API
      ===========================*/
    _this._extendSwiperSlide = function  (el) {
        el.append = function () {
            if (params.loop) {
                el.insertAfter(_this.slides.length-_this.loopedSlides);
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else {
                _this.wrapper.appendChild(el);
            }

            _this.reInit();
            return el;
        }
        el.prepend = function () {
            if (params.loop) {
                _this.wrapper.insertBefore(el, _this.slides[_this.loopedSlides]);
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else {
                _this.wrapper.insertBefore(el, _this.wrapper.firstChild);
            }
            _this.reInit();
            return el;
        }
        el.insertAfter = function (index) {
            if(typeof index === 'undefined') return false;
            var beforeSlide;

            if (params.loop) {
                beforeSlide = _this.slides[index + 1 + _this.loopedSlides];
                _this.wrapper.insertBefore(el, beforeSlide);
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else {
                beforeSlide = _this.slides[index + 1];
                _this.wrapper.insertBefore(el, beforeSlide)
            }
            _this.reInit();
            return el;
        }
        el.clone = function () {
            return _this._extendSwiperSlide(el.cloneNode(true))
        }
        el.remove = function () {
            _this.wrapper.removeChild(el);
            _this.reInit();
        }
        el.html = function (html) {
            if (typeof html === 'undefined') {
                return el.innerHTML;
            }
            else {
                el.innerHTML = html;
                return el;
            }
        }
        el.index = function () {
            var index;
            for (var i = _this.slides.length - 1; i >= 0; i--) {
                if(el === _this.slides[i]) index = i;
            }
            return index;
        }
        el.isActive = function () {
            if (el.index() === _this.activeIndex) return true;
            else return false;
        }
        if (!el.swiperSlideDataStorage) el.swiperSlideDataStorage={};
        el.getData = function (name) {
            return el.swiperSlideDataStorage[name];
        }
        el.setData = function (name, value) {
            el.swiperSlideDataStorage[name] = value;
            return el;
        }
        el.data = function (name, value) {
            if (!value) {
                return el.getAttribute('data-'+name);
            }
            else {
                el.setAttribute('data-'+name,value);
                return el;
            }
        }
        el.getWidth = function (outer) {
            return _this.h.getWidth(el, outer);
        }
        el.getHeight = function (outer) {
            return _this.h.getHeight(el, outer);
        }
        el.getOffset = function() {
            return _this.h.getOffset(el);
        }
        return el;
    }

    //Calculate information about number of slides
    _this.calcSlides = function (forceCalcSlides) {
        var oldNumber = _this.slides ? _this.slides.length : false;
        _this.slides = [];
        _this.displaySlides = [];
        for (var i = 0; i < _this.wrapper.childNodes.length; i++) {
            if (_this.wrapper.childNodes[i].className) {
                var _className = _this.wrapper.childNodes[i].className;
                var _slideClasses = _className.split(' ');
                for (var j = 0; j < _slideClasses.length; j++) {
                    if(_slideClasses[j]===params.slideClass) {
                        _this.slides.push(_this.wrapper.childNodes[i]);
                    }
                }
            }
        }
        for (i = _this.slides.length - 1; i >= 0; i--) {
            _this._extendSwiperSlide(_this.slides[i]);
        }
        if (!oldNumber) return;
        if(oldNumber!==_this.slides.length || forceCalcSlides) {
            // Number of slides has been changed
            removeSlideEvents();
            addSlideEvents();
            _this.updateActiveSlide();
            if (params.createPagination && _this.params.pagination) _this.createPagination();
            _this.callPlugins('numberOfSlidesChanged');
        }
    }

    //Create Slide
    _this.createSlide = function (html, slideClassList, el) {
        var slideClassList = slideClassList || _this.params.slideClass;
        var el = el||params.slideElement;
        var newSlide = document.createElement(el);
        newSlide.innerHTML = html||'';
        newSlide.className = slideClassList;
        return _this._extendSwiperSlide(newSlide);
    }

    //Append Slide
    _this.appendSlide = function (html, slideClassList, el) {
        if (!html) return;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).append()
        }
        else {
            return _this.createSlide(html, slideClassList, el).append()
        }
    }
    _this.prependSlide = function (html, slideClassList, el) {
        if (!html) return;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).prepend()
        }
        else {
            return _this.createSlide(html, slideClassList, el).prepend()
        }
    }
    _this.insertSlideAfter = function (index, html, slideClassList, el) {
        if (typeof index === 'undefined') return false;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).insertAfter(index);
        }
        else {
            return _this.createSlide(html, slideClassList, el).insertAfter(index);
        }
    }
    _this.removeSlide = function (index) {
        if (_this.slides[index]) {
            if (params.loop) {
                if (!_this.slides[index+_this.loopedSlides]) return false;
                _this.slides[index+_this.loopedSlides].remove();
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else _this.slides[index].remove();
            return true;
        }
        else return false;
    }
    _this.removeLastSlide = function () {
        if (_this.slides.length>0) {
            if (params.loop) {
                _this.slides[_this.slides.length - 1 - _this.loopedSlides].remove();
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else _this.slides[ (_this.slides.length-1) ].remove();
            return true;
        }
        else {
            return false;
        }
    }
    _this.removeAllSlides = function () {
        for (var i = _this.slides.length - 1; i >= 0; i--) {
            _this.slides[i].remove()
        }
    }
    _this.getSlide = function (index) {
        return _this.slides[index]
    }
    _this.getLastSlide = function () {
        return _this.slides[ _this.slides.length-1 ]
    }
    _this.getFirstSlide = function () {
        return _this.slides[0]
    }

    //Currently Active Slide
    _this.activeSlide = function () {
        return _this.slides[_this.activeIndex]
    }

    /*=========================
      Plugins API
      ===========================*/
    var _plugins = [];
    for (var plugin in _this.plugins) {
        if (params[plugin]) {
            var p = _this.plugins[plugin](_this, params[plugin]);
            if (p) _plugins.push( p );
        }
    }
    _this.callPlugins = function(method, args) {
        if (!args) args = {}
        for (var i=0; i<_plugins.length; i++) {
            if (method in _plugins[i]) {
                _plugins[i][method](args);
            }
        }
    }


         /*=========================

     Wrapper for Callbacks : Allows additive callbacks via function arrays

     ===========================*/

    _this.fireCallback = function() {

        var callback = arguments[0];

        if( Object.prototype.toString.call( callback ) === '[object Array]' ) {

            for (var i = 0; i < callback.length; i++) {

                if (typeof callback[i] === 'function') {

                    callback[i](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])

                }

            }

        } else {

            callback(arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);

        }

    }

    function isArray (obj) {

        "use strict";

        if (Object.prototype.toString.apply( obj ) === '[object Array]') return true;

        return false;

    }



    /**

+     * Allows user to add callbacks, rather than replace them

+     * @param callback

+     * @param func

+     * @return {*}

+     */

    _this.addCallback = function (callback, func) {

        "use strict";

        var _this = this, tempFunc;

        if (_this.params['on' + callback]) {

           if (isArray(this.params['on' + callback])) {

                return this.params['on' + callback].push(func);

            } else if (typeof this.params['on' + callback] === 'function') {

                tempFunc = this.params['on' + callback];

                this.params['on' + callback] = [];

                this.params['on' + callback].push(tempFunc);

               return this.params['on' + callback].push(func);

            }

        } else {

            this.params['on' + callback] = [];

           return this.params['on' + callback].push(func);

        }

    }

    _this.removeCallbacks = function (callback) {

        if (_this.params['on' + callback]) {

            return _this.params['on' + callback] = null;

        }

    }



    /*=========================


    /*=========================
      WP8 Fix
      ===========================*/
    if (_this.browser.ie10 && !params.onlyExternal) {
        _this.wrapper.classList.add('swiper-wp8-' + (isH ? 'horizontal' : 'vertical'));
    }

    /*=========================
      Free Mode Class
      ===========================*/
    if (params.freeMode) {
        _this.container.className+=' swiper-free-mode';
    }

    /*==================================================
        Init/Re-init/Resize Fix
    ====================================================*/
    _this.initialized = false;
    _this.init = function(force, forceCalcSlides) {
        var _width = _this.h.getWidth(_this.container);
        var _height = _this.h.getHeight(_this.container);
        if (_width===_this.width && _height===_this.height && !force) return;
        _this.width = _width;
        _this.height = _height;

        containerSize = isH ? _width : _height;
        var wrapper = _this.wrapper;

        if (force) {
            _this.calcSlides(forceCalcSlides);
        }

        if (params.slidesPerView==='auto') {
            //Auto mode
            var slidesWidth = 0;
            var slidesHeight = 0;

            //Unset Styles
            if (params.slidesOffset>0) {
                wrapper.style.paddingLeft = '';
                wrapper.style.paddingRight = '';
                wrapper.style.paddingTop = '';
                wrapper.style.paddingBottom = '';
            }
            wrapper.style.width = '';
            wrapper.style.height = '';
            if (params.offsetPxBefore>0) {
                if (isH) _this.wrapperLeft = params.offsetPxBefore;
                else _this.wrapperTop = params.offsetPxBefore;
            }
            if (params.offsetPxAfter>0) {
                if (isH) _this.wrapperRight = params.offsetPxAfter;
                else _this.wrapperBottom = params.offsetPxAfter;
            }

            if (params.centeredSlides) {
                if (isH) {
                    _this.wrapperLeft = (containerSize - this.slides[0].getWidth(true) )/2;
                    _this.wrapperRight = (containerSize - _this.slides[ _this.slides.length-1 ].getWidth(true))/2;
                }
                else {
                    _this.wrapperTop = (containerSize - _this.slides[0].getHeight(true))/2;
                    _this.wrapperBottom = (containerSize - _this.slides[ _this.slides.length-1 ].getHeight(true))/2;
                }
            }

            if (isH) {
                if (_this.wrapperLeft>=0) wrapper.style.paddingLeft = _this.wrapperLeft+'px';
                if (_this.wrapperRight>=0) wrapper.style.paddingRight = _this.wrapperRight+'px';
            }
            else {
                if (_this.wrapperTop>=0) wrapper.style.paddingTop = _this.wrapperTop+'px';
                if (_this.wrapperBottom>=0) wrapper.style.paddingBottom = _this.wrapperBottom+'px';
            }
            var slideLeft = 0;
            var centeredSlideLeft=0;
            _this.snapGrid = [];
            _this.slidesGrid = [];

            var slideMaxHeight = 0;
            for(var i = 0; i<_this.slides.length; i++) {
                var slideWidth = _this.slides[i].getWidth(true);
                var slideHeight = _this.slides[i].getHeight(true);
                if (params.calculateHeight) {
                    slideMaxHeight = Math.max(slideMaxHeight, slideHeight)
                }
                var _slideSize = isH ? slideWidth : slideHeight;
                if (params.centeredSlides) {
                    var nextSlideWidth = i === _this.slides.length-1 ? 0 : _this.slides[i+1].getWidth(true);
                    var nextSlideHeight = i === _this.slides.length-1 ? 0 : _this.slides[i+1].getHeight(true);
                    var nextSlideSize = isH ? nextSlideWidth : nextSlideHeight;
                    if (_slideSize>containerSize) {
                        for (var j=0; j<=Math.floor(_slideSize/(containerSize+_this.wrapperLeft)); j++) {
                            if (j === 0) _this.snapGrid.push(slideLeft+_this.wrapperLeft);
                            else _this.snapGrid.push(slideLeft+_this.wrapperLeft+containerSize*j);
                        }
                        _this.slidesGrid.push(slideLeft+_this.wrapperLeft);
                    }
                    else {
                        _this.snapGrid.push(centeredSlideLeft);
                        _this.slidesGrid.push(centeredSlideLeft);
                    }

                    centeredSlideLeft += _slideSize/2 + nextSlideSize/2;

                }
                else {
                    if (_slideSize>containerSize) {
                        for (var j=0; j<=Math.floor(_slideSize/containerSize); j++) {
                            _this.snapGrid.push(slideLeft+containerSize*j);
                        }
                    }
                    else {
                        _this.snapGrid.push(slideLeft);
                    }
                    _this.slidesGrid.push(slideLeft);
                }

                slideLeft += _slideSize;

                slidesWidth += slideWidth;
                slidesHeight += slideHeight;
            }
            if (params.calculateHeight) _this.height = slideMaxHeight;
            if(isH) {
                wrapperSize = slidesWidth + _this.wrapperRight + _this.wrapperLeft;
                wrapper.style.width = (slidesWidth)+'px';
                wrapper.style.height = (_this.height)+'px';
            }
            else {
                wrapperSize = slidesHeight + _this.wrapperTop + _this.wrapperBottom;
                wrapper.style.width = (_this.width)+'px';
                wrapper.style.height = (slidesHeight)+'px';
            }

        }
        else if (params.scrollContainer) {
            //Scroll Container
            wrapper.style.width = '';
            wrapper.style.height = '';
            var wrapperWidth = _this.slides[0].getWidth(true);
            var wrapperHeight = _this.slides[0].getHeight(true);
            wrapperSize = isH ? wrapperWidth : wrapperHeight;
            wrapper.style.width = wrapperWidth+'px';
            wrapper.style.height = wrapperHeight+'px';
            slideSize = isH ? wrapperWidth : wrapperHeight;

        }
        else {
            //For usual slides
            if (params.calculateHeight) {
                var slideMaxHeight = 0;
                var wrapperHeight = 0;
                //ResetWrapperSize
                if (!isH) _this.container.style.height= '';
                wrapper.style.height='';

                for (var i=0; i<_this.slides.length; i++) {
                    //ResetSlideSize
                    _this.slides[i].style.height='';
                    slideMaxHeight = Math.max( _this.slides[i].getHeight(true), slideMaxHeight );
                    if (!isH) wrapperHeight+=_this.slides[i].getHeight(true);
                }
                var slideHeight = slideMaxHeight;
                _this.height = slideHeight;

                if (isH) wrapperHeight = slideHeight;
                else containerSize = slideHeight, _this.container.style.height= containerSize+'px';
            }
            else {
                var slideHeight = isH ? _this.height : _this.height/params.slidesPerView;
                var wrapperHeight = isH ? _this.height : _this.slides.length*slideHeight;
            }
            var slideWidth = isH ? _this.width/params.slidesPerView : _this.width;
            var wrapperWidth = isH ? _this.slides.length*slideWidth : _this.width;
            slideSize = isH ? slideWidth : slideHeight;

            if (params.offsetSlidesBefore>0) {
                if (isH) _this.wrapperLeft = slideSize*params.offsetSlidesBefore;
                else _this.wrapperTop = slideSize*params.offsetSlidesBefore;
            }
            if (params.offsetSlidesAfter>0) {
                if (isH) _this.wrapperRight = slideSize*params.offsetSlidesAfter;
                else _this.wrapperBottom = slideSize*params.offsetSlidesAfter;
            }
            if (params.offsetPxBefore>0) {
                if (isH) _this.wrapperLeft = params.offsetPxBefore;
                else _this.wrapperTop = params.offsetPxBefore;
            }
            if (params.offsetPxAfter>0) {
                if (isH) _this.wrapperRight = params.offsetPxAfter;
                else _this.wrapperBottom = params.offsetPxAfter;
            }
            if (params.centeredSlides) {
                if (isH) {
                    _this.wrapperLeft = (containerSize - slideSize)/2;
                    _this.wrapperRight = (containerSize - slideSize)/2;
                }
                else {
                    _this.wrapperTop = (containerSize - slideSize)/2;
                    _this.wrapperBottom = (containerSize - slideSize)/2;
                }
            }
            if (isH) {
                if (_this.wrapperLeft>0) wrapper.style.paddingLeft = _this.wrapperLeft+'px';
                if (_this.wrapperRight>0) wrapper.style.paddingRight = _this.wrapperRight+'px';
            }
            else {
                if (_this.wrapperTop>0) wrapper.style.paddingTop = _this.wrapperTop+'px';
                if (_this.wrapperBottom>0) wrapper.style.paddingBottom = _this.wrapperBottom+'px';
            }

            wrapperSize = isH ? wrapperWidth + _this.wrapperRight + _this.wrapperLeft : wrapperHeight + _this.wrapperTop + _this.wrapperBottom;
            if (!params.cssWidthAndHeight) {
                if (parseFloat(wrapperWidth) > 0) {
                    wrapper.style.width = wrapperWidth+'px';

                }

                if (parseFloat(wrapperHeight) > 0) {
                    wrapper.style.height = wrapperHeight+'px';

                }

            }
            var slideLeft = 0;
            _this.snapGrid = [];
            _this.slidesGrid = [];
            for (var i=0; i<_this.slides.length; i++) {
                _this.snapGrid.push(slideLeft);
                _this.slidesGrid.push(slideLeft);
                slideLeft+=slideSize;
                if (!params.cssWidthAndHeight) {
                    if (parseFloat(slideWidth) > 0) {
                        _this.slides[i].style.width = slideWidth+'px';

                    }

                    if (parseFloat(slideHeight) > 0) {
                        _this.slides[i].style.height = slideHeight+'px';

                    }

                }
            }

        }

        if (!_this.initialized) {
            _this.callPlugins('onFirstInit');
            if (params.onFirstInit) _this.fireCallback(params.onFirstInit,_this);
        }
        else {
            _this.callPlugins('onInit');
             if (params.onInit) _this.fireCallback(params.onInit,_this);
        }
        _this.initialized = true;
    }
    
    _this.reInit = function (forceCalcSlides) {
        _this.init(true, forceCalcSlides);
    }
    
    _this.resizeFix = function (reInit) {

        _this.callPlugins('beforeResizeFix');
        
        _this.init(params.resizeReInit || reInit);
        
        // swipe to active slide in fixed mode
        if (!params.freeMode) {
            _this.swipeTo((params.loop ? _this.activeLoopIndex : _this.activeIndex), 0, false);
        }
        
        // move wrapper to the beginning in free mode
        else if (_this.getWrapperTranslate() < -maxWrapperPosition()) {
        	_this.setWrapperTransition(0);
            _this.setWrapperTranslate(-maxWrapperPosition());
        }
        
        _this.callPlugins('afterResizeFix');
    }

    /*==========================================
        Max and Min Positions
    ============================================*/
    function maxWrapperPosition() {
        var a = (wrapperSize - containerSize);
        if (params.freeMode) {
            a = wrapperSize - containerSize;
        }
        // if (params.loop) a -= containerSize;
        if (params.slidesPerView > _this.slides.length) a = 0;
        if (a<0) a = 0;
        return a;
    }
    function minWrapperPosition() {
        var a = 0;
        // if (params.loop) a = containerSize;
        return a;
    }

    /*==========================================
        Event Listeners
    ============================================*/
    function initEvents() {
        var bind = _this.h.addEventListener;
        
        //Touch Events
        if (!_this.browser.ie10) {
            if (_this.support.touch) {
                bind(_this.wrapper, 'touchstart', onTouchStart);
                bind(_this.wrapper, 'touchmove', onTouchMove);
                bind(_this.wrapper, 'touchend', onTouchEnd);
            }
            if (params.simulateTouch) {
                bind(_this.wrapper, 'mousedown', onTouchStart);
                bind(document, 'mousemove', onTouchMove);
                bind(document, 'mouseup', onTouchEnd);
            }
        }
        else {
            bind(_this.wrapper, _this.touchEvents.touchStart, onTouchStart);
            bind(document, _this.touchEvents.touchMove, onTouchMove);
            bind(document, _this.touchEvents.touchEnd, onTouchEnd);
        }

        //Resize Event
        if (params.autoResize) {
            bind(window, 'resize', _this.resizeFix);
        }
        //Slide Events
        addSlideEvents();
        //Mousewheel
        _this._wheelEvent = false;
        if (params.mousewheelControl) {
            if ( document.onmousewheel !== undefined ) {
                _this._wheelEvent = "mousewheel";
            }
            try {
                WheelEvent("wheel");
                _this._wheelEvent = "wheel";
            } catch (e) {}
            if ( !_this._wheelEvent ) {
                _this._wheelEvent = "DOMMouseScroll";
            }

            if (_this._wheelEvent) {
                bind(_this.container, _this._wheelEvent, handleMousewheel);
            }
        }

        //Keyboard
        if (params.keyboardControl) {
            bind(document, 'keydown', handleKeyboardKeys);
        }
        if (params.updateOnImagesReady) {
            _this.imagesToLoad = $$('img', _this.container);

            for (var i=0; i<_this.imagesToLoad.length; i++) {
                _loadImage(_this.imagesToLoad[i].getAttribute('src'))
            }
        }
        function _loadImage(src) {
            var image = new Image();
            image.onload = function(){
                _this.imagesLoaded++;
                if (_this.imagesLoaded==_this.imagesToLoad.length) {
                    _this.reInit();
                    if (params.onImagesReady) _this.fireCallback(params.onImagesReady, _this);
                }
            }
            image.src = src;
        }
    }

    //Remove Event Listeners
    _this.destroy = function(removeResizeFix){
    	var unbind = _this.h.removeEventListener;
    	
        //Touch Events
        if (!_this.browser.ie10) {
            if (_this.support.touch) {
                unbind(_this.wrapper, 'touchstart', onTouchStart);
                unbind(_this.wrapper, 'touchmove', onTouchMove);
                unbind(_this.wrapper, 'touchend', onTouchEnd);
            }
            if (params.simulateTouch) {
                unbind(_this.wrapper, 'mousedown', onTouchStart);
                unbind(document, 'mousemove', onTouchMove);
                unbind(document, 'mouseup', onTouchEnd);
            }
        }
        else {
            unbind(_this.wrapper, _this.touchEvents.touchStart, onTouchStart);
            unbind(document, _this.touchEvents.touchMove, onTouchMove);
            unbind(document, _this.touchEvents.touchEnd, onTouchEnd);
        }

        //Resize Event
        if (params.autoResize) {
            unbind(window, 'resize', _this.resizeFix);
        }
        
        //Init Slide Events
        removeSlideEvents();

        //Pagination
        if (params.paginationClickable) {
            removePaginationEvents();
        }

        //Mousewheel
        if (params.mousewheelControl && _this._wheelEvent) {
           unbind(_this.container, _this._wheelEvent, handleMousewheel);
        }

        //Keyboard
        if (params.keyboardControl) {
            unbind(document, 'keydown', handleKeyboardKeys);
        }

        //Stop autoplay
        if (params.autoplay) {
            _this.stopAutoplay();
        }
        _this.callPlugins('onDestroy');

        //Destroy variable
        _this = null;
    }
    function addSlideEvents() {
    	var bind = _this.h.addEventListener,
    		i;

        //Prevent Links Events
        if (params.preventLinks) {
            var links = $$('a', _this.container);
            for (i=0; i<links.length; i++) {
                bind(links[i], 'click', preventClick);
            }
        }
        //Release Form Elements
        if (params.releaseFormElements) {
            var formElements = $$('input, textarea, select', _this.container);
            for (i=0; i<formElements.length; i++) {
                bind(formElements[i], _this.touchEvents.touchStart, releaseForms, true);
            }
        }

        //Slide Clicks & Touches
        if (params.onSlideClick) {
            for (i=0; i<_this.slides.length; i++) {
                bind(_this.slides[i], 'click', slideClick);
            }
        }
        if (params.onSlideTouch) {
            for (i=0; i<_this.slides.length; i++) {
                bind(_this.slides[i], _this.touchEvents.touchStart, slideTouch);
            }
        }
    }
    function removeSlideEvents() {
    	var unbind = _this.h.removeEventListener,
    		i;

        //Slide Clicks & Touches
        if (params.onSlideClick) {
            for (i=0; i<_this.slides.length; i++) {
                unbind(_this.slides[i], 'click', slideClick);
            }
        }
        if (params.onSlideTouch) {
            for (i=0; i<_this.slides.length; i++) {
                unbind(_this.slides[i], _this.touchEvents.touchStart, slideTouch);
            }
        }
        //Release Form Elements
        if (params.releaseFormElements) {
            var formElements = $$('input, textarea, select', _this.container);
            for (i=0; i<formElements.length; i++) {
                unbind(formElements[i], _this.touchEvents.touchStart, releaseForms, true);
            }
        }
        //Prevent Links Events
        if (params.preventLinks) {
            var links = $$('a', _this.container);
            for (i=0; i<links.length; i++) {
                unbind(links[i], 'click', preventClick);
            }
        }
    }
    /*==========================================
        Keyboard Control
    ============================================*/
    function handleKeyboardKeys (e) {
        var kc = e.keyCode || e.charCode;
        if (kc==37 || kc==39 || kc==38 || kc==40) {
            var inView = false;
            //Check that swiper should be inside of visible area of window
            var swiperOffset = _this.h.getOffset( _this.container );
            var scrollLeft = _this.h.windowScroll().left;
            var scrollTop = _this.h.windowScroll().top;
            var windowWidth = _this.h.windowWidth();
            var windowHeight = _this.h.windowHeight();
            var swiperCoord = [
                [swiperOffset.left, swiperOffset.top],
                [swiperOffset.left + _this.width, swiperOffset.top],
                [swiperOffset.left, swiperOffset.top + _this.height],
                [swiperOffset.left + _this.width, swiperOffset.top + _this.height]
            ]
            for (var i=0; i<swiperCoord.length; i++) {
                var point = swiperCoord[i];
                if (
                    point[0]>=scrollLeft && point[0]<=scrollLeft+windowWidth &&
                    point[1]>=scrollTop && point[1]<=scrollTop+windowHeight
                ) {
                    inView = true;
                }

            }
            if (!inView) return;
        }
        if (isH) {
            if (kc==37 || kc==39) {
                if (e.preventDefault) e.preventDefault();
                else e.returnValue = false;
            }
            if (kc == 39) _this.swipeNext();
            if (kc == 37) _this.swipePrev();
        }
        else {
            if (kc==38 || kc==40) {
                if (e.preventDefault) e.preventDefault();
                else e.returnValue = false;
            }
            if (kc == 40) _this.swipeNext();
            if (kc == 38) _this.swipePrev();
        }
    }

    /*==========================================
        Mousewheel Control
    ============================================*/
    var allowScrollChange = true;
    var lastScrollTime = (new Date()).getTime();
    function handleMousewheel (e) {
        var we = _this._wheelEvent;
        var delta = 0;
        //Opera & IE
        if (e.detail) delta = -e.detail;
        //WebKits
        else if (we == 'mousewheel') delta = e.wheelDelta;
        //Old FireFox
        else if (we == 'DOMMouseScroll') delta = -e.detail;
        //New FireFox
        else if (we == 'wheel') {
            delta = Math.abs(e.deltaX)>Math.abs(e.deltaY) ? - e.deltaX : - e.deltaY;
        }
        if (!params.freeMode) {
            if(delta<0) _this.swipeNext();
            else _this.swipePrev();
        }
        else {
            //Freemode or scrollContainer:
            var position = _this.getWrapperTranslate() + delta;
            
            if (position > 0) position = 0;
            if (position < -maxWrapperPosition()) position = -maxWrapperPosition();
            
            _this.setWrapperTransition(0);
            _this.setWrapperTranslate(position);
            _this.updateActiveSlide(position);
        }
        if (params.autoplay) _this.stopAutoplay();

        if(e.preventDefault) e.preventDefault();
        else e.returnValue = false;
        return false;
    }

    /*=========================
      Grab Cursor
      ===========================*/
    if (params.grabCursor) {
    	var containerStyle = _this.container.style;
        containerStyle.cursor = 'move';
        containerStyle.cursor = 'grab';
        containerStyle.cursor = '-moz-grab';
        containerStyle.cursor = '-webkit-grab';
    }

    /*=========================
      Slides Events Handlers
      ===========================*/
    
    _this.allowSlideClick = true;
    function slideClick(event) {
        if (_this.allowSlideClick) {
            
            setClickedSlide(event);
            this.fireCallback(params.onSlideClick, _this, event);
        }
    }
    
    function slideTouch(event) {
    	
        setClickedSlide(event);
       _this.fireCallback(params.onSlideTouch, _this, event);
    }
    
    function setClickedSlide(event) {
		
		// IE 6-8 support
		if (!event.currentTarget) {
			var element = event.srcElement;
			do {
				if (element.className.indexOf(params.slideClass) > -1) {
                	break;
        		}
			}
			while (element = element.parentNode);
			_this.clickedSlide = element;
		}
		else {
			_this.clickedSlide = event.currentTarget;
		}
		
        _this.clickedSlideIndex     = _this.slides.indexOf(_this.clickedSlide);
        _this.clickedSlideLoopIndex = _this.clickedSlideIndex - (_this.loopedSlides || 0);
    }
    
    _this.allowLinks = true;
    function preventClick(e) {
        if (!_this.allowLinks) {
            if(e.preventDefault) e.preventDefault();
            else e.returnValue = false;
            return false;
        }
    }
    function releaseForms(e) {
        if (e.stopPropagation) e.stopPropagation();
        else e.returnValue = false;
        return false;
    }

    /*==================================================
        Event Handlers
    ====================================================*/
    var isTouchEvent = false;
    var allowThresholdMove;
    var allowMomentumBounce = true;
    function onTouchStart(event) {

    	//vc gallery when using lightbox fix
    	if(jQuery(event.target).parents('.swiper-container').attr('data-desktop-swipe') == 'false' && !Modernizr.touch) return false;
    	
    	//deactivate touch if only one slide
		if(jQuery(event.target).parents('.swiper-container').find('.swiper-slide').length == 1) return false;
		
		//deactivate touch for duplicate transitions
		if(jQuery(event.target).parents('.swiper-container').find('.swiper-slide.duplicate-transition').length > 0) return false;
		
        if (params.preventLinks) _this.allowLinks = true;
        //Exit if slider is already was touched
        if (_this.isTouched || params.onlyExternal) {
            return false;
        }

        if (params.noSwiping && (event.target || event.srcElement) && noSwipingSlide(event.target || event.srcElement)) return false;
        allowMomentumBounce = false;

        //Check For Nested Swipers
        _this.isTouched = true;
        isTouchEvent = event.type=='touchstart';

        if (!isTouchEvent || event.targetTouches.length == 1 ) {
            _this.callPlugins('onTouchStartBegin');

            if(!isTouchEvent) {
                if(event.preventDefault) event.preventDefault();
                else event.returnValue = false;
            }
            
            var pageX = isTouchEvent ? event.targetTouches[0].pageX : (event.pageX || event.clientX);
            var pageY = isTouchEvent ? event.targetTouches[0].pageY : (event.pageY || event.clientY);

            //Start Touches to check the scrolling
            _this.touches.startX = _this.touches.currentX = pageX;
            _this.touches.startY = _this.touches.currentY = pageY;

            _this.touches.start = _this.touches.current = isH ? pageX : pageY;

            //Set Transition Time to 0
            _this.setWrapperTransition(0);

            //Get Start Translate Position
            _this.positions.start = _this.positions.current = _this.getWrapperTranslate();

            //Set Transform
            _this.setWrapperTranslate(_this.positions.start);

            //TouchStartTime
            _this.times.start = (new Date()).getTime();

            //Unset Scrolling
            isScrolling = undefined;

            //Set Treshold
            if (params.moveStartThreshold>0) allowThresholdMove = false;

            //CallBack
            if (params.onTouchStart) _this.fireCallback(params.onTouchStart, _this);
            _this.callPlugins('onTouchStartEnd');

        }
    }
    var velocityPrevPosition, velocityPrevTime;
    function onTouchMove(event) {
        // If slider is not touched - exit
        if (!_this.isTouched || params.onlyExternal) return;
        if (isTouchEvent && event.type=='mousemove') return;

        var pageX = isTouchEvent ? event.targetTouches[0].pageX : (event.pageX || event.clientX);
        var pageY = isTouchEvent ? event.targetTouches[0].pageY : (event.pageY || event.clientY);

        //check for scrolling
        if ( typeof isScrolling === 'undefined' && isH) {
          isScrolling = !!( isScrolling || Math.abs(pageY - _this.touches.startY) > Math.abs( pageX - _this.touches.startX ) );
        }
        if ( typeof isScrolling === 'undefined' && !isH) {
          isScrolling = !!( isScrolling || Math.abs(pageY - _this.touches.startY) < Math.abs( pageX - _this.touches.startX ) );
        }
        if (isScrolling ) {
            _this.isTouched = false;
            return;
        }

        //Check For Nested Swipers
        if (event.assignedToSwiper) {
            _this.isTouched = false;
            return;
        }
        event.assignedToSwiper = true;

        //Block inner links
        if (params.preventLinks) {
            _this.allowLinks = false;
        }
        if (params.onSlideClick) {
            _this.allowSlideClick = false;
        }

        //Stop AutoPlay if exist
        if (params.autoplay) {
            _this.stopAutoplay();
        }
        if (!isTouchEvent || event.touches.length == 1) {

        	//Moved Flag
        	if (!_this.isMoved) {
        		_this.callPlugins('onTouchMoveStart');
        		
				if (params.loop) {
					_this.fixLoop();
					_this.positions.start = _this.getWrapperTranslate();
				}
				if (params.onTouchMoveStart) _this.fireCallback(params.onTouchMoveStart, _this);
        	}
        	_this.isMoved = true;
        	
        	// cancel event
            if(event.preventDefault) event.preventDefault();
            else event.returnValue = false;

            _this.touches.current = isH ? pageX : pageY ;

            _this.positions.current = (_this.touches.current - _this.touches.start) * params.touchRatio + _this.positions.start;

            //Resistance Callbacks
            if(_this.positions.current > 0 && params.onResistanceBefore) {
                _this.fireCallback(params.onResistanceBefore, _this, _this.positions.current);
            }
            if(_this.positions.current < -maxWrapperPosition() && params.onResistanceAfter) {
                _this.fireCallback(params.onResistanceAfter, _this, Math.abs(_this.positions.current + maxWrapperPosition()));
            }
            //Resistance
            if (params.resistance && params.resistance!='100%') {
                //Resistance for Negative-Back sliding
                if(_this.positions.current > 0) {
                    var resistance = 1 - _this.positions.current/containerSize/2;
                    if (resistance < 0.5)
                        _this.positions.current = (containerSize/2);
                    else
                        _this.positions.current = _this.positions.current * resistance;
                }
                //Resistance for After-End Sliding
                if ( _this.positions.current < -maxWrapperPosition() ) {

                    var diff = (_this.touches.current - _this.touches.start)*params.touchRatio + (maxWrapperPosition()+_this.positions.start);
                    var resistance = (containerSize+diff)/(containerSize);
                    var newPos = _this.positions.current-diff*(1-resistance)/2;
                    var stopPos = -maxWrapperPosition() - containerSize/2;

                    if (newPos < stopPos || resistance<=0)
                        _this.positions.current = stopPos;
                    else
                        _this.positions.current = newPos;
                }
            }
            if (params.resistance && params.resistance=='100%') {
                //Resistance for Negative-Back sliding
                if(_this.positions.current > 0 && !(params.freeMode&&!params.freeModeFluid)) {
                    _this.positions.current = 0;
                }
                //Resistance for After-End Sliding
                if ( (_this.positions.current) < -maxWrapperPosition() && !(params.freeMode&&!params.freeModeFluid)) {
                    _this.positions.current = -maxWrapperPosition();
                }
            }
            //Move Slides
            if (!params.followFinger) return;

            if (!params.moveStartThreshold) {
                _this.setWrapperTranslate(_this.positions.current);
            }
            else {
                if ( Math.abs(_this.touches.current - _this.touches.start)>params.moveStartThreshold || allowThresholdMove) {
                    allowThresholdMove = true;
                    _this.setWrapperTranslate(_this.positions.current);
                }
                else {
                    _this.positions.current = _this.positions.start;
                }
            }

            if (params.freeMode || params.watchActiveIndex) {
                _this.updateActiveSlide(_this.positions.current);
            }

            //Grab Cursor
             //Grab Cursor
            if (params.grabCursor) {
                _this.container.style.cursor = 'move';
                _this.container.style.cursor = 'grabbing';
                _this.container.style.cursor = '-moz-grabbing';
                _this.container.style.cursor = '-webkit-grabbing';
            }  
            //Velocity
            if (!velocityPrevPosition) velocityPrevPosition = _this.touches.current;
            if (!velocityPrevTime) velocityPrevTime = (new Date).getTime();
            _this.velocity = (_this.touches.current - velocityPrevPosition)/((new Date).getTime() - velocityPrevTime)/2;

            if (Math.abs(_this.touches.current - velocityPrevPosition)<2) _this.velocity=0;
            velocityPrevPosition = _this.touches.current;
            velocityPrevTime = (new Date).getTime();
            //Callbacks
            _this.callPlugins('onTouchMoveEnd');
            if (params.onTouchMove) _this.fireCallback(params.onTouchMove, _this);

            return false;
        }
    }
    function onTouchEnd(event) {
        //Check For scrolling
        if (isScrolling) {
            _this.swipeReset();
        }
        // If slider is not touched exit
        if ( params.onlyExternal || !_this.isTouched ) return;
        _this.isTouched = false

        //Return Grab Cursor
        if (params.grabCursor) {
            _this.container.style.cursor = 'move';
            _this.container.style.cursor = 'grab';
            _this.container.style.cursor = '-moz-grab';
            _this.container.style.cursor = '-webkit-grab';
        }

        //Check for Current Position
        if (!_this.positions.current && _this.positions.current!==0) {
            _this.positions.current = _this.positions.start
        }

        //For case if slider touched but not moved
        if (params.followFinger) {
            _this.setWrapperTranslate(_this.positions.current);
        }

        // TouchEndTime
        _this.times.end = (new Date()).getTime();

        //Difference
        _this.touches.diff = _this.touches.current - _this.touches.start
        _this.touches.abs = Math.abs(_this.touches.diff)

        _this.positions.diff = _this.positions.current - _this.positions.start
        _this.positions.abs = Math.abs(_this.positions.diff)

        var diff = _this.positions.diff ;
        var diffAbs =_this.positions.abs ;
        var timeDiff = _this.times.end - _this.times.start

        if(diffAbs < 5 && (timeDiff) < 300 && _this.allowLinks == false) {
            if (!params.freeMode && diffAbs!=0) _this.swipeReset()
            //Release inner links
            if (params.preventLinks) {
                _this.allowLinks = true;
            }
            if (params.onSlideClick) {
                _this.allowSlideClick = true;
            }
        }
        
        setTimeout(function () {
            //Release inner links
            if (params.preventLinks) {
                _this.allowLinks = true;
            }
            if (params.onSlideClick) {
                _this.allowSlideClick = true;
            }
        }, 100);

        var maxPosition = maxWrapperPosition();

        //Not moved or Prevent Negative Back Sliding/After-End Sliding
        if (!_this.isMoved && params.freeMode) {
            _this.isMoved = false;
            if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this);
            _this.callPlugins('onTouchEnd');
            return;   
        }
        if (!_this.isMoved || _this.positions.current > 0 || _this.positions.current < -maxPosition) {
            _this.swipeReset();
           if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this);
            _this.callPlugins('onTouchEnd');
            return;
        }

        _this.isMoved = false;

        //Free Mode
        if (params.freeMode) {
            if ( params.freeModeFluid ) {
                var momentumDuration = 1000*params.momentumRatio;
                var momentumDistance = _this.velocity*momentumDuration;
                var newPosition = _this.positions.current + momentumDistance
                var doBounce = false;
                var afterBouncePosition;
                var bounceAmount = Math.abs( _this.velocity )*20*params.momentumBounceRatio;
                if (newPosition < -maxPosition) {
                    if (params.momentumBounce && _this.support.transitions) {
                        if (newPosition + maxPosition < -bounceAmount) newPosition = -maxPosition-bounceAmount;
                        afterBouncePosition = -maxPosition;
                        doBounce=true;
                        allowMomentumBounce = true;
                    }
                    else newPosition = -maxPosition;
                }
                if (newPosition > 0) {
                    if (params.momentumBounce && _this.support.transitions) {
                        if (newPosition>bounceAmount) newPosition = bounceAmount;
                        afterBouncePosition = 0
                        doBounce = true;
                        allowMomentumBounce = true;
                    }
                    else newPosition = 0;
                }
                //Fix duration
                if (_this.velocity!=0) momentumDuration = Math.abs((newPosition - _this.positions.current)/_this.velocity)

                _this.setWrapperTranslate(newPosition);

                _this.setWrapperTransition( momentumDuration );

                if (params.momentumBounce && doBounce) {
                    _this.wrapperTransitionEnd(function () {
                        if (!allowMomentumBounce) return;
                        
                        if (params.onMomentumBounce) params.onMomentumBounce(_this);
                        
                        _this.setWrapperTranslate(afterBouncePosition);
                        _this.setWrapperTransition(300);
                    })
                }

                _this.updateActiveSlide(newPosition)
            }
            if (!params.freeModeFluid || timeDiff >= 300) _this.updateActiveSlide(_this.positions.current)

            if (params.onTouchEnd) params.onTouchEnd(_this)
            _this.callPlugins('onTouchEnd');
            return;
        }

        //Direction
        direction = diff < 0 ? "toNext" : "toPrev"

        //Short Touches
        if (direction=="toNext" && ( timeDiff <= 300 ) ) {
            if (diffAbs < 30 || !params.shortSwipes) _this.swipeReset()
            else _this.swipeNext(true);
        }

        if (direction=="toPrev" && ( timeDiff <= 300 ) ) {
            if (diffAbs < 30 || !params.shortSwipes) _this.swipeReset()
            else _this.swipePrev(true);
        }

        //Long Touches
        var targetSlideSize = 0;
        if(params.slidesPerView == 'auto') {
            //Define current slide's width
            var currentPosition = Math.abs(_this.getWrapperTranslate());
            var slidesOffset = 0;
            var _slideSize;
            for (var i=0; i<_this.slides.length; i++) {
                _slideSize = isH ? _this.slides[i].getWidth(true) : _this.slides[i].getHeight(true);
                slidesOffset+= _slideSize;
                if (slidesOffset>currentPosition) {
                    targetSlideSize = _slideSize;
                    break;
                }
            }
            if (targetSlideSize>containerSize) targetSlideSize = containerSize;
        }
        else {
            targetSlideSize = slideSize * params.slidesPerView;
        }
        if (direction=="toNext" && ( timeDiff > 300 ) ) {
            if (diffAbs >= targetSlideSize*0.5) {
                _this.swipeNext(true)
            }
            else {
                _this.swipeReset()
            }
        }
        if (direction=="toPrev" && ( timeDiff > 300 ) ) {
            if (diffAbs >= targetSlideSize*0.5) {
                _this.swipePrev(true);
            }
            else {
                _this.swipeReset()
            }
        }
        if (params.onTouchEnd) params.onTouchEnd(_this)
        _this.callPlugins('onTouchEnd');
    }


    /*==================================================
        noSwiping Bubble Check by Isaac Strack
    ====================================================*/
    function noSwipingSlide(el){
        /*This function is specifically designed to check the parent elements for the noSwiping class, up to the wrapper.
        We need to check parents because while onTouchStart bubbles, _this.isTouched is checked in onTouchStart, which stops the bubbling.
        So, if a text box, for example, is the initial target, and the parent slide container has the noSwiping class, the _this.isTouched
        check will never find it, and what was supposed to be noSwiping is able to be swiped.
        This function will iterate up and check for the noSwiping class in parents, up through the wrapperClass.*/

        // First we create a truthy variable, which is that swiping is allowd (noSwiping = false)
        var noSwiping = false;
    
        // Now we iterate up (parentElements) until we reach the node with the wrapperClass.
        do{

            // Each time, we check to see if there's a 'swiper-no-swiping' class (noSwipingClass).
            if (el.className.indexOf(params.noSwipingClass)>-1)
            {
                noSwiping = true; // If there is, we set noSwiping = true;
            }

            el = el.parentElement;  // now we iterate up (parent node)

        } while(!noSwiping && el.parentElement && el.className.indexOf(params.wrapperClass)==-1); // also include el.parentElement truthy, just in case.

        // because we didn't check the wrapper itself, we do so now, if noSwiping is false:
        if (!noSwiping && el.className.indexOf(params.wrapperClass)>-1 && el.className.indexOf(params.noSwipingClass)>-1)
            noSwiping = true; // if the wrapper has the noSwipingClass, we set noSwiping = true;

        return noSwiping;
    }

    /*==================================================
        Swipe Functions
    ====================================================*/
    _this.swipeNext = function(internal){
        if (!internal && params.loop) _this.fixLoop();
        _this.callPlugins('onSwipeNext');
        var currentPosition = _this.getWrapperTranslate();
        var newPosition = currentPosition;
        if (params.slidesPerView=='auto') {
            for (var i=0; i<_this.snapGrid.length; i++) {
                if (-currentPosition >= _this.snapGrid[i] && -currentPosition<_this.snapGrid[i+1]) {
                    newPosition = -_this.snapGrid[i+1]
                    break;
                }
            }
        }
        else {
            var groupSize = slideSize * params.slidesPerGroup;
            newPosition = -(Math.floor(Math.abs(currentPosition)/Math.floor(groupSize))*groupSize + groupSize);
        }
        if (newPosition < - maxWrapperPosition()) {
            newPosition = - maxWrapperPosition()
        };

        if (newPosition == currentPosition) return false;

        swipeToPosition(newPosition, 'next');
        return true
    }
    _this.swipePrev = function(internal){
        if (!internal && params.loop) _this.fixLoop();
        if (!internal && params.autoplay) _this.stopAutoplay();
        _this.callPlugins('onSwipePrev');

        var currentPosition = Math.ceil(_this.getWrapperTranslate());
        var newPosition;
        if (params.slidesPerView=='auto') {
            newPosition = 0;
            for (var i=1; i<_this.snapGrid.length; i++) {
                if (-currentPosition == _this.snapGrid[i]) {
                    newPosition = -_this.snapGrid[i-1]
                    break;
                }
                if (-currentPosition > _this.snapGrid[i] && -currentPosition<_this.snapGrid[i+1]) {
                    newPosition = -_this.snapGrid[i]
                    break;
                }
            }
        }
        else {
            var groupSize = slideSize * params.slidesPerGroup;
            newPosition = -(Math.ceil(-currentPosition/groupSize)-1)*groupSize;
        }

        if (newPosition > 0) newPosition = 0;

        if (newPosition == currentPosition) return false;
        swipeToPosition(newPosition, 'prev');
        return true;

    }
    _this.swipeReset = function(){
        _this.callPlugins('onSwipeReset');
        var currentPosition = _this.getWrapperTranslate();
        var groupSize = slideSize * params.slidesPerGroup;
        var newPosition;
        var maxPosition = -maxWrapperPosition();
        if (params.slidesPerView=='auto') {
            newPosition = 0;
            for (var i=0; i<_this.snapGrid.length; i++) {
                if (-currentPosition===_this.snapGrid[i]) return;
                if (-currentPosition >= _this.snapGrid[i] && -currentPosition<_this.snapGrid[i+1]) {
                    if(_this.positions.diff>0) newPosition = -_this.snapGrid[i+1]
                    else newPosition = -_this.snapGrid[i]
                    break;
                }
            }
            if (-currentPosition >= _this.snapGrid[_this.snapGrid.length-1]) newPosition = -_this.snapGrid[_this.snapGrid.length-1];
            if (currentPosition <= -maxWrapperPosition()) newPosition = -maxWrapperPosition()
        }
        else {
            newPosition = currentPosition<0 ? Math.round(currentPosition/groupSize)*groupSize : 0
        }
        if (params.scrollContainer)  {
            newPosition = currentPosition<0 ? currentPosition : 0;
        }
        if (newPosition < -maxWrapperPosition()) {
            newPosition = -maxWrapperPosition()
        }
        if (params.scrollContainer && (containerSize>slideSize)) {
            newPosition = 0;
        }

        if (newPosition == currentPosition) return false;

        swipeToPosition(newPosition, 'reset');
        return true;
    }
    
    _this.swipeTo = function(index, speed, runCallbacks) {
        index = parseInt(index, 10);
        _this.callPlugins('onSwipeTo', {index:index, speed:speed});
        if (params.loop) index = index + _this.loopedSlides;
        var currentPosition = _this.getWrapperTranslate();
        if (index > (_this.slides.length-1) || index < 0) return;
        var newPosition
        if (params.slidesPerView=='auto') {
            newPosition = -_this.slidesGrid[ index ];
        }
        else {
            newPosition =  -index*slideSize;
        }
        if (newPosition < - maxWrapperPosition()) {
            newPosition = - maxWrapperPosition();
        };

        if (newPosition == currentPosition) return false;

        runCallbacks = runCallbacks===false ? false : true;
        swipeToPosition(newPosition, 'to', {index:index, speed:speed, runCallbacks:runCallbacks});
        return true;
    }
    
    function swipeToPosition(newPosition, action, toOptions) {
    	var speed = (action=='to' && toOptions.speed >= 0) ? toOptions.speed : params.speed;
    	
        var timeOld = + new Date();
        if (_this.support.transitions || !params.DOMAnimation) {
            _this.setWrapperTranslate(newPosition);
            _this.setWrapperTransition(speed);
        }
        else {
            //Try the DOM animation
            var currentPosition = _this.getWrapperTranslate();
            var animationStep = Math.ceil( (newPosition - currentPosition)/speed*(1000/60) );
            var direction = currentPosition > newPosition ? 'toNext' : 'toPrev';
            var condition = direction=='toNext' ? currentPosition > newPosition : currentPosition < newPosition;
            if (_this._DOMAnimating) return;

            anim();
        }
        function anim(){
            var timeNew = + new Date();
            var time = timeNew - timeOld;
            currentPosition += animationStep * time / (1000/60);

            condition = direction=='toNext' ? currentPosition > newPosition : currentPosition < newPosition;
            if (condition) {
                _this.setWrapperTranslate(Math.round(currentPosition));
                _this._DOMAnimating = true
                window.setTimeout(function(){
                    anim()
                }, 1000 / 60)
            }
            else {
                if (params.onSlideChangeEnd) { 
                  _this.fireCallback(params.onSlideChangeEnd, _this);
                }
                _this.setWrapperTranslate(newPosition);
                _this._DOMAnimating = false;
            }
        }

        //Update Active Slide Index
        _this.updateActiveSlide(newPosition);

        //Callbacks
        if (params.onSlideNext && action=='next') {
            _this.fireCallback(params.onSlideNext, _this, newPosition);
        }
        if (params.onSlidePrev && action=='prev') {
            _this.fireCallback(params.onSlidePrev, _this, newPosition);
        }
        //"Reset" Callback
        if (params.onSlideReset && action=='reset') {
             _this.fireCallback(params.onSlideReset, _this, newPosition);
        }

        //"Next", "Prev" and "To" Callbacks
        if (action=='next' || action=='prev' || (action=='to' && toOptions.runCallbacks==true))
            slideChangeCallbacks(action);
    }
    /*==================================================
        Transition Callbacks
    ====================================================*/
    //Prevent Multiple Callbacks
    _this._queueStartCallbacks = false;
    _this._queueEndCallbacks = false;
    function slideChangeCallbacks(direction) {
        //Transition Start Callback
        _this.callPlugins('onSlideChangeStart');
        if (params.onSlideChangeStart) {
            if (params.queueStartCallbacks && _this.support.transitions) {
                if (_this._queueStartCallbacks) return;
                _this._queueStartCallbacks = true;
                _this.fireCallback(params.onSlideChangeStart, _this, direction)
                _this.wrapperTransitionEnd(function(){
                    _this._queueStartCallbacks = false;
                })
            }
            else _this.fireCallback(params.onSlideChangeStart, _this, direction)
        }
        //Transition End Callback
        if (params.onSlideChangeEnd) {
            if (_this.support.transitions) {
                if (params.queueEndCallbacks) {
                    if (_this._queueEndCallbacks) return;
                    _this._queueEndCallbacks = true;
                    _this.wrapperTransitionEnd(function(swiper){ 
                        setTimeout(function(){ if (params.loop) _this.fixLoop(); },50);
                        _this.fireCallback(params.onSlideChangeEnd, swiper, direction)
                    })
                     
                }
                else _this.wrapperTransitionEnd(function(swiper){
                    //setTimeout(function(){ if (params.loop) _this.fixLoop(); },50);
                    _this.fireCallback(params.onSlideChangeEnd, swiper, direction)
                })
            }
            else {
                if (!params.DOMAnimation) {
                    setTimeout(function(){
                       _this.fireCallback(params.onSlideChangeStart, _this, direction)
                    },10)
                }
            }
        }
    }
    
    /*==================================================
        Update Active Slide Index
    ====================================================*/
    _this.updateActiveSlide = function(position) {
        if (!_this.initialized) return;
        if (_this.slides.length==0) return;
        _this.previousIndex = _this.activeIndex;
        if (typeof position=='undefined') position = _this.getWrapperTranslate();
        if (position>0) position=0;
        
        if (params.slidesPerView == 'auto') {
            var slidesOffset = 0;
            _this.activeIndex = _this.slidesGrid.indexOf(-position);
            if (_this.activeIndex<0) {
                for (var i=0; i<_this.slidesGrid.length-1; i++) {
                    if (-position>_this.slidesGrid[i] && -position<_this.slidesGrid[i+1]) {
                        break;
                    }
                }
                var leftDistance = Math.abs( _this.slidesGrid[i] + position )
                var rightDistance = Math.abs( _this.slidesGrid[i+1] + position )
                if (leftDistance<=rightDistance) _this.activeIndex = i;
                else _this.activeIndex = i+1;
            }
        }
        else {
        	_this.activeIndex = Math[params.visibilityFullFit ? 'ceil' : 'round']( -position/slideSize );
        }
        
        if (_this.activeIndex == _this.slides.length ) _this.activeIndex = _this.slides.length - 1;
        if (_this.activeIndex < 0) _this.activeIndex = 0;
        
        // Check for slide
        if (!_this.slides[_this.activeIndex]) return;
        
        // Calc Visible slides
        _this.calcVisibleSlides(position);

        // Mark visible and active slides with additonal classes
        var activeClassRegexp = new RegExp( "\\s*" + params.slideActiveClass );
        var inViewClassRegexp = new RegExp( "\\s*" + params.slideVisibleClass );

        for (var i = 0; i < _this.slides.length; i++) {
            _this.slides[ i ].className = _this.slides[ i ].className.replace( activeClassRegexp, '' ).replace( inViewClassRegexp, '' );
            if ( _this.visibleSlides.indexOf( _this.slides[ i ] )>=0 ) {
                _this.slides[ i ].className += ' ' + params.slideVisibleClass;
            }

        }
        _this.slides[ _this.activeIndex ].className += ' ' + params.slideActiveClass;

        //Update loop index
        if (params.loop) {
            var ls = _this.loopedSlides;
            _this.activeLoopIndex = _this.activeIndex - ls;
            if (_this.activeLoopIndex >= _this.slides.length - ls*2 ) {
                _this.activeLoopIndex = _this.slides.length - ls*2 - _this.activeLoopIndex;
            }
            if (_this.activeLoopIndex<0) {
                _this.activeLoopIndex = _this.slides.length - ls*2 + _this.activeLoopIndex;
            }
        }
        else {
            _this.activeLoopIndex = _this.activeIndex;
        }
        //Update Pagination
        if (params.pagination) {
            _this.updatePagination(position);
        }
    }
    /*==================================================
        Pagination
    ====================================================*/
    _this.createPagination = function (firstInit) {
        if (params.paginationClickable && _this.paginationButtons) {
            removePaginationEvents();
        }
        var paginationHTML = "";
        var numOfSlides = _this.slides.length;
        var numOfButtons = numOfSlides;
        if (params.loop) numOfButtons -= _this.loopedSlides*2
        for (var i = 0; i < numOfButtons; i++) {
            paginationHTML += '<'+params.paginationElement+' class="'+params.paginationElementClass+'"></'+params.paginationElement+'>'
        }
        _this.paginationContainer = params.pagination.nodeType ? params.pagination : $$(params.pagination)[0];
        _this.paginationContainer.innerHTML = paginationHTML;
        _this.paginationButtons = $$('.'+params.paginationElementClass, _this.paginationContainer);
        if (!firstInit) _this.updatePagination()
        _this.callPlugins('onCreatePagination');
        if (params.paginationClickable) {
            addPaginationEvents();
        }
    }
    function removePaginationEvents() {
        var pagers = _this.paginationButtons;
        for (var i=0; i<pagers.length; i++) {
            _this.h.removeEventListener(pagers[i], 'click', paginationClick);
        }
    }
    function addPaginationEvents() {
        var pagers = _this.paginationButtons;
        for (var i=0; i<pagers.length; i++) {
            _this.h.addEventListener(pagers[i], 'click', paginationClick);
        }
    }
    function paginationClick(e){
        var index;
        var target = e.target || e.srcElement;
        var pagers = _this.paginationButtons;
        for (var i=0; i<pagers.length; i++) {
            if (target===pagers[i]) index = i;
        }
        _this.swipeTo(index)
    }
    _this.updatePagination = function(position) {
        if (!params.pagination) return;
        if (_this.slides.length<1) return;

        var activePagers = $$('.'+params.paginationActiveClass, _this.paginationContainer);
        if(!activePagers) return;

        //Reset all Buttons' class to not active
        var pagers = _this.paginationButtons;
        if (pagers.length==0) return;
        for (var i=0; i < pagers.length; i++) {
            pagers[i].className = params.paginationElementClass
        }

        var indexOffset = params.loop ? _this.loopedSlides : 0;
        if (params.paginationAsRange) {
            if (!_this.visibleSlides) _this.calcVisibleSlides(position)
            //Get Visible Indexes
            var visibleIndexes = [];
            for (var i = 0; i < _this.visibleSlides.length; i++) {
                var visIndex = _this.slides.indexOf( _this.visibleSlides[i] ) - indexOffset

                if (params.loop && visIndex<0) {
                    visIndex = _this.slides.length - _this.loopedSlides*2 + visIndex;
                }
                if (params.loop && visIndex>=_this.slides.length-_this.loopedSlides*2) {
                    visIndex = _this.slides.length - _this.loopedSlides*2 - visIndex;
                    visIndex = Math.abs(visIndex)
                }
                visibleIndexes.push( visIndex )
            }
            
            for (i=0; i<visibleIndexes.length; i++) {
                if (pagers[ visibleIndexes[i] ]) pagers[ visibleIndexes[i] ].className += ' ' + params.paginationVisibleClass;
            }
            
            if (params.loop) {
                pagers[ _this.activeLoopIndex ].className += ' ' + params.paginationActiveClass;
            }
            else {
                pagers[ _this.activeIndex ].className += ' ' + params.paginationActiveClass;
            }
            
        }
        else {
            if (params.loop) {
                pagers[ _this.activeLoopIndex ].className+=' '+params.paginationActiveClass+' '+params.paginationVisibleClass;
            }
            else {
                pagers[ _this.activeIndex ].className+=' '+params.paginationActiveClass+' '+params.paginationVisibleClass;
            }

        }

    }
    _this.calcVisibleSlides = function(position){
        var visibleSlides = [];
        var _slideLeft = 0, _slideSize = 0, _slideRight = 0;
        if (isH && _this.wrapperLeft>0) position = position+_this.wrapperLeft;
        if (!isH && _this.wrapperTop>0) position = position+_this.wrapperTop;

        for (var i=0; i<_this.slides.length; i++) {
            _slideLeft += _slideSize;
            if (params.slidesPerView == 'auto')
                _slideSize  = isH ? _this.h.getWidth(_this.slides[i],true) : _this.h.getHeight(_this.slides[i],true);
            else _slideSize = slideSize;

            _slideRight = _slideLeft + _slideSize;
            var isVisibile = false;
            if (params.visibilityFullFit) {
                if (_slideLeft >= -position && _slideRight <= -position+containerSize) isVisibile = true;
                if (_slideLeft <= -position && _slideRight >= -position+containerSize) isVisibile = true;
            }
            else {

                if (_slideRight > -position && _slideRight <= ((-position+containerSize))) isVisibile = true;
                if (_slideLeft >= -position && _slideLeft < ((-position+containerSize))) isVisibile = true;
                if (_slideLeft < -position && _slideRight > ((-position+containerSize))) isVisibile = true;
            }

            if (isVisibile) visibleSlides.push(_this.slides[i])

        }
        if (visibleSlides.length==0) visibleSlides = [ _this.slides[ _this.activeIndex ] ]

        _this.visibleSlides = visibleSlides;
    }

    /*==========================================
        Autoplay
    ============================================*/
    _this.autoPlayIntervalId = undefined;
    _this.startAutoplay = function () {
        if (typeof _this.autoPlayIntervalId !== 'undefined') return false;
        if (params.autoplay && !params.loop) {
            _this.autoPlayIntervalId = setInterval(function(){
                if (!_this.swipeNext(true)) _this.swipeTo(0);
            }, params.autoplay)
        }
        if (params.autoplay && params.loop) {
            _this.autoPlayIntervalId = setInterval(function(){
                _this.swipeNext();
            }, params.autoplay)
        }
        _this.callPlugins('onAutoplayStart');
    }
    _this.stopAutoplay = function () {
        if (_this.autoPlayIntervalId) clearInterval(_this.autoPlayIntervalId);
        _this.autoPlayIntervalId = undefined;
        _this.callPlugins('onAutoplayStop');
    }
    
    /*==================================================
        Loop
    ====================================================*/
    _this.loopCreated = false;
    _this.removeLoopedSlides = function(){
        if (_this.loopCreated) {
            for (var i=0; i<_this.slides.length; i++) {
                if (_this.slides[i].getData('looped')===true) _this.wrapper.removeChild(_this.slides[i]);
            }
        }
    }
    
    _this.createLoop = function() {
        if (_this.slides.length==0) return;
        
        _this.loopedSlides = params.slidesPerView + params.loopAdditionalSlides;
        if (_this.loopedSlides > _this.slides.length) {
			_this.loopedSlides = _this.slides.length;
        }
        
        var slideFirstHTML = '',
        	slideLastHTML = '',
        	i;

        //Grab First Slides
        for (i=0; i<_this.loopedSlides; i++) {
            slideFirstHTML += _this.slides[i].outerHTML;
        }
        //Grab Last Slides
        for (i=_this.slides.length-_this.loopedSlides; i<_this.slides.length; i++) {
            slideLastHTML += _this.slides[i].outerHTML;
        }
        wrapper.innerHTML = slideLastHTML + wrapper.innerHTML + slideFirstHTML;

        _this.loopCreated = true;
        _this.calcSlides();

        //Update Looped Slides with special class
        for (i=0; i<_this.slides.length; i++) {
            if (i<_this.loopedSlides || i>=_this.slides.length-_this.loopedSlides) _this.slides[i].setData('looped', true);
        }
        _this.callPlugins('onCreateLoop');

    }
    
    _this.fixLoop = function() {
    	
    	if(_this.params.loop == true) {
    		
	    	var newIndex;
	        //Fix For Negative Oversliding
	        if (_this.activeIndex < _this.loopedSlides) {
	            newIndex = _this.slides.length - _this.loopedSlides*3 + _this.activeIndex;
	            _this.swipeTo(newIndex, 0, false);
	        }
	        //Fix For Positive Oversliding
	        else if (_this.activeIndex > _this.slides.length - params.slidesPerView*2) {
	            newIndex = -_this.slides.length + _this.activeIndex + _this.loopedSlides
	            _this.swipeTo(newIndex, 0, false);
	        }
       }
       
    }
    
    /*==================================================
        Slides Loader
    ====================================================*/
    _this.loadSlides = function(){
        var slidesHTML = '';
        _this.activeLoaderIndex = 0;
        var slides = params.loader.slides;
        var slidesToLoad = params.loader.loadAllSlides ? slides.length : params.slidesPerView*(1+params.loader.surroundGroups);
        for (var i=0; i< slidesToLoad; i++) {
            if (params.loader.slidesHTMLType=='outer') slidesHTML+=slides[i];
            else {
                slidesHTML+='<'+params.slideElement+' class="'+params.slideClass+'" data-swiperindex="'+i+'">'+slides[i]+'</'+params.slideElement+'>';
            }
        }
        _this.wrapper.innerHTML = slidesHTML;
        _this.calcSlides(true);
        //Add permanent transitionEnd callback
        if (!params.loader.loadAllSlides) {
            _this.wrapperTransitionEnd(_this.reloadSlides, true);
        }
    }
    
    _this.reloadSlides = function(){
        var slides = params.loader.slides;
        var newActiveIndex = parseInt(_this.activeSlide().data('swiperindex'),10)
        if (newActiveIndex<0 || newActiveIndex>slides.length-1) return //<-- Exit
        _this.activeLoaderIndex = newActiveIndex;
        var firstIndex = Math.max(0, newActiveIndex - params.slidesPerView*params.loader.surroundGroups)
        var lastIndex = Math.min(newActiveIndex+params.slidesPerView*(1+params.loader.surroundGroups)-1, slides.length-1)
        //Update Transforms
        if (newActiveIndex>0) {
            var newTransform = -slideSize*(newActiveIndex-firstIndex)
            _this.setWrapperTranslate(newTransform);
            _this.setWrapperTransition(0);
        }
        //New Slides
        if (params.loader.logic==='reload') {
            _this.wrapper.innerHTML = '';
            var slidesHTML = '';
            for (var i = firstIndex; i<=lastIndex; i++) {
                slidesHTML += params.loader.slidesHTMLType == 'outer' ? slides[i] : '<'+params.slideElement+' class="'+params.slideClass+'" data-swiperindex="'+i+'">'+slides[i]+'</'+params.slideElement+'>';
            }
            _this.wrapper.innerHTML = slidesHTML;
        }
        else {
            var minExistIndex=1000;
            var maxExistIndex=0;
            for (var i=0; i<_this.slides.length; i++) {
                var index = _this.slides[i].data('swiperindex');
                if (index<firstIndex || index>lastIndex) {
                    _this.wrapper.removeChild(_this.slides[i]);
                }
                else {
                    minExistIndex = Math.min(index, minExistIndex)
                    maxExistIndex = Math.max(index, maxExistIndex)
                }
            }
            for (var i=firstIndex; i<=lastIndex; i++) {
                if (i<minExistIndex) {
                    var newSlide = document.createElement(params.slideElement);
                    newSlide.className = params.slideClass;
                    newSlide.setAttribute('data-swiperindex',i);
                    newSlide.innerHTML = slides[i];
                    _this.wrapper.insertBefore(newSlide, _this.wrapper.firstChild);
                }
                if (i>maxExistIndex) {
                    var newSlide = document.createElement(params.slideElement);
                    newSlide.className = params.slideClass;
                    newSlide.setAttribute('data-swiperindex',i);
                    newSlide.innerHTML = slides[i];
                    _this.wrapper.appendChild(newSlide);
                }
            }
        }
        //reInit
        _this.reInit(true);
    }
    
    /*==================================================
        Make Swiper
    ====================================================*/
    function makeSwiper(){
        _this.calcSlides();
        if (params.loader.slides.length>0 && _this.slides.length==0) {
            _this.loadSlides();
        }
        if (params.loop) {
            _this.createLoop();
        }
        _this.init();
        initEvents();
        if (params.pagination && params.createPagination) {
            _this.createPagination(true);
        }
        if (params.loop || params.initialSlide>0) {
            _this.swipeTo( params.initialSlide, 0, false );
        }
        else {
            _this.updateActiveSlide(0);
        }
        if (params.autoplay) {
            _this.startAutoplay();
        }

    }
    
    makeSwiper();
}

Swiper.prototype = {
    plugins : {},
    
    /*==================================================
        Wrapper Operations
    ====================================================*/
    wrapperTransitionEnd : function(callback, permanent) {
        var a = this,
        	el = a.wrapper,
        	events = ['webkitTransitionEnd', 'transitionend', 'oTransitionEnd', 'MSTransitionEnd', 'msTransitionEnd'],
        	i;
        
        function fireCallBack() {
            callback(a);
            if (a.params.queueEndCallbacks) a._queueEndCallbacks = false;
            if (!permanent) {
                for (i=0; i<events.length; i++) {
                    a.h.removeEventListener(el, events[i], fireCallBack);
                }
            }
        }
        
        if (callback) {
            for (i=0; i<events.length; i++) {
                a.h.addEventListener(el, events[i], fireCallBack);
            }
        }
    },

    getWrapperTranslate : function (axis) {
        var el = this.wrapper,
        	matrix, curTransform, curStyle, transformMatrix;
        
        // automatic axis detection
        if (typeof axis == 'undefined') {
			axis = this.params.mode == 'horizontal' ? 'x' : 'y';
        }
        
       curStyle = window.getComputedStyle(el, null);

            if (window.WebKitCSSMatrix) {
                transformMatrix = new WebKitCSSMatrix(curStyle.webkitTransform);
            }

            else {
                transformMatrix = curStyle.MozTransform || curStyle.OTransform || curStyle.MsTransform || curStyle.msTransform  || curStyle.transform || curStyle.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,");
                matrix = transformMatrix.toString().split(',');
            }
        
        if (this.support.transforms && this.params.useCSS3Transforms) {
            if (axis=='x') {
            	//Latest Chrome and webkits Fix
                if (window.WebKitCSSMatrix)
                    curTransform = transformMatrix.m41;
                //Crazy IE10 Matrix
                else if (matrix.length==16)
                    curTransform = parseFloat( matrix[12] );
                //Normal Browsers
                else
                    curTransform = parseFloat( matrix[4] );
            }
            if (axis=='y') {
            	//Latest Chrome and webkits Fix
                if (window.WebKitCSSMatrix)
                    curTransform = transformMatrix.m42;
                //Crazy IE10 Matrix
                else if (matrix.length==16)
                    curTransform = parseFloat( matrix[13] );
                //Normal Browsers
                else
                    curTransform = parseFloat( matrix[5] );
            }
        }
        else {
            if (axis=='x') curTransform = parseFloat(el.style.left,10) || 0;
            if (axis=='y') curTransform = parseFloat(el.style.top,10) || 0;
        }
        return curTransform || 0;
    },

    setWrapperTranslate : function (x, y, z) {
        var es = this.wrapper.style,
        	coords = {x: 0, y: 0, z: 0},
        	translate;

        // passed all coordinates
        if (arguments.length == 3) {
			coords.x = x;
			coords.y = y;
			coords.z = z;
        }
        
        // passed one coordinate and optional axis
        else {
        	if (typeof y == 'undefined') {
				y = this.params.mode == 'horizontal' ? 'x' : 'y';
        	}
        	coords[y] = x;
        }

        if (this.support.transforms && this.params.useCSS3Transforms) {
        	translate = this.support.transforms3d ? 'translate3d(' + coords.x + 'px, ' + coords.y + 'px, ' + coords.z + 'px)' : 'translate(' + coords.x + 'px, ' + coords.y + 'px)';
            es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = translate;
        }
        else {
            es.left = coords.x + 'px';
            es.top  = coords.y + 'px';
        }
        this.callPlugins('onSetWrapperTransform', coords);
        if (this.params.onSetWrapperTransform) this.fireCallback(this.params.onSetWrapperTransform, this, coords);
    },

    setWrapperTransition : function (duration) {
        var es = this.wrapper.style;
        es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = (duration / 1000) + 's';
        this.callPlugins('onSetWrapperTransition', {duration: duration});
        if (this.params.onSetWrapperTransition) this.fireCallback(this.params.onSetWrapperTransition, this, duration);
    },

    /*==================================================
        Helpers
    ====================================================*/
    h : {
        getWidth: function (el, outer) {
            var width = window.getComputedStyle(el, null).getPropertyValue('width')
            var returnWidth = parseFloat(width);
            //IE Fixes
            if(isNaN(returnWidth) || width.indexOf('%')>0) {
                returnWidth = el.offsetWidth - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-left')) - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-right'));
            }
            if (outer) returnWidth += parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-left')) + parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-right'))

            return returnWidth;
        },
        getHeight: function(el, outer) {
            if (outer) return el.offsetHeight;

            var height = window.getComputedStyle(el, null).getPropertyValue('height')
            var returnHeight = parseFloat(height);
            //IE Fixes
            if(isNaN(returnHeight) || height.indexOf('%')>0) {
                returnHeight = el.offsetHeight - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-top')) - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-bottom'));
            }
            if (outer) returnHeight += parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-top')) + parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-bottom'))
            return returnHeight;
        },
        getOffset: function(el) {
            var box = el.getBoundingClientRect();
            var body = document.body;
            var clientTop  = el.clientTop  || body.clientTop  || 0;
            var clientLeft = el.clientLeft || body.clientLeft || 0;
            var scrollTop  = window.pageYOffset || el.scrollTop;
            var scrollLeft = window.pageXOffset || el.scrollLeft;
            if (document.documentElement && !window.pageYOffset) {
                //IE7-8
                scrollTop  = document.documentElement.scrollTop;
                scrollLeft = document.documentElement.scrollLeft;
            }
            return {
                top: box.top  + scrollTop  - clientTop,
                left: box.left + scrollLeft - clientLeft
            };
        },
        windowWidth : function() {
            if (window.innerWidth) return window.innerWidth
            else if (document.documentElement && document.documentElement.clientWidth) return document.documentElement.clientWidth;
        },
        windowHeight : function() {
            if (window.innerHeight) return window.innerHeight
            else if (document.documentElement && document.documentElement.clientHeight) return document.documentElement.clientHeight;
        },
        windowScroll : function() {
            var left=0, top=0;
            if (typeof pageYOffset != 'undefined') {
                return {
                    left: window.pageXOffset,
                    top: window.pageYOffset
                }
            }
            else if (document.documentElement) {
                return {
                    left: document.documentElement.scrollLeft,
                    top: document.documentElement.scrollTop
                }
            }
        },

        addEventListener : function (el, event, listener, useCapture) {
        	if (typeof useCapture == 'undefined') {
				useCapture = false;
        	}
        	
            if (el.addEventListener) {
                el.addEventListener(event, listener, useCapture);
            }
            else if (el.attachEvent) {
                el.attachEvent('on' + event, listener);
            }
        },
        
        removeEventListener : function (el, event, listener, useCapture) {
        	if (typeof useCapture == 'undefined') {
				useCapture = false;
        	}
        	
            if (el.removeEventListener) {
                el.removeEventListener(event, listener, useCapture);
            }
            else if (el.detachEvent) {
                el.detachEvent('on' + event, listener);
            }
        }
    },
    setTransform : function (el, transform) {
        var es = el.style
        es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = transform
    },
    setTranslate : function (el, translate) {
        var es = el.style
        var pos = {
            x : translate.x || 0,
            y : translate.y || 0,
            z : translate.z || 0
        };
        var transformString = this.support.transforms3d ? 'translate3d('+(pos.x)+'px,'+(pos.y)+'px,'+(pos.z)+'px)' : 'translate('+(pos.x)+'px,'+(pos.y)+'px)';
        es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = transformString;
        if (!this.support.transforms) {
            es.left = pos.x+'px'
            es.top = pos.y+'px'
        }
    },
    setTransition : function (el, duration) {
        var es = el.style
        es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = duration+'ms';
    },
    /*==================================================
        Feature Detection
    ====================================================*/
    support: {

        touch : (window.Modernizr && Modernizr.touch===true) || (function() {
            return !!(("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch);
        })(),

        transforms3d : (window.Modernizr && Modernizr.csstransforms3d===true) || (function() {
            var div = document.createElement('div').style;
            return ("webkitPerspective" in div || "MozPerspective" in div || "OPerspective" in div || "MsPerspective" in div || "perspective" in div);
        })(),

        transforms : (window.Modernizr && Modernizr.csstransforms===true) || (function(){
            var div = document.createElement('div').style;
            return ('transform' in div || 'WebkitTransform' in div || 'MozTransform' in div || 'msTransform' in div || 'MsTransform' in div || 'OTransform' in div);
        })(),

        transitions : (window.Modernizr && Modernizr.csstransitions===true) || (function(){
            var div = document.createElement('div').style;
            return ('transition' in div || 'WebkitTransition' in div || 'MozTransition' in div || 'msTransition' in div || 'MsTransition' in div || 'OTransition' in div);
        })()
    },

    browser : {

        ie8 : (function(){
            var rv = -1; // Return value assumes failure.
            if (navigator.appName == 'Microsoft Internet Explorer') {
                var ua = navigator.userAgent;
                var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                if (re.exec(ua) != null)
                    rv = parseFloat(RegExp.$1);
            }
            return rv != -1 && rv < 9;
        })(),

        ie10 : window.navigator.msPointerEnabled
    }
}

/*=========================
  jQuery & Zepto Plugins
  ===========================*/
if (window.jQuery||window.Zepto) {
    (function($){
        if(!jQuery().swiper) {
            $.fn.swiper = function(params) {
                var s = new Swiper($(this)[0], params)
                $(this).data('swiper',s);
                return s;
            }
        }
    })(window.jQuery||window.Zepto)
}

// component
if ( typeof( module ) !== 'undefined' )
{
    module.exports = Swiper;
}





}




/*
 * Swiper Smooth Progress 1.1.0
 * Smooth progress plugin for Swiper
 *
 * http://www.idangero.us/sliders/swiper/plugins/progress.php
 *
 * Copyright 2010-2014, Vladimir Kharlampidi
 * The iDangero.us
 * http://www.idangero.us/
 *
 * Licensed under GPL & MIT
 *
 * Released on: January 29, 2014
*/
Swiper.prototype.plugins.progress=function(a){function b(){for(var b=0;b<a.slides.length;b++){var c=a.slides[b];c.progressSlideSize=e?a.h.getWidth(c):a.h.getHeight(c),c.progressSlideOffset="offsetLeft"in c?e?c.offsetLeft:c.offsetTop:e?c.getOffset().left-a.h.getOffset(a.container).left:c.getOffset().top-a.h.getOffset(a.container).top}d=e?a.h.getWidth(a.wrapper)+a.wrapperLeft+a.wrapperRight-a.width:a.h.getHeight(a.wrapper)+a.wrapperTop+a.wrapperBottom-a.height}function c(b){var c,b=b||{x:0,y:0,z:0};c=1==a.params.centeredSlides?e?-b.x+a.width/2:-b.y+a.height/2:e?-b.x:-b.y;for(var f=0;f<a.slides.length;f++){var g=a.slides[f],h=1==a.params.centeredSlides?g.progressSlideSize/2:0,i=(c-g.progressSlideOffset-h)/g.progressSlideSize;g.progress=i}a.progress=e?-b.x/d:-b.y/d,a.params.onProgressChange&&a.fireCallback(a.params.onProgressChange,a)}var d,e="horizontal"==a.params.mode,f={onFirstInit:function(){b(),c({x:a.getWrapperTranslate("x"),y:a.getWrapperTranslate("y")})},onInit:function(){b()},onSetWrapperTransform:function(a){c(a)}};return f};




 
 
 
jQuery(document).ready(function($){ 

    Array.prototype.getKeyByValue = function( value ) {
        for( var prop in this ) {
            if( this.hasOwnProperty( prop ) ) {
                 if( this[ prop ] === value )
                     return prop;
            }
        }
    }

	var doneVideoInit = false;
	var $captionTrans = 0;
	var $loading_bg_storage = $('.first-section .nectar-slider-loading').css('background-image');
    
    function transparentheaderLoadingCalcs() {
    	//handle slider loading pos when trasparent header is being used
    	if($('body #header-outer[data-transparent-header="true"]').length > 0) {

    		if($('#page-header-bg').length == 0) {

    			$('.nectar-slider-wrap.first-section .swiper-container .swiper-wrapper .swiper-slide').addClass('not-loaded');
    			
    			if($('.container-wrap .main-content > .row > div').find('.nectar-slider-wrap.first-section').length > 0) {
    				$('.container-wrap .main-content > .row > div').first().css('padding-top','0');
    			}
    			

    			
                if($('#header-outer[data-remove-border="true"]').length == 0) {
        			//if not using flexible/fullscreen height - calculate the loading / bg pos early (if using the option calculates later)
        			if($('.first-section .nectar-slider-wrap[data-flexible-height="true"]').length == 0 && $('.first-section .nectar-slider-wrap[data-fullscreen="true"]').length == 0) {
        				$('.first-section .nectar-slider-loading').css({
        					'top': $('#header-space').height(),
        					'background-position' : 'center ' + ((($('.first-section .swiper-container').height()/2) + 15) - $('#header-space').height()) +'px'
        				});

                        $('.first-section .nectar-slider-wrap .nectar-slider-loading .loading-icon').css({
                            'opacity' : '1',
                            'height' :  $('.first-section .swiper-container').height() - $('#header-space').height() + 'px'
                        });
        			} else {
        				var $loading_bg_storage = $('.first-section .nectar-slider-loading').css('background-image');
        				
        				$('.first-section .nectar-slider-loading').css({
        					'top': $('#header-space').height(),
        					'background-image': 'none'
        				});
        			}
                }
    			
    		}
    	} 
    }
    transparentheaderLoadingCalcs();
	
	//move parallax sliders to correct dom location  
	var parallaxSlider = $('.parallax_slider_outer.first-section');

    //ajaxify
    $(window).on("pronto.request", function(){ 

        //remove window resize events
        $(window).off('resize.nsSliderContent');
        $(window).off('resize.dynamicHeights');
        $(window).off('resize.nsHeightUpdate');

        //clear all autoplay intervals
        $('.nectar-slider-wrap').each(function(){
            if($(this).attr('data-autorotate') && $(this).attr('data-autorotate').length > 1) {

                var $autoplayVal = $('#'+$(this).attr('id')).attr('data-autorotate');
                if(typeof $autoplayVal !='undefined' && $autoplayVal.length != '0' && parseInt($autoplayVal)) {
                    clearInterval(autoplay[$('#'+$(this).attr('id')).attr('autoplay-id')].interval); 
                } 

            }
        });
        
    });

    $(window).on("pronto.render vc_reload", function(){ 

        if($('.nectar-slider-wrap').length > 0){

            $(window).on('resize.nsSliderContent', nsSliderContentResize);

            parallaxSlider = $('.parallax_slider_outer.first-section'); 
            $(window).unbind('resize.nsHeightUpdate');


            transparentheaderLoadingCalcs();

            ////remove parallax if using page header on projects/posts
            //if($('body.single #page-header-bg').length > 0) $('.parallax_slider_outer').removeClass('parallax_slider_outer').addClass('no-left-margin');
            //parallaxSliderPos();

            nectarSliderMainInit();
            setTimeout(function(){ 
                initialSlideLoad();
                dynamicHeightSliders();
                $(window).on('resize.dynamicHeights', dynamicHeightSliders);
                sliderSize();
                resizeToCover();
                parallaxCheck();
                sliderbuttonHoverEffect();
            },150);


        }

    }); //pronto render

	function parallaxSliderPos(){
		if(parallaxSlider.parent().attr('class') == 'wpb_wrapper') { 

            //give the slider the custom row ID if assigned
            if(parallaxSlider.parents('.wpb_row').length > 0) {
                if(parallaxSlider.parents('.wpb_row').not('[id^="fws_"]').length > 0) parallaxSlider.attr('id',parallaxSlider.parents('.wpb_row').attr('id'));
            } 
            //remove the row
            parallaxSlider.parents('.wpb_row').remove(); 
        }
        //alter the DOM pos
		parallaxSlider.insertBefore('.container-wrap');	
        
	}
	
	////remove parallax if using page header on projects/posts
	//if($('body.single #page-header-bg').length > 0) $('.parallax_slider_outer').removeClass('parallax_slider_outer').addClass('no-left-margin');
	
	//parallaxSliderPos();
	
  var $onMobileBrowser = navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/);
  
	//initial cals
	var $smoothSrollWidth = 0; 
	var nectarSliderKenBurns;
	
	/*=========================
     Begin Nectar Slider Main Construction
     ===========================*/
	var $nectarSliders = [];
	
    function nectarSliderMainInit() { 

        $nectarSliders = [];

        //remove boxed full width attr when not needed
        $('.nectar-slider-wrap[data-full-width="boxed-full-width"]').each(function(){ if($(this).parents('.wpb_column').length > 0 && $(this).parents('.full-width-content').length > 0) $(this).attr('data-full-width','false') });
        var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && window.innerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
        var $windowWidth = $(window).width() - $leftHeaderSize;

        if($('body > #boxed').length == 0 && $('.nectar-slider-wrap[data-full-width="true"]').parent().attr('id') != 'portfolio-extra' && $('.nectar-slider-wrap[data-full-width="true"]').parents('.post-area:not(".span_12")').length == 0){ 
            $('.nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .nectar-slider-wrap').css({'left': -( ($windowWidth-$smoothSrollWidth)/2 - $('.main-content').width()/2), 'margin-left': '0' });
            $('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css('width',$windowWidth);
        } 
        else if( $('.nectar-slider-wrap[data-full-width="true"]').parent().attr('id') == 'portfolio-extra' && $('#full_width_portfolio').length != 0){  
            $('.nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .nectar-slider-wrap').css({'left': -( ($windowWidth-$smoothSrollWidth)/2 - $('.main-content').width()/2), 'margin-left': '0' });
            $('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css('width',$windowWidth);
        }
        else { 
            var $container = ($('body > #boxed').length == 0) ? '.post-area' : '.container-wrap';
            //backup incase only slider is used with nothing else in boxed mode
            if($($container).width() == '0' && $('body > #boxed').length > 0) $container = '#boxed';
            $('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css('width',$($container).width());
        }
        
        //show slider once full width calcs have taken place
        $('.nectar-slider-wrap, .slide-bg-overlay').show();
        
        //set bg colors / textures after js has made the slider fullwidth
        $('.swiper-container, .swiper-slide').css('background-color','#000');
        $('.video-texture').css('opacity','1');



    	$('.nectar-slider-wrap').each(function(i){
    		var $arrows = $(this).find('.swiper-container').attr('data-arrows');
    		var $bullets = $(this).find('.swiper-container').attr('data-bullets');
    		var $swipe = $(this).find('.swiper-container').attr('data-desktop-swipe');
    		var $loop = $(this).find('.swiper-container').attr('data-loop');
    		
    		//swipe  
    		if($swipe == 'true' && $('#'+$(this).attr('id') +' .swiper-wrapper > div').length > 1 && $(this).attr('data-overall_style') != 'directional' ){
    			var $grab = 1;
    			var $desktopSwipe = 1;
    		} else {
    			var $grab = 0;
    			var $desktopSwipe = 0;
    		}

            ////set swipe as true for mobile
            if($('body').hasClass('mobile') && $(this).attr('data-overall_style') != 'directional') $desktopSwipe = 1;

    		//bullets
    		if($bullets == 'true' && $(this).find('.swiper-wrapper > div').length > 1 && $(this).attr('data-overall_style') != 'directional' ){
    			$bullets = '#'+$(this).attr('id')+' .slider-pagination';
    		} else {
    			$bullets = null;
    		}

            //loop
            $useLoop = ($loop == 'true' && $(this).find('.swiper-wrapper > div').length > 1 && !$('html').hasClass('no-video') || $(this).attr('data-overall_style') == 'directional' && $(this).find('.swiper-wrapper > div').length > 1 && !$('html').hasClass('no-video')) ? true : false;
            if($useLoop == false) $(this).find('.swiper-container').attr('data-loop','false');

            //obj config

            if($(this).attr('data-transition') == 'fade' && $(this).attr('data-overall_style') != 'directional' && $(this).attr('data-button-styling') != 'btn_with_preview'){
                            
                   var progressVar = true;
                   var touchRatio = 1.3;

                    $sliderOptions = {
                        loop: $useLoop,
                        grabCursor: $grab,
                        touchRatio: touchRatio,
                        speed: 600,
                        pagination : $bullets,
                        simulateTouch: $desktopSwipe,
                        
                        onSlideChangeEnd: nectarSlideRotateReset,
                        onTouchEnd: nectarSlideRotateResetTouch,
                        
                        //onTouchEnd: captionTransition,
                        onSlideChangeStart: onChangeStart,
                        onTouchMove: clearAutoplay,
                        onFirstInit: nectarInit,
                        progress: progressVar,
                        onProgressChange: function(swiper){
                           
                            if($(swiper.container).parents('.nectar-slider-wrap').hasClass('loaded')) {
                                for (var i = 0; i < swiper.slides.length; i++){
                                  var slide = swiper.slides[i];
                                  var progress = slide.progress;
                                  var translate = progress*swiper.width;  

                                  var opacity = Math.max(1 - Math.abs(progress), 0);
                                  slide.style.opacity = opacity;
                                  swiper.setTransform(slide,'translate3d('+translate+'px,0,0)');
                                }
                            }
                        },
                        onTouchStart: function(swiper){
                            for (var i = 0; i < swiper.slides.length; i++){
                              swiper.setTransition(swiper.slides[i], 0);
                            }
                        },
                        onSetWrapperTransition:  function(swiper, speed) {
                            for (var i = 0; i < swiper.slides.length; i++){
                              swiper.setTransition(swiper.slides[i], speed);
                            }
                        }
                    }
                 

            } else {

               var touchRatio = 0.6;
               var css3Trans = ($('#'+$(this).attr('id') + '.nectar-slider-wrap[data-overall_style="directional"]').length == 1) ? false: true;
                $sliderOptions = {
                    loop: $useLoop,
                    grabCursor: $grab,
                    touchRatio: touchRatio,
                    speed: 500,
                    useCSS3Transforms: css3Trans,
                    pagination : $bullets,
                    simulateTouch: $desktopSwipe,
                    
                    onSlideChangeEnd: nectarSlideRotateReset,
                    onTouchEnd: nectarSlideRotateResetTouch,
                    
                    onSlideChangeStart: onChangeStart,
                    onTouchMove: clearAutoplay,
                    onFirstInit: nectarInit
                        
                }
            }

    		
    		$nectarSliders[i] = new Swiper('#'+$(this).attr('id')+' .swiper-container', $sliderOptions);
    		$nectarSliders[i].swipeReset();
        
        $('#'+$(this).attr('id')).addClass('nectar-slider-enabled');

    		 
    		 
    		 //webkit looped slider first video fix
    		 if(navigator.userAgent.indexOf('Chrome') > 0 && !/Edge\/12./i.test(navigator.userAgent) && !/Edge\/\d./i.test(navigator.userAgent)) { 
    		 	 if( jQuery(this).find('.swiper-slide:nth-child(2) video source[type="video/webm"]').length > 0 && jQuery(this).find('.swiper-container').attr('data-loop') == 'true') {

    			  	var webmSource = jQuery(this).find('.swiper-slide:nth-child(2) video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
    	          	var firstVideo = jQuery(this).find('.swiper-slide:nth-child(2) video').get(0);
    	          	firstVideo.src = webmSource;
    	          	firstVideo.load();
    	          	
    	          	var webmSource2 = jQuery(this).find('.swiper-slide:last-child video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
    	          	var lastVideo = jQuery(this).find('.swiper-slide:last-child video').get(0);
    	          	lastVideo.src = webmSource2;
    	          	lastVideo.load();
    	         }

                 if( jQuery(this).find('.swiper-slide:eq(-2) video source[type="video/webm"]').length > 0 && jQuery(this).find('.swiper-container').attr('data-loop') == 'true') {

                    var webmSource = jQuery(this).find('.swiper-slide:eq(-2) video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
                    var firstVideo = jQuery(this).find('.swiper-slide:eq(-2) video').get(0);
                    firstVideo.src = webmSource;
                    firstVideo.load();
                    
                    var webmSource2 = jQuery(this).find('.swiper-slide:nth-child(1) video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
                    var lastVideo = jQuery(this).find('.swiper-slide:nth-child(1) video').get(0);
                    lastVideo.src = webmSource2;
                    lastVideo.load();
                 }
    		 }

         
        //autorotation visualized style
        if($('#'+$(this).attr('id') + ' [data-bullet_style="see_through_ar_visualized"]').length == 1) {
          $('#'+$(this).attr('id')).find('.slider-pagination .swiper-pagination-switch').append('<svg width="65px" height="65px" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg"><circle stroke-width="9" fill="none" stroke-linecap="round" cx="33" cy="33" r="28"></circle> <circle class="time" stroke-width="9" fill="none" stroke-linecap="round" cx="33" cy="33" r="28"></circle></svg>');
        } 
     
    		//Navigation arrows

        ////default arrows
    		if($arrows == 'true' && $('#'+$(this).attr('id') +' .swiper-wrapper > div').length > 1 && $('#'+$(this).attr('id') + '.nectar-slider-wrap[data-button-styling="btn_with_preview"]').length == 0 && $('#'+$(this).attr('id') + '.nectar-slider-wrap[data-overall_style="directional"]').length == 0 ){
    			
    			$('.slide-count i').transition({ scale: 0.5, opacity: 0 });
    			
    			//hover event
    			$('body').on('mouseenter','.nectar-slider-wrap[data-button-styling="btn_with_count"][data-overall_style="classic"]  .swiper-container .slider-prev, .nectar-slider-wrap[data-button-styling="btn_with_count"] .swiper-container .slider-next',function(){
    				
    				$(this).find('.slide-count i').clearQueue().stop(true,true).delay(110).transition({ scale: 1, opacity: 1 },200);
    				
    				$(this).stop(true,true).animate({
    					'width' : '100px'
    				},300,'easeOutCubic');
    				$(this).find('.slide-count span').clearQueue().stop().delay(100).animate({
    					'opacity' : '1'
    				},225,'easeOutCubic');
    			});

          $('body').on('mouseleave','.nectar-slider-wrap[data-button-styling="btn_with_count"][data-overall_style="classic"] .swiper-container .slider-prev, .nectar-slider-wrap[data-button-styling="btn_with_count"] .swiper-container .slider-next',function(){
              
              $('.slide-count i').stop(true,true).transition({ scale: 0, opacity: 0 },200);
              
              $(this).stop().delay(150).animate({
                  'width' : '50px'
              },300,'easeOutCubic');
              $(this).find('.slide-count span').stop(true,true).animate({
                  'opacity' : '0'
              },200,'easeOutCubic');
          });
    			
    			//add slide counts
    			var $slideCount = ($(this).find('.swiper-container').attr('data-loop') != 'true' ) ? $('#'+$(this).attr('id') + ' .swiper-wrapper > div').length :  $('#'+$(this).attr('id') + ' .swiper-wrapper > div').length - 2;
    			
    			//ie8
    			if($('html').hasClass('no-video')) $slideCount = $('#'+$(this).attr('id') + ' .swiper-wrapper > div').length;
    			
    			$('#'+$(this).attr('id')+' .slider-prev .slide-count .slide-total').html( $slideCount );
    			$('#'+$(this).attr('id')+' .slider-next .slide-count .slide-total').html( $slideCount );
    			
    			//prev

                function prevArrowAnimation(e) {
                                
                   if($(this).hasClass('inactive')) return false;
                   
                   var $that = $(this); 
                   
                   //non looped slider
                   if($(this).parents('.swiper-container').attr('data-loop') != 'true') {
                        
                      
                       if( $(this).parents('.swiper-container').find('.swiper-slide-active').index()+1 == 1 && !$('html').hasClass('no-video')){
                            
                            //make sure the animation is complete
                             var $timeout;
                           
                             clearTimeout($timeout);
                             $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,700);

                             if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') != 'fade') {

                                 var $that = $(this);

                                 $(this).parents('.swiper-container').animate({
                                    'left' : 25+'px'
                                 },250,function(){
                                    //$(this).parents('.swiper-container').find('.swiper-wrapper').stop(true,false).css('transition','left,top');
                                    //$nectarSliders[i].swipeReset();
                                    $that.parents('.swiper-container').stop().animate({
                                        'left' : 0+'px'
                                     },250);
                                 });
                             }

                             $(this).addClass('inactive');
                            
                       }    
                     
                   }

                   e.preventDefault();
                   $nectarSliders[i].swipePrev();

                    if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') == 'fade') {
                         var $timeout;
                           
                         clearTimeout($timeout);
                         $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,800);

                         $(this).addClass('inactive');
                    }
                }

                $('body').off('click','#'+$(this).attr('id')+' .slider-prev',prevArrowAnimation);
                $('body').on('click','#'+$(this).attr('id')+' .slider-prev',prevArrowAnimation);


                function nextArrowAnimation(e) {
                   
                   if($(this).hasClass('inactive')) return false;
                   
                   var $that = $(this);     
                   var $slideNum = $(this).parents('.swiper-container').find('.swiper-wrapper > div').length;
                    
                    //non looped slider
                   if($(this).parents('.swiper-container').attr('data-loop') != 'true') {
                    
                         if( $(this).parents('.swiper-container').find('.swiper-slide-active').index()+1 == $slideNum && !$('html').hasClass('no-video')) {

                            //make sure the animation is complete
                            var $timeout;
                               
                            clearTimeout($timeout);
                            $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,700);

                            if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') != 'fade') {
                                  var $that = $(this);

                                 $(this).parents('.swiper-container').animate({
                                    'left' : -25+'px'
                                 },250,function(){
                                    $that.parents('.swiper-container').stop().animate({
                                        'left' : 0+'px'
                                     },250);
                                 });
                             }
                             $(this).addClass('inactive');
                        }
                   }
                    
                        
                   e.preventDefault();
                   $nectarSliders[i].swipeNext();

                    if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') == 'fade') {
                         var $timeout;
                           
                         clearTimeout($timeout);
                         $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,800);

                         $(this).addClass('inactive');
                    }
                   
                }

    			//next
                $('body').off('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimation);
    			$('body').on('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimation);



    		}
            ////button with preview on hover
            else if($arrows == 'true' && $('#'+$(this).attr('id') +' .swiper-wrapper > div').length > 1 && $('#'+$(this).attr('id') + '.nectar-slider-wrap[data-button-styling="btn_with_preview"]').length == 1 && $('#'+$(this).attr('id') + '.nectar-slider-wrap[data-overall_style="directional"]').length == 0){


                function prevArrowAnimationWithPreview(e) {
                    if($(this).hasClass('inactive')) return false;
                    var $that = $(this);     

                    //make sure the animation is complete
                    var $timeout;
                       
                    clearTimeout($timeout);
                    $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,1000);

                    var $timeout2;
                       
                    clearTimeout($timeout2);
                    $timeout2 = setTimeout(function(){ $that.removeClass('stophover'); } ,500);

                    $(this).addClass('inactive').addClass('stophover');

                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').prev('.swiper-slide').removeClass('prev-high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().removeClass('prev-move');

                    e.preventDefault();
                    $nectarSliders[i].swipePrev();
                }

                function nextArrowAnimationWithPreview(e) {
                    if($(this).hasClass('inactive')) return false;
                    var $that = $(this);     

                    //make sure the animation is complete
                    var $timeout;
                       
                    clearTimeout($timeout);
                    $timeout = setTimeout(function(){ $that.removeClass('inactive'); } ,1000);

                    var $timeout2;
                       
                    clearTimeout($timeout2);
                    $timeout2 = setTimeout(function(){ $that.removeClass('stophover'); } ,500);

                    $(this).addClass('inactive').addClass('stophover');

                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').next('.swiper-slide').removeClass('high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().removeClass('next-move');

                    e.preventDefault();
                    $nectarSliders[i].swipeNext();
                }

                function darkSlideNextMouseOver(currentSlider, activeIndex) { 
                    var $indexAdd = ($(currentSlider).find('.swiper-container').attr('data-loop') == 'true') ? 2 : 2;
                 
                    if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + 1) +')').attr('data-color-scheme') == 'dark') {
                        if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +')').attr('data-color-scheme') == 'dark') {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +') .video-texture').addClass('half-light-overlay').addClass('no-trans');
                        }
                        else {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +') .video-texture').addClass('light-overlay').addClass('no-trans');
                        }
                    } else {
                        if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +')').attr('data-color-scheme') == 'light') {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +') .video-texture').addClass('half-dark-overlay').addClass('no-trans');
                        } else {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + $indexAdd) +') .video-texture').addClass('dark-overlay').addClass('no-trans');
                        }
                    }
                }
                function darkSlidePrevMouseOver(currentSlider, activeIndex) { 
                    var $indexAdd = ($(currentSlider).find('.swiper-container').attr('data-loop') == 'true') ? 0 : 0;
                 
                    if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex + 1) +')').attr('data-color-scheme') == 'dark') {
                        if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +')').attr('data-color-scheme') == 'dark') {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +') .video-texture').addClass('half-light-overlay').addClass('no-trans');
                        } else {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +') .video-texture').addClass('light-overlay').addClass('no-trans');
                        }
                    } else {
                        if($(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +')').attr('data-color-scheme') == 'light') {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +') .video-texture').addClass('half-dark-overlay').addClass('no-trans');
                        } else {
                            $(currentSlider).find('.swiper-slide:nth-child('+ (activeIndex - $indexAdd) +') .video-texture').addClass('dark-overlay').addClass('no-trans');
                        }
                    }
                }


                $('body').off('click','#'+$(this).attr('id')+' .slider-prev',prevArrowAnimationWithPreview);
                $('body').on('click','#'+$(this).attr('id')+' .slider-prev',prevArrowAnimationWithPreview);

                //next
                $('body').off('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimationWithPreview);
                $('body').on('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimationWithPreview);

                //hover event
                $('body').on('mouseenter','.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-next:not(.stophover)',function(){
                    
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').next('.swiper-slide').addClass('high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().addClass('next-move');
                    $(this).stop().addClass('next-arrow-move');
                    darkSlideNextMouseOver($(this).parents('.nectar-slider-wrap'), $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').index());
                   
                });

                $('body').on('mouseleave','.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-next',function(){
                    
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').next('.swiper-slide').removeClass('high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().removeClass('next-move');
                    $(this).stop().removeClass('next-arrow-move');
                });


                $('body').on('mouseenter','.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-prev:not(.stophover)',function(){
                    
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').prev('.swiper-slide').addClass('prev-high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().addClass('prev-move');
                    $(this).stop().addClass('prev-arrow-move');
                    darkSlidePrevMouseOver($(this).parents('.nectar-slider-wrap'), $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').index());
                });

                $('body').on('mouseleave','.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-prev',function(){
                    
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').prev('.swiper-slide').removeClass('prev-high-z-index');
                    $(this).parents('.nectar-slider-wrap').find('.swiper-slide-active').stop().removeClass('prev-move');
                    $(this).stop().removeClass('prev-arrow-move');

                });

            } else if($('#'+$(this).attr('id') + '.nectar-slider-wrap[data-overall_style="directional"]').length == 1) {

                function prevArrowAnimationDirectional(e) {
                    if($(this).hasClass('inactive') || $(this).parents('.swiper-container').find('.slider-next').hasClass('inactive')) return false;
                    var $that = $(this);     

                    //cache the widths
                    if($('#header-outer.transparent').length > 0){
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                           var $extraWidth = ($(this).find('.original .sf-sub-indicator').length > 0) ? 14: 1;
                           $(this).find('.original').attr('data-w',$(this).find('span.original').width()+$extraWidth); 
                        });
                    }

                   //make sure the animation is complete
                    var $timeout;
                       
                    clearTimeout($timeout);
                    $timeout = setTimeout(function(){ 
                        
                        $that.parents('.swiper-container').removeClass('directional-trans-prev');
                        $that.parents('.swiper-container').find('.swiper-wrapper').transition({ 'x': 0, 'left': parseInt($that.parents('.swiper-container').find('.swiper-wrapper').css('left')) + $that.parents('.swiper-container').width() },0);
                        setTimeout(function(){ 
                            $nectarSliders[i].updateActiveSlide();
                            $nectarSliders[i].fixLoop();
                            $that.removeClass('inactive');
                        },50);

                        resetContentPos();
                         
                    } ,1100);

                    //call the dark/light slide coloring half way through transition
                    var $timeout2;
                       
                    clearTimeout($timeout2);
                    $timeout2 = setTimeout(function(){ 

                        if($that.parents('.swiper-container').attr('data-loop') != 'true') {
                            if( $that.parents('.swiper-container').find('.swiper-slide-active').index()+1 != 1) {
                               onChangeStart($nectarSliders[i]); 
                            }
                        }
                          
                        else {
                            onChangeStart($nectarSliders[i]); 
                        }

                    } ,100);

                     //non looped slider
                   if($(this).parents('.swiper-container').attr('data-loop') != 'true') {
                        
                      
                      /* if( $(this).parents('.swiper-container').find('.swiper-slide-active').index()+1 == 1 && !$('html').hasClass('no-video')){

                             if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') != 'fade') {

                                 $(this).parents('.swiper-container').find('.swiper-wrapper').stop(true,false).css('transition','none').animate({
                                    'left' : parseInt($(this).parents('.swiper-container').find('.swiper-wrapper').css('left')) + 20
                                 },200,function(){
                                    $(this).parents('.swiper-container').find('.swiper-wrapper').stop(true,false).css('transition','left,top');
                                    $nectarSliders[i].swipeReset();
                                 });
                             } 
                       }  else { */

                          $(this).parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active .content').children().each(function(i){
                                $(this).stop().delay(i*50).transition({
                                    'x': $that.parents('.swiper-container').width()/2.5 + "px"
                                }, 950,'easeInOutQuart'); 
                          });
                       //} 
                     
                   }

                    $(this).addClass('inactive');

                     //move the slide with eaising
                    $(this).parents('.swiper-container').find('.swiper-wrapper').transition({ 'x': $(this).parents('.swiper-container').width() + "px"},1100,'easeInOutQuart');

                    if($(this).parents('.swiper-container').attr('data-loop') == 'true') {
                        $(this).parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active .content > *').each(function(i){
                                $(this).stop().delay(i*50).transition({
                                    'x': $that.parents('.swiper-container').width()/2.5 + "px"
                                }, 950,'easeInOutQuart'); 
                        });
                    }
                    
                    //kenburns
                    
                    if($that.parents('.nectar-slider-wrap').is('[data-bg-animation="ken_burns"]')) {
                        
                        $that.parents('.swiper-container').find('.swiper-slide').attr('data-ken-burns','active');
                        
                        clearTimeout(nectarSliderKenBurns);
                        nectarSliderKenBurns = setTimeout(function(){
                          	$that.parents('.swiper-container').find('.swiper-slide:not(".swiper-slide-active")').attr('data-ken-burns','inactive');
                        },1250);

                    }
                    

                    $that.parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active').prev('.swiper-slide').find('.content > *').transition({
                        'x': '-' + $that.parents('.swiper-container').width() + "px"
                    }, 0);

                    setTimeout(function(){
                         $that.parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active').prev('.swiper-slide').find('.content > *').each(function(i){

                            $(this).stop().delay(i*50).transition({
                                'x': "0px"
                            }, 750,'easeInOutQuart'); 
                        });
                    },200);

                    $that.parents('.swiper-container').addClass('directional-trans-prev');

                    e.preventDefault();
         
                }

                function nextArrowAnimationDirectional(e) {
                    if($(this).hasClass('inactive') || $(this).parents('.swiper-container').find('.slider-prev').hasClass('inactive')) return false;
                    var $that = $(this);     

                    //cache the widths
                    if($('#header-outer.transparent').length > 0){
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $extraWidth = ($(this).find('.original .sf-sub-indicator').length > 0) ? 14: 1;
                            $(this).find('.original').attr('data-w',$(this).find('span.original').width()+$extraWidth); 
                        });
                    }

                    //make sure the animation is complete
                    var $timeout;
                       
                    clearTimeout($timeout);
                    $timeout = setTimeout(function(){ 
                        
                        $that.parents('.swiper-container').removeClass('directional-trans-next');
                        $that.parents('.swiper-container').find('.swiper-wrapper').transition({ 'x': 0, 'left': parseInt($that.parents('.swiper-container').find('.swiper-wrapper').css('left')) - $that.parents('.swiper-container').width() },0);
                        setTimeout(function(){ 
                            $nectarSliders[i].updateActiveSlide();
                            $nectarSliders[i].fixLoop(); 
                            $that.removeClass('inactive');
                        },50);
                        resetContentPos();

                    } ,1100);

                    //call the dark/light slide coloring half way through transition
                    var $timeout2;
                       
                    clearTimeout($timeout2);
                    $timeout2 = setTimeout(function(){ 

                        if($that.parents('.swiper-container').attr('data-loop') != 'true') {
                            if( $that.parents('.swiper-container').find('.swiper-slide-active').index()+1 != $slideNum) {
                               onChangeStart($nectarSliders[i]); 
                            }
                        }
                          
                        else {
                            onChangeStart($nectarSliders[i]); 
                        }

                    } ,100);
                    


                     var $slideNum = $(this).parents('.swiper-container').find('.swiper-wrapper > div').length;
                    
                    //non looped slider
                       if($(this).parents('.swiper-container').attr('data-loop') != 'true') {
                        
                             if( $(this).parents('.swiper-container').find('.swiper-slide-active').index()+1 == $slideNum && !$('html').hasClass('no-video')) {

                                if( $(this).parents('.nectar-slider-wrap ').attr('data-transition') != 'fade') {
                                     $(this).parents('.swiper-container').find('.swiper-wrapper').stop(true,false).css('transition','none').animate({
                                        'left' : parseInt($(this).parents('.swiper-container').find('.swiper-wrapper').css('left')) - 20
                                     },200,function(){
                                        $(this).parents('.swiper-container').find('.swiper-wrapper').stop(true,false).css('transition','left,top');
                                        $nectarSliders[i].swipeReset();
                                     });
                                 }
                            } else {
                                 $(this).parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active .content > *').each(function(i){
                                        $(this).stop().delay(i*50).transition({
                                            'x': '-' + $that.parents('.swiper-container').width()/2.5 + "px"
                                        }, 950,'easeInOutQuart'); 
                                });
                            }
                       }

                       $(this).addClass('inactive');

                    
                    //move the slide with eaising
                    $(this).parents('.swiper-container').find('.swiper-wrapper').transition({ 'x': - $(this).parents('.swiper-container').width() + "px"},1100,'easeInOutQuart');

                    if($(this).parents('.swiper-container').attr('data-loop') == 'true') {
                        $(this).parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active .content > *').each(function(i){
                                $(this).stop().delay(i*50).transition({
                                    'x': '-' + $that.parents('.swiper-container').width()/2.5 + "px"
                                }, 950,'easeInOutQuart'); 
                        });
                    }

                    
                    //kenburns
                    if($that.parents('.nectar-slider-wrap').is('[data-bg-animation="ken_burns"]')) {
                        
                        $that.parents('.swiper-container').find('.swiper-slide').attr('data-ken-burns','active');
                          
                        clearTimeout(nectarSliderKenBurns);
                        nectarSliderKenBurns = setTimeout(function(){
                          	$that.parents('.swiper-container').find('.swiper-slide:not(".swiper-slide-active")').attr('data-ken-burns','inactive');  
                        },1250);

                    }
                    
                    
                    $that.parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active').next('.swiper-slide').find('.content > *').transition({
                        'x': $that.parents('.swiper-container').width() + "px"
                    }, 0);

                    setTimeout(function(){
                         $that.parents('.swiper-container').find('.swiper-wrapper').find('.swiper-slide-active').next('.swiper-slide').find('.content > *').each(function(i){

                            $(this).stop().delay(i*50).transition({
                                'x': "0px"
                            }, 750,'easeInOutQuart'); 
                        });
                    },200);
                   

                    $that.parents('.swiper-container').addClass('directional-trans-next');

                    e.preventDefault();
                    

                }


                //prev
                $('body').off('click','#'+$(this).attr('id')+' .slider-prev', prevArrowAnimationDirectional);
                $('body').on('click','#'+$(this).attr('id')+' .slider-prev', prevArrowAnimationDirectional);

                //next
                $('body').off('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimationDirectional);
                $('body').on('click','#'+$(this).attr('id')+' .slider-next',  nextArrowAnimationDirectional);

                function resetContentPos() {
                    $($nectarSliders[i].container).find('.swiper-slide .content > *').transition({ 'x': '0px' }, 0); 
                }

            }


            //scale pagination style
            if($('#'+$(this).attr('id') +' .swiper-container').attr('data-bullet_style') == 'scale' ){
                 $('#'+$(this).attr('id')+' .slider-pagination .swiper-pagination-switch').append('<i />');
            }
    	    //Clickable pagination
    	    if($bullets != null && $('#'+$(this).attr('id') +' .swiper-wrapper > div').length > 1 ){
    		    $('#'+$(this).attr('id')+' .slider-pagination .swiper-pagination-switch').click(function(){
    		        $nectarSliders[i].swipeTo($(this).index());
    		    });
    	    }
    	});
    	


	}

	nectarSliderMainInit();
	
	
	
    function darkFirstSlide(slider){
        if( slider.parents('.parallax_slider_outer').length > 0 && slider.parents('.first-section').length > 0 && slider.find('.swiper-slide-active[data-color-scheme="dark"]').length > 0 ) {
            $('#header-outer').addClass('dark-slide');
        } else if(slider.parents('.parallax_slider_outer').length > 0 || slider.parents('.first-section').length > 0 ) {
    
            if( slider.find('.swiper-slide-active[data-color-scheme="dark"]').length > 0 && slider.parents('.first-section').length > 0 && slider.is('[data-full-width="true"]') ) {
              //dont remove if needed
            } else {
                $('#header-outer').removeClass('dark-slide');
            }
        }
    }

    function directionalNavColorInit(slider) {
        
        if(slider.attr('data-overall_style') == 'directional' && slider.find('.swiper-slide[data-color-scheme="dark"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0) {

            if( slider.parents('.parallax_slider_outer').length > 0 || slider.parents('.first-section').length > 0 ) {

                $('#header-outer').addClass('directional-nav-effect');

                $selector = ($('#header-outer[data-permanent-transparent="1"]').length == 0) ? '#header-outer' : '#header-outer .midnightHeader.nectar-slider';

                $($selector+' nav > ul.sf-menu > li > a, '+$selector+' nav > ul.buttons > li.slide-out-widget-area-toggle a span, '+$selector+' nav > ul.buttons > li#search-btn a').each(function(){
                    var $text = $(this).html();
                    $(this).html(' ');
                    $(this).append('<span class="dark"><span>'+ $text + '</span></span>');
                    $(this).append('<span class="light"><span>'+ $text + '</span></span>');
                    $(this).append('<span class="original"><span>'+ $text + '</span></span>');
                });

                $($selector+' .cart-outer .cart-icon-wrap').each(function(){
                    $(this).find('> i').remove();
                    $(this).append('<span class="dark"><span><i class="icon-salient-cart"></i></span></span>');
                    $(this).append('<span class="light"><span><i class="icon-salient-cart"></i></span></span>');
                    $(this).append('<span class="original"><span><i class="icon-salient-cart"></i></span></span>');
                });
                setTimeout(function(){
                     $($selector+' .cart-outer .cart-icon-wrap').each(function(){
                        $(this).find('> i, > span').remove();
                        $(this).append('<span class="dark"><span><i class="icon-salient-cart"></i></span></span>');
                        $(this).append('<span class="light"><span><i class="icon-salient-cart"></i></span></span>');
                        $(this).append('<span class="original"><span><i class="icon-salient-cart"></i></span></span>');
                    });
                },1000);

                //logo
                if($($selector+' header#top #logo img').length > 0) {
                    $($selector+' header#top #logo').append('<span class="dark"><span></span></span>');
                    $($selector+' header#top #logo').append('<span class="light"><span></span></span>');
                    $($selector+ ' header#top #logo').append('<span class="original"><span></span></span>');
                    $($selector+ ' header#top #logo img:first').clone().appendTo($($selector+ ' header#top #logo .original span'));
                    if($($selector+' header#top #logo img.starting-logo').length > 0) $($selector+ ' header#top #logo img.starting-logo:not(.dark-version)').clone().appendTo($($selector+ ' header#top #logo .light span'));
                    if($($selector+' header#top #logo img.starting-logo.retina.logo').length > 0) $($selector+ ' header#top #logo img.starting-logo.retina-logo:not(.dark-version)').clone().appendTo($($selector+ ' header#top #logo .light span'));
                    if($($selector+' header#top #logo img.starting-logo.dark-version').length > 0) $($selector+ ' header#top #logo img.starting-logo.dark-version').clone().appendTo($($selector+ ' header#top #logo .dark span'));
                    if($($selector+' header#top #logo img.starting-logo.dark-version.retina.logo').length > 0) $($selector+ ' header#top #logo img.starting-logo.dark-version.retina-logo').clone().appendTo($($selector+ ' header#top #logo .dark span'));
                    $($selector+ ' header#top #logo > img').remove();

                } else {
                    var $logoText = $('header#top #logo').text();
                    $($selector+' header#top #logo').html(' ');
                    $($selector+' header#top #logo').append('<span class="dark"><span>'+$logoText+'</span></span>');
                    $($selector+' header#top #logo').append('<span class="light"><span>'+$logoText+'</span></span>');
                    $($selector+' header#top #logo').append('<span class="original"><span>'+$logoText+'</span></span>');
                }

                //retina logo fix
                if($('.retina-logo:visible').length > 0) $($selector+' header#top #logo').css('width',$('.retina-logo').width());

                //cache the widths
                 $($selector+' nav > ul.sf-menu > li > a, '+$selector+' nav > ul.buttons > li#search-btn a, '+$selector+' nav > ul.buttons > li.slide-out-widget-area-toggle a > span, '+$selector+' .cart-icon-wrap, '+$selector+' header#top #logo').each(function(){
                    var $extraWidth = ($(this).find('.original .sf-sub-indicator').length > 0) ? 14: 1;
                    $(this).find('.original').attr('data-w',$(this).find('span.original').width()+$extraWidth);  
                });
            

            }   
        }
    }

	//initial slide load 
    var $animating = false;

    //responsive slider
    var $sliderHeights = [];
    var $existingSliders = [];
    var slideAnimationQueues = [];
    
    var sliderLength = $('.swiper-container').length;
  	var sliderLoadedLength = 0;
    
    function initialSlideLoad() {

        $animating = false;

        //add class to first video slide
        $('.swiper-wrapper').each(function(){
            if($(this).find('.swiper-slide:nth-child(2) video').length > 0) $(this).find('.swiper-slide:nth-child(2)').addClass('first_video_slide');
            
              //ken burns start
              if($(this).parents('.nectar-slider-wrap[data-bg-animation="ken_burns"]').length > 0) {
                $(this).find('.swiper-slide.swiper-slide-active').addClass('ken-burns').attr('data-ken-burns','active');
              }

              
        });

      
    	$('.nectar-slider-wrap').each(function(i){
    		
  
        slideAnimationQueues[i] = {
            sliderID: $(this).attr('id'),
            captionInterval: '',
            kenBurnsInterval: '' 
        } 
           
    		var $sliderWrapCount = $('.nectar-slider-wrap').length;
    		var $that = $(this);
        
        //store animation queue arr obj
        

    		if($(this).find('.swiper-slide-active video').length > 0){
    			
    			if(!$('html').hasClass('no-video') && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/) ){

                //var videoReady = setInterval(function(){

        				//if($that.find('.swiper-slide-active:first video').get(0).readyState > 3) {

          					showSliderControls();
          					resizeToCover();
          					slideContentPos();
          					sliderLoadIn($that,i);

                    var $timeout = 0;
                    if($('#ajax-loading-screen[data-effect="center_mask_reveal"]').length > 0 && $that.parents('.parallax_slider_outer').length > 0 ||
                       $('#ajax-loading-screen[data-effect="center_mask_reveal"]').length > 0 && $that.hasClass('first-nectar-slider') ) {
                         $timeout = 450;
                    } 

          					setTimeout(function(){ captionTransition($nectarSliders[i]); onChangeStart($nectarSliders[i]); },$timeout); 
          					darkFirstSlide($that);
                    //clearInterval(videoReady);
  				
                    $that.addClass('loaded');
                    if($that.parents('.parallax_slider_outer').length > 0 || $that.hasClass('first-nectar-slider')) {
                        $('#ajax-loading-screen').addClass('loaded');
                        setTimeout(function(){ $('#ajax-loading-screen').addClass('hidden'); },1000);
                    }
        				//}

            //},60);

    			} 
    			//ie8
    			else {
    				showSliderControls();
    				resizeToCover();
    				slideContentPos();
    				sliderLoadIn($that,i);
    				captionTransition($nectarSliders[i]);
                    darkFirstSlide($that);
    			}
    		} 
        
    		else {
    	 
    			  //var $firstBg = $(this).find('.swiper-slide-active .image-bg').attr('style');
    		    //var pattern = /url\(["']?([^'")]+)['"]?\)/;
    		    //var match = pattern.exec($firstBg);
            
    		    //if (match) {
    		    	       
    		    //var slideImg = new Image();
    				//slideImg.src = match[1];
    				
    				//$(slideImg).load(function(){ 
            
  					showSliderControls();
  					resizeToCover();
  					slideContentPos();
  					sliderLoadIn($that,i);
  					var $timeout = 0;
            if($('#ajax-loading-screen[data-effect="center_mask_reveal"]').length > 0 && $that.parents('.parallax_slider_outer').length > 0 && $that.parents('.first-section').length > 0 ||
                $('#ajax-loading-screen[data-effect="center_mask_reveal"]').length > 0 && $that.hasClass('first-nectar-slider') ) {
                $timeout = 450;
            } 

            setTimeout(function(){ captionTransition($nectarSliders[i]);  onChangeStart($nectarSliders[i]); },$timeout); 

            darkFirstSlide($that);

            $that.addClass('loaded');
            if($that.parents('.parallax_slider_outer').length > 0 || $that.hasClass('first-nectar-slider')) {
                $('#ajax-loading-screen').addClass('loaded');
                setTimeout(function(){ $('#ajax-loading-screen').addClass('hidden'); },1000);
            }

    				//});

                   
    			//}
    		}
    		
    		//mobile check
    		if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){
    			

              $that.find('.video-wrap').each(function(){

                var $videoSlide = $(this);
                $(this).css('bottom','0');
                $(this).find('video')[0].onplay = function(){    
    						  $videoSlide.find('video').css({'opacity':'1'});
                  setTimeout(function(){
                    $videoSlide.parents('.swiper-slide').find('.mobile-video-image').transition({'opacity':'0'},250);
                  },100);
                  
    						};

              });
              
              
              
              captionTransition($nectarSliders[i]);
              showSliderControls();
              resizeToCover();
              slideContentPos();
              darkFirstSlide($that);
              
              setTimeout(function(){ resizeToCover(); slideContentPos(); },400);
              
              $('.nectar-slider-wrap').find('.nectar-slider-loading').fadeOut(800,'easeInOutExpo');
              $('.nectar-slider-wrap.first-section .swiper-container .swiper-wrapper .swiper-slide').removeClass('not-loaded');
              
      			
      		


                $(this).addClass('loaded');

                if($that.parents('.parallax_slider_outer').length > 0 || $that.hasClass('first-nectar-slider')) {
                    $('#ajax-loading-screen').addClass('loaded');
                    setTimeout(function(){ $('#ajax-loading-screen').addClass('hidden'); },1000);
                }

                autorotateInit($(this),i);
    		}


                if($('header#top #logo img').length > 0) {
                  var logoImg = new Image();
                      logoImg.src = $('header#top #logo img:first').attr('src');
                    
                     $(logoImg).load(function(){
                        //directionalNavColorInit($that);
                        if($that.attr('data-overall_style') == 'directional') {
                           $that.find('.swiper-container').addClass('directional-trans-current'); 
                           onChangeStart($nectarSliders[i]);
                        } 
                    });
                 } else {
                    //directionalNavColorInit($that);
                    if($that.attr('data-overall_style') == 'directional') {
                       $that.find('.swiper-container').addClass('directional-trans-current'); 
                       onChangeStart($nectarSliders[i]);
                    } 
                 }
    		
    	});


        ////get slider heights
        $('.swiper-container').each(function(i){
          
            //left and right pagination room
            if( $(this).is('[data-bullet_position="left"]') ) {
              if($(this).find('[data-x-pos="left"]').length > 0) {
                $(this).find('.slider-pagination-wrap').addClass('extra-room');
              }
            }
            if( $(this).is('[data-bullet_position="right"]') ) {
              if($(this).find('[data-x-pos="right"]').length > 0) {
                $(this).find('.slider-pagination-wrap').addClass('extra-room');
              }
            }
            
        });


    }




    function nectarInit(){
        if(doneVideoInit == true) return;
        
         //videos
        /*$('.swiper-slide .slider-video').mediaelementplayer({
            enableKeyboard: false,
            iPadUseNativeControls: false,
            pauseOtherPlayers: false,
            // force iPhone's native controls
            iPhoneUseNativeControls: false,
            // force Android's native controls
            AndroidUseNativeControls: false
        });*/

        //if using pp - init it after slide loads
        if($().prettyPhoto) prettyPhotoInit();


        $('.swiper-slide iframe[data-aspectRatio]').each(function() {
            var newWidth = $(this).parent().width();
             
            var $el = $(this);
            
            //in nectar slider
            if($(this).parents('.swiper-slide').length > 0) {
                if($(this).is(':visible')) $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));
            } 
            //all others
            else {
                $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));
            }
            
            
        });
        
        
        doneVideoInit = true;
     }


	
    //autoplay - set vars before init call
    var autoplay = [];
    var sliderAutoplayCount = 0;
  
  //dynamic height sliders - set before calling dynamic height
  function nsSliderArrStore() {
    $sliderHeights = [];
    $existingSliders = [];
    $('.swiper-container').each(function(i){
      $sliderHeights[i] = parseInt($(this).attr('data-height'));  
      $existingSliders[i] = $(this).parent().attr('id');
    });
  }
  
  nsSliderArrStore();
	dynamicHeightSliders();  
  sliderSize();
  resizeToCover();
  
  /*quickly calc first slider bg parallax calcs to avoid any jumping*/
  for(var nsCount=0; nsCount<$nectarSliders.length; nsCount++) {
    $nectarSliders[nsCount].resizeFix();
  }
  
  $('.parallax_slider_outer .nectar-slider-wrap .slide-bg-wrap').each(function(){
    if($(this).parents('.top-level').length > 0) {
      if($(this).find('.image-bg').length > 0) { 
        $(this).find('.image-bg').css({'height':  Math.ceil( $(this).parent().offset().top * 0.25 ) + $(this).outerHeight(true) });
      }
      if($(this).find('.video-wrap').length > 0) { 
        $(this).find('.video-wrap').css({'height': Math.ceil( $(this).parent().offset().top * 0.25 ) + $(this).outerHeight(true) }); 
      }
    } 
  });
  

  
	initialSlideLoad();
	
	//switching tab with autorotate hides captions
  document.addEventListener("visibilitychange", function() {
      if (document.hidden){
          //browser tab is hidden
      } else {
          //browser tab is vis
          $('.nectar-slider-wrap').each(function(i){
              $nectarSliders[i].resizeFix();
          });
        
      }
  });
  
  


	var headerPadding = parseInt($('#header-outer').attr('data-padding'));
    var shrinkNum = 8;
    if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');
	
	
	function dynamicHeightSliders(){
		
		var $adminBarHeight = ($('#wpadminbar').length > 0) ? 28 : 0 ;
		
			$('.nectar-slider-wrap').each(function(){
				
				var $heightCalc;
				var $minHeight = $('.swiper-container').attr('data-min-height');

        //material header nav parallax top
        if(window.innerWidth > 1000) {
            $('body.material:not("[data-header-format=\'left-header\']") .parallax_slider_outer.first-section .nectar-slider-wrap').css('top',$('#ajax-content-wrap').offset().top);
        } else {
            $('body.material:not("[data-header-format=\'left-header\']") .parallax_slider_outer.first-section .nectar-slider-wrap').css('top','0');
        }

				//fullscreen
				if($(this).attr('data-fullscreen') == 'true' && $(this).attr('data-full-width') == 'true' || 
           $(this).attr('data-fullscreen') == 'true' && $(this).attr('data-full-width') == 'boxed-full-width' ||
           $(this).attr('data-fullscreen') == 'true' && $(this).parents('.full-width-content').length > 0 && $(this).parents('.vc_col-sm-12').length > 0 ) {
					
            if($(this).parents('#nectar_fullscreen_rows').length == 0) {
     
    					//first slider on page
    					if($(this).hasClass('first-section') && $(this).index() == 0 || $(this).parents('.wpb_row').length > 0 && $(this).parents('.wpb_row').hasClass('first-section') && $(this).index() == 0 && $(this).parents('.parallax_slider_outer').length == 0){
    						//min height
    						$heightCal = ( ($(window).height() - $(this).offset().top + 2) <= $minHeight ) ? $minHeight : $(window).height() - $(this).offset().top + 2;
    						$(this).find('.swiper-container').attr('data-height',$heightCal);
    						
    					}
    					//first parallax slider on page
    					else if($(this).parents('.first-section').length > 0 && $(this).parents('.parallax_slider_outer').length > 0 && $(this).parents('#full_width_portfolio').length == 0){ 
    						
    						//min height
    						$heightCal = ( ($(window).height() - $(this).offset().top + 2) <= $minHeight ) ? $minHeight : $(window).height() - $(this).offset().top + 2;
    						$(this).find('.swiper-container').attr('data-height',$heightCal);
    						
    					} 
    					//first portfolio slider on page
    					else if($(this).parents('#full_width_portfolio').length > 0 && $(this).attr('data-parallax') != 'true' && $(this).index() == 0){ 
    						//min height
    						$heightCal = ( ($(window).height() - $(this).offset().top + 2) <= $minHeight ) ? $minHeight : $(window).height() - $(this).offset().top + 2;
    						$(this).find('.swiper-container').attr('data-height',$heightCal);
    						
    					}
    					//first portfolio parallax slider on page
    					else if($(this).parents('#full_width_portfolio').length > 0 && $(this).attr('data-parallax') == 'true'){ 
    						
    						//min height
    						$heightCal = ( ($(window).height() - $(this).offset().top + 2) <= $minHeight ) ? $minHeight : $(window).height() - $(this).offset().top + 2;
    						$(this).find('.swiper-container').attr('data-height',$heightCal);
    					}
    					//all others
    					else { 
                $resize = ($('#header-outer[data-header-resize="0"]').length > 0) ? 0 : parseInt(shrinkNum) + headerPadding*2;
    						$headerSize = ($('#header-outer[data-permanent-transparent="false"]').length > 0) ? $('#header-space').height() - $resize-3 : -3;
                            if($('#nectar_fullscreen_rows').length > 0 || ($('body[data-header-format="left-header"]').length > 0 && window.innerWidth >= 1000)) $headerSize = $adminBarHeight;
    						//min height
    						$heightCal = ( $(window).height() - $headerSize <= $minHeight ) ? $minHeight : $(window).height() - $headerSize;
    						$(this).find('.swiper-container').attr('data-height',$heightCal);
    					}
    					
    					//transparent header loading icon center
              if($('#header-outer[data-remove-border="true"]').length == 0) {
        					if($('body #header-outer[data-transparent-header="true"]').length > 0
        					   && $('.first-section .nectar-slider-wrap[data-fullscreen="true"]').length > 0 
        					   && $('#page-header-bg').length == 0) {
        					   	
        						$('.first-section .nectar-slider-loading').css({
        							'background-image': $loading_bg_storage,
        							'background-position' : 'center ' + ((($(window).height() /2) + 15) - $('#header-space').height()) +'px'
        						});

                        $('.first-section .nectar-slider-wrap .nectar-slider-loading .loading-icon').css({
                            'opacity' : '1',
                            'height' :  $(window).height() - $('#header-space').height() + 'px'
                        });
            			}
              }
    	
    				}

        }

				//flexible height
				if($(this).attr('data-flexible-height') == 'true' && $(this).attr('data-fullscreen') != 'true') {

					//escape if not placed in a flexible ready position
					if($(this).parents('.wpb_row.full-width-content').length == 0 && $(this).attr('data-full-width') == 'false') { $(this).attr('data-flexible-height','false'); return false; }
					
					var $minHeight = $('.swiper-container').attr('data-min-height');
					
					var currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
					var $windowWidth = $(window).width();
					var $definedHeight = $sliderHeights[currentKey];

					var dif = $(window).width() / 1600;
          
					if( window.innerWidth > 1000 && $('#boxed').length == 0) {
						$(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*dif));
					} else {
						
						//column sliders
						var $parentCol = ($(this).parents('.wpb_column').length > 0) ? $(this).parents('.wpb_column') : $(this).parents('.col') ;
						if($parentCol.length == 0) $parentCol = $('.main-content');
							
						if(!$parentCol.hasClass('vc_span12') && !$parentCol.hasClass('main-content') && !$parentCol.hasClass('span_12') && !$parentCol.hasClass('vc_col-sm-12') ) {
						
							var $parentColWidth = sliderColumnDesktopWidth($parentCol);
							var $parentColRatio = 1100/$parentColWidth;
							
							//min height
							if( $definedHeight*dif <= $minHeight ){
								$(this).find('.swiper-container').attr('data-height',$minHeight);
							} else {
								$(this).find('.swiper-container').attr('data-height',Math.ceil($parentColRatio*$definedHeight*dif));	
							}
						  
						} 
						
						
						//boxed
						else if($('#boxed').length > 0){
							
							var dif = $('#boxed').width() / 1600;
							
							if( window.innerWidth > 1300 ) {
								
								if($('body[data-ext-responsive="true"]').length > 0){
									
									if($(this).has('[data-full-width="boxed-full-width"]')) {
										($('#boxed').width() < 1400) ? $(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*dif)) : $(this).find('.swiper-container').attr('data-height',$definedHeight*(1400/1600));
									
                                    }
									
								} else {
									if($(this).has('[data-full-width="boxed-full-width"]')) $(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*(1200/1600)));
								   
                                }
								
						    } else if( window.innerWidth <= 1300 && window.innerWidth >= 1000  ) {
								
								if($('body[data-ext-responsive="true"]').length > 0){
									$(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*dif));
								} else {
									if($(this).has('[data-full-width="boxed-full-width"]')) $(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*(980/1600)));
								}
								
							} else if( window.innerWidth < 1000 && window.innerWidth > 690 ) {
								
								var $heightCalc = ( $definedHeight*(679/1600) <= $minHeight ) ? $minHeight : $definedHeight*(679/1600);
								if($(this).has('[data-full-width="boxed-full-width"]')) $(this).find('.swiper-container').attr('data-height',$minHeight);
							
							} else if( window.innerWidth <= 690 && window.innerWidth >= 470 ) {
								
								var $heightCalc = ( $definedHeight*(410/1600) <= $minHeight ) ? $minHeight : $definedHeight*(410/1600);
								if($(this).has('[data-full-width="boxed-full-width"]')) $(this).find('.swiper-container').attr('data-height', $heightCalc);
							
							} else if( window.innerWidth < 470 ) {
								
								var $heightCalc = ( $definedHeight*(318/1600) <= $minHeight ) ? $minHeight : $definedHeight*(318/1600);
								if($(this).has('[data-full-width="boxed-full-width"]')) $(this).find('.swiper-container').attr('data-height', $heightCalc);
							
							} 
							
						}
						
						
						//regular
						else {
							
							//min height
							if( $definedHeight*dif <= $minHeight ){
								$(this).find('.swiper-container').attr('data-height',$minHeight);
							} else {
								$(this).find('.swiper-container').attr('data-height',Math.ceil($definedHeight*dif));
							}
							
						}
						
					}
					
					
					//transparent header loading icon center
					if($('body #header-outer[data-transparent-header="true"]').length > 0 && 
					   $('.first-section .nectar-slider-wrap[data-flexible-height="true"]').length > 0) {
					   	
						$('.first-section .nectar-slider-loading').css({
							'background-image': $loading_bg_storage,
							'background-position' : 'center ' + (((($definedHeight*dif)/2) + 15) - $('#header-space').height()) +'px'
						});

            $('.first-section .nectar-slider-wrap .nectar-slider-loading .loading-icon').css({
                'opacity' : '1',
                'height' :  $definedHeight*dif - $('#header-space').height() + 'px'
            });
					}
					
				}
		});
	}
	
	if(window.innerWidth > 690){
		$(window).on('resize.dynamicHeights',dynamicHeightSliders);
	} else {
		
		//stop mobile browsers from firing the resize event on scroll (when toolbar hides)
        var $windowWidth = $(window).width(), $windowHeight = $(window).height();
        var $orientationChange = 0;
    
        window.addEventListener("orientationchange", function() {
           $orientationChange = 1;
        });
        
        $(window).resize(function(){
            if( ($(window).width() != $windowWidth && $(window).height != $windowHeight) || $orientationChange == 1){
                dynamicHeightSliders(); 
                $orientationChange = 0;
            }
        });
	}
	
    setTimeout(function(){
       dynamicHeightSliders(); 
    },100);
	
	
	////helper function
	function sliderColumnDesktopWidth(parentCol) {
		
		var $parentColWidth = 1100;
		var $columnNumberParsed = $(parentCol).attr('class').match(/\d+/);
		
		if($columnNumberParsed == '2') { $parentColWidth = 170 }
		else if($columnNumberParsed == '3') { $parentColWidth = 260 } 
		else if($columnNumberParsed == '4') { $parentColWidth = 340 } 
		else if($columnNumberParsed == '6') { $parentColWidth = 530 } 
		else if($columnNumberParsed == '8') { $parentColWidth = 700 } 
		else if($columnNumberParsed == '9') { $parentColWidth = 805 }
		else if($columnNumberParsed == '10') { $parentColWidth = 916.3 }
		else if($columnNumberParsed == '12') { $parentColWidth = 1100 }
	
		return $parentColWidth;
	}
	
	
	
	$(window).resize(sliderSize);
	
	function sliderSize(){
		

		//check for mobile first
		if( window.innerWidth < 1000 && window.innerWidth > 690 ) {
			
			//fullwidth sliders
			$('.nectar-slider-wrap[data-full-width="true"]:not([data-fullscreen="true"],[data-flexible-height="true"])').each(function(i){
				currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
				$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/1.4 )	
			});
			
			//column sliders
			$('.nectar-slider-wrap[data-full-width="false"]:not([data-fullscreen="true"]):not([data-flexible-height="true"])').each(function(i){
				currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
				
				var $currentSliderHeight = $sliderHeights[currentKey];
				var $parentCol = ($(this).parents('.wpb_column').length > 0) ? $(this).parents('.wpb_column') : $(this).parents('.col');
				
				//fullwidth but inside the span_9 post area
				if($(this).parents('.post-area').length > 0 && $(this).parents('.vc_span12').length > 0) $parentCol = $(this).parents('.post-area');
				
				//last resort
				if($parentCol.length == 0) $parentCol = $('.main-content');

				var $parentColWidth = sliderColumnDesktopWidth($parentCol);
				
				var $aspectRatio = $currentSliderHeight/$parentColWidth;
				
				$(this).find('.swiper-container').attr('data-height',$aspectRatio*$parentCol.width());
			});
			
			//boxed sliders
			$('.nectar-slider-wrap[data-full-width="boxed-full-width"]:not([data-flexible-height="true"]):not([data-fullscreen="true"])').each(function(i){
				currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
				$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/1.9 )	
			});
		} 
		
		else if( window.innerWidth <= 690 ) {
	      
	      //fullwidth sliders		
		  $('.nectar-slider-wrap[data-full-width="true"]:not([data-fullscreen="true"],[data-flexible-height="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/2.7 )	
		  });
		  
		  //column sliders
		  $('.nectar-slider-wrap[data-full-width="false"]:not([data-fullscreen="true"]):not([data-flexible-height="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			
			var $currentSliderHeight = $sliderHeights[currentKey];
			var $parentCol = ($(this).parents('.wpb_column').length > 0) ? $(this).parents('.wpb_column') : $(this).parents('.col');
			
			//fullwidth but inside the span_9 post area
			if($(this).parents('.post-area').length > 0 && $(this).parents('.vc_span12').length > 0) $parentCol = $(this).parents('.post-area');
			
			//last resort
			if($parentCol.length == 0) $parentCol = $('.main-content');
			
			var $parentColWidth = sliderColumnDesktopWidth($parentCol);
			
			var $aspectRatio = $currentSliderHeight/$parentColWidth;
			
			$(this).find('.swiper-container').attr('data-height',$aspectRatio*$parentCol.width());	
		  });
		  
		  //boxed sliders		
		  $('.nectar-slider-wrap[data-full-width="boxed-full-width"]:not([data-flexible-height="true"]):not([data-fullscreen="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/2.9 )	
		  });
		
		} 
		
		else if( window.innerWidth < 1300 && window.innerWidth >= 1000  ) {
	      
	      //fullwidth sliders		
		  $('.nectar-slider-wrap[data-full-width="true"]:not([data-fullscreen="true"],[data-flexible-height="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/1.2 )	
		  });
		  
		  //column sliders
		  $('.nectar-slider-wrap[data-full-width="false"]:not([data-fullscreen="true"]):not([data-flexible-height="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			
			var $currentSliderHeight = $sliderHeights[currentKey];
			var $parentCol = ($(this).parents('.wpb_column').length > 0) ? $(this).parents('.wpb_column') : $(this).parents('.col') ;
			
			//fullwidth but inside the span_9 post area
			if($(this).parents('.post-area').length > 0 && $(this).parents('.vc_span12').length > 0) $parentCol = $(this).parents('.post-area');
			
			//last resort
			if($parentCol.length == 0) $parentCol = $('.main-content');
			
			var $parentColWidth = sliderColumnDesktopWidth($parentCol);
			
			var $aspectRatio = $currentSliderHeight/$parentColWidth;
			
			$(this).find('.swiper-container').attr('data-height',$aspectRatio*$parentCol.width());	
					
		  });
		  
		  //boxed sliders		
		  $('.nectar-slider-wrap[data-full-width="boxed-full-width"]:not([data-flexible-height="true"]):not([data-fullscreen="true"])').each(function(i){
		  	currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
			$(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey]/1.2 )	
		  });
		
		} 
		
		else {
			
			//fullwidth sliders
			$('.nectar-slider-wrap[data-full-width="true"]:not([data-fullscreen="true"],[data-flexible-height="true"])').each(function(i){
				currentKey = $existingSliders.getKeyByValue($(this).attr('id'));
				if($(this).attr('data-flexible-height') != 'true') $(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey])	
			});
			
			//boxed sliders
			$('.nectar-slider-wrap[data-full-width="false"]:not([data-fullscreen="true"]), .nectar-slider-wrap[data-full-width="boxed-full-width"]:not([data-flexible-height="true"]):not([data-fullscreen="true"])').each(function(i){
				currentKey = $existingSliders.getKeyByValue($(this).attr('id')); 
				if($(this).attr('data-flexible-height') != 'true') $(this).find('.swiper-container').attr('data-height',$sliderHeights[currentKey] )	
			});
		}

	}
	
	
    //remove transform if single slide
	$('.nectar-slider-wrap').each(function(){
          if($(this).find('.swiper-wrapper .swiper-slide').length == 1) {
               $(this).find('.swiper-slide').addClass('no-transform');
           $(this).find('.swiper-wrapper').addClass('no-transform');
        }
    });
	
	 

	//slider height
	
	var min_w = 1500; // minimum video width allowed
	var vid_w_orig;  // original video dimensions
	var vid_h_orig;
	
    vid_w_orig = 1280;
    vid_h_orig = 720;
    var $headerHeight = $('header').height()-1;
    
    function nsSliderContentResize(e){
         resizeToCover(); slideContentPos(); 
         for(var i=0; i < $nectarSliders.length; i++){

            //make sure the parent is not hidden
            if($($nectarSliders[i].container).parent().attr('data-transition') && $($nectarSliders[i].container).parent().attr('data-transition') == 'fade') {
                
                for(var k=0; k < $nectarSliders[i].slides.length; k++){
                      $nectarSliders[i].setTransition($nectarSliders[i].slides[k], 0);
                }

                $('.swiper-wrapper').stop(true,true).css('transition-duration','0s');

                if($('.nectar-slider-loading').css('display') == 'none') {
                    
                    //var captionTransString = $($nectarSliders[i].container).parent().is('[data-caption-trans]') ? $($nectarSliders[i].container).parent().attr('data-caption-trans') : 'fade_in_from_bottom';
                    //$($nectarSliders[i].container).find('.swiper-slide-active').find('.content').children().each(function(i){
                      //captionTransAnimation($(this),i,captionTransString); 
                    //});

                     $('.swiper-wrapper .swiper-slide.swiper-slide-active .content > *').css({
                        'opacity': 1,
                        'transform' : 'translateY(0)'
                     });
                 }
             }

            $nectarSliders[i].reInit();
            $nectarSliders[i].resizeFix();
                
        }

    }

   // $(window).off('resize.nsSliderContent');
    $(window).on('resize.nsSliderContent',nsSliderContentResize);
    //if(!$('body').hasClass('ajax-loaded')) $(window).trigger('resize'); 
 
	function resizeToCover() {
		$('.nectar-slider-wrap').each(function(i){


			if( $(this).css('visibility') != 'hidden') {

          var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && window.innerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
          var $windowWidth = $(window).width() - $leftHeaderSize;

    			//width resize 
    			if($('body > #boxed').length == 0 && $('.nectar-slider-wrap[data-full-width="true"]').parent().attr('id') != 'portfolio-extra' && $(this).parents('.post-area:not(".span_12")').length == 0){ 
    				$('.nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .nectar-slider-wrap').css({'left': -( ($windowWidth-$smoothSrollWidth)/2 - $('.main-content').width()/2), 'margin-left': '0' });
    				$('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css('width',$windowWidth);
    		
        	} 
    			else if( $('.nectar-slider-wrap[data-full-width="true"]').parent().attr('id') == 'portfolio-extra' && $('#full_width_portfolio').length != 0){   
    				$('.nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .nectar-slider-wrap').css({'left': -( ($windowWidth-$smoothSrollWidth)/2 - $('.main-content').width()/2), 'margin-left': '0' });
    				$('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css('width',$windowWidth);
    			}
    			else {
    				var $container = ($('body > #boxed').length == 0) ? '.post-area' : '.container-wrap';
    				//backup incase only slider is used with nothing else in boxed mode
    				if($($container).width() == '0' && $('body > #boxed').length > 0) $container = '#boxed';
    				$('.nectar-slider-wrap[data-full-width="true"] .swiper-container, .nectar-slider-wrap[data-full-width="true"], .parallax_slider_outer.first-section .swiper-container, .parallax_slider_outer.first-section .nectar-slider-wrap').css({'width':$($container).width(), 'margin-left': '0'});
    			}

               
    			
    			var $sliderHeight = parseInt($(this).find('.swiper-container').attr('data-height'));
          var $sliderMinHeight = ( $(this).find('.swiper-container').is('[data-min-height]') && $(this).find('.swiper-container').attr('data-min-height').length > 0 ) ? parseInt($(this).find('.swiper-container').attr('data-min-height')) : 0;
      
          if($sliderMinHeight > $sliderHeight) { $sliderHeight = $sliderMinHeight; }
          
    			var isFullWidthCompatible = ($(this).attr('data-full-width') == 'true') ? 'true' : 'false';
    			
    			if($(this).parent().attr('id') == 'portfolio-extra' && $('#full_width_portfolio').length == 0 || $(this).parents('.post-area').length > 0) { isFullWidthCompatible = 'false'; };
    			
    			var $sliderWidth = (isFullWidthCompatible == 'true') ? $windowWidth-$smoothSrollWidth : $(this).width();
    			
    			$(this).parents('.parallax_slider_outer').css('height',$sliderHeight);
    			$(this).css('height',$sliderHeight);
    			$(this).find('.swiper-container, .swiper-slide').css({'height':$sliderHeight+1});
    			$(this).find('.swiper-container').css('width', $sliderWidth);
    			//$(this).find('.swiper-slide').css('width', $sliderWidth);
    			
          
          //when using parallax, let that calc height/width
          if(!$(this).is('[data-parallax="true"]')) { 

    		    // set the video viewport to the window size
    		    $(this).find('.video-wrap').width($sliderWidth+2);
    		    $(this).find('.video-wrap').height($sliderHeight+2);
          
                 //handle left header
                /*if($('body[data-header-format="left-header"]').length > 0) {
                    $('.parallax_slider_outer.first-section .nectar-slider-wrap, .parallax_slider_outer.first-section .nectar-slider-wrap .swiper-container').css('width',$('#ajax-content-wrap').width());
                    if($(this).parents('.parallax_slider_outer.first-section').length > 0) {
                        $sliderWidth = (isFullWidthCompatible == 'true') ? $(window).width()-$smoothSrollWidth - $('#header-outer').width() : $(this).width() - $('#header-outer').width();
                        $(this).find('.video-wrap').width($sliderWidth+2);
                    }
                } */
    		
    		    // use largest scale factor of horizontal/vertical
    		    var scale_h = $sliderWidth / vid_w_orig;
    		    var scale_v = ($sliderHeight - $headerHeight) / vid_h_orig; 
    		    var scale = scale_h > scale_v ? scale_h : scale_v;
    			
    			//update minium width to never allow excess space
    		    min_w = 1280/720 * ($sliderHeight+20);
    		    
    		    // don't allow scaled width < minimum video width
    		    if (scale * vid_w_orig < min_w) {scale = min_w / vid_w_orig;}
    		        
    		    // now scale the video
    		    $(this).find('video, .mejs-overlay, .mejs-poster').width(Math.ceil(scale * vid_w_orig +2));
    		    $(this).find('video, .mejs-overlay, .mejs-poster').height(Math.ceil(scale * vid_h_orig +2));
    		    
    		    // and center it by scrolling the video viewport
    		    $(this).find('.video-wrap').scrollLeft(($(this).find('video').width() - $sliderWidth) / 2);
    		    
    		    $(this).find('.swiper-slide').each(function(){
    		    	
    		    	//video alignment
    		    	if($(this).find('.video-wrap').length > 0){
    			    	//align  middle
    				    if($(this).attr('data-bg-alignment') == 'center'){
    				    	$(this).find('.video-wrap, .mejs-overlay, .mejs-poster').scrollTop(($(this).find('video').height() - ($sliderHeight)) / 2);
    				    }
    				    //align bottom
    				    else if($(this).attr('data-bg-alignment') == 'bottom'){
    				    	$(this).find('.video-wrap').scrollTop(($(this).find('video').height() - ($sliderHeight+2)));
    				    }
    				    //align top
    				    else {
    				    	$(this).find('.video-wrap').scrollTop(0);
    				    } 
    			    }
    		    	
    		    });

            } //if visible
          
          }//parallax
		});
	}; 
	
	////initial call
	resizeToCover();
	
	
	//caption transitions 
  var loopFixTimeout;
  var previousSlide = 0;
  
	function captionTransition(obj){ 

			resizeToCover(); 
			
			var $containerClass;

			(typeof obj == 'undefined') ? $containerClass = 'div[id^=ns-id-]' : $containerClass = '#'+$(obj.container).parents('.nectar-slider-wrap').attr('id'); ;
	   

	 		var fromLeft = Math.abs(parseInt($($containerClass+' .swiper-wrapper').css('left')));
			var currentSlide = Math.round(fromLeft/$($containerClass+' .swiper-slide').width()); 
			var $slideNum =  $($containerClass+':first .swiper-wrapper > div').length;  
      
      var captionTransString = $($containerClass).is('[data-caption-trans]') ? $($containerClass).attr('data-caption-trans') : 'fade_in_from_bottom';
      

			currentSlide = $($containerClass + ' .swiper-slide-active').index();
			//if(isNaN(currentSlide)) currentSlide = 0;
    
			//make sure user isn't going back to same slide 
			if( $($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').find('.content *').length > 0 && captionTransString != 'reveal_title') {
				if($($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').find('.content *').css('opacity') != '0' && !$('html').hasClass('no-video')) {
					//play video if there's one
					playVideoBG(currentSlide + 1, $containerClass);
					
					if(!$($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').hasClass('autorotate-shown')) {
						return false;
					} else {
						$($containerClass+' .swiper-slide').removeClass('autorotate-shown');
					}
					
				}
			} 
      
      if( $($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').find('.content h2').length > 0 && captionTransString == 'reveal_title') {
				if($($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').find('.content h2 .word').css('opacity') != '0' && !$('html').hasClass('no-video')) {
					//play video if there's one
					playVideoBG(currentSlide + 1, $containerClass);
					
					if(!$($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').hasClass('autorotate-shown')) {
						return false;
					} else {
						$($containerClass+' .swiper-slide').removeClass('autorotate-shown');
					}
					
				}
			} 

			//hide all
			if(!$('html').hasClass('no-video')) {
        if(captionTransString == 'fade_in_from_bottom') {
          $($containerClass+' .swiper-slide .content p, '+$containerClass+' .swiper-slide .content h2, '+$containerClass+' .swiper-slide .content .buttons').stop(true,true).css({'opacity':0, 'transform' : 'translateY(40px)'});
        } else if(captionTransString == 'reveal_title') {
          $($containerClass+' .swiper-slide .content h2 .word').stop(true,true).css({'opacity': '0'});
          
          //if(currentSlide > previousSlide || $($containerClass +' .swiper-container').attr('data-loop') == 'true' && currentSlide == 1 && previousSlide == $slideNum - 2) {
          //  $($containerClass+' .swiper-slide .content h2 span span .text-char').css({'transform' : 'translateX(100px)', 'opacity': '0'});
          //} else {
            $($containerClass+' .swiper-slide .content h2 .word > [class*="char"]').css({ 'opacity': '0'});
          //}
          $($containerClass+' .swiper-slide .content h2 .word > [class*="char"] > span').css({'transform' : 'scale(0)'});
          
          $($containerClass+' .swiper-slide .content p, '+$containerClass+' .swiper-slide .content .buttons').stop(true,true).css({'opacity':0, 'transform' : 'translateY(40px)'});
        }
				
			}
			
			//pause video if there's one
			$($containerClass+' .swiper-slide').each(function(){
				if($(this).find('.video-wrap video').length > 0 && !$('html').hasClass('no-video') && !$onMobileBrowser) { $(this).find('.video-wrap video').get(0).pause(); }
			});
			$($containerClass+' .swiper-slide:not(".swiper-slide-active")').each(function(){
				if($(this).find('.video-wrap video').length > 0) { 
	       
					if($(this).find('.video-wrap video').get(0).currentTime != 0 ) $(this).find('.video-wrap video').get(0).currentTime = 0;
				}
			});

			
			if($($containerClass +' .swiper-container').attr('data-loop') == 'true') {

    
				//webkit video fix
				if(  $($containerClass+' .swiper-slide-active').index()+1 == 2 && $($containerClass+' .swiper-slide-active video').length > 0  && !$('html').hasClass('no-video') && !$onMobileBrowser) { 

					$($containerClass+' .swiper-slide:last-child').find('.video-wrap video').get(0).play();
					$($containerClass+' .swiper-slide:last-child').find('.video-wrap video').get(0).pause();
				}
				if(  $($containerClass+' .swiper-slide-active').index()+1 == $slideNum-1 && $($containerClass+' .swiper-slide-active video').length > 0  && !$('html').hasClass('no-video') && !$onMobileBrowser) { 
					
					$($containerClass+' .swiper-slide:first-child').find('.video-wrap video').get(0).play();
					$($containerClass+' .swiper-slide:first-child').find('.video-wrap video').get(0).pause();
				}
				if($($containerClass+' .swiper-slide-active').index()+1 != 2 && $($containerClass+' .swiper-slide:nth-child(2) video').length > 0  && !$('html').hasClass('no-video') && !$onMobileBrowser) {
					
					$($containerClass+' .swiper-slide:last-child').find('.video-wrap video').get(0).play();
					$($containerClass+' .swiper-slide:last-child').find('.video-wrap video').get(0).pause();
					
					$($containerClass+' .swiper-slide:nth-child(2) video').get(0).pause();
					if($($containerClass+' .swiper-slide:nth-child(2) video').get(0).currentTime != 0 ) $($containerClass+' .swiper-slide:nth-child(2) video').get(0).currentTime = 0;
				}
				
				//also show duplicate slide if applicable
        if($($containerClass).attr('data-overall_style') != 'directional') {
          
    				////first
    				if(  $($containerClass+' .swiper-slide-active').index()+1 == $slideNum-1 ){

    					$($containerClass+' .swiper-slide:nth-child(1)').find('.content').children().each(function(i){
    	           
                 captionTransAnimation($(this),i,captionTransString); 

    					});
    	
    				}
    				
    				////last
    				if( $($containerClass+' .swiper-slide-active').index()+1 == 2 ){
    					$($containerClass+' .swiper-slide:nth-child('+ ($slideNum) + ')').find('.content').children().each(function(i){
    						captionTransAnimation($(this),i,captionTransString); 
    					});
    	
    				}


            //looped last going to first
            if(  $($containerClass+' .swiper-slide-active').index()+1 == $slideNum ){

               $($containerClass+' .swiper-slide:nth-child(2)').find('.content').children().each(function(i){

                    captionTransAnimation($(this),i,captionTransString); 
                });
            }

            //looped first going to last
            if(  $($containerClass+' .swiper-slide-active').index()+1 == 1 ){  

               $($containerClass+' .swiper-slide:eq(-2)').find('.content').children().each(function(i){

                    captionTransAnimation($(this),i,captionTransString); 
                });
            }
            
            
        }//directional

				
			}//if using loop
			
			//play video if there's one
			setTimeout(function(){ 
          playVideoBG($($containerClass + ' .swiper-slide-active').index() + 1, $containerClass); 
      },50);
				

			//fadeIn active slide

        if($($containerClass).attr('data-overall_style') != 'directional') {
    			$($containerClass+' .swiper-slide:nth-child('+ (currentSlide + 1) +')').find('.content').children().each(function(i){
    				
    				captionTransAnimation($(this),i,captionTransString);
            
    			});
        }
        
      

			
			previousSlide = $($containerClass + ' .swiper-slide-active').index();
			
			$captionTrans++;
			if($captionTrans == $('.swiper-wrapper').length) { $('div.first_video_slide').addClass('nulled') }


            //slider arrows with preview effect
            setTimeout(function(){

                if($('.slider-next').hasClass('next-arrow-move')) {
                    $('.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-next').trigger('mouseenter');
                } else if($('.slider-prev').hasClass('prev-arrow-move')) {
                    $('.nectar-slider-wrap[data-button-styling="btn_with_preview"] .swiper-container .slider-prev').trigger('mouseenter');
                }


               if($($containerClass).attr('data-button-styling') == 'btn_with_preview') {     
                 $($containerClass+' .swiper-slide').addClass('prev-high-z-index-static');
                 $($containerClass+' .swiper-slide.swiper-slide-active').removeClass('prev-high-z-index-static');
               }

            },175);

           
			
			
	} 
  
  
  function NSsplitLineText() {
		$('.nectar-slider-wrap[data-caption-trans="reveal_title"] .content h2').each(function(){
      
      var storedCSSOpacity = ($(this).parents('.swiper-slide-active').length > 0 && $(this).find('.word').length > 0 ) ? $(this).find('> .word').css('opacity') : '0';
        
      var textArr = $(this).text();
      textArr = textArr.trim();
      textArr = textArr.split(' ');

      $(this)[0].innerHTML = '';
      
      for(var i=0;i<textArr.length;i++) {
          $(this)[0].innerHTML += '<span class="word">'+ textArr[i] + '</span> ';
      }
      
      $(this).find('.word').each(function(){
        charming($(this)[0]);
      });
      
      $(this).find('[class*="char"]').wrapInner('<span class="char-inner" />');
      
      if($(this).parents('.swiper-slide-active').length > 0) {
        $(this).find('> .word').css('opacity', storedCSSOpacity);
      }
      
		});
		
	}
  
function charming(element, options) {
  options = options || {}
  element.normalize()
  var splitRegex = options.splitRegex

  var tagName = options.tagName || 'span'
  var classPrefix = options.classPrefix != null ? options.classPrefix : 'char'
  var count = 1

  function inject (element) {
    var parentNode = element.parentNode
    var string = element.nodeValue
    var split = splitRegex ? string.split(splitRegex) : string
    var length = split.length
    var i = -1
    while (++i < length) {
      var node = document.createElement(tagName)
      if (classPrefix) {
        node.className = classPrefix + count
        count++
      }
      node.appendChild(document.createTextNode(split[i]))
      node.setAttribute('aria-hidden', 'true')
      parentNode.insertBefore(node, element)
    }
    if (string.trim() !== '') {
      parentNode.setAttribute('aria-label', string)
    }
    parentNode.removeChild(element)
  }

  ;(function traverse (element) {
    // `element` is itself a text node.
    if (element.nodeType === 3) {
      return inject(element)
    }

    // `element` has a single child text node.
    var childNodes = Array.prototype.slice.call(element.childNodes) // static array of nodes
    var length = childNodes.length
    if (length === 1 && childNodes[0].nodeType === 3) {
      return inject(childNodes[0])
    }

    // `element` has more than one child node.
    var i = -1
    while (++i < length) {
      traverse(childNodes[i])
    }
  })(element)
}
  
  NSsplitLineText();
  //$(window).smartresize(NSsplitLineText);
  
  //caption trans helper
  var charCount = 0;
  function captionTransAnimation(obj,index,animationName) {
      
    if(animationName == 'reveal_title') {
      
      if(obj.parent().find('.word').length == 0) charCount = 0;
      
      if(index === 0 && obj.parent().find('.word').length > 0) {
        
        charCount = obj.find('.word > [class*="char"]').length;
        
        obj.find('.word').css('opacity','1');
  
        obj.find('.word > [class*="char"]').each(function(i){
          
          $(this).delay(i*18).transition({
            'transform' : 'translateY(0px)',
            'opacity' : '1'
          },700,'cubic-bezier(.25, 0.25, 0.1, 1)'); 	
          
          $(this).find('> span').delay(i*18).transition({
            'transform' : 'scale(1)',
          },700,'cubic-bezier(.25, 0.25, 0.1, 1)'); 	
          
        }); //char loop
          

        
      } else {

        obj.stop().delay(index*150 + (charCount*18)).transition({
          'opacity' : 1,
          'transform' : 'translateY(0)'
        },850,'cubic-bezier(.25, .25, 0.1, 1)'); 	
        
      }
      
      
    } else if(animationName == 'fade_in_from_bottom') {
      
      obj.stop().delay(index*130).transition({
        'opacity' : 1,
        'transform' : 'translateY(0)'
      },800,'cubic-bezier(.25, .25, 0.1, 1)'); 	
      
    }
    
  }
	
	//used in caption transition
	function playVideoBG(nthChild, containerClass){

		if($(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.video-wrap video').length > 0){ 
			
			if(!$('html').hasClass('no-video') && !$onMobileBrowser) $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.video-wrap video').get(0).play();
			
			if(!$(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-overlay.mejs-overlay-play').hasClass('playing') && $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-overlay.mejs-overlay-play').hasClass('mobile-played')) { $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-overlay.mejs-overlay-play').addClass('playing'); }
			if(!$(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-poster').hasClass('playing') && $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-poster').hasClass('mobile-played')) $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-poster').addClass('playing');
			
			var $that = $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-overlay.mejs-overlay-play');
			var $that2 = $(containerClass+' .swiper-slide:nth-child('+ (nthChild) +')').find('.mejs-poster');
			
			if($that.hasClass('playing') && $that.hasClass('mobile-played')) {
				setTimeout(function(){ $that.addClass('behind-buttons'); $that2.addClass('behind-buttons');},200);
			} else {
				$that.removeClass('behind-buttons'); $that2.removeClass('behind-buttons');
			}
		}
		
	}
	
	
	var $startingSlide = null;
	 

	
	function slideContentPos(){
		
		$('.swiper-wrapper').each(function(){
			
			//etxra space if first slider in section
			var $extraHeight = ($(this).parents('.nectar-slider-wrap').hasClass('first-section') || $(this).parents('.parallax_slider_outer').hasClass('first-section')) ? 30 : 0;

			var $sliderHeight = parseInt($(this).parents('.swiper-container').attr('data-height'));
      var $minimumHeight = ($(this).parents('.swiper-container').css('min-height')) ? parseInt($(this).parents('.swiper-container').css('min-height')) : 0;
      
      //min height addition
      if($minimumHeight > $sliderHeight) {
        $sliderHeight = $minimumHeight;
      }
			
			var $transparentHeader = ($('#header-outer[data-transparent-header="true"]').length > 0) ? $('#header-space').height() : 0 ;
			if($(this).parents('.first-section').length == 0 || window.innerWidth < 1000) { 
          $transparentHeader = null;
      } else if($transparentHeader != 0 && $('#header-outer[data-remove-border="true"]').length > 0){
          $transparentHeader = $transparentHeader/2
      }
      
      if($(this).parents('.swiper-container[data-bullet_position="right"][data-bullets="true"]').length > 0 && $transparentHeader > 0 || 
         $(this).parents('.swiper-container[data-bullet_position="left"][data-bullets="true"]').length > 0 && $transparentHeader > 0) {
           
        var secondaryHeaderHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0;
        $(this).parents('.swiper-container').find('.slider-pagination-wrap').css({
          'margin-top': ($transparentHeader + secondaryHeaderHeight)/2
        });
      }
			
			$(this).find('.swiper-slide').each(function(){
				
				var $contentHeight = $(this).find('.content').height();
				var $contentItems = $(this).find('.content > *').length;

        $contentHeight2 = 0;
        $(this).find('.content > *').each(function(){
            $contentHeight2 += $(this).height() + parseInt($(this).css('margin-bottom')) + parseInt($(this).css('padding-bottom')); 
        });
				
				if($(this).find('.content > *').css('padding-top') == '25px') $contentHeight = $contentHeight - 25*$contentItems;
				
				if($(this).attr('data-y-pos') == 'top' && window.innerWidth > 690 ){
          var secondaryHeaderHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0;
					var $topHeight = ($transparentHeader > 0) ? $transparentHeader + secondaryHeaderHeight + 60 : 60;
					$(this).find('.content').css('top', $topHeight + 'px');
				} 
				else if($(this).attr('data-y-pos') == 'middle' || window.innerWidth <= 690) {
					$(this).find('.content').css('top', ((($sliderHeight + $transparentHeader)/2) - ($contentHeight2/2)) + 'px');
				} 
				else {

            $(this).find('.content').css({'bottom': '75px', 'top': 'auto'});

				}
			});
		});
	}
	
 
	
	function showSliderControls() {
		$('.swiper-container .slider-prev, .swiper-container .slider-next, .slider-pagination').animate({'opacity':1},550,'easeOutSine');
	}
	
	

	function sliderLoadIn(slider,index) { 
		
		slider.find('.nectar-slider-loading').fadeOut(800,'easeInOutExpo');
    
    setTimeout(function(){
      slider.find('.nectar-slider-loading').css('display','none');
    },1000);
    
		$('.nectar-slider-wrap.first-section .swiper-container .swiper-wrapper .swiper-slide').removeClass('not-loaded');
		
		slider.find('span.ie-fix').remove();
		
    //ar visualized start
    if(slider.find('.swiper-container[data-bullet_style="see_through_ar_visualized"][data-bullets="true"]').length > 0) {
      slider.find('.swiper-active-switch').removeClass('ar-vis');
      setTimeout(function(){
        slider.parent().find('.swiper-active-switch').addClass('ar-vis');
      },50);  
    }
    
		///make sure to init smooth scrolling after slider height exists
		var $smoothActive = $('body').attr('data-smooth-scrolling'); 
		if( $smoothActive == 1 && $(window).width() > 690 && $('body').outerHeight(true) > $(window).height() && $('#ascrail2000').length == 0 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){ niceScrollInit(); resizeToCover(); }
		
		//check for sliders in tabbed
		sliderLoadedLength++;
		if($('.tabbed').find('.swiper-container').length > 0 && sliderLoadedLength == sliderLength) {
			setTimeout(function(){ $('.tabbed > ul li:first-child a').click(); }, 200);
		}

        if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) { autorotateInit(slider,index); }
		
	}
	

	
	//play video user is hovering over
	$('body').on('mouseover','.swiper-slide', function(){
		if(!$onMobileBrowser && $(this).find('video').length > 0 && $(this).find('video').get(0).paused == true && $animating == false){
			$(this).find('video').get(0).play();
		}
	});

	
	//mobile play event
	$('body').on('click', '.mejs-overlay.mejs-overlay-play',function(){
		$(this).toggleClass('playing');
		$(this).addClass('mobile-played');
		
		$(this).parent().find('.mejs-poster').toggleClass('playing');
		$(this).parent().find('.mejs-poster').addClass('mobile-played');
		
		var $that = $(this);
		var $that2 = $(this).parent().find('.mejs-poster');
		
		if($(this).hasClass('playing') && $(this).hasClass('mobile-played')) {
			
			setTimeout(function(){ $that.addClass('behind-buttons'); $that2.addClass('behind-buttons'); },200);
		} else {
			setTimeout(function(){ $that.removeClass('behind-buttons'); $that2.removeClass('behind-buttons'); },1);
		}
	}); 
	


	var $sliderHeight = parseInt($(this).find('.swiper-container').attr('data-height'));
	var portfolioHeaderHeight = ($('.project-title.parallax-effect').length > 0) ? 100 : 0;
	
    function autorotateInit(slider,num) {
        
    	//$('.nectar-slider-wrap').each(function(i){
    		var $autoplayVal = slider.attr('data-autorotate');
    		var $that = slider;
    		var $sliderNum = num;
    		
    		if(typeof $autoplayVal !='undefined' && $autoplayVal.length != '0' && parseInt($autoplayVal)) { 
    			nectarSlideRotateInit(slider,$autoplayVal,$sliderNum);
          
    		}	

    	//});
    }
	
  
	function nectarSlideRotateInit(slider,interval,sliderNum){

		autoplay[sliderAutoplayCount] = { interval: setInterval(function(){ nectarSlideRotate(slider, sliderNum); } ,interval), nectarSliderNum: sliderNum, autoplayNum: sliderAutoplayCount, nectarSliderID: slider.attr('id') };
		
		$('#'+slider.attr('id')).attr('autoplay-id',sliderAutoplayCount);
		
    //ar visualized style
    if( $('#'+slider.attr('id')).find('[data-bullet_style="see_through_ar_visualized"]').length > 0 ) {
      $('#'+slider.attr('id')).find('.slider-pagination svg circle.time').css('transition', 'stroke-dashoffset '+ (parseInt(interval) + 100) +'ms linear, stroke 0.2s ease');
    }
    
    var sliderClosure = slider;
    var sliderAutoplayCountClosure = sliderAutoplayCount;
    var intervalClosure = interval;
    var sliderNumClosure = sliderNum;
    
    
		$('#'+slider.attr('id') + ' a.slider-prev, #'+slider.attr('id') + ' a.slider-next, #' + slider.attr('id') + ' .slider-pagination span:not(.swiper-active-switch)').click(function(e){ 
			
      
      nectarSlideRotateReset(sliderClosure, sliderAutoplayCountClosure, intervalClosure, sliderNumClosure);
        
      //if(typeof e.clientY != 'undefined'){
			//	clearInterval(autoplay[$('#'+slider.attr('id')).attr('autoplay-id')]); 

              //class for directional
            //  if( $('#'+slider.attr('id')).parent().hasClass('parallax_slider_outer') && $('#'+slider.attr('id')).attr('data-overall_style') == 'directional' ) {
            //       $('#'+slider.attr('id')).parent().addClass('user-stopped');
            //  }
			//}
		});
		
		sliderAutoplayCount++;
	}
  
  function nectarSlideRotateReset(slider, sliderAutoplayCount, interval,sliderNum){

    slider = (typeof slider.activeIndex === 'undefined') ? slider : $(slider.container).parent();
    
    sliderAutoplayCount = (typeof sliderAutoplayCount !== 'undefined' && typeof sliderAutoplayCount === 'number') ? sliderAutoplayCount : 'unknown';
    
    if( sliderAutoplayCount === 'unknown') {

      $.each(autoplay,function(k,v){

        if( $('body').find('#' + v.nectarSliderID).length > 0 && v.nectarSliderID == $(slider).attr('id') ) {
          
          sliderAutoplayCount = v.autoplayNum;
          interval = ($('body').find('#' + v.nectarSliderID).attr('data-autorotate').length > 0 && parseInt($('body').find('#' + v.nectarSliderID).attr('data-autorotate')) > 1000) ? $('body').find('#' + v.nectarSliderID).attr('data-autorotate') : 4000;
          sliderNum = v.nectarSliderNum;

        }
        
      });
    }
    

    
    //only reset and restart when slider was found and link was made
    if( sliderAutoplayCount !== 'unknown') {
  		clearInterval(autoplay[$('#'+slider.attr('id')).attr('autoplay-id')].interval); 
      autoplay[sliderAutoplayCount].interval = setInterval(function(){ nectarSlideRotate(slider, sliderNum); } ,interval);
    }


	}
  
  
  
  var resetTouchIndex = 1;
  var resetTouchIndexLast = 'unknown';
  
  function nectarSlideRotateResetTouch(slider){
    
    
    //if user has not dragged any significant distance
    if(Math.abs(slider.touches.diff) < 15) { return } 
    
    //first time run set last index
    if(resetTouchIndexLast == 'unknown' && $(slider.container).is('[data-loop="true"]')) {
      resetTouchIndexLast = 1;
    } else if(resetTouchIndexLast == 'unknown') {
      resetTouchIndexLast = 0;
    }
    
    resetTouchIndex = slider.activeIndex;
    
    nectarSlideRotateReset(slider);
    
    //if going back to same slide on touch - reset visualization
    if($(slider.container).is('[data-bullets="true"][data-bullet_style="see_through_ar_visualized"]') && resetTouchIndex == resetTouchIndexLast) {
      var dotIndexStored = $(slider.container).find('.ar-vis').index();

      $(slider.container).find('.ar-vis').removeClass('ar-vis');
      setTimeout(function(){
        $(slider.container).find('.slider-pagination > span:nth-child('+ (dotIndexStored + 1) +')').addClass('ar-vis');
      },50);
      
      
    }  
    
    resetTouchIndexLast = slider.activeIndex;

	}
  

  
	
	function nectarSlideRotate(slider, sliderNum){

        if($('#nectar_fullscreen_rows').length > 0) {
            if($(slider).parents('.wpb_row.active').length == 0) {
                return true;
            }
        }
        //non looped
		if($nectarSliders[sliderNum].activeIndex + 1 < $(slider).find('.swiper-wrapper > div.swiper-slide').length){
			
            //btn with preview hover effect clear
            if($(slider).attr('data-button-styling') == 'btn_with_preview') {    
                $(slider).find('.swiper-slide').removeClass('high-z-index').removeClass('prev-high-z-index');
                $(slider).find('.swiper-slide').removeClass('next-move').removeClass('prev-move');
            }

            //classic style
            if($(slider).attr('data-overall_style') != 'directional') {
                $nectarSliders[sliderNum].swipeNext();
            } else {
                //directional based
                $($nectarSliders[sliderNum].container).find('.slider-next').trigger('click');
            }
    
		} else {
            //looped sliders
            if($(slider).find('.swiper-container').is("[data-loop]") && $(slider).find('.swiper-container').attr('data-loop') == 'true') {

                //classic style
                if($(slider).attr('data-overall_style') != 'directional') {
                    $nectarSliders[sliderNum].swipeNext();
                } else {
                    //directional based
                   $($nectarSliders[sliderNum].container).find('.slider-next').trigger('click');
                } 

            } 
            //regular sliders
            else {

                if($(slider).attr('data-overall_style') != 'directional') {
                    $nectarSliders[sliderNum].swipeTo(0,550);
                } else {
                    $(slider).find('.swiper-container').addClass('directional-trans-prev');
                    $nectarSliders[sliderNum].swipeTo(0,550);
                    //call the dark/light slide coloring half way through transition
                    var $timeout2;     
                    clearTimeout($timeout2);
                    $timeout2 = setTimeout(function(){ onChangeStart($nectarSliders[sliderNum]); } ,100);
                }
               
            }
		}
	}
	
	function clearAutoplay(e){ 
		var $autoplayVal = $('#'+$(e.container).parent().attr('id')).attr('data-autorotate');

		if(typeof $autoplayVal !='undefined' && $autoplayVal.length != '0' && parseInt($autoplayVal)) {

			clearInterval(autoplay[$('#'+$(e.container).parent().attr('id')).attr('autoplay-id')].interval); 
		}
	}
	 
  
	function onChangeStart(e){
        //performance
        //$('.swiper-wrapper').css('will-change','transform');

		//cache slider obj
		var $obj = e;
		
		$animating = true;
		
		 var $slideNum = $($obj.container).find('.swiper-wrapper > div').length;
		
        //one for effects and one for basic slide arrow count 
		$activeIndex = ($($obj.container).attr('data-loop') == 'true') ? $obj.activeIndex + 1: $obj.activeIndex+1;
        $activeIndex2 = ($($obj.container).attr('data-loop') == 'true') ? $obj.activeIndex: $obj.activeIndex+1;

		//dark slide header 
        if($($obj.container).parent().attr('data-overall_style') != 'directional') {
            if( $($obj.container).parents('.first-section').length > 0 && $($obj.container).find('.swiper-slide-active[data-color-scheme="dark"]').length > 0 ||
                $('#nectar_fullscreen_rows').length > 0 && $($obj.container).find('.swiper-slide-active[data-color-scheme="dark"]').length > 0) {
                $('#header-outer').addClass('dark-slide');
            } else if( $($obj.container).parents('.first-section').length > 0 || $('#nectar_fullscreen_rows').length > 0) {
                $('#header-outer').removeClass('dark-slide');
            }
        } 
        ////directional trans - will be called early so need to adjust the slide being looked for
        else {
            if($($obj.container).hasClass('directional-trans-next')) {
 
                if($($obj.container).parents('.first-section').length > 0 && $($obj.container).find('.swiper-slide-active').next('.swiper-slide').is('[data-color-scheme="dark"]') ) {
                    $('#header-outer').addClass('dark-slide');
                    //nav items animation 
                    if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.dark').css('display','inline').transition({'margin-left': '0'},0).transition({ 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left': '0'},0);
                            $(this).find('span.light').css('display','inline-block').transition({'margin-left': $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left': '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                } else {
                    $('#header-outer').removeClass('dark-slide');
                     //nav items animation 
                     if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.light').css('display','inline').transition({'margin-left': '0'},0).css('display','inline').transition({'width' :  $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left': '0'},0);
                            $(this).find('span.dark').css('display','inline-block').transition({'margin-left': $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left':  '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                }
            } 

            else if($($obj.container).hasClass('directional-trans-prev')) {
                 if( $($obj.container).parents('.first-section').length > 0 && $($obj.container).find('.swiper-slide-active').prev('.swiper-slide').is('[data-color-scheme="dark"]') ) {
                    $('#header-outer').addClass('dark-slide');
                     //nav items animation 
                     if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.dark').css('display','inline').transition({'margin-left': '0'},0).transition({ 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left': '0'},0);
                            $(this).find('span.light').css('display','inline-block').transition({'margin-left':  $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left':  '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                } else {
                    $('#header-outer').removeClass('dark-slide');
                     //nav items animation 
                     if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.light').css('display','inline').transition({'margin-left': '0'},0).transition({'width' : $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left': '0'},0);
                            $(this).find('span.dark').css('display','inline-block').transition({'margin-left': $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left': '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                }
            }

            else if($($obj.container).hasClass('directional-trans-current')) {
                 if( $($obj.container).parents('.first-section').length > 0 && $($obj.container).find('.swiper-slide-active').is('[data-color-scheme="dark"]') ) {
                    $('#header-outer').addClass('dark-slide');
                     //nav items animation 
                     if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.dark').css('display','inline').transition({'margin-left': '0'},0).transition({ 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left': '0'},0);
                            $(this).find('span.light').css('display','inline-block').transition({'margin-left':  $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left':  '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                } else {
                    $('#header-outer').removeClass('dark-slide');
                     //nav items animation 
                     if($('body.mobile').length == 0) { 
                        $('#header-outer nav > ul.sf-menu > li > a, #header-outer nav > ul.buttons > li#search-btn a, #header-outer nav > ul.buttons > li.slide-out-widget-area-toggle a > span, #header-outer .cart-icon-wrap, header#top #logo').each(function(){
                            var $itemWidth = parseInt($(this).find('span.original').attr('data-w'));

                            $(this).find('span.light').css('display','inline').transition({'margin-left': '0'},0).transition({'width' : $itemWidth }, 325, 'easeInOutQuart');
                            $(this).find('span.light > span').transition({'margin-left': '0'},0);
                            $(this).find('span.dark').css('display','inline-block').transition({'margin-left': $itemWidth, 'width' :  '0' }, 325, 'easeInOutQuart');
                            $(this).find('span.dark > span').transition({'margin-left': '-' + ($itemWidth) + 'px', 'width' :  $itemWidth }, 325, 'easeInOutQuart');
                        });
                    }
                }
            }


        }

        //light and dark controls
        if($($obj.container).parent().attr('data-overall_style') != 'directional') {
            if($($obj.container).find('.swiper-slide:nth-child('+ ($activeIndex) +')').attr('data-color-scheme') == 'dark') {
                $($obj.container).find('.slider-pagination').addClass('dark-cs');
                $($obj.container).find('.slider-prev, .slider-next').addClass('dark-cs');
            } else {
               $($obj.container).find('.slider-pagination').removeClass('dark-cs');
               $($obj.container).find('.slider-prev, .slider-next').removeClass('dark-cs');
            }
        } 
        ////directional trans - will be called early so need to adjust the slide being looked for
        else {
            if($($obj.container).hasClass('directional-trans-next')) {
                if($($obj.container).find('.swiper-slide:nth-child('+ ($activeIndex+1) +')').attr('data-color-scheme') == 'dark') {
                    $($obj.container).find('.slider-pagination').addClass('dark-cs');
                    $($obj.container).find('.slider-prev, .slider-next').addClass('dark-cs');
                } else {
                   $($obj.container).find('.slider-pagination').removeClass('dark-cs');
                   $($obj.container).find('.slider-prev, .slider-next').removeClass('dark-cs');
                }
            } else if($($obj.container).hasClass('directional-trans-prev')) {
                if($($obj.container).find('.swiper-slide:nth-child('+ ($activeIndex-1) +')').attr('data-color-scheme') == 'dark') {
                    $($obj.container).find('.slider-pagination').addClass('dark-cs');
                    $($obj.container).find('.slider-prev, .slider-next').addClass('dark-cs');
                } else {
                   $($obj.container).find('.slider-pagination').removeClass('dark-cs');
                   $($obj.container).find('.slider-prev, .slider-next').removeClass('dark-cs');
                }
            
            } else if($($obj.container).hasClass('directional-trans-current')) {
                if($($obj.container).find('.swiper-slide:nth-child('+ ($activeIndex) +')').attr('data-color-scheme') == 'dark') {
                    $($obj.container).find('.slider-pagination').addClass('dark-cs');
                    $($obj.container).find('.slider-prev, .slider-next').addClass('dark-cs');
                } else {
                   $($obj.container).find('.slider-pagination').removeClass('dark-cs');
                   $($obj.container).find('.slider-prev, .slider-next').removeClass('dark-cs');
                }
            
            }
        }


        $($obj.container).find('.swiper-slide .video-texture').removeClass('no-trans').removeClass('light-overlay').removeClass('dark-overlay').removeClass('half-dark-overlay').removeClass('half-light-overlay');

		//add slide counts
		$($obj.container).find('.slider-prev .slide-count .slide-current').html( $activeIndex2 );
		$($obj.container).find('.slider-next .slide-count .slide-current').html( $activeIndex2 );
		
		if($($obj.container).attr('data-loop') == 'true'){
			//duplicate first slide
			if( $($obj.container).find('.swiper-slide-active').index()+1 == 1) { 
				$($obj.container).find('.slider-next .slide-count .slide-current, .slider-prev .slide-count .slide-current').html( $slideNum - 2 );
			}
			//duplicate last slide
			else if( $($obj.container).find('.swiper-slide-active').index()+1 == $slideNum) {
				$($obj.container).find('.slider-next .slide-count .slide-current, .slider-prev .slide-count .slide-current').html( 1 );
			}
		}
			
		if($obj.activeIndex >= 10) { $($obj.container).find('.slider-next .slide-count .slide-current').addClass('double-digits'); } else {
			$($obj.container).find('.slider-next .slide-count .slide-current').removeClass('double-digits');
		}
		
		
		$($obj.container).find('.swiper-slide:not(".swiper-slide-active")').each(function(){
			if($(this).find('.video-wrap video').length > 0) { 

				//if($(this).find('.video-wrap video').get(0).currentTime != 0 ) $(this).find('.video-wrap video').get(0).currentTime = 0;
			}
		});
		
		
		//don't allow swiping on duplicate transition
		if($($obj.container).attr('data-loop') == 'true'){
			if($obj.previousIndex == 1 && $obj.activeIndex == 0 || $obj.previousIndex == $slideNum - 2 && $obj.activeIndex == $slideNum - 1 ){
				$('.swiper-slide').addClass('duplicate-transition');	
			}
		}
		
    
    //reset ken_burns
    
    var sliderIndex = 0;
    
    slideAnimationQueues.forEach(function(val,index) {
      if(typeof val === 'undefined') return;
      
      if( val.sliderID == $($obj.container).parent().attr('id') )  {
        sliderIndex = index;
      }

    });
    

    if($($obj.container).parent().is('[data-bg-animation="ken_burns"]') && $($obj.container).parents('.nectar-slider-wrap[data-overall_style="directional"]').length == 0 ) {
        $($obj.container).find('.swiper-slide').addClass('ken-burns');
        clearTimeout(slideAnimationQueues[sliderIndex].kenBurnsInterval);
        slideAnimationQueues[sliderIndex].kenBurnsInterval = setTimeout(function(){
          	$($obj.container).find('.swiper-slide:not(".swiper-slide-active")').removeClass('ken-burns');
        },600);
    }

    
    var nsTimeout = ($($obj.container).parents('.nectar-slider-wrap[data-transition="fade"]').length > 0) ? 350 : 525;
    
		clearTimeout(slideAnimationQueues[sliderIndex].captionInterval);
    if($($obj.container).parents('.nectar-slider-wrap[data-overall_style="directional"]').length == 0) {
      
  		slideAnimationQueues[sliderIndex].captionInterval = setTimeout(function(){ 
        
        $animating = false; 
        
        if( $($obj.container).attr('data-loop') == 'true' ) {
            $('.swiper-slide').removeClass('duplicate-transition');
            
            $.each($nectarSliders,function(k,v){
              $nectarSliders[k].updateActiveSlide();
              $nectarSliders[k].fixLoop(); 
            });
        }
        
        captionTransition($obj);

      },nsTimeout);
      
    }


	}
	
	


	//functions to add or remove slider for webkit parallax fix
	function hideSlider() {

		if( $(window).scrollTop()/($sliderHeight + portfolioHeaderHeight + 125) >= 1){
			$('.parallax_slider_outer .nectar-slider-wrap, .project-title.parallax-effect').css('visibility','hidden').hide();
			$('.parallax_slider_outer').removeClass('element-in-view').addClass('element-out-of-view');

            //directional slider autorotate clear
            if($('.parallax_slider_outer .nectar-slider-wrap[data-overall_style="directional"]').length > 0) {
                var $autoplayVal = $('.parallax_slider_outer .nectar-slider-wrap[data-overall_style="directional"]').attr('data-autorotate');
                if(typeof $autoplayVal !='undefined' && $autoplayVal.length != '0' && parseInt($autoplayVal)) { 
                    clearInterval( autoplay[$('.parallax_slider_outer .nectar-slider-wrap').attr('autoplay-id')].interval ); 
                }
            }

			//$(window).on('scroll',showSlider);
			//$(window).off('scroll',hideSlider);
		}
	}
	
	function showSlider() {
		if( $(window).scrollTop()/($sliderHeight + portfolioHeaderHeight + 125) <= 1){

			$('.parallax_slider_outer .nectar-slider-wrap, .project-title.parallax-effect').css('visibility','visible').show();
			$('.parallax_slider_outer').addClass('element-in-view');

            //directional slider autorotate restart
            if($('.parallax_slider_outer .nectar-slider-wrap[data-overall_style="directional"]').length > 0 && $('.parallax_slider_outer.element-out-of-view').length > 0 && $('.parallax_slider_outer.user-stopped').length == 0) {
                var $autoplayVal = $('.parallax_slider_outer .nectar-slider-wrap[data-overall_style="directional"]').attr('data-autorotate');
                if(typeof $autoplayVal !='undefined' && $autoplayVal.length != '0' && parseInt($autoplayVal)) { 
                    nectarSlideRotateInit($('.parallax_slider_outer .nectar-slider-wrap'),$autoplayVal,0);
                } 
            }  

            $('.parallax_slider_outer').removeClass('element-out-of-view');

			//resize iframes incase user resized window when out of view
			$('.swiper-slide iframe[data-aspectRatio]').each(function() {
			 	var newWidth = $(this).parent().width();
				var $el = $(this);
				if($(this).is(':visible')) $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));

			});
			
			
			nsSliderContentResize();
			
			//show content on current slide - could have been hidden from autorotate when slider was hidden
			if($('.parallax_slider_outer').length > 0) {
				var fromLeft = Math.abs(parseInt($('.parallax_slider_outer .nectar-slider-wrap .swiper-wrapper').css('left')));
				var currentSlide = Math.round(fromLeft/$('.parallax_slider_outer .nectar-slider-wrap .swiper-slide').width()); 

				$('.parallax_slider_outer .swiper-wrapper .swiper-slide:eq(' + currentSlide + ')').find('.content').children().each(function(i){
					$(this).stop(true,true).css({ 'opacity' : 1, 'transform' : 'translateY(0)' });	
				});
				
				$('.parallax_slider_outer .swiper-wrapper .swiper-slide:eq(' + currentSlide + ')').addClass('autorotate-shown');
			}
			
			//$(window).on('scroll',hideSlider);
			//$(window).off('scroll',showSlider);
				
		} 
	} 
	
	function sliderCorrectDisplayCheck() {
		if( $(window).scrollTop()/($sliderHeight + portfolioHeaderHeight + 125) >= 1){
			hideSlider();
		}
		//$(window).unbind('scroll',sliderCorrectDisplayCheck);
	}
	

    var $smoothActive = $('body').attr('data-smooth-scrolling'); 
    
	// Create cross browser requestAnimationFrame method:
    window.requestAnimationFrame = window.requestAnimationFrame
     || window.mozRequestAnimationFrame
     || window.webkitRequestAnimationFrame
     || window.msRequestAnimationFrame
     || function(f){setTimeout(f, 1000/60)}


    var lastPosition = -1;
    var $headerRemoveStickyness = ($('body[data-hhun="1"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length > 0) ? 1 : 0;

    if($headerRemoveStickyness) {
        var $multiplier1 =  0.55;
        var $multiplier2 =  0.45;
    } else {
        var $multiplier1 =  ($('body[data-hhun="1"]').length > 0) ? 0.45: 0.40;
        var $multiplier2 = ($('body[data-hhun="1"]').length > 0) ? 0.09: 0.19;
    }
    

    var $parallaxHeaderHUN = ($('#header-outer[data-transparency-option="1"]').length > 0) ? 0.49: 0.4;
  
  var parallaxSliderEl = $('.parallax_slider_outer.first-section');
  var nectarDisableForSafari = (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1);
  var $scrollTop = $(window).scrollTop();
  var parallaxSliderStyle = (parallaxSliderEl.find('> .nectar-slider-wrap[data-parallax-style="bg_only"]').length > 0) ? 'bg-only' : 'both';
  
	function parallaxScroll(){
      
        $scrollTop = $(window).scrollTop();
        //if on mobile, auto RAF
        if($onMobileBrowser){
          $('.parallax_slider_outer .nectar-slider-wrap .slide-bg-wrap').css({ 'transform': 'translate3d(0, '+$scrollTop*0.25+'px, 0)' });
          window.requestAnimationFrame(parallaxScroll);
        } 
        
        else if(parallaxSliderStyle == 'bg-only'){
          $('.parallax_slider_outer .nectar-slider-wrap .slide-bg-wrap').css({ 'transform': 'translate3d(0, '+$scrollTop*0.25+'px, 0)' });
        } 
        
        else {
          
            // Avoid calculations if not needed
            if (lastPosition == $scrollTop || $('.parallax_slider_outer.element-in-view').length == 0 || $('.ocm-effect-wrap.material-ocm-open').length > 0 ) {
               // window.requestAnimationFrame(parallaxScroll);
                return false;
            } else lastPosition = $scrollTop;

            if($smoothActive == 1 && nectarDisableForSafari) { return false; }
            
            if( $('body[data-hhun="1"]').length > 0  && !$('#header-outer').hasClass('side-widget-open') && !$('#header-outer .slide-out-widget-area-toggle a').hasClass('animating') ) { 
                
                $('#header-outer.parallax-contained').css({ 'transform': 'translateY('+$scrollTop*-$parallaxHeaderHUN+'px)' });
            }
            
            $('.parallax_slider_outer.first-section .nectar-slider-wrap').css({ 'transform': 'translateY('+$scrollTop*-$multiplier1+'px)' });
            
            $('.parallax_slider_outer.first-section .swiper-slide:not(".static"):not(".caption-no-fade") .content, .nectar-slider-wrap .swiper-container .slider-next, .nectar-slider-wrap .swiper-container .slider-prev, .swiper-wrapper .slider-down-arrow').css({ 'transform': 'translateY('+$scrollTop*-$multiplier2+'px)' });
            
            if(!$headerRemoveStickyness) {
                $('.parallax_slider_outer.first-section .swiper-slide:not(".static"):not(".caption-no-fade") .content, .nectar-slider-wrap .swiper-container .slider-next, .nectar-slider-wrap .swiper-container .slider-prev, .swiper-wrapper .slider-down-arrow').css({ 'opacity': 1-($scrollTop/($sliderHeight-120)) });
            }

            $('#full_width_portfolio .project-title.parallax-effect').css({ 'transform': 'translateY('+ $scrollTop*-$multiplier1+'px)' });   
            
        }

        
    }

    function sliderParallaxUpdateHeight(){
        $sliderHeight = parseInt($('.parallax_slider_outer.first-section .swiper-container').attr('data-height'));
        
        /*    
        $('.project-title').css({
            'top': $headerNavSpace + $adminBarHeight + 'px',
            'width' : $('#ajax-content-wrap').width()
        }); */ 
    }
	
	
	var $sliderHeight = parseInt($('.parallax_slider_outer.first-section .swiper-container').attr('data-height'));
	var $adminBarHeight = ($('#wpadminbar').length > 0) ? 28 : 0;
	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').height();

	function sliderParallaxInit() {
		

		if($('#portfolio-extra').length > 0 && $('#full_width_portfolio').length == 0) { return false; }

		//$(window).on('scroll.nsParallaxScroll',parallaxScroll); 
        $('.parallax_slider_outer').addClass('element-in-view');
        
        /*
        if(!$onMobileBrowser) {
          window.addEventListener('scroll', function(){ 
              window.requestAnimationFrame(parallaxScroll);
          }, false);
        }
        
        parallaxScroll();
      
    
    if($('.parallax_slider_outer > [data-parallax-style="bg_only"]').length > 0) {
      parallaxBGOnlySizing();
      $(window).resize(parallaxBGOnlySizing);
    }  
    
    function parallaxBGOnlySizing() {
      var slideTopOffset = $('.parallax_slider_outer > [data-parallax-style="bg_only"] .swiper-slide').offset().top;
      
      $('.parallax_slider_outer > [data-parallax-style="bg_only"] .swiper-slide .image-bg, .parallax_slider_outer > [data-parallax-style="bg_only"] .swiper-slide .slide-bg-overlay').css({'height':'calc(100% + ' + slideTopOffset/3 + 'px)', 'top': '-' + slideTopOffset/6 + 'px'});
      $('.parallax_slider_outer > [data-parallax-style="bg_only"] .swiper-slide video').css({'top': '-' + slideTopOffset/6 + 'px'});
    }
    */
    
		//hide slider to not mess up parallax section
		var portfolioHeaderHeight = ($('.project-title.parallax-effect').length > 0) ? 100 : 0;
		
		function displayParallaxSliderInit() {
	    	if( $(window).scrollTop()/($sliderHeight + portfolioHeaderHeight + 90) >= 1){
          //$(window).off('scroll.nsVisibilityHide');
		    	//$(window).on('scroll.nsVisibilityHide', hideSlider);
		    	//$(window).on('scroll',showSlider);
		    } else {
          //$(window).off('scroll.nsVisibilityShow');
		    	//$(window).on('scroll.nsVisibilityShow', showSlider);
		    	//$(window).off('scroll', hideSlider);
		    }
	    }
      
      if(!$onMobileBrowser) {
        
    		displayParallaxSliderInit();
    		
        //$(window).off('scroll.nsCorrectDisplayCheck');
    		//$(window).on('scroll.nsCorrectDisplayCheck',sliderCorrectDisplayCheck);
    		
    					 
    		//$('body:not(".single") .page-header-no-bg, body:not(".single") #page-header-wrap, body:not(".single") #page-header-bg').remove();
    		
    		/*$('.project-title').addClass('parallax-effect').css({
    			'top': $headerNavSpace + $adminBarHeight + 'px',
          'width' : $('#ajax-content-wrap').width()
    		});*/ 
    		
    		//caption alignment if portfolio fullwidth parallax
    		if($('.project-title.parallax-effect').length > 0) {
    			$('.parallax_slider_outer.first-section .swiper-slide .content, .nectar-slider-wrap.first-section .swiper-slide .content').css('margin-top','0px');
    			$('.swiper-container .slider-prev, .swiper-container .slider-next').css('margin-top','-28px');
    		}
    		
    		//if using wooCommerce sitewide notice
    		if($('.demo_store').length > 0) $('.project-title.parallax-effect').css('margin-top','-25px');
    		
    		if($('#full_width_portfolio').length > 0){
    			$('.parallax_slider_outer.first-section').css('margin-top','93px');
    		}
    		
            $(window).off('resize.nsHeightUpdate');
    		      $(window).on('resize.nsHeightUpdate',sliderParallaxUpdateHeight);
    }
		
	} 
	
	//parallax
    function parallaxCheck(){

        $(window).off('scroll.nsParallaxScroll'); 

    	if($('.parallax_slider_outer').length > 0){
    		sliderParallaxInit();
           if($('body[data-hhun="1"]').length > 0 && !$('#header-outer[data-remove-fixed="1"]').length > 0)  $('#header-outer').addClass('parallax-contained');
    		
    	} else if($('.parallax_slider_outer').length > 0 && navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
    		
        /*
        $('.project-title').addClass('parallax-effect').css({
    			'top': $headerNavSpace + $adminBarHeight + 'px',
          'width' : $('#ajax-content-wrap').width() 
    		});

        $('body:not(".single") .page-header-no-bg, body:not(".single") #page-header-wrap, body:not(".single") #page-header-bg').remove();
        */ 
    	}  else {
             $('.project-title').css({'top':'0'});
        }
    }
	parallaxCheck();

	function niceScrollInit(){
    
    if(!$().niceScroll) { return; }
    
		$("html").niceScroll({
			scrollspeed: 60,
			mousescrollstep: 40,
			cursorwidth: 15,
			cursorborder: 0,
			cursorcolor: '#303030',
			cursorborderradius: 6,
			autohidemode: false,
			horizrailenabled: false
		});
		
		if($('#boxed').length == 0){
			$('body, body #header-outer, body #header-secondary-outer, body #search-outer').css('padding-right','16px');
		}
		
		$('html').addClass('no-overflow-y');
	}
	
	//pause video backgrounds if popup video player is started
	$('.portfolio-items a.pp:contains(Video), .swiper-container .buttons a.pp').click(function(){
		$('.swiper-slide').each(function(){
			if($(this).find('.video-wrap video').length > 0) { 
				$(this).find('.video-wrap video').get(0).pause();
			}
		});
	});
	
	
	//solid button hover effect
	$.cssHooks.backgroundColor = {
	    get: function(elem) {
	        if (elem.currentStyle)
	            var bg = elem.currentStyle["backgroundColor"];
	        else if (window.getComputedStyle)
	            var bg = document.defaultView.getComputedStyle(elem,
	                null).getPropertyValue("background-color");
	        if (bg.search("rgb") == -1)
	            return bg;
	        else {
	            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	            function hex(x) {
	                return ("0" + parseInt(x).toString(16)).slice(-2);
	            }
	            if(bg) {
	            	return "#" + hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
	            }
	        }
	    }
	}
	
	function shadeColor(color, shade) {
	    var colorInt = parseInt(color.substring(1),16);
	
	    var R = (colorInt & 0xFF0000) >> 16;
	    var G = (colorInt & 0x00FF00) >> 8;
	    var B = (colorInt & 0x0000FF) >> 0;
	
	    R = R + Math.floor((shade/255)*R);
	    G = G + Math.floor((shade/255)*G);
	    B = B + Math.floor((shade/255)*B);
	
	    var newColorInt = (R<<16) + (G<<8) + (B);
	    var newColorStr = "#"+newColorInt.toString(16);
	
	    return newColorStr;
	}
	
    function sliderbuttonHoverEffect() {
    	$('body:not([data-button-style*="shadow"]) .swiper-slide').each(function(){
    		$(this).find('.solid_color').each(function(){
    			var $currentColor = $(this).find('a').css('background-color');
    			var $hoverColor = shadeColor($currentColor, -16);
    			$(this).find('a').hover(function(){
    				$(this).attr('style','background-color:'+$hoverColor+'!important;');
    			},function(){
    				$(this).attr('style','');
    			});
    		});
    	});
	}
    sliderbuttonHoverEffect();


  


	
	//slider down arrow
	var logoHeight = parseInt($('#header-outer').attr('data-logo-height'));
	//headerPadding/shinkNum moved up before fullscreen calcs
	
	headerPadding = headerPadding - headerPadding/1.8;
	
	$('body').on('click','.slider-down-arrow',function(){
		
    //within full page row
    if($(this).parents('#nectar_fullscreen_rows').length > 0) {
      if($('#fp-nav li .active').parent().next('li').length > 0) {
        $('#fp-nav li .active').parent().next('li').find('a').trigger('click');
      }
      return;
    }
    
		var $currentSlider = $(this).parents('.swiper-container');
		var $topDistance = $currentSlider.attr('data-height');
		var $offset = ($currentSlider.parents('.first-section').length == 0 || $('#header-outer[data-transparent-header="true"]').length == 0) ? $currentSlider.offset().top : 0;
    var materialSecondary = 0;
    
    if($('body.material').length > 0 && $('#header-secondary-outer').length > 0) { materialSecondary = $('#header-secondary-outer').height(); }

        if($('body[data-permanent-transparent="1"]').length == 0) {
            //regular
    		if(!$('body').hasClass('mobile')){

                //hhun
                if($('body[data-hhun="1"]').length > 0) {
                    $('body,html').stop().animate({
                        scrollTop: parseInt($topDistance) + $offset + 2
                    },1000,'easeInOutCubic')
                } else {
                  
                  
                  if( $('#header-outer[data-condense="true"]').length > 0) {
          					
          						var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
          						var $secondaryHeaderHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0;

          						$headerNavSpace = $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt($secondaryHeaderHeight);
          				    
                      $('body,html').stop().animate({
                          scrollTop: parseInt($topDistance - $headerNavSpace) + 2
                      },1000,'easeInOutCubic');
                      
                  } else {

                    $('body,html').stop().animate({
                        scrollTop: parseInt($topDistance - $('#header-space').height()) + parseInt(shrinkNum) + headerPadding*2 + $offset + 2 + materialSecondary
                    },1000,'easeInOutCubic');
                    
                  }
                  
                  
                  
                }
    			
    		} else {
                $scrollPos = ($('#header-outer[data-mobile-fixed="1"]').length > 0) ? parseInt($topDistance) - $('#header-space').height() + parseInt($currentSlider.offset().top) + 2 : parseInt($topDistance) + parseInt($currentSlider.offset().top) + 2;
    			$('body,html').stop().animate({
    				scrollTop: $scrollPos
    			},1000,'easeInOutCubic')
    		}
        } else {
            //permanent transparent
            $('body,html').stop().animate({
                scrollTop: parseInt($topDistance) + parseInt($currentSlider.offset().top) + 2
            },1000,'easeInOutCubic')
        }
		return false;
	});



	
	function prettyPhotoInit(){

		var loading_animation = ($('body[data-loading-animation]').attr('data-loading-animation') != 'none') ? $('body').attr('data-loading-animation') : null ;
        var ascend_loader = ($('body').hasClass('ascend')) ? '<span class="default-loading-icon spin"></span>' : '';
        var ascend_loader_class = ($('body').hasClass('ascend')) ? 'default_loader ' : '';

		$(".nectar-slider-wrap a[rel^='prettyPhoto']").prettyPhoto({
			theme: 'dark_rounded',
			allow_resize: true,
			default_width: 690,
			opacity: 0.85, 
			animation_speed: 'normal',
			default_height: 388,
			social_tools: '',
			markup: '<div class="pp_pic_holder"> \
						   <div class="ppt">&nbsp;</div> \
							<div class="pp_details"> \
								<div class="pp_nav"> \
								    <a href="#" class="pp_arrow_previous"> <i class="icon-salient-left-arrow-thin icon-default-style"></i> </a> \
									<a href="#" class="pp_arrow_next"> <i class="icon-salient-right-arrow-thin icon-default-style"></i> </a> \
									<p class="currentTextHolder">0/0</p> \
								</div> \
								<a class="pp_close" href="#"><span class="icon-salient-x icon-default-style"></span></a> \
							</div> \
							<div class="pp_content_container"> \
								<div class="pp_left"> \
								<div class="pp_right"> \
									<div class="pp_content"> \
										<div class="pp_fade"> \
											<div class="pp_hoverContainer"> \
											</div> \
											<div id="pp_full_res"></div> \
                                            <p class="pp_description"></p> \
										</div> \
									</div> \
								</div> \
								</div> \
							</div> \
						</div> \
						<div class="pp_loaderIcon ' + ascend_loader_class + loading_animation+'"> '+ascend_loader+' </div> \
						<div class="pp_overlay"></div>'
		});
		
	}
	


	
});

