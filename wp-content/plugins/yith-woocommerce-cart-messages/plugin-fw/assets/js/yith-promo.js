(function ($) {
    $(document).on('click', '.notice-dismiss', function () {
        var t = $(this),
            promo_wrapper = t.parent('div.yith-notice-is-dismissible'),
            promo_id = promo_wrapper.attr('id');

        if (typeof promo_id != 'undefined') {
            var cname = 'hide_' + promo_id,
                cvalue = 'yes',
                expiry = promo_wrapper.data('expiry'),
                expiry_date = new Date(expiry);

            expiry_date.setUTCHours( 23 );
            expiry_date.setUTCMinutes( 59 );
            expiry_date.setUTCSeconds( 59 );

            document.cookie = cname + "=" + cvalue + ";" + 'expires=' + expiry_date.toUTCString() + ";path=/";
        }
    });
})(jQuery);
