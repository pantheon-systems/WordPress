/*!
 * jQuery Strong Slider Plugin
 * Version 2.0.1
 *
 * Copyright (c) 2017 Chris Dillon
 * Released under the MIT license
 *
 * Forked from bxSlider v4.2.5
 * Copyright 2013-2015 Steven Wanderski
 * Licensed under MIT (http://opensource.org/licenses/MIT)
 */

//throw new Error("STOP");

/**
 * @namespace verge.inViewport
 */

;(function ($) {

  var defaults = {

    debug: false,
    logAs: 'strongSlider',
    compat: {
      flatsome: false
    },

    // GENERAL
    mode: 'horizontal',
    slideSelector: 'div.t-slide',
    infiniteLoop: true,
    hideControlOnEnd: false,
    speed: 500,
    easing: null,
    slideMargin: 0,
    startSlide: 0,
    randomStart: false,
    captions: false,
    adaptiveHeight: false,
    adaptiveHeightSpeed: 500,
    useCSS: true,
    preloadImages: 'visible',
    responsive: true,
    slideZIndex: 50,
    stretch: false,
    imagesLoaded: true,
    wrapperClass: 'wpmslider-wrapper',

    // TOUCH
    touchEnabled: true,
    swipeThreshold: 50,
    oneToOneTouch: true,
    preventDefaultSwipeX: true,
    preventDefaultSwipeY: false,

    // ACCESSIBILITY
    ariaLive: true,
    ariaHidden: true,

    // KEYBOARD
    keyboardEnabled: false,

    // PAGER
    pager: true,
    pagerType: 'full',
    pagerShortSeparator: ' / ',
    pagerSelector: null,
    buildPager: null,
    pagerCustom: null,

    // CONTROLS
    controls: true,
    nextText: 'Next',
    prevText: 'Prev',
    nextSelector: null,
    prevSelector: null,
    autoControls: false,
    startText: 'Start',
    stopText: 'Stop',
    autoControlsCombine: false,
    autoControlsSelector: null,

    // AUTO
    auto: true,
    pause: 4000,
    autoStart: true,
    autoDirection: 'next',
    stopAutoOnClick: false,
    autoHover: false,
    autoDelay: 0,
    autoSlideForOnePage: false,

    // CAROUSEL
    minSlides: 1,
    maxSlides: 1,
    moveSlides: 0,
    slideWidth: 0,
    shrinkItems: false,

    // CALLBACKS
    onSliderLoad: function () {
      return true
    },
    onSlideBefore: function () {
      return true
    },
    onSlideAfter: function () {
      return true
    },
    onSlideNext: function () {
      return true
    },
    onSlidePrev: function () {
      return true
    },
    onSliderResize: function () {
      return true
    }
  }

  $.fn.strongSlider = function (options) {

    if (this.length === 0) {
      return this
    }

    // create a namespace to be used throughout the plugin
    var slider = {},
      // set a reference to our slider element
      viewEl = this,
      el = this.find('.wpmslider-content')

    // Return if slider is already initialized
    if ($(el).data('strongSlider')) {
      return
    }

    /**
     * ===================================================================================
     * = PRIVATE FUNCTIONS
     * ===================================================================================
     */

    /**
     * Initializes namespace settings to be used throughout plugin
     */
    var init = function () {
      // Return if slider is already initialized
      if ($(el).data('strongSlider')) {
        return
      }

      // timer to check visibility; used to control sliders in hidden tabs
      slider.visibilityInterval = 0
      // slider state
      slider.hidden = false

      // merge user-supplied options with the defaults
      var sliderVar = viewEl.data('slider-var')
      var config = {}

      if (typeof( window[sliderVar] ) !== 'undefined') {
        config = window[sliderVar].config
      }

      slider.settings = $.extend({}, defaults, config, options)
      slider.debug = slider.settings.debug
      slider.logAs = slider.settings.logAs
      if (slider.debug) console.log(slider.logAs, 'slider.settings', slider.settings)

      // parse slideWidth setting
      slider.settings.slideWidth = parseInt(slider.settings.slideWidth)

      // store the original children
      slider.children = el.children(slider.settings.slideSelector)

      // check if actual number of slides is less than minSlides / maxSlides
      if (slider.children.length < slider.settings.minSlides) {
        slider.settings.minSlides = slider.children.length
      }
      if (slider.children.length < slider.settings.maxSlides) {
        slider.settings.maxSlides = slider.children.length
      }

      // if random start, set the startSlide setting to random number
      if (slider.settings.randomStart) {
        slider.settings.startSlide = Math.floor(Math.random() * slider.children.length)
      }

      // store active slide information
      slider.active = {index: slider.settings.startSlide}

      // store if the slider is in carousel mode (displaying / moving multiple slides)
      slider.carousel = slider.settings.minSlides > 1 || slider.settings.maxSlides > 1

      // if carousel, force preloadImages = 'all'
      if (slider.carousel) {
        slider.settings.preloadImages = 'all'
      }

      // calculate the min / max width thresholds based on min / max number of slides
      // used to setup and update carousel slides dimensions
      slider.minThreshold = (slider.settings.minSlides * slider.settings.slideWidth) + ((slider.settings.minSlides - 1) * slider.settings.slideMargin)
      slider.maxThreshold = (slider.settings.maxSlides * slider.settings.slideWidth) + ((slider.settings.maxSlides - 1) * slider.settings.slideMargin)

      // store the current state of the slider (if currently animating, working is true)
      slider.working = false

      // initialize the controls object
      slider.controls = {}

      // initialize an auto interval
      // no interval = is paused or waiting for user to start
      slider.interval = null

      // determine which property to use for transitions
      slider.animProp = slider.settings.mode === 'vertical' ? 'top' : 'left'

      // determine if hardware acceleration can be used
      slider.usingCSS = slider.settings.useCSS && slider.settings.mode !== 'fade' && (function () {
        // create our test div element
        var div = document.createElement('div')
        // css transition properties
        var props = ['WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective']

        // test for each property
        for (var i = 0; i < props.length; i++) {
          if (div.style[props[i]] !== undefined) {
            slider.cssPrefix = props[i].replace('Perspective', '').toLowerCase()
            slider.animProp = '-' + slider.cssPrefix + '-transform'
            return true
          }
        }
        return false
      }())

      // if vertical mode always make maxSlides and minSlides equal
      if (slider.settings.mode === 'vertical') {
        slider.settings.maxSlides = slider.settings.minSlides
      }
      // save original style data
      el.data('origStyle', el.attr('style'))
      el.children(slider.settings.slideSelector).each(function () {
        $(this).data('origStyle', $(this).attr('style'))
      })

      // Bail if no slides
      if (!el.getSlideCount()) {
        return
      }

      // Wait for images loaded
      if (slider.settings.imagesLoaded) {
        viewEl.imagesLoaded(function () {
          initVisibilityCheck()
        })
      }
      else {
        initVisibilityCheck()
      }

    }

    /**
     * Primary
     *
     * @returns {boolean}
     */
    var reallyVisible = function () {
      return (viewEl.is(':visible') && viewEl.css('visibility') !== 'hidden')
    }

    /**
     * Secondary
     *
     * @returns {boolean}
     */
    var compatCheck = function () {
      if (slider.settings.compat.flatsome) {
        if (viewEl.find('img.lazy-load').length) {
          if (slider.debug) console.log(slider.logAs, 'lazy loading...')
          return false
        }
      }
      if (slider.debug) console.log(slider.logAs, 'compat check complete')
      return true
    }

    /**
     * Check visibility and lazy load status.
     */
    var initVisibilityCheck = function () {
      if (reallyVisible() && compatCheck()) {
        clearInterval(slider.visibilityInterval)
        setup()
      }
      else {
        if (slider.visibilityInterval === 0) {
          slider.visibilityInterval = setInterval(initVisibilityCheck, 1000 * 4)
        }
      }
    }

    /**
     * Performs all DOM and CSS modifications
     */
    var setup = function () {
      // set the default preload selector (visible)
      var preloadSelector = slider.children.eq(slider.settings.startSlide)

      // wrap el in a wrapper
      el.wrap('<div class="' + slider.settings.wrapperClass + '"><div class="wpmslider-viewport"></div></div>')

      // store a namespace reference to .wpmslider-viewport
      slider.viewport = el.parent()

      // add aria-live if the setting is enabled
      if (slider.settings.ariaLive) {
        slider.viewport.attr('aria-live', 'polite')
      }

      // add a loading div to display while images are loading
      slider.loader = $('<div class="wpmslider-loading" />')
      slider.viewport.prepend(slider.loader)

      // set el to a massive width, to hold any needed slides
      // also strip any margin and padding from el
      el.css({
        width: slider.settings.mode === 'horizontal' ? (slider.children.length * 1000 + 215) + '%' : 'auto',
        position: 'relative'
      })

      // if using CSS, add the easing property
      if (slider.usingCSS && slider.settings.easing) {
        el.css('-' + slider.cssPrefix + '-transition-timing-function', slider.settings.easing)
        // if not using CSS and no easing value was supplied, use the default JS animation easing (swing)
      }
      else if (!slider.settings.easing) {
        slider.settings.easing = 'swing'
      }

      // make modifications to the viewport (.wpmslider-viewport)
      slider.viewport.css({
        width: '100%',
        overflow: 'hidden',
        position: 'relative'
      })

      slider.viewport.parent().css({
        maxWidth: getViewportMaxWidth()
      })

      // make modification to the wrapper (.wpmslider-wrapper)
      if (!slider.settings.pager && !slider.settings.controls) {
        slider.viewport.parent().css({
          margin: '0 auto 0px'
        })
      }

      // apply css to all slider children
      slider.children.css({
        float: slider.settings.mode === 'horizontal' ? 'left' : 'none',
        // display: 'unset', // not working in Chrome
        position: 'relative'
      })

      // apply the calculated width after the float is applied to prevent scrollbar interference
      slider.children.css('width', getSlideWidth())

      // if slideMargin is supplied, add the css
      if (slider.settings.mode === 'horizontal' && slider.settings.slideMargin > 0) {
        slider.children.css('marginRight', slider.settings.slideMargin)
      }
      if (slider.settings.mode === 'vertical' && slider.settings.slideMargin > 0) {
        slider.children.css('marginBottom', slider.settings.slideMargin)
      }

      // if "fade" mode, add positioning and z-index CSS
      if (slider.settings.mode === 'fade') {
        slider.children.css({
          position: 'absolute',
          zIndex: 0,
          display: 'none'
        })
        // prepare the z-index on the showing element
        slider.children.eq(slider.settings.startSlide).css({zIndex: slider.settings.slideZIndex, display: 'block'})
      }
      else {
        slider.children.css({
          display: 'block'
        })
      }

      // create an element to contain all slider controls (pager, start / stop, etc)
      slider.controls.el = $('<div class="wpmslider-controls" />')

      // if captions are requested, add them
      if (slider.settings.captions) {
        appendCaptions()
      }

      // check if startSlide is last slide
      slider.active.last = slider.settings.startSlide === getPagerQty() - 1

      if (slider.settings.preloadImages === 'all') {
        preloadSelector = slider.children
      }

      // [ LEFT ]
      // if controls are requested, add them
      if (slider.settings.controls) {
        appendControlPrev()
      }

      // [ MIDDLE ]
      // if auto is true, and auto controls are requested, add them
      if (slider.settings.auto && slider.settings.autoControls) {
        appendControlsAuto()
      }

      // if pager is requested, add it
      if (slider.settings.pager) {
        appendPager()
      }

      // [ RIGHT ]
      if (slider.settings.controls) {
        appendControlNext()
      }

      // if any control option is requested, add the controls wrapper
      if (slider.settings.controls || slider.settings.autoControls || slider.settings.pager) {
        slider.viewport.after(slider.controls.el)
      }

      start()
    }

    /**
     * Start the slider
     */
    var start = function () {

      // if infinite loop, prepare additional slides
      if (slider.settings.infiniteLoop && slider.settings.mode !== 'fade') {

        // slide is always 1 if not carousel mode
        var slice = slider.settings.mode === 'vertical' ? slider.settings.minSlides : slider.settings.maxSlides

        var sliceAppend = slider.children.slice(0, slice).clone(true).addClass('wpmslider-clone')

        var slicePrepend = slider.children.slice(-slice).clone(true).addClass('wpmslider-clone')

        if (slider.settings.ariaHidden) {
          sliceAppend.attr('aria-hidden', true)
          slicePrepend.attr('aria-hidden', true)
        }

        el.append(sliceAppend).prepend(slicePrepend)
      }

      // remove the loading DOM element
      slider.loader.remove()

      // set the left / top position of "el"
      setSlidePosition()

      // if "vertical" mode, always use adaptiveHeight to prevent odd behavior
      if (slider.settings.mode === 'vertical') {
        slider.settings.adaptiveHeight = true
      }

      // set the viewport height
      slider.viewport.height(getViewportHeight())

      // if stretch, set t-slide height to 100%
      if (slider.settings.stretch) {
        setSlideHeight()
      }

      // make sure everything is positioned just right (same as a window resize)
      el.redrawSlider()

      // onSliderLoad callback
      slider.settings.onSliderLoad.call(el, slider.active.index)

      // slider has been fully initialized
      slider.initialized = true
      slider.visibilityInterval = setInterval(visibilityCheck, 500)

      if (slider.settings.responsive) {
        attachListeners()
      }

      // if auto is true and has more than 1 page, start the show
      if (slider.settings.auto
        && slider.settings.autoStart
        && (getPagerQty() > 1 || slider.settings.autoSlideForOnePage)) {
        initAuto()
      }
// throw new Error("STOP");

      // if pager is requested, make the appropriate pager link active
      if (slider.settings.pager) {
        updatePagerActive(slider.settings.startSlide)
      }
      // check for any updates to the controls (like hideControlOnEnd updates)
      if (slider.settings.controls) {
        updateDirectionControls()
      }
      // if touchEnabled is true, setup the touch events
      if (slider.settings.touchEnabled) {
        initTouch()
      }
      // if keyboardEnabled is true, setup the keyboard events
      if (slider.settings.keyboardEnabled) {
        $(document).keydown(keyPress)
      }
    }

    /**
     * ==============================================================
     * EVENTS
     *
     * Pause/play actions are coupled by method. The slider can only
     * be restarted by the partner of the mechanism that paused it.
     * For example, a slider paused by switching windows (blur) will
     * only restart upon switching back (focus).
     *
     * Event                         : Action     : Function
     * ------------------------------:------------:-------------------
     * hide/show (ex: tabbed pages)  : pause/play : visibilityCheck
     * scroll out/in of viewport     : pause/play : visibilityCheck
     * hover in/out                  : pause/play : initAuto
     * blur/focus                    : pause/play : attachListeners
     * resize and orientation change : redraw     : attachListeners
     * ==============================================================
     */

    /**
     * Window event listeners.
     *
     * Not checking inViewport on scroll event because we also check that
     * in the general visibility check.
     */
    var attachListeners = function () {

      window.addEventListener('resize', updateLayout, false)
      window.addEventListener('orientationchange', updateLayout, false)

      // Test this with dev console closed
      // (or click in the document once to establish focus).
      window.addEventListener('blur', function () {
        pauseEvent('blur')
      })

      window.addEventListener('focus', function () {
        playEvent('blur')
      })

    }

    // Debounced resize event.
    var updateLayout = _.debounce(function () { resizeWindow() }, 250)

    // General visibility check.
    var visibilityCheck = function () {
      if (!slider.settings.auto) {
        return
      }

      if (!reallyVisible()) {
        pauseEvent('hide')
      }
      else {
        playEvent('hide')
      }

      if (!verge.inViewport(el)) {
        pauseEvent('scroll')
      }
      else {
        playEvent('scroll')
      }
    }

    var pauseEvent = function (action) {
      // if the auto show is currently playing (has an active interval)
      if (slider.interval) {
        // stop the auto show and pass true argument which will prevent control update
        el.stopAuto(true)
        // create a new autoPaused value which will be used by the corresponding event
        slider.autoPaused = action
        if (slider.debug) console.log(slider.logAs, 'pause', action)
      }
    }

    var playEvent = function (action) {
      // if the autoPaused value was created by the prior event
      if (slider.autoPaused === action) {
        // start the auto show and pass true argument which will prevent control update
        el.startAuto(true)
        // reset the autoPaused value
        slider.autoPaused = null
        if (slider.debug) console.log(slider.logAs, 'play', action)
      }
    }

    var setSlideHeight = function () {
      var heights = slider.children.map(function () {
        return jQuery(this).actual('outerHeight')
      }).get()

      var maxHeight = arrayMax(heights)
      slider.children.height(maxHeight)
    }

    // Function to get the max value in array
    var arrayMax = function (array) {
      return Math.max.apply(Math, array)
    }

    /**
     * Returns the calculated height of the SLIDER viewport (not browser viewport),
     * used to determine either adaptiveHeight or the maxHeight value
     */
    var getViewportHeight = function () {

      var height = 0

      // first determine which children (slides) should be used in our height calculation
      var children = $()

      // if mode is not "vertical" and adaptiveHeight is false, include all children
      if (slider.settings.mode !== 'vertical' && !slider.settings.adaptiveHeight) {

        children = slider.children

      }
      else {

        // if not carousel, return the single active child
        if (!slider.carousel) {

          children = slider.children.eq(slider.active.index)
          // if carousel, return a slice of children

        }
        else {

          // get the individual slide index
          var currentIndex = slider.settings.moveSlides === 1 ? slider.active.index : slider.active.index * getMoveBy()

          // add the current slide to the children
          children = slider.children.eq(currentIndex)

          // cycle through the remaining "showing" slides
          for (var i = 1; i <= slider.settings.maxSlides - 1; i++) {

            // if looped back to the start
            if (currentIndex + i >= slider.children.length) {
              children = children.add(slider.children.eq(i - 1))
            }
            else {
              children = children.add(slider.children.eq(currentIndex + i))
            }

          }

        }

      }

      // if "vertical" mode, calculate the sum of the heights of the children
      if (slider.settings.mode === 'vertical') {

        children.each(function (index) {
          height += $(this).outerHeight()
        })

        // add user-supplied margins
        if (slider.settings.slideMargin > 0) {
          height += slider.settings.slideMargin * (slider.settings.minSlides - 1)
        }

      }
      else {

        // if not "vertical" mode, calculate the max height of the children
        height = Math.max.apply(Math, children.map(function () {
          return $(this).outerHeight(false)
        }).get())

      }

      if (slider.viewport.css('box-sizing') === 'border-box') {
        height += parseFloat(slider.viewport.css('padding-top')) + parseFloat(slider.viewport.css('padding-bottom')) +
          parseFloat(slider.viewport.css('border-top-width')) + parseFloat(slider.viewport.css('border-bottom-width'))
      }
      else if (slider.viewport.css('box-sizing') === 'padding-box') {
        height += parseFloat(slider.viewport.css('padding-top')) + parseFloat(slider.viewport.css('padding-bottom'))
      }

      return height
    }

    /**
     * Returns the calculated width to be used for the outer wrapper / SLIDER viewport
     */
    var getViewportMaxWidth = function () {
      var width = '100%'
      if (slider.settings.slideWidth > 0) {
        if (slider.settings.mode === 'horizontal') {
          width = (slider.settings.maxSlides * slider.settings.slideWidth) + ((slider.settings.maxSlides - 1) * slider.settings.slideMargin)
        }
        else {
          width = slider.settings.slideWidth
        }
      }
      return width
    }

    /**
     * Returns the calculated width to be applied to each slide
     */
    var getSlideWidth = function () {

      var newElWidth = slider.settings.slideWidth, // start with any user-supplied slide width
        wrapWidth = slider.viewport.width()    // get the current viewport width

      // if slide width was not supplied, or is larger than the viewport use the viewport width
      if (slider.settings.slideWidth === 0 ||

        (slider.settings.slideWidth > wrapWidth && !slider.carousel) ||
        slider.settings.mode === 'vertical') {
        newElWidth = wrapWidth

      }
      else if (slider.settings.maxSlides > 1 && slider.settings.mode === 'horizontal') {

        // if carousel, use the thresholds to determine the width
        if (wrapWidth > slider.maxThreshold) {
          return newElWidth
        }
        else if (wrapWidth < slider.minThreshold) {
          newElWidth = (wrapWidth - (slider.settings.slideMargin * (slider.settings.minSlides - 1))) / slider.settings.minSlides
        }
        else if (slider.settings.shrinkItems) {
          newElWidth = Math.floor((wrapWidth + slider.settings.slideMargin) / (Math.ceil((wrapWidth + slider.settings.slideMargin) / (newElWidth + slider.settings.slideMargin))) - slider.settings.slideMargin)
        }

      }
      return newElWidth
    }

    /**
     * Returns the number of slides currently visible in the viewport (includes partially visible slides)
     */
    var getNumberSlidesShowing = function () {
      var slidesShowing = 1,
        childWidth = null
      if (slider.settings.mode === 'horizontal' && slider.settings.slideWidth > 0) {
        // if viewport is smaller than minThreshold, return minSlides
        if (slider.viewport.width() < slider.minThreshold) {
          slidesShowing = slider.settings.minSlides
        }
        // if viewport is larger than maxThreshold, return maxSlides
        else if (slider.viewport.width() > slider.maxThreshold) {
          slidesShowing = slider.settings.maxSlides
        }
        // if viewport is between min / max thresholds, divide viewport width by first child width
        else {
          childWidth = slider.children.first().width() + slider.settings.slideMargin
          slidesShowing = Math.floor((slider.viewport.width() +
            slider.settings.slideMargin) / childWidth)
        }
      }
      // if "vertical" mode, slides showing will always be minSlides
      else if (slider.settings.mode === 'vertical') {
        slidesShowing = slider.settings.minSlides
      }
      return slidesShowing
    }

    /**
     * Returns the number of pages (one full viewport of slides is one "page")
     */
    var getPagerQty = function () {
      var pagerQty = 0,
        breakPoint = 0,
        counter = 0
      // if moveSlides is specified by the user
      if (slider.settings.moveSlides > 0) {
        if (slider.settings.infiniteLoop) {
          pagerQty = Math.ceil(slider.children.length / getMoveBy())
        }
        else {
          // when breakpoint goes above children length, counter is the number of pages
          while (breakPoint < slider.children.length) {
            ++pagerQty
            breakPoint = counter + getNumberSlidesShowing()
            counter += slider.settings.moveSlides <= getNumberSlidesShowing() ? slider.settings.moveSlides : getNumberSlidesShowing()
          }
        }
      }
      // if moveSlides is 0 (auto) divide children length by sides showing, then round up
      else {
        pagerQty = Math.ceil(slider.children.length / getNumberSlidesShowing())
      }
      return pagerQty
    }

    /**
     * Returns the number of individual slides by which to shift the slider
     */
    var getMoveBy = function () {
      // if moveSlides was set by the user and moveSlides is less than number of slides showing
      if (slider.settings.moveSlides > 0 && slider.settings.moveSlides <= getNumberSlidesShowing()) {
        return slider.settings.moveSlides
      }
      // if moveSlides is 0 (auto)
      return getNumberSlidesShowing()
    }

    /**
     * Sets the slider's (el) left or top position
     */
    var setSlidePosition = function () {
      var position, lastChild, lastShowingIndex

      // if last slide, not infinite loop, and number of children is larger than specified maxSlides
      if (slider.children.length > slider.settings.maxSlides && slider.active.last && !slider.settings.infiniteLoop) {

        if (slider.settings.mode === 'horizontal') {
          // get the last child's position
          lastChild = slider.children.last()
          position = lastChild.position()
          // set the left position
          setPositionProperty(-(position.left - (slider.viewport.width() - lastChild.outerWidth())), 'reset', 0)
        }
        else if (slider.settings.mode === 'vertical') {
          // get the last showing index's position
          lastShowingIndex = slider.children.length - slider.settings.minSlides
          position = slider.children.eq(lastShowingIndex).position()
          // set the top position
          setPositionProperty(-position.top, 'reset', 0)
        }

      }
      // if not last slide
      else {

        // get the position of the first showing slide
        position = slider.children.eq(slider.active.index * getMoveBy()).position()

        // check for last slide
        if (slider.active.index === getPagerQty() - 1) {
          slider.active.last = true
        }

        // set the respective position
        if (position !== undefined) {
          if (slider.settings.mode === 'horizontal') {
            setPositionProperty(-position.left, 'reset', 0)
          }
          else if (slider.settings.mode === 'vertical') {
            setPositionProperty(-position.top, 'reset', 0)
          }
          else if (slider.settings.mode === 'none') {
            setPositionProperty(-position.top, 'reset', 0)
          }
        }

      }
    }

    /**
     * Sets the el's animating property position (which in turn will sometimes animate el).
     * If using CSS, sets the transform property. If not using CSS, sets the top / left property.
     *
     * @param value (int)
     *  - the animating property's value
     *
     * @param type (string) 'slide', 'reset'
     *  - the type of instance for which the function is being
     *
     * @param duration (int)
     *  - the amount of time (in ms) the transition should occupy
     *
     * @param params (array) optional
     *  - an optional parameter containing any variables that need to be passed in
     */
    var setPositionProperty = function (value, type, duration, params) {
      var animateObj, propValue
      // use CSS transform
      if (slider.usingCSS) {

        // determine the translate3d value
        // propValue = slider.settings.mode === 'vertical' ? 'translate3d(0, ' + value + 'px, 0)' : 'translate3d(' + value + 'px, 0, 0)'
        if (slider.settings.mode === 'vertical') {
          //propValue = 'translate3d(0, ' + value + 'px, 0)'
          propValue = 'translateY(' + value + 'px)'
        }
        else if (slider.settings.mode === 'horizontal') {
          // propValue = 'translate3d(' + value + 'px, 0, 0)'
          propValue = 'translateX(' + value + 'px'
        }
        else if (slider.settings.mode === 'none') {
          // propValue = 'translate3d(0, ' + value + 'px, 0)'
          propValue = 'translateY(' + value + 'px)'
          duration = 0
        }

        // add the CSS transition-duration
        el.css('-' + slider.cssPrefix + '-transition-duration', duration / 1000 + 's')

        if (type === 'slide') {

          // set the property value
          el.css(slider.animProp, propValue)
          if (duration !== 0) {
            // bind a callback method - executes when CSS transition completes
            el.on('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function (e) {
              //make sure it's the correct one
              if (!$(e.target).is(el)) {
                return
              }
              // unbind the callback
              el.off('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd')
              updateAfterSlideTransition()
            })
          }
          else {
            updateAfterSlideTransition()
          }

        }
        else if (type === 'reset') {

          el.css(slider.animProp, propValue)

        }

      }
      // use JS animate
      else {

        animateObj = {}
        animateObj[slider.animProp] = value
        if (type === 'slide') {
          el.animate(animateObj, duration, slider.settings.easing, function () {
            updateAfterSlideTransition()
          })
        }
        else if (type === 'reset') {
          el.css(slider.animProp, value)
        }

      }
    }

    /**
     * Populates the pager with proper amount of pages
     */
    var populatePager = function () {
      var pagerHtml = '',
        linkContent = '',
        pagerQty = getPagerQty()

      // loop through each pager item
      for (var i = 0; i < pagerQty; i++) {
        linkContent = ''

        if (slider.settings.buildPager) {
          // if using icons, use no link text
          if (slider.settings.buildPager === 'icons') {
            linkContent = ''
          }
          // if a buildPager function is supplied, use it to get pager link value, else use index + 1
          if ($.isFunction(slider.settings.buildPager) || slider.settings.pagerCustom) {
            linkContent = slider.settings.buildPager(i)
          }
          slider.pagerEl.addClass('wpmslider-custom-pager')
        }
        else {
          linkContent = i + 1
          slider.pagerEl.addClass('wpmslider-default-pager')
        }

        // add the markup to the string
        pagerHtml += '<div class="wpmslider-pager-item"><a href="" data-slide-index="' + i + '" class="wpmslider-pager-link">' + linkContent + '</a></div>'
      }

      // populate the pager element with pager links
      slider.pagerEl.html(pagerHtml)
    }

    /**
     * Appends the pager to the controls element
     */
    var appendPager = function () {
      if (!slider.settings.pagerCustom) {
        // create the pager DOM element
        slider.pagerEl = $('<div class="wpmslider-pager" />')
        // if a pager selector was supplied, populate it with the pager
        if (slider.settings.pagerSelector) {
          $(slider.settings.pagerSelector).html(slider.pagerEl)
        }
        else {
          // if no pager selector was supplied, add it after the wrapper
          slider.controls.el.addClass('wpmslider-has-pager').append(slider.pagerEl)
        }
        // populate the pager
        populatePager()
      }
      else {
        slider.pagerEl = $(slider.settings.pagerCustom)
      }
      // assign the pager click binding
      slider.pagerEl.on('click touchend', 'a', clickPagerBind)
    }

    /**
     * Appends prev control to the controls element
     */
    var appendControlPrev = function () {
      slider.controls.prev = $('<a class="wpmslider-prev" href="">' + slider.settings.prevText + '</a>')

      // bind click actions to the controls
      slider.controls.prev.on('click touchend', clickPrevBind)

      // if prevSelector was supplied, populate it
      if (slider.settings.prevSelector) {
        $(slider.settings.prevSelector).append(slider.controls.prev)
      }

      // if no custom selectors were supplied
      if (!slider.settings.prevSelector) {
        // add the controls to the DOM
        slider.controls.directionEl = $('<div class="wpmslider-controls-direction" />')
        // add the control elements to the directionEl
        slider.controls.directionEl.append(slider.controls.prev)
        slider.controls.el.addClass('wpmslider-has-controls-direction').append(slider.controls.directionEl)
      }
    }

    /**
     * Appends prev / next controls to the controls element
     */
    var appendControlNext = function () {
      slider.controls.next = $('<a class="wpmslider-next" href="">' + slider.settings.nextText + '</a>')

      // bind click actions to the controls
      slider.controls.next.on('click touchend', clickNextBind)

      // if nextSelector was supplied, populate it
      if (slider.settings.nextSelector) {
        $(slider.settings.nextSelector).append(slider.controls.next)
      }

      // if no custom selectors were supplied
      if (!slider.settings.nextSelector) {
        // add the controls to the DOM
        slider.controls.directionEl = $('<div class="wpmslider-controls-direction" />')
        // add the control elements to the directionEl
        slider.controls.directionEl.append(slider.controls.next)
        slider.controls.el.addClass('wpmslider-has-controls-direction').append(slider.controls.directionEl)
      }
    }

    /**
     * Appends start / stop auto controls to the controls element
     */
    var appendControlsAuto = function () {
      slider.controls.start = $('<div class="wpmslider-controls-auto-item"><a class="wpmslider-start" href="">' + slider.settings.startText + '</a></div>')
      slider.controls.stop = $('<div class="wpmslider-controls-auto-item"><a class="wpmslider-stop" href="">' + slider.settings.stopText + '</a></div>')

      // add the controls to the DOM
      slider.controls.autoEl = $('<div class="wpmslider-controls-auto" />')

      // bind click actions to the controls
      slider.controls.autoEl.on('click', '.wpmslider-start', clickStartBind)
      slider.controls.autoEl.on('click', '.wpmslider-stop', clickStopBind)

      // if autoControlsCombine, insert only the "start" control
      if (slider.settings.autoControlsCombine) {
        slider.controls.autoEl.append(slider.controls.start)
        // if autoControlsCombine is false, insert both controls
      }
      else {
        slider.controls.autoEl.append(slider.controls.start).append(slider.controls.stop)
      }

      // if auto controls selector was supplied, populate it with the controls
      if (slider.settings.autoControlsSelector) {
        $(slider.settings.autoControlsSelector).html(slider.controls.autoEl)
        // if auto controls selector was not supplied, add it after the wrapper
      }
      else {
        slider.controls.el.addClass('wpmslider-has-controls-auto').append(slider.controls.autoEl)
      }

      // update the auto controls
      updateAutoControls(slider.settings.autoStart ? 'stop' : 'start')
    }

    /**
     * Appends image captions to the DOM
     */
    var appendCaptions = function () {
      // cycle through each child
      slider.children.each(function (index) {
        // get the image title attribute
        var title = $(this).find('img:first').attr('title')
        // append the caption
        if (title !== undefined && ('' + title).length) {
          $(this).append('<div class="wpmslider-caption"><span>' + title + '</span></div>')
        }
      })
    }

    /**
     * Click next binding
     *
     * @param e (event)
     *  - DOM event object
     */
    var clickNextBind = function (e) {
      e.preventDefault()
      if (slider.controls.el.hasClass('disabled')) {
        return
      }
      // if auto show is running, stop it
      if (slider.settings.auto && slider.settings.stopAutoOnClick) {
        if (slider.debug) console.log(slider.logAs, 'stop on navigation')
        el.stopAuto()
      }
      el.goToNextSlide()
    }

    /**
     * Click prev binding
     *
     * @param e (event)
     *  - DOM event object
     */
    var clickPrevBind = function (e) {
      e.preventDefault()
      if (slider.controls.el.hasClass('disabled')) {
        return
      }
      // if auto show is running, stop it
      if (slider.settings.auto && slider.settings.stopAutoOnClick) {
        if (slider.debug) console.log(slider.logAs, 'stop on navigation')
        el.stopAuto()
      }
      el.goToPrevSlide()
    }

    /**
     * Click start binding
     *
     * @param e (event)
     *  - DOM event object
     */
    var clickStartBind = function (e) {
      el.startAuto()
      e.preventDefault()
    }

    /**
     * Click stop binding
     *
     * @param e (event)
     *  - DOM event object
     */
    var clickStopBind = function (e) {
      el.stopAuto()
      e.preventDefault()
    }

    /**
     * Click pager binding
     *
     * @param e (event)
     *  - DOM event object
     */
    var clickPagerBind = function (e) {
      var pagerLink, pagerIndex
      e.preventDefault()
      if (slider.controls.el.hasClass('disabled')) {
        return
      }
      // if auto show is running, stop it
      if (slider.settings.auto && slider.settings.stopAutoOnClick) {
        if (slider.debug) console.log(slider.logAs, 'stop on navigation')
        el.stopAuto()
      }
      pagerLink = $(e.currentTarget)
      if (pagerLink.attr('data-slide-index') !== undefined) {
        pagerIndex = parseInt(pagerLink.attr('data-slide-index'))
        // if clicked pager link is not active, continue with the goToSlide call
        if (pagerIndex !== slider.active.index) {
          el.goToSlide(pagerIndex)
        }
      }
    }

    /**
     * Updates the pager links with an active class
     *
     * @param slideIndex (int)
     *  - index of slide to make active
     */
    var updatePagerActive = function (slideIndex) {
      // if "short" pager type
      var len = slider.children.length // nb of children
      if (slider.settings.pagerType === 'short') {
        if (slider.settings.maxSlides > 1) {
          len = Math.ceil(slider.children.length / slider.settings.maxSlides)
        }
        slider.pagerEl.html((slideIndex + 1) + slider.settings.pagerShortSeparator + len)
        return
      }
      // remove all pager active classes
      slider.pagerEl.find('a').removeClass('active')
      // apply the active class for all pagers
      slider.pagerEl.each(function (i, el) {
        $(el).find('a').eq(slideIndex).addClass('active')
      })
    }

    /**
     * Performs needed actions after a slide transition
     */
    var updateAfterSlideTransition = function () {
      // if infinite loop is true
      if (slider.settings.infiniteLoop) {
        var position = ''
        // first slide
        if (slider.active.index === 0) {
          // set the new position
          position = slider.children.eq(0).position()
        }
        // carousel, last slide
        else if (slider.active.index === getPagerQty() - 1 && slider.carousel) {
          position = slider.children.eq((getPagerQty() - 1) * getMoveBy()).position()
        }
        // last slide
        else if (slider.active.index === slider.children.length - 1) {
          position = slider.children.eq(slider.children.length - 1).position()
        }
        if (position) {
          if (slider.settings.mode === 'horizontal') {
            setPositionProperty(-position.left, 'reset', 0)
          }
          else if (slider.settings.mode === 'vertical') {
            setPositionProperty(-position.top, 'reset', 0)
          }
        }
      }
      // declare that the transition is complete
      slider.working = false
      // onSlideAfter callback
      slider.settings.onSlideAfter.call(el, slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index)
    }

    /**
     * Updates the auto controls state (either active, or combined switch)
     *
     * @param state (string) "start", "stop"
     *  - the new state of the auto show
     */
    var updateAutoControls = function (state) {
      // if autoControlsCombine is true, replace the current control with the new state
      if (slider.settings.autoControlsCombine) {
        slider.controls.autoEl.html(slider.controls[state])
      }
      // if autoControlsCombine is false, apply the "active" class to the appropriate control
      else {
        slider.controls.autoEl.find('a').removeClass('active')
        slider.controls.autoEl.find('a:not(.wpmslider-' + state + ')').addClass('active')
      }
    }

    /**
     * Updates the direction controls (checks if either should be hidden)
     */
    var updateDirectionControls = function () {
      if (getPagerQty() === 1) {
        slider.controls.prev.addClass('disabled')
        slider.controls.next.addClass('disabled')
      }
      else if (!slider.settings.infiniteLoop && slider.settings.hideControlOnEnd) {
        // if first slide
        if (slider.active.index === 0) {
          slider.controls.prev.addClass('disabled')
          slider.controls.next.removeClass('disabled')
        }
        // if last slide
        else if (slider.active.index === getPagerQty() - 1) {
          slider.controls.next.addClass('disabled')
          slider.controls.prev.removeClass('disabled')
        }
        // if any slide in the middle
        else {
          slider.controls.prev.removeClass('disabled')
          slider.controls.next.removeClass('disabled')
        }
      }
    }

    /**
     * Initializes the auto process
     */
    var initAuto = function () {
      // if autoDelay was supplied, launch the auto show using a setTimeout() call
      if (slider.settings.autoDelay > 0) {
        var timeout = setTimeout(el.startAuto, slider.settings.autoDelay)
      }
      // if autoDelay was not supplied, start the auto show normally
      else {
        el.startAuto()
      }

      // if autoHover is requested
      if (slider.settings.autoHover) {
        // on el hover
        el.hover(function () {
          pauseEvent('hover')
        }, function () {
          playEvent('hover')
        })
      }
    }

    /**
     * Check if el is on screen
     *
     * Replaced with verge.inViewport
     */
    /*
    var isOnScreen = function (el) {
      var win = $(window),
        viewport = {
          top: win.scrollTop(),
          left: win.scrollLeft()
        },
        bounds = el.offset()

      viewport.right = viewport.left + win.width()
      viewport.bottom = viewport.top + win.height()
      bounds.right = bounds.left + el.outerWidth()
      bounds.bottom = bounds.top + el.outerHeight()

      return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom))
    }
    */

    /**
     * Initializes keyboard events
     */
    var keyPress = function (e) {
      var activeElementTag = document.activeElement.tagName.toLowerCase(),
        tagFilters = 'input|textarea',
        p = new RegExp(activeElementTag, ['i']),
        result = p.exec(tagFilters)

      if (result === null && verge.inViewport(el)) {
        if (e.keyCode === 39) {
          clickNextBind(e)
          return false
        }
        else if (e.keyCode === 37) {
          clickPrevBind(e)
          return false
        }
      }
    }

    /**
     * Initializes touch events
     */
    var initTouch = function () {
      // initialize object to contain all touch values
      slider.touch = {
        start: {x: 0, y: 0},
        end: {x: 0, y: 0}
      }
      slider.viewport.on('touchstart MSPointerDown pointerdown', onTouchStart)

      //for browsers that have implemented pointer events and fire a click after
      //every pointerup regardless of whether pointerup is on same screen location as pointerdown or not
      slider.viewport.on('click', '.wpmslider a', function (e) {
        if (slider.viewport.hasClass('click-disabled')) {
          e.preventDefault()
          slider.viewport.removeClass('click-disabled')
        }
      })
    }

    /**
     * Event handler for "touchstart"
     *
     * @param e (event)
     *  - DOM event object
     */
    var onTouchStart = function (e) {
      //disable slider controls while user is interacting with slides to avoid slider freeze that happens on touch devices when a slide swipe happens immediately after interacting with slider controls
      slider.controls.el.addClass('disabled')

      if (slider.working) {
        e.preventDefault()
        slider.controls.el.removeClass('disabled')
      }
      else {
        // record the original position when touch starts
        slider.touch.originalPos = el.position()
        var orig = e.originalEvent,
          touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig]
        // https://stackoverflow.com/questions/41541121/domexception-failed-to-execute-setpointercapture-on-element-and-releasepoin
        var chromePointerEvents = typeof PointerEvent === 'function'
        if (chromePointerEvents) { if (orig.pointerId === undefined) { return; } }
        // record the starting touch x, y coordinates
        slider.touch.start.x = touchPoints[0].pageX
        slider.touch.start.y = touchPoints[0].pageY

        if (slider.viewport.get(0).setPointerCapture) {
          slider.pointerId = orig.pointerId
          slider.viewport.get(0).setPointerCapture(slider.pointerId)
        }
        // bind a "touchmove" event to the viewport
        slider.viewport.on('touchmove MSPointerMove pointermove', onTouchMove)
        // bind a "touchend" event to the viewport
        slider.viewport.on('touchend MSPointerUp pointerup', onTouchEnd)
        slider.viewport.on('MSPointerCancel pointercancel', onPointerCancel)
      }
    }

    /**
     * Cancel Pointer for Windows Phone
     *
     * @param e (event)
     *  - DOM event object
     */
    var onPointerCancel = function (e) {
      /* onPointerCancel handler is needed to deal with situations when a touchend
       doesn't fire after a touchstart (this happens on windows phones only) */
      setPositionProperty(slider.touch.originalPos.left, 'reset', 0)

      //remove handlers
      slider.controls.el.removeClass('disabled')
      slider.viewport.off('MSPointerCancel pointercancel', onPointerCancel)
      slider.viewport.off('touchmove MSPointerMove pointermove', onTouchMove)
      slider.viewport.off('touchend MSPointerUp pointerup', onTouchEnd)
      if (slider.viewport.get(0).releasePointerCapture) {
        slider.viewport.get(0).releasePointerCapture(slider.pointerId)
      }
    }

    /**
     * Event handler for "touchmove"
     *
     * @param e (event)
     *  - DOM event object
     */
    var onTouchMove = function (e) {
      var orig = e.originalEvent,
        touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig],
        // if scrolling on y axis, do not prevent default
        xMovement = Math.abs(touchPoints[0].pageX - slider.touch.start.x),
        yMovement = Math.abs(touchPoints[0].pageY - slider.touch.start.y),
        value = 0,
        change = 0

      // x axis swipe
      if ((xMovement * 3) > yMovement && slider.settings.preventDefaultSwipeX) {
        e.preventDefault()
      }
      // y axis swipe
      else if ((yMovement * 3) > xMovement && slider.settings.preventDefaultSwipeY) {
        e.preventDefault()
      }
      if (slider.settings.mode !== 'fade' && slider.settings.oneToOneTouch) {
        // if horizontal, drag along x axis
        if (slider.settings.mode === 'horizontal') {
          change = touchPoints[0].pageX - slider.touch.start.x
          value = slider.touch.originalPos.left + change
          // if vertical, drag along y axis
        }
        else {
          change = touchPoints[0].pageY - slider.touch.start.y
          value = slider.touch.originalPos.top + change
        }
        setPositionProperty(value, 'reset', 0)
      }
    }

    /**
     * Event handler for "touchend"
     *
     * @param e (event)
     *  - DOM event object
     */
    var onTouchEnd = function (e) {
      slider.viewport.off('touchmove MSPointerMove pointermove', onTouchMove)
      //enable slider controls as soon as user stops interacing with slides
      slider.controls.el.removeClass('disabled')
      var orig = e.originalEvent,
        touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig],
        value = 0,
        distance = 0
      // record end x, y positions
      slider.touch.end.x = touchPoints[0].pageX
      slider.touch.end.y = touchPoints[0].pageY
      // if fade mode, check if absolute x distance clears the threshold
      if (slider.settings.mode === 'fade') {
        distance = Math.abs(slider.touch.start.x - slider.touch.end.x)
        if (distance >= slider.settings.swipeThreshold) {
          if (slider.touch.start.x > slider.touch.end.x) {
            el.goToNextSlide()
          }
          else {
            el.goToPrevSlide()
          }
          el.stopAuto()
        }
      }
      // not fade mode
      else {
        // calculate distance and el's animate property
        if (slider.settings.mode === 'horizontal') {
          distance = slider.touch.end.x - slider.touch.start.x
          value = slider.touch.originalPos.left
        }
        else {
          distance = slider.touch.end.y - slider.touch.start.y
          value = slider.touch.originalPos.top
        }
        // if not infinite loop and first / last slide, do not attempt a slide transition
        if (!slider.settings.infiniteLoop && ((slider.active.index === 0 && distance > 0) || (slider.active.last && distance < 0))) {
          setPositionProperty(value, 'reset', 200)
        }
        else {
          // check if distance clears threshold
          if (Math.abs(distance) >= slider.settings.swipeThreshold) {
            if (distance < 0) {
              el.goToNextSlide()
            }
            else {
              el.goToPrevSlide()
            }
            el.stopAuto()
          }
          else {
            // el.animate(property, 200);
            setPositionProperty(value, 'reset', 200)
          }
        }
      }
      slider.viewport.off('touchend MSPointerUp pointerup', onTouchEnd)
      if (slider.viewport.get(0).releasePointerCapture) {
        slider.viewport.get(0).releasePointerCapture(slider.pointerId)
      }
    }

    /**
     * Window resize event callback
     */
    var resizeWindow = function (e) {
      // don't do anything if slider isn't initialized.
      if (!slider.initialized) {
        return
      }
      // Delay if slider working.
      if (slider.working) {
        window.setTimeout(resizeWindow, 10)
      }
      else {
        // update all dynamic elements
        el.redrawSlider()
        // Call user resize handler
        slider.settings.onSliderResize.call(el, slider.active.index)
      }
    }

    /**
     * Adds an aria-hidden=true attribute to each element
     *
     * @param startVisibleIndex (int)
     *  - the first visible element's index
     */
    var applyAriaHiddenAttributes = function (startVisibleIndex) {
      var numberOfSlidesShowing = getNumberSlidesShowing()
      // only apply attributes if the setting is enabled
      if (slider.settings.ariaHidden) {
        // add aria-hidden=true to all elements
        slider.children.attr('aria-hidden', 'true')
        // get the visible elements and change to aria-hidden=false
        slider.children.slice(startVisibleIndex, startVisibleIndex + numberOfSlidesShowing).attr('aria-hidden', 'false')
      }
    }

    /**
     * Returns index according to present page range
     *
     * @param slideIndex (int)
     *  - the desired slide index
     */
    var setSlideIndex = function (slideIndex) {
      if (slideIndex < 0) {
        if (slider.settings.infiniteLoop) {
          return getPagerQty() - 1
        }
        else {
          //we don't go to undefined slides
          return slider.active.index
        }
      }
      // if slideIndex is greater than children length, set active index to 0 (this happens during infinite loop)
      else if (slideIndex >= getPagerQty()) {
        if (slider.settings.infiniteLoop) {
          return 0
        }
        else {
          //we don't move to undefined pages
          return slider.active.index
        }
      }
      // set active index to requested slide
      else {
        return slideIndex
      }
    }

    /**
     * ===================================================================================
     * = PUBLIC FUNCTIONS
     * ===================================================================================
     */

    /**
     * Performs slide transition to the specified slide
     *
     * @param slideIndex (int)
     *  - the destination slide's index (zero-based)
     *
     * @param direction (string)
     *  - INTERNAL USE ONLY - the direction of travel ("prev" / "next")
     */
    el.goToSlide = function (slideIndex, direction) {
      // onSlideBefore, onSlideNext, onSlidePrev callbacks
      // Allow transition canceling based on returned value
      var performTransition,
        moveBy = 0,
        position = {left: 0, top: 0},
        lastChild = null,
        lastShowingIndex, eq, value, requestEl
      // store the old index
      slider.oldIndex = slider.active.index
      //set new index
      slider.active.index = setSlideIndex(slideIndex)

      // if plugin is currently in motion, ignore request
      if (slider.working || slider.active.index === slider.oldIndex) {
        return
      }
      // declare that plugin is in motion
      slider.working = true

      performTransition = slider.settings.onSlideBefore.call(el, slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index)

      // If transitions canceled, reset and return
      if (typeof (performTransition) !== 'undefined' && !performTransition) {
        slider.active.index = slider.oldIndex // restore old index
        slider.working = false // is not in motion
        return
      }

      if (direction === 'next') {
        // Prevent canceling in future functions or lack thereof from negating previous commands to cancel
        if (!slider.settings.onSlideNext.call(el, slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index)) {
          performTransition = false
        }
      }
      else if (direction === 'prev') {
        // Prevent canceling in future functions or lack thereof from negating previous commands to cancel
        if (!slider.settings.onSlidePrev.call(el, slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index)) {
          performTransition = false
        }
      }

      // check if last slide
      slider.active.last = slider.active.index >= getPagerQty() - 1

      // update the pager with active class
      if (slider.settings.pager || slider.settings.pagerCustom) {
        updatePagerActive(slider.active.index)
      }

      // check for direction control update
      if (slider.settings.controls) {
        updateDirectionControls()
      }

      if (slider.settings.mode === 'fade') {

        // if adaptiveHeight is true and next height is different from current height, animate to the new height
        if (slider.settings.adaptiveHeight && slider.viewport.height() !== getViewportHeight()) {
          slider.viewport.animate({height: getViewportHeight()}, slider.settings.adaptiveHeightSpeed)
        }

        // fade out the visible child and reset its z-index value
        slider.children.filter(':visible').fadeOut(slider.settings.speed).css({zIndex: 0})

        // fade in the newly requested slide
        slider.children.eq(slider.active.index).css('zIndex', slider.settings.slideZIndex + 1).fadeIn(slider.settings.speed, function () {
          $(this).css('zIndex', slider.settings.slideZIndex)
          updateAfterSlideTransition()
        })

      }
      // slider mode is not "fade"
      else {

        // if adaptiveHeight is true and next height is different from current height, animate to the new height
        if (slider.settings.adaptiveHeight && slider.viewport.height() !== getViewportHeight()) {
          slider.viewport.animate({height: getViewportHeight()}, slider.settings.adaptiveHeightSpeed)
        }

        // if carousel and not infinite loop
        if (!slider.settings.infiniteLoop && slider.carousel && slider.active.last) {

          if (slider.settings.mode === 'horizontal') {
            // get the last child position
            lastChild = slider.children.eq(slider.children.length - 1)
            position = lastChild.position()
            // calculate the position of the last slide
            moveBy = slider.viewport.width() - lastChild.outerWidth()
          }
          else {
            // get last showing index position
            lastShowingIndex = slider.children.length - slider.settings.minSlides
            position = slider.children.eq(lastShowingIndex).position()
          }

        }
        // horizontal carousel, going previous while on first slide (infiniteLoop mode)
        else if (slider.carousel && slider.active.last && direction === 'prev') {

          // get the last child position
          eq = slider.settings.moveSlides === 1 ? slider.settings.maxSlides - getMoveBy() : ((getPagerQty() - 1) * getMoveBy()) - (slider.children.length - slider.settings.maxSlides)
          lastChild = el.children('.wpmslider-clone').eq(eq)
          position = lastChild.position()

        }
        // if infinite loop and "Next" is clicked on the last slide
        else if (direction === 'next' && slider.active.index === 0) {

          // get the last clone position
          position = el.find('> .wpmslider-clone').eq(slider.settings.maxSlides).position()
          slider.active.last = false

        }
        // normal non-zero requests
        else if (slideIndex >= 0) {

          //parseInt is applied to allow floats for slides/page
          requestEl = slideIndex * parseInt(getMoveBy())
          position = slider.children.eq(requestEl).position()

        }

        /* If the position doesn't exist
         * (e.g. if you destroy the slider on a next click),
         * it doesn't throw an error.
         */
        if (typeof position !== 'undefined') {

          // value = slider.settings.mode === 'horizontal' ? -(position.left - moveBy) : -position.top
          if (slider.settings.mode === 'horizontal') {
            value = -(position.left - moveBy)
          }
          else {
            value = -position.top
          }

          // plugin values to be animated
          setPositionProperty(value, 'slide', slider.settings.speed)

        }
        else {
          slider.working = false
        }

      }

      if (slider.settings.ariaHidden) {
        applyAriaHiddenAttributes(slider.active.index * getMoveBy())
      }
    }

    /**
     * Transitions to the next slide in the show
     */
    el.goToNextSlide = function () {
      // if infiniteLoop is false and last page is showing, disregard call
      if (!slider.settings.infiniteLoop && slider.active.last) {
        return
      }
      var pagerIndex = parseInt(slider.active.index) + 1
      el.goToSlide(pagerIndex, 'next')
    }

    /**
     * Transitions to the prev slide in the show
     */
    el.goToPrevSlide = function () {
      // if infiniteLoop is false and last page is showing, disregard call
      if (!slider.settings.infiniteLoop && slider.active.index === 0) {
        return
      }
      var pagerIndex = parseInt(slider.active.index) - 1
      el.goToSlide(pagerIndex, 'prev')
    }

    /**
     * Starts the auto show
     *
     * @param preventControlUpdate (boolean)
     *  - if true, auto controls state will not be updated
     */
    el.startAuto = function (preventControlUpdate) {
      // if an interval already exists, disregard call
      if (slider.interval) {
        return
      }

      // create an interval
      slider.interval = setInterval(function () {
        if (slider.settings.autoDirection === 'next') {
          el.goToNextSlide()
        }
        else {
          el.goToPrevSlide()
        }
      }, slider.settings.pause)

      // if auto controls are displayed and preventControlUpdate is not true
      if (slider.settings.autoControls && preventControlUpdate !== true) {
        updateAutoControls('stop')
      }
    }

    /**
     * Stops the auto show
     *
     * @param preventControlUpdate (boolean)
     *  - if true, auto controls state will not be updated
     */
    el.stopAuto = function (preventControlUpdate) {
      // if no interval exists, disregard call
      if (!slider.interval) {
        return
      }
      // clear the interval
      clearInterval(slider.interval)
      slider.interval = null
      // if auto controls are displayed and preventControlUpdate is not true
      if (slider.settings.autoControls && preventControlUpdate !== true) {
        updateAutoControls('start')
      }
      //clearInterval(el.visibilityInterval);
    }

    /**
     * Returns current slide index (zero-based)
     */
    el.getCurrentSlide = function () {
      return slider.active.index
    }

    /**
     * Returns current slide element
     */
    el.getCurrentSlideElement = function () {
      return slider.children.eq(slider.active.index)
    }

    /**
     * Returns a slide element
     * @param index (int)
     *  - The index (zero-based) of the element you want returned.
     */
    el.getSlideElement = function (index) {
      return slider.children.eq(index)
    }

    /**
     * Returns number of slides in show
     */
    el.getSlideCount = function () {
      return slider.children.length
    }

    /**
     * Return slider.working variable
     */
    el.isWorking = function () {
      return slider.working
    }

    /**
     * Update all dynamic slider elements
     */
    el.redrawSlider = function () {
      // resize all children in ratio to new screen size
      slider.children.add(el.find('.wpmslider-clone')).outerWidth(getSlideWidth())
      // adjust the height
      slider.viewport.css('height', getViewportHeight())
      // update the slide position
      setSlidePosition()
      // if active.last was true before the screen resize, we want
      // to keep it last no matter what screen size we end on
      if (slider.active.last) {
        slider.active.index = getPagerQty() - 1
      }
      // if the active index (page) no longer exists due to the resize, simply set the index as last
      if (slider.active.index >= getPagerQty()) {
        slider.active.last = true
      }
      // if a pager is being displayed and a custom pager is not being used, update it
      if (slider.settings.pager && !slider.settings.pagerCustom) {
        populatePager()
        updatePagerActive(slider.active.index)
      }
      if (slider.settings.ariaHidden) {
        applyAriaHiddenAttributes(slider.active.index * getMoveBy())
      }
    }

    /**
     * Destroy the current instance of the slider (revert everything back to original state)
     */
    el.destroySlider = function () {
      // don't do anything if slider has already been destroyed
      if (!slider.initialized) {
        return
      }
      slider.initialized = false
      $('.wpmslider-clone', this).remove()

      slider.children.each(function () {
        if ($(this).data('origStyle') !== undefined) {
          $(this).attr('style', $(this).data('origStyle'))
        }
        else {
          $(this).removeAttr('style')
        }
      })

      if ($(this).data('origStyle') !== undefined) {
        this.attr('style', $(this).data('origStyle'))
      }
      else {
        $(this).removeAttr('style')
      }

      $(this).unwrap().unwrap()

      if (slider.controls.el) {
        slider.controls.el.remove()
      }
      if (slider.controls.next) {
        slider.controls.next.remove()
      }
      if (slider.controls.prev) {
        slider.controls.prev.remove()
      }
      if (slider.pagerEl && slider.settings.controls && !slider.settings.pagerCustom) {
        slider.pagerEl.remove()
      }

      $('.wpmslider-caption', this).remove()

      if (slider.controls.autoEl) {
        slider.controls.autoEl.remove()
      }

      clearInterval(slider.interval)
      clearInterval(slider.visibilityInterval)
      
      if (slider.settings.responsive) {
        $(window).off('resize', resizeWindow)
      }

      if (slider.settings.keyboardEnabled) {
        $(document).off('keydown', keyPress)
      }

      //remove self reference in data
      $(this).removeData('strongSlider')
    }

    /**
     * Reload the slider (revert all DOM changes, and re-initialize)
     */
    el.reloadSlider = function (settings) {
      if (settings !== undefined) {
        options = settings
      }
      el.destroySlider()
      init()
      // Store reference to self in order to access public functions later
      $(el).data('strongSlider', this)
    }

    // Fire it up!
    init()

    // Store reference to self in order to access public functions later
    $(el).data('strongSlider', this)

    // Set initialized flag on container
    viewEl.attr('data-state', 'init')

    if (slider.debug) console.log(slider.logAs, 'viewport', verge.viewportW(), 'x', verge.viewportH())

    // returns the current jQuery object
    return this
  }

})(jQuery)
