(function($) {
    'use strict';
    $(function(){
        $('body').on('click', 'button.notice-dismiss', function(e) {
            var $div_id = $(this).parent('.notice').attr('id');
            $.ajax({
                url:    ajaxurl,
                method: 'POST',
                data:   {
                    action:         'dismiss-notice',
                    nonce:          products_screen_option.nonce,
                    dismiss_notice: $div_id
                },
            });
        });
    });
})(jQuery);