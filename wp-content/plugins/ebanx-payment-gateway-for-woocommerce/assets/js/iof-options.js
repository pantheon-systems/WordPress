;(function($) {
  $(document).ready(function () {
    $('.iof-checkbox').click(function() {
      return confirm(woocommerce_ebanx_iof_options.confirm_message);
    });
  });
})(jQuery);
