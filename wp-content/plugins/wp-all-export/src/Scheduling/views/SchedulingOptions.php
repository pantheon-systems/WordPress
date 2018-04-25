<?php
$scheduling = \Wpae\Scheduling\Scheduling::create();
$post = $export->options;
$hasActiveLicense = $scheduling->checkLicense();
$cron_job_key = PMXE_Plugin::getInstance()->getOption('cron_job_key');
$options = \PMXE_Plugin::getInstance()->getOption();
?>
<style type="text/css">
    .days-of-week {
        margin-left: 5px;
    }

    .days-of-week li {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px 30px;;
        display: inline-block;
        margin-right: 10px;
        cursor: pointer;
        font-weight: bold;
        width: 38px;
        text-align: center;
        height: 16px;
        color: rgb(68,68,68);
        float: left;
    }

    .days-of-week li.selected {
        color: #fff;
        background-color: #425F9A;
        border-color: #585858;
    }

    #weekly, #monthly {
        height: 40px;
    }

    .timepicker {
        width: 100px;
        padding: 10px;
        border-radius: 5px;
        margin-right: 10px;
    }

    #times {
        margin-top: 5px;
        width: 800px;
    }

    #times input {
        margin-top: 10px;
        margin-left: 0;
        float: left;
    }

    #times input.error {
        border-color: red !important;
    }

    .subscribe {

    }

    .subscribe .button-container {
        float: left;
        width: 150px;
    }

    .subscribe .text-container {
        float: left;
        width: auto;
    }

    .subscribe .text-container p {
        margin: 0;
        color: #425F9A;
        font-size: 14px;
        font-weight: bold;
    }

    .subscribe .text-container p a {
        color: #425F9A;
        text-decoration: underline;
    }

    .save {
        padding-left: 5px;
        padding-top: 5px;
        width: auto;
    }

    .ui-timepicker-wrapper {
        width: 98px;
    }

    .easing-spinner {
        width: 30px;
        height: 30px;
        position: relative;
        display: inline-block;

        margin-top: 7px;
        margin-left: -25px;

        float: left;
    }

    .double-bounce1, .double-bounce2 {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #fff;
        opacity: 0.6;
        position: absolute;
        top: 0;
        left: 0;

        -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
        animation: sk-bounce 2.0s infinite ease-in-out;
    }

    .double-bounce2 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .wpae-save-button svg {
        margin-top: 7px;
        margin-left: -215px;
        display: inline-block;
        position: relative;
    }

    @-webkit-keyframes sk-bounce {
        0%, 100% {
            -webkit-transform: scale(0.0)
        }
        50% {
            -webkit-transform: scale(1.0)
        }
    }

    @keyframes sk-bounce {
        0%, 100% {
            transform: scale(0.0);
            -webkit-transform: scale(0.0);
        }
        50% {
            transform: scale(1.0);
            -webkit-transform: scale(1.0);
        }
    }

    #add-subscription-field {
        position: absolute;
        left: -152px;
        top: -2px;
        height: 46px;
        border-radius: 5px;
        font-size: 17px;
        padding-left: 10px;
        display: none;
        width: 140px;
    }

    #find-subscription-link {
        position: absolute;
        left: 133px;
        top: 14px;
        height:30px;
        width: 300px;
        display: none;
    }

    #find-subscription-link a {
        display: block;
        width: 100%;
        height: 46px;
        white-space: nowrap;
    }

    #weekly li.error, #monthly li.error {
        border-color: red;
    }

    .chosen-single {
        margin-bottom: 0 !important;
    }

    .chosen-container.chosen-with-drop .chosen-drop {
        margin-top: -3px;
    }

    .wpallexport-preview-content h4{
        font-size: 14px;
        margin-bottom: 5px;
        color: #40acad;
        text-decoration: none;
    }
    .manual-scheduling { margin-left: 26px;}
</style>

