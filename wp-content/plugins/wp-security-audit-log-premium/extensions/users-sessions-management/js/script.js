/**
 * Users Sessions Management Script.
 *
 */
jQuery(document).ready(function () {
	jQuery("h2:first").after('<div id="msg-busy-page"></div>');

	// Tab handling code.
	jQuery('#wsal-tabs>a').click(function () {
		jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
		jQuery('div.wsal-tab').hide();
		jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
	});

	// Show relevant tab.
	var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
	if (hashlink.length) {
		hashlink.click();
	} else {
		jQuery('#wsal-tabs>a:first').click();
	}

	jQuery('form input[type=checkbox]').unbind('change').change(function () {
		current = this.name + 'Emails';
		if (jQuery(this).is(':checked')) {
			jQuery('#' + current).prop('required', true);
		} else {
			jQuery('#' + current).removeProp('required');
		}
	});

	jQuery('#delete_all_sessions').on('click', function () {
		jQuery(this).attr('value', 'Logging out...');
		jQuery(this).attr('disabled', 'disabled');
		AjaxSessionsDestroy();
		jQuery(this).attr('value', 'Done');
	});

	/**
	 * Destroy Session.
	 *
	 * @since 3.1
	 */
	jQuery('.wsal_destroy_session').click(function (event) {
		event.preventDefault();
		jQuery(this).text('Logging out...');
		jQuery(this).attr('disabled', 'disabled');
		var session_data = {
			action: jQuery(this).data('action'),
			user_id: jQuery(this).data('user-id'),
			token: jQuery(this).data('token'),
			wpnonce: jQuery(this).data('wpnonce'),
		}

		WsalDestroySession(jQuery(this), session_data);
	});

	/**
	 * Destroy All Sessions.
	 *
	 * @since 3.1.4
	 */
	var terminate_sessions_modal = jQuery('[data-remodal-id=wsal-terminate-sessions]');
	jQuery(document).on('closed', terminate_sessions_modal, function (event) {
		if (event.reason && event.reason === 'confirmation') {
			// Terminate all sessions.
			var nonce = jQuery('#wsal-terminate-all-sessions').val();
			terminate_all_sessions(nonce);
		}
	});

	// Select option on focus.
	jQuery('#multi-sessions-limit').focus(function (event) {
		jQuery('#allow-limited').attr('checked', 'checked');
	});
	jQuery('#session_override_pass').focus(function (event) {
		jQuery('#with_warning').attr('checked', 'checked');
	});

	/**
	 * Sessions Blocked Option.
	 *
	 * @since 3.1.4
	 */
	var session_blocked = jQuery('#wsal_blocked_session_override fieldset');
	jQuery('input[name=MultiSessions]').change(function (event) {
		var checked = jQuery(this).val();
		if (checked === '1') {
			session_blocked.removeAttr('disabled');
		} else {
			session_blocked.attr('disabled', 'disabled');
		}
	});

	/**
	 * Terminate all sessions tooltip.
	 *
	 * @since 3.1.5
	 */
	jQuery('#wsal_terminate_all').darkTooltip({
		animation: 'fadeIn',
		gravity: 'north',
		size: 'medium',
		confirm: false,
	});
});

function Refresh() {
	location.reload();
}

function WsalSsasChange(value) {
	jQuery('#wsal-cbid').val(value);
	jQuery('#sessionsForm').submit();
}

var validateEmail = function (value) {
	return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(value);
};

jQuery('form').submit(function () {
	var res = true;

	jQuery(".emailsAlert").each(function () {
		var emailStr = jQuery(this).val().trim();
		if (emailStr != "") {
			var emails = emailStr.split(/[;,]+/);
			for (var i in emails) {
				var email = jQuery.trim(emails[i]);
				if (!validateEmail(email)) {
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
	var data = jQuery.parseJSON(dataSessions);
	var current_token = data.token;
	var blog_id = data.blog_id;

	var SessionsChk = function () {
		var is_page_busy = false;

		jQuery('body').mousemove(function (event) {
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
			success: function (result) {
				if (result && result !== 'false') {
					current_token = result;
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

var offset = 0;
function AjaxSessionsDestroy() {
	if (!script_data.script_nonce) {
		return;
	}
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		async: true,
		dataType: 'json',
		data: {
			action: 'AjaxSessionsDestroy',
			nonce: script_data.script_nonce,
			offset: offset,
		},
		success: function (response) {
			offset = response;
			if (!response.success) {
				console.log(response.message);
			} else if (response != 0) {
				AjaxSessionsDestroy();
			}
		},
		error: function (xhr, textStatus, error) {
			console.log(xhr.statusText);
			console.log(textStatus);
			console.log(error);
		}
	});
}

/**
 * Destroy Individual Session.
 *
 * @since 3.1
 */
function WsalDestroySession(btn, session_data) {
	if (!session_data) {
		return false;
	}

	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		async: true,
		dataType: 'json',
		data: {
			action: session_data.action,
			user_id: session_data.user_id,
			token: session_data.token,
			nonce: session_data.wpnonce,
		},
		success: function (response) {
			if (!response.success) {
				console.log(response.message);
			} else {
				btn.text('Refreshing...');
				Refresh();
			}
		},
		error: function (xhr, textStatus, error) {
			console.log(xhr.statusText);
			console.log(textStatus);
			console.log(error);
		}
	});
}

/**
 * Terminate all sessions.
 *
 * @param {string} nonce Terminal all sessions nonce.
 */
function terminate_all_sessions(nonce) {
	if (!nonce) {
		return;
	}

	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		async: true,
		dataType: 'json',
		data: {
			action: 'wsal_terminate_all_sessions',
			nonce: nonce,
		},
		success: function (response) {
			if (!response.success) {
				console.log(response.message);
			} else {
				Refresh();
			}
		},
		error: function (xhr, textStatus, error) {
			console.log(xhr.statusText);
			console.log(textStatus);
			console.log(error);
		}
	});
}
