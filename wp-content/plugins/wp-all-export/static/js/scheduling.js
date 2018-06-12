(function ($) {
    $(function () {
        $(document).ready(function () {

            window.pmxeISchedulingFormValid = function () {

                var schedulingEnabled = $('#scheduling_enable').is(':checked');

                if (!schedulingEnabled) {
                    return {
                        isValid: true
                    };
                }

                var runOn = $('input[name="scheduling_run_on"]:checked').val();

                // Validate weekdays
                if (runOn == 'weekly') {
                    var weeklyDays = $('#weekly_days').val();

                    if (weeklyDays == '') {
                        return {
                            isValid: false,
                            message: 'Please select at least a day on which the export should run'
                        }
                    }
                } else if (runOn == 'monthly') {
                    var monthlyDays = $('#monthly_days').val();

                    if (monthlyDays == '') {
                        return {
                            isValid: false,
                            message: 'Please select at least a day on which the export should run'
                        }
                    }
                }

                // Validate times
                var timeValid = false;
                var timeInputs = $('.timepicker');

                timeInputs.each(function () {
                    if ($(this).val() != '') {
                        timeValid = true;
                    }
                });

                if (!timeValid) {
                    return {
                        isValid: false,
                        message: 'Please select at least a time'
                    };
                }

                return {
                    isValid: true
                };
            };

            $('#weekly li').click(function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    $(this).addClass('selected');
                }

                $('#weekly_days').val('');

                $('#weekly li.selected').each(function () {
                    var val = $(this).data('day');
                    $('#weekly_days').val($('#weekly_days').val() + val + ',');
                });

                $('#weekly_days').val($('#weekly_days').val().slice(0, -1));

            });

            $('#monthly li').click(function () {
                $(this).parent().parent().find('.days-of-week li').removeClass('selected');
                $(this).addClass('selected');
            });

            $('input[name="scheduling_run_on"]').change(function () {
                var val = $('input[name="scheduling_run_on"]:checked').val();
                if (val == "weekly") {

                    $('#weekly').slideDown();
                    $('#monthly').slideUp();

                } else if (val == "monthly") {

                    $('#weekly').slideUp();
                    $('#monthly').slideDown();

                }
            });

            $('.timepicker').timepicker();

            var selectedTimes = [];

            var onTimeSelected = function () {

                selectedTimes.push([$(this).val(), $(this).val() + 1]);

                var isLastChild = $(this).is(':last-child');
                if (isLastChild) {
                    $(this).parent().append('<input class="timepicker" name="scheduling_times[]" style="display: none;" type="text" />');
                    $('.timepicker:last-child').timepicker({
                        'disableTimeRanges': selectedTimes
                    });
                    $('.timepicker:last-child').fadeIn('fast');
                    $('.timepicker').on('changeTime', onTimeSelected);
                }
            };

            $('.timepicker').on('changeTime', onTimeSelected);

            $('#timezone').chosen({width: '329px'});

            $(document).on('wpae-scheduling-form:submitted', function(e){
                // Do this to cancel the form submit
                // e.preventDefault();

                $(this).find('.easing-spinner').toggle();
                $(this).find('.save-text').html('Saving...');

                var $button = $(this);

                var schedulingEnable = $('#scheduling_enable').is(':checked');

                var formData = $('#scheduling-form :input').serializeArray();

                formData.push({name: 'security', value: wp_all_export_security});
                formData.push({name: 'action', value: 'save_scheduling'});
                formData.push({name: 'scheduling_enable', value: schedulingEnable});

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: formData,
                    success: function (response) {
                        $button.find('.easing-spinner').toggle();
                        $button.find('.save-text').html('Save');
                        $button.find('svg').show();
                        $button.find('svg').fadeOut(5000);
                    },
                    error: function () {
                        $button.find('.easing-spinner').toggle();
                        $button.find('.save-text').html('Save');
                    }
                });
            });

        });
    });
})(jQuery);