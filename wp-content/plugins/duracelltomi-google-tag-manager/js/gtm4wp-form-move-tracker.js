jQuery( function() {
	jQuery( ":input" )
		.on( "focus", function() {
			var input      = jQuery(this);
			var inputID    = input.attr("id") || "(no input ID)";
			var inputName  = input.attr("name") || "(no input name)";
			var inputClass = input.attr("class") || "(no input class)";

			var form      = jQuery(this.form);
			var formID    = form.attr("id") || "(no form ID)";
			var formName  = form.attr("name") || "(no form name)";
			var formClass = form.attr("class") || "(no form class)";

			window[ gtm4wp_datalayer_name ].push({
				'event'    : 'gtm4wp.formElementEnter',

				'inputID'   : inputID,
				'inputName' : inputName,
				'inputClass': inputClass,

				'formID'   : formID,
				'formName' : formName,
				'formClass': formClass
			});
		})

		.on( "blur", function() {
			var input      = jQuery(this);
			var inputID    = input.attr("id") || "(no input ID)";
			var inputName  = input.attr("name") || "(no input name)";
			var inputClass = input.attr("class") || "(no input class)";

			var form      = jQuery(this.form);
			var formID    = form.attr("id") || "(no form ID)";
			var formName  = form.attr("name") || "(no form name)";
			var formClass = form.attr("class") || "(no form class)";

			window[ gtm4wp_datalayer_name ].push({
				'event'    : 'gtm4wp.formElementLeave',

				'inputID'   : inputID,
				'inputName' : inputName,
				'inputClass': inputClass,

				'formID'   : formID,
				'formName' : formName,
				'formClass': formClass
			});
		});
});