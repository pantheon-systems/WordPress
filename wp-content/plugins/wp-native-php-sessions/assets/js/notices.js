jQuery(document).ready(function($) {
  $(document).on('click', '.notice-dismiss', function() {
      $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
              action: 'dismiss_notice'
          }
      });
  });
});
