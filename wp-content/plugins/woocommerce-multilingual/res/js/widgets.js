jQuery(document).ready(function($){
    $(document).on('widget-added', function (e, widget) {
        var button = widget.find('.wcml-cs-widgets-edit-link');

        if (button.length > 0) {
            var sidebar_slug = widget.closest('.widgets-sortables').attr('id'),
                link         = button.attr('href');

            if ('#currency-switcher/' === link.slice(-19)) {
                button.attr('href', link + sidebar_slug);
            }
        }
    });
});

