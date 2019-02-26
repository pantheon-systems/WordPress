;(function($) {
  $(document).ready(function () {
    $('.manual-review-checkbox').click(function() {
      return confirm(woocommerce_ebanx_manual_reviews_options.confirm_message);
    });
  });
})(jQuery);
