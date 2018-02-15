/**
 * Strong Testimonials admin
 *
 * @namespace wpmtst_admin
 */

jQuery(document).ready(function ($) {
  /**
   * ----------------------------------------
   * Persistent admin notices.
   * Dismissible from any page.
   * ----------------------------------------
   */
  $('.wpmtst.notice.is-dismissible').on('click', '.notice-dismiss', function (event) {
    event.preventDefault()
    var $this = $(this)
    if ('undefined' === $this.parent().attr('data-key')) {
      return
    }
    $.post(ajaxurl, {
      action: 'wpmtst_dismiss_notice',
      key: $this.parent().attr('data-key'),
      nonce: wpmtst_admin.nonce
    })
  })
})
