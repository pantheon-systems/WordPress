jQuery(document).ready(function () {
	"use strict";

	var dialog = jQuery('.wpml-tm-invalid-fields-dialog');

	jQuery('.wpml-tm-invalid-fields-open-dialog').click(function () {
		var fields = jQuery(this).data('fields');
		dialog.dialog({
			dialogClass: 'wpml-dialog otgs-ui-dialog',
			width: 'auto',
			title: jQuery(this).data('page-title'),
			modal: true,
			open: function () {
				dialog.html('');
				dialog.append('<ul>');
				jQuery.each(fields, function(index, value){
					var field = JSON.parse(value);
					dialog.append('<li><strong>' + field.title + '</strong>: ' + field.content + '</li>');
				});
				dialog.append( '</ul>' );
			}
		});
	});
});