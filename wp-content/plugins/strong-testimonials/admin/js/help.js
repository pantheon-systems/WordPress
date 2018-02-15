(function ($) {
  'use strict'

  $('.open-help-tab').on('click', function () {

    var tab = this.hash

    // Find the tab in the Help container
    var tabLink = $('#contextual-help-columns').find('a[href="' + tab + '"]')

    if ($('#screen-meta').is(':hidden')) {
      // If Help container is closed, open it, then select tab
      $('#contextual-help-link').click().promise().done(function () {
        tabLink.click()
      })
    }

    $('html, body').animate({scrollTop: 0}, 800)

    return false
  })
})(jQuery)
