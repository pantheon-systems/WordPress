/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function ($) {

    $(".oceanwp-newsletter-form-wrap #mc-embedded-subscribe-form").on("submit", function (e) {
        e.preventDefault();

        var element = $(this);
        var email = element.find('.email').val();
        var valid = true;

        element.find(".err-msg").hide();

        if ($.trim(email).length == 0) {
            valid = false;
            element.find(".email-err.req").show();
        } else if (!isValidEmailAddress(email)) {
            valid = false;
            element.find(".email-err.not-valid").show();
        }

        if (element.find(".gdpr").length && !element.find(".gdpr").is(":checked")) {
            valid = false;
            element.find(".gdpr-err.err-msg").show();
        }

        element.find(".res-msg").hide();

        if (valid) {
            element.find("button").attr("disabled", true);

            var data = {
                action: "oceanwp_mailchimp_request",
                email: email,
            };

            $.ajax({
                type: 'POST',
                url: oceanwpLocalize.ajax_url,
                data: data,
                success: function( response ) {
                     element.find("button").attr("disabled", false);

                    if (response.status) {
                        element.find(".res-msg.success").show().delay(5000).fadeOut();
                    } else {
                        element.find(".res-msg.failed").show().delay(5000).fadeOut();
                    }

                },
                complete: function() {
                }

            });
        }

    });

});

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};