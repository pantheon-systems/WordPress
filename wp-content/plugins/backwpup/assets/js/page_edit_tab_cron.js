jQuery(document).ready(function ($) {
    $('input[name="activetype"]').change(function () {
        if ( $(this).val() == 'wpcron' || $(this).val() == 'easycron') {
            $('.wpcron').show();
        } else {
            $('.wpcron').hide();
        }
    });

    if ($('input[name="activetype"]:checked').val() == 'wpcron' || $('input[name="activetype"]:checked').val() == 'easycron' ) {
        $('.wpcron').show();
    } else {
        $('.wpcron').hide();
    }

    $('input[name="cronselect"]').change(function () {
        if ('basic' == $('input[name="cronselect"]:checked').val()) {
            $('.wpcronadvanced').hide();
            $('.wpcronbasic').show();
            cronstampbasic();
        } else {
            $('.wpcronadvanced').show();
            $('.wpcronbasic').hide();
            cronstampadvanced();
        }
    });

    function cronstampadvanced() {
        var cronminutes = [];
        var cronhours = [];
        var cronmday = [];
        var cronmon = [];
        var cronwday = [];
        $('input[name="cronminutes[]"]:checked').each(function () {
            cronminutes.push($(this).val());
        });
        $('input[name="cronhours[]"]:checked').each(function () {
            cronhours.push($(this).val());
        });
        $('input[name="cronmday[]"]:checked').each(function () {
            cronmday.push($(this).val());
        });
        $('input[name="cronmon[]"]:checked').each(function () {
            cronmon.push($(this).val());
        });
        $('input[name="cronwday[]"]:checked').each(function () {
            cronwday.push($(this).val());
        });
        var data = {
            action:'backwpup_cron_text',
            cronminutes:cronminutes,
            cronhours:cronhours,
            cronmday:cronmday,
            cronmon:cronmon,
            cronwday:cronwday,
            crontype:'advanced',
            _ajax_nonce:$('#backwpupajaxnonce').val()
        };
        $.post(ajaxurl, data, function (response) {
            $('#schedulecron').replaceWith(response);
        });
    }
    $('input[name="cronminutes[]"]').change(function () {
        cronstampadvanced();
    });
    $('input[name="cronhours[]"]').change(function () {
        cronstampadvanced();
    });
    $('input[name="cronmday[]"]').change(function () {
        cronstampadvanced();
    });
    $('input[name="cronmon[]"]').change(function () {
        cronstampadvanced();
    });
    $('input[name="cronwday[]"]').change(function () {
        cronstampadvanced();
    });

    function cronstampbasic() {
        var cronminutes = [];
        var cronhours = [];
        var cronmday = [];
        var cronmon = [];
        var cronwday = [];
        if ('mon' == $('input[name="cronbtype"]:checked').val()) {
            cronminutes.push($('select[name="moncronminutes"]').val());
            cronhours.push($('select[name="moncronhours"]').val());
            cronmday.push($('select[name="moncronmday"]').val());
            cronmon.push('*');
            cronwday.push('*');
        }
        if ('week' == $('input[name="cronbtype"]:checked').val()) {
            cronminutes.push($('select[name="weekcronminutes"]').val());
            cronhours.push($('select[name="weekcronhours"]').val());
            cronmday.push('*');
            cronmon.push('*');
            cronwday.push($('select[name="weekcronwday"]').val());
        }
        if ('day' == $('input[name="cronbtype"]:checked').val()) {
            cronminutes.push($('select[name="daycronminutes"]').val());
            cronhours.push($('select[name="daycronhours"]').val());
            cronmday.push('*');
            cronmon.push('*');
            cronwday.push('*');
        }
        if ('hour' == $('input[name="cronbtype"]:checked').val()) {
            cronminutes.push($('select[name="hourcronminutes"]').val());
            cronhours.push('*');
            cronmday.push('*');
            cronmon.push('*');
            cronwday.push('*');
        }
        var data = {
            action:'backwpup_cron_text',
            cronminutes:cronminutes,
            cronhours:cronhours,
            cronmday:cronmday,
            cronmon:cronmon,
            cronwday:cronwday,
            crontype:'basic',
            _ajax_nonce:$('#backwpupajaxnonce').val()
        };
        $.post(ajaxurl, data, function (response) {
            $('#schedulecron').replaceWith(response);
        });
    }
    $('input[name="cronbtype"]').change(function () {
        cronstampbasic();
    });
    $('select[name="moncronmday"]').change(function () {
        cronstampbasic();
    });
    $('select[name="moncronhours"]').change(function () {
        cronstampbasic();
    });
    $('select[name="moncronminutes"]').change(function () {
        cronstampbasic();
    });
    $('select[name="weekcronwday"]').change(function () {
        cronstampbasic();
    });
    $('select[name="weekcronhours"]').change(function () {
        cronstampbasic();
    });
    $('select[name="weekcronminutes"]').change(function () {
        cronstampbasic();
    });
    $('select[name="daycronhours"]').change(function () {
        cronstampbasic();
    });
    $('select[name="daycronminutes"]').change(function () {
        cronstampbasic();
    });
    $('select[name="hourcronminutes"]').change(function () {
        cronstampbasic();
    });
});
