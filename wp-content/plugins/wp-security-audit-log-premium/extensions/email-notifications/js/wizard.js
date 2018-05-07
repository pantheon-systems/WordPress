jQuery(document).ready(function() {
    // tab handling code
    jQuery('#wsal-tabs>a').click(function() {
        jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
        jQuery('table.wsal-tab').hide();
        jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
    });
    // show relevant tab
    var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
    if (hashlink.length) {
        hashlink.click();
    } else {
        jQuery('#wsal-tabs>a:first').click();
    }

    jQuery('.option-email').unbind('keyup').keyup(function() {
        var validEmail = false;
        if (jQuery(this).val().length > 0 ) {
            jQuery(this).css('border-color', '#ddd');
            // Email validation
            if (!validateEmail(jQuery(this).val())) {
                jQuery(this).css('border-color', '#dd3d36');
            }
        }
    });

    jQuery('.option-name').unbind('keyup').keyup(function() {
        var validEmail = false;
        if (jQuery(this).val().length > 0 ) {
            jQuery(this).css('border-color', '#ddd');
        }
    });

    jQuery('#save-first-step').click(function() {
        SaveFirstStep();
    });

    function SaveFirstStep() {
        var id = null;
        var email = null;
        var builtIn = {};
        var validate = true;
        jQuery('[name="built-in[]"]').each(function () {
            if (jQuery(this).prop('checked') == true) {
                id = jQuery(this).prop("id");
                email = jQuery('#email-'+id);
                if (email.val().length === 0 ) {
                    email.css('border-color', '#dd3d36');
                    validate = false;
                }
                if (email.val().length > 0 ) {
                    if (!validateEmail(email.val())) {
                        email.css('border-color', '#dd3d36');
                        validate = false;
                    } else {
                        builtIn[id] = {email : email.val()};
                    }
                }
            } else {
                id = jQuery(this).prop("id");
                builtIn[id] = 0;
            }
        });

        if (!validate) {
            return false;
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'SaveFirstStep',
                builtIn: JSON.stringify(builtIn),
            },
            success: function(response) {

            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
        nextStep('second');
    }

    SaveChanges = function() {
        var email = jQuery('#notifications-email').val();
        var name = jQuery('#notifications-name').val();
        var categoryName = jQuery('#category-name').val();
        var alerts = [];
        jQuery('[name="alerts[]"]').each(function () {
            if (jQuery(this).prop('checked') == true) {
                alerts.push(jQuery(this).val());
            }
        });
        if (alerts.length > 0) {
            if (jQuery('#notifications-email').val().length === 0 ) {
                jQuery('#notifications-email').css('border-color', '#dd3d36');
                return false;
            }
            if (jQuery('#notifications-email').val().length > 0 ) {
                if (!validateEmail(jQuery('#notifications-email').val())) {
                    jQuery('#notifications-email').css('border-color', '#dd3d36');
                    return false;
                }
            }
            if (jQuery('#notifications-name').val().length === 0 ) {
                jQuery('#notifications-name').css('border-color', '#dd3d36');
                return false;
            }
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'SaveChanges',
                categoryName: categoryName,
                alerts: JSON.stringify(alerts),
                email: email,
                name: name
            },
            success: function(response) {
                if (!response) {
                    window.location.hash = 'tab-second';
                    window.location.reload();
                } else {
                    nextStep('fifth');
                }
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
    };

    jQuery("#backToCategory").click(function(event) {
        event.preventDefault();
        nextStep('second');
    });

    jQuery("#backToAlerts").click(function(event) {
        event.preventDefault();
        nextStep('third');
    });
});

function nextStep(step) {
    jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
    jQuery('table.wsal-tab').hide();
    jQuery(jQuery("#wsal-tabs>a[href='#tab-"+step+"']").addClass('nav-tab-active').attr('href')).show();
}

function goToThirdStep() {
    jQuery('#loading').show();
    jQuery('#category-alerts').html('');
    var checkedType = false;
    var groupParent = '';
    jQuery('[name="types[]"]').each(function (){
        if (jQuery(this).is(":checked")) {
            checkedType = jQuery(this).val();
            groupParent = jQuery( this ).data( 'group-parent' );
        }
    });
    if (checkedType !== false) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            data: {
                action: 'ShowAlertByType',
                type: checkedType,
                parentType: groupParent,
            },
            success: function(html) {
                jQuery('#category-alerts').html(html);
                jQuery('#loading').hide();
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
        nextStep('third');
    } else {
        SaveChanges();
    }
}

function saveNotifications() {
    SaveChanges();
}

function checkAll(elem, name) {
    jQuery('[name="'+name+'[]"]').each(function () {
        jQuery(this).prop('checked', jQuery(elem).prop("checked"));
    });
}

function validateEmail(email) {
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
        return false;
    } else {
        return true;
    }
}
