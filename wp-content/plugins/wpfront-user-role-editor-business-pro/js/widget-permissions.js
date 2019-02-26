(function ($) {
    var ROLES_USERS = 4;

    $(function () {
        $('.widgets-holder-wrap').on('change', 'input.user-restriction-type', function () {
            var $this = $(this);
            if ($this.val() == ROLES_USERS) {
                $this.closest('span.user-restriction-container').find('span.roles-container').removeClass('hidden');
            } else {
                $this.closest('span.user-restriction-container').find('span.roles-container').addClass('hidden');
            }
        });
    });
})(jQuery);