<script type="text/javascript">
    (function ($) {
        $(function () {

            var hasActiveLicense = <?php echo $hasActiveLicense? 'true':'false'; ?>;

            function isAutomaticSchedulingEnabled() {
                return $('input[name="scheduling_enable"]:checked').val() == 1;
            }

            $(document).ready(function () {

                function openSchedulingAccordeonIfClosed() {
                    if ($('.wpallexport-file-options').hasClass('closed')) {
                        // Open accordion
                        $('#scheduling-title').trigger('click');
                    }
                }

                window.pmxeValidateSchedulingForm = function () {

                    var schedulingEnabled = isAutomaticSchedulingEnabled();

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
                            $('#weekly li').addClass('error');
                            return {
                                isValid: false,
                                message: 'Please select at least a day on which the export should run'
                            }
                        }
                    } else if (runOn == 'monthly') {
                        var monthlyDays = $('#monthly_days').val();

                        if (monthlyDays == '') {
                            $('#monthly li').addClass('error');
                            return {
                                isValid: false,
                                message: 'Please select at least a day on which the export should run'
                            }
                        }
                    }

                    // Validate times
                    var timeValid = true;
                    var timeMessage = 'Please select at least a time for the export to run';
                    var timeInputs = $('.timepicker');
                    var timesHasValues = false;

                    timeInputs.each(function (key, $elem) {

                        if($(this).val() !== ''){
                            timesHasValues = true;
                        }

                        if (!$(this).val().match(/^(0?[1-9]|1[012])(:[0-5]\d)[APap][mM]$/) && $(this).val() != '') {
                            $(this).addClass('error');
                            timeValid = false;
                        } else {
                            $(this).removeClass('error');
                        }
                    });

                    if(!timesHasValues) {
                        timeValid = false;
                        $('.timepicker').addClass('error');
                    }

                    if (!timeValid) {
                        return {
                            isValid: false,
                            message: timeMessage
                        };
                    }

                    return {
                        isValid: true
                    };
                };



                $('#weekly li').click(function () {

                    $('#weekly li').removeClass('error');

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

                    $('#monthly li').removeClass('error');
                    $(this).parent().parent().find('.days-of-week li').removeClass('selected');
                    $(this).addClass('selected');

                    $('#monthly_days').val($(this).data('day'));
                });

                $('input[name="scheduling_run_on"]').change(function () {
                    var val = $('input[name="scheduling_run_on"]:checked').val();
                    if (val == "weekly") {

                        $('#weekly').slideDown({
                            queue: false
                        });
                        $('#monthly').slideUp({
                            queue: false
                        });

                    } else if (val == "monthly") {

                        $('#weekly').slideUp({
                            queue: false
                        });
                        $('#monthly').slideDown({
                            queue: false
                        });
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

                $('#timezone').chosen({width: '320px'});


                $('.wpae-save-button').click(function (e) {

                    var initialValue = $(this).find('.save-text').html();
                    var schedulingEnable = $('input[name="scheduling_enable"]:checked').val() == 1;
                    if(!hasActiveLicense) {
                        if (!$(this).data('iunderstand') && schedulingEnable) {
                            $('#no-subscription').slideDown();
                            $(this).find('.save-text').html('<?php echo _e('I Understand');?>');
                            $(this).find('.save-text').css('left', '100px');
                            $(this).data('iunderstand', 1);

                            openSchedulingAccordeonIfClosed();
                            e.preventDefault();
                            return;
                        } else {
                            var submitEvent = $.Event('wpae-scheduling-options-form:submit');
                            $(document).trigger(submitEvent);

                            return;
                        }
                    }

                    // Don't process scheduling
                    if (!schedulingEnable) {
                        var submitEvent = $.Event('wpae-scheduling-options-form:submit');
                        $(document).trigger(submitEvent);

                        return;
                    }

                    var validationResponse = pmxeValidateSchedulingForm();
                    if (!validationResponse.isValid) {

                        openSchedulingAccordeonIfClosed();
                        $('html, body').animate({
                            scrollTop: $("#scheduling-title").offset().top-100
                        }, 500);
                        e.preventDefault();
                        return false;
                    }
                    
                    var $button = $(this);

                    var formData = $('#scheduling-form :input').serializeArray();

                    formData.push({name: 'security', value: wp_all_export_security});
                    formData.push({name: 'action', value: 'save_scheduling'});
                    formData.push({name: 'element_id', value: <?php echo $export_id; ?>});
                    formData.push({name: 'scheduling_enable', value: $('input[name="scheduling_enable"]:checked').val()});

                    $button.find('.easing-spinner').toggle();

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: formData,
                        success: function (response) {
                            $button.find('.easing-spinner').toggle();
                            $button.find('.save-text').html(initialValue);
                            $button.find('svg').show();

                            setTimeout(function(){
                                var submitEvent = $.Event('wpae-scheduling-options-form:submit');
                                $(document).trigger(submitEvent);
                            }, 1000);

                        },
                        error: function () {
                            $button.find('.easing-spinner').toggle();
                            $button.find('.save-text').html(initialValue);
                        }
                    });
                });

                $('.wp_all_export_confirm_and_run').click(function(e){

                    e.preventDefault();

                    var schedulingEnable = isAutomaticSchedulingEnabled();

                    if(schedulingEnable) {


                        if(!hasActiveLicense) {
                            if (!$(this).data('iunderstand') && schedulingEnable) {
                                $('#no-subscription').slideDown();
                                $(this).find('.save-text').html('<?php echo _e('I Understand');?>');
                                $(this).find('.save-text').css('left', '100px');
                                $(this).data('iunderstand', 1);

                                openSchedulingAccordeonIfClosed();

                                $('html, body').animate({
                                    scrollTop: $(".wpallexport-submit-buttons").offset().top+200
                                }, 900);

                                e.preventDefault();
                                return;
                            } else {
                                var submitEvent = $.Event('wpae-scheduling-options-form:submit');
                                $(document).trigger(submitEvent);

                                return;
                            }
                        }

                        var validationResponse = pmxeValidateSchedulingForm();
                        if (!validationResponse.isValid) {

                            openSchedulingAccordeonIfClosed();
                            $('html, body').animate({
                                scrollTop: $("#scheduling-title").offset().top-100
                            }, 500);
                            e.preventDefault();
                            return false;
                        }

                        var $button = $('.wpae-scheduling-status');
                        $button.find('.easing-spinner').toggle();

                        var formData = $('#scheduling-form :input').serializeArray();

                        formData.push({name: 'security', value: wp_all_export_security});
                        formData.push({name: 'action', value: 'save_scheduling'});
                        formData.push({name: 'element_id', value: <?php echo $export_id; ?>});
                        formData.push({name: 'scheduling_enable', value: $('input[name="scheduling_enable"]:checked').val()});

                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: formData,
                            success: function (response) {
                                $button.find('.easing-spinner').toggle();
                                $button.find('svg').show();

                                setTimeout(function(){
                                    var submitEvent = $.Event('wpae-scheduling-options-form:submit');
                                    $(document).trigger(submitEvent);
                                }, 1000);

                            },
                            error: function () {
                                $button.find('.easing-spinner').toggle();
                            }
                        });

                        return false;
                    } else {
                        $('form.choose-export-options').submit();

                    }

                    return false;
                });

                <?php if($post['scheduling_timezone'] == 'UTC') {
                ?>
                    var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                    $('#timezone').val(timeZone);
                    $('#timezone').trigger("chosen:updated");

                <?php
                }
                ?>

                var saveSubscription = false;

                $('#add-subscription').click(function(){

                    $('#add-subscription-field').show();
                    $('#add-subscription-field').animate({width:'400px'}, 225);
                    $('#add-subscription-field').animate({left:'-1px'}, 225);
                    $('#subscribe-button .button-subscribe').css('background-color','#46ba69');

                    $('.text-container p').fadeOut();

                    setTimeout(function () {
                        $('#find-subscription-link').show();
                        $('#find-subscription-link').animate({left: '410px'}, 300, 'swing');
                    }, 225);
                    $('.subscribe-button-text').html('<?php _e('Activate'); ?>');
                    saveSubscription = true;
                    return false;
                });

                $('#subscribe-button').click(function(){

                    if(saveSubscription) {
                        $('#subscribe-button .easing-spinner').show();

                        var license = $('#add-subscription-field').val();
                        $.ajax({
                            url:ajaxurl+'?action=wpae_api&q=schedulingLicense/saveSchedulingLicense&security=<?php echo wp_create_nonce("wp_all_export_secure");?>',
                            type:"POST",
                            data: {
                                license: license
                            },
                            dataType:"json",
                            success: function(response){

                                $('#subscribe-button .button-subscribe').css('background-color','#425f9a');
                                if(response.success) {
                                    hasActiveLicense = true;
                                    $('#subscribe-button .easing-spinner').hide();
                                    $('#subscribe-button svg.success').show();
                                    $('#subscribe-button svg.success').fadeOut(3000, function () {
                                        $('.subscribe').hide({queue: false});
                                        $('#subscribe-filler').show({queue: false});
                                    });
                                } else {
                                    $('#subscribe-button .easing-spinner').hide();
                                    $('#subscribe-button svg.error').show();
                                    $('.subscribe-button-text').html('<?php _e('Subscribe'); ?>');

                                    $('#subscribe-button svg.error').fadeOut(3000, function () {
                                        $('#subscribe-button svg.error').hide({queue: false});

                                    });

                                    $('#add-subscription').html('<?php _e('Invalid license, try again?');?>');
                                    $('.text-container p').fadeIn();

                                    $('#find-subscription-link').animate({width: 'toggle'}, 300, 'swing');

                                    setTimeout(function () {
                                        $('#add-subscription-field').animate({width:'140px'}, 225);
                                        $('#add-subscription-field').animate({left:'-152px'}, 225);
                                    }, 300);

                                    $('#add-subscription-field').val('');

                                    $('#subscribe-button-text').html('<?php _e('Subscribe'); ?>');
                                    saveSubscription = false;
                                }
                            }
                        });

                        return false;
                    }
                });
            });
            // help scheduling template
            $('.help_scheduling').click(function(){

                $('.wp-all-export-scheduling-help').css('left', ($( document ).width()/2) - 255 ).show();
                $('#wp-all-export-scheduling-help-inner').css('max-height', $( window ).height()-150).show();
                $('.wpallexport-overlay').show();
                return false;
            });

            $('.wp_all_export_scheduling_help').find('h3').click(function(){
                var $action = $(this).find('span').html();
                $('.wp_all_export_scheduling_help').find('h3').each(function(){
                    $(this).find('span').html("+");
                });
                if ( $action == "+" ) {
                    $('.wp_all_export_help_tab').slideUp();
                    $('.wp_all_export_help_tab[rel=' + $(this).attr('id') + ']').slideDown();
                    $(this).find('span').html("-");
                }
                else{
                    $('.wp_all_export_help_tab[rel=' + $(this).attr('id') + ']').slideUp();
                    $(this).find('span').html("+");
                }
            });
        });
    })(jQuery);

