jQuery(document).ready(function() {
    jQuery("h2:first").after('<div id="msg-busy-page"></div>');
    // tab handling code
    jQuery('#wsal-tabs>a').click(function(){
        jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
        jQuery('div.wsal-tab').hide();
        jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
    });
    // show relevant tab
    var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
    if (hashlink.length) {
        hashlink.click();
    } else {
        jQuery('#wsal-tabs>a:first').click();
    }

    jQuery('form input[type=checkbox]').unbind('change').change(function() {
        current = this.name + 'Emails';
        if (jQuery(this).is(':checked')) {
            jQuery('#'+current).prop('required', true);
        } else {
            jQuery('#'+current).removeProp('required');
        }
    });
});

function Refresh() {
    location.reload();
}

function WsalSsasChange(value) {
    jQuery('#wsal-cbid').val(value);
    jQuery('#sessionsForm').submit();
}

var validateEmail = function(value) {
    return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(value);
};

jQuery('form').submit(function() {
    var res = true;
    
    jQuery(".emailsAlert").each(function() {
        var emailStr = jQuery(this).val().trim();
        if(emailStr != "") {
            var emails = emailStr.split(/[;,]+/);
            for (var i in emails) {
                var email = jQuery.trim(emails[i]);
                if(!validateEmail(email)){
                    jQuery(this).addClass("error");
                    res = false; 
                } else {
                    jQuery(this).removeClass("error");
                }
            }
        }
    })
    return res;
});

function SessionAutoRefresh(dataSessions) {
    var current_token = dataSessions.token;
    var blog_id = dataSessions.blog_id;
    
    var SessionsChk = function() {
        var is_page_busy = false;

        jQuery('body').mousemove(function(event) {
            is_page_busy = true;
        });

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            data: { 
                action: 'SessionAutoRefresh',
                sessions_count: current_token,
                blog_id: blog_id
            },
            success: function(data) {
                if(data && data !== 'false'){
                    current_token = data;
                    if (!is_page_busy) {
                        location.reload();
                    } else {
                        var msg = 'New session. Please press <a href="javascript:Refresh();">Refresh</a>';
                        jQuery("#msg-busy-page").html(msg).addClass('updated');
                    }
                }
            }
        });
    };
    setInterval(SessionsChk, 5000);
}
