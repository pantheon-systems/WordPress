<script type="text/javascript">
    (function ($) {
        $(function () {

            // Main accordion logic
            $('input[name="scheduling_enable"]').change(function () {
                if ($('input[name="scheduling_enable"]:checked').val() == 1) {
                    $('#automatic-scheduling').slideDown();
                    $('.manual-scheduling').slideUp();
                    setTimeout(function () {
                        $('.timezone-select').slideDown(275);
                    }, 200);
                }
                else if ($('input[name="scheduling_enable"]:checked').val() == 2) {
                    $('.timezone-select').slideUp(275);
                    $('#automatic-scheduling').slideUp();
                    $('.manual-scheduling').slideDown();
                } else {
                    $('.timezone-select').hide();
                    $('#automatic-scheduling').slideUp();
                    $('.manual-scheduling').slideUp();
                }
            });
        });
    })(jQuery);
</script>