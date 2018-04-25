/**
 * Component controller
 *
 * @namespace window.strongControllerParms
 */

'use strict'

var debugit = false

var strongController = {

  defaults: {
    method: "",
    universalTimer: 500,
    observerTimer: 500,
    containerId: "page",    // = what we listen to  (try page > content > primary)
    addedNodeId: "content", // = what we listen for
    event: "",
    script: "",
    debug: false
  },

  config: {},

  setup: function (settings) {
    // Convert strings to integers
    settings.universalTimer = parseInt(settings.universalTimer)
    settings.observerTimer = parseInt(settings.observerTimer)
    // Convert strings to booleans
    settings.debug = !!settings.debug
    debugit = settings.debug
    this.config = jQuery.extend({}, this.defaults, settings)
  },

  mutationObserver: window.MutationObserver || window.WebKitMutationObserver,

  eventListenerSupported: window.addEventListener,

  checkInit: function () {
    return jQuery(".strong-view[data-state='idle']").length
  },

  /**
   * Initialize sliders.
   */
  initSliders: function () {
    var sliders = jQuery(".strong-view.slider-container[data-state='idle']")
    if (debugit) console.log('sliders found:', sliders.length)
    if (sliders.length) {
      // Initialize independently
      sliders.each(function () {
        jQuery(this).strongSlider()
      })
    }
  },

  /**
   * Initialize paginated views.
   */
  initPagers: function () {
    var pagers = jQuery(".strong-pager[data-state='idle']")
    if (debugit) console.log('pagers found:', pagers.length)
    if (pagers.length) {
      pagers.each( function () {
        jQuery(this).strongPager()
      })
    }
  },

  /**
   * Initialize layouts.
   */
  initLayouts: function () {
    /*
     * Masonry
     */
    var grids = jQuery(".strong-view[data-state='idle'] .strong-masonry")
    if (debugit) console.log('Masonry found:', grids.length)
    if (grids.length) {
      // Add our element sizing.
      grids.prepend('<div class="grid-sizer"></div><div class="gutter-sizer"></div>')

      // Initialize Masonry after images are loaded.
      grids.imagesLoaded(function () {
        grids.masonry({
          columnWidth: '.grid-sizer',
          gutter: '.gutter-sizer',
          itemSelector: '.testimonial',
          percentPosition: true
        })
        grids.closest('.strong-view').attr('data-state', 'init')
      })
    }

  },

  /**
   * Initialize form validation.
   */
  initForm: function () {
    var forms = jQuery(".strong-form[data-state='idle']")
    if (debugit) console.log('forms found:', forms.length)
    if (forms.length) {
      strongValidation.init()
      // initialize Captcha plugins here
    }
  },

  /**
   * Create observer that reacts to nodes added or removed.
   *
   * https://stackoverflow.com/a/14570614/51600
   */
  observer: function (obj, callback) {
    if (this.mutationObserver) {

      // Define a new observer
      var obs = new this.mutationObserver(function (mutations) {
        // Loop through mutations
        for (var i=0; i < mutations.length; i++) {
          if (mutations[i].addedNodes.length) {
            if (debugit) console.log('mutation observed', mutations)
            // Loop through added nodes
            for (var j = 0; j < mutations[i].addedNodes.length; j++) {
              if (mutations[i].addedNodes[j].id === strongController.config.containerId) {
                if (debugit) console.log('+', strongController.config.containerId)
                callback()
                return
              }
            }
          }
        }
      })
      // Have the observer observe obj for changes
      obs.observe(obj, {childList: true, subtree: true})

    } else if (this.eventListenerSupported) {

      obj.addEventListener('DOMNodeInserted', function(e) {
        /** currentTarget **/
        if ( e.currentTarget.id === obj.id ) {
          if (debugit) console.log('DOMNodeInserted:', e.currentTarget.id)
          callback()
        }
      }, false)

    }
  },

  /**
   * Timer variables
   */
  intervalId: null,
  timeoutId: null,

  /**
   * Set up interval
   */
  newInterval: function () {
      strongController.intervalId = setInterval(function tick () {
        if (debugit) console.log('tick > checkInit', strongController.checkInit())

        // Check for uninitialized components (sliders, paginated, layouts)
        if (strongController.checkInit()) {
          strongController.start()
        }
      }, strongController.config.universalTimer)
  },

  /**
   * Set up timeout
   */
  newTimeout: function () {
      strongController.timeoutId = setTimeout(function tick () {
        if (debugit) console.log('tick > checkInit', strongController.checkInit())

        // Check for uninitialized components (sliders, paginated, layouts)
        if (strongController.checkInit()) {
          strongController.start()
        }
      }, strongController.config.observerTimer)
  },

  /**
   * Initialize controller.
   */
  init: function () {
    jQuery(document).focus() // if dev console open
    if (debugit) console.log('strongController init')

    var settings = {}
    if (typeof window.strongControllerParms !== 'undefined') {
      settings = window.strongControllerParms
    } else {
      if (debugit) console.log('settings not found')
    }
    this.setup(settings)
    if (debugit) console.log('config', this.config)
  },

  /**
   * Start components.
   */
  start: function() {
    if (debugit) console.log('start')
    strongController.initSliders()
    strongController.initPagers()
    strongController.initLayouts()
    strongController.initForm()
  },

  /**
   * Listen.
   */
  listen: function() {
    if (debugit) console.log('listen')

    switch (this.config.method) {
      case 'universal':
        // Set a timer to check for idle components.
        this.newInterval()
        break

      case 'observer':
        // Observe a specific DOM element on a timer.
        // Calling start() here is too soon; the transition is not complete yet.
        this.observer(document.getElementById(this.config.containerId), this.newTimeout)
        break

      case 'event':
        // The theme/plugin uses an event emitter.

        // jQuery Pjax -!- Not working in any theme tested yet -!-
        // event name = pjax:end

        // Pjax by MoOx
        // @link https://github.com/MoOx/pjax
        // event name = pjax:success

        // Ajax Pagination and Infinite Scroll by Malinky
        // @link https://wordpress.org/plugins/malinky-ajax-pagination/
        // event name = malinkyLoadPostsComplete

        document.addEventListener(this.config.event, this.start)
        break

      case 'script':
        // The theme/plugin uses a dispatcher.

        switch (this.config.script) {
          case 'barba':
            // Barba
            // @link http://barbajs.org/
            if (typeof Barba === 'object' && Barba.hasOwnProperty('Dispatcher')) {
              Barba.Dispatcher.on('transitionCompleted', this.start)
            }
            break
          default:
        }
        break

      default:
      // no Pjax support
    }
  }

}

jQuery(document).ready(function ($) {

  // Initialize controller.
  strongController.init()

  // Start components.
  strongController.start()

  // Listen.
  strongController.listen()

})
