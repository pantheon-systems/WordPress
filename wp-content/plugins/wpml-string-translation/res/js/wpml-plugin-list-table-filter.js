(function ($, window) {
    "use strict";

    $(document).ready(function () {
        $('.wpml_plugin_table_filters').find('a').each(function (i, element) {
            $(element).on('click', function (e) {
                e.preventDefault();
                var element = $(this),
                    filter = element.attr('href').replace('#', '');

                $("#wpml_strings_in_plugins > tbody > tr").filter(function () {
                    var tr = $(this),
                        display = '';
                    if (filter && 'all' !== filter && filter !== tr.data('plugin-status')) {
                        display = 'none';
                    }
                    tr.css('display', display);
                });
            });
        });
    });

})(jQuery, window);
