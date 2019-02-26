jQuery(document).ready(function () {
	var container = jQuery('.otgs-installer-component-setting');
	container.find('.js-otgs-components-report-user-choice').click(function () {
		var spinner = container.find('.spinner');

		spinner.addClass('is-active');

		var element = jQuery(this);

		var agree = element.is(':checked') ? 1 : 0;
		if (element.is(':radio')) {
			agree = element.val();
		}

		jQuery.ajax({
									url:     ajaxurl,
									type:    'POST',
									data:    {
										action: element.data('nonce-action'),
										nonce:  element.data('nonce-value'),
										agree:  agree,
										repo:   element.data('repo'),
									},
									success: function () {
										spinner.removeClass('is-active');
									},
								});
	});
});
