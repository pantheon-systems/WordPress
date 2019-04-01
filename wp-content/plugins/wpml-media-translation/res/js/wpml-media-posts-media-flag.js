var WPML_Media_Posts_Media_Flag = WPML_Media_Posts_Media_Flag || {};

jQuery(function ($) {

    "use strict";

	var updateContainer = $('#wpml-media-posts-media-flag');

	var updateButton = updateContainer.find('.button-primary');
	var spinner      = updateContainer.find('.spinner');

	var prepareAction = updateContainer.data('prepareAction');
	var prepareNonce  = updateContainer.data('prepareNonce');

	var processAction = updateContainer.data('processAction');
	var processNonce  = updateContainer.data('processNonce');

	var statusContainer = updateContainer.find('.status');

    function getQueryParams(qs) {
        qs = qs.split('+').join(' ');

        var params = {},
            tokens,
            re = /[?&]?([^=]+)=([^&]*)/g;

        while (tokens = re.exec(qs)) {
            params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
        }

        return params;
    }


    var queryParams = getQueryParams(location.search);
    if (queryParams.run_setup) {
        showProgress();
        runSetup();
    }

    updateButton.on("click", function () {
        showProgress();
        runSetup();
    });

    function showProgress() {
        spinner.css({visibility: "visible"});
        updateButton.prop("disabled", true);
    }

    function hideProgress() {
        spinner.css({visibility: "hidden"});
        updateButton.prop("disabled", false);
    }

    function setStatus(statusText) {
        statusContainer.html(statusText);
    }

    function runSetup() {
        var data = {
			action: prepareAction,
			nonce: prepareNonce
        };
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
				handleResponse(response);
				if (!response.success) {
					return;
				}

                if (response.data.status) {
                    setStatus(response.data.status);
                }
                setInitialLanguage();
			},
			error: function (jqXHR, status, error) {
				statusContainer.html(jqXHR.statusText || status || error);
			}
        });
    }

	function handleResponse(response) {
		var error = [];

		if (response.error) {
			error.push(response.error);
		}
		if (!response.success && response.data) {
			error.push(response.data);
		}

		if (error.length) {
			statusContainer.html('<pre>' + error.join('</pre><pre>') + '</pre>');
		}
	}

    function setInitialLanguage() {
        var data = {
			action: processAction,
			nonce: processNonce
        };
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
				handleResponse(response);
				if (!response.success) {
					return;
				}

				var message = response.message ? response.message : response.data.message;
				setStatus(message);
				setHasMediaFlag(0);
			},
			error: function (jqXHR, status, error) {
				statusContainer.html(jqXHR.statusText || status || error);
			}
        });
    }

    function setHasMediaFlag(offset) {
        var data = {
			action: processAction,
			nonce: processNonce,
            offset: offset
        };
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
				handleResponse(response);
				if (!response.success) {
					return;
				}

                if (response.data.status) {
                    setStatus(response.data.status);
                }
                if (response.data.continue) {
                    setHasMediaFlag(response.data.offset);
                } else {
                    if (queryParams.redirect_to) {
                        location.href = queryParams.redirect_to;
                    } else {
                        location.reload();
                    }
                }
			},
			error: function (jqXHR, status, error) {
				statusContainer.html(jqXHR.statusText || status || error);
			}
        });
    }

});
