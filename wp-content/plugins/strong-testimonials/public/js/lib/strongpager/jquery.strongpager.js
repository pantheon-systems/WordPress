/*!
 * jQuery Strong Pager Plugin
 * Version 2.0.1
 *
 * Copyright (c) 2017 Chris Dillon
 * Released under the MIT license
 */
;(function ($) {

  var defaults = {
    pageSize: 10,
    currentPage: 1,
    pagerLocation: 'after',
    scrollTop: 1,
    offset: 40,
    div: '.strong-content',
    imagesLoaded: true
  }

  $.fn.strongPager = function (options) {

    if (this.length === 0) {
      return this
    }

    // create a namespace to be used throughout the plugin
    var pager = {}
    // set a reference to our view container
    var el = this

    /**
     * Initialize
     */
    var init = function () {
      var pagerVar = el.data('pager-var')
      var config = {}

      if (typeof( window[pagerVar] ) !== 'undefined') {
        config = window[pagerVar].config
      }

      // Merge user options with the defaults
      pager.settings = $.extend({}, defaults, config, options)

      pager.div = el.find(pager.settings.div)
      pager.pageCounter = 0
      pager.scrollto = 0
      pager.currentPage = pager.settings.currentPage
      pager.visibilityInterval = 0

      // Wait for images loaded
      if (pager.settings.imagesLoaded) {
        el.imagesLoaded(setup)
      } else {
        setup()
      }

      // Store reference to self in order to access public functions later
      $(el).data('strongPager', this)

      // Set initialized flag
      el.attr("data-state","init")

    }

    /**
     * Scroll upon navigation
     */
    var scroll = function () {
      if (pager.settings.scrollTop) {
        $('html, body').animate({scrollTop: pager.scrollto}, 800)
      }
    }

    /**
     * Paginate
     */
    var paginate = function () {
      var pageCounter = 1

      pager.div.wrap('<div class="simplePagerContainer"></div>')

      pager.div.children().each(function (i) {
        var rangeEnd = pageCounter * pager.settings.pageSize - 1
        if (i > rangeEnd) {
          pageCounter++
        }
        $(this).addClass('simplePagerPage' + pageCounter)
      })

      pager.pageCounter = pageCounter
    }

    /**
     * Calculate offset for scrolling
     */
    var findOffset = function () {
      var containerOffset

      // WooCommerce product tabs
      if (el.closest('.woocommerce-tabs').length) {
        containerOffset = el.closest('.woocommerce-tabs').offset()
      } else {
        containerOffset = el.find('.simplePagerContainer').offset()
      }

      pager.scrollto = ~~(containerOffset.top - pager.settings.offset)

      // WordPress admin bar
      if (document.getElementById('#wpadminbar')) {
        pager.scrollto -= 32
      }
    }

    /**
     * Hide all and show current
     */
    var switchPages = function (fade) {
      // Hide the pages
      pager.div.children().hide()

      // Show the container which now has paging controls
      el.show()

      // Show the current page
      var newPage = pager.div.children('.simplePagerPage' + pager.currentPage)
      if (fade) {
        newPage.fadeIn()
      } else {
        newPage.show()
      }
    }

    /**
     * Add navigation
     */
    var addNavigation = function () {
      var nav = '<ul class="simplePagerNav">'
      var cssClass

      for (var i = 1; i <= pager.pageCounter; i++) {
        cssClass = ''
        if (i === pager.currentPage) {
          cssClass = 'currentPage '
        }
        nav += '<li class="' + cssClass + 'simplePageNav' + i + '"><a rel="' + i + '" href="#">' + i + '</a></li>'
      }
      nav += '</ul>'
      nav = '<div class="simplePagerList">' + nav + '</div>'

      switch (pager.settings.pagerLocation) {
        case 'before':
          pager.div.before(nav)
          break
        case 'both':
          pager.div.before(nav)
          pager.div.after(nav)
          break
        default:
          pager.div.after(nav)
      }
    }

    /**
     * Navigation behavior
     */
    var navigationHandler = function () {
      el.find('.simplePagerNav a').click(function (e) {
        var $this = $(e.target)
        var container

        container = $this.closest('.simplePagerContainer')

        // Get the REL attribute
        pager.currentPage = $this.attr('rel')

        // Remove current page highlight
        container.find('li.currentPage').removeClass('currentPage')

        // Add current page highlight
        container.find('a[rel="' + pager.currentPage + '"]').parent('li').addClass('currentPage')

        // Switch pages
        switchPages(true)

        // Scroll up for any nav click
        scroll()

        return false
      })
    }

    /**
     * Visibility check.
     */
    var visibilityCheck = function () {
      if (el.is(':visible')) {
        clearInterval(pager.visibilityInterval)
        findOffset()
      }
    }

    /**
     * Setup
     */
    var setup = function () {
      paginate()
      // Bail if only one page
      if (pager.pageCounter > 1) {
        addNavigation()
        navigationHandler()
      }

      switchPages()

      // Set up timer to calculate offset which is dependent on visibility
      pager.visibilityInterval = setInterval( visibilityCheck, 500 );
    }

    /**
     * Start it up
     */
    init()

    return this
  }

})(jQuery)