</script>
<?php require __DIR__.'/CommonJs.php'; ?>
<div class="wpallexport-collapsed wpallexport-section wpallexport-file-options closed"
     style="margin-top: -10px; margin-bottom: 10px;">
    <div id="scheduling-form">

        <div class="wpallexport-content-section" style="padding-bottom: 15px; margin-bottom: 10px;">
            <div class="wpallexport-collapsed-header" id="scheduling-options-header" style="padding-left: 25px;">
                <h3 id="scheduling-title" style="position: relative;">
                    <?php _e('Scheduling Options', PMXE_Plugin::LANGUAGE_DOMAIN); ?>
                </h3>
            </div>

            <div class="wpallexport-collapsed-content" style="padding: 0; height: auto; display: none;">
                <div class="wpallexport-collapsed-content-inner" style="padding-bottom: 0; overflow: auto;">
                    <div style="margin-bottom: 20px;">
                        <label>
                            <input type="radio" name="scheduling_enable" value="0" <?php if(!$post['scheduling_enable']) { ?> checked="checked" <?php } ?>/>
                            <h4 style="display: inline-block;"><?php _e('Do Not Schedule'); ?></h4>
                        </label>
                    </div>
                    <div>
                        <label>
                        <input type="radio" name="scheduling_enable" value="1" <?php if($post['scheduling_enable'] == 1) {?> checked="checked" <?php }?>/>
                            <h4 style="margin-top: 0; position: relative; display: inline-block;"><?php _e('Automatic Scheduling', PMXE_Plugin::LANGUAGE_DOMAIN); ?>
                                <span class="connection-icon" style="position: absolute; top:-1px; left: 152px;">
                                    <?php include 'ConnectionIcon.php'; ?>
                                </span>
                                <?php if (!$scheduling->checkConnection() && $hasActiveLicense) { ?>
                                    <span style="margin-left: 25px; display: inline-block; font-weight: normal;">

                                        <span <?php if(!$scheduling->checkConnection() && $scheduling->checkLicense() ) { ?> style="color: #f2b03d;" <?php } ?>>Unable to connect to the scheduling Service. Please contact support at</span>
                                        <a style="text-decoration: underline; color: #0073aa;"
                                                href="http://wpallimport.com/support"
                                                target="_blank">http://wpallimport.com/support</a>
                            </span>
                                <?php } ?>
                            </h4>
                        </label>
                    </div>
                    <div style="margin-bottom: 10px; margin-left:26px;">
                        <label style="font-size: 13px;">
                            <?php _e('Run this export on a schedule.'); ?>
                        </label>
                    </div>
                    <div id="automatic-scheduling"
                         style="margin-left: 21px; <?php if ($post['scheduling_enable'] != 1) { ?> display: none; <?php } ?>">
                        <div>
                            <div class="input">
                                <label style="color: rgb(68,68,68);">
                                    <input
                                            type="radio" <?php if ($post['scheduling_run_on'] != 'monthly') { ?> checked="checked" <?php } ?>
                                            name="scheduling_run_on" value="weekly"
                                            checked="checked"/> <?php _e('Every week on...', PMXE_Plugin::LANGUAGE_DOMAIN); ?>
                                </label>
                            </div>
                            <input type="hidden" style="width: 500px;" name="scheduling_weekly_days"
                                   value="<?php echo $post['scheduling_weekly_days']; ?>" id="weekly_days"/>
                            <?php
                            if (isset($post['scheduling_weekly_days'])) {
                                $weeklyArray = explode(',', $post['scheduling_weekly_days']);
                            } else {
                                $weeklyArray = array();
                            }
                            ?>
                            <ul class="days-of-week" id="weekly" style="<?php if ($post['scheduling_run_on'] == 'monthly') { ?> display: none; <?php } ?>">
                                <li data-day="0" <?php if (in_array('0', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Mon
                                </li>
                                <li data-day="1" <?php if (in_array('1', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Tue
                                </li>
                                <li data-day="2" <?php if (in_array('2', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Wed
                                </li>
                                <li data-day="3" <?php if (in_array('3', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Thu
                                </li>
                                <li data-day="4" <?php if (in_array('4', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Fri
                                </li>
                                <li data-day="5" <?php if (in_array('5', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Sat
                                </li>
                                <li data-day="6" <?php if (in_array('6', $weeklyArray)) { ?> class="selected" <?php } ?>>
                                    Sun
                                </li>
                            </ul>
                        </div>
                        <div style="clear: both;"></div>
                        <div>
                            <div class="input">
                                <label style="color: rgb(68,68,68);">
                                    <input
                                            type="radio" <?php if ($post['scheduling_run_on'] == 'monthly') { ?> checked="checked" <?php } ?>
                                            name="scheduling_run_on"
                                            value="monthly"/> <?php _e('Every month on the first...', PMXE_Plugin::LANGUAGE_DOMAIN); ?>
                                </label>
                            </div>
                            <input type="hidden" name="scheduling_monthly_days" value="<?php if (isset($post['scheduling_monthly_days'])) echo $post['scheduling_monthly_days']; ?>" id="monthly_days"/>
                            <?php
                            if (isset($post['scheduling_monthly_days'])) {
                                $monthlyArray = explode(',', $post['scheduling_monthly_days']);
                            } else {
                                $monthlyArray = array();
                            }
                            ?>
                            <ul class="days-of-week" id="monthly"
                                style="<?php if ($post['scheduling_run_on'] != 'monthly') { ?> display: none; <?php } ?>">
                                <li data-day="0" <?php if (in_array('0', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Mon
                                </li>
                                <li data-day="1" <?php if (in_array('1', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Tue
                                </li>
                                <li data-day="2" <?php if (in_array('2', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Wed
                                </li>
                                <li data-day="3" <?php if (in_array('3', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Thu
                                </li>
                                <li data-day="4" <?php if (in_array('4', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Fri
                                </li>
                                <li data-day="5" <?php if (in_array('5', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Sat
                                </li>
                                <li data-day="6" <?php if (in_array('6', $monthlyArray)) { ?> class="selected" <?php } ?>>
                                    Sun
                                </li>
                            </ul>
                        </div>
                        <div style="clear: both;"></div>

                        <div id="times-container" style="margin-left: 5px;">
                            <div style="margin-top: 10px; margin-bottom: 5px;">
                                What times do you want this export to run?
                            </div>

                            <div id="times" style="margin-bottom: 10px;">
                                <?php if (is_array($post['scheduling_times'])) {
                                    foreach ($post['scheduling_times'] as $time) { ?>

                                        <?php if ($time) { ?>
                                            <input class="timepicker" type="text" name="scheduling_times[]"
                                                   value="<?php echo $time; ?>"/>
                                        <?php } ?>
                                    <?php } ?>
                                    <input class="timepicker" type="text" name="scheduling_times[]"/>
                                <?php } ?>
                            </div>
                            <div style="clear: both;"></div>
                            <div class="timezone-select" style="position:absolute; margin-top: 10px;">
                                <?php

                                $timezoneValue = false;
                                if ($post['scheduling_timezone']) {
                                    $timezoneValue = $post['scheduling_timezone'];
                                }

                                $timezoneSelect = new \Wpae\Scheduling\Timezone\TimezoneSelect();
                                echo $timezoneSelect->getTimezoneSelect($timezoneValue);
                                ?>
                            </div>
                        </div>
                        <div style="height: 60px; margin-top: 30px; <?php if(!$hasActiveLicense) {?>display: none; <?php } ?>" id="subscribe-filler">&nbsp;</div>
                        <?php
                        if (!$hasActiveLicense) {
                            ?>
                            <div class="subscribe" style="margin-left: 5px; margin-top: 65px; margin-bottom: 130px; position: relative;">
                                <div class="button-container">

                                    <a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=515704" target="_blank" id="subscribe-button">
                                        <div class="button button-primary button-hero wpallexport-large-button button-subscribe"
                                             style="background-image: none; width: 140px; text-align: center; position: absolute; z-index: 4;">
                                            <svg class="success" width="30" height="30" viewBox="0 0 1792 1792"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 style="fill: white; display: none; position: absolute; top: 6px; left: 15px;">
                                                <path
                                                        d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"
                                                        fill="white"/>
                                            </svg>
                                            <svg class="error" width="30" height="30" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"
                                                 style="fill: red; display: none; position: absolute; top: 6px; left: 15px;">
                                                <path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                            <div class="easing-spinner" style="display: none; position: absolute; left: 37px;">
                                                <div class="double-bounce1"></div>
                                                <div class="double-bounce2"></div>
                                            </div>

                                            <span class="subscribe-button-text">
                                            <?php _e('Subscribe'); ?>
                                        </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="text-container" style="position: absolute; left: 150px; top: 2px;">
                                    <p><?php _e('Get automatic scheduling for unlimited sites, just $9/mo.', PMXE_Plugin::LANGUAGE_DOMAIN); ?></p>
                                    <p><?php _e('Have a license?', PMXE_Plugin::LANGUAGE_DOMAIN); ?>
                                        <a href="#" id="add-subscription"><?php _e('Register this site.'); ?></a> <?php _e('Questions?', PMXE_Plugin::LANGUAGE_DOMAIN); ?> <a href="#" class="help_scheduling">Read more.</a></p>
                                    <input type="password" id="add-subscription-field" style="position: absolute; z-index: 2; font-size: 14px;" placeholder="<?php _e('Enter your license', PMXE_Plugin::LANGUAGE_DOMAIN); ?>"/>
                                    <div style="position: absolute;" id="find-subscription-link"><a href="http://www.wpallimport.com/portal/automatic-scheduling/" target="_blank"><?php _e('Find your license at the customer portal.', PMXE_Plugin::LANGUAGE_DOMAIN);?></a></div>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <div style="clear:both"></div>
                    <?php require 'ManualScheduling.php'; ?>
                </div>
            </div>
        </div>

        <div style="clear: both;"></div>
    </div>
</div>

<fieldset class="optionsset column rad4 wp-all-export-scheduling-help">

    <div class="title">
        <span style="font-size:1.5em;" class="wpallexport-add-row-title"><?php _e('Automatic Scheduling', 'wp_all_export_plugin'); ?></span>
    </div>

    <?php
    include_once 'SchedulingHelp.php';
    ?>
</fieldset>
