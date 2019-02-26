jQuery(function($) {
    /**
     * DISMISS NOTICES
     * 
     * @since 2.0.4
     */
    $('.wpmm_notices').on('click', '.notice-dismiss', function() {
        var notice_key = $(this).parent().data('key');
        
        $.post(ajaxurl, {
            action: 'wpmm_dismiss_notices',
            notice_key: notice_key
        }, function(response) {
            if (!response.success) {
//                alert(response.data);
                return false;
            }
        }, 'json');
    });
});