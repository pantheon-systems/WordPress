/*globals jQuery, ajaxurl, icl_ajx_url, wpml_end_user_data */

var WPML_End_User_Send_Request = function ($) {
    "use strict";

    var wpml_endpoint = wpml_end_user_data.endpoint;

    var get_info = function (success_callback) {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'end_user_get_info'
            },
            beforeSend: function () {
                $("body").addClass("AJAXwait");
            },
            success: success_callback,
            error: display_error
        });
    };

    var handle_response = function (callback) {
        return function (response) {
            if (response.success) {
                callback(response);
            } else {
                display_error(response);
            }
        };
    };

    var build_form = function (data) {
        var form = $("<form>", {
            "action": wpml_endpoint,
            "method": "POST"
        });

        form.on("submit", function () {
            $("body").removeClass("AJAXwait");
            return true;
        });

        ["site_info", "theme_info", "wp_user_info"].forEach(function (object_name) {
            if (data.hasOwnProperty(object_name)) {
                var field_data = data[object_name];
                for (var field in field_data) {
                    if (field_data.hasOwnProperty(field)) {
                        form.append($("<input>", {
                            "name": object_name + "[" + field + "]",
                            "value": field_data[field],
                            "type": "hidden"
                        }));
                    }
                }
            }
        });

        if (data.hasOwnProperty("plugins")) {
            var i = 0;
            data["plugins"].forEach(function(plugin) {
                for (var field in plugin) {
                    if (plugin.hasOwnProperty(field)) {
                        form.append($("<input>", {
                            "name": "plugins[" + i + "][" + field + "]",
                            "value": plugin[field],
                            "type": "hidden"
                        }));
                    }
                }

                i++;
            });
        }

        return form;
    };

    var can_open_new_tab = function (url) {
        var open = window.open(url, "_blank");
        if (open) {
            open.close();
            return true;
        } else {
            return false;
        }
    };

    var submit_form = function (data) {
        var form = build_form(data);
        var url = form.attr("action");

        if (can_open_new_tab(url)) {
            form.attr("target", "_blank");
        }

        form.appendTo("body").submit();
    };

    var display_error = function () {
        $("body").removeClass("AJAXwait");
        window.alert("Unexpected error appeared");
    };

    return {
        "get_info": get_info,
        "handle_response" : handle_response,
        "submit_form" : submit_form,
        "display_error" : display_error
    };
};

var WPML_End_User_Notice = function ($, send_request) {
    "use strict";

    var init = function () {
        $(".js-wpml-end-user-send-request").on("click", button_handler);
    };

    var button_handler = function (event) {
        event.preventDefault();
        var msg = $(this).data("confirm-message");

        display_confirm_message(msg, function () {
            $(this).dialog("close");

            send_request.get_info(
                send_request.handle_response(get_info_success_callback)
            );
        });
    };

    var get_info_success_callback = function (response) {
        var data = response.data;
        send_request.submit_form(data);
    };

    var display_confirm_message = function (message, callback) {

        var dialog = $("<div></div>");
        dialog.addClass("wpml-end-user-dialog-confirm");
        dialog.css("display", "none");

        message = $("<p></p>").html(message);
        dialog.append(message);

        dialog.dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            dialogClass: "wpml-end-user-confirmation-dialog",
            buttons: [
                {
                    text: wpml_end_user_data.confirm_button_label,
                    click: callback,
                    class: "button-primary"
                }
            ]
        });
    };

    return {
        'init': init
    };
};

var WPML_End_User_How_To_Button = function ($, send_request) {
    "use strict";

    var container_builder = (function () {
        var get_container = function () {
            if (is_page_list()) {
                return build_page_list_container();
            } else {
                return build_tm_dashboard_container();
            }
        };

        var is_page_list = function() {
            var href = window.location.href;
            return href.indexOf("edit.php?post_type=page") !== -1;
        };

        var build_tm_dashboard_container = function() {
            var container = $('<div></div>');
            container.addClass('icl_subsubsub');

            container.insertAfter('[name="translation-dashboard-filter"]');

            return container;
        };

        var build_page_list_container = function () {
            var subsubsub = $('.subsubsub');
            var container = subsubsub.next('.icl_subsubsub');

            if (container.length === 0) {
                container = $('<li></li>');
                container.addClass('icl_subsubsub');

                subsubsub.after(container);
            }

            return container;
        };

        return {
            "get_container" : get_container
        };
    })();

    var init = function() {
        var data = wpml_end_user_data;

        build_how_to_button(data, container_builder.get_container());

		if ( wpml_end_user_data.is_site_allowed_to_send_data ) {
			$(".js-wpml-end-user-send-request").on("click", button_handler);
		}
    };

    var build_how_to_button = function (data, container) {
        var button = $(data.button);
        container.append(button);
    };

    var button_handler = function(event) {
        event.preventDefault();

        send_request.get_info(
            send_request.handle_response(get_info_success_callback)
        );
    };

    var get_info_success_callback = function (response) {
        var data = response.data;
        send_request.submit_form(data);
    };

    return {
        "init" : init
    };
};

var WPML_End_User_Loader = (function($) {
    "use strict";

    var init = function() {
        var send_req = WPML_End_User_Send_Request($);
        if (is_notice_displayed()) {
            WPML_End_User_Notice($, send_req).init();
        } else {
            WPML_End_User_How_To_Button($, send_req).init();
        }
    };

    var is_notice_displayed = function() {
        return $("[data-group='end-user-notice']").length > 0;
    };

    return {
        'init' : init
    };

})(jQuery);

jQuery(document).ready(function () {
    "use strict";
    WPML_End_User_Loader.init();
});

